<?php

namespace Captbrogers\TesseractOcr;

use Captbrogers\TesseractOcr\Exceptions\ProcOpenUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\StreamSetBlockingUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\FwriteUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\FreadUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\ProcGetStatusUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\ProcCloseUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\FcloseUnavailableException;

class Process
{
    private $processHandler;

    private $standardIn;

    private $standardOut;

    private $standardError;

    public function __construct($command)
    {
        if (! is_callable('proc_open')) {
            throw new ProcOpenUnavailableException;
        }
        if (! is_callable('stream_set_blocking')) {
            throw new StreamSetBlockingUnavailableException;
        }

        $streamDescriptors = [
            ['pipe', 'r'], // stdin
            ['pipe', 'w'], // stdout
            ['pipe', 'w'], // stderr
        ];

        $currentDirectory = null;

        $environmentVariables = [
            'OMP_THREAD_LIMIT' => config('tesseract-ocr.thread_limit'),
        ];

        $this->processHandler = proc_open($command, $streamDescriptors, $outputPipes, $currentDirectory, $environmentVariables);
        list($this->standardIn, $this->standardOut, $this->standardError) = $outputPipes;
    }

    public function write($data, $length)
    {
        if (! is_callable('fwrite')) {
            throw new FwriteUnavailableException;
        }

        $total = 0;
        do {
            $resource = fwrite($this->standardIn, substr($data, $total));
        } while ($resource && $total += $resource < $length);

        return $total === $length;
    }

    public function wait()
    {
        if (! is_callable('fread')) {
            throw new FreadUnavailableException;
        }
        if (! is_callable('proc_get_status')) {
            throw new ProcGetStatusUnavailableException;
        }

        $running = true;
        $data = [
            'out' => '',
            'err' => '',
        ];

        while ($running) {
            $data['out'] .= fread($this->standardOut, 8192);
            $data['err'] .= fread($this->standardError, 8192);
            $proccessInfo = proc_get_status($this->processHandler);
            $running = $proccessInfo['running'];
        }

        return $data;
    }

    public function close()
    {
        if (! is_callable('proc_close')) {
            throw new ProcCloseUnavailableException;
        }

        $this->closeStream($this->standardIn);
        $this->closeStream($this->standardOut);
        $this->closeStream($this->standardError);
        return proc_close($this->processHandler);
    }

    public function closeStandardIn()
    {
        $this->closeStream($this->standardIn);
    }

    private function closeStream(&$stream)
    {
        if (! is_callable('fclose')) {
            throw new FcloseUnavailableException;
        }

        if (! is_null($stream)) {
            fclose($stream);
            $stream = null;
        }
    }
}
