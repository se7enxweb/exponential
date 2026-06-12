#!/bin/sh
# @description Check PHP source files for deprecated call-time pass-by-reference usage
# @long-description Scans a PHP file for the deprecated call-time pass-by-reference pattern (& before a function call argument). These patterns cause E_DEPRECATED in PHP 5.3+ and fatal errors in PHP 7+.

grep -n -H -E '[a-zA-Z0-9_]+ *\( [^&]*&\$[a-zA-Z_]+' $1 | grep -v -E 'function &?[a-zA-Z0-9]+ *\('
