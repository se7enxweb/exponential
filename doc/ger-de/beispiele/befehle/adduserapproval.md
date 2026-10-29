---
title: exp:adduserapproval
audience: developer, admin
category: examples
summary: "Richtet die Freigabe neuer Konten in einer Benutzergruppe ein (Standard: neue Editors warten auf die Administrator users): das Workflow-Ereignis auf content/publish vorher und die Rolle, mit der die Anfragenden solche Konten anlegen dürfen; --mode=section richtet die optionale ..."
keywords: Konsole, Befehl, exp:adduserapproval, adduserapproval, console, command, CLI, adduserapproval.php
type: generated
generated: exp:help:examples
---
# exp:adduserapproval

Richtet die Freigabe neuer Konten in einer Benutzergruppe ein (Standard: neue Editors warten auf die Administrator users): das Workflow-Ereignis auf content/publish vorher und die Rolle, mit der die Anfragenden solche Konten anlegen dürfen; --mode=section richtet die optionale Alternative ein, einen Abschnitt Staff mit dem einfachen Freigabe-Ereignis (beides ein Probelauf, außer mit --apply).

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
| `--apply` |  | Die Änderungen vornehmen, in beiden Modi (Standard ist ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: auflisten, was angelegt und geändert würde, nichts ändern |
| `--mode` | erforderlich | group (Standard: das Ereignis "Approve user accounts") oder section (der Abschnitt Staff und das einfache Freigabe-Ereignis) |
| `--staff` | erforderlich | --mode=section: eine Gruppe, deren Konten in den Abschnitt Staff kommen (Standard Administrator users und Editors), kann wiederholt werden |
| `--exempt` | erforderlich | --mode=section: eine Gruppe, deren Mitglieder keine Freigabe brauchen (Standard Administrator users), kann wiederholt werden |
| `--required` | erforderlich | Eine Gruppe, deren neue Konten eine Freigabe brauchen (Standard Editors), kann wiederholt werden |
| `--approvers` | erforderlich | Eine Gruppe, deren Mitglieder freigeben (Standard Administrator users), kann wiederholt werden |
| `--requesters` | erforderlich | Eine Gruppe, die Konten in den Gruppen mit Freigabe anlegen darf und auf die Freigabe wartet (Standard Editors), kann wiederholt werden |
| `--no-role` |  | Die Rolle der Anfragenden nicht anlegen oder zuweisen (die Anfragenden haben ihre Richtlinien bereits) |
| `--admin-id` | erforderlich | Der Benutzer, von dem der Workflow angelegt wird (Standard 14) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/adduserapproval.php`, `kernel/private/classes/commands/adduserapproval.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
