# phpmailer-dsn

[![CI](https://github.com/voronkovich/phpmailer-dsn/actions/workflows/ci.yml/badge.svg)](https://github.com/voronkovich/phpmailer-dsn/actions/workflows/ci.yml)

Library for configuring [PHPMailer](https://github.com/PHPMailer/PHPMailer) with [DSN string](https://en.wikipedia.org/wiki/Data_source_name).

# Abandoned!!!

This feature [was merged to PHPMailer](https://github.com/PHPMailer/PHPMailer/pull/2874). Use the PHPMailer itself instead:

```sh
composer require phpmailer/phpmailer:^6.8.0
```

## Installation

```sh
composer require voronkovich/phpmailer-dsn
```

## Usage

```php
use Voronkovich\PHPMailerDSN\DSNConfigurator;
use PHPMailer\PHPMailer\PHPMailer;

$mailer = new PHPMailer(true);
$configurator = new DSNConfigurator();

$configurator->configure($mailer, 'smtp://localhost:2525');
```

## Configuraton

Supported protocols:

- `mail`
- `sendmail`
- `qmail`
- `smtp`
- `smtps`

Additional configuration could be applied via query string:

```php
$dsn = 'mail://localhost?XMailer=SuperMailer&FromName=CoolSite';

$configurator->configure($mailer, $dsn);
```

[PHPMailer](https://github.com/PHPMailer/PHPMailer) is configured by public properties, so you can use any of them. All allowed options could be found at [PHPMailer Docs](https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-PHPMailer.html#toc-properties).

## Examples

### Sendmail

```php
$dsn = 'sendmail://localhost?Sendmail=/usr/sbin/sendmail%20-oi%20-t';

$configurator->configure($mailer, $dsn);
```

### SMTP

```sh
$dsn = 'smtp://user@password@localhost?SMTPDebug=3&Timeout=1000';

$configurator->configure($mailer, $dsn);
```

### Gmail

```sh
$dsn = 'smtps://user@gmail.com:password@smtp.gmail.com?SMTPDebug=3';

$configurator->configure($mailer, $dsn);
```

## License

Copyright (c) Voronkovich Oleg. Distributed under the MIT.
