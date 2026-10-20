---
title: exp:explovestack
audience: developer, admin
category: examples
summary: Prüft eine von LoveStack kommende Installation (nur lesend) und trägt ihre Datenbank als die von Exponential ein (migrate).
keywords: Konsole, Befehl, exp:explovestack, explovestack, console, command, CLI, explovestack.php, exp:lovestack
type: generated
generated: exp:help:examples
---
# exp:explovestack

Prüft eine von LoveStack kommende Installation (nur lesend) und trägt ihre Datenbank als die von Exponential ein (migrate).

Skript `bin/php/explovestack.php`, Code in `kernel/private/classes/commands/explovestack.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:explovestack --help
./console exp:lovestack --help
php bin/php/explovestack.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Sites coming from LoveStack (mugoweb/ezpublish-legacy)

check    lists what this installation and its database bring along from LoveStack:
         the database version row, LoveStack's db-schema-version row, a unique
         index on ezuser.login, data_text NOT NULL, the legacy_2018 extension,
         LoveStack-only settings in settings/override and the siteaccesses, and
         the LoveStack package in composer.json. Reads only, writes nothing.
migrate  records a LoveStack database as Exponential's: the version row
         'lovestack' becomes this release's version, and LoveStack's schema
         version is kept as ezsite_data lovestack-schema-version. Then it
         prints the settings to rename or remove. Never changes content or
         settings. A dry run unless --apply is given; running it again
         changes nothing. Refuses a database that does not come from LoveStack.

./console exp:lovestack check
./console exp:lovestack check --json
./console exp:lovestack migrate              (dry run: prints what it would write)
./console exp:lovestack migrate --apply

Exit code: check 0 nothing to do, 1 something needs attention (status found);
migrate 0 done or nothing to do, 1 refused; 2 usage error.
Guide: doc/eng-us/guides/upgrading.md (Coming from LoveStack)
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Ein JSON-Objekt statt des Textes |
| `--dry-run` |  | migrate: ausgeben, was geschrieben würde, nichts schreiben (der Standard) |
| `--apply` |  | migrate: die zwei Zeilen in ezsite_data schreiben |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[ACTION]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/explovestack.php`, `kernel/private/classes/commands/explovestack.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
