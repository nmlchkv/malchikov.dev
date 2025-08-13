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
            Log::info("Check data:", $data);

            $this->urlCheckService->saveCheck($url, $data);
            Log::info("Check successfully saved for URL ID: " . $url);

            flash(__('The page has been checked successfully'))->success();
        } catch (\Exception $exception) {
            Log::error("Error while checking URL: " . $exception->getMessage());
            flash(__('An error occurred during the check: ') . $exception->getMessage())->error();
        }

        return redirect()->route('urls.show', ['url' => $url]);
    }
}
