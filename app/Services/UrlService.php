<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UrlService
{
    private const URL_SCHEMES = ['http://', 'https://'];
    private const DEFAULT_SCHEME = 'https://';

    private const PAGINATION_COUNT = 15;
    private const URL_VALIDATION_RULES = [
        'url.name' => 'required|url|max:255'
    ];


    public function validateUrl(array $data): array
    {
        $data['url']['name'] = $this->ensureScheme($data['url']['name']);

        $validator = Validator::make($data, self::URL_VALIDATION_RULES);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function normalizeUrl(string $url): string
    {
        return rtrim(mb_strtolower(trim($this->ensureScheme($url))), '/');
    }

    public function findUrl(string $normalizedUrl)
    {
        return DB::table('urls')
            ->where('name', $normalizedUrl)
            ->first();
    }

    public function createUrl(string $normalizedUrl): int
    {
        return DB::table('urls')->insertGetId([
            'name' => $normalizedUrl,
            'created_at' => now()
        ]);
    }
    public function getUrlById(int $id)
    {
        return DB::table('urls')->find($id);
    }
    public function getUrlChecks(int $id)
    {
        return DB::table('url_checks')
            ->where('url_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(self::PAGINATION_COUNT);
    }
    public function getUrlsWithChecks()
    {
        $urls = DB::table('urls')
            ->orderBy('id')
            ->paginate(15);

        $urlIds = collect($urls->items())->pluck('id');

        $checks = DB::table('url_checks')
            ->select(['url_id', DB::raw('MAX(created_at) as check_date'), 'status_code'])
            ->whereIn('url_id', $urlIds)
            ->groupBy('url_id', 'status_code')
            ->get()
            ->keyBy('url_id');

        return compact('urls', 'checks');
    }
    public function ensureScheme(string $url): string
    {
        return Str::startsWith($url, self::URL_SCHEMES) ? $url : self::DEFAULT_SCHEME . $url;
    }
}
