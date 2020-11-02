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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Klipper\Component\DoctrineChoice\Model\ChoiceInterface;
use Klipper\Component\DoctrineChoice\Validator\Constraints\EntityDoctrineChoice;
use Klipper\Component\Model\Traits\OrganizationalRequiredTrait;
use Klipper\Component\Model\Traits\TimestampableTrait;
use Klipper\Module\PartnerBundle\Model\Traits\AccountableOptionalTrait;
use Klipper\Module\ProductBundle\Model\Traits\ProductableTrait;
use Klipper\Module\ProductBundle\Model\Traits\ProductCombinationableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Device model.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @Serializer\ExclusionPolicy("all")
 */
abstract class AbstractDevice implements DeviceInterface
{
    use AccountableOptionalTrait;
    use OrganizationalRequiredTrait;
    use ProductableTrait;
    use ProductCombinationableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(min="0", max="128")
     *
     * @Serializer\Expose
     */
    protected ?string $serialNumber = null;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(min="0", max="15")
     *
     * @Serializer\Expose
     */
    protected ?string $imei = null;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(min="0", max="15")
     *
     * @Serializer\Expose
     */
    protected ?string $imei2 = null;

    /**
     * @ORM\ManyToOne(targetEntity="Klipper\Component\DoctrineChoice\Model\ChoiceInterface")
     *
     * @EntityDoctrineChoice("device_status")
     *
     * @Serializer\Expose
     */
    protected ?ChoiceInterface $status = null;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\Type(type="datetime")
     *
     * @Serializer\Expose
     */
    protected ?\DateTimeInterface $terminatedAt = null;

    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setImei(?string $imei): self
    {
        $this->imei = $imei;

        return $this;
    }

    public function getImei(): ?string
    {
        return $this->imei;
    }

    public function setImei2(?string $imei2): self
    {
        $this->imei2 = $imei2;

        return $this;
    }

    public function getImei2(): ?string
    {
        return $this->imei2;
    }

    public function setStatus(?ChoiceInterface $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?ChoiceInterface
    {
        return $this->status;
    }

    public function setTerminatedAt(?\DateTimeInterface $terminatedAt): self
    {
        $this->terminatedAt = $terminatedAt;

        return $this;
    }

    public function getTerminatedAt(): ?\DateTimeInterface
    {
        return $this->terminatedAt;
    }
}
