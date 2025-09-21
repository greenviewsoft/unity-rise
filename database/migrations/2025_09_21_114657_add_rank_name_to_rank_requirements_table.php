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
        Schema::table('rank_requirements', function (Blueprint $table) {
            $table->string('rank_name')->nullable()->after('rank');
        });
        
        // Update existing records with rank names
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
            \DB::table('rank_requirements')
                ->where('rank', $rank)
                ->update(['rank_name' => $name]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rank_requirements', function (Blueprint $table) {
            $table->dropColumn('rank_name');
        });
    }
};
