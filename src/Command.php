<?php

namespace Captbrogers\TesseractOcr;

use Captbrogers\TesseractOcr\Exceptions\ExecUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\FileExistsUnavailableException;
use Captbrogers\TesseractOcr\Exceptions\TesseractOcrNotFoundException;

class Command
{
    /** @var string */
    public $executable;

    /** @var bool */
    public $useFileAsInput = true;

    /** @var bool */
    public $useFileAsOutput = false;

    /** @var array */
    public array $options = [];

    /** @var string */
    public $configFile;

    /** @var string */
    public $tempDirectory;

    /** @var string */
    public $threadLimit;

    /** @var string */
    public $image;

    /** @var string */
    public $imageSize;

    /** @var string */
    private $outputFile;

    /**
     * Class constructor.
     *
     * @param string|null $image
     * @param string|null $outputFile
     *
     * @return void
     */
    public function __construct($image = null, $outputFile = null)
    {
        $this->executable = config('tesseract-ocr.executable_path', 'tesseract');
        $this->threadLimit = config('tesseract-ocr.thread_limit', '1');

        $this->image = $image;
        $this->outputFile = $outputFile;

        $this->checkBinary();
    }

    /**
     * .
     *
     * @since 1.0.0
     *
     * @throws \Captbrogers
     * @throws \Captbrogers
     * @throws \Captbrogers
     *
     * @return void
     */
    public function checkBinary(): void
    {
        if (! is_callable('file_exists')) {
            throw new FileExistsUnavailableException;
        }

        if (! file_exists($this->executable)) {
            throw new TesseractOcrNotFoundException;
        }

        $version = $this->getTesseractVersion();
        if (strlen($version) < 3) {
            throw new \Exception;
        }
    }

    /**
     * Return the whole command as a string.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function build(): string
    {
        return "$this";
    }

    /**
     * Resolve a temporary directory that will be used.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getTempDir(): string
    {
        return $this->tempDirectory ?: sys_get_temp_dir();
    }

    /**
     * Determine the output file or stream.
     *
     * @since 1.0.0
     *
     * @param bool $withExt
     *
     * @return string
     */
    public function getOutputFile($withExt = true): string
    {
        if (! $this->outputFile) {
            $this->outputFile = $this->getTempDir().DIRECTORY_SEPARATOR.basename(tempnam($this->getTempDir(), 'ocr'));
        }
        if (! $withExt) {
            return $this->outputFile;
        }

        $hasCustomExt = array('hocr', 'tsv', 'pdf');
        $ext = in_array($this->configFile, $hasCustomExt) ? $this->configFile : 'txt';
        return "{$this->outputFile}.{$ext}";
    }

    /**
     * Get the version of the Tesseract binary.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getTesseractVersion(): string
    {
        if (! is_callable('exec')) {
            throw new ExecUnavailableException;
        }

        exec($this->escape($this->executable).' --version 2>&1', $output);
        $outputParts = explode(' ', $output[0]);
        return $outputParts[1];
    }

    /**
     * Call Tesseract binary to get a list of available
     * languages that can be used.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function getAvailableLanguages(): array
    {
        if (! is_callable('exec')) {
            throw new ExecUnavailableException;
        }

        exec($this->escape($this->executable) . ' --list-langs 2>&1', $output);
        array_shift($output);
        sort($output);
        return $output;
    }

    /**
     * Sanitize a string for use with a command invocation.
     *
     * @since 1.0.0
     *
     * @param string $dirtyString
     *
     * @return string
     */
    public function escape(string $dirtyString): string
    {
        return '"' . addcslashes($dirtyString, '$"\\`') . '"';
    }

    /**
     * Turn the command with options into a string.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function __toString(): string
    {
        $fullCommand = [
            $this->escape($this->executable),
        ];
        $fullCommand[] = $this->useFileAsInput ? $this->escape($this->image) : '-';
        $fullCommand[] = $this->useFileAsOutput ? $this->escape($this->getOutputFile(false)) : '-';

        foreach ($this->options as $option) {
            if (is_callable($option)) {
                $fullCommand[] = "$option";
            }
        }

        return implode(' ', $fullCommand);
    }
}
