---
title: exp:expuseredit
audience: developer, admin
category: examples
summary: "Ein Benutzerkonto ändern: E-Mail-Adresse, Anmeldename, Vor- und Nachname, hinzugefügte oder entfernte Gruppen, die Hauptgruppe (ein Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:expuseredit, expuseredit, console, command, CLI, expuseredit.php, exp:user:edit
type: generated
generated: exp:help:examples
---
# exp:expuseredit

Ein Benutzerkonto ändern: E-Mail-Adresse, Anmeldename, Vor- und Nachname, hinzugefügte oder entfernte Gruppen, die Hauptgruppe (ein Probelauf ohne --apply).

Skript `bin/php/expuseredit.php`, Code in `kernel/private/classes/commands/expuseredit.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuseredit --help
./console exp:user:edit --help
php bin/php/expuseredit.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Change a user account
Changes the e-mail address and the login (checked as the administration checks them: valid, not
another account's), the first and last name (a new version, published with its workflows), and
the groups: --add-group adds a location in a group, --remove-group removes one (an account keeps
at least one), --main-group chooses the main one. Without --apply nothing is changed.

./console exp:user:edit jane --email=jane.doe@example.com --apply
./console exp:user:edit jane --add-group=Editors --remove-group=Members --main-group=Editors --apply
./console exp:user:edit 1234 --first-name=Janet --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--email` | erforderlich | Die E-Mail-Adresse (create: des neuen Kontos; edit: die neue Adresse) |
| `--login` | erforderlich | Der Anmeldename (create: des neuen Kontos; edit: der neue Anmeldename) |
| `--first-name` | erforderlich | Der Vorname (das Attribut first_name der Benutzerklasse) |
| `--last-name` | erforderlich | Der Nachname (das Attribut last_name der Benutzerklasse) |
| `--add-group` | erforderlich | Das Konto dieser Benutzergruppe hinzufügen (Knoten-ID oder Name; für mehrere wiederholen) |
| `--remove-group` | erforderlich | Das Konto aus dieser Benutzergruppe entfernen (Knoten-ID oder Name; für mehrere wiederholen); ein Konto behält mindestens eine |
| `--main-group` | erforderlich | Diese Gruppe (Knoten-ID oder Name, eine, in der das Konto Mitglied ist oder wird) zu seiner Hauptgruppe machen |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuseredit.php`, `kernel/private/classes/commands/expuseredit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
