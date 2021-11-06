<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

$options = getopt('h',[
    'help',
    'enable-class-properties',
    'disable-class-properties',
    'enable-class-methods',
    'disable-class-methods',
], $rest_index);
$arguments = array_slice($argv, $rest_index);

$usage =<<<EOS
usage: php PhpClassDiagram.php [OPTIONS] <target php source directory>

OPTIONS
  -h, --help                     show this help page.
      --enable-class-properties  describe properties in class diagram.
      --disable-class-properties not describe properties in class diagram.
      --enable-class-methods     describe methods in class diagram.
      --disable-class-methods    not describe methods in class diagram.
EOS;

if (isset($options['h']) || isset($options['help'])) {
    fputs(STDERR, $usage);
    exit(-1);
}

$directory = array_shift($arguments);
if (empty($directory)) {
    fputs(STDERR, "ERROR: not specified php source file.\n");
    fputs(STDERR, $usage);
    exit(-1);
}
if ( ! is_dir($directory)) {
    fputs(STDERR, sprintf("ERROR: specified directory dose not exists. directory: %s\n", $directory));
    fputs(STDERR, $usage);
    exit(-1);
}

include_once './vendor/autoload.php';
use Smeghead\PhpClassDiagram\ {
    Options,
    Main,
};

new Main($directory, new Options($options));
