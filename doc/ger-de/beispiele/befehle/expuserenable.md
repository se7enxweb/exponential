---
title: exp:expuserenable
audience: developer, admin
category: examples
summary: Ein deaktiviertes Benutzerkonto wieder aktivieren, wie es Benutzereinstellungen bearbeiten tut; ein nie aktiviertes Konto wird stattdessen mit exp:user:approve freigegeben (ein Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:expuserenable, expuserenable, console, command, CLI, expuserenable.php, exp:user:enable
type: generated
generated: exp:help:examples
---
# exp:expuserenable

Ein deaktiviertes Benutzerkonto wieder aktivieren, wie es Benutzereinstellungen bearbeiten tut; ein nie aktiviertes Konto wird stattdessen mit exp:user:approve freigegeben (ein Probelauf ohne --apply).

Skript `bin/php/expuserenable.php`, Code in `kernel/private/classes/commands/expuserenable.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserenable --help
./console exp:user:enable --help
php bin/php/expuserenable.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Enable a user account
Enables a disabled account again through the setsettings operation of the user module, as Edit
user settings does. An account that was never activated (it still has its activation key) is not
enabled here: exp:user:approve finishes its registration, with the mails and steps that belong to
it. Without --apply nothing is changed.

./console exp:user:enable jane
./console exp:user:enable jane --apply
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

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserenable.php`, `kernel/private/classes/commands/expuserenable.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
