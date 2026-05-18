<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('stage')->default('approach_lead_client')->after('status');
        });

        // Migrate existing statuses to the closest matching new stage
        DB::table('projects')->update(['stage' => 'project_progress']);
        DB::table('projects')->where('status', 'completed')->update(['stage' => 'project_handover']);
        DB::table('projects')->where('status', 'archived')->update(['stage' => 'project_handover']);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
};
