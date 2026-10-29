---
title: exp:addeditorpolicies
audience: developer, admin
category: examples
summary: "Give the Editor role the everyday policies it lacks: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, the image editor, the own account (--dry-run lists them first)."
keywords: Konsole, Befehl, exp:addeditorpolicies, addeditorpolicies, console, command, CLI, addeditorpolicies.php
type: generated
generated: exp:help:examples
---
# exp:addeditorpolicies

Give the Editor role the everyday policies it lacks: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, the image editor, the own account (--dry-run lists them first).

Skript `bin/php/addeditorpolicies.php`, Code in `kernel/private/classes/commands/addeditorpolicies.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:addeditorpolicies --help
php bin/php/addeditorpolicies.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Give the Editor role the policies an editor needs for everyday work and it lacks: hiding and
revealing (content/hide), the trash (content/restore), the list of what uses an object
(content/reverserelatedlist), where the modules exist ezmultiupload and the image editor
(expimageeditor/edit), and the own account
(user/password, user/selfedit, notification/use) when no other role of the editors has it.
A policy the role, or another role of the same users or groups, has in any form (also limited, or
module/*) is left alone, so the script can run again.
First, a content/* without limitations (the base data's, which plain_site keeps) is replaced by the
content functions the site packages give editors (create, edit, read, manage_locations, diff,
versionread, versionremove, remove, translate, bookmark, pendinglist, dashboard, view_embed, hide,
restore, reverserelatedlist): it also grants emptying everybody's trash, the languages, the URL
aliases and everybody's content jobs. --keep-content-wildcard leaves it. Lists what the role still
holds that an editor role should not (WARN lines). Prints PASS or FAIL at the end.

Example: php bin/php/addeditorpolicies.php --dry-run
         php bin/php/addeditorpolicies.php --only=content/restore
         php bin/php/addeditorpolicies.php --skip=ezmultiupload/* --role=Redakteur
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | List what would be removed and added, change nothing |
| `--role` | erforderlich | The role to complete (ID or name); default Editor |
| `--only` | erforderlich | Only this policy (module/function), may be repeated |
| `--skip` | erforderlich | Never this policy (module/function), may be repeated; e.g. --skip=content/restore keeps the trash closed |
| `--keep-content-wildcard` |  | Leave a content/* of the role as it is (not replaced by single functions) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addeditorpolicies.php`, `kernel/private/classes/commands/addeditorpolicies.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
