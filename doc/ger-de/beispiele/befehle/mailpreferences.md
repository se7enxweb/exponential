---
title: exp:mailpreferences
audience: developer, admin
category: examples
summary: "Zeigt und ändert die E-Mail-Einstellungen eines Benutzers oder einer Adresse: Kategorien, Hauptschalter, Häufigkeit, Export, Löschen."
keywords: Konsole, Befehl, exp:mailpreferences, mailpreferences, console, command, CLI, mailpreferences.php, exp:mail:preferences
type: generated
generated: exp:help:examples
---
# exp:mailpreferences

Zeigt und ändert die E-Mail-Einstellungen eines Benutzers oder einer Adresse: Kategorien, Hauptschalter, Häufigkeit, Export, Löschen.

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
| `--user` | erforderlich | die Person: ein Login oder eine Benutzer-ID |
| `--email` | erforderlich | die Person: eine Adresse (und das Konto, zu dem sie gehört, falls vorhanden) |
| `--category` | erforderlich | set, frequency: der Kategoriebezeichner |
| `--state` | erforderlich | set, master: on oder off |
| `--frequency` | erforderlich | frequency: immediate, daily oder weekly |
| `--format` | erforderlich | export: json (Standard) oder csv |
| `--output` | erforderlich | export: in diese Datei statt auf den Bildschirm schreiben |
| `--wording` | erforderlich | der im Einwilligungsprotokoll festgehaltene Text (Standard: "Changed on the console") |
| `--source` | erforderlich | admin (Standard) oder import: import schaltet eine Kategorie mit Double-Opt-in sofort ein |
| `--no-mail` |  | set: die Bestätigungsmail eines Double-Opt-in nicht senden |
| `--yes` |  | erase: wirklich löschen |
| `--json` |  | show, categories: JSON |
| `--addresses` |  | die Adresse zeigen, statt sie zu verbergen |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailpreferences.php`, `kernel/private/classes/commands/mailpreferences.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
