<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvestmentSuccessNotification extends Notification
{
    use Queueable;

    protected $investment;
    protected $plan;

    /**
     * Create a new notification instance.
     */
    public function __construct($investment, $plan = null)
    {
        $this->investment = $investment;
        $this->plan = $plan;
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
        $planName = $this->plan ? $this->plan->plan_name : 'Investment Plan';
        
        return (new MailMessage)
            ->subject('Investment Successful - Unity Rise')
            ->greeting('Hello ' . $this->investment->user->name . '!')
            ->line('Your investment has been successfully created.')
            ->line('')
            ->line('Investment Details:')
            ->line('')
            ->line('Plan: ' . ($this->plan ? $this->plan->name : 'N/A'))
            ->line('')
            ->line('Amount: $' . number_format($this->investment->amount, 2))
            ->line('')
            ->line('Duration: ' . ($this->plan ? $this->plan->duration_days : 'N/A') . ' days')
            ->line('')
            ->line('Expected Return: ' . ($this->plan ? $this->plan->total_profit_percentage : 'N/A') . '%')
            ->line('')
            ->line('Date: ' . $this->investment->created_at->format('M d, Y h:i A'))
            ->line('')
            ->line('Your investment is now active and generating returns.')
            ->action('View Investments', url('/user/investments'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'investment_id' => $this->investment->id,
            'amount' => $this->investment->amount,
            'plan_name' => $this->plan ? $this->plan->plan_name : null,
        ];
    }
}