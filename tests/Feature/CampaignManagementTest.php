<?php

namespace Tests\Feature;

use App\Filament\Resources\Campaigns\Pages\ManageCampaigns;
use App\Models\Barang;
use App\Models\Campaign;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CampaignManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_campaign_management(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/campaigns')
            ->assertOk();
    }

    public function test_admin_can_create_campaign(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $barang = Barang::create([
            'nama_barang' => 'Kopi Nusantara',
            'kategori' => 'Minuman',
            'stok' => 25,
            'harga' => 50000,
        ]);

        Livewire::actingAs(User::factory()->create())
            ->test(ManageCampaigns::class)
            ->callAction('create', [
                'name' => 'Promo Akhir Tahun',
                'barang_id' => $barang->id,
                'channel' => 'social_media',
                'objective' => 'Meningkatkan penjualan akhir tahun.',
                'target_audience' => 'Pelanggan retail',
                'budget' => 15000000,
                'start_date' => '2026-11-01',
                'end_date' => '2026-11-30',
                'status' => 'scheduled',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('campaigns', [
            'name' => 'Promo Akhir Tahun',
            'barang_id' => $barang->id,
            'channel' => 'social_media',
            'budget' => '15000000.00',
            'status' => 'scheduled',
        ]);

        $campaign = Campaign::where('name', 'Promo Akhir Tahun')->firstOrFail();
        $this->assertSame('Kopi Nusantara', $campaign->barang->nama_barang);
        $this->assertTrue($barang->campaigns()->whereKey($campaign->id)->exists());
    }

    public function test_admin_cannot_create_campaign_without_barang(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs(User::factory()->create())
            ->test(ManageCampaigns::class)
            ->callAction('create', [
                'name' => 'Promo Tanpa Barang',
                'channel' => 'email',
                'budget' => 1000000,
                'start_date' => '2026-11-01',
                'end_date' => '2026-11-30',
                'status' => 'draft',
            ])
            ->assertHasFormErrors(['barang_id']);

        $this->assertDatabaseMissing('campaigns', [
            'name' => 'Promo Tanpa Barang',
        ]);
    }

    public function test_campaign_end_date_cannot_be_before_start_date(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $barang = Barang::create([
            'nama_barang' => 'Teh Melati',
            'stok' => 10,
            'harga' => 30000,
        ]);

        Livewire::actingAs(User::factory()->create())
            ->test(ManageCampaigns::class)
            ->callAction('create', [
                'name' => 'Promo Tidak Valid',
                'barang_id' => $barang->id,
                'channel' => 'email',
                'budget' => 1000000,
                'start_date' => '2026-11-30',
                'end_date' => '2026-11-01',
                'status' => 'draft',
            ])
            ->assertHasFormErrors(['end_date']);

        $this->assertDatabaseMissing('campaigns', [
            'name' => 'Promo Tidak Valid',
        ]);
    }
}
