---
title: exp:expuserresetpassword
audience: developer, admin
category: examples
summary: "Das Passwort eines Benutzers zurücksetzen: den Link „Passwort zurücksetzen“ senden, ein erzeugtes Passwort setzen und einmal anzeigen oder eines von der Standardeingabe; auf Wunsch bei der nächsten Anmeldung ein neues verlangen (ein Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:expuserresetpassword, expuserresetpassword, console, command, CLI, expuserresetpassword.php, exp:user:reset-password
type: generated
generated: exp:help:examples
---
# exp:expuserresetpassword

Das Passwort eines Benutzers zurücksetzen: den Link „Passwort zurücksetzen“ senden, ein erzeugtes Passwort setzen und einmal anzeigen oder eines von der Standardeingabe; auf Wunsch bei der nächsten Anmeldung ein neues verlangen (ein Probelauf ohne --apply).

Skript `bin/php/expuserresetpassword.php`, Code in `kernel/private/classes/commands/expuserresetpassword.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserresetpassword --help
./console exp:user:reset-password --help
php bin/php/expuserresetpassword.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Reset a user's password
Choose one way: --send-link sends the mail of "Forgot password" with its one-time link (the
password stays until the person uses the link); --generate sets a generated password that passes
the password rules and prints it once; --password-stdin sets the password read from standard input
(never from the command line). --generate and --password-stdin end the account's sessions;
--require-change makes the person choose a new password at the next sign-in. The password is
never written to a log or the audit. In demo mode an administrator's password is never changed.
Without --apply nothing is changed and no mail is sent.

./console exp:user:reset-password jane --send-link --apply
./console exp:user:reset-password jane --generate --require-change --apply
./console exp:user:reset-password ci-bot --password-stdin --apply < secret-file
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--send-link` |  | Die Mail „Passwort zurücksetzen“ mit ihrem Einmal-Link senden (das Passwort bleibt, bis der Link benutzt wird) |
| `--generate` |  | Ein erzeugtes Passwort setzen, das die Passwortregeln erfüllt, es einmal ausgeben und die Sitzungen des Kontos beenden |
| `--password-stdin` |  | Das Passwort von der Standardeingabe lesen (eine Zeile), nie von der Befehlszeile (create und reset-password: es muss die Passwortregeln erfüllen) |
| `--require-change` |  | Das Konto muss bei seiner nächsten Anmeldung ein neues Passwort wählen |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserresetpassword.php`, `kernel/private/classes/commands/expuserresetpassword.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
