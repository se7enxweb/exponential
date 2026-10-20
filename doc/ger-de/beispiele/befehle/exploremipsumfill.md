---
title: exp:exploremipsumfill
audience: developer, admin
category: examples
summary: Fill a translation of objects in a test language (Lorem Ipsum, qlo-ZZ) with lorem ipsum text.
keywords: Konsole, Befehl, exp:exploremipsumfill, exploremipsumfill, console, command, CLI, exploremipsumfill.php, exp:loremipsum
type: generated
generated: exp:help:examples
---
# exp:exploremipsumfill

Fill a translation of objects in a test language (Lorem Ipsum, qlo-ZZ) with lorem ipsum text.

Skript `bin/php/exploremipsumfill.php`, Code in `kernel/private/classes/commands/exploremipsumfill.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exploremipsumfill --help
./console exp:loremipsum --help
php bin/php/exploremipsumfill.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Fills the translation of objects in a test language with lorem ipsum text.

Text lines, text blocks, rich text and keywords get lorem ipsum; every other attribute keeps
the value of the language it is copied from. One published version per object. The language
must be a test language (TestLanguage=true in its share/locale file) and already added on
Setup > Languages; the command never adds one.

  php bin/php/exploremipsumfill.php <object id>[,...] [--dry-run]
  php bin/php/exploremipsumfill.php --subtree=<node id> [--limit=50] [--dry-run]
  ./console exp:loremipsum <object id> --language=qlo-ZZ --from=eng-GB

Exit code: 0 done (or a dry run), 1 refused or failed, 2 usage error.
Guide: doc/eng-us/guides/test-language-lorem-ipsum.md
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--language` | erforderlich | the test language to fill (default:  |
| `--from` | erforderlich |  |
| `--subtree` | erforderlich |  |
| `--limit` | erforderlich |  |
| `--dry-run` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[OBJECTS]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exploremipsumfill.php`, `kernel/private/classes/commands/exploremipsumfill.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
