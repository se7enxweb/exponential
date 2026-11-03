---
title: exp:expuserapprove
audience: developer, admin
category: examples
summary: Eine wartende Registrierung freigeben (das Konto aktivieren oder seinen Freigabeeintrag annehmen und es veröffentlichen) oder mit --reject ablehnen, wie es Benutzer > Nicht aktiviert und der Freigabeeintrag tun (ein Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:expuserapprove, expuserapprove, console, command, CLI, expuserapprove.php, exp:user:approve
type: generated
generated: exp:help:examples
---
# exp:expuserapprove

Eine wartende Registrierung freigeben (das Konto aktivieren oder seinen Freigabeeintrag annehmen und es veröffentlichen) oder mit --reject ablehnen, wie es Benutzer > Nicht aktiviert und der Freigabeeintrag tun (ein Probelauf ohne --apply).

Skript `bin/php/expuserapprove.php`, Code in `kernel/private/classes/commands/expuserapprove.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserapprove --help
./console exp:user:approve --help
php bin/php/expuserapprove.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Approve or reject a waiting registration
A registration waits for the person to confirm the e-mail address, for an administrator to activate
the account, or for an approval item (exp:user:pending lists them). Approving activates the account
as Users > Unactivated does (the registration then completes: publish, the welcome mail), or accepts
the approval item and continues its workflow at once. --reject denies the item and removes the
account for good, as the reject link of the registration mail does; an active account is never
removed here. Without --apply nothing is changed.

./console exp:user:approve jane --apply
./console exp:user:approve spam-account --reject --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--reject` |  | Die Registrierung stattdessen ablehnen: ihre Freigabe verweigern und das Konto endgültig entfernen |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserapprove.php`, `kernel/private/classes/commands/expuserapprove.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
