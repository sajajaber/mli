<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('footer_settings')) {
            Schema::create('footer_settings', function (Blueprint $table) {
                $table->id();
                $table->string('tagline')->nullable();
                $table->text('description')->nullable();
                $table->string('phone_primary')->nullable();
                $table->string('phone_secondary')->nullable();
                $table->string('email')->nullable();
                $table->text('office_address')->nullable();
                $table->text('map_url')->nullable();
                $table->text('facebook_url')->nullable();
                $table->text('linkedin_url')->nullable();
                $table->text('privacy_policy_url')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('footer_settings') && DB::table('footer_settings')->count() === 0) {
            $now = now();

            DB::table('footer_settings')->insert([
                'tagline' => 'Media Link International',
                'description' => 'Connecting stories, media and people through meaningful communication and creative experiences.',
                'phone_primary' => '+961 1 395 901',
                'phone_secondary' => '+961 1 395 952',
                'email' => 'info@mli-lb.com',
                'office_address' => "Tayouneh, Omar Bayham St.\nAl-Nakhil Bldg, Second Floor\nBeirut, Lebanon",
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=Media+Link+International+Beirut',
                'facebook_url' => null,
                'linkedin_url' => null,
                'privacy_policy_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
