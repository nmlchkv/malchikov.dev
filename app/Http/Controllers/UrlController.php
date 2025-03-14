<?php

namespace App\Http\Controllers;

use App\Services\UrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class UrlController extends Controller
{
    protected UrlService $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->urlService->validateUrl($request->all());
            $normalizedUrl = $this->urlService->normalizeUrl($validated['url']['name']);
            $existingUrl = $this->urlService->findUrl($normalizedUrl);

            if ($existingUrl) {
                flash('Страница уже существует')->warning();
                $id = $existingUrl->id;
            } else {
                $id = $this->urlService->createUrl($normalizedUrl);
                flash('Страница успешно добавлена')->success();
            }

            return redirect()->route('urls.show', $id);
        } catch (ValidationException $e) {
            flash('Некорректный URL')->error();
            return response(View::make('laravel'), 422);
        }
    }
    public function show(int $id)
    {
        $url = $this->urlService->getUrlById($id);
        $checks = $this->urlService->getUrlChecks($id);

        if (!$url) {
            abort(404, 'URL не найден');
        }

        return view('show', compact('url', 'checks'));
    }
    public function index()
    {
        $data = $this->urlService->getUrlsWithChecks();
        return view('index', $data);
    }
}
