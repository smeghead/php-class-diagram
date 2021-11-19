# CHANGELOG

## v0.0.3 (2021-11-18)

### Bug fix

 * in `bin/php-class-diagram`, changed how to load `autoload.php`.
 * fail to get namespace of FullyQualified.

## v0.0.2 (2021-11-18)

### Added

 * Added dependency arrows. The class has a dependency on the return type of the method with no public arguments.
 * Changed the output class name to include the package name.
 * Added package diagram option.
 * make package expression hierarchy in output script.
 * support package relation diagram.

### Bug fix

 * fix bug that failed to parse NullableType method parameter.
 * fix bug that wrong namespace found when search use statements.

## v0.0.1 (2021-11-11)

### Added

 * Added -v --version option.
 * access modifier information to class properties and methods.

## v0.0.0 (2021-11-08)

### Added

 * registered to Packagist.

