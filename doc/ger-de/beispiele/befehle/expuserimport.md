---
title: exp:expuserimport
audience: developer, admin
category: examples
summary: Konten aus einer CSV- oder JSON-Datei importieren, jedes so angelegt wie mit exp:user:create, mit der Aktivierungsmail oder deaktiviert; nie ein Passwort aus der Datei (ohne --apply ein Probelauf, der jede Zeile prüft).
keywords: Konsole, Befehl, exp:expuserimport, expuserimport, console, command, CLI, expuserimport.php, exp:user:import
type: generated
generated: exp:help:examples
---
# exp:expuserimport

Konten aus einer CSV- oder JSON-Datei importieren, jedes so angelegt wie mit exp:user:create, mit der Aktivierungsmail oder deaktiviert; nie ein Passwort aus der Datei (ohne --apply ein Probelauf, der jede Zeile prüft).

Skript `bin/php/expuserimport.php`, Code in `kernel/private/classes/commands/expuserimport.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserimport --help
./console exp:user:import --help
php bin/php/expuserimport.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Import accounts
Reads a CSV file (a header line with the columns login, email, first_name, last_name, groups) or a
JSON file (an array of objects with the same keys); groups are node ids or names separated by |,
and a row without groups goes to --group. Each account is created exactly as exp:user:create
creates it. Choose --send-activation (every person gets the activation mail and sets a password
with "Forgot password") or --disabled (the accounts start disabled; reset their passwords later).
A file with a password column is refused: an import never takes passwords. A login that exists
already is skipped; a login twice in the file is refused. Without --apply every row is checked
and nothing is created.

./console exp:user:import members.csv --group=Members --send-activation
./console exp:user:import members.csv --group=Members --send-activation --apply
./console exp:user:import staff.json --disabled --apply --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--type` | erforderlich | Der Dateityp: csv (Vorgabe) oder json; import liest ihn aus dem Dateinamen, wenn er nicht angegeben ist |
| `--group` | erforderlich | Eine Benutzergruppe per Knoten-ID oder Name: list, export und bulk nehmen nur ihre Mitglieder (unter ihr, in jeder Tiefe); create und import: die Gruppe der neuen Konten (für mehrere wiederholen, die erste ist die Hauptgruppe; Vorgabe [UserSettings] DefaultUserPlacement) |
| `--send-activation` |  | Das Konto deaktiviert anlegen und die Aktivierungsmail senden; die Person setzt ihr Passwort mit „Passwort vergessen“ |
| `--disabled` |  | Das Konto deaktiviert anlegen (später mit exp:user:enable aktivieren) |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[file]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserimport.php`, `kernel/private/classes/commands/expuserimport.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
