<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tesseract Executable Binary
    |--------------------------------------------------------------------------
    |
    | Here you may specify the full path and file name of the executable
    | you wish to use when invoking the Tesseract OCR application.
    |
    */

    'executable_path' => '/usr/bin/tesseract',

    /*
    |--------------------------------------------------------------------------
    | .
    |--------------------------------------------------------------------------
    |
    | .
    |
    */

    'thread_limit' => '1',

    /*
    |--------------------------------------------------------------------------
    | Language Fallback
    |--------------------------------------------------------------------------
    |
    | This value is used when executing Tesseract and no language was
    | specified. Please change it to your preffered language.
    |
    */

    'fallback_language' => 'eng',

    /*
    |--------------------------------------------------------------------------
    | DPI Fallback
    |--------------------------------------------------------------------------
    |
    | Tesseract requires a DPI value at time of scanning, but if none is
    | given this value will be used.
    |
    */

    'fallback_dpi' => '70',
];
