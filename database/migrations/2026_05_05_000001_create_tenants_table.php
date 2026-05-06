<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('holding_company_name');
            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->string('pic_name');
            $table->string('pic_email')->unique();
            $table->string('pic_phone')->nullable();
            $table->string('pic_job_title')->nullable();
            $table->unsignedInteger('estimated_users')->nullable();
            $table->string('status')->default('pending');
            $table->text('approval_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
