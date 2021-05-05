<?php

namespace Captbrogers\TesseractOcr;

class TesseractOcrServiceProvider
{
    /**
     * .
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/tesseract-ocr.php' => config_path('tesseract-ocr.php'),
        ]);
    }

    /**
     * .
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tesseract-ocr.php', 'tesseract-ocr');
    }
}
