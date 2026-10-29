---
title: exp:addanonymouspublicpolicies
audience: developer, admin
category: examples
summary: "Gibt der Rolle Anonymous die öffentlichen Richtlinien, die ihr fehlen: user/register, rss/feed, tags/view, die öffentlichen Views des Newsletters (Probelauf, außer mit --apply)."
keywords: Konsole, Befehl, exp:addanonymouspublicpolicies, addanonymouspublicpolicies, console, command, CLI, addanonymouspublicpolicies.php
type: generated
generated: exp:help:examples
---
# exp:addanonymouspublicpolicies

Gibt der Rolle Anonymous die öffentlichen Richtlinien, die ihr fehlen: user/register, rss/feed, tags/view, die öffentlichen Views des Newsletters (Probelauf, außer mit --apply).

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
Without --apply nothing is written: the script lists what it would add. --dry-run is still accepted
and means the same as no flag; --apply together with --dry-run is a usage error (exit 2).

Example: php bin/php/addanonymouspublicpolicies.php
         php bin/php/addanonymouspublicpolicies.php --apply
         php bin/php/addanonymouspublicpolicies.php --apply --only=user/register
         php bin/php/addanonymouspublicpolicies.php --apply --skip=user/register --role=Visitors
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen schreiben (Standard ist ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Der Standard, beibehalten für Skripte, die die Option übergeben: auflisten, was hinzugefügt würde, nichts ändern |
| `--role` | erforderlich | Die zu ergänzende Rolle (ID oder Name); Standard Anonymous |
| `--only` | erforderlich | Nur diese Richtlinie (Modul/Funktion), kann wiederholt werden |
| `--skip` | erforderlich | Diese Richtlinie nie (Modul/Funktion), kann wiederholt werden; z. B. hält --skip=user/register die Registrierung geschlossen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/addanonymouspublicpolicies.php`, `kernel/private/classes/commands/addanonymouspublicpolicies.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
