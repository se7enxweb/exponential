---
title: exp:expusersessions
audience: developer, admin
category: examples
summary: "Die erfassten Sitzungen eines Kontos (Ihre Geräte): auflisten, alle oder eine beenden, wie es ein Administrator unter user/sessions tut (das Beenden ist ohne --apply ein Probelauf)."
keywords: Konsole, Befehl, exp:expusersessions, expusersessions, console, command, CLI, expusersessions.php, exp:user:sessions
type: generated
generated: exp:help:examples
---
# exp:expusersessions

Die erfassten Sitzungen eines Kontos (Ihre Geräte): auflisten, alle oder eine beenden, wie es ein Administrator unter user/sessions tut (das Beenden ist ohne --apply ein Probelauf).

Skript `bin/php/expusersessions.php`, Code in `kernel/private/classes/commands/expusersessions.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expusersessions --help
./console exp:user:sessions --help
php bin/php/expusersessions.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The sessions of an account
Lists the account's running sessions as Your devices shows them: id, how it signed in, device and
browser, the approximate address and the last activity. --end ends all of them, --session=<id>
ends one, as an administrator does on user/sessions; the person has to sign in again there. The
audit records access.session.revoke. Without --apply nothing is ended.

./console exp:user:sessions jane
./console exp:user:sessions jane --end --apply
./console exp:user:sessions jane --session=812 --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--end` |  | Jede erfasste Sitzung des Kontos beenden (Ihre Geräte) |
| `--session` | erforderlich | Nur die Sitzung mit dieser ID beenden (aus der Liste) |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expusersessions.php`, `kernel/private/classes/commands/expusersessions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
