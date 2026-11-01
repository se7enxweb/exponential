---
title: exp:expmigrationrecordshow
audience: developer, admin
category: examples
summary: "Zeigt eine Aufzeichnung von Änderungen: jedes erfasste Ereignis als der Migrationsschritt, für den es steht, oder mit --plan die Schritte, die eine Migration daraus hätte."
keywords: Konsole, Befehl, exp:expmigrationrecordshow, expmigrationrecordshow, console, command, CLI, expmigrationrecordshow.php, exp:migration:record:show
type: generated
generated: exp:help:examples
---
# exp:expmigrationrecordshow

Zeigt eine Aufzeichnung von Änderungen: jedes erfasste Ereignis als der Migrationsschritt, für den es steht, oder mit --plan die Schritte, die eine Migration daraus hätte.

Skript `bin/php/expmigrationrecordshow.php`, Code in `kernel/private/classes/commands/expmigrationrecordshow.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expmigrationrecordshow --help
./console exp:migration:record:show --help
php bin/php/expmigrationrecordshow.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Show a recording of changes
The recording and every event it captured, in order: when, the audit event (content.object.publish ...),
the migration step it stands for (type/mode) and what it is about in stable names (remote ids,
identifiers), with what was left out on purpose (personal data, secrets). --format=json gives the
events in full (exp-migration-recording-event/1). --plan lists the steps a migration of the recording
would have instead: the events folded per thing changed. Changes nothing.

./console exp:migration:record:show 20261101-120000-a1b2c3
./console exp:migration:record:show 20261101-120000-a1b2c3 --plan
./console exp:migration:record:show 20261101-120000-a1b2c3 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-migration/1) |
| `--plan` |  | Die Schritte, die eine Migration der Aufzeichnung hätte (ihre Ereignisse je geänderter Sache zusammengefasst), statt jedes Ereignisses |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[recording]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expmigrationrecordshow.php`, `kernel/private/classes/commands/expmigrationrecordshow.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
