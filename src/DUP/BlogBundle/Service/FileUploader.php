<?php

namespace DUP\BlogBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {

    private $targetDir;

    function __construct($targetDir) {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file) {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move(
                $this->targetDir, $fileName
        );

        return $fileName;
    }

}
