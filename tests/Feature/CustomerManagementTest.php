<?php

namespace Tests\Feature;

use App\Filament\Resources\Customers\Pages\ManageCustomers;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_customer_management(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/customers')
            ->assertOk();
    }

    public function test_user_can_search_prospects_and_customers(): void
    {
        $matchingCustomer = Customer::factory()->create([
            'name' => 'Dewi Anggraini',
            'company_name' => 'PT Berkah Nusantara',
        ]);
        $otherCustomer = Customer::factory()->create([
            'name' => 'Budi Santoso',
            'company_name' => 'CV Maju Jaya',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs(User::factory()->create())
            ->test(ManageCustomers::class)
            ->searchTable('Berkah')
            ->assertCanSeeTableRecords([$matchingCustomer])
            ->assertCanNotSeeTableRecords([$otherCustomer]);
    }

    public function test_user_can_create_a_prospect_linked_to_a_campaign(): void
    {
        $campaign = Campaign::create([
            'name' => 'Kampanye Retail November',
            'channel' => 'email',
            'budget' => 5000000,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'status' => 'active',
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs(User::factory()->create())
            ->test(ManageCustomers::class)
            ->callAction('create', [
                'name' => 'Dewi Anggraini',
                'company_name' => 'PT Berkah Nusantara',
                'email' => 'dewi@berkah.example',
                'phone' => '081234567890',
                'source' => 'campaign',
                'campaign_id' => $campaign->id,
                'status' => Customer::STATUS_PROSPECT,
                'follow_up_date' => '2026-11-10',
                'notes' => 'Meminta katalog produk.',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('customers', [
            'email' => 'dewi@berkah.example',
            'campaign_id' => $campaign->id,
            'status' => Customer::STATUS_PROSPECT,
            'source' => 'campaign',
        ]);
    }

    public function test_user_can_convert_a_prospect_to_a_customer(): void
    {
        $prospect = Customer::factory()->prospect()->create();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs(User::factory()->create())
            ->test(ManageCustomers::class)
            ->callTableAction('convertToCustomer', $prospect)
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('customers', [
            'id' => $prospect->id,
            'status' => Customer::STATUS_CUSTOMER,
        ]);
    }

    public function test_user_cannot_create_contacts_with_an_invalid_email(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs(User::factory()->create())
            ->test(ManageCustomers::class)
            ->callAction('create', [
                'name' => 'Kontak Tidak Valid',
                'email' => 'bukan-email',
                'source' => 'website',
                'status' => Customer::STATUS_PROSPECT,
            ])
            ->assertHasFormErrors(['email' => 'email']);

        $this->assertDatabaseMissing('customers', [
            'name' => 'Kontak Tidak Valid',
        ]);
    }

    public function test_deleting_a_campaign_keeps_its_customer_and_removes_the_campaign_link(): void
    {
        $campaign = Campaign::create([
            'name' => 'Kampanye Ritel',
            'channel' => 'event',
            'budget' => 2500000,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'status' => 'completed',
        ]);
        $customer = Customer::factory()->create([
            'campaign_id' => $campaign->id,
        ]);

        $campaign->delete();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'campaign_id' => null,
        ]);
    }
}
