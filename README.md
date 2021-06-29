# Laravel wrapper for Tesseract OCR

[![Latest Version on Packagist](https://img.shields.io/packagist/v/captbrogers/laravel-tesseract-ocr.svg?style=flat-square)](https://packagist.org/packages/captbrogers/laravel-tesseract-ocr)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/captbrogers/laravel-tesseract-ocr/run-tests?label=tests)](https://github.com/captbrogers/laravel-tesseract-ocr/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/captbrogers/laravel-tesseract-ocr/Check%20&%20fix%20styling?label=code%20style)](https://github.com/captbrogers/laravel-tesseract-ocr/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/captbrogers/laravel-tesseract-ocr.svg?style=flat-square)](https://packagist.org/packages/captbrogers/laravel-tesseract-ocr)

I would first like to thank thiagoalessio for his work on the [Tesseract OCR for PHP package](https://github.com/thiagoalessio/tesseract-ocr-for-php). I leaned heavily on that code to make this.

One of the main differences between this package and thiagoalessio's is that this package does not have compatibility for PHP prior to version 7.3. This package has exceptions thrown in the event that certain required PHP functions are not available. Those functions being `exec`, `proc_open`, `prog_get_status`, `proc_close`, `file_get_contents`, `file_exists`, `fread`, `fwrite`, `fclose`, `stream_set_blocking`. Some hosting providers or server configurations may have them disabled for security reasons and in those cases you should be informed of why an image scan failed.

## Installation

You can install this package via composer:

```bash
composer require captbrogers/laravel-tesseract-ocr
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Captbrogers\TesseractOcr\TesseractOcrServiceProvider" --tag="laravel-tesseract-ocr-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$tesseract = new Captbrogers\TesseractOcr();
$tesseract->lang('deu')
    ->dpi(300)
    ->run();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Brian Rogers](https://github.com/captbrogers)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
