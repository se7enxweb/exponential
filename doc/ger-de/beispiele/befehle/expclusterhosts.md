---
title: exp:expclusterhosts
audience: developer, admin
category: examples
summary: List the hosts of settings/cluster.ini and check their settings; --check also logs in on each.
keywords: Konsole, Befehl, exp:expclusterhosts, expclusterhosts, console, command, CLI, expclusterhosts.php, exp:cluster:hosts
type: generated
generated: exp:help:examples
---
# exp:expclusterhosts

List the hosts of settings/cluster.ini and check their settings; --check also logs in on each.

Skript `bin/php/expclusterhosts.php`, Code in `kernel/private/classes/commands/expclusterhosts.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expclusterhosts --help
./console exp:cluster:hosts --help
php bin/php/expclusterhosts.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
List the hosts of settings/cluster.ini
Each host with its ssh target, port, root, PHP binary, users and engine, and what is wrong
with the settings. --check also logs in on each host and checks that its root holds
bin/php/console and that its PHP binary runs.

./console exp:cluster:hosts
./console exp:cluster:hosts --check
./console exp:cluster:hosts --json

Exit code: 0 the settings are right (and with --check every host answered); 1 a host failed
the check; 2 the settings are wrong.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--check` |  |  |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expclusterhosts.php`, `kernel/private/classes/commands/expclusterhosts.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
