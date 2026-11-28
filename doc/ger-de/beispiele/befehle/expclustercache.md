---
title: exp:expclustercache
audience: developer, admin
category: examples
summary: "Clear the caches of this installation on every host of settings/cluster.ini: every cache (with the Valkey keys of the cache clear listeners) and Velocity's response cache."
keywords: Konsole, Befehl, exp:expclustercache, expclustercache, console, command, CLI, expclustercache.php, exp:cluster:cache
type: generated
generated: exp:help:examples
---
# exp:expclustercache

Clear the caches of this installation on every host of settings/cluster.ini: every cache (with the Valkey keys of the cache clear listeners) and Velocity's response cache.

Skript `bin/php/expclustercache.php`, Code in `kernel/private/classes/commands/expclustercache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expclustercache --help
./console exp:cluster:cache --help
php bin/php/expclustercache.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Clear the caches on every host
On each host of settings/cluster.ini: exp:ezcache --clear-all, which also clears the caches
extensions keep elsewhere through the cache clear listeners (Valkey INI and view keys) and
the template-block cache; then, on a host with Engine=velocity, Velocity's response cache
(exp:velocity cache clear --apply). --tag and --id clear only those caches. --full runs
exp:velocity deploy --all-caches --no-autoload instead, which also reloads PHP-FPM and
restarts Velocity in the right order.

./console exp:cluster:cache clear
./console exp:cluster:cache clear --tag=template,content
./console exp:cluster:cache clear --full --parallel
./console exp:cluster:cache clear --dry-run

Exit code: 0 every host passed; 1 a host failed; 2 wrong options or settings.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--tag` | erforderlich |  |
| `--id` | erforderlich |  |
| `--purge` |  |  |
| `--full` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expclustercache.php`, `kernel/private/classes/commands/expclustercache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
