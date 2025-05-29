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
        if (empty($data['url']['name'])) {
            throw ValidationException::withMessages([
                'url.name' => 'The URL field is required.'
            ]);
        }
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

    public function findUrl(string $url)
    {
        $canonical = $this->canonicalUrl($url);
        return DB::table('urls')->where('name', $canonical)->first();
    }

    public function createUrl(string $url, $userId = null): int
    {
        $canonical = $this->canonicalUrl($url);
        return DB::table('urls')->insertGetId([
            'name' => $canonical,
            'user_id' => $userId,
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
    public function getUrlsWithChecks($userId)
    {
        $urls = DB::table('urls')
            ->where('user_id', $userId)
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
        foreach (self::URL_SCHEMES as $scheme) {
            if (Str::startsWith(mb_strtolower($url), $scheme)) {
                return $url;
            }
        }
        return self::DEFAULT_SCHEME . $url;
    }
    public function canonicalUrl(string $url): string
    {
        $url = trim(mb_strtolower($url));
        $url = preg_replace('/^https?:\/\//', '', $url);
        $url = rtrim($url, '/');
        $url = preg_replace('/^www\./', '', $url);
        $url = 'www.' . $url;
        return $url;
    }
    public function findOrCreateUrl(string $url, $userId, &$wasCreated = null): int
    {
        $canonical = $this->canonicalUrl($url);

        if (is_null($userId)) {
            $existingUrl = DB::table('urls')
                ->where('name', $canonical)
                ->whereNull('user_id')
                ->first();
        } else {
            $existingUrl = DB::table('urls')
                ->where('name', $canonical)
                ->where('user_id', $userId)
                ->first();
        }

        if ($existingUrl) {
            $wasCreated = false;
            return $existingUrl->id;
        } else {
            $wasCreated = true;
            return $this->createUrl($canonical, $userId);
        }
    }
}
