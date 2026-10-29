---
title: exp:addmembermanagement
audience: developer, admin
category: examples
summary: "Richtet die Mitgliederverwaltung nach membermanagement.ini ein: die Rolle, mit der eine Gruppe (Standard Editors) die Konten einer anderen (Standard Members) verwaltet, nie Administrator users oder Editors (Probelauf, außer mit --apply)."
keywords: Konsole, Befehl, exp:addmembermanagement, addmembermanagement, console, command, CLI, addmembermanagement.php
type: generated
generated: exp:help:examples
---
# exp:addmembermanagement

Richtet die Mitgliederverwaltung nach membermanagement.ini ein: die Rolle, mit der eine Gruppe (Standard Editors) die Konten einer anderen (Standard Members) verwaltet, nie Administrator users oder Editors (Probelauf, außer mit --apply).

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
Without --apply nothing is written: the script lists what it would create, add and assign. --dry-run
is still accepted and means the same as no flag; --apply together with --dry-run is a usage error (exit 2).

Example: php bin/php/addmembermanagement.php
         php bin/php/addmembermanagement.php --apply
         php bin/php/addmembermanagement.php --apply --rule=editors_manage_members
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen schreiben (Standard ist ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: auflisten, was angelegt, hinzugefügt und zugewiesen würde; nichts ändern |
| `--rule` | erforderlich | Nur diese Regel ([MemberRule_<name>]), kann wiederholt werden |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addmembermanagement.php`, `kernel/private/classes/commands/addmembermanagement.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
