---
title: Einen Konsolenbefehl schreiben
audience: php-developer, developer
category: php-development
order: 230
summary: Eine Anleitung für ein Kommandozeilenwerkzeug in einer Erweiterung - die Befehlsklasse, die Exponential\Runnable\Command erweitert, die Einstiegsdatei, Optionen und Hilfe, Siteaccess und Datenbank, Ausgabe und Exit-Codes, das Tag @description und die Ausführung über bin/php/console als ext:<extension>:<name>.
keywords: Konsolenbefehl, Kommandozeile, Exit-Code, Befehl einer Erweiterung, console command, CLI, Exponential\Runnable\Command, bin/php/console, ext namespace, eZScript, eZCLI, getOptions, startup, shutdown, exit code, description tag, allow-root-user, siteaccess, extension command
type: help
---
# Einen Konsolenbefehl schreiben

Diese Anleitung richtet sich an PHP-Entwickler, die einer Erweiterung ein Kommandozeilenwerkzeug hinzufügen: einen
Import, einen Bericht, ein Reparaturskript. Der Befehl ist eine Klasse, die `Exponential\Runnable\Command` erweitert;
eine kurze Einstiegsdatei ruft sie auf; die Konsole `bin/php/console` findet sie ohne jede Registrierung.

## Inhalt

1. [Das Ergebnis](#das-ergebnis)
2. [Schritt 1: die Befehlsklasse](#schritt-1-die-befehlsklasse)
3. [Schritt 2: die Einstiegsdatei](#schritt-2-die-einstiegsdatei)
4. [Schritt 3: Autoloads und ein erster Lauf](#schritt-3-autoloads-und-ein-erster-lauf)
5. [Optionen und Argumente](#optionen-und-argumente)
6. [Die Hilfsmethoden](#die-hilfsmethoden)
7. [Ausgabe, Logging und Exit-Codes](#ausgabe-logging-und-exit-codes)
8. [Ausführung als root, Siteaccesses und Benutzer](#ausführung-als-root-siteaccesses-und-benutzer)
9. [Lang laufende Befehle](#lang-laufende-befehle)

## Das Ergebnis

```bash
php bin/php/console ext:acmereport:report --month=2026-09
php bin/php/console list ext:acmereport            # every command of the extension
php bin/php/console ext:acmereport:report --help
```

## Schritt 1: die Befehlsklasse

`extension/acmereport/classes/runnable/commands/report.php`:

```php
<?php
namespace Exponential\Command\Extension\Acmereport;

class Report extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "Writes the monthly report of acmereport",
                              'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
        $options = $this->startup( '[month:]', '', array( 'month' => 'The month, YYYY-MM' ) );

        $month = (string)$options['month'];
        if ( !preg_match( '/^\d{4}-\d{2}$/', $month ) )
        {
            $this->error( 'Give the month as --month=YYYY-MM' );
            $this->shutdown( 1 );
        }

        $totals = \acmeReportService::monthlyTotals( $month );   // the work: a class you can call anywhere
        foreach ( $totals as $status => $total )
            $this->output( sprintf( '%-12s %8d', $status, $total ) );

        $this->shutdown( 0 );
    }
}
```

## Schritt 2: die Einstiegsdatei

`extension/acmereport/bin/php/report.php`:

```php
#!/usr/bin/env php
<?php
/**
 * @description Writes the monthly report of acmereport
 */
require_once 'autoload.php';
\Exponential\Command\Extension\Acmereport\Report::main( __FILE__ );
```

Das Tag `@description` ist das, was `php bin/php/console list` anzeigt; ein Tag `@long-description` fügt der Hilfe Text
hinzu. Machen Sie die Datei ausführbar (`chmod +x`).

## Schritt 3: Autoloads und ein erster Lauf

```bash
php -l extension/acmereport/classes/runnable/commands/report.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console ext:acmereport:report --help
php bin/php/console ext:acmereport:report --month=2026-09
```

Führen Sie jeden Befehl aus dem Wurzelverzeichnis der Installation aus (dem Verzeichnis mit `index.php`);
`require_once 'autoload.php'` verlässt sich darauf.

## Optionen und Argumente

Der Options-String hat das Format von `eZCLI::parseOptionString()`, das `eZScript::getOptions()` verwendet:

| Form | Bedeutung |
|---|---|
| `[o]` | eine kurze Option, `-o` |
| `[opt]` | eine lange Option, `--opt` |
| `[o\|opt]` | dieselbe Option in kurzer oder langer Form |
| `[opt:]` | eine Option mit Wert, `--opt=value` |
| `[opt;]` | ein optionaler Wert, `--opt` oder `--opt=value` |
| `[opt*]`, `[opt+]`, `[opt?]` | beliebig oft, mindestens einmal, höchstens einmal angegeben |

Lesen Sie die Werte aus dem zurückgegebenen Array über den langen Namen (`$options['month']`), und prüfen Sie eine
Option, die fehlen kann, mit `empty()`, bevor Sie sie verwenden.

Das zweite Argument beschreibt Positionsargumente; das dritte liefert den Hilfetext jeder Option. Die Standardoptionen
gibt es gratis: `--help`, `-q`/`--quiet`, `-s`/`--siteaccess`, `-l`/`--login`, `-d`/`--debug`, `-v`/`--verbose`.

## Die Hilfsmethoden

| Hilfsmethode | Tut |
|---|---|
| `$this->script( $settings )` | erzeugt das Skript, `eZScript::instance( $settings )` |
| `$this->startup( $config, $argumentConfig, $optionHelp )` | `startup()` des Skripts, die Optionen, `initialize()` des Skripts (Siteaccess, Erweiterungen, Sitzung, Datenbank) |
| `$this->options( ... )` | nur das Parsen der Optionen, wenn Sie Schritte dazwischen brauchen |
| `$this->cli()` | das Terminal, `eZCLI::instance()` |
| `$this->output()`, `error()`, `warning()` | eine Zeile, ein Fehler, eine Warnung; `output()` schreibt mit `-q` nichts |
| `$this->isQuiet()` | `-q` wurde angegeben |
| `$this->log( $message, $logName = null )` | `eZLog::write()`, standardmäßig nach `var/log/<script name>.log` |
| `$this->shutdown( $exitCode, $exitText )` | `eZScript::shutdown()`, das beendet |

## Ausgabe, Logging und Exit-Codes

- Beenden Sie mit `0` bei Erfolg und einem anderen Code bei einem Fehlschlag: Deploy-Skripte, Cron und CI lesen ihn,
  und die Konsole gibt ihn unverändert weiter.
- Schreiben Sie Ergebnisse auf die Standardausgabe und Probleme über `error()`, sodass eine Pipe nur die Ergebnisse erhält.
- Protokollieren Sie, was Daten geändert hat (`$this->log()`), niemals Passwörter, Tokens oder personenbezogene Daten.
- Bieten Sie `--dry-run` für alles an, was Daten ändert.

## Ausführung als root, Siteaccesses und Benutzer

- Skripte verweigern die Ausführung als `root`, sofern nicht `--allow-root-user` angegeben ist. Führen Sie sie nach
  Möglichkeit als Benutzer der Site aus, damit die Dateien, die sie in `var/` schreiben, der Site gehören.
- `-s <siteaccess>` wählt die Einstellungen; ohne die Option wird der Standard-Siteaccess verwendet.
- In einem Skript ist der aktuelle Benutzer anonym, bis das Skript einen anmeldet. Verwenden Sie einen eigenen Benutzer
  mit genau den Richtlinien, die der Befehl braucht, nicht den Administrator.

## Lang laufende Befehle

- Holen Sie Inhalte seitenweise und leeren Sie die In-Memory-Caches zwischen den Seiten
  (`eZContentObject::clearCache()`), sonst wächst der Speicher mit jedem Objekt.
- Committen Sie in Stapeln mit kurzen Transaktionen.
- Geben Sie den Fortschritt aus, sofern nicht `-q`; Cron läuft mit `-q`.
- Befehle, die nach Zeitplan laufen sollen, sind besser ein Cronjob-Teil, der denselben Dienst aufruft
  ([Einen Cronjob-Teil schreiben](einen-cronjob-teil-schreiben.md)).

## Verwandte Seiten

- [Modul-Views als Klassen](modul-views-als-klassen.md)
- [Exponential Console](../../bc/6.0/konsole.md)
- [Konsolenbefehle für Entwickler](../erweiterungsentwicklung/teil-8-referenz/konsolenbefehle-fuer-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
