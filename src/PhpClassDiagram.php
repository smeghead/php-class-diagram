<?php declare(strict_types=1);
namespace Smeghead\PhpClassDiagram;

$options = getopt('h::',['help::'], $rest_index);
$arguments = array_slice($argv, $rest_index);

$usage =<<<EOS
usage: php PhpClassDiagram.php [-h] <target php source directory>

EOS;

if (isset($options['h'])) {
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
use Smeghead\PhpClassDiagram\Main;

new Main($directory);
