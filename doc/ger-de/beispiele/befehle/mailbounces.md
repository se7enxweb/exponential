---
title: exp:mailbounces
audience: developer, admin
category: examples
summary: "Liest das Bounce-Postfach: harte Bounces und Spam-Beschwerden kommen auf die Sperrliste (Probelauf, außer mit --apply)."
keywords: Konsole, Befehl, exp:mailbounces, mailbounces, console, command, CLI, mailbounces.php, exp:mail:bounces
type: generated
generated: exp:help:examples
---
# exp:mailbounces

Liest das Bounce-Postfach: harte Bounces und Spam-Beschwerden kommen auf die Sperrliste (Probelauf, außer mit --apply).

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

Without --apply nothing is changed: the messages are read and reported, no address is suppressed,
no message is marked or deleted and the status is not written. --dry-run is still accepted and
means the same as no flag; --apply together with --dry-run is a usage error (exit 2).

./console exp:mail:bounces                      show what reading the mailbox would do
./console exp:mail:bounces --apply              read the mailbox and suppress the addresses
./console exp:mail:bounces --file=dsn.eml       read saved messages (a file or a directory of .eml)
./console exp:mail:bounces --status             the last run

Ends with PASS, SKIP (the reader is disabled or not configured) or FAIL (exit 1).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Adressen sperren, die Nachrichten behandeln und den Status schreiben (Standard ist ein Probelauf) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: nur berichten, nichts sperren, keine Nachricht ändern, den Status nicht schreiben |
| `--file` | erforderlich | Diese gespeicherten Nachrichten (.eml-Datei oder ein Verzeichnis davon) statt des Postfachs lesen |
| `--status` |  | Den letzten Lauf zeigen und aufhören |
| `--json` |  | Ein JSON-Objekt statt des Textes |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailbounces.php`, `kernel/private/classes/commands/mailbounces.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
