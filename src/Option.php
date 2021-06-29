<?php

namespace Captbrogers\TesseractOcr;

class Option
{
    /**
     * Inform Tesseract which language(s) to use.
     *
     * @param string|array|null $lang
     *
     * @return string
     */
    public static function lang($lang = null): string
    {
        if (is_null($lang)) {
            $lang = config('tesseract-ocr.fallback_language');
        }

        if (is_array($lang)) {
            $lang = implode('+', $lang);
        }

        return "-l $lang";
    }

    /**
     * Inform Tesseract what DPI to expect for the image.
     *
     * @param string|int|null $lang
     *
     * @return string
     */
    public static function dpi($dpi = null): string
    {
        if (is_null($dpi)) {
            $dpi = config('tesseract-ocr.fallback_dpi');
        }

        return "--dpi $dpi";
    }

    /**
     * .
     *
     * @param string $psm
     *
     * @return string
     */
    public static function psm(string $psm): string
    {
        return function ($version) use ($psm) {
            $version = preg_replace('/^v/', '', $version);
            return (version_compare($version, 4, '>=') ? '-' : '')."-psm $psm";
        };
    }

    /**
     * .
     *
     * @param string $oem
     *
     * @return string
     */
    public static function oem(string $oem): string
    {
        return function ($version) use ($oem) {
            Option::checkMinVersion('3.05', $version, 'oem');
            return "--oem $oem";
        };
    }

    /**
     * .
     *
     * @param string $path
     *
     * @return string
     */
    public static function userWords(string $path): string
    {
        return function ($version) use ($path) {
            Option::checkMinVersion('3.04', $version, 'user-words');
            return '--user-words "'.addcslashes($path, '\\"').'"';
        };
    }

    /**
     * .
     *
     * @param string $path
     *
     * @return string
     */
    public static function userPatterns(string $path): string
    {
        return function ($version) use ($path) {
            Option::checkMinVersion('3.04', $version, 'user-patterns');
            return '--user-patterns "'.addcslashes($path, '\\"').'"';
        };
    }

    /**
     * .
     *
     * @param string $path
     *
     * @return string
     */
    public static function tessdataDir(string $path): string
    {
        return function () use ($path) {
            return '--tessdata-dir "'.addcslashes($path, '\\"').'"';
        };
    }

    /**
     * .
     *
     * @param string $keyname
     * @param string|int|null $value
     *
     * @return string
     */
    public static function config(string $keyname, $value): string
    {
        return function () use ($keyname, $value) {
            $snakeCaseKeyname = strtolower(preg_replace('/([A-Z])+/', '_$1', $keyname));
            $pair = $snakeCaseKeyname . '=' . $value;
            return '-c "'.addcslashes($pair, '\\"').'"';
        };
    }

    /**
     * .
     *
     * @throws Exception
     *
     * @param string $minimumVersion
     * @param string $currentVersion
     * @param string|int|null $option
     *
     * @return bool|null
     */
    public static function checkMinVersion(string $minimumVersion, string $currentVersion, $option): ?bool
    {
        $minimumVersion = preg_replace('/^v/', '', $minimumVersion);
        $currentVersion = preg_replace('/^v/', '', $currentVersion);

        if (! version_compare($currentVersion, $minimumVersion, '<')) {
            return;
        }

        $message = "$option option is only available on Tesseract $minimumVersion or later.";
        $message.= PHP_EOL."Your version of Tesseract is $currentVersion";
        throw new \Exception($message);
    }
}
