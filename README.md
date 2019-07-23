# A Better Toolkit

[![Packagist Version](https://img.shields.io/packagist/v/abetter/toolkit.svg)](https://packagist.org/packages/abetter/toolkit)
[![Latest Stable Version](https://poser.pugx.org/abetter/toolkit/v/stable.svg)](https://packagist.org/packages/abetter/toolkit)
[![Total Downloads](https://poser.pugx.org/abetter/toolkit/downloads.svg)](https://packagist.org/packages/abetter/toolkit)
[![License](https://poser.pugx.org/abetter/toolkit/license.svg)](https://packagist.org/packages/abetter/toolkit)

ABetter Toolkit is a package of new and modified directives for faster development of component-based web applications, with focus on scalable static caching.

---

## Requirements

* PHP 7.2+
* MySQL 5.7+
* Composer 1.6+
* Laravel 5.8+
* Deployer 6+
* Node 10.0+
* NPM 6.4+

---

## Installation

Via Composer:

```bash
composer require abetter/toolkit
```

#### Laravel modifications

Add middleware to app/Http/Kernel.php:

```bash
protected $middleware = [
	\ABetter\Toolkit\SandboxMiddleware::class,
];
```

Note: The middleware helps Blade clear the view cache when developing many nested components.

----

## Directives

#### @component : Improved directive for injecting components

    @component('<view.name>',[<variables>])
	@component('<view.name>',TRUE)
	@component('<view.name>') <slot-here> @endcomponent

Component names will be auto-resolved if the blade file has same basename as folder.

You can auto-terminate a @component with TRUE as the second paramater, to avoid writing out @endcomponent, e.g when not using any slots or nested content.

#### @inject : Improved directive for injecting class as variable

    @inject('<variable>','<relative-class-file>')
	@inject('Menu','Menu.class.php')
	@inject('Menu')

Class-files will be auto-resolved if it's located in the component folder.

#### @block : Insert wrapped block section

	@block('<class-name>')
	@block('block--typography')
	@endblock

#### @style : Embedd sass/css in html source code

    @style('<relative-filename>')
	@style('menu.scss')

Embedded Sass/CSS files will be rendered as external files in development mode to support browsersync live, but will be embedded in html source on Stage/Production for better caching.

#### @script : Embedd js in html source code

    @script('<relative-filename>')
	@script('menu.js')

Embedded JS files will be rendered as external files in development mode to support browsersync live, but will be embedded in html source on Stage/Production for better caching.

#### @svg : Embedd svg in html source code

    @svg('<filename-relative-to-resources>')
	@svg('/images/logo.svg')

#### @lipsum : Insert mockup text

	@lipsum('<variables>')
	@lipsum('medium')

#### @pixsum : Insert mockup image

	@pixsum('<variables>','<options>')
	@pixsum('photo:tech')
	@pixsum('photo:tech','img:w500')

#### @logosum : Insert mockup svg logo

	@logosum('<variables>')
	@logosum('My Brand Name')

---

# Contributors

[Johan Sjöland](https://www.abetterstory.com/]) <johan@sjoland.com>  
Senior Product Developer: ABetter Story Sweden AB.

## License

MIT license. Please see the [license file](LICENSE) for more information.
