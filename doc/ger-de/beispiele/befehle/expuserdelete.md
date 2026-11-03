---
title: exp:expuserdelete
audience: developer, admin
category: examples
summary: Ein Benutzerkonto endgültig entfernen, wie es die Administration tut; verweigert den anonymen Benutzer, die Administratorkonten, den handelnden Benutzer und den letzten Administrator (ein Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:expuserdelete, expuserdelete, console, command, CLI, expuserdelete.php, exp:user:delete
type: generated
generated: exp:help:examples
---
# exp:expuserdelete

Ein Benutzerkonto endgültig entfernen, wie es die Administration tut; verweigert den anonymen Benutzer, die Administratorkonten, den handelnden Benutzer und den letzten Administrator (ein Probelauf ohne --apply).

Skript `bin/php/expuserdelete.php`, Code in `kernel/private/classes/commands/expuserdelete.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserdelete --help
./console exp:user:delete --help
php bin/php/expuserdelete.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Remove a user account for good
Removes the account's locations as the administration does; accounts are never moved to the
trash (the administration refuses that too), so the account, its settings and its sessions go.
The content the person created stays. Refused for the anonymous user, the user of
[UserSettings] UserCreatorID, the administrator account, the user who acts and the last enabled
administrator. To keep the account but stop its use, see exp:user:disable. Without --apply
nothing is removed.

./console exp:user:delete jane
./console exp:user:delete jane --apply
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

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserdelete.php`, `kernel/private/classes/commands/expuserdelete.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
