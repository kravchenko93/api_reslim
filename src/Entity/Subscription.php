<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Dto\SubscriptionInfoDto;
use App\Enum\SubscriptionType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 * @ORM\Table(name="`subscription`")
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator(sequenceName="public.subscription_id_seq")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $info;

    /**
     * @ORM\Column(type="string")
     * @Assert\Choice(choices=SubscriptionType::ALL)
     */
    private $type;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info ?? [];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return SubscriptionInfoDto
     */
    public function getInfoAsObject(): SubscriptionInfoDto
    {
        $userInfoDto = new SubscriptionInfoDto($this->getType(), $this->getInfo());

        return $userInfoDto;
    }

    /**
     * @param SubscriptionInfoDto $info
     * @return void
     */
    public function setInfoAsObject($info): void
    {
        var_dump($info->getFields());exit;
        $this->info = $info->getFields();
    }

    /**
     * @param array $info
     * @return void
     */
    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

}
