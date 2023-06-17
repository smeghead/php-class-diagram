# CHANGELOG

### Features

 * Cut out PlantUML's formatter to interface. This is preparation for Mermaid support.

## v1.1.0 (2023-04-24)


### Features

 * Corresponds to the method of defining field variables with constructor arguments introduced in php8.


## v1.0.0 (2023-04-06)


## v0.6.1 (2023-04-03)

### Features

 * Implove unit tests.

## v0.6.0 (2023-03-27)

### Features

 * Added a division diagram to the output when the --dig-diagram option is specified.
 * Display a summary of the class Document Comment under the class name in the class diagram.

## v0.5.0 (2023-03-12)

### Features

 * add division diagram option.

### Bug fix

 * Fixed so that unnamed external packages are not displayed in the package association diagram.

## v0.4.0 (2023-03-01)

### Bug fix

 * Fixed. #40 If the namespace name and class name are the same, the class will not be displayed

## v0.3.1 (2023-02-24)

### Features

 * support enum.

### Bug fix

 * Fixed an error when loading traits. #37

## v0.3.0 (2023-02-23)

### Features

 * Fixed so that the external package of the package relationship diagram is displayed hierarchically.
 * Changed package notation in class diagram from square to default notation.

### Bug fix

 * Method's Parameter Dependencies added to arrows.
 * Removed duplicate dependency arrows in class diagrams.

## v0.2.1 (2023-02-16)

### Bug fix

 * Fixed an exception that occurred when a UnionType was specified in DocString.
 * Fixed an issue where DocString did not correctly compare types when describing an array of classes.

## v0.2.0 (2023-02-16)

### Features

 * add support Union Types.
 * remove PHP7.4 support. 

## v0.1.1 (2202-12-12)

### Bug fix

 * Fixed to align the indentation of the line specified in header options.

## v0.1.0 (2202-12-08)

### Features

 * add package diagram description to README.
 * Updated the PHP version of the development environment to 8.1.
 * add `--include` option. Enabled to specify the search pattern of the PHP source code to be processed.
 * add `--exclude` option. Enabled to specify the exclusion pattern of the PHP source code to be processed.

## v0.0.8 (2022-11-21)

### Features

 * add `--header` options.


## v0.0.7 (2022-02-16)

### Features

 * `--enable-class-properties` and `--enable-class-methos` options is on by default.

### Bug fix

 * fix recognize FullyQualified type name in php doc.

## v0.0.6 (2021-11-27)

### Features

 * refactor properties names and variable names. rename Namespace to Package.
 * In the package relationship diagram, the dependency in both directions is
   shown by a thick red line. Two-way dependencies may indicate a bad design.
   
### Bug fix

 * In the package relationship diagram, relation name dose not included package name.

## v0.0.5 (2021-11-22)

### Added

 * refactor class name. rename Namespace\_ to Package.
 * refactor class name. rename PhpRelection to Php\PhpReader.
 * refactor class name. rename Relation to DiagramElement\Relation.
 * refactor class name. rename Options to Config\Options.
 * add --jig-diagram option that shows class-diagram and package-related-diagram.

## v0.0.4 (2021-11-21)

### Added

 * refactor class structure. remove PhpClassClass and PhpClassNamespace.

### Bug fix

 * fix a bug wrong extends and implements class type.
 * Corrected to analyze all classes when there are two or more classes in one file.

## v0.0.3 (2021-11-18)

### Added

 * print ROOT package to package relation diagram. To make it easier to
   distinguish from the package to be analyzed in the case of a package-related
   diagram that includes an external namespace.

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

