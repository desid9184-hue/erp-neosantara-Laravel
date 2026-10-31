<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::updateOrCreate(
            ['email' => 'prospek@retail-makmur.example'],
            [
                'name' => 'Andi Pratama',
                'company_name' => 'PT Retail Makmur',
                'phone' => '081234567890',
                'source' => 'website',
                'status' => Customer::STATUS_PROSPECT,
                'follow_up_date' => now()->addDays(3)->toDateString(),
                'notes' => 'Tertarik dengan penawaran produk retail.',
            ],
        );

        Customer::updateOrCreate(
            ['email' => 'pengadaan@dagang-sejahtera.example'],
            [
                'name' => 'Siti Rahmawati',
                'company_name' => 'CV Dagang Sejahtera',
                'phone' => '081298765432',
                'source' => 'referral',
                'status' => Customer::STATUS_CUSTOMER,
                'notes' => 'Contoh pelanggan dari referensi.',
            ],
        );
    }
}
