---
title: Einfache CLI-Skripte mit eZScript und ezexec
audience: php-developer, developer
category: php-development
order: 250
summary: Der klassische Weg, ein Kommandozeilenskript mit eZScript und eZCLI zu schreiben, der weiterhin funktioniert und dem Sie in älteren Erweiterungen begegnen werden - instance(), startup(), getOptions(), initialize(), shutdown() - dazu das Ausführen von einmaligem Code mit gebootetem Kernel über bin/php/ezexec.php und wann Sie ein Skript in eine Befehlsklasse überführen.
keywords: CLI-Skript, einmaliges Skript, Bootstrap, älteres Skript, eZScript, eZCLI, CLI script, getOptions, startup, initialize, shutdown, ezexec.php, one-off script, bootstrap, use-session, use-modules, use-extensions, allow-root-user, legacy script
type: help
---
# Einfache CLI-Skripte mit eZScript und ezexec

Diese Seite richtet sich an PHP-Entwickler, die als einfache Dateien geschriebene Kommandozeilenskripte pflegen oder ein
Stück PHP mit gebootetem Kernel ausführen müssen. Neue Befehle werden als Klassen geschrieben
([Einen Konsolenbefehl schreiben](einen-konsolenbefehl-schreiben.md)); die einfache Form läuft weiterhin genau wie bisher, und
jeder klassenbasierte Befehl verwendet darunter dasselbe `eZScript`, sodass es so oder so hilft, es zu kennen.

## Inhalt

1. [Der Lebenszyklus](#der-lebenszyklus)
2. [Ein einfaches Skript](#ein-einfaches-skript)
3. [Die Einstellungen von eZScript::instance()](#die-einstellungen-von-ezscriptinstance)
4. [Ausgabe mit eZCLI](#ausgabe-mit-ezcli)
5. [Code mit dem Kernel ausführen: ezexec.php](#code-mit-dem-kernel-ausführen-ezexecphp)
6. [Ein einfaches Skript in eine Klasse überführen](#ein-einfaches-skript-in-eine-klasse-überführen)

## Der Lebenszyklus

| Schritt | Aufruf | Tut |
|---|---|---|
| 1 | `eZScript::instance( $settings )` | erzeugt das Skriptobjekt |
| 2 | `$script->startup()` | prüft, dass es auf der Kommandozeile läuft, setzt die Fehlerberichterstattung und die Debug-Behandlung |
| 3 | `$script->getOptions( $config, $argumentConfig, $help )` | parst die Kommandozeile; gibt `--help` aus und beendet; wendet `-s`, `-l`, `-d`, `-v`, `-q` an |
| 4 | `$script->initialize()` | lädt Siteaccess, Erweiterungen, Sitzung und Datenbank |
| 5 | Ihre Arbeit | |
| 6 | `$script->shutdown( $exitCode, $exitText )` | schreibt auf Wunsch den Debug-Bericht und beendet |

Parsen Sie die Optionen vor `initialize()`, damit `--help` ohne Datenbank antwortet.

## Ein einfaches Skript

```php
#!/usr/bin/env php
<?php
/**
 * @description Counts the items of acmereport
 */
require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Counts the items of acmereport",
                                     'use-session' => false, 'use-modules' => false, 'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[status:]', '', array( 'status' => 'Only items with this status' ) );
$script->initialize();

$conds = $options['status'] ? array( 'status' => (string)$options['status'] ) : null;
$cli->output( 'Items: ' . eZPersistentObject::count( acmeReportItem::definition(), $conds ) );

$script->shutdown( 0 );
```

Führen Sie es aus dem Wurzelverzeichnis der Installation aus: `php extension/acmereport/bin/php/count.php --status=open`
oder über die Konsole als `ext:acmereport:count`.

## Die Einstellungen von eZScript::instance()

| Schlüssel | Bedeutung |
|---|---|
| `description` | der Text von `--help` |
| `use-session` | eine Sitzung starten (die meisten Skripte brauchen keine) |
| `use-modules` | das Modulsystem laden (nötig, um Views auszuführen oder Richtlinien von Modulen zu prüfen) |
| `use-extensions` | die aktiven Erweiterungen und ihre Einstellungen laden (fast immer `true`) |
| `site-access` | ein fester Siteaccess statt des Standards |

## Ausgabe mit eZCLI

| Aufruf | Schreibt |
|---|---|
| `$cli->output( $text )` | eine Zeile auf die Standardausgabe; nichts mit `-q` |
| `$cli->notice( $text )`, `warning( $text )`, `error( $text )` | markierte Zeilen |
| `$cli->stylize( 'emphasize', $text )` | Text mit einem Terminal-Stil; einfach, wenn Farben ausgeschaltet sind |

## Code mit dem Kernel ausführen: ezexec.php

Um eine PHP-Datei einmal mit gebootetem Kernel (Einstellungen, Erweiterungen, Datenbank) auszuführen, für eine Diagnose
oder eine einmalige Datenkorrektur, übergeben Sie sie an `bin/php/ezexec.php`:

```bash
php bin/php/ezexec.php path/to/your-script.php
php bin/php/ezexec.php tests/bin/bootsmoke.php      # how CI boots the kernel on PHP 8.0
```

Die Datei läuft mit verfügbaren Klassen und Einstellungen des Kernels; `eZDB::instance()` verbindet sich mit der
Datenbank der Site. Halten Sie solche Dateien außerhalb der Reichweite des Web-Wurzelverzeichnisses und aus den
Releases Ihrer Erweiterung heraus, und geben Sie ihnen einen Namen, der sagt, was sie tun. Alles, was Sie zweimal
ausführen, gehört in einen richtigen Befehl.

## Ein einfaches Skript in eine Klasse überführen

1. Legen Sie `extension/<ext>/classes/runnable/commands/<name>.php` mit einer Klasse an, die
   `Exponential\Runnable\Command` in `Exponential\Command\Extension\<Ext>` erweitert.
2. Verschieben Sie den Code in `run()`; ersetzen Sie `eZScript::instance()` durch `$this->script()`, die drei
   Startschritte durch `$this->startup()`, `$cli->output()` durch `$this->output()`, `$script->shutdown()` durch
   `$this->shutdown()`.
3. Schreiben Sie Klassennamen voll qualifiziert (`\eZCLI`), weil die Klasse in einem Namespace liegt.
4. Reduzieren Sie die alte Datei auf `require_once 'autoload.php';` und einen Aufruf `main( __FILE__ )`.
5. Vergleichen Sie `--help` und die Exit-Codes vorher und nachher; der Kernel hat seine eigenen Umstellungen so
   nachgewiesen ([Befehle, Cronjob-Teile und Modul-Views als Klassen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)).

## Verwandte Seiten

- [Referenz zu eZScript und eZCLI](../erweiterungsentwicklung/teil-8-referenz/ezscript-und-ezcli.md)
- [CLI-Skripte mit eZScript (Anwendungsentwickler)](../ersteller/anwendungsentwickler/cli-skripte.md)
- [CLI-Skripte und Konsolenbefehle](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
