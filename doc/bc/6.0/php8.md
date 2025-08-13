# PHP 8 support

## PHP 8.0 support

For the [2023.12.05 release](https://github.com/se7enxweb/exponential/releases/tag/v2023.13.05),
Exponential received changes all over the code base, switching variable syntax to PHP 8 style, variable return type comparison checking before use errors under PHP 8.2, and more. 

The reason is to achieve full PHP 8.0 support by avoiding the deprecation warnings when using PHP 8 syntax.

Care has been taken to keep around compatibility functions in all known cases to avoid fatal errors
for custom extensions, however to avoid warnings you might need to adapt your code as well.

Common cases are classes extending `eZPersistentObject` or `eZDataType`.

Further reading:
- [www.php.net/manual/en/migration80.incompatible.php](https://www.php.net/manual/en/migration80.incompatible.php)
- [www.php.net/manual/en/migration81.incompatible.php](https://www.php.net/manual/en/migration81.incompatible.php)

## PHP 8.2 support

Starting with the 2023.12 release, issues happening on PHP 8.1 and PHP 8.2 have been fixed, but in your own code (extensions) you'll
also need to handle some of those.

Further reading:
- [www.php.net/manual/en/migration82.incompatible.php](https://www.php.net/manual/en/migration82.incompatible.php)

## PHP 8.3 support

Starting with the 2023.12 release, most issues happening on PHP 8.2 and PHP 8.3 have been fixed, but in your own code (extensions) you'll
also need to handle some of those.

Further reading:
- [www.php.net/manual/en/migration83.incompatible.php](https://www.php.net/manual/en/migration83.incompatible.php)
