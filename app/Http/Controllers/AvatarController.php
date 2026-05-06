<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AvatarController extends Controller
{
    /**
     * Stream a stored avatar to the browser.
     *
     * Reads the file directly from the storage disk so it works across
     * environments where the public/storage symlink/junction is not
     * served correctly (e.g. PHP built-in dev server on Windows).
     */
    public function show(string $filename): BinaryFileResponse|Response
    {
        $path = 'avatars/'.$filename;

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
