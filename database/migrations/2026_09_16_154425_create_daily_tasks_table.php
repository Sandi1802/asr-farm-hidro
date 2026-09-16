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
        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('shift'); // 'opening', 'siang', 'closing', 'pr'
            $table->string('task_name');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('is_pr')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_tasks');
    }
};
