---
title: exp:ezapplytemplate
audience: developer, admin
category: examples
summary: Apply the template blocks found in files and write the files back (exit code 0, 1 or 2).
keywords: Konsole, Befehl, exp:ezapplytemplate, ezapplytemplate, console, command, CLI, ezapplytemplate.php
type: generated
generated: exp:help:examples
---
# exp:ezapplytemplate

Apply the template blocks found in files and write the files back (exit code 0, 1 or 2).

Skript `bin/php/ezapplytemplate.php`, Code in `kernel/private/classes/commands/ezapplytemplate.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezapplytemplate --help
php bin/php/ezapplytemplate.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-a` |  |  |
| `--all` |  | Go trough all files defined in codetemplate.ini |
| `--check-only` |  | Will only check if the files will be changed or have errors |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[file]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezapplytemplate.php`, `kernel/private/classes/commands/ezapplytemplate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
