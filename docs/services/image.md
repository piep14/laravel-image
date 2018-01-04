Image Manager
================================================
The Image Manager is the main entry point to interact with image.

You can access it with the facade:
```php
Image::method();
```

With the app helper:
```php
app('image')->method();
```

Or with the image helper:
```php
image()->method();

// Passing arguments is an alias to image()->make()
$image = image('path/to/image.jpg', 300, 300);
```

For this documentation, we will be using the facade, but any call can be changed to `app('image')`

#### Methods

- [`url($path, $width, $height, $filters)`](#url)
- [`make($path, $filters)`](#make)
- [`open($path)`](#open)
- [`save($image, $path)`](#save)
- [`source($source)`](#source)
- [`pattern($config)`](#pattern)
- [`parse($url, $config)`](#parse)
- [`routes($config)`](#routes)

---

## <a name="url" id="url"></a>`url($path, $width = null, $height = null, $filters = [])`
Generates an url containing the filters, according to the url format in the config (more info can be found in the [Url Generator](url-generator.md) documentation)

##### Arguments
- `(string)` `$path` The path of the image.
- `(int | array)` `$width` The width of the image. It can be null, or can also be an array of filters.
- `(int)` `$height` The height of the image.
- `(array)` `$filters` An array of filters

##### Return
`(string)` The generated url

##### Examples

```php
echo Image::url('path/to/image.jpg', 300, 300);
// '/path/to/image-filters(300x300).jpg'
```

You can also omit the size parameters and pass a filters array as the second argument
```php
echo Image::url('path/to/image.jpg', [
    'width' => 300,
    'height' => 300,
    'rotate' => 180
]);
// '/path/to/image-filters(300x300-rotate(180)).jpg'
```

There is also an `image_url()` helper available
```php
echo image_url('path/to/image.jpg', 300, 300);
```

You can change the format of the url by changing the configuration in the `config/image.php` file or by passing the same options in the filters array. (see [Url Generator](url-generator.md) for available options)

---

## <a name="make" id="make"></a>`make($path, $filters = [])`
Make an Image object from a path and apply the filters.

##### Arguments
- `(string)` `$path` The path of the image.
- `(array)` `$filters` An array of filters

##### Return
`(Imagine\Image\ImageInterface)` The image object

##### Examples

Create an Image object with the image resized (and cropped) to 300x300 and rotated 180 degrees.
```php
$image = Image::make('path/to/image.jpg', [
    'width' => 300,
    'height' => 300,
    'crop' => true,
    'rotate' => 180
]);
```

There is also an `image()` helper available
```php
$image = image('path/to/image.jpg', [
    'width' => 300,
    'height' => 300,
    'crop' => true
]);
```

---

## <a name="open" id="open"></a>`open($path)`
Open an image from a path, without applying any filters. The image is opened according to the default source specified in the `config/image.php` file.

##### Arguments
- `(string)` `$path` The path of the image.

##### Return
`(Imagine\Image\ImageInterface)` The image object

##### Examples

Open the image path and return an Image object
```php
$image = Image::open('path/to/image.jpg');
```

---

## <a name="save" id="save"></a>`save($image, $path)`
Save an Image object at a given path on the default source.

##### Arguments
- `(Imagine\Image\ImageInterface)` `$image` The image object to be saved
- `(string)` `$path` The path to save the image

##### Return
`(string)` The path of the saved image

##### Examples

Create a resized image and save it to a new path
```php
$image = Image::make('path/to/image.jpg', [
    'width' => 300,
    'height' => 300,
    'crop' => true
]);
Image::save($image, 'path/to/image-resized.jpg');
```

Or save it to a different source:
```php
$image = Image::make('path/to/image.jpg', [
    'width' => 300,
    'height' => 300,
    'crop' => true
]);
Image::source('cloud')->save($image, 'path/to/image-resized.jpg');
```

---

## <a name="source" id="source"></a>`source($source)`
Get an Image manipulator for a specific source. (more info can be found in the [Sources](../sources.md) documentation)

##### Arguments
- `(string)` `$source` The source name

##### Return
`(Folklore\Image\ImageManipulator)` The image manipulator object, bound the to specified source

---

## <a name="filter" id="filter"></a>`filter($name, $filter)`
Add a filter to the manager that can be used by the `Image::url()` and `Image::make` method.

##### Arguments
- `(string)` `$name` The name of the filter
- `(array|closure|string)` `$filter` The filter can be an array of filters, a closure that will get the Image object or a class path to a Filter class. (more info can be found in the [Filters](../filters.md) documentation)

##### Examples

From an array
```php
// Declare the filter in a Service Provider
Image::filter('small', [
    'width' => 100,
    'height' => 100,
    'crop' => true,
]);

// Use it when making an image
$image = Image::make('path/to/image.jpg', [
    'small' => true,
]);

// or

$image = Image::make('path/to/image.jpg', 'small');
```

With a closure
```php
// Declare the filter in a Service Provider
Image::filter('circle', function ($image, $color)
{
    // See Imagine documentation for the Image object (https://imagine.readthedocs.io/en/latest/index.html)
    $image->draw()
        ->ellipse(new Point(0, 0), new Box(300, 225), $image->palette()->color($color));
    return $image;
});

// Use it when making an image
$image = Image::make('path/to/image.jpg', [
    'circle' => '#FFCC00',
]);
```

With a class path
```php
// Declare the filter in a Service Provider
Image::filter('custom', \App\Filters\CustomFilter::class);

// Use it when making an image
$image = Image::make('path/to/image.jpg', [
    'custom' => true,
]);
```

---

## <a name="parse" id="parse"></a>`parse($url, $config)`


---

## <a name="pattern" id="pattern"></a>`pattern($config)`


---

## <a name="routes" id="routes"></a>`routes($config)`

Add the routes from the file specified in the `config/image.php` file at `routes.map`. You can pass a config array to override values from the config or you can also pass a path to a routes file. This method is automatically called if you have a path in your `config/image.php`. To disable this you can set `routes.map` to null.

##### Arguments
- `(array|string)` `$config` A config array that will override values from the `config/image.php`. If you pass a string, it is considered as a path to a filtes containing routes.


##### Examples

Map the routes on the Laravel Router
```php
Image::routes();

// or with the helper
image()->routes();
```

Map a custom routes file
```php
Image::routes(base_path('routes/my-custom-file.php'));

// or an equivalent
Image::routes([
    'map' => base_path('routes/my-custom-file.php')
]);
```