#!/bin/bash
set -e

SCRIPT_DIR=$(cd $(dirname $0); pwd)

$SCRIPT_DIR/bin/php-class-diagram \
	--enable-class-properties \
	--enable-class-methods $SCRIPT_DIR/src \
	| plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/dogfood.png
$SCRIPT_DIR/bin/php-class-diagram \
	--disable-class-properties \
	--disable-class-methods $SCRIPT_DIR/src \
	| plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/dogfood-model.png
$SCRIPT_DIR/bin/php-class-diagram \
	--package-diagram $SCRIPT_DIR/src \
	| plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/dogfood-package.png
$SCRIPT_DIR/bin/php-class-diagram \
	--division-diagram $SCRIPT_DIR/test/fixtures/enum \
	| plantuml -charset utf-8 -pipe -tpng > $SCRIPT_DIR/output-division.png
