# php-class-diagram

read php source and output PlantUML file.

## execute

php source files.
```
└─test
    └─fixtures
        └─no-namespace
            └─product
                    Name.php
                    Price.php
                    Product.php
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
