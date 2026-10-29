---
title: exp:adduserapproval
audience: developer, admin
category: examples
summary: "Set up the approval of new accounts in a user group (default: new Editors wait for the Administrator users): the workflow event on content/publish before and the role that lets the requesters create such accounts (--dry-run lists it first); --mode=section sets up the optional ..."
keywords: Konsole, Befehl, exp:adduserapproval, adduserapproval, console, command, CLI, adduserapproval.php
type: generated
generated: exp:help:examples
---
# exp:adduserapproval

Set up the approval of new accounts in a user group (default: new Editors wait for the Administrator users): the workflow event on content/publish before and the role that lets the requesters create such accounts (--dry-run lists it first); --mode=section sets up the optional alternative, a section Staff with the plain approval event (a dry run unless --apply).

Skript `bin/php/adduserapproval.php`, Code in `kernel/private/classes/commands/adduserapproval.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:adduserapproval --help
php bin/php/adduserapproval.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Set up the approval of new user accounts: the workflow "
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | List what would be created and changed; change nothing |
| `--mode` | erforderlich | group (default: the event "Approve user accounts") or section (the section Staff and the plain approval event) |
| `--apply` |  | --mode=section only: make the changes (without it the section setup is a dry run) |
| `--staff` | erforderlich | --mode=section: a group whose accounts go into the section Staff (default Administrator users and Editors), may be repeated |
| `--exempt` | erforderlich | --mode=section: a group whose members need no approval (default Administrator users), may be repeated |
| `--required` | erforderlich | A group whose new accounts need approval (default Editors), may be repeated |
| `--approvers` | erforderlich | A group whose members approve (default Administrator users), may be repeated |
| `--requesters` | erforderlich | A group that may create accounts in the required groups and waits for approval (default Editors), may be repeated |
| `--no-role` |  | Do not create or assign the requesters' role (the requesters have their policies already) |
| `--admin-id` | erforderlich | The user the workflow is created by (default 14) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/adduserapproval.php`, `kernel/private/classes/commands/adduserapproval.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
