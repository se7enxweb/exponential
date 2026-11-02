---
title: exp:expstaticpublish
audience: developer, admin
category: examples
summary: "Den statischen Build eines Ziels veröffentlichen (statisches Veröffentlichen): im Git-Repository des Ziels committen, als static/<ziel>/<n> taggen und pushen, nie erzwungen."
keywords: Konsole, Befehl, exp:expstaticpublish, expstaticpublish, console, command, CLI, expstaticpublish.php, exp:static:publish
type: generated
generated: exp:help:examples
---
# exp:expstaticpublish

Den statischen Build eines Ziels veröffentlichen (statisches Veröffentlichen): im Git-Repository des Ziels committen, als static/<ziel>/<n> taggen und pushen, nie erzwungen.

Skript `bin/php/expstaticpublish.php`, Code in `kernel/private/classes/commands/expstaticpublish.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticpublish --help
./console exp:static:publish --help
php bin/php/expstaticpublish.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Publish a static build to its git remote
Commits the build in the target's folder (var/<site>/static-publish/<target>/, a git repository of
its own: only the files of the build), tags the commit static/<target>/<n> and pushes the branch and
the tag to the target's Remote, signed in with its Credential (a token from Setup > Secrets, a deploy
key, or none). A push is never forced; a push that fails stays local and exp:static:push or the
cronjob part staticpublish tries again. Every publish is a row of exp_static_run.

./console exp:static:publish github-public --build
./console exp:static:publish github-public --nodes=2,57             build these nodes' pages, then publish
./console exp:static:publish github-public --message="New opening hours"
./console exp:static:publish github-public --no-push                commit and tag only
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--build` |  | Das Ziel zuerst bauen (wie exp:static:build); wird der Build nicht fertig, wird nichts committet |
| `--nodes` | erforderlich | Nur die Seiten dieser Knoten bauen (Knoten-IDs; eine Kommaliste oder wiederholt), in jedem Siteaccess des Ziels; der Rest des letzten Builds bleibt |
| `--message` | erforderlich | Die Commit-Nachricht (Standard: was veröffentlicht wurde, oder welche Veröffentlichung wiederhergestellt wird) |
| `--no-push` |  | Committen und taggen, aber nicht pushen (exp:static:push pusht später) |
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |
| `--login` | erforderlich | Run as this user (the administration starts its jobs so); recorded as who ran it |
| `--run-id` | erforderlich | The id of the run (date-time-6 hex digits) instead of a new one (the administration passes its job's id) |
| `--progress` | erforderlich | Also append the progress, one JSON object per line, to this file (the administration follows it) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticpublish.php`, `kernel/private/classes/commands/expstaticpublish.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
