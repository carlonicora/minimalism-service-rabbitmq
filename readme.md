# minimalism-service-rabbitmq

**minimalism-service-rabbitmq** is a service for [minimalism](https://github.com/carlonicora/minimalism) to send
messages to rabbitMQ and retrieve them

## Getting Started

To use this library, you need to have an application using minimalism. This library does not work outside this scope.

### Prerequisite

You should have read the [minimalism documentation](https://github.com/carlonicora/minimalism/readme.md) and understand
the concepts of services in the framework.

### Installing

Require this package, with [Composer](https://getcomposer.org/), in the root directory of your project.

```
$ composer require carlonicora/minimalism-service-rabbitmq
```

or simply add the requirement in `composer.json`

```json
{
    "require": {
        "carlonicora/minimalism-service-rabbitmq": "~1.0"
    }
}
```

## Deployment

This service requires you to set up two parameters in your `.env` file in order to manage queue messages

### Required parameters

```dotenv
#defines the name of the queue messages are sent to or retrieved from 
MINIMALISM_SERVICE_RABBITMQ_QUEUE_NAME=

#defines the connection ot rabbitMQ
MINIMALISM_SERVICE_RABBITMQ=host,port,user,password
```

## Build With

* [minimalism](https://github.com/carlonicora/minimalism) - minimal modular PHP MVC framework
* [php-amqplib](https://github.com/php-amqplib/php-amqplib)

## Versioning

This project use [Semantiv Versioning](https://semver.org/) for its tags.

## Authors

* **Carlo Nicora** - Initial version - [GitHub](https://github.com/carlonicora) |
[phlow](https://phlow.com/@carlo)

# License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT) - see the
[LICENSE.md](LICENSE.md) file for details 

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)