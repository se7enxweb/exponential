---
title: exp:ezconvert2isbn13
audience: developer, admin
category: examples
summary: Wandelt ISBN-10-Nummern in ISBN-13 um.
keywords: Konsole, Befehl, exp:ezconvert2isbn13, ezconvert2isbn13, console, command, CLI, ezconvert2isbn13.php
type: generated
generated: exp:help:examples
---
# exp:ezconvert2isbn13

Wandelt ISBN-10-Nummern in ISBN-13 um.

Skript `bin/php/ezconvert2isbn13.php`, Code in `kernel/private/classes/commands/ezconvert2isbn13.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezconvert2isbn13 --help
php bin/php/ezconvert2isbn13.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--class-id` | erforderlich | Die Klassen-ID für das ISBN-Attribut. |
| `--attribute-id` | erforderlich | Die Attribut-ID des umzuwandelnden ISBN-Attributs. |
| `--all-classes` |  | Wandelt alle ISBN-Attribute in allen Inhaltsklassen um. |
| `-f` |  | Kurzer Alias für force. |
| `--force` |  | Wandelt alle Attribute um, auch wenn die Klasse auf ISBN gesetzt ist. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezconvert2isbn13.php`, `kernel/private/classes/commands/ezconvert2isbn13.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
