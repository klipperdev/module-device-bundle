<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DeviceBundle\Doctrine\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Klipper\Component\DoctrineChoice\ChoiceManagerInterface;
use Klipper\Component\DoctrineExtra\Util\ClassUtils;
use Klipper\Module\DeviceBundle\Model\DeviceInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class DeviceSubscriber implements EventSubscriber
{
    private ChoiceManagerInterface $choiceManager;

    /**
     * @var DeviceInterface[]
     */
    private array $devices = [];

    public function __construct(ChoiceManagerInterface $choiceManager)
    {
        $this->choiceManager = $choiceManager;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
            Events::postFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $object) {
            $this->persistDevice($em, $object, true);
        }

        foreach ($uow->getScheduledEntityUpdates() as $object) {
            $this->persistDevice($em, $object);
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!empty($this->devices)) {
            $em = $args->getEntityManager();
            $ids = [];

            foreach ($this->devices as $device) {
                $ids[] = $device->getId();
                // Update value only for the display
                $device->setSerialNumber('UNKNOWN_'.$device->getId());
            }

            $em->createQuery('UPDATE App:Device d SET d.serialNumber = CONCAT(\'UNKNOWN_\', d.id) WHERE d.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->execute()
            ;

            $this->devices = [];
        }
    }

    private function persistDevice(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        $uow = $em->getUnitOfWork();

        if ($object instanceof DeviceInterface) {
            $meta = $em->getClassMetadata(ClassUtils::getClass($object));
            $changeSet = $uow->getEntityChangeSet($object);
            $edited = false;

            if (null === $object->getStatus()
                && null !== $defaultStatus = $this->choiceManager->getChoice('device_status', 'in_use')
            ) {
                $edited = true;
                $object->setStatus($defaultStatus);
            }

            if ($create || isset($changeSet['status']) || (isset($changeSet['terminatedAt']) && null === $changeSet['terminatedAt'][1])) {
                $status = $object->getStatus();

                if (null !== $status && \in_array($status->getValue(), ['recycled', 'terminated'], true)) {
                    $edited = true;
                    $object->setTerminatedAt(new \DateTime());
                } else {
                    $edited = $edited || null !== $object->getTerminatedAt();
                    $object->setTerminatedAt(null);
                }
            }

            if ($object->isEmpty()) {
                if ($create || null === $object->getId()) {
                    $this->devices[] = $object;
                } else {
                    $edited = true;
                    $object->setSerialNumber('UNKNOWN_'.$object->getId());
                }
            }

            if ($create || $edited) {
                $uow->recomputeSingleEntityChangeSet($meta, $object);
            }
        }
    }
}
