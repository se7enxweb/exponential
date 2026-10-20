---
title: exp:expfetch
audience: developer, admin
category: examples
summary: "The system card: the Exponential 'e' next to the facts of this installation, its engine and its machine, with health hints (exp:info adds an inventory, exp:about the project)."
keywords: Konsole, Befehl, exp:expfetch, expfetch, console, command, CLI, expfetch.php, exp:info, exp:about
type: generated
generated: exp:help:examples
---
# exp:expfetch

The system card: the Exponential "e" next to the facts of this installation, its engine and its machine, with health hints (exp:info adds an inventory, exp:about the project).

Skript `bin/php/expfetch.php`, Code in `kernel/private/classes/commands/syscard.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expfetch --help
./console exp:info --help
./console exp:about --help
php bin/php/expfetch.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The system card of this installation, drawn next to the Exponential "e"

./console exp:expfetch                     Exponential, the machine, PHP, the engine, the database and the site
./console exp:info                         the same and an inventory: extensions and versions, siteaccesses,
                                           settings, cronjobs, the queue, cache sizes, help coverage
./console exp:about                        the same and the project: what Exponential is, license, links
./console exp:expfetch --plain             plain text to paste (no logo, no colors)
./console exp:info --json --no-logo        one JSON document, e.g. for a support request or monitoring
./console exp:expfetch --section=php,engine  only these sections
./console exp:expfetch --watch=2           refresh every 2 seconds (Ctrl-C stops)
./console exp:expfetch --theme=light       darker colors for a light terminal (also orange, mono, high-contrast)
./console exp:expfetch --color=256 --width=100 --animate
./console exp:expfetch --completion=bash >> ~/.bashrc

Colors only on a terminal, never with NO_COLOR set; the depth (truecolor, 256, 16) is taken from COLORTERM
and TERM. The facts come from the providers of settings/syscard.ini, each with a time budget: one that takes
too long shows n/a. Nothing is changed, and no password, key, token or full database address is printed.
Exit code: 0, or 2 for a wrong option.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  |  |
| `--plain` |  |  |
| `--no-logo` |  |  |
| `--section` | erforderlich |  |
| `--no-color` |  |  |
| `--color` | erforderlich |  |
| `--theme` | erforderlich |  |
| `--width` | erforderlich |  |
| `--ascii` |  |  |
| `--no-hints` |  |  |
| `--no-bars` |  |  |
| `--watch` | optional |  |
| `--count` | erforderlich |  |
| `--animate` |  |  |
| `--mode` | erforderlich |  |
| `--list-providers` |  |  |
| `--list-themes` |  |  |
| `--completion` | erforderlich |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expfetch.php`, `kernel/private/classes/commands/syscard.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
