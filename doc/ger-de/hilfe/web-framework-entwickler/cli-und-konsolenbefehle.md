---
title: "Kommandozeile: die Konsole und eZScript"
audience: web-framework-developer, developer
category: web-frameworks
order: 310
summary: Das Gegenstück von Exponential zu bin/console, artisan, manage.py, Rails-Befehlen und Spring-Boot-Runnern - bin/php/console mit den Namensräumen exp:, ext:, shell: und cron:, einen Befehl als Klasse Exponential\Runnable\Command oder als klassisches eZScript-Skript schreiben, Optionen, Siteaccess und Benutzer, Exit-Codes und die Befehle, die Sie täglich verwenden.
keywords: Konsole, Kommandozeile, Befehle, Konsolenbefehle, Skripte, Exit-Codes, console, CLI, command line, bin/php/console, artisan, manage.py, bin/console, exp:, ext:, cron:, eZScript, eZCLI, Exponential\Runnable\Command, getOptions, startup, shutdown, allow-root-user, siteaccess, exit codes, Web-Framework-Entwickler, web framework developer
type: help
---
# Kommandozeile: die Konsole und eZScript

Diese Seite richtet sich an Entwickler, die in `bin/console`, `php artisan`, `manage.py` oder `bin/rails` zu Hause
sind. Exponential hat einen einzigen Einstiegspunkt für alle Befehle, `bin/php/console` (Installationen legen meist
eine Abkürzung `./console` im Stammverzeichnis an). Er findet Befehle zur Laufzeit anhand ihres Speicherorts, sodass
der Befehl einer Erweiterung nicht registriert werden muss
([Exponential Console](../../bc/6.0/konsole.md), [Konsolenbefehle](../ersteller/anwendungsentwickler/konsolenbefehle.md)).

## Inhalt

1. [Befehle finden](#befehle-finden)
2. [Befehlsnamen](#befehlsnamen)
3. [Einen Befehl schreiben](#einen-befehl-schreiben)
4. [Siteaccess, Benutzer und root](#siteaccess-benutzer-und-root)
5. [Befehle, die Sie täglich verwenden](#befehle-die-sie-täglich-verwenden)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Befehle finden

```bash
php bin/php/console                   # every command
php bin/php/console list ext:expdemo  # one namespace
php bin/php/console --version
```

## Befehlsnamen

| Namensraum | Führt aus | Beispiel |
|---|---|---|
| `exp:<name>` | `bin/php/<name>.php` | `exp:ezcache` |
| `shell:<name>` | `bin/shell/<name>.sh` | |
| `bin:<name>` | `bin/<name>.sh` oder `.php` | |
| `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` oder `bin/<name>.sh` | `ext:expqueue:status` |
| `cron:<part>` | einen Cronjob-Teil | `cron:frequent` |

Ein Skript kann mit einem `@alias`-Tag in seinem Docblock zusätzliche Namen deklarieren (`bin/php/exphelp.php`
deklariert `@alias exp:help`), und ein `@description`-Tag beschreibt es in `list`.

## Einen Befehl schreiben

Neue Befehle sind Klassen, die `Exponential\Runnable\Command` erweitern und von einem einzeiligen Skript aufgerufen
werden ([CLI-Skripte mit eZScript](../ersteller/anwendungsentwickler/cli-skripte.md)):

```php
<?php
// extension/expdemo/classes/runnable/commands/report.php
namespace Exponential\Command\Extension\Expdemo;

class Report extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "Writes the monthly report",
                              'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
        $options = $this->startup( '[month:][dry-run]', '', array(
            'month'   => 'The month, YYYY-MM',
            'dry-run' => 'Show what would be written, write nothing' ) );
        $this->output( \expDemoReportService::build( $options['month'], !empty( $options['dry-run'] ) ) );
        $this->shutdown( 0 );
    }
}
```

```php
#!/usr/bin/env php
<?php
// extension/expdemo/bin/php/report.php
require_once 'autoload.php';
\Exponential\Command\Extension\Expdemo\Report::main( __FILE__ );
```

Aufruf: `php bin/php/console ext:expdemo:report --month=2026-10 --dry-run`. Die klassische Form, die viele Skripte
verwenden, ruft `eZScript::instance()`, `startup()`, `getOptions()`, `initialize()` und `shutdown()` direkt auf; die
Ausgabe läuft über `eZCLI` (`output()`, `notice()`, `warning()`, `error()`). Optionszeichenketten verwenden `[name:]`
für einen Wert und `[name]` für einen Schalter; `--help`, `-s/--siteaccess`, `--allow-root-user` und die
Debug-Optionen werden automatisch ergänzt.

## Siteaccess, Benutzer und root

- `-s <siteaccess>` wählt die Einstellungen aus; Erweiterungen, die nur in diesem Siteaccess aktiv sind, benötigen es.
- Der Benutzer ist anonym. Ein Befehl, der Inhalte schreibt, meldet einen eigenen Benutzer an
  (`eZUser::setCurrentlyLoggedInUser( $user, $user->id() )`), niemals den gemeinsam genutzten Administrator.
- Skripte verweigern die Ausführung als root, sofern nicht `--allow-root-user` angegeben ist; führen Sie sie als
  Benutzer des Webservers aus, denn Dateien, die ein root-Prozess in `var/` schreibt, beschädigen die Website später.
- Der Exit-Code wird unverändert zurückgegeben: `shutdown( 0 )` bei Erfolg, jeder andere Wert bei einem Fehler.
- Bieten Sie für jeden Befehl, der schreibt oder löscht, `--dry-run` an, und nehmen Sie niemals Passwörter als
  Optionen entgegen.

## Befehle, die Sie täglich verwenden

| Befehl | Wirkung |
|---|---|
| `exp:ezpgenerateautoloads -e` | Autoloads der Erweiterungen nach dem Hinzufügen von Klassen neu erzeugen |
| `exp:ezcache --clear-tag=ini` | Caches nach Tag oder ID leeren |
| `exp:cache status` | jede Cache-Ebene, PASS oder FAIL |
| `exp:ini get/set/where` | Einstellungen lesen und ändern |
| `exp:velocity deploy` | Autoloads, Caches, PHP-Reload, Velocity-Neustart, in der richtigen Reihenfolge |
| `exp:install` | eine neue Installation mit einem Befehl ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)) |
| `exp:help check`, `exp:help search` | Linkprüfung und Suche der Hilfe |
| `exp:maintenance on/off` | Wartungsmodus |
| `cron:<part>` | einen Cronjob-Teil ausführen |

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony | als Services registrierte Befehle, `#[AsCommand]` | anhand des Speicherorts gefundene Dateien, `Exponential\Runnable\Command` |
| Laravel | Artisan-Befehle, `$signature` | die Optionszeichenkette von `startup()` / `getOptions()` |
| Django | Management-Befehle in `management/commands/` | Skripte in `extension/<ext>/bin/php/` |
| Rails | Rake-Tasks, `bin/rails`-Befehle | dasselbe |
| Spring | `CommandLineRunner` | eine Befehlsklasse |

## Verwandte Seiten

- [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Muster für Befehle](../../standards/muster/befehl.md)
- [Geplante Aufgaben: Cronjobs](geplante-aufgaben-und-cronjobs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
