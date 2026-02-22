<?php

namespace App\Http\Controllers;

use App\Models\AnimeTracking;
use App\Services\AniListService;
use Illuminate\Http\Request;

class AnimeController extends Controller
{
    protected $anilist;
    private const STATUS_OPTIONS = ['airing'];
    private const SEASON_OPTIONS = []; // Empty array to disable season
    private const SORT_OPTIONS = ['popular', 'recent'];
    private const TYPE_OPTIONS = ['shows', 'movies'];
    private const GENRE_OPTIONS = [
        'Action',
        'Fantasy',
        'Comedy',
        'Drama',
        'Science Fiction',
        'Adventure',
        'Romance',
        'Mystery',
        'Slice of Life',
        'Horror',
        'Sports',
        'Crime',
        'Music',
        'War',
        'Thriller',
        'Gourmet',
        'Historical',
    ];

    public function __construct(AniListService $anilist)
    {
        $this->anilist = $anilist;
    }

    public function index()
    {
        $genres = self::GENRE_OPTIONS;
        $filters = $this->resolveFilters(request(), $genres);
        $page = max(1, (int) request()->query('page', 1));
        
        $popularResponse = $this->anilist->getPopularAnime(10, $page, $filters);

        return view('anime.index', [
            'popularAnime' => $popularResponse['items'],
            'pageInfo' => $popularResponse['pageInfo'],
            'genres' => $genres,
            'filters' => $filters,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            return redirect()->route('home');
        }

        $genres = self::GENRE_OPTIONS;
        $filters = $this->resolveFilters($request, $genres);
        $page = max(1, (int) $request->query('page', 1));
        
        $searchResponse = $this->anilist->searchAnime($query, 10, $page, $filters);

        return view('anime.search', [
            'results' => $searchResponse['items'],
            'query' => $query,
            'pageInfo' => $searchResponse['pageInfo'],
            'genres' => $genres,
            'filters' => $filters,
        ]);
    }

    public function show($id)
    {
        $anime = $this->anilist->getAnimeDetail($id);

        if (!$anime) {
            abort(404);
        }

        $tracking = [
            'watched_episodes' => 0,
            'status' => 'watching',
            'score' => null,
            'rating_tier' => null,
        ];

        if (auth()->check()) {
            $trackingRecord = AnimeTracking::query()
                ->where('user_id', auth()->id())
                ->where('anime_id', (int) $id)
                ->first();

            if ($trackingRecord) {
                $tracking = [
                    'watched_episodes' => $trackingRecord->watched_episodes,
                    'status' => $trackingRecord->status,
                    'score' => $trackingRecord->score,
                    'rating_tier' => $this->scoreToRatingTier($trackingRecord->score),
                ];
            }
        }

        return view('anime.show', [
            'anime' => $anime,
            'tracking' => $tracking,
            'canTrack' => auth()->check(),
        ]);
    }

    public function updateProgress(Request $request, $id)
    {
        $validated = $request->validate([
            'watched_episodes' => 'required|integer|min:0',
            'status' => 'required|in:watching,completed,plan_to_watch,paused',
            'rating_tier' => 'nullable|in:meh,aight,top_tier',
        ]);

        AnimeTracking::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'anime_id' => (int) $id,
            ],
            [
                'watched_episodes' => (int) $validated['watched_episodes'],
                'status' => $validated['status'],
                'score' => $this->ratingTierToScore($validated['rating_tier'] ?? null),
            ]
        );

        return redirect()->back()->with('success', 'Progress updated!');
    }

    private function resolveFilters(Request $request, array $genres): array
    {
        $genre = $request->query('genre');
        if ($genre === 'any' || (!empty($genre) && !in_array($genre, $genres, true))) {
            $genre = null;
        }

        $status = $request->query('status');
        if ($status === 'any' || !in_array($status, self::STATUS_OPTIONS, true)) {
            $status = null;
        }

        // Season is now always null (disabled)
        $season = null;

        $sort = $request->query('sort', 'popular');
        if (!in_array($sort, self::SORT_OPTIONS, true)) {
            $sort = 'popular';
        }

        $type = $request->query('type');
        if ($type === 'any' || !in_array($type, self::TYPE_OPTIONS, true)) {
            $type = null;
        }

        return [
            'genre' => $genre,
            'status' => $status,
            'season' => $season,
            'sort' => $sort,
            'type' => $type,
        ];
    }

    private function ratingTierToScore(?string $ratingTier): ?float
    {
        return match ($ratingTier) {
            'meh' => 4.0,
            'aight' => 7.0,
            'top_tier' => 10.0,
            default => null,
        };
    }

    private function scoreToRatingTier($score): ?string
    {
        if ($score === null) {
            return null;
        }

        $score = (float) $score;

        if ($score >= 9.0) {
            return 'top_tier';
        }

        if ($score >= 6.0) {
            return 'aight';
        }

        return 'meh';
    }
}
