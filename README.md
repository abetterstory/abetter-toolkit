# A Better Toolkit

[![Packagist Version](https://img.shields.io/packagist/v/abetter/toolkit.svg)](https://packagist.org/packages/abetter/toolkit)
[![Latest Stable Version](https://poser.pugx.org/abetter/toolkit/v/stable.svg)](https://packagist.org/packages/abetter/toolkit)
[![Total Downloads](https://poser.pugx.org/abetter/toolkit/downloads.svg)](https://packagist.org/packages/abetter/toolkit)
[![License](https://poser.pugx.org/abetter/toolkit/license.svg)](https://packagist.org/packages/abetter/toolkit)

ABetter Blade toolkit for Laravel 5+

## Install
- composer require abetter/toolkit

## Register Middleware
- Add to $middleware in app/Kernel.php
- \ABetter\Toolkit\SandboxMiddleware::class,

## Directives
- @component('<view.name>',[<variables>])
- @inject('<variable>','<relative-class-file>')
- @style('<relative-filename>')
- @script('<relative-filename>')
