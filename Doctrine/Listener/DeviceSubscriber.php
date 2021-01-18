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
use Doctrine\ORM\Events;
use Klipper\Component\DoctrineChoice\Model\ChoiceInterface;
use Klipper\Component\DoctrineExtra\Util\ClassUtils;
use Klipper\Module\DeviceBundle\Model\DeviceInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class DeviceSubscriber implements EventSubscriber
{
    private ?array $statusChoices = null;

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
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

    private function persistDevice(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        $uow = $em->getUnitOfWork();

        if ($object instanceof DeviceInterface) {
            $meta = $em->getClassMetadata(ClassUtils::getClass($object));
            $changeSet = $uow->getEntityChangeSet($object);
            $edited = false;

            if (null === $object->getStatus() && null !== $defaultStatus = $this->getStatus($em, 'operational')) {
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

            if ($create || $edited) {
                $uow->recomputeSingleEntityChangeSet($meta, $object);
            }
        }
    }

    private function getStatus(EntityManagerInterface $em, string $value): ?ChoiceInterface
    {
        if (null === $this->statusChoices) {
            $this->statusChoices = [];
            $res = $em->getRepository(ChoiceInterface::class)->findBy([
                'type' => 'device_status',
            ]);

            /** @var ChoiceInterface $item */
            foreach ($res as $item) {
                $this->statusChoices[$item->getValue()] = $item;
            }
        }

        return $this->statusChoices[$value] ?? null;
    }
}
