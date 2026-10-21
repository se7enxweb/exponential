---
title: exp:ezcreatedeactivatedusergroup
audience: developer, admin
category: examples
summary: Legt die Benutzergruppe für deaktivierte Konten („Deaktivieren und verschieben“) schon vor der ersten Verwendung an.
keywords: Konsole, Befehl, exp:ezcreatedeactivatedusergroup, ezcreatedeactivatedusergroup, console, command, CLI, ezcreatedeactivatedusergroup.php
type: generated
generated: exp:help:examples
---
# exp:ezcreatedeactivatedusergroup

Legt die Benutzergruppe für deaktivierte Konten („Deaktivieren und verschieben“) schon vor der ersten Verwendung an.

Skript `bin/php/ezcreatedeactivatedusergroup.php`, Code in `kernel/private/classes/commands/ezcreatedeactivatedusergroup.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezcreatedeactivatedusergroup --help
php bin/php/ezcreatedeactivatedusergroup.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Create the user group for deactivated accounts (Deactivate and move) ahead of first use.
A dry run unless --commit is given. Prints the node id; never writes an INI file.

  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess>
  php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit [--name=...] [--section-id=N]
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--commit` |  | Die Gruppe anlegen (ohne die Option nur sagen, was getan würde) |
| `--name` | erforderlich | Der Name der Gruppe (Standard: "Deactivated users" in der Sprache des Siteaccess) |
| `--section-id` | erforderlich | Die Gruppe in diese Sektion legen (Standard: die Sektion des Wurzelknotens der Benutzer) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezcreatedeactivatedusergroup.php`, `kernel/private/classes/commands/ezcreatedeactivatedusergroup.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
