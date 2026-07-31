<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tourism_service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_type');
            $table->string('provider_type_other')->nullable();
            $table->string('commercial_name');
            $table->string('business_name')->nullable();
            $table->string('nit', 40)->nullable();
            $table->boolean('has_tourism_license')->default(false);
            $table->date('tourism_license_issued_at')->nullable();
            $table->date('tourism_license_renewed_at')->nullable();
            $table->string('legal_representative');
            $table->string('identity_document', 50);
            $table->string('landline', 40)->nullable();
            $table->string('whatsapp', 40);
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('other_social_network')->nullable();
            $table->string('department')->default('Tarija');
            $table->string('municipality');
            $table->string('address');
            $table->text('maps_location')->nullable();
            $table->string('lodging_type')->nullable();
            $table->string('lodging_type_other')->nullable();
            $table->unsignedInteger('room_count')->nullable();
            $table->unsignedInteger('guest_capacity')->nullable();
            $table->json('lodging_services')->nullable();
            $table->string('lodging_services_other')->nullable();
            $table->string('agency_type')->nullable();
            $table->json('agency_services')->nullable();
            $table->string('agency_services_other')->nullable();
            $table->json('tourism_modalities')->nullable();
            $table->string('package_types')->nullable();
            $table->text('main_destinations')->nullable();
            $table->boolean('has_guide_credential')->default(false);
            $table->date('guide_credential_issued_at')->nullable();
            $table->date('guide_credential_renewed_at')->nullable();
            $table->json('languages')->nullable();
            $table->string('language_other')->nullable();
            $table->json('specialties')->nullable();
            $table->string('specialty_other')->nullable();
            $table->unsignedInteger('experience_years')->nullable();
            $table->json('documents')->nullable();
            $table->boolean('declaration_accepted');
            $table->string('applicant_name');
            $table->string('application_place');
            $table->date('application_date');
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['provider_type', 'status']);
            $table->index('commercial_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourism_service_providers');
    }
};
