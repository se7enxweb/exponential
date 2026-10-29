---
title: exp:velocity
audience: developer, admin
category: examples
summary: Der Befehl exp:velocity (bin/php/velocity.php).
keywords: Konsole, Befehl, exp:velocity, velocity, console, command, CLI, velocity.php, exp:vc
type: generated
generated: exp:help:examples
---
# exp:velocity


Skript `bin/php/velocity.php`, Code in `kernel/private/classes/commands/velocity.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:velocity --help
./console exp:vc --help
php bin/php/velocity.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Als JSON berichten, für einen Aufrufer, der kein Mensch ist |
| `--keep-global` | erforderlich | Weitere Globals, die zwischen Anfragen erhalten bleiben (durch Kommas getrennt), zusätzlich zu den eingebauten Standards und velocity.ini KeepGlobals[]; für start, restart und command |
| `--engine` | erforderlich | php, frankenphp oder qbix (oder mehrere, durch Kommas getrennt) nur für diesen Befehl, statt [ServerSettings] Engine |
| `--from` | erforderlich | install: die Binärdatei aus dieser Datei nehmen, statt sie herunterzuladen (sie wird dennoch geprüft) |
| `--force` |  | install: erneut herunterladen, auch wenn die Binärdatei schon da ist |
| `--check` |  | install: den Hash der installierten Binärdatei erneut gegen ihre SHA-256 prüfen |
| `--trust-github-digest` |  | install: wenn für die Version keine Sha256 festgelegt ist, den Digest akzeptieren, den GitHub für das Release veröffentlicht |
| `--all` |  | start, stop, restart, graceful, kill oder status für jede Engine, eine nach der anderen |
| `--https` |  |  |
| `--no-https` |  |  |
| `--rebuild-phar` |  | start, restart, graceful, deploy: das Engine-Archiv neu bauen, auch wenn es aktuell ist |
| `--kernel` |  | deploy: auch das Autoload-Array des Kernels neu erzeugen (nur kernel/ und lib/) |
| `--apply` |  | config set\|unset, cache clear, layout migrate, site\|conf\|mod, ssl renew, watchdog install\|remove, install: die Änderung vornehmen (ohne die Option sind sie ein Probelauf, der nichts ändert) |
| `--dry-run` |  | der Standard der schreibenden Befehle (beibehalten für Skripte, die die Option übergeben); deploy: die Schritte ausgeben und was jeder täte, und nichts tun |
| `--no-fpm` |  | deploy: PHP-FPM in Ruhe lassen (kein Neuladen, kein Warten) |
| `--fpm-reload` |  | deploy: PHP-FPM neu laden, auch wenn sein OPcache die Dateien neu prüft (php.ini oder Pool geändert, Dateien umbenannt) |
| `--no-velocity` |  | deploy: Velocity nicht neu starten |
| `--no-autoload` |  | deploy: die Autoload-Arrays nicht neu erzeugen |
| `--packer` |  | deploy: auch die gepackten Skripte und Styles leeren (ezjscore-packer, direkt vor template-block) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[command]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/velocity.php`, `kernel/private/classes/commands/velocity.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
