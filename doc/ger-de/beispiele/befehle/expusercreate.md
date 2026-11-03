---
title: exp:expusercreate
audience: developer, admin
category: examples
summary: "Ein Benutzerkonto über das Veröffentlichen von Inhalten anlegen, wie es die Administration tut (seine Workflows und die Freigabe neuer Konten laufen): Anmeldename, E-Mail-Adresse, Namen, Gruppen und ein Passwort von der Standardeingabe, ein erzeugtes oder die Aktivierungsmail ..."
keywords: Konsole, Befehl, exp:expusercreate, expusercreate, console, command, CLI, expusercreate.php, exp:user:create
type: generated
generated: exp:help:examples
---
# exp:expusercreate

Ein Benutzerkonto über das Veröffentlichen von Inhalten anlegen, wie es die Administration tut (seine Workflows und die Freigabe neuer Konten laufen): Anmeldename, E-Mail-Adresse, Namen, Gruppen und ein Passwort von der Standardeingabe, ein erzeugtes oder die Aktivierungsmail (ein Probelauf ohne --apply).

Skript `bin/php/expusercreate.php`, Code in `kernel/private/classes/commands/expusercreate.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expusercreate --help
./console exp:user:create --help
php bin/php/expusercreate.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Create a user account
Creates the account as a content object of a user class in its first group and publishes it with
the content/publish operation, exactly as the administration does: its workflows run, so where
new accounts need approval the account waits until it is approved. The other groups are added as
locations. Choose one way to the password: --password-stdin (one line on standard input, never
on the command line), --generate-password (printed once) or --send-activation (the account starts
disabled and gets the activation mail). Without --apply nothing is created: the command lists what
it would do and which checks fail.

./console exp:user:create --login=jane --email=jane@example.com --first-name=Jane --last-name=Doe \
    --group=Editors --generate-password --apply
printf '%s\n' "$PASSWORD" | ./console exp:user:create --login=ci-bot --email=ci@example.com --group=12 --password-stdin --apply
./console exp:user:create --login=member1 --email=member1@example.com --group=Members --send-activation --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--login` | erforderlich | Der Anmeldename (create: des neuen Kontos; edit: der neue Anmeldename) |
| `--email` | erforderlich | Die E-Mail-Adresse (create: des neuen Kontos; edit: die neue Adresse) |
| `--first-name` | erforderlich | Der Vorname (das Attribut first_name der Benutzerklasse) |
| `--last-name` | erforderlich | Der Nachname (das Attribut last_name der Benutzerklasse) |
| `--group` | erforderlich | Eine Benutzergruppe per Knoten-ID oder Name: list, export und bulk nehmen nur ihre Mitglieder (unter ihr, in jeder Tiefe); create und import: die Gruppe der neuen Konten (für mehrere wiederholen, die erste ist die Hauptgruppe; Vorgabe [UserSettings] DefaultUserPlacement) |
| `--class` | erforderlich | Die Benutzerklasse des neuen Kontos (Vorgabe die Klasse aus [UserSettings] UserClassID) |
| `--password-stdin` |  | Das Passwort von der Standardeingabe lesen (eine Zeile), nie von der Befehlszeile (create und reset-password: es muss die Passwortregeln erfüllen) |
| `--generate-password` |  | Ein Passwort erzeugen, das die Passwortregeln erfüllt, und es einmal ausgeben |
| `--send-activation` |  | Das Konto deaktiviert anlegen und die Aktivierungsmail senden; die Person setzt ihr Passwort mit „Passwort vergessen“ |
| `--disabled` |  | Das Konto deaktiviert anlegen (später mit exp:user:enable aktivieren) |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expusercreate.php`, `kernel/private/classes/commands/expusercreate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
