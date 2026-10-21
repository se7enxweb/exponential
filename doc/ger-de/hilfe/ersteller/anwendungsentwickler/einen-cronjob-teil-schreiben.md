---
title: Einen Cronjob-Teil schreiben
audience: app-creator, developer
category: app-creation
order: 290
summary: Das Skript schreiben, das ein Cronjob-Teil ausführt - die klassenbasierte Form mit CronjobPart, die Variablen, die es erhält, Ausgabe und stiller Modus, eine Beschreibung für die Konsole, Stapel, Idempotenz, Benutzer und Fehler.
keywords: Cronjob-Skript, Cronjob-Teil, Stapel, idempotent, stiller Modus, Anwendungsentwickler, cronjob script, CronjobPart, runcronjobs, cli, isQuiet, description tag, batch, idempotent, cronjob part, Exponential Runnable, app creator
type: help
---
# Einen Cronjob-Teil schreiben

Diese Seite richtet sich an Anwendungsentwickler, die den Code schreiben, den ein Cronjob-Teil ausführt. Neue Skripte
sind Klassen, die `Exponential\Runnable\CronjobPart` erweitern, mit einer einzeiligen Einstiegsdatei; die einfache
Dateiform älterer Skripte funktioniert weiterhin
([Ein Cronjob-Teil](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md#ein-cronjob-teil)).

## Inhalt

1. [Die Klasse und die Einstiegsdatei](#die-klasse-und-die-einstiegsdatei)
2. [Was das Skript erhält](#was-das-skript-erhält)
3. [Ausgabe](#ausgabe)
4. [Die Arbeit gut erledigen](#die-arbeit-gut-erledigen)
5. [Testen](#testen)

## Die Klasse und die Einstiegsdatei

```php
<?php
// extension/expdemo/classes/runnable/cronjobs/expdemoimport.php
namespace Exponential\Cronjob\Extension\Expdemo;

class ExpdemoImport extends \Exponential\Runnable\CronjobPart
{
    public function run( array $scope )
    {
        $cli = $scope['cli'];
        $result = \expDemoImportService::importNewItems( 100 );   // the work lives in a service class
        if ( empty( $scope['isQuiet'] ) )
            $cli->output( "Imported {$result['created']} items, updated {$result['updated']}" );
    }
}
```

```php
<?php
/**
 * @description Import new items from the partner feed
 */
// extension/expdemo/cronjobs/expdemo_import.php
return \Exponential\Cronjob\Extension\Expdemo\ExpdemoImport::main( __FILE__, get_defined_vars() );
```

Generieren Sie die Autoloads neu, fügen Sie das Skript einem Teil hinzu ([Cronjobs für Anwendungen](cronjobs-fuer-anwendungen.md))
und führen Sie es mit `php runcronjobs.php -s <siteaccess> --script=expdemo_import.php` aus.

## Was das Skript erhält

`get_defined_vars()` übergibt, was `eZRunCronjobs::runScript()` definiert: `$cli` (ein `eZCLI` für die Ausgabe) und
`$isQuiet`. Der Kernel ist für den mit `-s` angegebenen Siteaccess initialisiert; der aktuelle Benutzer ist anonym.

## Ausgabe

- Eine Zeile je aussagekräftigem Ergebnis; im stillen Modus keine Ausgabe außer Fehlern.
- `$cli->error( ... )` für Fehler, damit sie in Logs auffallen.
- Das Tag `@description` ist das, was `php bin/php/console list cron` und **Setup > Cronjobs** anzeigen
  ([Ihr eigenes Skript beschreiben](../../../bc/6.0/konsole.md#das-eigene-skript-beschreiben)).

## Die Arbeit gut erledigen

| Regel | Warum |
|---|---|
| In Stapeln mit einem Limit je Lauf arbeiten | ein Lauf muss enden, bevor der nächste beginnt |
| Idempotent machen (Remote-IDs, Markierungen „verarbeitet“) | ein abgestürzter Lauf wird einfach wiederholt |
| Einen eigens dafür vorgesehenen Benutzer anmelden, wenn Sie Inhalte schreiben | Rechte und Audit nennen ein echtes Konto, nicht den Administrator |
| Fehler je Element abfangen, weitermachen, am Ende berichten | ein fehlerhafter Datensatz soll den Rest nicht aufhalten |
| Caches im Speicher zwischen den Stapeln leeren | langen Läufen geht sonst der Speicher aus |
| Langsame Einzelaufgaben an eine Warteschlange übergeben | der Teil bleibt kurz ([Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)) |

## Testen

Halten Sie die Logik in einer Dienstklasse und testen Sie sie mit PHPUnit; führen Sie das Skript auf einer
Entwicklungskopie von Hand mit `--script` aus; prüfen Sie die Ausgabe unter **Setup > Cronjobs**
([Anwendungen testen](anwendungen-testen.md)).

## Verwandte Seiten

- [Cronjobs für Anwendungen](cronjobs-fuer-anwendungen.md)
- [CLI-Skripte](cli-skripte.md)
- Cronjob-Kochbuch (`doc/eng-us/examples/cookbook/cronjob.md`)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
