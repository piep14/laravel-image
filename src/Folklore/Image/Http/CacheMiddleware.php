<?php

namespace Folklore\Image\Http;

use Folklore\Image\Http\ImageResponse;
use Closure;

class CacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = $request->path();
        $route = $request->route();
        $routeAction = $route ? $route->getAction():[];
        $cachePath = array_get($routeAction, 'image.cache_path', public_path());
        $cacheFilePath = rtrim($cachePath, '/').'/'.ltrim($path, '/');
        $cacheDirectory = dirname($cacheFilePath);

        // If the cache file exists, serve this file.
        if (file_exists($cacheFilePath)) {
            return response()
                ->image()
                ->setImagePath($cacheFilePath);
        }

        // Get the response
        $response = $next($request);

        // Check if cache directory is writable and create the directory if
        // it doesn't exists.
        $directoryExists = file_exists($cacheDirectory);
        if ($directoryExists && !is_writable($cacheDirectory)) {
            throw new \Exception('Destination is not writeable');
        }
        if (!$directoryExists) {
            app('files')->makeDirectory($cacheDirectory, 0755, true, true);
        }

        // If it's an ImageResponse, save the image from the Image object.
        // Otherwise, get the response content and save it.
        if ($response instanceof ImageResponse) {
            $image = $response->getImage();
            $image->save($cacheFilePath);
            $response->setImagePath($cacheFilePath);
        } else {
            $content = $response->getContent();
            app('files')->put($cacheFilePath, $content);
        }

        return $response;
    }
}