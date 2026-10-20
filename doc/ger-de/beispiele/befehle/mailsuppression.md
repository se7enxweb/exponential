---
title: exp:mailsuppression
audience: developer, admin
category: examples
summary: "The suppression list of the e-mail gate: list, check, add and lift entries (only hashes are stored)."
keywords: Konsole, Befehl, exp:mailsuppression, mailsuppression, console, command, CLI, mailsuppression.php, exp:mail:suppression
type: generated
generated: exp:help:examples
---
# exp:mailsuppression

The suppression list of the e-mail gate: list, check, add and lift entries (only hashes are stored).

Skript `bin/php/mailsuppression.php`, Code in `kernel/private/classes/commands/mailsuppression.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:mailsuppression --help
./console exp:mail:suppression --help
php bin/php/mailsuppression.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
E-mail suppression list
No optional mail goes to a suppressed address. The table holds a salted sha256 hash of each
address, never the address: an entry is found by entering the address.
  list                      the entries: hash (start), reason, date, note
  check                     is --email suppressed, and why
  add                       suppress --email with --reason (bounce, complaint, unsubscribe_all, legal, admin, bridge)
  lift                      lift the entry of --email or --hash

./console exp:mail:suppression list --reason=bounce
./console exp:mail:suppression add --email=someone@example.com --reason=legal --note="Request of 2026-10-04"
./console exp:mail:suppression lift --hash=3f2a...
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--email` | erforderlich | the address (check, add, lift) |
| `--hash` | erforderlich | lift: the hash of an entry (list shows its start; the whole hash is in --json) |
| `--reason` | erforderlich | add: the reason; list: only this reason |
| `--note` | erforderlich | add: a note for the admins (the address is removed from it) |
| `--limit` | erforderlich | list: how many (default 50) |
| `--offset` | erforderlich | list: skip this many |
| `--json` |  | list: JSON |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailsuppression.php`, `kernel/private/classes/commands/mailsuppression.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
