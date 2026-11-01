---
title: exp:expstaticrollback
audience: developer, admin
category: examples
summary: "Ein Ziel des statischen Veröffentlichens auf eine frühere Veröffentlichung zurücknehmen: ein neuer Commit mit den Dateien dieses Tags, getaggt und gepusht; der Verlauf bleibt."
keywords: Konsole, Befehl, exp:expstaticrollback, expstaticrollback, console, command, CLI, expstaticrollback.php, exp:static:rollback
type: generated
generated: exp:help:examples
---
# exp:expstaticrollback

Ein Ziel des statischen Veröffentlichens auf eine frühere Veröffentlichung zurücknehmen: ein neuer Commit mit den Dateien dieses Tags, getaggt und gepusht; der Verlauf bleibt.

Skript `bin/php/expstaticrollback.php`, Code in `kernel/private/classes/commands/expstaticrollback.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticrollback --help
./console exp:static:rollback --help
php bin/php/expstaticrollback.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Roll a static site back to an earlier publish
Names an earlier publish by its number or its tag static/<target>/<n> (exp:static:status lists
them) and lists what would change. With --apply it makes a new commit that holds the files of that
publish, tags it with the next number and pushes it: nothing is deleted from the history, no tag is
moved and the push is never forced, so a rollback can itself be rolled back.

./console exp:static:rollback github-public 4                 what would change
./console exp:static:rollback github-public 4 --apply
./console exp:static:rollback github-public static/github-public/4 --apply --no-push
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Den Commit des Zurücknehmens machen; ohne zeigt der Befehl nur, was sich ändern würde |
| `--message` | erforderlich | Die Commit-Nachricht (Standard: was veröffentlicht wurde, oder welche Veröffentlichung wiederhergestellt wird) |
| `--no-push` |  | Committen und taggen, aber nicht pushen (exp:static:push pusht später) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |
| `--login` | erforderlich | Run as this user (the administration starts its jobs so); recorded as who ran it |
| `--run-id` | erforderlich | The id of the run (date-time-6 hex digits) instead of a new one (the administration passes its job's id) |
| `--progress` | erforderlich | Also append the progress, one JSON object per line, to this file (the administration follows it) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target][to]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticrollback.php`, `kernel/private/classes/commands/expstaticrollback.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
