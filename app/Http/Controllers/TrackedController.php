<?php

namespace App\Http\Controllers;

use App\Models\AnimeTracking;
use App\Services\AniListService;
use Illuminate\Http\Request;

class TrackedController extends Controller
{
    public function index(Request $request, AniListService $anilist)
    {
        $statusOptions = ['watching', 'completed', 'plan_to_watch', 'paused', 'all'];
        $selectedStatus = $request->query('status', 'watching');
        if (!in_array($selectedStatus, $statusOptions, true)) {
            $selectedStatus = 'watching';
        }

        $trackingsQuery = AnimeTracking::query()
            ->where('user_id', $request->user()->id)
            ->latest('updated_at');

        if ($selectedStatus !== 'all') {
            $trackingsQuery->where('status', $selectedStatus);
        }

        $trackings = $trackingsQuery->get();

        $animeMap = [];
        if ($trackings->isNotEmpty()) {
            $animeList = $anilist->getAnimeByIds($trackings->pluck('anime_id')->all());
            $animeMap = collect($animeList)->keyBy('id')->all();
        }

        return view('tracked.index', [
            'trackings' => $trackings,
            'animeMap' => $animeMap,
            'selectedStatus' => $selectedStatus,
            'statusOptions' => $statusOptions,
        ]);
    }
}
