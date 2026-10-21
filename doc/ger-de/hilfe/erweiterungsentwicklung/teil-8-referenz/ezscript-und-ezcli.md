---
title: eZScript und eZCLI
audience: developer
category: extension-development
order: 140
summary: Die Referenz von eZScript (Instanzeinstellungen, startup, getOptions, initialize, iterate, shutdown) und eZCLI (output, notice, warning, error, stylize) für Kommandozeilenskripte und Konsolenbefehle einer Erweiterung.
keywords: Kommandozeile, Skripte, Konsolenbefehle, Optionen, Fortschrittsanzeige, Exit-Code, eZScript, eZCLI, instance, startup, getOptions, initialize, shutdown, iterate, resetIteration, isQuiet, output, notice, warning, error, stylize, command line
type: help
---
# eZScript und eZCLI

Teil 8, Referenz 14. `eZScript` (`kernel/classes/ezscript.php`) richtet den Kernel für ein Kommandozeilenskript ein;
`eZCLI` (`lib/ezutils/classes/ezcli.php`) schreibt ins Terminal. Ein vollständiges Skript und wie die Konsole es
findet, steht in [CLI-Skripte und Konsolenbefehle](../teil-3-erweiterungsentwickler/cli-skripte-und-console-befehle.md).

## eZScript

| Methode | Verwendung |
|---|---|
| `eZScript::instance( $settings = array() )` (statisch) | das Skriptobjekt |
| `startup()` | grundlegende Initialisierung; zuerst aufrufen |
| `getOptions( $config = '', $argumentConfig = '', $optionHelp = false, ... )` | Optionen und Argumente auswerten; behandelt `--help` |
| `initialize()` | Siteaccess, Datenbank, Erweiterungen; nach `getOptions()` aufrufen |
| `isQuiet()` | ob `-q` angegeben wurde |
| `resetIteration( $iterationMax = false, $startIndex = 0 )`, `iterate( $cli, $status, $text = false )` | eine Fortschrittsanzeige für Schleifen |
| `setUseDebugOutput( $useDebug )` | Debug-Ausgabe am Ende |
| `shutdown( $exitCode = false, $exitText = false )` | sauber mit einem Exit-Code beenden |

Die meistverwendeten Einstellungen von `instance()`:

| Schlüssel | Bedeutung |
|---|---|
| `description` | der Text, den `--help` anzeigt |
| `use-session` | eine Session starten (selten nötig) |
| `use-modules` | das Modulsystem laden |
| `use-extensions` | die Einstellungen der aktiven Erweiterungen lesen (fast immer `true`) |

Syntax der Optionen von `getOptions()`: `[name:]` nimmt einen Wert (`--name=Ada`), `[flag]` nicht, `[n:]` ist eine
Kurzoption. Der Rückgabewert ist ein Array, nach Optionsnamen geordnet, plus `arguments` für die
Positionsargumente.

## Eine Schleife mit Fortschrittsanzeige

```php
<?php
$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Re-index items.\n", 'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[limit:]', '', array( 'limit' => 'How many items (default: all)' ) );
$script->initialize();

$items = array( 1, 2, 3 );
$script->resetIteration( count( $items ) );
foreach ( $items as $item )
{
    $ok = true; // the work for $item
    $script->iterate( $cli, $ok, "Item $item" );
}
$script->shutdown( 0 );
```

`iterate()` gibt je Element einen `.` für Erfolg und eine `~` für einen Fehlschlag aus und bricht die Zeilen mit einem
laufenden Zähler um.

## eZCLI

| Methode | Schreibt |
|---|---|
| `eZCLI::instance()` (statisch) | das Terminalobjekt |
| `output( $string = false, $addEOL = true )` | normale Ausgabe (mit `-q` unterdrückt) |
| `notice( ... )`, `warning( ... )`, `error( ... )` | Meldungen dieser Art |
| `stylize( $styleName, $text )` | Text mit einem Stil, z. B. `$cli->stylize( 'success', 'Done' )` |
| `setUseStyles( $useStyles )` | Farben ein oder aus |

## Exit-Codes und Sicherheit

- Beenden Sie bei Erfolg mit `0` und bei einem Fehler mit einem Wert ungleich null, damit Cronjobs und Deployments
  reagieren können.
- Skripte verweigern die Ausführung als root, sofern nicht `--allow-root-user` angegeben ist; führen Sie sie als
  Benutzer des Webservers aus, damit die Dateien, die sie schreiben, den richtigen Eigentümer haben.
- Ein Skript, das Daten ändert, sollte einen Probelauf (`[dry-run]`) anbieten und sagen, was es tun würde.

## Verwandte Seiten

- Zurück: [eZModule und Modulergebnisse](ezmodule.md)
- Weiter: [eZFile, eZDir und Cluster-Datei-Handler](ezfile-ezdir-und-cluster-dateien.md)
- [Konsolenbefehle für Entwickler](konsolenbefehle-fuer-entwickler.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
