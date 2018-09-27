<?php

namespace App\Vendor\Codesleeve\Stapler;

use Codesleeve\Stapler\Attachment as BaseAttachment;
use Codesleeve\Stapler\File\UploadedFile;
use Codesleeve\Stapler\Factories\File as FileFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class Attachment extends BaseAttachment
{
    /**
     * Handle dynamic method calls on the attachment.
     * This allows us to call methods on the underlying
     * storage driver directly via the attachment.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $callable = ['remove', 'move'];

        if (in_array($method, $callable)) {

            // we need to override umask to make 0777 permissions work
            // this will solve the issue with different permissions for web user and cli user
            if ($method == 'move') {
                $oldUmask = umask(0);
            }

            $result = call_user_func_array([$this->storageDriver, $method], $parameters);


            if ($method == 'move') {
                umask($oldUmask);
            }

            return $result;
        }
    }

    /**
     * Process the queuedForWrite que.
     */
    protected function flushWrites()
    {
        foreach ($this->queuedForWrite as $style) {
            $ext = pathinfo($this->uploadedFile->getFilename(), PATHINFO_EXTENSION);
            if (
                $style->dimensions
                && (
                    $this->uploadedFile->isImage()
                    || in_array($ext, ['psd', 'ai', 'eps', 'ar3'])
                )
            ) {
                if ($ext == 'psd') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/psd.png'), 'psd.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($ext == 'ai') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/ai.png'), 'ai.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($ext == 'eps') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/eps.png'), 'eps.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($ext == 'ar3') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/ar3.png'), 'ar3.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($this->uploadedFile->isImage()) {
                    $file = $this->resizer->resize($this->uploadedFile, $style);
                }
            }
            else {
                $file = $this->uploadedFile->getRealPath();
            }

            $filePath = $this->path($style->name);
            $this->move($file, $filePath);
        }

        $this->queuedForWrite = [];
    }

    /**
     * Rebuilds the images for this attachment.
     */
    public function reprocess()
    {
        $fileLocation = $this->storage == 'filesystem' ? $this->path('original') : $this->url('original');

        if (!$this->originalFilename() || !file_exists($fileLocation)) {
            return;
        }

        foreach ($this->styles as $style) {
            $file = FileFactory::create($fileLocation);

            $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            if (
                $style->dimensions
                && (
                    $file->isImage()
                    || in_array($ext, ['psd', 'ai', 'eps', 'ar3'])
                )
            ) {
                if ($ext == 'psd') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/psd.png'), 'psd.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($ext == 'ai') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/ai.png'), 'ai.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($ext == 'eps') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/eps.png'), 'eps.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($ext == 'ar3') {
                    $uploadedFile = new UploadedFile(
                        new SymfonyUploadedFile(public_path('img/files/ar3.png'), 'ar3.png')
                    );
                    $file = $this->resizer->resize($uploadedFile, $style);
                }
                else if ($file->isImage()) {
                    $file = $this->resizer->resize($file, $style);
                }
            }
            else {
                $file = $file->getRealPath();
            }

            $filePath = $this->path($style->name);
            $this->move($file, $filePath);
        }
    }
}
