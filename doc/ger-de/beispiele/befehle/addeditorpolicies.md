---
title: exp:addeditorpolicies
audience: developer, admin
category: examples
summary: "Gibt der Rolle Editor die alltäglichen Richtlinien, die ihr fehlen: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, den Bildeditor, das eigene Konto (Probelauf, außer mit --apply)."
keywords: Konsole, Befehl, exp:addeditorpolicies, addeditorpolicies, console, command, CLI, addeditorpolicies.php
type: generated
generated: exp:help:examples
---
# exp:addeditorpolicies

Gibt der Rolle Editor die alltäglichen Richtlinien, die ihr fehlen: content/hide, content/restore, content/reverserelatedlist, ezmultiupload, den Bildeditor, das eigene Konto (Probelauf, außer mit --apply).

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
(content/reverserelatedlist), where the modules are active ezmultiupload and the image editors
(expimageeditor/edit, ezie/*), and the own account
(user/password, user/selfedit, notification/use) when no other role of the editors has it.
A policy the role, or another role of the same users or groups, has in any form (also limited, or
module/*) is left alone, so the script can run again.
First, a content/* without limitations (the base data's, which plain_site keeps) is replaced by the
content functions the site packages give editors (create, edit, read, manage_locations, diff,
versionread, versionremove, remove, translate, bookmark, pendinglist, dashboard, view_embed, hide,
restore, reverserelatedlist): it also grants emptying everybody's trash, the languages, the URL
aliases and everybody's content jobs. --keep-content-wildcard leaves it. Lists what the role still
holds that an editor role should not (WARN lines). Prints PASS or FAIL at the end.
Without --apply nothing is written: the script lists what it would remove and add. --dry-run is
still accepted and means the same as no flag; --apply together with --dry-run is a usage error (exit 2).

Example: php bin/php/addeditorpolicies.php
         php bin/php/addeditorpolicies.php --apply
         php bin/php/addeditorpolicies.php --apply --only=content/restore
         php bin/php/addeditorpolicies.php --apply --skip=ezmultiupload/* --role=Redakteur
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen schreiben (Standard ist ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: auflisten, was entfernt und hinzugefügt würde, nichts ändern |
| `--role` | erforderlich | Die zu vervollständigende Rolle (ID oder Name); Standard Editor |
| `--only` | erforderlich | Nur diese Richtlinie (Modul/Funktion), kann wiederholt werden |
| `--skip` | erforderlich | Nie diese Richtlinie (Modul/Funktion), kann wiederholt werden; z. B. hält --skip=content/restore den Papierkorb geschlossen |
| `--keep-content-wildcard` |  | Ein content/* der Rolle so lassen, wie es ist (nicht durch einzelne Funktionen ersetzt) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addeditorpolicies.php`, `kernel/private/classes/commands/addeditorpolicies.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
