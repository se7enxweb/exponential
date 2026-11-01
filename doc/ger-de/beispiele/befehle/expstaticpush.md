---
title: exp:expstaticpush
audience: developer, admin
category: examples
summary: Die Ziele des statischen Veröffentlichens erneut pushen, deren Push gescheitert ist (Läufe mit dem Push-Zustand pending), nie erzwungen.
keywords: Konsole, Befehl, exp:expstaticpush, expstaticpush, console, command, CLI, expstaticpush.php, exp:static:push
type: generated
generated: exp:help:examples
---
# exp:expstaticpush

Die Ziele des statischen Veröffentlichens erneut pushen, deren Push gescheitert ist (Läufe mit dem Push-Zustand pending), nie erzwungen.

Skript `bin/php/expstaticpush.php`, Code in `kernel/private/classes/commands/expstaticpush.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticpush --help
./console exp:static:push --help
php bin/php/expstaticpush.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Push waiting static publishes again
A publish whose push failed (the remote was not reachable, the credential was missing) keeps its
commit and tag in the target's folder with the push state pending. This command pushes those
targets again, the branch with every commit and the tags, never forced. The cronjob part
staticpublish does the same.

./console exp:static:push                      every target with a waiting push
./console exp:static:push github-public
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticpush.php`, `kernel/private/classes/commands/expstaticpush.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
