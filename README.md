# itc/laravel-sugar

[build status badge here](about:FIXME)
[code coverage badge here](about:FIXME)
[code quality badge here](about:FIXME)

This is a thoroughly-tested collection of components and traits with no external dependencies. It reduces boilerplate code while adhering to [SOLID](https://en.wikipedia.org/wiki/SOLID_(object-oriented_design)) design principles. Its goal is to make working with Laravel sweeter than it already is.

## Features

### Model caching

Add caching behavior to any Laravel model.

```php
use Illuminate\Database\Eloquent\Model;
use ITC\Laravel\Sugar\Models\Behaviors\Caching as ModelCachingBehavior;

class Car extends Model
{
    use ModelCachingBehavior;
}

$car = Car::findOrFail(123);

// cache the model instance
$car->remember();

// retrieve the cached model instance
$car = Car::recall(123);

// uncache the model instance
$car->forget();
```

## Requirements

- PHP 7.0 or later
- Laravel 5.3 or later

## Installation

This is a [Composer](https://getcomposer.org/) package. Installation is trivial!

```
composer require itc/laravel-sugar
```

## Getting Help

-  [API Documentation](http://devdocs.it-consultis.com.cn/composer/itc-laravel-sugar/)
- Reach us on our [Slack channel](about:FIXME)

## License

[MIT](about:FIXME)

