---
title: exp:exphostrun
audience: developer, admin
category: examples
summary: Run a console command (or, with --shell, a command line) in this installation on one host of settings/cluster.ini, over ssh.
keywords: Konsole, Befehl, exp:exphostrun, exphostrun, console, command, CLI, exphostrun.php, exp:host:run
type: generated
generated: exp:help:examples
---
# exp:exphostrun

Run a console command (or, with --shell, a command line) in this installation on one host of settings/cluster.ini, over ssh.

Skript `bin/php/exphostrun.php`, Code in `kernel/private/classes/commands/exphostrun.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:exphostrun --help
./console exp:host:run --help
php bin/php/exphostrun.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Run a command in this installation on one host, over ssh
The host is one of settings/cluster.ini [ClusterSettings] HostList[]. The words after --
are a console command run there as bin/php/console <words>, in the installation's root
on that host, with its PHP binary, as its User= (sudo -n), else as the ssh login.
--shell runs the words as a command line for sh instead. ssh runs with BatchMode=yes:
a host that would ask for a password fails. Nothing is interactive.

./console exp:host:run web -- exp:cache status
./console exp:host:run web -- exp:ezcache --clear-tag=template
./console exp:host:run web --shell -- git log -1 --oneline
./console exp:host:run web --dry-run -- exp:velocity deploy

Exit code: 0 PASS; 1 the command failed on the host (or ssh did); 2 wrong options or settings.
```

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/exphostrun.php`, `kernel/private/classes/commands/exphostrun.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
