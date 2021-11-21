# php-class-diagram

A CLI tool that parses the PHP source directory and outputs PlantUML class diagram scripts.

# Features

 * Generating class diagrams from source code helps improve continuous design.
 * Generates expressive class diagrams with an emphasis on namespaces and relationships.
 * A simple CLI tool that is easy to handle.
 * It is also possible to output a package relationship diagram that visualizes the dependency on the external namespace.

[![Latest Stable Version](http://poser.pugx.org/smeghead/php-class-diagram/v)](https://packagist.org/packages/smeghead/php-class-diagram) [![Total Downloads](http://poser.pugx.org/smeghead/php-class-diagram/downloads)](https://packagist.org/packages/smeghead/php-class-diagram) [![Latest Unstable Version](http://poser.pugx.org/smeghead/php-class-diagram/v/unstable)](https://packagist.org/packages/smeghead/php-class-diagram) [![License](http://poser.pugx.org/smeghead/php-class-diagram/license)](https://packagist.org/packages/smeghead/php-class-diagram) [![PHP Version Require](http://poser.pugx.org/smeghead/php-class-diagram/require/php)](https://packagist.org/packages/smeghead/php-class-diagram)

### php-class-diagram class diagram

![dogfood class diagram image.](dogfood.png)

### php-class-diagram package related diagram

![dogfood package related diagram image.](dogfood-package.png)

## Install

```bash
$ mkdir sample
$ cd sample
$ composer init
$ composer require smeghead/php-class-diagram
```

you can execute `./vendor/bin/php-class-diagram`.
for instance, try to display help message.

```bash
$ vendor/bin/php-class-diagram --help
usage: php-class-diagram [OPTIONS] <target php source directory>

A CLI tool that parses the PHP source directory and outputs PlantUML class diagram scripts.

OPTIONS
  -h, --help                     show this help page.
      --class-diagram            output class diagram script. (default)
      --package-diagram          output package diagram script.
      --enable-class-properties  describe properties in class diagram.
      --disable-class-properties not describe properties in class diagram.
      --enable-class-methods     describe methods in class diagram.
      --disable-class-methods    not describe methods in class diagram.
      --php5                     parse php source file as php5.
      --php7                     parse php source file as php7.
      --php8                     parse php source file as php8. (not suppoted)
```

## How to execute

When three php source files that TYPE commented exist in `test/fixtures/no-namespace`,

 * php source files.

```
└─test
    └─fixtures
        └─no-namespace
            └─product
                    Product.php
                    Name.php
                    Price.php
```

 * Product.php
```php
<?php
class Product {
    /** @var Name   product name. */
    private $name;
    /** @var Price  price of product. */
    private $price;
}
```

 * Name.php
```php
<?php
class Name {
    /** @var string  name. */
    private $name;
}
```

 * Price.php
```php
<?php
class Price {
    /** @var int  price. */
    private int $price;
}
```

To execute `php-class-diagram` will print PlantUML script.

```bash
$ vendor/bin/php-class-diagram test/fixtures/no-namespace
@startuml
  package product as product <<Rectangle>> {
    class product.Price
    class product.Name
    class product.Product
  }
  product.Product ..> product.Name
  product.Product ..> product.Price
@enduml
```

Use PlnatUML to convert the PlantUML script to an image.

![PlantUML output image.](output.png)

## Development

### Open shell

```bash
docker-compose build
docker-compose run --rm php_cli bash
```

### install dependencies

```bash
composer install
```

### execute tests

```bash
composer test
```
