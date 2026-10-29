---
title: exp:contentcheck
audience: developer, admin
category: examples
summary: "Inhaltsprüfungen: den Referenzindex aufbauen, die ungenutzten Assets und die defekten Beziehungen unter einem Knoten auflisten, die fehlenden Dateien auflisten, die Speicherdateien durchsuchen (nur Bericht; --fix, --quarantine und --fix-permissions sind ein Probelauf, außer ..."
keywords: Konsole, Befehl, exp:contentcheck, contentcheck, console, command, CLI, contentcheck.php, exp:content-check
type: generated
generated: exp:help:examples
---
# exp:contentcheck

Inhaltsprüfungen: den Referenzindex aufbauen, die ungenutzten Assets und die defekten Beziehungen unter einem Knoten auflisten, die fehlenden Dateien auflisten, die Speicherdateien durchsuchen (nur Bericht; --fix, --quarantine und --fix-permissions sind ein Probelauf, außer mit --apply).

Skript `bin/php/contentcheck.php`, Code in `kernel/private/classes/commands/contentcheck.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:contentcheck --help
./console exp:content-check --help
php bin/php/contentcheck.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Content checks (report only; --fix, --quarantine and --fix-permissions are a dry run unless --apply)

./console exp:content-check --build-index           build the reference index (also the cronjob part contentcheck)
./console exp:content-check --unreferenced --node=2  the unused assets below node 2
./console exp:content-check --broken --node=2        the broken relations below node 2, and the layouts'
./console exp:content-check --why=123,456           what uses objects 123 and 456
./console exp:content-check --storage               scan the storage files: orphans and file problems
./console exp:content-check --storage --quarantine            which orphans would be quarantined
./console exp:content-check --storage --quarantine --apply    move them to the quarantine
./console exp:content-check --storage --fix-permissions [--apply]   set owner and mode of used files
./console exp:content-check --missing-files --node=2  files missing from the storage, stale image aliases
./console exp:content-check --missing-files --fix=regenerate   what regenerating would do
./console exp:content-check --fix=restore --items=123-4,125-2 --apply  restore two field versions
./console exp:content-check --fix=replace --items=123-4 --replace-with=photo.jpg --apply

--fix (regenerate, restore, replace, remove, review), --quarantine and --fix-permissions are the only
options that change anything, and only with --apply: without it they show the plan and change nothing.
--dry-run means the same as no --apply; --apply together with --dry-run is a usage error (exit 2).
Without --items --fix takes every finding of --missing-files.
The lists are those of the user given by --login (default admin): what that user may read.
Exit code: 0 nothing found, 1 something found, 2 error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--build-index` |  | Den Referenzindex aufbauen und im Cache behalten |
| `--unreferenced` |  | Die ungenutzten Assets unter --node auflisten |
| `--broken` |  | Die defekten Verknüpfungen unter --node (alle Seiten) und in den Layouts auflisten |
| `--missing-files` |  | Die Felder unter --node auflisten, deren Dateien fehlen, und Bilder mit fehlenden oder veralteten Aliasen |
| `--problems` | erforderlich | Mit --missing-files: missing,alias_missing,alias_stale,alias_absent (Standard: die ersten drei) |
| `--versions` | erforderlich | Mit --missing-files: published,draft (Standard: beide) |
| `--fix` | erforderlich | Beheben: neu erzeugen, wiederherstellen, ersetzen, entfernen oder prüfen (die Befunde von --missing-files oder --items) |
| `--items` | erforderlich | Mit --fix: Feldversionen als <attribute id>-<version>, durch Kommas getrennt |
| `--replace-with` | erforderlich | Mit --fix=replace: die Datei, die an den erwarteten Pfad gelegt wird (ein Eintrag) |
| `--node` | erforderlich | Der Knoten, bei dem die Listen beginnen (Standard: content.ini [NodeSettings] RootNode) |
| `--why` | erforderlich | Objekt-IDs: sagen, was jede von ihnen verwendet |
| `--login` | erforderlich | Der Benutzer, dessen Zugriffsrechte die Listen verwenden (Standard admin) |
| `--json` |  | Ein JSON-Objekt statt des Textes |
| `--storage` |  | Die Speicherdateien durchsuchen (alle, in Stapeln) und das Ergebnis für die Speicheransicht behalten |
| `--quarantine` |  | Mit --storage: die verwaisten Dateien des Durchlaufs in die Quarantäne verschieben (jede wird erneut geprüft) |
| `--fix-permissions` |  | Mit --storage: bei verwendeten Dateien mit falschem Eigentümer oder Modus den erwarteten setzen |
| `--apply` |  | Mit --fix, --quarantine oder --fix-permissions: ausführen (Standard ist ein Probelauf, der den Plan zeigt) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: zeigen, was getan würde, nichts ändern |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/contentcheck.php`, `kernel/private/classes/commands/contentcheck.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
