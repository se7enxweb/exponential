---
title: exp:expuserchecklogin
audience: developer, admin
category: examples
summary: Prüfen, ob ein von der Standardeingabe gelesenes Passwort ein Konto anmeldet (richtiges Passwort, aktiviert, nicht gesperrt), für Skripte und Zustandsprüfungen; zählt nie als fehlgeschlagene Anmeldung.
keywords: Konsole, Befehl, exp:expuserchecklogin, expuserchecklogin, console, command, CLI, expuserchecklogin.php, exp:user:check-login
type: generated
generated: exp:help:examples
---
# exp:expuserchecklogin

Prüfen, ob ein von der Standardeingabe gelesenes Passwort ein Konto anmeldet (richtiges Passwort, aktiviert, nicht gesperrt), für Skripte und Zustandsprüfungen; zählt nie als fehlgeschlagene Anmeldung.

Skript `bin/php/expuserchecklogin.php`, Code in `kernel/private/classes/commands/expuserchecklogin.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserchecklogin --help
./console exp:user:check-login --help
php bin/php/expuserchecklogin.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Check a sign-in
Reads a password from standard input (--password-stdin, never from the command line) and says
whether it signs the account in: the right password, the account enabled and not locked. Exit 0
when it does, 1 when not, with the reason (wrong_password, disabled, locked, no_object). The check
does not sign in, opens no session, never counts as a failed sign-in and never prints the
password. Made for scripts and health checks of service accounts; a second sign-in step is not
checked.

./console exp:user:check-login ci-bot --password-stdin < /root/secrets/ci-bot-password
printf '%s\n' "$PASSWORD" | ./console exp:user:check-login ci-bot --password-stdin --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--password-stdin` |  | Das Passwort von der Standardeingabe lesen (eine Zeile), nie von der Befehlszeile (create und reset-password: es muss die Passwortregeln erfüllen) |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserchecklogin.php`, `kernel/private/classes/commands/expuserchecklogin.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
