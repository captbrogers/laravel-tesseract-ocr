<?php

namespace Captbrogers\TesseractOcr;

use Captbrogers\TesseractOcr\Command;
use Captbrogers\TesseractOcr\Exceptions\FileGetContentsUnavailableException;
use Captbrogers\TesseractOcr\Process;

class TesseractOcr
{
    /** @var string */
    public $sourceImage;

    /** @var \Captbrogers\TesseractOcr\Command */
    public $command;

    /**
     * Class constructor.
     *
     * @since 1.0.0
     *
     * @param string $imageFile
     * @param \Captbrogers\TesseractOcr\Command ?$command
     *
     * @return void
     */
    public function __construct(string $imageFile, $command = null)
    {
        // Wrapping in quotes to avoid special character conflicts.
        $this->sourceImage = "$imageFile";
        //$this->command = $command ?: new Command($this->sourceImage);
    }

    /**
     * Execute the Tesseract command and return the output.
     *
     * @since 1.0.0
     *
     * @throws \Captbrogers\TesseractOcr\Exceptions\FileGetContentsUnavailableException
     *
     * @return string
     */
    public function run(): string
    {
        if (! is_callable('file_get_contents')) {
            throw new FileGetContentsUnavailableException;
        }

        $this->command = new Command($this->sourceImage);
dd($this->command->build());
        $process = new Process($this->command);

        if (! $this->command->useFileAsInput) {
            $process->write($this->command->image, $this->command->imageSize);
            $process->closeStdin();
        }

        $output = $process->wait();
//dd($this->command);
dd($output);
        $text = '';
        if ($this->command->useFileAsOutput) {
            $text = file_get_contents($this->command->getOutputFile());
        } else {
            $text = $output['out'];
        }
dd($text);
        return trim($text, " \t\n\r\0\x0A\x0B\x0C");
    }

    /**
     * Set the image data.
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    public function imageData($image, $size): TesseractOcr
    {
        FriendlyErrors::checkTesseractVersion('4.0.0', 'Reading image data from stdin', $this->command);
        $this->command->useFileAsInput = false;
        $this->command->image = $image;
        $this->command->imageSize = $size;
        return $this;
    }

    /**
     * .
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    public function withoutTempFiles(): TesseractOcr
    {
        FriendlyErrors::checkTesseractVersion('4.0.0', 'Writing to stdout (without using temp files)', $this->command);
        $this->command->useFileAsOutput = false;
        return $this;
    }

    /**
     * Set the image path if it wasn't set on the constructor? Uh??
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    public function image(string $image): TesseractOcr
    {
        $this->command->image = $image;
        return $this;
    }

    /**
     * Temporarily override the executable binary path.
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    public function executable(string $executable): TesseractOcr
    {
        //FriendlyErrors::checkTesseractPresence($executable);
        $this->command->executable = $executable;
        return $this;
    }

    /**
     * Set the path and filename of the config file to use.
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    /*public function configFile(string $configFile): TesseractOcr
    {
        $this->command->configFile = $configFile;
        return $this;
    }*/

    /**
     * Set the temporary directory to place output in
     * during processing of an image.
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    public function tempDir(string $tempDir): TesseractOcr
    {
        $this->command->tempDir = $tempDir;
        return $this;
    }

    /**
     * Set the thread limit.
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    public function threadLimit(string $limit): TesseractOcr
    {
        $this->command->threadLimit = $limit;
        return $this;
    }

    /**
     * Set the path and filename of the output file.
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    public function setOutputFile(string $path): TesseractOcr
    {
        $this->outputFile = $path;
        return $this;
    }

    /**
     * .
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    /*public function allowlist()
    {
        $concat = function ($arg) { return is_array($arg) ? join('', $arg) : $arg; };
        $allowlist = join('', array_map($concat, func_get_args()));
        $this->command->options[] = Option::config('tessedit_char_whitelist', $allowlist);
        return $this;
    }*/

    /**
     * Get the version of the Tesseract binary.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function version(): string
    {
        return $this->command->getTesseractVersion();
    }

    /**
     * Call Tesseract binary to get a list of available
     * languages that can be used.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function availableLanguages(): array
    {
        return $this->command->getAvailableLanguages();
    }

    /**
     * .
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    /*public function __call($method, $args)
    {
        if ($this->isConfigFile($method)) return $this->configFile($method);
        if ($this->isOption($method)) {
            $option = $this->getOptionClassName().'::'.$method;
            $this->command->options[] = call_user_func_array($option, $args);
            return $this;
        }
        $arg = empty($args) ? null : $args[0];
        $this->command->options[] = Option::config($method, $arg);
        return $this;
    }*/

    /**
     * .
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    /*private function isConfigFile($name)
    {
        return in_array($name, array('digits', 'hocr', 'pdf', 'quiet', 'tsv', 'txt'));
    }*/

    /**
     * Check if an option exists.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    private function isOption(string $name): bool
    {
        return in_array($name, get_class_methods($this->getOptionClassName()));
    }

    /**
     * Return the full namespace of the Option class.
     *
     * @since 1.0.0
     *
     * @return string
     */
    private function getOptionClassName(): string
    {
        return __NAMESPACE__.'\\Option';
    }

    /**
     * .
     *
     * @since 1.0.0
     *
     * @return \Captbrogers\TesseractOcr\TesseractOcr
     */
    /*private function cleanTempFiles()
    {
        if (file_exists($this->command->getOutputFile(false))) {
            unlink($this->command->getOutputFile(false));
        }
        if (file_exists($this->command->getOutputFile(true))) {
            unlink($this->command->getOutputFile(true));
        }
    }*/
}
