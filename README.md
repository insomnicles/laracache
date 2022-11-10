# Laracache

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

A trait for accessing Laravel Eloquent models directly from Cache

Source code packagist.

## Installation

Via Composer

``` bash
$ composer require insomnicles/laracache
```

## Usage

Make sure your app is connected to a Cache: memcache, redis, etc.
- If you're using Sail in Laravel 8+, this is setup out of the box


Add the Cachable Trait to your Model; for example, for the User Model
```
	use Insomnicles\Laracache\Cachable.php

	class User {
		use Cachable;

	}

```

Use the Cached Model via Eloquent-like methods
```
	User::refreshCache();
	User::allInCache();
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/insomnicles/laracache.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/insomnicles/laracache.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/insomnicles/laracache/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/insomnicles/laracache
[link-downloads]: https://packagist.org/packages/insomnicles/laracache
[link-travis]: https://travis-ci.org/insomnicles/laracache
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/insomnicles
[link-contributors]: ../../contributors
