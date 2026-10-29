---
title: exp:explogs
audience: developer, admin
category: examples
summary: "Der Log-Viewer auf der Kommandozeile: die Log-Dateien auflisten, einer folgen, sie und ihre Rotationen durchsuchen, eine Fehlerreferenz und die Zeilen ihrer Anfrage finden (standardmäßig maskiert); Logs rotieren und ihre Archive aufräumen (Probelauf, außer mit --apply)."
keywords: Konsole, Befehl, exp:explogs, explogs, console, command, CLI, explogs.php, exp:logs
type: generated
generated: exp:help:examples
---
# exp:explogs

Der Log-Viewer auf der Kommandozeile: die Log-Dateien auflisten, einer folgen, sie und ihre Rotationen durchsuchen, eine Fehlerreferenz und die Zeilen ihrer Anfrage finden (standardmäßig maskiert); Logs rotieren und ihre Archive aufräumen (Probelauf, außer mit --apply).

Skript `bin/php/explogs.php`, Code in `kernel/private/classes/commands/explogs.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:explogs --help
./console exp:logs --help
php bin/php/explogs.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The log viewer on the command line (read only; e-mail and IP addresses and secrets masked unless --unmask)

./console exp:logs --list                                  the log files: group, size, time, lines a minute
./console exp:logs --tail=var/log/error.log --lines=50     the last 50 entries
./console exp:logs --tail=var/log/error.log --follow       follow it (Ctrl-C ends)
./console exp:logs --search=timeout --file=var/log/error.log --rotations --level=error,warning
./console exp:logs --search='/content/view/\d+' --regex --scope=kernel --from='2026-10-17 01:00' --to='2026-10-17 02:00'
./console exp:logs --reference=ERR-0123456789          where an error page's reference is, and its request
./console exp:logs --related=var/log/error.log:123456      the lines of the request of the entry at that offset

Log management (doc/eng-us/features/6.0/log-rotation.md):
./console exp:logs --archives                              each log with its archives and their sizes
./console exp:logs --rotate=error,debug --apply            rotate these logs now (keys as in --archives, or ids)
./console exp:logs --rotate-all --apply                    rotate every log that is not empty
./console exp:logs --prune                                 the archives beyond site.ini [FileSettings] limits
./console exp:logs --prune=error --apply                   remove them (all logs, or these)
./console exp:logs --compress --apply                      compress the archives from .2 on
./console exp:logs --truncate=debug [--in-place] --apply   empty a log (rotated, or in place)
--rotate, --rotate-all, --prune, --compress and --truncate change files only with --apply; without it
they print the plan. --dry-run means the same as no --apply; --apply together with --dry-run is a usage
error (exit 2).

Exit code: 0 something listed or found, 1 nothing found, 2 error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--list` |  | Die Logdateien, die der Viewer öffnen darf, nach Gruppe auflisten, mit Größe, Zeit und Zeilen pro Minute |
| `--archives` |  | Die rotierbaren Logs mit ihren Archiven (.1 .. .N, .gz), Größen und Grenzen auflisten |
| `--rotate` | erforderlich | Diese Logs jetzt rotieren, unabhängig von ihrer Größe: Schlüssel (error,debug,site-storage) oder IDs (var/log/error.log), durch Kommas getrennt |
| `--rotate-all` |  | Jedes Log über seiner MaxSize rotieren (mit --force: jedes Log, das nicht leer ist) |
| `--force` |  | Mit --rotate-all: auch die Logs unter ihrer MaxSize |
| `--prune` | optional | Die Archive über die Grenzen (Keep, MaxAgeDays) hinaus entfernen: von jedem Log oder von diesen Schlüsseln |
| `--days` | erforderlich | Mit --prune: Archive entfernen, die älter als so viele Tage sind, statt MaxAgeDays |
| `--compress` | optional | Die unkomprimierten Archive ab .2 komprimieren: von jedem Log oder von diesen Schlüsseln |
| `--truncate` | erforderlich | Diese Logs leeren: sie rotieren (der Inhalt bleibt in .1) oder mit --in-place die Datei selbst leeren |
| `--in-place` |  | Mit --truncate: die Datei an Ort und Stelle leeren, ohne Kopie |
| `--apply` |  | Mit --rotate, --rotate-all, --prune, --compress oder --truncate: die Dateien ändern (Standard ist ein Probelauf, der den Plan ausgibt) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: nur den Plan ausgeben |
| `--tail` | erforderlich | Die letzten Einträge einer Datei (ihr Pfad relativ zum Wurzelverzeichnis, z. B. var/log/error.log) |
| `--lines` | erforderlich | Mit --tail: wie viele Einträge (Standard 20) |
| `--follow` |  | Mit --tail: weiter ausgeben, was zur Datei hinzukommt, mit zunehmendem Abfrageintervall (Strg-C beendet) |
| `--interval` | erforderlich | Mit --follow: das kürzeste Abfrageintervall in Millisekunden (Standard [TailSettings] PollInterval) |
| `--search` | erforderlich | Nach einem Text suchen (wörtlich oder als regulärer Ausdruck mit --regex) |
| `--regex` |  | Mit --search: der Text ist ein regulärer Ausdruck (begrenzt durch [SearchSettings] BacktrackLimit und TimeLimit) |
| `--case` |  | Mit --search: Groß- und Kleinschreibung beachten |
| `--file` | erforderlich | Mit --search: die Datei (Standard: [LogViewerSettings] DefaultFile) |
| `--scope` | erforderlich | Mit --search: file (Standard), kernel (jedes Kernel-Log) oder all (jede aufgeführte Datei) |
| `--rotations` |  | Mit --search: auch die Rotationen (.1 .. .N, .gz) |
| `--level` | erforderlich | Nur diese Stufen: error,warning,notice,debug |
| `--from` | erforderlich | Nur Einträge zu oder nach diesem Zeitpunkt (z. B. 2026-10-17 01:00) |
| `--to` | erforderlich | Nur Einträge zu oder vor diesem Zeitpunkt (ein Datum allein bedeutet den ganzen Tag) |
| `--sa` | erforderlich | Nur Einträge dieses Siteaccess |
| `--uri` | erforderlich | Nur Einträge, deren Anfrage diesen Text enthält |
| `--ip` | erforderlich | Nur Einträge, deren Client-Adresse damit beginnt |
| `--cursor` | erforderlich | Mit --search: dort weitermachen, wo eine frühere Suche aufgehört hat (ausgegeben als "More: --cursor=...") |
| `--limit` | erforderlich | Mit --search: höchstens so viele Einträge ausgeben |
| `--reference` | erforderlich | Eine Fehlerreferenz (ERR-XXXXXXXXXX) in den Kernel-Logs und ihren Rotationen finden, mit ihrer Anfrage |
| `--related` | erforderlich | Die Zeilen derselben Anfrage wie der Eintrag bei <file>:<offset> |
| `--unmask` |  | E-Mail-Adressen, IP-Adressen und Geheimnisse unmaskiert zeigen (die Werte geheimer Einstellungen bleiben verborgen) |
| `--json` |  | Ein JSON-Objekt statt des Textes |
| `--color` | erforderlich | Die Stufen einfärben: auto (Standard: wenn die Ausgabe ein Terminal ist), always, never |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/explogs.php`, `kernel/private/classes/commands/explogs.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
