# ezpm: der Paketmanager auf der Kommandozeile

Diese Seite ist für Administratoren, die Inhalte oder ganze Websites aus der Shell zwischen Installationen bewegen.
`ezpm.php` ist das Kommandozeilen-Frontend des Paketmanagers. Es legt Pakete (`.ezpkg`-Archive mit Inhalten, Klassen,
Templates und Dateien) an, füllt, exportiert, importiert, listet und installiert sie ohne Browser. Seit Juli 2026
(Entwicklung von Release 6.0.15, Commits `868f7a625c`, `688f48e93f`, `fc338f04b7`, `f090f202a1` und weitere) ist es über
die Exponential-Konsole erreichbar, zeigt, wie weit es gekommen ist, installiert große Pakete, ohne die Datenbank zu
sperren, und kann einen ganzen Inhaltsbaum exportieren.

Verwenden Sie es, wenn Sie eine Website oder einen Teil einer Website zwischen Installationen bewegen, ein Demo-Paket
bauen oder ein Paket auf einem Server installieren, auf dem der Browser in eine Zeitüberschreitung liefe.

## Ausführen

Alle Befehle beginnen im Wurzelverzeichnis des Projekts.

```bash
# list the commands the console knows; ezpm appears under its own namespace
php bin/php/console list ezpm

# the same script, called directly
php ezpm.php help
php ezpm.php help add
```

`php bin/php/console ezpm help add` gibt die Hilfe des Befehls `add` aus; `./console list ezpm` zeigt den einen Eintrag,
den die Konsole dafür hat (geprüft auf 6.0.15: `ezpm  Create, import, install, list and delete Exponential packages`).
`ezpm.php` lief bei der Prüfung, die diese Seite geschrieben hat, als Betriebssystembenutzer `root` ohne
`--allow-root-user`; andere Skripte fragen danach, siehe [die Konsole](../../../eng-us/bc/6.0/console.md).

Globale Optionen (aus `php ezpm.php help`):

| Option | Bedeutung |
|---|---|
| `-s`, `--siteaccess` | Zu verwendender Siteaccess. Ohne ihn wird der `DefaultAccess` aus `site.ini` verwendet (seit `9617bdcea4`). |
| `-q`, `--quiet` | Keine Ausgabe außer Fehlern. |
| `-d`, `--debug` | Debug-Ausgabe am Ende zeigen. |
| `-l`, `--login`, `-p`, `--password` | Für alle Operationen als dieser Benutzer anmelden. |
| `-r`, `--repos` | Repository, in dem nach Paketen gesucht wird. |
| `--db-type`, `--db-name`, `--db-user`, `--db-password`, `--db-host`, `--db-socket` | Die Datenbankverbindung überschreiben. |
| `-c`, `--colors` / `--no-colors` | ANSI-Farben ein (Standard) oder einfache Ausgabe. |
| `--logfiles` / `--no-logfiles` | Protokolldateien anlegen oder nicht (Standard: nicht). |

Befehle: `create`, `add`, `set`, `delete`, `export`, `import`, `install`, `list`, `info`, `help` und seit Oktober 2026
`convert`, `validate` und `check` für das JSON-Paketformat: `export --format=json`, `install --as-drafts`, siehe
[das JSON-Paketformat](../../../eng-us/bc/6.0/package-json-format.md).

## Einen Teil des Inhaltsbaums verpacken

Ein Paket mit einem Inhaltsbaum wird in drei Schritten gebaut: anlegen, hinzufügen, exportieren.

```bash
# 1. an empty package
php ezpm.php create mypackage 'My package summary' 1.0.1 install

# 2. add a node and everything under it (node id 2 = the content root)
php ezpm.php add mypackage ezcontentobject 2

# 3. write mypackage.ezpkg into a directory
php ezpm.php export mypackage -d var/tmp
```

`add ezcontentobject` nimmt eine Knoten-ID oder einen URL-Pfad als Ausgangspunkt und diese Optionen (alle in
`php ezpm.php help add`):

| Option | Wirkung |
|---|---|
| `--include-classes` / `--exclude-classes` | Die Inhaltsklassen ins Paket nehmen (Standard) oder weglassen. |
| `--include-templates` / `--exclude-templates` | Die Template-Überschreibungen ins Paket nehmen (Standard) oder weglassen. |
| `--node-main` / `--node-selected` | Nur die Hauptzuweisung des Knotens exportieren (Standard) oder alle ausgewählten Zuweisungen. |
| `--siteaccess=sa,sa2` | Siteaccesses, aus denen Templates gesammelt werden. |
| `--language=loc1,loc2` | Zu exportierende Sprachen (Standard: alle). |
| `--current-version` / `--all-versions` | Nur die aktuelle Version (Standard) oder jede Version. |
| `--minimal-template-set` | Nur die minimale Menge an Templates. |

`ezcontentsubtree` ist ein Alias von `ezcontentobject` (`6ec591c375`), der sich besser liest, wenn Sie „diesen Knoten und
seinen Teilbaum“ meinen:

```bash
php ezpm.php add mypackage ezcontentsubtree /content/my-page --exclude-classes --siteaccess=site
```

Während das Paket gebaut wird, nennt die Statuszeile die Phase: *Collecting subtree nodes*, *Fetching node objects*,
*Serializing content objects*. Eine fehlende Ausgangssprache oder ein fehlendes serialisiertes Attribut hält den Export
nicht mehr an (August, `752adad843`, `8af48c1e59`); das Element wird übersprungen.

## Ein Paket installieren

```bash
# install into the content root (node 2, the default)
php ezpm.php install mypackage

# install the content objects under another parent node
php ezpm.php install mypackage -d 61
php ezpm.php install mypackage --destination-node-id 61
```

Was der Installer seit Juli anders macht:

- **Ein Lauf zur Zeit.** `install` und `add` nehmen eine Sperre (eine Datei namens `ezpm.lock` im var-Verzeichnis). Ein
  zweiter Lauf, während der erste läuft, hält mit *Another ezpm process is currently running* an. Das verhinderte die
  Zeitüberschreitungen beim Warten auf Datenbanksperren, die zwei parallele Läufe verursachten.
- **Schnell gebaut.** Für `install` und `add` werden der Ansichts-Cache und der Template-Cache für den Lauf abgeschaltet
  und die Suchindizierung verzögert, weil aus ihm keine Seite ausgeliefert wird. Wenn er vorbei ist, bauen Sie den
  Suchindex mit `php bin/php/updatesearchindex.php` neu auf und leeren Sie die Caches
  (`php bin/php/ezcache.php --clear-all --allow-root-user`).
- **Commits in Teilschritten.** Der Handler für Inhaltsobjekte committet die Transaktion alle 50 Objekte, sodass ein
  Paket mit Zehntausenden von Objekten nicht für den ganzen Lauf Sperren hält. Jedes Objekt wird zuerst geprüft; ein
  fehlgeschlagenes Objekt wird im Fehler mit seiner Remote-ID genannt.
- **Fortschritt mit Schätzung.** Eine einzige Statuszeile zeigt den Befehl, einen Prozentsatz mit `current/total`, die
  verstrichene Zeit und das erwartete Ende. Sie berücksichtigt `--quiet`.
- **Keine Duplikate.** Installationselemente werden entdoppelt, sodass wiederholte Elemente nicht zweimal verarbeitet
  werden oder Versionen hinzufügen.
- **Sicher ohne Terminal.** Das zerstörende Ersetzen von Klassen wird übersprungen, wenn die Installation nicht
  interaktiv ist.
- **Abhängigkeiten zuerst.** Pakete, die im Paket unter `requires` aufgeführt sind, werden vor dem Paket selbst
  installiert (29. Juli, `cb57cdd026`), auch wenn das Paket nur importiert. Ein fehlendes benötigtes Paket ist eine
  Warnung; eines, das scheitert, hält die Installation an.

## Grenzen

- Die Sperre gilt pro Installation (`var/`), nicht pro Host.
- Verzögerte Indizierung bedeutet, dass Suchergebnisse erscheinen, nachdem Sie den Index neu aufgebaut haben.
- Installieren Sie zuerst in eine Kopie: Der Installer schreibt Inhalte.

## Verwandte Seiten

- [Das JSON-Paketformat](../../../eng-us/bc/6.0/package-json-format.md): `export --format=json`, `convert`, `validate`, `check`,
  `install --as-drafts`
- [Eine Paketinstallation, die große Pakete übersteht](paketinstallation-in-teilschritten.md) (der Installer im Browser, Juni 2026)
- [In ein Paket hineinsehen, es mit Ihrer Website vergleichen, einzelne Elemente importieren](../../../eng-us/features/6.0/package-compare-and-import.md), [Paketlizenzen und Versionen](paketlizenzen-und-versionen.md)
- [Kickstarter-CLI](kickstarter-cli.md) (installiert eine ganze Website aus `kickstart.ini`), [Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md), [Standardwerte einer sauberen Installation](standardwerte-einer-sauberen-installation.md)
- [Konsole](../../../eng-us/bc/6.0/console.md), [Verhaltensänderungen von Juli und August 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-07-08.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Juli 2026](../../../eng-us/history/2026/2026-07.md), [August 2026](../../../eng-us/history/2026/2026-08.md) (Korrekturen am Export)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
