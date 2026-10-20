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
| `--json` |  | Report as JSON, for a caller that is not a person |
| `--keep-global` | erforderlich | More globals to keep between requests (comma-separated), appended to the built-in defaults and velocity.ini KeepGlobals[]; for start, restart and command |
| `--engine` | erforderlich | php, frankenphp or qbix -- or several, comma-separated -- for this command only, instead of [ServerSettings] Engine |
| `--from` | erforderlich | install: take the binary from this file instead of downloading it (still verified) |
| `--force` |  | install: download again even if the binary is already there |
| `--check` |  | install: re-hash the installed binary against its SHA-256 |
| `--trust-github-digest` |  | install: with no Sha256 pinned for the version, accept the digest GitHub publishes for the release |
| `--all` |  | start, stop, restart, graceful, kill or status every engine, one after the other |
| `--https` |  |  |
| `--no-https` |  |  |
| `--rebuild-phar` |  | start, restart, graceful, deploy: rebuild the engine archive even when it is current |
| `--kernel` |  | deploy: regenerate the kernel autoload array too (kernel/ and lib/ only) |
| `--dry-run` |  | deploy: print the steps and what each would do, and do nothing |
| `--no-fpm` |  | deploy: leave PHP-FPM alone (no reload, no wait) |
| `--fpm-reload` |  | deploy: reload PHP-FPM even when its OPcache revalidates the files (php.ini or pool changed, files renamed) |
| `--no-velocity` |  | deploy: do not restart Velocity |
| `--no-autoload` |  | deploy: do not regenerate autoload arrays |
| `--packer` |  | deploy: clear the packed scripts and styles too (ezjscore-packer, right before template-block) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[command]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/velocity.php`, `kernel/private/classes/commands/velocity.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
