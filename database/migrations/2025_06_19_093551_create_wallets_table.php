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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('walletable_id');  //to make it fit for users and admins table
            $table->string('walletable_type');
            $table->unique(['walletable_id', 'walletable_type']);
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('temp_balance', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
