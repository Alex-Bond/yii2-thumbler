Image Resizer with automatic cache for Yii 2
============================================

This extension allows to resize images and automatically cache them.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist Alex-Bond/yii2-thumbler
```

or add

```json
"Alex-Bond/yii2-thumbler": "*"
```

to the require section of your composer.json.

Usage
-----

To use this extension, you need add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'thumbler'=> [
            'class' => 'alexBond\thumbler',
            'sourcePath' => '/path/to/source/files',
            'thumbsPath' => '/path/to/resize/cache',
        ],
    ],
];
```

After this just call `resize()` method like this:

```php
$path = \Yii::$app->thumbler->resize('image.png',500,500);
```

As result of this call extension will return path to resized image relative to `$thumbsPath`.

Methods
-------

***resize($image, $width, $height, $method = Thumbler::METHOD_NOT_BOXED, $backgroundColor = 'ffffff', $callExceptionOnError = true)***

<table>
  <tr>
    <th>Parameter</th><th>Description</th><th>Possible Values</th>
  </tr>
  <tr>
    <td>$image</td><td>Path to image based relative to $sourcePath</td><td>String</td>
  </tr>
  <tr>
    <td>$width</td><td>Width of needed image in pixels</td><td>int</td>
  </tr>
  <tr>
    <td>$height</td><td>Height of needed image in pixels</td><td>int</td>
  </tr>
  <tr>
    <td>$method</td><td>Resize algorithm</td>
    <td>
    Thumbler::METHOD_BOXED;   
    Thumbler::METHOD_NOT_BOXED;    
    Thumbler::METHOD_CROP_TOP_LEFT;    
    Thumbler::METHOD_CROP_TOP_CENTER;    
    Thumbler::METHOD_CROP_TOP_RIGHT;    
    Thumbler::METHOD_CROP_MIDDLE_LEFT;    
    Thumbler::METHOD_CROP_CENTER;    
    Thumbler::METHOD_CROP_MIDDLE_RIGHT;    
    Thumbler::METHOD_CROP_BOTTOM_LEFT;    
    Thumbler::METHOD_CROP_BOTTOM_CENTER;    
    Thumbler::METHOD_CROP_BOTTOM_RIGHT;
    </td>
  </tr>
  <tr>
    <td>$backgroundColor</td><td>Background color for `Thumbler::METHOD_BOXED` algorithm</td><td>String (HEX color)</td>
  </tr>
  <tr>
    <td>$callExceptionOnError</td><td>When `true` extension will caught exception on error. If `false` extension will just add error in internal array which can be called by `getLastError()`</td><td>boolean</td>
  </tr>
</table>

***getLastError()***

Returns last error description.

***clearImageCache($image)***

Clears all cache for selected image file.

***clearAllCache()***

Clears all cache.