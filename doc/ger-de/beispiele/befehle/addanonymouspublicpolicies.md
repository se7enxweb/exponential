---
title: exp:addanonymouspublicpolicies
audience: developer, admin
category: examples
summary: "Give the Anonymous role the public policies it lacks: user/register, rss/feed, tags/view, the newsletter's public views (--dry-run lists them first)."
keywords: Konsole, Befehl, exp:addanonymouspublicpolicies, addanonymouspublicpolicies, console, command, CLI, addanonymouspublicpolicies.php
type: generated
generated: exp:help:examples
---
# exp:addanonymouspublicpolicies

Give the Anonymous role the public policies it lacks: user/register, rss/feed, tags/view, the newsletter's public views (--dry-run lists them first).

Skript `bin/php/addanonymouspublicpolicies.php`, Code in `kernel/private/classes/commands/addanonymouspublicpolicies.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:addanonymouspublicpolicies --help
php bin/php/addanonymouspublicpolicies.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Give the Anonymous role the policies a visitor who is not signed in needs on a public site and
it lacks: user/register (a new installation takes it out of PolicyOmitList[]), rss/feed, and where
the module exists tags/view (eztags) and newsletter subscribe, configure, unsubscribe and archive
(cjw_newsletter). A policy the role has in any form (also limited, or module/*) is left alone, so
the script can run again. Prints PASS or FAIL at the end.

Example: php bin/php/addanonymouspublicpolicies.php --dry-run
         php bin/php/addanonymouspublicpolicies.php --only=user/register
         php bin/php/addanonymouspublicpolicies.php --skip=user/register --role=Visitors
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | List what would be added, change nothing |
| `--role` | erforderlich | The role to complete (ID or name); default Anonymous |
| `--only` | erforderlich | Only this policy (module/function), may be repeated |
| `--skip` | erforderlich | Never this policy (module/function), may be repeated; e.g. --skip=user/register keeps registration closed |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addanonymouspublicpolicies.php`, `kernel/private/classes/commands/addanonymouspublicpolicies.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
