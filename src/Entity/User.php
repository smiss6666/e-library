<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Contracts\UserInterface;
use App\Entity\Traits\ActiveEntityTrait;
use App\Entity\Traits\Contact\FirstNameEntityTrait;
use App\Entity\Traits\Contact\LastNameEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_filter;
use function implode;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="users",
 *     indexes={
 *          @ORM\Index(name="idx_active", columns={"active"}),
 *     }
 * )
 * @ORM\EntityListeners({"App\EventListener\Doctrine\UserEntityListener"})
 */
class User extends AbstractEntity implements UserInterface
{

    use FirstNameEntityTrait,
        LastNameEntityTrait,
        ActiveEntityTrait;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reading", mappedBy="user")
     */
    protected Collection $reading;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user")
     */
    protected Collection $orders;

    /**
     * @ORM\Column(name="username", type="string", length=60, nullable=false)
     */
    protected ?string $username = null;

    /**
     * @ORM\Column(name="password", type="string", length=64, nullable=false)
     */
    protected ?string $password = null;

    /**
     * @var null|string
     * not mapped property
     */
    protected ?string $plainPassword = null;

    /**
     * @ORM\Column(name="email", type="string", length=60, nullable=true)
     */
    protected ?string $email = null;

    /**
     * @ORM\Column(name="salt", type="string", nullable=false)
     */
    protected string $salt;

    /**
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    protected array $roles = [UserInterface::ROLE_USER];

    public function __toString(): string
    {
        $labels = [];
        if (!$this->getId()) {
            $labels[] = 'New User';
        } else {
            $labels[] = $this->getUsername();
            $labels[] = !$this->isActive() ? '(disabled)' : null;
        }

        return implode(' ', array_filter($labels));
    }

    public function __construct()
    {
        try {
            $this->salt = bin2hex(random_bytes(12));
        } catch (\Exception $e) {
            $this->salt = '';
        }
        $this->reading = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isLibrarian(): bool
    {
        return $this->isAdmin() || $this->hasRole(self::ROLE_LIBRARIAN);
    }

    public function isUser(): bool
    {
        return (bool)$this->getId();
    }

    public function isReader(): bool
    {
        return $this->isUser() || $this->hasRole(self::ROLE_READER);
    }

    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        if ($plainPassword) {
            //$this->setUpdatedAt(new DateTime());
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function getRole(): ?string
    {
        return $this->roles[0] ?? '';
    }

    public function eraseCredentials()
    {
    }

    public function serialize(): ?string
    {
        return serialize(
            [
                $this->id,
                $this->username,
                $this->password,
                $this->salt,
            ]
        );
    }

    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ] = unserialize($serialized, ['allowed_classes' => [self::class]]);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Reading[]
     */
    public function getReading(): Collection
    {
        return $this->reading;
    }

    public function addReading(Reading $reading): self
    {
        if (!$this->reading->contains($reading)) {
            $this->reading[] = $reading;
            $reading->setUser($this);
        }

        return $this;
    }

    public function removeReading(Reading $reading): self
    {
        if ($this->reading->removeElement($reading)) {
            // set the owning side to null (unless already changed)
            if ($reading->getUser() === $this) {
                $reading->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

}
