---
title: exp:expuserresendactivation
audience: developer, admin
category: examples
summary: Die Aktivierungsmail mit einem neuen Schlüssel erneut an ein Konto senden, das nie aktiviert wurde, wie es Benutzer > Nicht aktiviert tut (ein Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:expuserresendactivation, expuserresendactivation, console, command, CLI, expuserresendactivation.php, exp:user:resend-activation
type: generated
generated: exp:help:examples
---
# exp:expuserresendactivation

Die Aktivierungsmail mit einem neuen Schlüssel erneut an ein Konto senden, das nie aktiviert wurde, wie es Benutzer > Nicht aktiviert tut (ein Probelauf ohne --apply).

Skript `bin/php/expuserresendactivation.php`, Code in `kernel/private/classes/commands/expuserresendactivation.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserresendactivation --help
./console exp:user:resend-activation --help
php bin/php/expuserresendactivation.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Send the activation mail again
For an account that never activated (disabled, with its activation key): a new key replaces the
old one, so an older mail's link stops working, and the activation mail goes to the account's
address, as Resend in Users > Unactivated does. To activate the account without the person, use
exp:user:approve. Without --apply no mail is sent.

./console exp:user:resend-activation member1
./console exp:user:resend-activation member1 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserresendactivation.php`, `kernel/private/classes/commands/expuserresendactivation.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
