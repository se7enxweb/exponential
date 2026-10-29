---
title: exp:collaborationsampledata
audience: developer, admin
category: examples
summary: Legt Beispielinhalte für das Kollaborationswerkzeug an (Freigaben, Kommentare, Gruppen) oder entfernt sie wieder (Probelauf, außer mit --apply).
keywords: Konsole, Befehl, exp:collaborationsampledata, collaborationsampledata, console, command, CLI, collaborationsampledata.php, exp:collaboration:sample-data
type: generated
generated: exp:help:examples
---
# exp:collaborationsampledata

Legt Beispielinhalte für das Kollaborationswerkzeug an (Freigaben, Kommentare, Gruppen) oder entfernt sie wieder (Probelauf, außer mit --apply).

Skript `bin/php/collaborationsampledata.php`, Code in `kernel/private/classes/commands/collaborationsampledata.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:collaborationsampledata --help
./console exp:collaboration:sample-data --help
php bin/php/collaborationsampledata.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Collaboration sample data
Builds a realistic set for the collaboration tool: the user group "Editors (sample)" with three
editors (e-mail addresses on example.invalid, random passwords that are not shown), the workflow
"Approval (sample)" that only reaches the section "Sample: collaboration", a folder in that
section and articles that wait for approval, were approved or were denied, with comment threads.
Everything is made through the kernel's own approval workflow and handler, and is marked
(names start with "Sample: ", remote ids with "sample-collab-") so --remove deletes exactly it.
The command is idempotent: what exists is left as it is.
Without --apply nothing is changed: the command lists what it would create (or, with --remove,
delete). --dry-run means the same as no flag; --apply together with --dry-run is a usage error
(exit 2).

./console exp:collaboration:sample-data
./console exp:collaboration:sample-data --apply
./console exp:collaboration:sample-data --remove --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen vornehmen (Standard ist ein Probelauf, der sie zeigt und nichts ändert) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: zeigen, was getan würde, nichts ändern |
| `--remove` |  | Die Beispieldaten löschen (und nur sie) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/collaborationsampledata.php`, `kernel/private/classes/commands/collaborationsampledata.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
