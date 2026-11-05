# ItalyStrap Settings API

[![Latest Stable Version](https://img.shields.io/packagist/v/italystrap/settings.svg)](https://packagist.org/packages/italystrap/settings)
[![Total Downloads](https://img.shields.io/packagist/dt/italystrap/settings.svg)](https://packagist.org/packages/italystrap/settings)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/italystrap/settings.svg)](https://packagist.org/packages/italystrap/settings)
[![License](https://img.shields.io/packagist/l/italystrap/settings.svg)](https://packagist.org/packages/italystrap/settings)
![PHP from Packagist](https://img.shields.io/packagist/php-v/italystrap/settings)

WordPress Settings API the OOP way

**Work in progress:** the project is currently in beta until considered viable. Until a 1.0.0 release the code in this repository is not stable. Expect changes breaking backward compatibility between minor versions (0.1.x -> 0.2.x).

## Table Of Contents

* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Advanced Usage](#advanced-usage)
* [Contributing](#contributing)
* [License](#license)

## Installation

The best way to use this package is through Composer:

```CMD
composer require italystrap/settings
```
This package adheres to the [SemVer](http://semver.org/) specification and will be fully backward compatible between minor versions.

## Idea of the structure

* Plugin MUST HAVE one options storage (call get_option one time and get all the options)
* Plugin COULD HAVE one or more settings pages with its own menu link (Parent and/or Child)
* Settings page COULD HAVE 0, 1 or many section
* Sections MUST HAVE at least 1 field
* Sections ARE separated by tabs
* Plugin COULD HAVE 0, 1 or more links in the plugins.php page

## Basic Usage

The simpler way to use it is to instantiate the Builder and add the stuff you need.

You can find last updated code in the [example.php](example.php) file

```php

use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\SettingsBuilder;

$text_domain = 'ItalyStrap';
$option_name = 'italystrap';
$settings_config = \ItalyStrap\Config\ConfigFactory::make(
	require __DIR__ . '/tests/_data/fixtures/config/settings.php'
);

// Initialize the builder
$settings = new SettingsBuilder(
	$option_name,
	ITALYSTRAP_BASENAME,
	ITALYSTRAP_FILE
);

// You can add configuration via the \ItalyStrap\Config\ConfigFactory::class
$settings->addPage(
	$settings_config->get( 'page' ),
	$settings_config->get( 'sections' )
);

// Ora manually
// The section parameter is optional
// Not every page need a section with fields
// For example in a docs page
// Manu title and slug are mandatory
$settings->addPage(
	[
		Page::PARENT		=> 'italystrap-dashboard',
		Page::PAGE_TITLE	=> \__( 'Dashboard 2', 'italystrap' ),
		Page::MENU_TITLE	=> \__( 'Child1', 'italystrap' ),
		Page::SLUG			=> 'slug-for-child-page',
		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/empty_form.php',
	]
);

// You can also add a sub page either for you parent page or for the WP admin pages
$settings->addPage(
	[
		Page::PARENT		=> 'options-general.php',
//		Page::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
		Page::MENU_TITLE	=> \__( 'Child-general', 'italystrap' ),
		Page::SLUG			=> 'slug-for-child-general',
		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/empty_form.php',
	]
);

// You can also add a link to the plugins.php page in your plugin link for activation
// For example if you want to add an external link to your docs.
$settings->addCustomPluginLink(
	'key-for-css',
	'http://localhost.com',
	'Custom',
	[ 'target' => '_blank' ]
);

// After you added pages[?section] and/or link call the build() method.
$settings->build();

```

## Advanced Usage

> TODO

## Contributing

All feedback / bug reports / pull requests are welcome.

## Development

PHP 8.2 or later. PHP, Composer and the database run in the Docker containers in `.docker/`, the same environment runs in CI. `make help` lists all the targets.

```bash
make up          # Start the containers
make install     # Install the dependencies and build the Codeception actors
make cs          # PHP CodeSniffer, PSR-12 and PHPCompatibility
make stan        # PHPStan at level max
make rector      # Rector dry run, make rector-fix applies it
make unit        # The unit suite, the core is not loaded
make integration # The integration suite, the core is loaded
make infection   # Mutation tests on the unit suite
make qa          # All the checks above
make down        # Stop and remove the containers
```

The PHP and core versions are in `.docker/test.env`.

## License

Copyright (c) 2019 Enea Overclokk, ItalyStrap

This code is licensed under the [MIT](LICENSE).

## Credits

> TODO