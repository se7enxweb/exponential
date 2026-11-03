---
title: exp:expuserunlock
audience: developer, admin
category: examples
summary: "Ein nach zu vielen falschen Passwörtern gesperrtes Konto entsperren: seine fehlgeschlagenen Anmeldungen zurück auf 0, wie es Benutzer > Hygiene tut (ein Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:expuserunlock, expuserunlock, console, command, CLI, expuserunlock.php, exp:user:unlock
type: generated
generated: exp:help:examples
---
# exp:expuserunlock

Ein nach zu vielen falschen Passwörtern gesperrtes Konto entsperren: seine fehlgeschlagenen Anmeldungen zurück auf 0, wie es Benutzer > Hygiene tut (ein Probelauf ohne --apply).

Skript `bin/php/expuserunlock.php`, Code in `kernel/private/classes/commands/expuserunlock.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserunlock --help
./console exp:user:unlock --help
php bin/php/expuserunlock.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Unlock an account
An account is locked after more wrong passwords than [UserSettings] MaxNumberOfFailedLogin. This
sets its failed sign-ins back to 0, as Users > Hygiene does, so the person can sign in again;
the password stays. The audit records access.user.unlock. Without --apply nothing is changed.

./console exp:user:unlock jane
./console exp:user:unlock jane --apply
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

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserunlock.php`, `kernel/private/classes/commands/expuserunlock.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
