<?php

namespace Captbrogers\TesseractOcr\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FileGetContentsUnavailableException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request   $request
     *
     * @return \Illuminate\Http\Response
     */
    public function render(Request$request): Response
    {
        return response(__('tesseract-ocr::errors.file_get_contents_unavailable'));
    }
}
