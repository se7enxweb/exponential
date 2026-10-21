---
title: CLI-Skripte und Console-Befehle
audience: developer, app-creator
category: extension-development
order: 210
summary: Kommandozeilenwerkzeuge in einer Erweiterung - bin/php-Skripte, die die Console als ext:<extension>:<name> ausführt, eZScript und eZCLI, Exit-Codes, @description und die Form als Command-Klasse.
keywords: Kommandozeile, Console, Befehl, Skript, Exit-Code, cli, command line, console, bin/php, ext:, eZScript, getOptions, startup, initialize, shutdown, eZCLI, output, Command class, @description, allow-root-user, exit code
type: help
---
# CLI-Skripte und Console-Befehle

Teil 3, Kapitel 21. Administratoren und Bereitstellungen brauchen Befehle: dies importieren, das neu aufbauen, über
jenes berichten. Eine Erweiterung liefert Befehle als Skripte in `bin/php/` aus, und die Console findet sie
automatisch. Das Beispiel auf dieser Seite wurde ausgeführt.

## Wo die Console sie findet

Die Console (`bin/php/console`, meist als `./console` verlinkt) führt keine Liste; sie findet Skripte zur Laufzeit:

| Skript | Befehl |
|---|---|
| `bin/php/<name>.php` | `exp:<name>` |
| `extension/<ext>/bin/php/<name>.php` | `ext:<ext>:<name>` |
| `extension/<ext>/bin/shell/<name>.sh` | `ext:<ext>:sh:<name>` |

`extension/mysite_hello/bin/php/greet.php` ist also `./console ext:mysite_hello:greet`. `./console list ext` listet
jeden Befehl einer Erweiterung mit einer einzeiligen Beschreibung auf, die die Console aus einem Tag `@description` im
ersten Docblock des Skripts liest (oder, falls es fehlt, aus der `description`, die an `eZScript` übergeben wird).

## Ein einfaches Skript

`extension/mysite_hello/bin/php/greet.php`:

```php
#!/usr/bin/env php
<?php
/**
 * @description Greet someone from the command line (an example command of mysite_hello)
 */
require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Greet someone from the command line.\n",
                                     'use-session' => false,
                                     'use-modules' => false,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[name:][greeting:]', '',
                                array( 'name' => 'Who to greet (default: world)',
                                       'greeting' => 'The greeting word (default: Hello)' ) );
$script->initialize();

$name = $options['name'] ? $options['name'] : 'world';
$greeting = $options['greeting'] ? $options['greeting'] : 'Hello';
$cli->output( $greeting . ', ' . $name . '!' );

$script->shutdown( 0 );
```

Ausgeführt im Stammverzeichnis der Installation:

```bash
./console ext:mysite_hello:greet --name=Ada
php extension/mysite_hello/bin/php/greet.php --help
```

Der erste Befehl gibt `Hello, Ada!` aus. `--help` gibt die Standardoptionen aus (`-q`, `-s` für den Siteaccess, `-d`
für Debug-Ausgabe, `-v`, `--allow-root-user` ...), gefolgt von Ihren eigenen:

```text
Options:
  --name=VALUE      Who to greet (default: world)
  --greeting=VALUE  The greeting word (default: Hello)
```

## Die Schritte

| Aufruf | Bewirkt |
|---|---|
| `eZScript::instance( $settings )` | das Skriptobjekt; `use-extensions` lädt die Einstellungen der aktiven Erweiterungen, `use-modules` das Modulsystem, `use-session` eine Sitzung |
| `$script->startup()` | grundlegende Initialisierung |
| `$script->getOptions( $config, $arguments, $help )` | wertet Optionen der Form `--name=value` aus (`[name:]` nimmt einen Wert, `[flag]` nicht), zeigt `--help` an und beendet sich |
| `$script->initialize()` | Siteaccess, Datenbank, Erweiterungen |
| `$cli->output()`, `$cli->error()`, `$cli->warning()` | schreiben auf das Terminal (bei `-q` gibt `output()` nichts aus) |
| `$script->shutdown( $exitCode, $text )` | sauberes Beenden mit dem Code; `0` bedeutet Erfolg |

Verwenden Sie bei einem Fehler einen Exit-Code ungleich null, damit sich Skripte und Bereitstellungen darauf
verlassen können.

## Als Klasse

Seit 6.0.15 sind die eigenen Befehle des Kernels Klassen (`\Exponential\Runnable\Command`); das Skript besteht aus zwei
Zeilen. Eine Klasse lässt sich testen und teilt Code mit Views und Cronjob-Teilen:

```php
<?php
// extension/mysite_hello/classes/runnable/commands/greet.php
namespace Exponential\Command\Extension\MysiteHello;

class Greet extends \Exponential\Runnable\Command
{
    public function run()
    {
        $this->script( array( 'description' => "Greet someone from the command line.",
                              'use-session' => false, 'use-modules' => false, 'use-extensions' => true ) );
        $options = $this->startup( '[name:]', '', array( 'name' => 'Who to greet' ) );
        $this->output( 'Hello, ' . ( $options['name'] ? $options['name'] : 'world' ) . '!' );
        $this->shutdown( 0 );
    }
}
```

```php
#!/usr/bin/env php
<?php
require_once 'autoload.php';
\Exponential\Command\Extension\MysiteHello\Greet::main( __FILE__ );
```

Die Hilfsmethoden (`script()`, `startup()`, `output()`, `error()`, `isQuiet()`, `log()`, `shutdown()`) sind in
[Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
aufgeführt.

## Regeln für Befehle

- Beginnen Sie jedes Skript mit `#!/usr/bin/env php` und machen Sie es ausführbar; Bibliotheksdateien bekommen keine
  solche Zeile.
- Akzeptieren Sie `--dry-run` für alles, was Daten ändert, und zeigen Sie, was sich ändern würde.
- Geben Sie nie Passwörter oder Schlüssel aus; maskieren Sie sie.
- Arbeiten Sie bei großen Aufgaben in Stapeln mit einer Fortschrittszeile; beachten Sie `-q`.
- Führen Sie Befehle als Benutzer der Website aus; eine Datei, die als root angelegt wurde, ist danach für den
  Webserver möglicherweise nicht beschreibbar.

## Verwandte Seiten

- Zurück: [Cronjob-Teile](cronjob-teile.md)
- Weiter: [Ein Design in einer Erweiterung](design-in-einer-erweiterung.md)
- [Die Console](../../../bc/6.0/konsole.md)
- [eZScript und eZCLI](../teil-8-referenz/ezscript-und-ezcli.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
