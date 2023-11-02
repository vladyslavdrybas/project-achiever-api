<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AnalyticsTrackNotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalyticsTrackNotificationRepository::class, readOnly: false)]
#[ORM\Table(name: "analytics_track_notification")]
class AnalyticsTrackNotification extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $user;

    #[ORM\Column(name: 'message', type: Types::JSON, unique: false, nullable: false)]
    protected array $message;

    /**
     * @return \App\Entity\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param \App\Entity\User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getMessage(): array
    {
        return $this->message;
    }

    /**
     * @param array $message
     */
    public function setMessage(array $message): void
    {
        $this->message = $message;
    }
}
