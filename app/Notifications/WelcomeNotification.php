<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $plainPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
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
            ->subject('Welcome to Unity Rise - Account Created Successfully!')
            ->greeting('Welcome to Unity Rise!')
            ->line('Congratulations! Your account has been successfully created.')
            ->line('**Your Account Details:**')
            ->line('Username: ' . $this->user->username)
            ->line('Email: ' . $this->user->email)
            ->line('Phone: ' . $this->user->phone)
            ->line('Password: ' . $this->plainPassword)
            ->line('Invitation Code: ' . $this->user->invitation_code)
            ->line('')
            ->line('**Important Security Information:**')
            ->line('• Please keep your login credentials secure')
            ->line('• We recommend changing your password after first login')
            ->line('• Never share your account details with anyone')
            ->line('• Your wallet address has been automatically generated')
            ->line('')
            ->action('Login to Your Account', url('/login'))
            ->line('If you have any questions, please contact our support team.')
            ->line('Thank you for joining Unity Rise!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'username' => $this->user->username,
            'email' => $this->user->email,
        ];
    }
}