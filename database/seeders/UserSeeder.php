<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@nb-cci.gov.ng'],
            [
                'name' => 'Super Admin',
                'phone' => '+2348000000001',
                'role' => 'super_admin',
                'account_status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $admin = User::updateOrCreate(
            ['email' => 'reviewer@nb-cci.gov.ng'],
            [
                'name' => 'NB-CCI Admin',
                'phone' => '+2348000000002',
                'role' => 'admin',
                'account_status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $businessOwner = User::updateOrCreate(
            ['email' => 'business@example.com'],
            [
                'name' => 'John Business',
                'phone' => '+2348000000003',
                'role' => 'business_owner',
                'account_status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'official@nb-cci.gov.ng'],
            [
                'name' => 'Official Agent',
                'phone' => '+2348000000004',
                'role' => 'government_official',
                'account_status' => 'active',
                'agency_id' => 1,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        Business::updateOrCreate(
            ['registration_number' => 'RC-1234567'],
            [
                'user_id' => $businessOwner->id,
                'registry_number' => 'NBCCI-NG-2026-000001',
                'business_name' => 'John Business Enterprises',
                'trading_name' => 'JBE Trading',
                'cac_number' => 'CAC-7654321',
                'nrs_number' => 'NRS-1122334',
                'nin' => '12345678901',
                'business_type' => 'Limited Liability Company',
                'sector_id' => Sector::where('name', 'Trading/Import-Export')->value('id'),
                'description' => 'General merchandise and import-export business',
                'address' => '12 Business Avenue, Victoria Island',
                'state' => 'Lagos',
                'lga' => 'Lagos Island',
                'city' => 'Lagos',
                'phone' => '+2348000000003',
                'email' => 'business@example.com',
                'website' => 'https://jbe-trading.example.com',
                'contact_person_name' => 'John Business',
                'contact_person_phone' => '+2348000000003',
                'contact_person_email' => 'business@example.com',
                'trade_activity' => 'Import and Export',
                'border_route' => 'Apapa Port',
                'status' => 'verified',
                'verified_at' => now(),
                'verification_expires_at' => now()->addYear(),
            ],
        );
    }
}
