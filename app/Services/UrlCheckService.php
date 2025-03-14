<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Url;
use App\Models\UrlCheck;

class UrlCheckService
{
    public function checkUrl(int $id): array
    {
        $url = Url::find($id);

        if (!$url) {
            throw new NotFoundHttpException("URL с ID {$id} не найден.");
        }

        try {
            $urlToCheck = $this->ensureValidScheme($url->name);

            $response = Http::get($urlToCheck);
            $document = new Document($response->body());

            return [
                'status_code' => $response->status(),
                'h1' => optional($document->first('h1'))->text(),
                'title' => optional($document->first('title'))->text(),
                'description' => optional($document->first('meta[name=description]'))->getAttribute('content') ?? null,
                'created_at' => Carbon::now(),
            ];
        } catch (RequestException | HttpClientException | ConnectionException $exception) {
            throw new HttpException(500, "Ошибка при запросе к URL: " . $exception->getMessage());
        }
    }

    public function saveCheck(int $id, array $data): void
    {
        if (!Url::find($id)) {
            throw new NotFoundHttpException("URL с ID {$id} не найден.");
        }

        $lastCheck = UrlCheck::where('url_id', $id)
            ->latest('created_at')
            ->first();

        if ($lastCheck) {
            $lastCheck->update($data);
        } else {
            UrlCheck::create(array_merge(['url_id' => $id], $data));
        }
    }

    private function ensureValidScheme(string $url): string
    {
        return Str::startsWith($url, ['http://', 'https://']) ? $url : 'https://' . $url;
    }
}
