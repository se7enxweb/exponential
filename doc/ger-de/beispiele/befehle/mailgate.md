---
title: exp:mailgate
audience: developer, admin
category: examples
summary: "Testet das Mail-Gate: wer von den angegebenen Adressen eine Mail einer Kategorie bekäme und wie die ergänzte Mail aussieht."
keywords: Konsole, Befehl, exp:mailgate, mailgate, console, command, CLI, mailgate.php, exp:mail:gate
type: generated
generated: exp:help:examples
---
# exp:mailgate

Testet das Mail-Gate: wer von den angegebenen Adressen eine Mail einer Kategorie bekäme und wie die ergänzte Mail aussieht.

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
| `--category` | erforderlich | die Kategorie der Testmail (leer: eine Mail ohne Kategorie) |
| `--to` | erforderlich | Adressen, durch Kommas getrennt |
| `--write` |  | die Testmail mit dem Datei-Transport durch das Gate schicken |
| `--html` |  | eine HTML-Testmail |
| `--mail-file-dir` | erforderlich | wohin --write die Mail legt |
| `--addresses` |  | die Adressen zeigen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailgate.php`, `kernel/private/classes/commands/mailgate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
