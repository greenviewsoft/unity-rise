<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositSuccessNotification extends Notification
{

    protected $deposit;

    /**
     * Create a new notification instance.
     */
    public function __construct($deposit)
    {
        $this->deposit = $deposit;
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
        return (new MailMessage)
            ->subject('Deposit Successful - Unity Rise')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your deposit has been successfully processed.')
            ->line('**Deposit Details:**')
            ->line('Amount: $' . number_format($this->deposit->amount, 2))
            ->line('Transaction ID: ' . $this->deposit->txid)
            ->line('Order Number: ' . $this->deposit->order_number)
            ->line('Date: ' . ($this->deposit->created_at ? $this->deposit->created_at->format('M d, Y h:i A') : now()->format('M d, Y h:i A')))
            ->line('Your account balance has been updated.')
            ->action('View Dashboard', url('/user/dashboard'))
            ->line('Thank you for using Unity Rise!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'deposit_id' => $this->deposit->id,
            'amount' => $this->deposit->amount,
            'txid' => $this->deposit->txid,
        ];
    }
}