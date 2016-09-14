<?php
namespace alexBond\thumbler;

use Symfony\Component\Filesystem\Filesystem;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Class Thumbler
 *
 * This extension allows to resize images and automatically cache them.
 *
 * @property \Zebra_Image $zebraInstance
 */
class Thumbler extends Component
{
    const METHOD_BOXED = 0;
    const METHOD_NOT_BOXED = 1;
    const METHOD_CROP_TOP_LEFT = 2;
    const METHOD_CROP_TOP_CENTER = 3;
    const METHOD_CROP_TOP_RIGHT = 4;
    const METHOD_CROP_MIDDLE_LEFT = 5;
    const METHOD_CROP_CENTER = 6;
    const METHOD_CROP_MIDDLE_RIGHT = 7;
    const METHOD_CROP_BOTTOM_LEFT = 8;
    const METHOD_CROP_BOTTOM_CENTER = 9;
    const METHOD_CROP_BOTTOM_RIGHT = 10;

    /**
     * @var \Zebra_Image
     */
    private $_zebraInstance = null;
    /**
     * @var string Path to sources of images
     */
    public $sourcePath;
    /**
     * @var string Path to thumbs of images
     */
    public $thumbsPath;
    /**
     * @var string Url to thumbs of images
     */
    public $thumbsUrl;

    /**
     * @return \Zebra_Image
     */
    public function getZebraInstance()
    {
        if ($this->_zebraInstance === null) {
            $this->_zebraInstance = new \Zebra_Image();
        }
        return $this->_zebraInstance;
    }

    /**
     * @param string $image Path to images in source folder
     * @param int $width
     * @param int $height
     * @param int $method
     * @param string $backgroundColor Background for METHOD_BOXED
     * @param bool $callExceptionOnError
     * @return bool|string
     */
    public function resize(
        $image,
        $width,
        $height,
        $method = self::METHOD_NOT_BOXED,
        $backgroundColor = 'FFFFFF',
        $callExceptionOnError = true
    ) {
        $this->checkConfig();
        if (!file_exists(\Yii::getAlias($this->thumbsPath) . DIRECTORY_SEPARATOR . $method . '_' . $width . 'x' . $height . '_' . $backgroundColor . DIRECTORY_SEPARATOR . $image)) {
            $this->zebraInstance->source_path = \Yii::getAlias($this->sourcePath) . DIRECTORY_SEPARATOR . $image;
            $this->zebraInstance->target_path = \Yii::getAlias($this->thumbsPath) . DIRECTORY_SEPARATOR . $method . '_' . $width . 'x' . $height . '_' . $backgroundColor . DIRECTORY_SEPARATOR . $image;

            $targetInfo = pathinfo(\Yii::getAlias($this->thumbsPath) . DIRECTORY_SEPARATOR . $method . '_' . $width . 'x' . $height . '_' . $backgroundColor . DIRECTORY_SEPARATOR . $image);
            if (!is_dir($targetInfo['dirname'])) {
                mkdir($targetInfo['dirname'], 0777, true);
            }

            if (!$this->zebraInstance->resize($width, $height, $method, "#" . $backgroundColor)) {
                if ($callExceptionOnError) {
                    $this->callException($this->zebraInstance->error);
                } else {
                    return false;
                }
            }
        }
        return $this->thumbsUrl . $method . '_' . $width . 'x' . $height . '_' . $backgroundColor . DIRECTORY_SEPARATOR . $image;
    }

    public function clearImageCache($image)
    {
        $this->checkConfig();

        $fs = new Filesystem();
        foreach (scandir(\Yii::getAlias($this->thumbsPath)) as $item) {
            if ($item == "." || $item == "..") {
                continue;
            }
            $fs->remove(\Yii::getAlias($this->thumbsPath) . DIRECTORY_SEPARATOR . $item . DIRECTORY_SEPARATOR . $image);
        }
    }

    public function clearAllCache()
    {
        $fs = new Filesystem();
        foreach (scandir(\Yii::getAlias($this->thumbsPath)) as $item) {
            if ($item == "." || $item == "..") {
                continue;
            }
            $fs->remove(\Yii::getAlias($this->thumbsPath) . DIRECTORY_SEPARATOR . $item);
        }
    }

    /**
     * @return bool|string Last error string or false when no errors detected.
     */
    public function getLastError()
    {
        if ($this->zebraInstance->error == 0) {
            return false;
        }
        switch ($this->zebraInstance->error) {
            case 1:
                return 'Source file could not be found!';
            case 2:
                return 'Source file is not readable!';
            case 3:
                return 'Could not write target file!';
            case 4:
                return 'Unsupported source file format!';
            case 5:
                return 'Unsupported target file format!';
            case 6:
                return 'GD library version does not support target file format!';
            case 7:
                return 'GD library is not installed!';
            default:
                return 'Unknown error';
        }
    }

    private function callException($error)
    {
        switch ($error) {
            case 1:
                throw new Exception('Source file could not be found!');
                break;
            case 2:
                throw new Exception('Source file is not readable!');
                break;
            case 3:
                throw new Exception('Could not write target file!');
                break;
            case 4:
                throw new Exception('Unsupported source file format!');
                break;
            case 5:
                throw new Exception('Unsupported target file format!');
                break;
            case 6:
                throw new Exception('GD library version does not support target file format!');
                break;
            case 7:
                throw new Exception('GD library is not installed!');
                break;
        }
    }

    public function checkConfig()
    {
        if (empty($this->sourcePath)) {
            throw new InvalidConfigException("Source path are empty");
        }
        if (!is_dir(\Yii::getAlias($this->sourcePath))) {
            throw new Exception("Source path not found");
        }
        if (empty($this->thumbsPath)) {
            throw new InvalidConfigException("Thumbs path are empty");
        }
        if (!is_dir(\Yii::getAlias($this->thumbsPath))) {
            throw new Exception("Thumbs path not found");
        }
        if (!is_dir(\Yii::getAlias($this->thumbsUrl))) {
            throw new Exception("Thumbs url not found");
        }
    }
}
