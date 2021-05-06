<?php

namespace Captbrogers\TesseractOcr;

class Command
{
    public $executable;
    public $useFileAsInput = true;
    public $useFileAsOutput = true;
    public array $options = [];
    public $configFile;
    public $tempDirectory;
    public $threadLimit;
    public $image;
    public $imageSize;
    private $outputFile;

    public function __construct($image = null, $outputFile = null)
    {
        $this->image = $image;
        $this->outputFile = $outputFile;

        $this->executable = config('tesseract-ocr.executable_path', 'tesseract');
        $this->threadLimit = config('tesseract-ocr.thread_limit', '1');
    }

    public function build()
    {
        return "$this";
    }

    public function getTempDir()
    {
        return $this->tempDirectory ?: sys_get_temp_dir();
    }

    public function getOutputFile($withExt = true)
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

    public function getTesseractVersion()
    {
        exec($this->escape($this->executable).' --version 2>&1', $output);
        $outputParts = explode(' ', $output[0]);
        return $outputParts[1];
    }

    public function getAvailableLanguages()
    {
        exec($this->escape($this->executable) . ' --list-langs 2>&1', $output);
        array_shift($output);
        sort($output);
        return $output;
    }

    /**
     * .
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
     * .
     *
     * @return string
     */
    public function __toString(): string
    {
        $fullCommand = [
            "OMP_THREAD_LIMIT={$this->threadLimit}",
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
