---
title: "By hand: a cronjob part"
audience: developer, app-creator
category: extension-development
order: 170
summary: Add a nightly digest to mysite_hello - a CronjobPart class, the one-line script, a part of its own in cronjob.ini, running it with runcronjobs.php and scheduling it in the crontab. All PHP files passed php -l.
keywords: tutorial, by hand, cronjob, cronjob part, CronjobPart, runcronjobs.php, cronjob.ini, CronjobPart-, ExtensionDirectories, crontab, isQuiet, eZLog
type: help
---
# By hand: a cronjob part

Tutorial 16. You add a digest that runs at night and writes the names of the newest articles to the extension's log.
It is written as a class, the way the kernel's own cronjob scripts are since 6.0.15. The files passed `php -l`.

## 1. The class

`extension/mysite_hello/classes/runnable/cronjobs/hellodigest.php`:

```php
<?php
namespace Exponential\Cronjob\Extension\MysiteHello;

/**
 * Cronjob part of mysite_hello: writes a digest of the newest articles to var/log/mysite_hello.log.
 * Registered in settings/cronjob.ini.append.php as [CronjobPart-mysite_hello] Scripts[]=hellodigest.php.
 */
class HelloDigest extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $result = \mysiteHelloFunctionCollection::fetchNewest( 'article', 2, 10 );
        $names = array();
        foreach ( $result['result'] as $node )
        {
            $names[] = $node->attribute( 'name' );
        }
        \eZLog::write( 'Digest: ' . count( $names ) . ' newest articles: ' . implode( '; ', $names ), 'mysite_hello.log' );
        if ( empty( $scope['isQuiet'] ) )
        {
            $cli->output( 'Digest written for ' . count( $names ) . ' articles' );
        }
    }
}
```

It reuses the fetch collection of tutorial 11, so the digest and the page show the same articles.

## 2. The script runcronjobs.php runs

`extension/mysite_hello/cronjobs/hellodigest.php`:

```php
<?php
return \Exponential\Cronjob\Extension\MysiteHello\HelloDigest::main( __FILE__, get_defined_vars() );
```

`main()` hands the variables `runcronjobs.php` defines (`cli`, `isQuiet` and the rest) to `run()` as `$scope`.

## 3. The part

`extension/mysite_hello/settings/cronjob.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[CronjobSettings]
ExtensionDirectories[]=mysite_hello

[CronjobPart-mysite_hello]
Scripts[]=hellodigest.php

[AdminSettings]
CrontabSchedule_mysite_hello=30 3 * * *

*/ ?>
```

`CrontabSchedule_mysite_hello` is the schedule **Setup > Cronjobs** proposes for the part.

## 4. Run it

```bash
php -l extension/mysite_hello/classes/runnable/cronjobs/hellodigest.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
php runcronjobs.php --list | grep -A1 mysite_hello
php runcronjobs.php -s <siteaccess> mysite_hello
tail -n 1 var/log/mysite_hello.log
```

Expected: the part with its script in the list; `Digest written for <n> articles`; the digest line in the log. With
`-q` the run prints nothing.

## 5. Schedule it

**Setup > Cronjobs** shows the part as not scheduled and proposes a crontab line with your installation's path and PHP.
Add it to the crontab of the user the web server runs as:

```text
30 3 * * * cd /path/to/installation && php runcronjobs.php -q -s <siteaccess> mysite_hello
```

## Related pages

- Previous: [By hand: a workflow event](by-hand-workflow-event.md)
- Next: [By hand: your own table with eZPersistentObject](by-hand-own-table-with-persistent-object.md)
- [Cronjob parts](../cronjob-parts.md), [Cronjobs for builders](../../part-2-builder/cronjobs-for-builders.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
