<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('universities')
            ->whereNull('trial_ends_at')
            ->orWhere('trial_ends_at', '<', now())
            ->update(['trial_ends_at' => now()->addDays(14)]);
    }

    public function down(): void
    {
    }
};
