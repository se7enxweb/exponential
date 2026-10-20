---
title: exp:addexphelpreadpolicy
audience: developer, admin
category: examples
summary: Grant exphelp/read (the help system) to the Editor role or the roles named (--dry-run lists them first).
keywords: Konsole, Befehl, exp:addexphelpreadpolicy, addexphelpreadpolicy, console, command, CLI, addexphelpreadpolicy.php, exp:help-grant
type: generated
generated: exp:help:examples
---
# exp:addexphelpreadpolicy

Grant exphelp/read (the help system) to the Editor role or the roles named (--dry-run lists them first).

Skript `bin/php/addexphelpreadpolicy.php`, Code in `kernel/private/classes/commands/addexphelpreadpolicy.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:addexphelpreadpolicy --help
./console exp:help-grant --help
php bin/php/addexphelpreadpolicy.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Grant the policy exphelp/read, reading the help system, to the Editor role or to the roles
named with --role, so editors see the Help control and the help pages. New installations give it
to the Editor role; administrators have it through their */* policy. Roles that have it already
are left alone; the script can run again.

Example: php bin/php/addexphelpreadpolicy.php --dry-run
         php bin/php/addexphelpreadpolicy.php --role=Editor --role=5
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | List what would be granted, change nothing |
| `--role:*` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addexphelpreadpolicy.php`, `kernel/private/classes/commands/addexphelpreadpolicy.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
