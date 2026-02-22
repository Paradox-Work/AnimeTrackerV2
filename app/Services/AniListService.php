<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AniListService
{
    private $endpoint = 'https://graphql.anilist.co';
    private const CACHE_VERSION = 'v8'; // Increment version to clear cache

    // Map UI genres to API genres (for regular genres)
    private const GENRE_MAPPING = [
        'Science Fiction' => 'Sci-Fi',
    ];

    // These should be searched as TAGS, not genres
// These should be searched as TAGS, not genres
// These should be searched as TAGS, not genres
private const TAG_GENRES = [
    'Crime',
    'Gourmet',
    'War',
    'Historical',
    'Suspense',
    'Award Winning',
];

// Map UI genres to actual AniList tag names
private const TAG_MAPPING = [
    'Crime' => 'Mafia',
    'Gourmet' => 'Restaurant',  // Changed from 'Food' to 'Restaurant'
    'War' => 'Military',
    'Historical' => 'Historical',
    'Suspense' => 'Suspense',
    'Award Winning' => null,  // Keep as null
];

    public function searchAnime($searchTerm, $perPage = 10, $page = 1, array $filters = [])
    {
        $cacheKey = 'anilist.search.' . self::CACHE_VERSION . '.' . md5(json_encode([$searchTerm, $perPage, $page, $filters]));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($searchTerm, $perPage, $page, $filters) {
            $query = '
            query SearchAnime(
                $search: String,
                $perPage: Int,
                $page: Int,
                $genre: String,
                $genreNotIn: [String],
                $tag: String,
                $status: MediaStatus,
                $format: MediaFormat,
                $formatIn: [MediaFormat],
                $isAdult: Boolean,
                $sort: [MediaSort]
            ) {
                Page(page: $page, perPage: $perPage) {
                    pageInfo {
                        currentPage
                        lastPage
                        hasNextPage
                        total
                        perPage
                    }
                    media(
                        search: $search,
                        type: ANIME,
                        genre: $genre,
                        genre_not_in: $genreNotIn,
                        tag: $tag,
                        status: $status,
                        format: $format,
                        format_in: $formatIn,
                        isAdult: $isAdult,
                        sort: $sort
                    ) {
                        id
                        title {
                            romaji
                            english
                        }
                        coverImage {
                            medium
                            large
                            extraLarge
                            color
                        }
                        episodes
                        format
                        status
                        averageScore
                        description
                        genres
                        tags {
                            name
                        }
                        popularity
                        startDate {
                            year
                            month
                            day
                        }
                    }
                }
            }';

            $variables = [
                'search' => $searchTerm,
                'perPage' => $perPage,
                'page' => $page,
                'isAdult' => false,
                'genreNotIn' => ['Hentai', 'Ecchi'],
            ];

                            // Handle genre/tag filter
                if (!empty($filters['genre'])) {
                    // Check if this is a tag-based genre
                    if (in_array($filters['genre'], self::TAG_GENRES)) {
                        // Map to actual tag name
                        $tagName = self::TAG_MAPPING[$filters['genre']] ?? null;
                        if ($tagName) {
                            $variables['tag'] = $tagName;
                        }
                        // If null (like Award Winning), don't add any filter
                    } else {
                        // Regular genre - apply mapping if needed
                        $apiGenre = self::GENRE_MAPPING[$filters['genre']] ?? $filters['genre'];
                        $variables['genre'] = $apiGenre;
                    }
                }

            // Handle status filter
            if (!empty($filters['status'])) {
                $status = $this->mapStatus($filters['status']);
                if ($status) {
                    $variables['status'] = $status;
                }
            }

            // Handle type filter
            if (!empty($filters['type'])) {
                $formatData = $this->mapType($filters['type']);
                if ($formatData['format']) {
                    $variables['format'] = $formatData['format'];
                }
                if ($formatData['formatIn']) {
                    $variables['formatIn'] = $formatData['formatIn'];
                }
            }

            // Handle sort
            $variables['sort'] = $this->mapSort($filters['sort'] ?? 'popular', $filters);

            $response = Http::post($this->endpoint, [
                'query' => $query,
                'variables' => $variables,
            ]);

            if ($response->failed()) {
                \Log::error('AniList API Error: ' . $response->body());
                return [
                    'items' => [],
                    'pageInfo' => [
                        'currentPage' => 1,
                        'lastPage' => 1,
                        'hasNextPage' => false,
                        'total' => 0,
                        'perPage' => $perPage,
                    ],
                ];
            }

            $pageData = $response->json()['data']['Page'] ?? [];

            return [
                'items' => $pageData['media'] ?? [],
                'pageInfo' => $pageData['pageInfo'] ?? [
                    'currentPage' => $page,
                    'lastPage' => 1,
                    'hasNextPage' => false,
                    'total' => count($pageData['media'] ?? []),
                    'perPage' => $perPage,
                ],
            ];
        });
    }

    public function getPopularAnime($perPage = 10, $page = 1, array $filters = [])
    {
        $cacheKey = 'anilist.popular.' . self::CACHE_VERSION . '.' . md5(json_encode([$perPage, $page, $filters]));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($perPage, $page, $filters) {
            $query = '
            query PopularAnime(
                $perPage: Int,
                $page: Int,
                $genre: String,
                $genreNotIn: [String],
                $tag: String,
                $status: MediaStatus,
                $format: MediaFormat,
                $formatIn: [MediaFormat],
                $isAdult: Boolean,
                $sort: [MediaSort]
            ) {
                Page(page: $page, perPage: $perPage) {
                    pageInfo {
                        currentPage
                        lastPage
                        hasNextPage
                        total
                        perPage
                    }
                    media(
                        type: ANIME,
                        genre: $genre,
                        genre_not_in: $genreNotIn,
                        tag: $tag,
                        status: $status,
                        format: $format,
                        format_in: $formatIn,
                        isAdult: $isAdult,
                        sort: $sort
                    ) {
                        id
                        title {
                            romaji
                            english
                        }
                        coverImage {
                            medium
                            large
                            extraLarge
                            color
                        }
                        episodes
                        status
                        averageScore
                        format
                        genres
                        tags {
                            name
                        }
                        popularity
                        startDate {
                            year
                            month
                            day
                        }
                    }
                }
            }';

            $variables = [
                'perPage' => $perPage,
                'page' => $page,
                'isAdult' => false,
                'genreNotIn' => ['Hentai', 'Ecchi'],
            ];

            // Handle genre/tag filter
            if (!empty($filters['genre'])) {
                // Check if this is a tag-based genre
                if (in_array($filters['genre'], self::TAG_GENRES)) {
                    // Map to actual tag name
                    $tagName = self::TAG_MAPPING[$filters['genre']] ?? $filters['genre'];
                    $variables['tag'] = $tagName;
                } else {
                    // Regular genre - apply mapping if needed
                    $apiGenre = self::GENRE_MAPPING[$filters['genre']] ?? $filters['genre'];
                    $variables['genre'] = $apiGenre;
                }
            }

            // Handle status filter
            if (!empty($filters['status'])) {
                $status = $this->mapStatus($filters['status']);
                if ($status) {
                    $variables['status'] = $status;
                }
            }

            // Handle type filter
            if (!empty($filters['type'])) {
                $formatData = $this->mapType($filters['type']);
                if ($formatData['format']) {
                    $variables['format'] = $formatData['format'];
                }
                if ($formatData['formatIn']) {
                    $variables['formatIn'] = $formatData['formatIn'];
                }
            }

            // Handle sort
            $variables['sort'] = $this->mapSort($filters['sort'] ?? 'popular', $filters);

            $response = Http::post($this->endpoint, [
                'query' => $query,
                'variables' => $variables,
            ]);

            if ($response->failed()) {
                \Log::error('AniList API Error: ' . $response->body());
                return [
                    'items' => [],
                    'pageInfo' => [
                        'currentPage' => 1,
                        'lastPage' => 1,
                        'hasNextPage' => false,
                        'total' => 0,
                        'perPage' => $perPage,
                    ],
                ];
            }

            $pageData = $response->json()['data']['Page'] ?? [];

            return [
                'items' => $pageData['media'] ?? [],
                'pageInfo' => $pageData['pageInfo'] ?? [
                    'currentPage' => $page,
                    'lastPage' => 1,
                    'hasNextPage' => false,
                    'total' => count($pageData['media'] ?? []),
                    'perPage' => $perPage,
                ],
            ];
        });
    }

    public function getAnimeDetail($id)
    {
        $query = '
        query AnimeDetail($id: Int, $genreNotIn: [String], $isAdult: Boolean) {
            Media(id: $id, type: ANIME, genre_not_in: $genreNotIn, isAdult: $isAdult) {
                id
                title {
                    romaji
                    english
                    native
                }
                coverImage {
                    large
                    extraLarge
                    color
                }
                bannerImage
                description
                episodes
                duration
                status
                format
                startDate {
                    year
                    month
                    day
                }
                endDate {
                    year
                    month
                    day
                }
                genres
                tags {
                    name
                }
                averageScore
                popularity
                studios(isMain: true) {
                    nodes {
                        name
                    }
                }
                nextAiringEpisode {
                    episode
                    airingAt
                }
            }
        }';

        $variables = [
            'id' => $id,
            'genreNotIn' => ['Hentai', 'Ecchi'],
            'isAdult' => false,
        ];

        $response = Http::post($this->endpoint, [
            'query' => $query,
            'variables' => $variables,
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json()['data']['Media'] ?? null;
    }

    public function getGenreCollection(): array
    {
        return Cache::remember('anilist.genre_collection', now()->addHours(12), function () {
            $query = '
            query GenreCollection {
                GenreCollection
            }';

            $response = Http::post($this->endpoint, [
                'query' => $query,
            ]);

            $genres = $response->json()['data']['GenreCollection'] ?? [];
            sort($genres);

            return $genres;
        });
    }

    public function getAnimeByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $query = '
        query AnimeByIds($ids: [Int], $genreNotIn: [String], $isAdult: Boolean) {
            Page(page: 1, perPage: 50) {
                media(id_in: $ids, type: ANIME, genre_not_in: $genreNotIn, isAdult: $isAdult) {
                    id
                    title {
                        romaji
                        english
                    }
                    coverImage {
                        medium
                        large
                    }
                    episodes
                    averageScore
                    format
                }
            }
        }';

        $response = Http::post($this->endpoint, [
            'query' => $query,
            'variables' => [
                'ids' => array_values(array_unique(array_map('intval', $ids))),
                'genreNotIn' => ['Hentai', 'Ecchi'],
                'isAdult' => false,
            ],
        ]);

        if ($response->failed()) {
            return [];
        }

        return $response->json()['data']['Page']['media'] ?? [];
    }

    private function mapStatus($status)
    {
        return match($status) {
            'airing' => 'RELEASING',
            default => null,
        };
    }

    private function mapType($type)
    {
        return match($type) {
            'movies' => [
                'format' => 'MOVIE',
                'formatIn' => null
            ],
            'shows' => [
                'format' => null,
                'formatIn' => ['TV', 'TV_SHORT', 'ONA', 'OVA']
            ],
            default => [
                'format' => null,
                'formatIn' => null
            ]
        };
    }

    private function mapSort($sort, $filters = [])
    {
        return match($sort) {
            'popular' => ['POPULARITY_DESC'],
            'recent' => $this->getRecentSort($filters),
            default => ['POPULARITY_DESC']
        };
    }

    private function getRecentSort($filters)
    {
        // If status is airing, show popular airing shows
        if (!empty($filters['status']) && $filters['status'] === 'airing') {
            return ['POPULARITY_DESC', 'START_DATE_DESC'];
        }
        
        // Default "Most recent" - show popular shows that started recently
        return ['POPULARITY_DESC', 'START_DATE_DESC'];
    }
}