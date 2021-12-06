<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DeviceBundle\Model;

use Klipper\Component\Content\Model\AttachmentInterface;
use Klipper\Component\Model\Traits\FilePathInterface;
use Klipper\Component\Model\Traits\NameableInterface;
use Klipper\Component\Model\Traits\OrganizationalRequiredInterface;
use Klipper\Component\Model\Traits\TimestampableInterface;
use Klipper\Component\Model\Traits\UserTrackableInterface;

/**
 * Device Attachment interface.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
interface DeviceAttachmentInterface extends
    AttachmentInterface,
    FilePathInterface,
    NameableInterface,
    OrganizationalRequiredInterface,
    TimestampableInterface,
    UserTrackableInterface
{
    /**
     * @return static
     */
    public function setDevice(?DeviceInterface $device);

    public function getDevice(): ?DeviceInterface;
}
