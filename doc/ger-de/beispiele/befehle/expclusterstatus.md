---
title: exp:expclusterstatus
audience: developer, admin
category: examples
summary: "The state of this installation on every host of settings/cluster.ini: version, PHP, git head, maintenance and Velocity."
keywords: Konsole, Befehl, exp:expclusterstatus, expclusterstatus, console, command, CLI, expclusterstatus.php, exp:cluster:status
type: generated
generated: exp:help:examples
---
# exp:expclusterstatus

The state of this installation on every host of settings/cluster.ini: version, PHP, git head, maintenance and Velocity.

Skript `bin/php/expclusterstatus.php`, Code in `kernel/private/classes/commands/expclusterstatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expclusterstatus --help
./console exp:cluster:status --help
php bin/php/expclusterstatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The state of every host
For each host of settings/cluster.ini: the version (console --version), the PHP version,
the git head, branch and commit date of the installation, whether maintenance mode is on,
and on a host with Engine=velocity whether Velocity answers. Reads only. A table, and
a warning when the hosts are not on the same git head.

./console exp:cluster:status
./console exp:cluster:status --parallel --json

Exit code: 0 every host answered; 1 a host did not; 2 wrong options or settings.
```

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expclusterstatus.php`, `kernel/private/classes/commands/expclusterstatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
