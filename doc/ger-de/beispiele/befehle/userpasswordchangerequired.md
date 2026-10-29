---
title: exp:userpasswordchangerequired
audience: developer, admin
category: examples
summary: "Passwortänderung bei der nächsten Anmeldung: zeigt die Markierung eines Benutzerkontos an, setzt sie oder löscht sie."
keywords: Konsole, Befehl, exp:userpasswordchangerequired, userpasswordchangerequired, console, command, CLI, userpasswordchangerequired.php, exp:user-password-change-required
type: generated
generated: exp:help:examples
---
# exp:userpasswordchangerequired

Passwortänderung bei der nächsten Anmeldung: zeigt die Markierung eines Benutzerkontos an, setzt sie oder löscht sie.

Skript `bin/php/userpasswordchangerequired.php`, Code in `kernel/private/classes/commands/userpasswordchangerequired.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:userpasswordchangerequired --help
./console exp:user-password-change-required --help
php bin/php/userpasswordchangerequired.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Change password at next sign-in
Shows, sets or clears the mark that sends a user to the password change form after
their next sign-in, before any other page, until they set a new password (which clears
it). Guide: doc/eng-us/features/6.0/password-change-at-next-sign-in.md

./console exp:user-password-change-required status --login=editor1
./console exp:user-password-change-required set --login=editor1
./console exp:user-password-change-required clear --user-id=14
./console exp:user-password-change-required status --login=editor1 --json

In demo mode (EXP_DEMO_MODE=1) the mark of an administrator is kept but ignored.
Exit code: 0 done, 1 no such user, 2 usage error.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--login` | erforderlich | Das Login des Kontos |
| `--user-id` | erforderlich | Die Benutzer-ID (Inhaltsobjekt-ID) des Kontos |
| `--json` |  | Ein JSON-Objekt statt des Textes |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[ACTION]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/userpasswordchangerequired.php`, `kernel/private/classes/commands/userpasswordchangerequired.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
