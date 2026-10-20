---
title: exp:install
audience: developer, admin
category: examples
summary: One-command installation, SQLite by default, no kickstart.ini needed.
keywords: Konsole, Befehl, exp:install, install, console, command, CLI, install.php, exp:names
type: generated
generated: exp:help:examples
---
# exp:install

One-command installation, SQLite by default, no kickstart.ini needed.

Skript `bin/php/install.php`, Code in `kernel/private/classes/commands/install.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:install --help
./console exp:names --help
php bin/php/install.php --help
```

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/install.php`, `kernel/private/classes/commands/install.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
