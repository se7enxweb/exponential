---
title: exp:expuserrequirepasswordchange
audience: developer, admin
category: examples
summary: Bei der nächsten Anmeldung des Kontos ein neues Passwort verlangen, oder diese Markierung mit --clear entfernen, wie es das Bearbeitungsformular des Kontos tut (ein Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:expuserrequirepasswordchange, expuserrequirepasswordchange, console, command, CLI, expuserrequirepasswordchange.php, exp:user:require-password-change
type: generated
generated: exp:help:examples
---
# exp:expuserrequirepasswordchange

Bei der nächsten Anmeldung des Kontos ein neues Passwort verlangen, oder diese Markierung mit --clear entfernen, wie es das Bearbeitungsformular des Kontos tut (ein Probelauf ohne --apply).

Skript `bin/php/expuserrequirepasswordchange.php`, Code in `kernel/private/classes/commands/expuserrequirepasswordchange.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserrequirepasswordchange --help
./console exp:user:require-password-change --help
php bin/php/expuserrequirepasswordchange.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Require a new password at the next sign-in
Marks the account, as "Change password at next sign-in" of its edit form does: after the next
sign-in the person must choose a new password before anything else. --clear removes the mark. The
audit records access.user.password.change_required.set or .clear. Without --apply nothing is
changed.

./console exp:user:require-password-change jane --apply
./console exp:user:require-password-change jane --clear --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--clear` |  | Stattdessen die Markierung entfernen: bei der nächsten Anmeldung wird kein neues Passwort verlangt |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserrequirepasswordchange.php`, `kernel/private/classes/commands/expuserrequirepasswordchange.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
