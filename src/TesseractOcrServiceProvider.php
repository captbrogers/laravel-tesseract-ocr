<?php

namespace Captbrogers\TesseractOcr;

use Illuminate\Support\ServiceProvider;

class TesseractOcrServiceProvider extends ServiceProvider
{
    /**
     * .
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tesseract-ocr');

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
