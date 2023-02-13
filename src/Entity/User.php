<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Dto\UserInfoDto;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(
 *  fields="email",
 *  message="User with same email is exist",
 *  groups={"creation"}
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="public.user_id_seq")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles;

    /**
     * @ORM\Column(type="json")
     */
    private $info;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="UserSubscription", mappedBy="user")
     * @ORM\OrderBy({"dateFinish" = "DESC"})
     */
    private $userSubscriptions;

    public function __construct()
    {
        $this->userSubscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles ?? [];

        return array_unique($roles);
    }

    /**
     * @param array|string[] $roles
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info ?? [];
    }

    /**
     * @return UserInfoDto
     */
    public function getInfoAsObject(): UserInfoDto
    {
        $userInfoDto = new UserInfoDto();
        foreach ($this->getInfo() as $key => $val) {
            $userInfoDto->$key = $val;
        }
        return $userInfoDto;
    }

    /**
     * @param UserInfoDto $info
     * @return void
     */
    public function setInfoAsObject($info): void
    {
        $this->info = $info;
    }

    /**
     * @param array $info
     * @return void
     */
    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return UserSubscription|null
     */
    public function getActiveUserSubscription(): ?UserSubscription
    {
        $activeSubscriptions = $this->getUserSubscriptions()->filter(function(UserSubscription $userSubscription) {
            return true === $userSubscription->isPaid() && $userSubscription->getDateFinish() >= new \DateTime('now');
        });

        return $activeSubscriptions[0] ?? null;
    }

    /**
     * @return UserSubscription[]|ArrayCollection
     */
    public function getUserSubscriptions()
    {
        return $this->userSubscriptions;
    }

    public function __toString(): string
    {
        return '#' . $this->getId() . ' ' . $this->getEmail();
    }

}
