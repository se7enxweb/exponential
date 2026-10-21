---
title: Cronjob-Teile
audience: developer, app-creator
category: extension-development
order: 200
summary: Arbeit nach Zeitplan - ein Cronjob-Skript in Ihrer Erweiterung, ein eigener Teil in cronjob.ini, als einfaches Skript oder als CronjobPart-Klasse, mit stillem Modus, Protokollierung und den Schaltern zum Anhalten.
keywords: Cronjob, Cronjob-Teil, Zeitplan, Protokoll, stiller Modus, cronjob, cronjob part, cronjobs directory, cronjob.ini, ExtensionDirectories, CronjobPart, Scripts, runcronjobs.php, CronjobPart class, isQuiet, cli, eZLog, schedule, cronjob/part/run
type: help
---
# Cronjob-Teile

Teil 3, Kapitel 20. Manche Arbeit sollte nicht während einer Seitenanfrage geschehen: eine Zusammenfassung versenden,
einen Feed importieren, aufräumen. Eine Erweiterung liefert solche Arbeit als **Cronjob-Skript** aus, gruppiert in
einem **Teil**, den die Crontab des Systems mit `runcronjobs.php` ausführt. Die Sicht des Builders beschreibt
[Cronjobs für Builder](../teil-2-builder/cronjobs-fuer-builder.md); dies ist
die Sicht des Entwicklers.

## Registrierung

`extension/mysite_hello/settings/cronjob.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[CronjobSettings]
ExtensionDirectories[]=mysite_hello

[CronjobPart-mysite_hello]
Scripts[]=hellodigest.php

*/ ?>
```

- `ExtensionDirectories[]` sorgt dafür, dass `runcronjobs.php` in `extension/mysite_hello/cronjobs/` sucht.
- Ein eigener Teil erlaubt Administratoren, Ihre Arbeit unabhängig einzuplanen
  (`php runcronjobs.php -s site mysite_hello`). Sie können Ihr Skript auch einem vorhandenen Teil hinzufügen
  (`[CronjobPart-infrequent]`), aber ein eigener Teil lässt sich leichter anhalten, einplanen und debuggen.

Die Erweiterung `expqueue` macht genau das: `ExtensionDirectories[]=expqueue` und `[CronjobPart-expqueue]` mit
`Scripts[]=expqueue.php`.

## Das Skript als Klasse (empfohlen)

Seit 6.0.15 sind die eigenen Cronjob-Skripte des Kernels Klassen; die Datei in `cronjobs/` besteht aus einer Zeile,
die ihre Variablen an die Klasse übergibt. `extension/mysite_hello/classes/runnable/cronjobs/hellodigest.php`:

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

`$scope` enthält die Variablen, die `runcronjobs.php` für jedes Skript definiert, darunter `cli` (ein `eZCLI`) und
`isQuiet` (die Option `-q`). Die Basisklasse ist `kernel/private/classes/runnable/cronjobpart.php`; das Muster ist in
[Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
beschrieben.

## Das Skript als einfache Datei

Ältere Skripte sind einfache PHP-Dateien, die dieselben Variablen direkt verwenden:

```php
<?php
// extension/mysite_hello/cronjobs/hellodigest.php
if ( !$isQuiet )
{
    $cli->output( 'Preparing the digest' );
}
```

Sie funktionieren weiterhin. Neuer Code lässt sich als Klasse leichter testen.

## Ausführen

```bash
php runcronjobs.php --list | grep -A2 mysite_hello
php runcronjobs.php -s site mysite_hello
php runcronjobs.php -s site --script=hellodigest.php
```

## Regeln für Cronjob-Code

| Regel | Warum |
|---|---|
| `-q` beachten | die Crontab erwartet Stille, solange nichts schiefgeht |
| Ergebnisse in ein Protokoll schreiben (`eZLog::write()`), Fehler mit `eZDebug::writeError()` | niemand liest die Ausgabe von Cron |
| In Stapeln arbeiten und nach einer Zeitgrenze aufhören | ein langer Lauf überschneidet sich mit dem nächsten |
| Gleichzeitiges doppeltes Ausführen gefahrlos machen oder sperren | Crontabs überschneiden sich, wenn ein Lauf langsam ist |
| Als Benutzer des Webservers ausführen | Dateien, die das Skript schreibt, müssen für die Website beschreibbar bleiben |
| Von einer Entwicklungsinstallation nie E-Mails an echte Personen senden | verwenden Sie dort den Mail-Transport in Dateien |

## Schalter zum Anhalten

Administratoren können Ihren Teil unter **Setup > Cronjobs** anhalten, und ein Listener des Ereignisses
`cronjob/part/run` kann die Ausführung eines Teils verweigern (zum Beispiel während eines Releases); siehe
[Einen Cronjob-Teil auslassen](../../../funktionen/6.0/filter-fuer-cronjob-teile.md). Sie müssen keinen eigenen
Schalter bauen.

## RAD

Das Werkzeug des Erweiterungspunkts für Cronjobs ist **Setup > Cronjobs** (`/setup/cronjobs`): Es listet Skripte auf,
die kein Teil ausführt, und aktiviert sie; der Eintrag "Cronjob script and part" im RAD-Katalog beschreibt die
Registrierung.

## Verwandte Seiten

- Zurück: [Trigger und Workflows für Entwickler](trigger-und-workflows-fuer-entwickler.md)
- Weiter: [CLI-Skripte und Console-Befehle](cli-skripte-und-console-befehle.md)
- [Von Hand: ein Cronjob-Teil](tutorials/von-hand-cronjob-teil.md)
- [Cronjobs](../../../anleitungen/cronjobs.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
