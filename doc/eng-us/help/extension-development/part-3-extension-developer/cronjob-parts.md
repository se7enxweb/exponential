---
title: Cronjob parts
audience: developer, app-creator
category: extension-development
order: 200
summary: Work on a schedule: a cronjob script in your extension, its own part in cronjob.ini, as a plain script or a CronjobPart class, with quiet mode, logging and the stop switches.
keywords: cronjob, cronjob part, cronjobs directory, cronjob.ini, ExtensionDirectories, CronjobPart, Scripts, runcronjobs.php, CronjobPart class, isQuiet, cli, eZLog, schedule, cronjob/part/run
type: help
---
# Cronjob parts

Part 3, chapter 20. Some work should not happen during a page request: sending a digest, importing a feed, cleaning
up. An extension ships such work as a **cronjob script**, grouped into a **part** that the system's crontab runs with
`runcronjobs.php`. The builder's view is [Cronjobs for builders](../part-2-builder/cronjobs-for-builders.md); this is
the developer's.

## Registration

`extension/mysite_hello/settings/cronjob.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[CronjobSettings]
ExtensionDirectories[]=mysite_hello

[CronjobPart-mysite_hello]
Scripts[]=hellodigest.php

*/ ?>
```

- `ExtensionDirectories[]` makes `runcronjobs.php` look in `extension/mysite_hello/cronjobs/`.
- A part of your own lets administrators schedule your work independently (`php runcronjobs.php -s site mysite_hello`).
  You may also add your script to an existing part (`[CronjobPart-infrequent]`), but your own part is easier to
  pause, schedule and debug.

The `expqueue` extension does exactly this: `ExtensionDirectories[]=expqueue` and `[CronjobPart-expqueue]` with
`Scripts[]=expqueue.php`.

## The script, as a class (recommended)

Since 6.0.15 the kernel's own cronjob scripts are classes; the file in `cronjobs/` is one line that hands its variables
to the class. `extension/mysite_hello/classes/runnable/cronjobs/hellodigest.php`:

```php
<?php
namespace Exponential\Cronjob\Extension\MysiteHello;

class HelloDigest extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $count = \mysiteHelloFunctionCollection::fetchNewest( 'article', 2, 10 );
        $number = is_array( $count['result'] ) ? count( $count['result'] ) : 0;
        \eZLog::write( "Digest: $number new articles", 'mysite_hello.log' );
        if ( empty( $scope['isQuiet'] ) )
        {
            $cli->output( "Digest prepared for $number articles" );
        }
    }
}
```

`extension/mysite_hello/cronjobs/hellodigest.php`:

```php
<?php
return \Exponential\Cronjob\Extension\MysiteHello\HelloDigest::main( __FILE__, get_defined_vars() );
```

`$scope` holds the variables `runcronjobs.php` defines for every script, among them `cli` (an `eZCLI`) and `isQuiet`
(the `-q` option). The base class is `kernel/private/classes/runnable/cronjobpart.php`; the pattern is described in
[Commands, cronjob parts and module views as classes](../../../bc/6.0/cli_cronjob_view_abstractions.md).

## The script, plain

Older scripts are plain PHP files that use the same variables directly:

```php
<?php
// extension/mysite_hello/cronjobs/hellodigest.php
if ( !$isQuiet )
{
    $cli->output( 'Preparing the digest' );
}
```

They keep working. New code is easier to test as a class.

## Run it

```bash
php runcronjobs.php --list | grep -A2 mysite_hello
php runcronjobs.php -s site mysite_hello
php runcronjobs.php -s site --script=hellodigest.php
```

## Rules for cronjob code

| Rule | Why |
|---|---|
| Respect `-q` | the crontab expects silence unless something is wrong |
| Write results to a log (`eZLog::write()`), errors with `eZDebug::writeError()` | nobody reads cron output |
| Work in batches, and stop after a time limit | a long run overlaps with the next one |
| Make it safe to run twice at once, or lock it | crontabs overlap when a run is slow |
| Run as the web server's user | files it writes must stay writable for the site |
| Never send mail to real people from a development installation | use the file mail transport there |

## Stop switches

Administrators can pause your part in **Setup > Cronjobs**, and a listener of the event `cronjob/part/run` can refuse
to run a part (for example during a release); see
[Leaving out a cronjob part](../../../features/6.0/cronjob-part-filter.md). You do not need to build your own switch.

## RAD

The cronjob extension point's tool is **Setup > Cronjobs** (`/setup/cronjobs`), which lists scripts no part runs and
activates them; the RAD catalogue entry "Cronjob script and part" describes the registration.

## Related pages

- Previous: [Triggers and workflows for developers](triggers-and-workflows-for-developers.md)
- Next: [CLI scripts and console commands](cli-scripts-and-console-commands.md)
- [By hand: a cronjob part](tutorials/by-hand-cronjob-part.md)
- [Cronjobs](../../../guides/cronjobs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
