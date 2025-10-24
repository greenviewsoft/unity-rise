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
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your investment has been successfully created.')
            ->line('**Investment Details:**')
            ->line('Plan: ' . $planName)
            ->line('Amount: $' . number_format($this->investment->amount, 2))
            ->line('Duration: ' . ($this->plan ? $this->plan->duration . ' days' : 'N/A'))
            ->line('Expected Return: ' . ($this->plan ? $this->plan->profit_percentage . '%' : 'N/A'))
            ->line('Date: ' . $this->investment->created_at->format('M d, Y h:i A'))
            ->line('Your investment is now active and generating returns.')
            ->action('View Investments', url('/user/investment'))
            ->line('Thank you for investing with Unity Rise!');
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