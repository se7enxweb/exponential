---
title: exp:adduserapproval
audience: developer, admin
category: examples
summary: "Richtet die Freigabe neuer Konten in einer Benutzergruppe ein (Standard: neue Redakteure warten auf die Administrator users): das Workflow-Ereignis vor content/publish und die Rolle, mit der die Anfragenden solche Konten anlegen dürfen (--dry-run listet es zuerst auf) ..."
keywords: Konsole, Befehl, exp:adduserapproval, adduserapproval, console, command, CLI, adduserapproval.php
type: generated
generated: exp:help:examples
---
# exp:adduserapproval

Richtet die Freigabe neuer Konten in einer Benutzergruppe ein (Standard: neue Redakteure warten auf die Administrator users): das Workflow-Ereignis vor content/publish und die Rolle, mit der die Anfragenden solche Konten anlegen dürfen (--dry-run listet es zuerst auf); --mode=section richtet die optionale Alternative ein, eine Sektion Staff mit dem einfachen Freigabe-Ereignis (ein Probelauf, außer mit --apply).

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
| `--dry-run` |  | Listet auf, was angelegt und geändert würde; ändert nichts |
| `--mode` | erforderlich | group (Standard: das Ereignis "Approve user accounts") oder section (die Sektion Staff und das einfache Freigabe-Ereignis) |
| `--apply` |  | Nur mit --mode=section: führt die Änderungen aus (ohne diese Option ist die Einrichtung der Sektion ein Probelauf) |
| `--staff` | erforderlich | --mode=section: eine Gruppe, deren Konten in die Sektion Staff kommen (Standard Administrator users und Editors), darf wiederholt werden |
| `--exempt` | erforderlich | --mode=section: eine Gruppe, deren Mitglieder keine Freigabe brauchen (Standard Administrator users), darf wiederholt werden |
| `--required` | erforderlich | Eine Gruppe, deren neue Konten freigegeben werden müssen (Standard Editors), darf wiederholt werden |
| `--approvers` | erforderlich | Eine Gruppe, deren Mitglieder freigeben (Standard Administrator users), darf wiederholt werden |
| `--requesters` | erforderlich | Eine Gruppe, die Konten in den erforderlichen Gruppen anlegen darf und auf Freigabe wartet (Standard Editors), darf wiederholt werden |
| `--no-role` |  | Legt die Rolle der Anfragenden nicht an und weist sie nicht zu (die Anfragenden haben ihre Richtlinien bereits) |
| `--admin-id` | erforderlich | Der Benutzer, durch den der Workflow angelegt wird (Standard 14) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/adduserapproval.php`, `kernel/private/classes/commands/adduserapproval.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
