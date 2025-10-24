<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralCommissionNotification extends Notification
{
    use Queueable;

    protected $commission;
    protected $referredUser;
    protected $level;

    /**
     * Create a new notification instance.
     */
    public function __construct($commission, $referredUser = null, $level = null)
    {
        $this->commission = $commission;
        $this->referredUser = $referredUser;
        $this->level = $level;
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
        $referredName = $this->referredUser ? $this->referredUser->name : 'Your referral';
        $levelText = $this->level ? "Level {$this->level}" : '';
        
        return (new MailMessage)
            ->subject('Referral Commission Earned - Unity Rise')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Congratulations! You have earned a referral commission.')
            ->line('**Commission Details:**')
            ->line('Amount: $' . number_format($this->commission->amount, 2))
            ->line('From: ' . $referredName)
            ->line($levelText ? 'Level: ' . $levelText : '')
            ->line('Commission Rate: ' . ($this->commission->commission_rate ?? 'N/A') . '%')
            ->line('Date: ' . $this->commission->created_at->format('M d, Y h:i A'))
            ->line('The commission has been added to your account balance.')
            ->action('View Dashboard', url('/user/dashboard'))
            ->line('Keep referring friends to earn more commissions!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'commission_id' => $this->commission->id,
            'amount' => $this->commission->amount,
            'level' => $this->level,
            'referred_user_id' => $this->referredUser ? $this->referredUser->id : null,
        ];
    }
}