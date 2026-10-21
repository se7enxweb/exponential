---
title: "Von Hand: ein Cronjob-Teil"
audience: developer, app-creator
category: extension-development
order: 170
summary: mysite_hello eine nächtliche Zusammenfassung hinzufügen - eine CronjobPart-Klasse, das einzeilige Skript, ein eigener Teil in cronjob.ini, die Ausführung mit runcronjobs.php und die Planung in der Crontab. Alle PHP-Dateien haben php -l bestanden.
keywords: Tutorial, von Hand, Cronjob, Cronjob-Teil, Zusammenfassung, Zeitplan, Crontab, tutorial, by hand, cronjob, cronjob part, CronjobPart, runcronjobs.php, cronjob.ini, CronjobPart-, ExtensionDirectories, crontab, isQuiet, eZLog
type: help
---
# Von Hand: ein Cronjob-Teil

Tutorial 16. Sie fügen eine Zusammenfassung hinzu, die nachts läuft und die Namen der neuesten Artikel in das Protokoll
der Erweiterung schreibt. Sie ist als Klasse geschrieben, so wie die eigenen Cronjob-Skripte des Kernels seit 6.0.15.
Die Dateien haben `php -l` bestanden.

## 1. Die Klasse

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

Sie verwendet die Fetch-Sammlung aus Tutorial 11 wieder, sodass die Zusammenfassung und die Seite dieselben Artikel
zeigen.

## 2. Das Skript, das runcronjobs.php ausführt

`extension/mysite_hello/cronjobs/hellodigest.php`:

```php
<?php
return \Exponential\Cronjob\Extension\MysiteHello\HelloDigest::main( __FILE__, get_defined_vars() );
```

`main()` übergibt die Variablen, die `runcronjobs.php` definiert (`cli`, `isQuiet` und die übrigen), als `$scope` an
`run()`.

## 3. Der Teil

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

`CrontabSchedule_mysite_hello` ist der Zeitplan, den **Setup > Cronjobs** für den Teil vorschlägt.

## 4. Ausführen

```bash
php -l extension/mysite_hello/classes/runnable/cronjobs/hellodigest.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
php runcronjobs.php --list | grep -A1 mysite_hello
php runcronjobs.php -s <siteaccess> mysite_hello
tail -n 1 var/log/mysite_hello.log
```

Erwartet: der Teil mit seinem Skript in der Liste; `Digest written for <n> articles`; die Zeile der Zusammenfassung im
Protokoll. Mit `-q` gibt der Lauf nichts aus.

## 5. Planen

**Setup > Cronjobs** zeigt den Teil als nicht geplant an und schlägt eine Crontab-Zeile mit dem Pfad und dem PHP Ihrer
Installation vor. Tragen Sie sie in die Crontab des Benutzers ein, unter dem der Webserver läuft:

```text
30 3 * * * cd /path/to/installation && php runcronjobs.php -q -s <siteaccess> mysite_hello
```

## Verwandte Seiten

- Zurück: [Von Hand: ein Workflow-Ereignis](von-hand-workflow-ereignis.md)
- Weiter: [Von Hand: Ihre eigene Tabelle mit eZPersistentObject](von-hand-eigene-tabelle-mit-persistentem-objekt.md)
- [Cronjob-Teile](../cronjob-teile.md), [Cronjobs für Builder](../../teil-2-builder/cronjobs-fuer-builder.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
