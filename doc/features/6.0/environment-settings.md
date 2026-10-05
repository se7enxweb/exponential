# Settings per environment (EXP_ENV)

This page is for administrators and developers who run the same installation on several machines: a developer
machine, a test or staging copy and the live site. Since October 2026 one constant, `EXP_ENV`, names the environment
of a machine, and every settings file can have a variant for that environment next to it. The settings that differ
per machine no longer have to be edited by hand after each copy or deployment.

## Set it up

1. Copy `config.env.php.example` to `config.env.php` in the root of the installation and set the name:

   ```php
   <?php
   define( 'EXP_ENV', 'test' );
   ```

   `config.env.php` is read before `config.php`, by the web entry points and by the command line scripts, and
   `.gitignore` keeps it out of version control.

2. Put the settings of the environment in a variant file. The environment name goes right after `.ini`:

   | Standard file | Variant for `test` |
   |---|---|
   | `settings/site.ini` | `settings/site.ini.test` |
   | `settings/override/site.ini.append.php` | `settings/override/site.ini.test.append.php` |
   | `settings/siteaccess/admin/site.ini.append.php` | `settings/siteaccess/admin/site.ini.test.append.php` |
   | `extension/site_example/settings/site.ini.append.php` | `extension/site_example/settings/site.ini.test.append.php` |

   ```ini
   # settings/override/site.ini.test.append.php
   <?php /*
   [MailSettings]
   Transport=file

   [DebugSettings]
   DebugOutput=enabled
   */ ?>
   ```

3. Clear the INI cache. Each environment has its own cache files, so switching `EXP_ENV` needs no cache clear later.

## How the variants are read

- A variant is read **right after its standard file**, in every layer: the base settings, extensions, extension
  siteaccesses, siteaccesses and `settings/override`. It overrides the settings of its own layer only, so a siteaccess
  setting still wins over a base variant, and `settings/override` still wins over everything.
- A variant is also read when its standard file does not exist.
- Without `EXP_ENV`, eZINI reads exactly the files it read before, and the cache file names stay the same.
- A name must be lowercase letters, digits, `-` or `_`, start with a letter and have at most 32 characters. An
  invalid name is treated as no environment, and a line in the PHP error log says so.

## What the name changes besides settings

| `EXP_ENV` | Effect |
|---|---|
| `dev`, `test` | `index.php` shows PHP errors in the browser (`display_errors` on) |
| `prod`, any other name, none | `display_errors` stays as php.ini sets it |

Templates read the name with `{exp_environment()}`, an empty string without an environment:

```
{if eq( exp_environment(), 'test' )}<div class="test-site-banner">Test site</div>{/if}
```

## PHP

`eZINI::environment()` returns the name or `false`, `eZINI::environmentName( $value )` checks a name,
`eZINI::environmentFilePath( $filePath, $environment )` returns the variant path of a settings file, and
`eZINI::resetEnvironment()` makes the next call read `EXP_ENV` again (for tests). The operator is
`expEnvironmentOperator` (`kernel/common/expenvironmentoperator.php`).

## Tests

`tests/tests/lib/ezutils/eZINIEnvironmentTest.php` (lib suite): the order of the files per layer, a variant without
its standard file, unchanged files and cache file name without an environment, a cache file per environment, name
checks and variant paths.
