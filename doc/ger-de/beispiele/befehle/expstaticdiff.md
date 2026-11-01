---
title: exp:expstaticdiff
audience: developer, admin
category: examples
summary: Was der letzte statische Build eines Ziels geändert hat, ob sein Ordner noch zum Manifest passt und ob jeder Link darin funktioniert, wenn er unterhalb eines Pfads ausgeliefert wird.
keywords: Konsole, Befehl, exp:expstaticdiff, expstaticdiff, console, command, CLI, expstaticdiff.php, exp:static:diff
type: generated
generated: exp:help:examples
---
# exp:expstaticdiff

Was der letzte statische Build eines Ziels geändert hat, ob sein Ordner noch zum Manifest passt und ob jeder Link darin funktioniert, wenn er unterhalb eines Pfads ausgeliefert wird.

Skript `bin/php/expstaticdiff.php`, Code in `kernel/private/classes/commands/expstaticdiff.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstaticdiff --help
./console exp:static:diff --help
php bin/php/expstaticdiff.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
What the last static build changed
Compares the manifest of the target's folder with the one of the build before it: the files added,
changed and removed. --verify compares the folder with its manifest (files missing, changed since the
build, or not listed); --check-links checks every link and file as a browser would, served below each
--mount. Changes nothing; exit 1 when --verify or --check-links finds a problem.

./console exp:static:diff github-public
./console exp:static:diff github-public --verify
./console exp:static:diff github-public --check-links --mount=/ --mount=/my-site/
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |
| `--verify` |  | Den Ordner mit seinem Manifest vergleichen: fehlende Dateien, seit dem Build geänderte oder nicht aufgeführte |
| `--check-links` |  | Jeden Link und jede Datei des Ordners prüfen, wie ein Browser es täte, ausgeliefert unterhalb jedes --mount |
| `--mount` | erforderlich | Der Pfad, unter dem der Ordner für --check-links ausgeliefert wird (Standard BasePath des Ziels; eine Kommaliste oder wiederholt: /,/repo/) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[target]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstaticdiff.php`, `kernel/private/classes/commands/expstaticdiff.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
