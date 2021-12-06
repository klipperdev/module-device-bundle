<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DeviceBundle\Controller;

use Klipper\Bundle\ApiBundle\Controller\ControllerHelper;
use Klipper\Component\Content\ContentManagerInterface;
use Klipper\Component\SecurityOauth\Scope\ScopeVote;
use Klipper\Module\DeviceBundle\Model\DeviceAttachmentInterface;
use Klipper\Module\DeviceBundle\Model\DeviceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     path="/device_attachments"
 * )
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class ApiDeviceAttachmentController
{
    /**
     * Upload the file of device attachment.
     *
     * @Entity("deviceId", class="App:Device")
     *
     * @Route("/upload/{deviceId}", methods={"POST"})
     */
    public function uploadFile(
        ControllerHelper $helper,
        ContentManagerInterface $contentManager,
        DeviceInterface $deviceId
    ): Response {
        if (class_exists(ScopeVote::class)) {
            $helper->denyAccessUnlessGranted(new ScopeVote('meta/device'));
        }

        return $contentManager->upload('device_attachment', $deviceId);
    }

    /**
     * Download the file of device attachment.
     *
     * @Entity("id", class="App:DeviceAttachment")
     *
     * @Route("/{id}/download", methods={"GET"})
     */
    public function download(
        ControllerHelper $helper,
        ContentManagerInterface $contentManager,
        DeviceAttachmentInterface $id
    ): Response {
        if (class_exists(ScopeVote::class)) {
            $helper->denyAccessUnlessGranted(new ScopeVote('meta/device'));
        }

        return $contentManager->download(
            'device_attachment',
            $id->getFilePath(),
            $id->getBasename()
        );
    }

    /**
     * Download the image preview of device attachment.
     *
     * @Entity("id", class="App:DeviceAttachment")
     *
     * @Route("/{id}/download.{ext}", methods={"GET"})
     */
    public function downloadPreview(
        ControllerHelper $helper,
        ContentManagerInterface $contentManager,
        DeviceAttachmentInterface $id
    ): Response {
        if (class_exists(ScopeVote::class)) {
            $helper->denyAccessUnlessGranted(new ScopeVote('meta/device'));
        }

        return $contentManager->downloadImage(
            'device_attachment',
            $id->getFilePath(),
            $id->getBasename()
        );
    }
}
