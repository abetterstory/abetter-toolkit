# LABS-ABetter-Toolkit v1.0.4

ABetter Blade toolkit for Laravel 5+

## Install
```bash
composer require abetter/toolkit
```

## Register Middleware

Add to the `$middleware` array in app/Kernel.php:
- \ABetter\Toolkit\SandboxMiddleware::class,

## Usage

```php
# Put demo code here.
```

## Directives
- @component('<view.name>',[<variables>])
- @inject('<variable>','<relative-class-file>')
- @style('<relative-filename>')
- @script('<relative-filename>')
