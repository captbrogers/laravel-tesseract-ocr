<?php

namespace Captbrogers\TesseractOcr;

use Captbrogers\TesseractOcr\Exceptions\FcloseUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\FreadUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\FwriteUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\ProcCloseUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\ProcGetStatusUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\ProcOpenUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\ShellExecUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\StreamSetBlockingUnavailableException;

class Process
{
    /** @var resource */
    private $processHandler;

    /** @var resource */
    private $standardIn;

    /** @var resource */
    private $standardOut;

    /** @var resource */
    private $standardError;

    /**
     * Write data to standard in.
     *
     * @since 1.0.0
     *
     * @throws \Captbrogers\TesseractOcr\Exceptions\ProcOpenUnavailableException
     * @throws \Captbrogers\TesseractOcr\Exceptions\StreamSetBlockingUnavailableException
     * @throws \Captbrogers\TesseractOcr\Exceptions\ShellExecUnavailableException
     *
     * @param string $command
     *
     * @return void
     */
    public function __construct(string $command)
    {
        if (! is_callable('proc_open')) {
            throw new ProcOpenUnavailableException;
        }
        if (! is_callable('stream_set_blocking')) {
            throw new StreamSetBlockingUnavailableException;
        }
        if (! is_callable('shell_exec')) {
            throw new ShellExecUnavailableException;
        }

        $streamDescriptors = [
            ['pipe', 'r'], // stdin
            ['pipe', 'w'], // stdout
            ['pipe', 'w'], // stderr
        ];

        $currentDirectory = null;

        // if the thread limit is "auto", then get half the core count
        // unless the core count is 1. Should be obvious what to do
        // in that case.
        $coreCount = shell_exec('nproc');
        $threadLimit = config('tesseract-ocr.thread_limit');
        if ($threadLimit === 'auto') {
            if ($coreCount < 2) {
                $threadLimit = '1';
            } else {
                $threadLimit = (string) $coreCount / 2;
            }
        }

        $environmentVariables = [
            'OMP_THREAD_LIMIT' => $threadLimit,
        ];
dd([
    'command' => $command,
    'current_directory' => $currentDirectory,
    'environment' => $environmentVariables,
]);
        $this->processHandler = proc_open($command, $streamDescriptors, $outputPipes, $currentDirectory, $environmentVariables);
        list($this->standardIn, $this->standardOut, $this->standardError) = $outputPipes;
    }

    /**
     * Write data to standard in.
     *
     * @since 1.0.0
     *
     * @throws \Captbrogers\TesseractOcr\Exceptions\FwriteUnavailableException
     *
     * @param ??? $data
     * @param ??? $length
     *
     * @return bool
     */
    public function write($data, $length): bool
    {
dd(['data' => gettype($data), 'length' => gettype($length)]);
        if (! is_callable('fwrite')) {
            throw new FwriteUnavailableException;
        }

        $total = 0;
        do {
            $resource = fwrite($this->standardIn, substr($data, $total));
        } while ($resource && $total += $resource < $length);

        return $total === $length;
    }

    /**
     * Go into a holding pattern and read the process stream
     * until it is complete.
     *
     * @since 1.0.0
     *
     * @throws \Captbrogers\TesseractOcr\Exceptions\FreadUnavailableException
     * @throws \Captbrogers\TesseractOcr\Exceptions\ProcGetStatusUnavailableException
     *
     * @return array
     */
    public function wait(): array
    {
        if (! is_callable('fread')) {
            throw new FreadUnavailableException;
        }
        if (! is_callable('proc_get_status')) {
            throw new ProcGetStatusUnavailableException;
        }

        $running = true;
        $data = ['out' => '', 'err' => ''];

        while ($running) {
            $data['out'] .= fread($this->standardOut, 8192);
            $data['err'] .= fread($this->standardError, 8192);
            $proccessInfo = proc_get_status($this->processHandler);
            $running = $proccessInfo['running'];
        }

        return $data;
    }

    /**
     * Close all process streams.
     *
     * @since 1.0.0
     *
     * @throws \Captbrogers\TesseractOcr\Exceptions\ProcCloseUnavailableException
     *
     * @return int
     */
    public function close(): int
    {
        if (! is_callable('proc_close')) {
            throw new ProcCloseUnavailableException;
        }

        $this->closeStream($this->standardIn);
        $this->closeStream($this->standardOut);
        $this->closeStream($this->standardError);

        return proc_close($this->processHandler);
    }

    /**
     * Close standard in specifically.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function closeStandardIn(): void
    {
        $this->closeStream($this->standardIn);
    }

    /**
     * Close a given stream.
     *
     * @since 1.0.0
     *
     * @throws \Captbrogers\TesseractOcr\Exceptions\FcloseUnavailableException
     *
     * @param resource &$stream  Closes a given resource stream.
     *
     * @return void
     */
    private function closeStream(resource &$stream): void
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
