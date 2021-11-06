# php-class-diagram

read php source and output PlantUML file.

## How to execute

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

```bash
$ php src/PhpClassDiagram.php test/fixtures/no-namespace
@startuml
  package "product" <<Rectangle>> {
    class Name
    class Price
    class Product
  }
  Product ..> Name
  Product ..> Price
@enduml
```

![PlantUML output image.](output.png)

## development

### Open shell

```bash
docker-compose build
docker-compose run --rm php_cli bash
```

### install dependencies

```bash
composer update
```

### execute tests

```bash
composer test test/
```
