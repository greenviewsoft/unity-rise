<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RankRewardNotification extends Notification
{
    use Queueable;

    protected $rankReward;
    protected $oldRankName;
    protected $newRankName;

    /**
     * Create a new notification instance.
     */
    public function __construct($rankReward, $oldRankName = null, $newRankName = null)
    {
        $this->rankReward = $rankReward;
        $this->oldRankName = $oldRankName;
        $this->newRankName = $newRankName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $isUpgrade = $this->rankReward->old_rank != $this->rankReward->new_rank;
        $subject = $isUpgrade ? 'Rank Upgrade & Reward - Unity Rise' : 'Rank Reward Earned - Unity Rise';
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Congratulations! You have achieved a new milestone.');

        if ($isUpgrade) {
            $message->line('**Rank Upgrade:**')
                   ->line('Previous Rank: ' . ($this->oldRankName ?: "Rank {$this->rankReward->old_rank}"))
                   ->line('New Rank: ' . ($this->newRankName ?: "Rank {$this->rankReward->new_rank}"));
        }

        $message->line('**Reward Details:**')
               ->line('Reward Amount: $' . number_format($this->rankReward->reward_amount, 2))
               ->line('Reward Type: ' . ucfirst(str_replace('_', ' ', $this->rankReward->reward_type)))
               ->line('Date: ' . $this->rankReward->created_at->format('M d, Y h:i A'))
               ->line('The reward has been added to your account balance.')
               ->action('View Rank Progress', url('/user/rank/requirements'))
               ->line('Keep growing your network to achieve higher ranks!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'rank_reward_id' => $this->rankReward->id,
            'old_rank' => $this->rankReward->old_rank,
            'new_rank' => $this->rankReward->new_rank,
            'reward_amount' => $this->rankReward->reward_amount,
            'reward_type' => $this->rankReward->reward_type,
        ];
    }
}