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
            $userId = auth()->id();

            $id = $this->urlService->findOrCreateUrl($normalizedUrl, $userId, $wasCreated);

            if ($wasCreated) {
                flash('Add')->success();
            } else {
                flash('Exist')->warning();
            }

            return redirect()->route('urls.show', $id);
        } catch (ValidationException $e) {
            flash('Not correct')->error();
            return response(View::make('laravel'), 422);
        }
    }
    public function index()
    {
        if (!auth()->check()) {
            abort(403, 'Access is allowed only for authorized users.');
        }
        $userId = auth()->id();
        $data = $this->urlService->getUrlsWithChecks($userId);
        return view('index', $data);
    }
    public function show(int $id)
    {
        $url = $this->urlService->getUrlById($id);
        $checks = $this->urlService->getUrlChecks($id);

        if (!$url) {
            abort(404, 'URL not found');
        }

        return view('show', compact('url', 'checks'));
    }
}
