# L5 Swagger extension - Annotation Tag Cleanup 🧹 - for OpenApi or Swagger Specification

A Laravel API documentation extension for L5-Swagger to organize Swagger specification operations based on tags, enhancing API documentation clarity and ease of navigation.

---

## About This Project

Welcome to the L5 Swagger Extension - Annotation Tag Cleanup, a custom Laravel package born from a genuine need to bring order and clarity to Swagger documentation.

🔍 The Challenge: As a developer, you've probably experienced the complexity of managing extensive API documentation. Like many, I found myself navigating through the labyrinth of Swagger documentation. Attempts to use Swagger processors or custom processors for cleanup seemed like hitting a brick wall.

🌟 The Solution: That's when the idea struck! Why not create something simple yet efficient, something that resonates with the ease and elegance Laravel is known for? And thus, this extension was born - a tool designed to declutter and organize Swagger operations based on tags.

🔗 What It Does: This extension enhances the L5-Swagger package by allowing you to effortlessly organize your API documentation. It filters operations, removes orphaned schemas, and ensures that your API documentation is as neat as your code.

🤝 Community Contribution: Built with the community in mind, this package isn't just a solution to my own struggles; it's a contribution to fellow developers who share the same pain points. It's open, it's straightforward, and it's here to make your Swagger documentation a breeze!

---

## Installation

`composer require "alfreddagenais/l5-swagger-annotation-tag-cleanup"`

---

## Usage Example

Place the following configs in `config/l5-swagger.php`

```php
return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            ...
            'atcOptions' => [
                'cleanOrphanSchemas' => true,
                'exclude' => [
                    'operations' => [
                        'tags' => ['mytagname', 'customtag2'],
                    ],
                ],
            ],
            ...
        ],
        ...
    ],
    ...
]
```

Run `php artisan l5-swagger-extatc:generate` to generate docs

---

## Settings

Keys can be added directly to your config file `config/l5-swagger.php`

Key Name | Description | Required | Default
--- | --- | --- | ---
`include` | Annotation to include | No | []
`exclude` | Annotation to exclude | No | []
`cleanOrphanSchemas` | Clean Orphan Schemas | No | false

### Include

Key Name | Description | Required | Default
--- | --- | --- | ---
`operations` | Operations | No | []
`securitySchemes` | Security Schemes | No | false

#### Operations

Key Name | Description | Required | Default
--- | --- | --- | ---
`tags` | Tags | No | []

#### Security Schemes

Key Name | Description | Required | Default
--- | --- | --- | ---
`names` | Names | No | []

---

## Settings Examples

```php
'atcOptions' => [
    'cleanOrphanSchemas' => true,
    'exclude' => [
        'operations' => [
            'tags' => ['mytagname', 'customtag2'],
        ],
    ],
],
```

```php
'atcOptions' => [
    'include' => [
        'operations' => [
            'tags' => ['mytagname'],
        ],
        'securitySchemes' => [
            'names' => ['oauth2'],
        ],
    ],
],
```

```php
'atcOptions' => [
    'include' => [
        'operations' => [
            'tags' => ['customtag2'],
        ],
        'securitySchemes' => [
            'names' => ['oauth2'],
        ],
    ],
    'exclude' => [
        'operations' => [
            'tags' => ['mytagname'],
        ],
        'securitySchemes' => [
            'names' => ['apikey'],
        ],
    ],
],
```

---

## Command Options

- Run `php artisan l5-swagger-extatc:generate --all` to generate all docs
- Run `php artisan l5-swagger-extatc:generate default` to generate default docs
- Run `php artisan l5-swagger-extatc:generate customname` to generate specific customname docs

See more command options on [DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)

---

## Disclaimer

This package is a wrapper of [DarkaOnLine/L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)

The actual Swagger spec is beyond the scope of this package. All L5-Swagger does is package up swagger-php and swagger-ui in a Laravel-friendly fashion, and tries to make it easy to serve. For info on how to use swagger-php [look here](https://zircote.github.io/swagger-php/). For good examples of swagger-php in action [look here](https://github.com/zircote/swagger-php/tree/master/Examples/petstore.swagger.io).

---

## License

MIT
