---
title: exp:addmembermanagement
audience: developer, admin
category: examples
summary: "Set up member management from membermanagement.ini: the role that lets a group (default Editors) manage the accounts of another (default Members), never Administrator users or Editors (--dry-run lists it first)."
keywords: Konsole, Befehl, exp:addmembermanagement, addmembermanagement, console, command, CLI, addmembermanagement.php
type: generated
generated: exp:help:examples
---
# exp:addmembermanagement

Set up member management from membermanagement.ini: the role that lets a group (default Editors) manage the accounts of another (default Members), never Administrator users or Editors (--dry-run lists it first).

Skript `bin/php/addmembermanagement.php`, Code in `kernel/private/classes/commands/addmembermanagement.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:addmembermanagement --help
php bin/php/addmembermanagement.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Set up member management as membermanagement.ini describes it: for each rule, the role with the
rule's policy set (each content policy limited to the managed groups), assigned to the manager groups.
The defaults give the Editors group the role "Member management" over Members. A rule whose managed
groups reach a protected group (default Administrator users and Editors) is refused. What a role already
has is left alone, so the script can run again. Prints PASS or FAIL at the end.

Example: php bin/php/addmembermanagement.php --dry-run
         php bin/php/addmembermanagement.php
         php bin/php/addmembermanagement.php --rule=editors_manage_members
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | List what would be created, added and assigned; change nothing |
| `--rule` | erforderlich | Only this rule ([MemberRule_<name>]), may be repeated |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addmembermanagement.php`, `kernel/private/classes/commands/addmembermanagement.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
