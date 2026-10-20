---
title: exp:mailbounces
audience: developer, admin
category: examples
summary: "Read the bounce mailbox: hard bounces and spam complaints go on the suppression list."
keywords: Konsole, Befehl, exp:mailbounces, mailbounces, console, command, CLI, mailbounces.php, exp:mail:bounces
type: generated
generated: exp:help:examples
---
# exp:mailbounces

Read the bounce mailbox: hard bounces and spam complaints go on the suppression list.

Skript `bin/php/mailbounces.php`, Code in `kernel/private/classes/commands/mailbounces.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:mailbounces --help
./console exp:mail:bounces --help
php bin/php/mailbounces.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Bounce mailbox
Reads the mailbox of mailpreferences.ini [BounceSettings] (IMAP or POP3, set in a settings
override): hard bounces (delivery status notifications with a permanent failure of
HardStatusCodes[]) and spam complaints (feedback loop reports) put the address on the
suppression list. Soft bounces and other mail are counted only. No address is shown in full.

./console exp:mail:bounces                      read the mailbox
./console exp:mail:bounces --dry-run            show what it would do; nothing is changed
./console exp:mail:bounces --file=dsn.eml       read saved messages (a file or a directory of .eml)
./console exp:mail:bounces --status             the last run

Ends with PASS, SKIP (the reader is disabled or not configured) or FAIL (exit 1).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Report only: nothing is suppressed, no message is changed, the status is not written |
| `--file` | erforderlich | Read these saved messages (.eml file, or a directory of them) instead of the mailbox |
| `--status` |  | Show the last run and stop |
| `--json` |  | One JSON object instead of the text |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailbounces.php`, `kernel/private/classes/commands/mailbounces.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
