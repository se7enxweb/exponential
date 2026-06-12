#!/bin/sh
# @description Check PHP foreach loop syntax for Exponential coding standard compliance
# @long-description Scans a PHP file for foreach loops that do not conform to the Exponential coding standard regarding spaces, variable naming, and bracing style.

grep -n -H -E 'foreach' $1 | grep -v -E 'foreach \( ([a-zA-Z_]+\( \$[a-zA-Z_]+ \)|\$[a-zA-Z_]+(->[a-zA-Z_]+\([^)]*\))?) as \$[a-zA-Z_]+( => \$[a-zA-Z_]+)? \) *[^{]? *(//.*)?$'
