---
title: exp:expmigrationrecordstart
audience: developer, admin
category: examples
summary: "Startet eine Aufzeichnung von Änderungen: die eigenen Änderungen eines Benutzers (eine Sitzungsaufzeichnung) oder jede Änderung der Website (--site-wide), festgehalten als Schritte einer YAML-Migration; ein Probelauf ohne --apply."
keywords: Konsole, Befehl, exp:expmigrationrecordstart, expmigrationrecordstart, console, command, CLI, expmigrationrecordstart.php, exp:migration:record:start
type: generated
generated: exp:help:examples
---
# exp:expmigrationrecordstart

Startet eine Aufzeichnung von Änderungen: die eigenen Änderungen eines Benutzers (eine Sitzungsaufzeichnung) oder jede Änderung der Website (--site-wide), festgehalten als Schritte einer YAML-Migration; ein Probelauf ohne --apply.

Skript `bin/php/expmigrationrecordstart.php`, Code in `kernel/private/classes/commands/expmigrationrecordstart.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationrecordstart --help
./console exp:migration:record:start --help
php bin/php/expmigrationrecordstart.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Start a recording of changes
From now until exp:migration:record:stop, what is changed is recorded at the kernel's operation level
(content published, moved, hidden, removed or translated, classes stored, roles, policies and their
assignments, sections, users and groups, URL aliases, settings written, workflow triggers), as the
steps of a YAML migration with remote ids and identifiers instead of numeric ids. A session recording
keeps the changes of one user (--user, by login; in the administration: the signed-in user), made
anywhere: the administration, the website sidebar, or a command run as that user. --site-wide opens
the site-wide window instead: every change of every user and of the console. One session recording
per user and one site-wide window at a time. The account of a user (login, e-mail, password) and the
fields of user objects are never recorded. --for=<minutes> or --until=<time> gives the recording a
window: it stops by itself when the window ends (at most 31 days). Without --apply it says what it
would start.

./console exp:migration:record:start --label="news section" --apply
./console exp:migration:record:start --user=editor --apply
./console exp:migration:record:start --site-wide --label="release 2.4 setup" --apply
./console exp:migration:record:start --site-wide --for=120 --label="editors' afternoon" --apply
./console exp:migration:record:start --site-wide --until="2026-11-20 18:00" --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (ohne die Option ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, angenommen für Skripte, die ihn übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--site-wide` |  | Die websiteweite Aufzeichnung: jede Änderung jedes Benutzers und der Konsole (ohne: die eigenen Änderungen eines Benutzers) |
| `--label` | erforderlich | Die Bezeichnung des Sicherungspunkts oder der Aufzeichnung (ein Sicherungspunkt ohne eine: "manual caption" mit der Uhrzeit) |
| `--user` | erforderlich | Der Benutzer, nach Login, dessen eigene Änderungen eine Sitzungsaufzeichnung festhält (Standard: [RunSettings] RunAsUser der migration.ini) |
| `--for` | erforderlich | A window of this many minutes: the recording stops by itself when it ends |
| `--until` | erforderlich | A window until this time (2026-11-20 18:00, the server's time zone): the recording stops by itself then |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationrecordstart.php`, `kernel/private/classes/commands/expmigrationrecordstart.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
