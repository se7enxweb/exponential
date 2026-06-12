#!/bin/sh
# @description Check PHP source files for print() statement usage
# @long-description Searches a PHP file for print() calls. The Exponential coding standard requires echo instead of print for output. Skips SDK and scrap directories.

if ! ( echo $1 | grep -E '(sdk|scrap|bf)/' &>/dev/null ); then grep -n -H -i "[ \t]print *(" $1; fi
