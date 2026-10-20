---
title: exp:ezgeneratetranslationcache
audience: developer, admin
category: examples
summary: Generate translation cache files (--ts-list limits it to the given translations).
keywords: Konsole, Befehl, exp:ezgeneratetranslationcache, ezgeneratetranslationcache, console, command, CLI, ezgeneratetranslationcache.php
type: generated
generated: exp:help:examples
---
# exp:ezgeneratetranslationcache

Generate translation cache files (--ts-list limits it to the given translations).

Skript `bin/php/ezgeneratetranslationcache.php`, Code in `kernel/private/classes/commands/ezgeneratetranslationcache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezgeneratetranslationcache --help
php bin/php/ezgeneratetranslationcache.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--ts-list` | erforderlich | A list of translations to generate caches for, for example 'rus-RU nor-NO' By default caches for all translations will be generated |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezgeneratetranslationcache.php`, `kernel/private/classes/commands/ezgeneratetranslationcache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
