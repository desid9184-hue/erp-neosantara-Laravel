<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    // Validasi data kampanye
    private function campaignRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            // Barang wajib dipilih dan harus tersedia di tabel barangs
            'barang_id' => ['required', 'integer', 'exists:barangs,id'],

            'channel' => [
                'required',
                Rule::in(array_keys(Campaign::CHANNELS)),
            ],

            'objective' => ['nullable', 'string'],
            'target_audience' => ['nullable', 'string'],
            'budget' => ['required', 'numeric', 'min:0'],

            'start_date' => ['required', 'date'],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'sometimes',
                Rule::in(array_keys(Campaign::STATUSES)),
            ],
        ];
    }

    // GET /api/campaigns
    // Mengambil semua kampanye beserta data barang
    public function index()
    {
        $campaigns = Campaign::with('barang')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $campaigns,
        ]);
    }

    // GET /api/campaigns/{id}
    // Mengambil satu kampanye berdasarkan ID
    public function show($id)
    {
        $campaign = Campaign::with('barang')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $campaign,
        ]);
    }

    // POST /api/campaigns
    // Menambahkan kampanye baru
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->campaignRules()
        );

        $campaign = Campaign::create($validated);

        // Ambil kembali data beserta relasi barang
        $campaign->load('barang');

        return response()->json([
            'success' => true,
            'message' => 'Kampanye berhasil ditambahkan',
            'data' => $campaign,
        ], 201);
    }

    // PUT /api/campaigns/{id}
    // Memperbarui kampanye
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate(
            $this->campaignRules()
        );

        $campaign->update($validated);

        // Ambil data terbaru beserta barang
        $campaign->refresh()->load('barang');

        return response()->json([
            'success' => true,
            'message' => 'Kampanye berhasil diperbarui',
            'data' => $campaign,
        ]);
    }

    // DELETE /api/campaigns/{id}
    // Menghapus kampanye
    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kampanye berhasil dihapus',
        ]);
    }
}
