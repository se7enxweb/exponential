# Spezifikation: Befehle, Cronjob-Teile und Modul-Views als Klassen

Diese Seite ist die Referenz für die ausführbaren Klassen: Seit 2026-10-02 liegt der Code jedes Kernel-Befehls, jedes
Cronjob-Teils und jeder Modul-View in einer Klasse, und die alten Dateien bleiben als schlanke Einstiegspunkte unter
denselben Pfaden erhalten. Lesen Sie sie, wenn Sie den Code hinter einem Skript oder einer View suchen, einen davon ersetzen
oder darauf hören möchten, ohne den Kernel zu bearbeiten, oder einen eigenen schreiben. Nichts, was ein Skript oder eine View
benennt, ändert sich. Die vollständige Anleitung mit Vorher-Nachher-Beispielen und einer Beschreibung, wie Sie einen
eigenen schreiben, ist [CLI-, Cronjob- und View-Abstraktionen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md).

## Kurz gesagt

- `bin/php/<name>.php`, `cronjobs/<name>.php` und `kernel/<module>/<view>.php` behalten ihre Pfade; der Code liegt in
  `kernel/private/classes/{commands,cronjobs,views}/`.
- Ersetzen Sie ein Runnable mit `site.ini [RunnableSettings] Implementation[<class>]=<subclass>`.
- Hören Sie auf eines mit `site.ini [RunnableSettings] Listeners[]=<event>@<callback>`.
- Erzeugen Sie nach dem Hinzufügen einer Klasse die Autoloads neu.

## Hierarchie

```
Exponential\Runnable\Runnable
 +- Exponential\Runnable\Command        run()                  command line script
 +- Exponential\Runnable\CronjobPart    run( array $scope )    cronjob part
 +- Exponential\Runnable\ModuleView     run( array $scope )    module view
```

| Art | Einstiegspunkt (unveränderter Pfad) | Klasse | Klassendatei |
|---|---|---|---|
| Befehl | `bin/php/<name>.php` | `Exponential\Command\Kernel\<Name>` | `kernel/private/classes/commands/<name>.php` |
| Cronjob-Teil | `cronjobs/<name>.php` | `Exponential\Cronjob\Kernel\<Name>` | `kernel/private/classes/cronjobs/<name>.php` |
| Modul-View | `kernel/<module>/<view>.php` | `Exponential\View\Kernel\<Module>\<View>` | `kernel/private/classes/views/<module>/<view>.php` |
| Befehl, Teil, View einer Extension | die Datei der Extension | `Exponential\<Kind>\Extension\<Ext>\...` | `extension/<ext>/classes/runnable/{commands,cronjobs,views}/` |

### Einstiegspunkte

Ein Einstiegspunkt behält seinen Pfad, seine Shebang-Zeile und seinen Header und macht dann einen einzigen Aufruf:

| Art | Aufruf |
|---|---|
| Befehl | `<Name>::main( __FILE__ )` |
| Cronjob-Teil | `return <Name>::main( __FILE__, get_defined_vars() )`, damit der Teil die Variablen liest, die der Runner setzt |
| Modul-View | `return <View>::main( __FILE__, get_defined_vars() )`, damit die View die Variablen liest, die der Modul-Runner setzt (etwa `$Params`) |

`ViewList` in `module.php` bleibt unberührt.

### Anzahlen und Sonderfälle

- Alle 198 Views der 22 Kernel-Module wurden verschoben (Stand 2026-10-02). Spätere Arbeiten kamen hinzu: Bei HEAD
  enthält `kernel/private/classes/views/` 210 Dateien in 24 Modulverzeichnissen, `commands/` 70 und `cronjobs/` 22.
  Zählen Sie selbst nach:

  ```bash
  find kernel/private/classes/views -name "*.php" | wc -l
  ```

- Eine View, die nach einem reservierten Wort von PHP benannt ist, erhält das Suffix `View`, zum Beispiel `Section\ListView`.
- Funktionen, die eine View hinter `function_exists()` deklariert hatte, wanderten in den globalen Namespace, damit
  String-Callbacks weiter funktionieren.
- Als dies geschrieben wurde, zählte die Übersicht der Erweiterungspunkte auf dieser Installation 469 Punkte zur
  Neuimplementierung (66 Kernel-Befehle, 20 Cronjob-Teile, 199 Views und die eigenen der Extensions). Zählen Sie unter **Setup > RAD** (`/setup/rad`) neu.
- Der Router des eingebauten Webservers, `bin/php/velocity-router.php`, ruft
  `Exponential\Command\Kernel\VelocityRouter::route()` auf. Die Klasse entscheidet (beantwortet, Datei vom Server gesendet oder
  Front-Controller ausführen), und die Datei handelt nach der Antwort.

## Gemeinsame Hilfsmethoden

`Command` stellt `cli()`, `script( $settings )`, `options( $config, $argumentConfig, $help )`, `startup()` (Start des
Skripts, Auswertung der Optionen und Initialisierung in einem Aufruf), `start( $settings, ... )`, `output()`,
`error()`, `warning()`, `isQuiet()`, `log()` und `shutdown()` bereit. Sie reichen Argumente unverändert an `eZScript` und
`eZCLI` weiter, sodass sich `--help`, `-q`, `--siteaccess` und die anderen Standardoptionen wie bisher verhalten.

Arbeit, die eine View, ein Befehl und ein Cronjob-Teil gemeinsam nutzen, liegt in Services in `kernel/private/classes/services/`:

| Service | Methoden |
|---|---|
| `Exponential\Service\Trash` | `canEmpty()`, `purgeObjects()`, `emptyArchived()`, `purgeInBatches()` |
| `SessionGarbageCollector` | `collect()` |
| `DraftsCleanup` | `cleanup()` (verwendet von `old_drafts_cleanup` und `internal_drafts_cleanup`) |
| `TrashRecord`, `TrashList` | Papierkorb-Einträge und -Listen |

## Erweiterungspunkte

| Punkt | Definition |
|---|---|
| Ereignisse | `Runnable::main()` führt `run()` zwischen zwei `ezpEvent`-Ereignissen aus. `runnable/<kind>/before` wird mit dem Runnable, der Klasse und ihren Variablen benachrichtigt. `runnable/<kind>/after` filtert das Ergebnis, sodass ein Listener ändern kann, was eine View zurückgibt. `<kind>` ist `command`, `cronjob` oder `view`. Ohne Listener ist das Ergebnis genau das, was `run()` zurückgibt |
| Neuimplementierung | `site.ini [RunnableSettings] Implementation[<runnable class>]=<subclass>`. Die Unterklasse muss die Klasse erweitern, die sie ersetzt; alles andere wird ignoriert |
| Listener | `site.ini [RunnableSettings] Listeners[]=<event>@<callback>` |
| Übersicht | `setup/rad` und `setup/radsurvey` führen jedes Runnable als Erweiterungspunkt auf |

Ein Befehl liest `RunnableSettings` nur aus `settings/site.ini` und `settings/override`.

### Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `site.ini` | `RunnableSettings` | `Implementation[<runnable class>]` | keiner | Installation (`settings/override` für Befehle) |
| `site.ini` | `RunnableSettings` | `Listeners[]` | keiner | Installation (`settings/override` für Befehle) |

## Beschreibungen in der Konsole

`bin/php/console list` und `help` folgen einem Einstiegspunkt zu der Klasse, die den Code enthält, und lesen dort den
Tag `@description`; vorher zeigten 42 Befehle „(no description)“ an. Ein Cronjob-Teil, der an mehrere Skripte gebunden ist,
zeigt eine Zeile. `runcronjobs.php`, `ezpm.php`, `ezpgenerateautoloads.php` und `ezasynchronouspublisher.php` sind
ebenfalls Befehle; der Autoload-Generator lädt seine Klasse selbst, da er läuft, bevor der Autoloader existiert.

## Copyright und Header

Jeder Copyright-Vermerk nennt zuerst „1998 - 2026 7x & Exponential Foundation“, über dem Vermerk des ursprünglichen
Rechteinhabers, der erhalten bleibt, wie es die GPL verlangt. Einstiegspunktdateien tragen einen Header von 7x und der
Exponential Foundation, wobei die Beschreibung erhalten bleibt; der ursprüngliche Header ist in die Klassendatei gewandert.

## Kompatibilität

Das Verhalten ist unverändert. Code, der eine View-Datei per `include` einbindet, einen Befehl über seinen Pfad aufruft oder
eine View in `module.php` benennt, funktioniert weiter. Code, der die alten Dateien nach ihrer Logik durchsucht hat, muss
jetzt in `kernel/private/classes/` suchen.

Erzeugen Sie nach dem Hinzufügen einer Klasse die Kernel-Autoloads neu und schließen Sie dabei jedes Worktree-Verzeichnis aus:

```bash
php bin/php/ezpgenerateautoloads.php -k --exclude='<worktree directory pattern>'
```

oder verwenden Sie `./console exp:velocity deploy --kernel`, das dasselbe als Teil einer Bereitstellung erledigt.

Tests: Unit-Tests für die Basisklassen und jeden verschobenen Einstiegspunkt.

## Verwandte Seiten

- [CLI-, Cronjob- und View-Abstraktionen](../../bc/6.0/cli-cronjob-und-view-abstraktionen.md), [RAD-Erweiterungspunkte](../../bc/6.0/rad-erweiterungspunkte.md), [Upgrade-Checkliste vom 1. bis 2. Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)
- [Cronjob-Konsole](../../funktionen/6.0/cronjobs-in-der-konsole.md), [Inhalts-Jobs](../../funktionen/6.0/inhaltsauftraege.md), [Exp-Debug-Leiste](../../funktionen/6.0/exp-debug-leiste.md)
- [Audit-Ereignismodell](audit-ereignismodell.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), [Chronik Oktober 2026](../../geschichte/2026/2026-10.md)
