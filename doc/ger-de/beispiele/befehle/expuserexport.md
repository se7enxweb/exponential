---
title: exp:expuserexport
audience: developer, admin
category: examples
summary: "Die Konten, die die Listenfilter auswählen, als CSV oder JSON exportieren: Anmeldename, E-Mail-Adresse, Namen, Zustand, Gruppen, letzter Besuch (nie ein Passwort oder sein Hash)."
keywords: Konsole, Befehl, exp:expuserexport, expuserexport, console, command, CLI, expuserexport.php, exp:user:export
type: generated
generated: exp:help:examples
---
# exp:expuserexport

Die Konten, die die Listenfilter auswählen, als CSV oder JSON exportieren: Anmeldename, E-Mail-Adresse, Namen, Zustand, Gruppen, letzter Besuch (nie ein Passwort oder sein Hash).

Skript `bin/php/expuserexport.php`, Code in `kernel/private/classes/commands/expuserexport.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserexport --help
./console exp:user:export --help
php bin/php/expuserexport.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Export accounts
Writes the accounts the filters of exp:user:list select, as CSV (a header line, cells that a
spreadsheet would run as formulas made safe) or JSON (an array of objects): id, login, email,
first_name, last_name, name, enabled, states, groups (node ids separated by |), main_group,
last_visit, login_count. Never a password or a password hash. To standard output, or with --output
to a new file of mode 0600 (then the command prints a report). The file holds personal data:
keep it as such. exp:user:import reads the same columns.

./console exp:user:export > users.csv
./console exp:user:export --group=Members --state=enabled --output=var/members.csv
./console exp:user:export --state=inactive --days=365 --type=json --output=var/inactive.json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--state` | erforderlich | Nur Konten in diesem Zustand: all (Vorgabe), enabled, disabled, unactivated, pending, locked, never, inactive, orphaned, duplicate_login oder duplicate_email |
| `--group` | erforderlich | Eine Benutzergruppe per Knoten-ID oder Name: list, export und bulk nehmen nur ihre Mitglieder (unter ihr, in jeder Tiefe); create und import: die Gruppe der neuen Konten (für mehrere wiederholen, die erste ist die Hauptgruppe; Vorgabe [UserSettings] DefaultUserPlacement) |
| `--search` | erforderlich | Nur Konten, deren Anmeldename, Name oder E-Mail-Adresse diesen Text enthält |
| `--days` | erforderlich | Mit --state=inactive: seit so vielen Tagen nicht angemeldet (Vorgabe 180) |
| `--type` | erforderlich | Der Dateityp: csv (Vorgabe) oder json; import liest ihn aus dem Dateinamen, wenn er nicht angegeben ist |
| `--output` | erforderlich | Den Export in diese Datei schreiben (neu, Modus 0600) statt auf die Standardausgabe |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserexport.php`, `kernel/private/classes/commands/expuserexport.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
