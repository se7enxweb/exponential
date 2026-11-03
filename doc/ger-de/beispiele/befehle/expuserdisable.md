---
title: exp:expuserdisable
audience: developer, admin
category: examples
summary: Ein Benutzerkonto deaktivieren und seine Sitzungen beenden, wie es Benutzereinstellungen bearbeiten tut; verweigert die geschützten Konten und den letzten Administrator (ein Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:expuserdisable, expuserdisable, console, command, CLI, expuserdisable.php, exp:user:disable
type: generated
generated: exp:help:examples
---
# exp:expuserdisable

Ein Benutzerkonto deaktivieren und seine Sitzungen beenden, wie es Benutzereinstellungen bearbeiten tut; verweigert die geschützten Konten und den letzten Administrator (ein Probelauf ohne --apply).

Skript `bin/php/expuserdisable.php`, Code in `kernel/private/classes/commands/expuserdisable.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserdisable --help
./console exp:user:disable --help
php bin/php/expuserdisable.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Disable a user account
Disables the account through the setsettings operation of the user module, as Edit user settings
and Users > Hygiene do, and ends all its sessions: the person can no longer sign in, and nothing
of the account or its content is removed. exp:user:enable undoes it. Refused for the anonymous
user, the user of [UserSettings] UserCreatorID, the administrator account, the user who acts and
the last enabled administrator. Without --apply nothing is changed.

./console exp:user:disable jane
./console exp:user:disable jane --apply
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

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserdisable.php`, `kernel/private/classes/commands/expuserdisable.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
