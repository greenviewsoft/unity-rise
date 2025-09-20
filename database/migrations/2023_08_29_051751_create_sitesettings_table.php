<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sitesettings', function (Blueprint $table) {
            $table->id();
            
            $table->string('name')->default(config('app.name'));
            $table->string('title')->default(config('app.name'));
            $table->string('logo')->default('public/assets/admin/assets/images/logo-white.png');
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('site_location')->nullable();
            $table->string('support_url')->nullable();

            $table->decimal('accumulated_profite', 15, 4)->default(46708005.0666);
            $table->decimal('accumulated_usd', 15, 2)->default(643.63);
            $table->bigInteger('membership')->default(17917308);
            $table->decimal('membership_usd', 15, 2)->default(643.63);

            $table->boolean('development')->default(true);
            $table->string('app_url')->default('https://localhost/trc20/' . strtolower(config('app.name')));
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sitesettings');
    }
};
