---
title: exp:expimagealias
audience: developer, admin
category: examples
summary: "Auf Abruf erzeugte Bild-Aliase: status, generate, regenerate, purge, resync und test, im Prozess oder als Jobs."
keywords: Konsole, Befehl, exp:expimagealias, expimagealias, console, command, CLI, expimagealias.php, exp:image:alias
type: generated
generated: exp:help:examples
---
# exp:expimagealias

Auf Abruf erzeugte Bild-Aliase: status, generate, regenerate, purge, resync und test, im Prozess oder als Jobs.

Skript `bin/php/expimagealias.php`, Code in `kernel/private/classes/commands/expimagealias.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expimagealias --help
./console exp:image:alias --help
php bin/php/expimagealias.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (der Standard) oder json (die Maschinenausgabe exp-image-alias/1) |
| `--node` | erforderlich | die Bilder des Objekts an diesem Knoten |
| `--subtree` | erforderlich | die Bilder jedes Objekts unter diesem Knoten (den Knoten eingeschlossen) |
| `--object` | erforderlich | test: die Bilder dieses Objekts |
| `--class` | erforderlich | nur Objekte dieser Klassen (Bezeichner, durch Kommas getrennt) |
| `--attribute` | erforderlich | nur diese Bildattribute (Bezeichner, durch Kommas getrennt) |
| `--alias` | erforderlich | nur diese Aliase (durch Kommas getrennt; resync: original für die Originale); test: diesen Alias erzeugen oder prüfen |
| `--since` | erforderlich | nur Objekte, die geändert wurden seit: einem Datum (2026-11-01, "2026-11-01 14:30") oder einer zurückliegenden Zeitspanne (2d, 12h, 30m, "1 week") |
| `--limit` | erforderlich | höchstens so viele Bilder |
| `--dry-run` |  | generate, regenerate, purge: nur die Bilder zählen; resync: sagen, was geholt würde; test --alias: nur berichten |
| `--overwrite` |  | resync: Dateien ersetzen, die von der Quelle abweichen (standardmäßig werden nur fehlende Dateien geholt) |
| `--queue` |  | den Lauf als Job einreihen und seine ID ausgeben, statt ihn hier auszuführen |
| `--wait` |  | ein Lauf hier wartet, solange Massenläufe pausiert sind, statt abzubrechen |
| `--all` |  | regenerate, purge: einen Lauf über jedes Bild der Installation erlauben |
| `--job` | erforderlich | status: ein Job der Bild-Aliase, mit seinem Fortschritt |
| `--pause` | optional | status: Massenläufe auf jedem Server pausieren (ein optionaler Grund: --pause="nightly import") |
| `--resume` |  | status: pausierte Massenläufe weiterlaufen lassen |
| `--cancel` | erforderlich | status: diesen Job abbrechen (ein wartender endet sofort, ein laufender hält nach seinem aktuellen Bild an) |
| `--source` | optional | test: die Verbindung zu einer Staging-Quelle testen (Standard: die, die SyncFrom nennt) |
| `--path` | erforderlich | test --source: ein Speicherpfad, nach dem die Quelle gefragt wird (Standard: ihre Basisadresse) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[subcommand]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expimagealias.php`, `kernel/private/classes/commands/expimagealias.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
