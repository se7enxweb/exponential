---
title: exp:expclusterrun
audience: developer, admin
category: examples
summary: Run a console command (or, with --shell, a command line) in this installation on every host of settings/cluster.ini, over ssh.
keywords: Konsole, Befehl, exp:expclusterrun, expclusterrun, console, command, CLI, expclusterrun.php, exp:cluster:run
type: generated
generated: exp:help:examples
---
# exp:expclusterrun

Run a console command (or, with --shell, a command line) in this installation on every host of settings/cluster.ini, over ssh.

Skript `bin/php/expclusterrun.php`, Code in `kernel/private/classes/commands/expclusterrun.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expclusterrun --help
./console exp:cluster:run --help
php bin/php/expclusterrun.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Run a command in this installation on every host, over ssh
The hosts are settings/cluster.ini [ClusterSettings] HostList[] (or --hosts). The words
after -- are a console command each host runs as bin/php/console <words> in its root, with
its PHP binary, as its User= (else the ssh login); --shell runs them as a command line for
sh. One host after the other by default, --parallel all at once. PASS/FAIL per host.

./console exp:cluster:run -- exp:cache status
./console exp:cluster:run --hosts=web,cluster -- exp:maintenance status
./console exp:cluster:run --parallel --shell -- uptime
./console exp:cluster:run --dry-run -- exp:ezcache --clear-tag=ini

Exit code: 0 every host passed; 1 a host failed; 2 wrong options or settings.
```

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expclusterrun.php`, `kernel/private/classes/commands/expclusterrun.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
