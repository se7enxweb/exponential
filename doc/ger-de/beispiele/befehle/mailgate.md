---
title: exp:mailgate
audience: developer, admin
category: examples
summary: "Test the mail gate: who of the given addresses would get a mail of a category, and what the decorated mail looks like."
keywords: Konsole, Befehl, exp:mailgate, mailgate, console, command, CLI, mailgate.php, exp:mail:gate
type: generated
generated: exp:help:examples
---
# exp:mailgate

Test the mail gate: who of the given addresses would get a mail of a category, and what the decorated mail looks like.

Skript `bin/php/mailgate.php`, Code in `kernel/private/classes/commands/mailgate.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:mailgate --help
./console exp:mail:gate --help
php bin/php/mailgate.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Mail gate test
Says for each address what the gate would decide for a mail of --category (allow, off, pending,
master_off, suppressed, unknown_category). With --write the mail goes through the gate with the
transport forced to "file" in this process: it is written to --mail-file-dir (default
var/tmp/mailgate-test), never sent. Addresses are hidden unless --addresses.

./console exp:mail:gate --category=newsletter --to=a@example.com,b@example.com
./console exp:mail:gate --category=content --to=editor@example.com --write --html
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--category` | erforderlich | the category of the test mail (empty: a mail without category) |
| `--to` | erforderlich | addresses, comma separated |
| `--write` |  | send the test mail through the gate with the file transport |
| `--html` |  | an HTML test mail |
| `--mail-file-dir` | erforderlich | where --write puts the mail |
| `--addresses` |  | show the addresses |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailgate.php`, `kernel/private/classes/commands/mailgate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
