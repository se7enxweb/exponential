---
title: exp:mailpreferences
audience: developer, admin
category: examples
summary: "Show and change the e-mail preferences of a user or an address: categories, master switch, frequency, export, erase."
keywords: Konsole, Befehl, exp:mailpreferences, mailpreferences, console, command, CLI, mailpreferences.php, exp:mail:preferences
type: generated
generated: exp:help:examples
---
# exp:mailpreferences

Show and change the e-mail preferences of a user or an address: categories, master switch, frequency, export, erase.

Skript `bin/php/mailpreferences.php`, Code in `kernel/private/classes/commands/mailpreferences.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:mailpreferences --help
./console exp:mail:preferences --help
php bin/php/mailpreferences.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
E-mail preferences of one person
  categories                the categories (identifier, essential/optional, default, frequencies, double opt-in)
  show                      the person's master switch, categories, frequencies, suppression, pending confirmations
  set                       switch a category on or off (--category, --state=on|off)
  frequency                 the frequency of a category (--category, --frequency=immediate|daily|weekly)
  master                    the master switch "all optional mail" (--state=on|off)
  export                    everything stored about the person, JSON or CSV (--format, --output)
  erase                     remove the preferences, anonymise the consent log (--yes)
  link                      print the person's preference link (works without login)
Every change is written to the consent log with source admin (or --source=import for consent
confirmed elsewhere) and the wording of --wording. Addresses are hidden unless --addresses.

./console exp:mail:preferences show --user=editor
./console exp:mail:preferences set --email=someone@example.com --category=newsletter --state=on --no-mail
./console exp:mail:preferences master --user=14 --state=off
./console exp:mail:preferences export --user=editor --format=csv --output=var/tmp/editor.csv
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--user` | erforderlich | the person: a login or a user id |
| `--email` | erforderlich | the person: an address (the account it belongs to, if any) |
| `--category` | erforderlich | set, frequency: the category identifier |
| `--state` | erforderlich | set, master: on or off |
| `--frequency` | erforderlich | frequency: immediate, daily or weekly |
| `--format` | erforderlich | export: json (default) or csv |
| `--output` | erforderlich | export: write to this file instead of the screen |
| `--wording` | erforderlich | the text recorded in the consent log (default: "Changed on the console") |
| `--source` | erforderlich | admin (default) or import: import switches a double opt-in category on at once |
| `--no-mail` |  | set: do not send the confirmation mail of a double opt-in |
| `--yes` |  | erase: really erase |
| `--json` |  | show, categories: JSON |
| `--addresses` |  | show the address instead of hiding it |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailpreferences.php`, `kernel/private/classes/commands/mailpreferences.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
