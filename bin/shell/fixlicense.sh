#!/bin/bash
# @description Update legacy eZ Systems license URLs in PHP source files
# @long-description Rewrites the old license URL (ez.no/home/licences/professional) to the current URL (ez.no/products/licences/professional) in all PHP files under the current directory using sed in-place replacement.

for i in `rgrep -R '*.php' -i -l 'http://ez.no/home/licences/professional' .`; do
    echo "Fixing $i"
    mv "$i" "$i.tmp"
    cat "$i.tmp" | sed 's#http://ez.no/home/licences/professional#http://ez.no/products/licences/professional#' > "$i"
done

