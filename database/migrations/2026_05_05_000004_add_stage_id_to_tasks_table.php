<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('stage_id')->nullable()->after('project_id')->constrained('workflow_stages')->nullOnDelete();
            $table->index('stage_id');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['stage_id']);
            $table->dropIndex(['stage_id']);
            $table->dropColumn('stage_id');
        });
    }
};
