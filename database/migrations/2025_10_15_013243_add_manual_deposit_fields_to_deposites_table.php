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
        Schema::table('deposites', function (Blueprint $table) {
            $table->string('deposit_type')->default('auto')->after('currency'); // 'auto' or 'manual'
            $table->string('screenshot')->nullable()->after('deposit_type'); // Screenshot file path
            $table->string('transaction_hash')->nullable()->after('screenshot'); // Optional transaction hash
            $table->text('user_notes')->nullable()->after('transaction_hash'); // User notes/comments
            $table->text('admin_notes')->nullable()->after('user_notes'); // Admin notes for approval/rejection
            $table->timestamp('approved_at')->nullable()->after('admin_notes'); // When approved
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at'); // Admin who approved
            
            // Add foreign key for approved_by (assuming users table has admin users)
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposites', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'deposit_type',
                'screenshot',
                'transaction_hash',
                'user_notes',
                'admin_notes',
                'approved_at',
                'approved_by'
            ]);
        });
    }
};
