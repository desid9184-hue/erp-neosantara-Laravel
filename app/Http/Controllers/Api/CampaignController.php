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

            'channel' => [
                'required',
                Rule::in(array_keys(Campaign::CHANNELS)),
            ],

            'objective' => ['nullable', 'string'],
            'target_audience' => ['nullable', 'string'],
            'budget' => ['nullable', 'numeric', 'min:0'],

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
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Campaign::latest()->get(),
        ]);
    }

    // POST /api/campaigns
    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->campaignRules()
        );

        $campaign = Campaign::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kampanye berhasil ditambahkan',
            'data' => $campaign,
        ], 201);
    }

    // PUT /api/campaigns/{id}
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate(
            $this->campaignRules()
        );

        $campaign->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kampanye berhasil diperbarui',
            'data' => $campaign,
        ]);
    }

    // DELETE /api/campaigns/{id}
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
