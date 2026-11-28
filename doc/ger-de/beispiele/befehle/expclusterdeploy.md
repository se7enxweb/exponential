---
title: exp:expclusterdeploy
audience: developer, admin
category: examples
summary: "Deploy this installation on every host of settings/cluster.ini: exp:velocity deploy on each, with the host's own options."
keywords: Konsole, Befehl, exp:expclusterdeploy, expclusterdeploy, console, command, CLI, expclusterdeploy.php, exp:cluster:deploy
type: generated
generated: exp:help:examples
---
# exp:expclusterdeploy

Deploy this installation on every host of settings/cluster.ini: exp:velocity deploy on each, with the host's own options.

Skript `bin/php/expclusterdeploy.php`, Code in `kernel/private/classes/commands/expclusterdeploy.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expclusterdeploy --help
./console exp:cluster:deploy --help
php bin/php/expclusterdeploy.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Deploy on every host
Runs exp:velocity deploy on each host of settings/cluster.ini, one after the other by
default: autoloads, caches, PHP-FPM, Velocity and the caches of rendered output, in that
order, PASS/FAIL per host. Each host adds its DeployOptions[]; a host with Engine=fpm
gets --no-velocity. It runs as the host's DeployUser=, else the ssh login, because it
reloads PHP-FPM. The code must already be on the hosts (git pull, rsync): this command
does not copy files.

./console exp:cluster:deploy
./console exp:cluster:deploy --kernel --packer
./console exp:cluster:deploy --hosts=web --remote-dry-run
./console exp:cluster:deploy --dry-run

Exit code: 0 every host passed; 1 a host failed; 2 wrong options or settings.
```

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expclusterdeploy.php`, `kernel/private/classes/commands/expclusterdeploy.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
