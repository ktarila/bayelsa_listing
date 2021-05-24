<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AddressRepository::class)
 */
class Address
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lineOne;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLineOne(): ?string
    {
        return $this->lineOne;
    }

    public function setLineOne(string $lineOne): self
    {
        $this->lineOne = $lineOne;

        return $this;
    }
}
