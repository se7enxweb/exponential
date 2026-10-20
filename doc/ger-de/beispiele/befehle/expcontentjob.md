---
title: exp:expcontentjob
audience: developer, admin
category: examples
summary: "Inhaltsaufträge ausführen, auflisten, fortsetzen und abbrechen: große Teilbäume stapelweise entfernen und kopieren."
keywords: Konsole, Befehl, exp:expcontentjob, expcontentjob, console, command, CLI, expcontentjob.php
type: generated
generated: exp:help:examples
---
# exp:expcontentjob

Inhaltsaufträge ausführen, auflisten, fortsetzen und abbrechen: große Teilbäume stapelweise entfernen und kopieren.

Skript `bin/php/expcontentjob.php`, Code in `kernel/private/classes/commands/expcontentjob.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expcontentjob --help
php bin/php/expcontentjob.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Content jobs: large subtree removes and copies, run in batches in the background.

  list [--all]                         the jobs
  show <id> [--json]                   one job and the end of its log
  run <id>                             run a job in the foreground
  resume <id> [--background]           resume a failed job or one whose worker died
  cancel <id>                          stop a job after its current batch
  remove <node id>[,...] [--trash|--delete] [--background]
  copy <src node> <dest parent> [--all-versions] [--keep-creator] [--keep-time] [--background]
  move <node> <new parent> | hide <node> | reveal <node> | section <node> <section id> | state <node> <state id>
  addlocation <target node> <node>[,...] | removelocation <node>[,...]        (all: [--background])
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--all` |  | list: jeder Auftrag, nicht nur die aktiven und fehlgeschlagenen |
| `--json` |  | show: der Auftrag als JSON |
| `--background` |  | remove, copy, resume: einen abgekoppelten Worker starten und zurückkehren |
| `--trash` |  | remove: die Objekte in den Papierkorb verschieben (der Standard von content.ini [RemoveSettings]) |
| `--delete` |  | remove: die Objekte endgültig löschen |
| `--all-versions` |  | copy: alle Versionen jedes Objekts kopieren, nicht nur die aktuelle |
| `--keep-creator` |  | copy: Eigentümer und Ersteller der kopierten Objekte beibehalten |
| `--keep-time` |  | copy: die Zeitpunkte der Erstellung und Änderung beibehalten |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expcontentjob.php`, `kernel/private/classes/commands/expcontentjob.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
