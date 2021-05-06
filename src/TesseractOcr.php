<?php

namespace Captbrogers\TesseractOcr;

use Captbrogers\TesseractOcr\Exceptions\BadWritePermissionsException;
use Captbrogers\TesseractOcr\Exceptions\FailedScanException;
use Captbrogers\TesseractOcr\Exceptions\ImageNotFoundException;
use Captbrogers\TesseractOcr\Exceptions\ProcOpenUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\TesseractOcrNotFoundException;
use Captbrogers\TesseractOcr\Command;
use Captbrogers\TesseractOcr\Process;

class TesseractOcr
{
    protected $sourceImage;

    /**
     * .
     *
     * @return void
     */
    public function __construct(string $imageFile)
    {
        // Wrapping in quotes to avoid special character conflicts.
        $this->sourceImage = "$imageFile";
    }

    /**
     * .
     *
     * @return string
     */
    public function run(): string
    {
        $command = new Command($this->sourceImage);
        $process = new Process($command);

        $output = $process->wait();

        if (! is_callable('file_get_contents')) {
            throw new FileGetContentsUnavailableException;
        }

        $text = '';
        if ($this->command->useFileAsOutput) {
            $text = file_get_contents($this->command->getOutputFile());
        } else {
            $text = $output['out'];
        }

        return trim($text, " \t\n\r\0\x0A\x0B\x0C");
    }
}
