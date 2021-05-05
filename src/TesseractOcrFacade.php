<?php

namespace Captbrogers\TesseractOcr;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Captbrogers\TesseractOcr\TesseractOcr
 */
class TesseractOcrFacade extends Facade
{
    /**
     * .
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'tesseract-ocr';
    }
}
