---
title: exp:expusershow
audience: developer, admin
category: examples
summary: "Ein Benutzerkonto anzeigen: Gruppen, Rollen mit ihrer Einschränkung, aktiviert oder gesperrt, letzter Besuch, Zustand der Registrierung, Anmeldung in zwei Schritten und aktive Sitzungen (nie ein Passwort-Hash)."
keywords: Konsole, Befehl, exp:expusershow, expusershow, console, command, CLI, expusershow.php, exp:user:show
type: generated
generated: exp:help:examples
---
# exp:expusershow

Ein Benutzerkonto anzeigen: Gruppen, Rollen mit ihrer Einschränkung, aktiviert oder gesperrt, letzter Besuch, Zustand der Registrierung, Anmeldung in zwei Schritten und aktive Sitzungen (nie ein Passwort-Hash).

Skript `bin/php/expusershow.php`, Code in `kernel/private/classes/commands/expusershow.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expusershow --help
./console exp:user:show --help
php bin/php/expusershow.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Show one user account
Names the account by its login, its e-mail address (when one account has it) or its id, and
shows its groups (the main one first), its roles with where they come from and their limitation,
whether it is enabled, unactivated or locked, its sign-ins and last visit, the state of its
registration, two-step sign-in, active sessions, whether a new password is required, and whether
the account is protected from being disabled or removed. Never a password hash. Changes nothing.

./console exp:user:show admin
./console exp:user:show jane@example.com --format=json
./console exp:user:show 14
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expusershow.php`, `kernel/private/classes/commands/expusershow.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
