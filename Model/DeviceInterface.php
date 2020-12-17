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

use Klipper\Component\DoctrineChoice\Model\ChoiceInterface;
use Klipper\Component\Model\Traits\LabelableInterface;
use Klipper\Component\Model\Traits\OrganizationalRequiredInterface;
use Klipper\Component\Model\Traits\TimestampableInterface;
use Klipper\Contracts\Model\IdInterface;
use Klipper\Module\PartnerBundle\Model\Traits\AccountableOptionalInterface;
use Klipper\Module\ProductBundle\Model\Traits\ProductableOptionalInterface;
use Klipper\Module\ProductBundle\Model\Traits\ProductCombinationableOptionalInterface;

/**
 * Device interface.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
interface DeviceInterface extends
    IdInterface,
    AccountableOptionalInterface,
    LabelableInterface,
    ProductableOptionalInterface,
    ProductCombinationableOptionalInterface,
    OrganizationalRequiredInterface,
    TimestampableInterface
{
    /**
     * @return static
     */
    public function setSerialNumber(?string $serialNumber);

    public function getSerialNumber(): ?string;

    /**
     * @return static
     */
    public function setImei(?string $imei);

    public function getImei(): ?string;

    /**
     * @return static
     */
    public function setImei2(?string $imei2);

    public function getImei2(): ?string;

    /**
     * @return static
     */
    public function setStatus(?ChoiceInterface $status);

    public function getStatus(): ?ChoiceInterface;

    /**
     * @return static
     */
    public function setTerminatedAt(?\DateTimeInterface $terminatedAt);

    public function getTerminatedAt(): ?\DateTimeInterface;
}
