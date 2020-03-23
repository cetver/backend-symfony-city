<?php

declare(strict_types=1);

namespace App\Entity;

use App\EntitySubscriber\GenerateIdFromNameInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class City.
 *
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @ORM\Table(name="cities")
 */
final class City implements GenerateIdFromNameInterface
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
    private string $country;
    /**
     * @var ArrayCollection|User[]
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\User",
     *     inversedBy="cities",
     *     indexBy="id",
     *     fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinTable(
     *     name="cities_users",
     *     joinColumns={@ORM\JoinColumn(name="city_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $users;

    public function __construct(string $name, string $country)
    {
        $this->name = $name;
        $this->country = $country;
        $this->users = new ArrayCollection();
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

    public function addUser(User $user)
    {
        $userId = $user->getId();
        if (!$this->users->containsKey($userId)) {
            $this->users[$userId] = $user;
            $user->addCity($this);
        }

        return $this;
    }
}
