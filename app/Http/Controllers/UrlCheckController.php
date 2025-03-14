<?php

namespace App\Http\Controllers;

use App\Services\UrlCheckService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UrlCheckController extends Controller
{
    protected UrlCheckService $urlCheckService;

    public function __construct(UrlCheckService $urlCheckService)
    {
        $this->urlCheckService = $urlCheckService;
    }

    public function store(Request $request, int $url)
    {
        try {
            $data = $this->urlCheckService->checkUrl($url);
            Log::info("Данные проверки:", $data);

            $this->urlCheckService->saveCheck($url, $data);
            Log::info("Проверка успешно сохранена для URL ID: " . $url);

            flash(__('Страница успешно проверена'))->success();
        } catch (\Exception $exception) {
            Log::error("Ошибка при проверке URL: " . $exception->getMessage());
            flash(__('Произошла ошибка при проверке: ') . $exception->getMessage())->error();
        }

        return redirect()->route('urls.show', ['url' => $url]);
    }
}
