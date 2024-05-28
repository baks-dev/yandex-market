# BaksDev Api YandexMarket

[![Version](https://img.shields.io/badge/version-7.0.5-blue)](https://github.com/baks-dev/yandex-market/releases)
![php 8.2+](https://img.shields.io/badge/php-min%208.1-red.svg)

Модуль Yandex Market Api

## Установка

``` bash
$ composer require baks-dev/yandex-market
```

## Дополнительно

Каждому токену добавляем свой транспорт очереди

``` php
$messenger
->transport('<UUID>')
->dsn('%env(MESSENGER_TRANSPORT_DSN)%')
->options(['queue_name' => 'profile_name'])
->retryStrategy()
->maxRetries(3)
->delay(1000)
->maxDelay(0)
->multiplier(2)
->service(null);
```

Тесты

``` bash
$ php bin/phpunit --group=yandex-market
```

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.

