<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SocialLink;

class SocialLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $socialLinks = [
            [
                'name' => 'Telegram Support',
                'icon' => 'fab fa-telegram-plane',
                'url' => 'https://t.me/your_support_channel',
                'color' => '#0088cc',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'WhatsApp Support',
                'icon' => 'fab fa-whatsapp',
                'url' => 'https://wa.me/1234567890',
                'color' => '#25d366',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Email Support',
                'icon' => 'fas fa-envelope',
                'url' => 'mailto:support@yourcompany.com',
                'color' => '#ea4335',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Discord Community',
                'icon' => 'fab fa-discord',
                'url' => 'https://discord.gg/your_server',
                'color' => '#5865f2',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Twitter Support',
                'icon' => 'fab fa-twitter',
                'url' => 'https://twitter.com/your_handle',
                'color' => '#1da1f2',
                'is_active' => false,
                'sort_order' => 5
            ]
        ];

        foreach ($socialLinks as $link) {
            SocialLink::create($link);
        }
    }
}