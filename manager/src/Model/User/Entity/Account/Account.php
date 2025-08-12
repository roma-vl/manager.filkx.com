<?php

declare(strict_types=1);

namespace App\Model\User\Entity\Account;

use App\Model\User\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'accounts')]
class Account
{
    #[ORM\Id]
    #[ORM\Column(type: 'user_account_id')]
    private Id $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 10, options: ['default' => 'en'])]
    private string $locale = 'en';

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownedAccounts')]
    private ?User $owner = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'account')]
    private Collection $users;

    public function __construct(Id $id, string $name, string $locale = 'en')
    {
        $this->id = $id;
        $this->name = $name;
        $this->locale = $locale;
        $this->createdAt = new \DateTimeImmutable();
        $this->users = new ArrayCollection();
    }

    public static function create(Id $id, string $name, string $locale = 'en'): self
    {
        return new self($id, $name, $locale);
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }
}
