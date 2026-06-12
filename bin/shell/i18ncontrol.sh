#!/bin/bash
# @description Control and manage i18n translation source files
# @long-description Finds and processes PHP and template source files containing translatable strings for extraction into .ts translation files. Part of the Exponential translation workflow.

# find the files that we should process
echo "Searching for template files"
FILES=`find design -name "*.tpl"`
for file in $FILES; do
    ./bin/awk/i18ncontrol.awk $file
done

