<?php namespace Quasar\Core\Support;

use Illuminate\Support\Str;
use Intervention\Image\Image as InterventionImage;

class Image
{
    /**
     * Return hash for filename
     *
     * @param  string  $extension
     * @return string
     */
    public static function getHashName(string $extension)
    {
        return Str::random(40) . '.' . $extension;
    }

    /**
     * Return if mime type belong to image
     *
     * @param  string  $mime
     * @return bool
     */
    public static function isImageMime(string $mime)
    {
        switch ($mime) 
        {
            case 'image/gif':
            case 'image/jpeg':
            case 'image/pjpeg':
            case 'image/png':
            case 'image/svg+xml':
                return true;
                break;
            default:
                return false;
        }
    }

    /**
     * Check orientation image from mobile device
     */
    public static function checkOrientation(InterventionImage $image)
    {
        $exif = $image->exif();
        $mime = $image->mime();
        
        if ($mime == 'image/jpeg' && ($exif['Orientation'] ?? false))
        {
            if (! empty($exif['Orientation']))
            {
                $rotate = false;
                switch($exif['Orientation'])
                {
                    case 8:
                        $image->rotate(90);
                        $rotate = true;
                        break;
                    case 3:
                        $image->rotate(180);
                        $rotate = true;
                        break;
                    case 6:
                        $image->rotate(-90);
                        $rotate = true;
                        break;
                }

                if ($rotate)
                {
                    $image->save();
                }
            }
        }

        return $image;
    }
}
