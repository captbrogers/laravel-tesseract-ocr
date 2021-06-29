<?php

return [
    // Given when the executable binary is not found.
    'binary_not_found' => 'The Tesseract OCR binary could not be found.',

    // Given when the source image is not found.
    'image_not_found' => 'The image you specified to be scanned was not found.',

    // Given when the scan completes but the output is considered garbage.
    'failed_scan' => 'Generic failed scan error.',

    // If the PHP function proc_open() isn't found or has been disabled.
    'proc_open_unavailable' => 'The function `proc_open()` is not available, could be disabled.',

    // If the PHP function prog_get_status() isn't found or has been disabled.
    'prog_get_status_unavailable' => 'The function `prog_get_status()` is not available, could be disabled.',

    // If the PHP function proc_close() isn't found or has been disabled.
    'proc_close_unavailable' => 'The function `proc_close()` is not available, could be disabled.',

    // If the PHP function exec() isn't found or has been disabled.
    'exec_unavailable' => 'The function `exec()` is not available, could be disabled.',

    // If the PHP function file_get_contents() isn't found or has been disabled.
    'file_get_contents_unavailable' => 'The function `file_get_contents()` is not available, could be disabled.',

    // If the PHP function file_exists() isn't found or has been disabled.
    'file_exists_unavailable' => 'The function `file_exists()` is not available, could be disabled.',

    // If the PHP function fread() isn't found or has been disabled.
    'fread_unavailable' => 'The function `fread()` is not available, could be disabled.',

    // If the PHP function fwrite() isn't found or has been disabled.
    'fwrite_unavailable' => 'The function `fwrite()` is not available, could be disabled.',

    // If the PHP function fclose() isn't found or has been disabled.
    'fclose_unavailable' => 'The function `fclose()` is not available, could be disabled.',

    // If the PHP function stream_set_blocking() isn't found or has been disabled.
    'stream_set_blocking_unavailable' => 'The function `stream_set_blocking()` is not available, could be disabled.',

    // Given on a successful scan but unable to save the output to a file.
    'bad_write_permissions' => 'Unable to write output from Tesseract scan to file, bad permissions.',
];
