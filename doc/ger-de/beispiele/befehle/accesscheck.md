---
title: exp:accesscheck
audience: developer, admin
category: examples
summary: Prüft, was ein Benutzer mit einem Knoten oder Objekt tun darf (content/read, edit, ...) und welche Einschränkung es verweigert.
keywords: Konsole, Befehl, exp:accesscheck, accesscheck, console, command, CLI, accesscheck.php, exp:access:check
type: generated
generated: exp:help:examples
---
# exp:accesscheck

Prüft, was ein Benutzer mit einem Knoten oder Objekt tun darf (content/read, edit, ...) und welche Einschränkung es verweigert.

Skript `bin/php/accesscheck.php`, Code in `kernel/private/classes/commands/accesscheck.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:accesscheck --help
./console exp:access:check --help
php bin/php/accesscheck.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Access check for a user
Whether a user may read, edit, remove ... a node or an object, decided as the views decide
it, and when not, the policies and the limitation that refused, those of extensions
included. Nobody signs in and nothing is changed.

./console exp:access:check --user=editor --node=2
./console exp:access:check --user=14 --object=57 --function=edit --language=eng-GB
./console exp:access:check --user=anonymous --node=2 --json

Functions:
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--user` | erforderlich | Der Benutzer: seine Inhaltsobjekt-ID oder sein Login; "anonymous" für den anonymen Benutzer |
| `--node` | erforderlich | Die zu prüfende Knoten-ID |
| `--object` | erforderlich | Die zu prüfende Objekt-ID (statt eines Knotens) |
| `--function` | erforderlich | Die content-Funktion (Standard read) |
| `--language` | erforderlich | Ein Sprachcode, für edit und translate |
| `--json` |  | Ein JSON-Objekt statt des Textes |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/accesscheck.php`, `kernel/private/classes/commands/accesscheck.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
