<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rank_requirements', function (Blueprint $table) {
            $table->id();
            $table->integer('rank')->unique();
            $table->string('rank_name')->nullable();
            $table->decimal('team_business_volume', 15, 2)->default(0);
            $table->integer('count_level')->default(0);
            $table->decimal('personal_investment', 15, 2)->default(0);
            $table->decimal('reward_amount', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Seed rank names
        $rankNames = [
            1 => 'Rookie',
            2 => 'Bronze',
            3 => 'Silver',
            4 => 'Gold',
            5 => 'Diamond',
            6 => 'Master',
            7 => 'Grand Master',
            8 => 'Champion',
            9 => 'Legend',
            10 => 'Mythic',
            11 => 'Immortal',
            12 => 'Divine'
        ];
        
        foreach ($rankNames as $rank => $name) {
            \DB::table('rank_requirements')->insert([
                'rank' => $rank,
                'rank_name' => $name,
                'team_business_volume' => 0,
                'count_level' => 0,
                'personal_investment' => 0,
                'reward_amount' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rank_requirements');
    }
};
