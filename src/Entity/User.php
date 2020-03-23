<?php

declare(strict_types=1);

namespace App\Entity;

use App\EntitySubscriber\GenerateIdFromNameInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
final class User implements GenerateIdFromNameInterface
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=6, nullable=false)
     */
    private string $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private string $name;
    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private string $phone;
    /**
     * @var ArrayCollection|City[]
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\City",
     *     mappedBy="users"
     * )
     */
    private $cities;

    public function __construct(string $name, string $phone)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->cities = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addCity(City $city)
    {
        $cityId = $city->getId();
        if (!$this->cities->containsKey($cityId)) {
            $this->cities[$cityId] = $city;
            $city->addUser($this);
        }

        return $this;
    }
}
