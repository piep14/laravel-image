RouteRegistrar
=====================

The route registrar is an helper to create routes that serve images with the Laravel Image package. It makes it easy to configure specific filters, restrict or allow filters and set a specific source for the route.

The registrar is bound to an `image` macro on the Laravel Router. You can then simply use it like this, directly fro the router:
```php
// From the facade
Router::image('some/path/{pattern}', [
    'allow_filters' => false,
    'filters' => [
        'width' => 100,
        'height' => 100,
        'crop' => true,
    ],
]);

// From the app helper
app('router')->image('some/path/{pattern}', [
    'allow_filters' => false,
    'filters' => [
        'width' => 100,
        'height' => 100,
        'crop' => true,
    ],
]);

// Or the $router variable in a routes file
$router->image('some/path/{pattern}', [
    'allow_filters' => false,
    'filters' => [
        'width' => 100,
        'height' => 100,
        'crop' => true,
    ],
]);
```

This package publish a routes file dedicated to your images routes (be sure to run `php artisan vendor:publish`). On Laravel 5.3 and up it put the routes file in `routes/images.php` and on Laravel 5.1 and 5.2, the file is at `app/Http/routesImages.php`. The location of this file can be changed in the config `config/image.php` under `routes.map`. If you wish to disable the autoloading of the routes file, you can set `routes.map` to `null`. You could still use `$router->image()` where you want prefer.

Here is the default content of the `routes/images.php` file. You can review all possible configuration options.
```php
$router->image('{pattern}', [
    // The name of the route
    'as' => 'image',

    // A domain that will be used by the route
    'domain' => null,

    // Any middleware you want ot add on the route.
    'middleware' => [],

    // The name of the source to get the image. If it is set to null,
    // it will use the default source.
    'source' => null,

    // Allow to specify a size as filter
    'allow_size' => true,

    // Allow to specify filters in url. You can also set this to
    // an array of specific filters to restrict this route to those
    // filters.
    //
    // Example: ["negative"]
    'allow_filters' => true,

    // Disallow some filters. Can be set to an array of filters.
    'disallow_filters' => false,

    // Any pattern options you want to override.
    'pattern' => [],

    // You can specify filters that will be applied to any image
    // on this route.
    'filters' => [
        // 'width' => 100
    ],

    // Expires header in seconds
    'expires' => 3600 * 24 * 31,

    // Any headers you want to add on the image
    'headers' => [],

    // Cache the file on local machine
    'cache' => true,

    // The path where the images are cached. It is defined to public
    // path, so the cached files would be accessible at the path they were
    // requested and they can be served statically on next requests.
    'cache_path' => public_path()

]);
```

#### Methods

- [`image($path, $config)`](#image)
- [`setPatternName($name)`](#setPatternName)
- [`getPatternName()`](#getPatternName)
- [`setCacheMiddleware($middleware)`](#setCacheMiddleware)
- [`getCacheMiddleware()`](#getCacheMiddleware)
- [`setController($value)`](#setController)
- [`getController()`](#getController)


---

<a name="image" id="image"></a>
### `image($path, $config = array())`

Creates a new image route.

#### Arguments
- `$path` `(string)` The path of the route. It must contain {pattern}. 
- `$config` `(array)` Configuration options for the route. 

#### Return
`(\Illuminate\Routing\Route)` The route created
        

#### Examples

```php
$router = app('router');
$router->image('/thumbnail/{pattern}', [
    'filters' => [
        'width' => 100,
        'height' => 100,
        'crop' => true
    ]
]);
```


---

<a name="setPatternName" id="setPatternName"></a>
### `setPatternName($name)`

Set the name of the router pattern

#### Arguments
- `$name` `(string)` The name of the pattern that will be added to the router 

#### Return
`(\Folklore\Image\RouteRegistrar)`


---

<a name="getPatternName" id="getPatternName"></a>
### `getPatternName()`

Get the name of the router pattern

#### Return
`(string)` The name of the pattern
        


---

<a name="setCacheMiddleware" id="setCacheMiddleware"></a>
### `setCacheMiddleware($middleware)`

Set the middleware that will be used for caching images

#### Arguments
- `$middleware` `(string)` The middleware name or class path 

#### Return
`(\Folklore\Image\RouteRegistrar)`


---

<a name="getCacheMiddleware" id="getCacheMiddleware"></a>
### `getCacheMiddleware()`


---

<a name="setController" id="setController"></a>
### `setController($value)`

#### Arguments
- `$value` 


---

<a name="getController" id="getController"></a>
### `getController()`
