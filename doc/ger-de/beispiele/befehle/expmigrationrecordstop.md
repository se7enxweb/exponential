---
title: exp:expmigrationrecordstop
audience: developer, admin
category: examples
summary: "Beendet eine Aufzeichnung von Änderungen: die genannte, die eigene eines Benutzers (--user) oder das websiteweite Fenster (--site-wide); ihre Ereignisse bleiben für exp:migration:generate --from-recording; ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expmigrationrecordstop, expmigrationrecordstop, console, command, CLI, expmigrationrecordstop.php, exp:migration:record:stop
type: generated
generated: exp:help:examples
---
# exp:expmigrationrecordstop

Beendet eine Aufzeichnung von Änderungen: die genannte, die eigene eines Benutzers (--user) oder das websiteweite Fenster (--site-wide); ihre Ereignisse bleiben für exp:migration:generate --from-recording; ein Probelauf ohne --apply.

Skript `bin/php/expmigrationrecordstop.php`, Code in `kernel/private/classes/commands/expmigrationrecordstop.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationrecordstop --help
./console exp:migration:record:stop --help
php bin/php/expmigrationrecordstop.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Stop a recording of changes
Stops the recording named by its id, or else the session recording of --user (default: [RunSettings]
RunAsUser), or with --site-wide the site-wide window. Nothing more is recorded; what was recorded stays
until exp:migration:record:discard, for exp:migration:record:show and exp:migration:generate
--from-recording. Without --apply it says what it would stop.

./console exp:migration:record:stop --apply
./console exp:migration:record:stop --site-wide --apply
./console exp:migration:record:stop 20261101-120000-a1b2c3 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--site-wide` |  | Die websiteweite Aufzeichnung: jede Änderung jedes Benutzers und der Konsole (ohne: die eigenen Änderungen eines Benutzers) |
| `--user` | erforderlich | Der Benutzer, nach Login, dessen eigene Änderungen eine Sitzungsaufzeichnung festhält (Standard: [RunSettings] RunAsUser der migration.ini) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[recording]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationrecordstop.php`, `kernel/private/classes/commands/expmigrationrecordstop.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
