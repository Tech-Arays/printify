<?php

namespace App\Models;

use Imagine\Image\Metadata\ExifMetadataReader;
use Imagine\Filter\Basic\Autorotate;

class FileAttachment extends File
{
    public function url($size = null)
    {
        if ($size) {
            $ext = pathinfo($this->path(), PATHINFO_EXTENSION);

            $thumb = null;
            if ($ext == 'psd') {
                $thumb = 'img/files/psd.png';
            }
            else if ($ext == 'ai') {
                $thumb = 'img/files/ai.png';
            }
            else if ($ext == 'eps') {
                $thumb = 'img/files/eps.png';
            }
            else {
                $thumb = parent::url($size);
            }
        }
        else {
            $thumb = parent::url($size);
        }

        return $thumb;
    }
}
