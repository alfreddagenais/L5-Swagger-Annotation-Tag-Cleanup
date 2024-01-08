# Contributing

Contributions of any kind are welcome.

Feel free to submit [Github Issues](https://github.com/alfreddagenais/L5-Swagger-Annotation-Tag-Cleanup/issues)
or [pull requests](https://github.com/alfreddagenais/L5-Swagger-Annotation-Tag-Cleanup/pulls).

## How-To

* [Fork](https://help.github.com/articles/fork-a-repo/) the repo.
* [Checkout](https://git-scm.com/docs/git-checkout) the branch you want to make changes on.
  * Typically, this will be `main`. Note that most of the time, `main` represents the next release of swagger-php, so Pull Requests that break backwards compatibility might be postponed.
* Install dependencies: `composer install`.
* Create a new branch, e.g. `feature-foo` or `bugfix-bar`.
* Make changes.
* If you are adding functionality or fixing a bug - add a test!

  Prefer adding new test cases over modifying existing ones.
* Update documentation if needed.
* Run static analysis using PHPStan/Psalm: `composer analyse`.
* Check if tests pass: `composer test`.
* Fix code style issues: `composer cs`.

## Useful commands

### To run both unit tests and linting execute

```shell
composer test
```

### To run static-analysis execute

```shell
composer analyse
```

### Running unit tests only

```shell
./bin/phpunit
```

### Running linting only

```shell
composer lint
```

### To make `php-cs-fixer` fix linting errors

```shell
composer cs
```

## Project's Standards

* [PSR-1: Basic Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
* [PSR-2: Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR-4: Autoloading Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR-5: PHPDoc (draft)](https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md)
