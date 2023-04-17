#!/bin/bash

for d in $(find .  -maxdepth 1 -type d | grep -v '^\.$'); do /usr/src/bin/php-class-diagram  $d | plantuml -pipe -tpng > output-images/$d.png; done
for d in $(find .  -maxdepth 1 -type d | grep -v '^\.$'); do /usr/src/bin/php-class-diagram --package-diagram $d | plantuml -pipe -tpng > output-images/$d-package.png; done
