---
title: Ein Cronjob
audience: developer, admin
category: examples
order: 80
summary: Ein Cronjob-Skript in einer Erweiterung schreiben, ihm in cronjob.ini einen eigenen Cronjob-Teil geben, es mit runcronjobs.php und der Konsole ausführen und in der Crontab einplanen; dazu die Klassenform eines Cronjob-Teils.
keywords: Cronjob, Cronjob-Teil, Crontab, Konsole, Zeitplan, Sperre, cronjob, cronjob part, runcronjobs.php, cronjob.ini, CronjobSettings, ExtensionDirectories, CronjobPart, Scripts, crontab, cron:, console, CronjobPart class, isQuiet, cli, MaxScriptExecutionTime
type: example
---
# Ein Cronjob

Ein Cronjob-Skript ist eine PHP-Datei, die `runcronjobs.php` mit geladenem Kernel für einen Siteaccess einbindet; ein
**Teil** fasst Skripte zusammen, die gemeinsam nach einem Zeitplan laufen. Sie schreiben `expexample_report.php`, das
die in den letzten 24 Stunden veröffentlichten Objekte zählt und die Zahl in `var/log/expexample.log` schreibt, geben
ihm den Teil `expexample`, führen es von Hand aus und planen es ein.

Voraussetzungen: die Erweiterung aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert; eine Shell im
Wurzelverzeichnis der Installation; der Name eines Siteaccess der Website (hier `site`).

## 1. Verzeichnis und Teil registrieren

`extension/expexample/settings/cronjob.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[CronjobSettings]
# Look for cronjobs/<script>.php in this extension
ExtensionDirectories[]=expexample

# A part of its own: php runcronjobs.php -s <siteaccess> expexample
[CronjobPart-expexample]
Scripts[]=expexample_report.php

*/ ?>
```

`runcronjobs.php` nimmt `cronjobs/` jeder Erweiterung aus `[CronjobSettings] ExtensionDirectories[]` in die
Verzeichnisse auf, die es durchsucht. Ein Skript, das stattdessen in `[CronjobSettings] Scripts[]` eingetragen ist,
läuft im Standardteil, dem Teil, den `runcronjobs.php` ohne Teilnamen ausführt; ein eigener Teil hält Ihren Zeitplan
von dem des Kernels getrennt.

## 2. Das Skript

`extension/expexample/cronjobs/expexample_report.php`:

```php
<?php
/**
 * @description Example: count the objects published in the last 24 hours and write the number to the log
 *
 * A cronjob script of the expexample extension, run by runcronjobs.php inside the part
 * [CronjobPart-expexample] (cronjob.ini.append.php). runcronjobs.php has set up the siteaccess, the database and
 * the variables $cli (eZCLI) and $isQuiet before it includes this file.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$since = time() - 86400;
$db = eZDB::instance();
$rows = $db->arrayQuery( 'SELECT COUNT(*) AS count FROM ezcontentobject WHERE status = ' . (int)eZContentObject::STATUS_PUBLISHED .
                         ' AND published >= ' . (int)$since );
$count = $rows ? (int)$rows[0]['count'] : 0;

eZLog::write( "objects published in the last 24 hours: $count", 'expexample.log' );

if ( !$isQuiet )
    $cli->output( "expexample_report: $count objects published in the last 24 hours" );
```

- Das Tag `@description` ist das, was `./console list cron` und **Setup > Cronjobs** für das Skript anzeigen.
- Jeder Wert, der in SQL eingesetzt wird, wird zuerst in eine Ganzzahl umgewandelt; Zeichenketten laufen durch
  `$db->escapeString()`.
- Jedes Skript läuft unter einer eigenen Sperre: Ein zweiter Lauf, während der erste sie hält, wird übersprungen. Ein
  Lauf, der länger dauert als `[CronjobSettings] MaxScriptExecutionTime` (12 Stunden), verliert die Sperre an den
  nächsten; setzen Sie `MaxScriptExecutionTime` in `[CronjobPart-expexample]` für einen Teil, der mehr oder weniger
  braucht.

### Dasselbe als Klasse

Ab 6.0.15 sind die eigenen Teile des Kernels Klassen; das einfache Skript oben funktioniert weiterhin. Die
Klassenform, die sich testen und über Ereignisse erweitern lässt:

```php
<?php
// extension/expexample/classes/runnable/cronjobs/expexamplereport.php
namespace Exponential\Cronjob\Extension\Expexample;

class Expexamplereport extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $count = \expExampleReport::publishedSince( time() - 86400 );
        if ( empty( $scope['isQuiet'] ) )
            $scope['cli']->output( "expexample_report: $count objects published in the last 24 hours" );
    }
}
```

```php
<?php
// extension/expexample/cronjobs/expexample_report.php
return \Exponential\Cronjob\Extension\Expexample\Expexamplereport::main( __FILE__, get_defined_vars() );
```

`expExampleReport::publishedSince()` steht für eine eigene Klasse, die die Abfrage enthält, sodass auch eine Ansicht
oder ein Befehl sie aufrufen kann. Erzeugen Sie die Autoloads neu, nachdem Sie die Klassen hinzugefügt haben.

## 3. Von Hand ausführen

```bash
php -l extension/expexample/cronjobs/expexample_report.php
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php runcronjobs.php --list --allow-root-user                                   # CronjobPart-expexample is listed
php runcronjobs.php -s site expexample --allow-root-user                       # the part
php runcronjobs.php -s site --script=expexample_report.php --allow-root-user   # the one script
./console cron:expexample -s site --allow-root-user                            # the same through the console
```

Erwartet: `expexample_report: <n> objects published in the last 24 hours` und dieselbe Zahl in
`var/log/expexample.log`. Besser als `--allow-root-user`: Führen Sie den Job als Benutzer der Website aus, damit die
Dateien, die er schreibt, für den Webserver beschreibbar bleiben.

**Setup > Cronjobs** zeigt den Teil als Karte mit seinem Skript und seiner Beschreibung und führt ihn aus dem Browser
aus.

## 4. Einplanen

Eine Crontab-Zeile je Teil, für den Benutzer, unter dem die Website läuft:

```cron
30 2 * * * cd /var/www/example.com && php runcronjobs.php -q -s site expexample >/dev/null 2>&1
```

**Setup > Cronjobs** schlägt die fehlenden Zeilen unter *Vorgeschlagene Einträge* vor, mit dem Wurzelverzeichnis und
dem PHP-Programm dieser Installation; der Zeitplan, den es für einen Teil vorschlägt, kommt aus
`cronjob.ini [AdminSettings] CrontabSchedule_<part>`:

```ini
# settings/override/cronjob.ini.append.php
[AdminSettings]
CrontabSchedule_expexample=30 2 * * *
```

Für diese Seite geprüft (2026-10-18): `php -l` für das Skript; die Optionen von `runcronjobs.php` sind die aus
`kernel/private/classes/commands/runcronjobs.php`; die Suche nach Erweiterungsverzeichnissen ist dort
`eZExtension::expandedPathList( $extensionDirectories, 'cronjobs' )`. Die Klassenform ist die aus
`doc/eng-us/bc/6.0/cli_cronjob_view_abstractions.md`; sie wurde für diese Seite nicht ausgeführt.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| `--list` zeigt den Teil nicht | die Erweiterung ist inaktiv, der INI-Cache, oder dem Blocknamen fehlt `CronjobPart-` |
| *Cronjob part locked by other process* | ein vorheriger Lauf hält die Sperre noch; sie wird nach `MaxScriptExecutionTime` übernommen |
| Die Website kann vom Job geschriebene Dateien nicht ändern | der Job lief als root; führen Sie ihn als Benutzer der Website aus |
| Funktioniert von Hand, nicht aus cron | `PATH` und Arbeitsverzeichnis von cron: wechseln Sie mit `cd` in das Wurzelverzeichnis und geben Sie das PHP-Programm mit vollem Pfad an |

## Abgeleitet aus

- `kernel/private/classes/commands/runcronjobs.php`, `kernel/private/classes/runnable/cronjobpart.php`
- `cronjobs/unlock.php` und `kernel/private/classes/cronjobs/unlock.php` (ein Teil des Kernels in beiden Formen)
- `settings/cronjob.ini`; `doc/eng-us/guides/cronjobs.md`, Abschnitte 1, 12 und 13

## Verwandte Seiten

- [Kochbuch](README.md)
- [Cronjobs: ausführen, einplanen und verfolgen](../../anleitungen/cronjobs.md)
- [Cronjob-Teile](../cronjobs/README.md): jeder Teil und jedes Skript des Kernels
- [Befehle](../befehle/README.md): `runcronjobs.php` und die Konsole
- [Einen Cronjob-Teil auslassen](../../../eng-us/features/6.0/cronjob-part-filter.md),
  [Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
