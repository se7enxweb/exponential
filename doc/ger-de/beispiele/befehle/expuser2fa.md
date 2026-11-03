---
title: exp:expuser2fa
audience: developer, admin
category: examples
summary: "Anmeldung in zwei Schritten und Passkeys eines Kontos: anzeigen; den zweiten Schritt für jemanden zurücksetzen, der sein Telefon verloren hat, die Passkeys eines verlorenen Geräts entfernen (Änderungen sind ohne --apply ein Probelauf)."
keywords: Konsole, Befehl, exp:expuser2fa, expuser2fa, console, command, CLI, expuser2fa.php, exp:user:2fa
type: generated
generated: exp:help:examples
---
# exp:expuser2fa

Anmeldung in zwei Schritten und Passkeys eines Kontos: anzeigen; den zweiten Schritt für jemanden zurücksetzen, der sein Telefon verloren hat, die Passkeys eines verlorenen Geräts entfernen (Änderungen sind ohne --apply ein Probelauf).

Skript `bin/php/expuser2fa.php`, Code in `kernel/private/classes/commands/expuser2fa.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuser2fa --help
./console exp:user:2fa --help
php bin/php/expuser2fa.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Two-step sign-in and passkeys of an account
Shows whether the account has a second sign-in step (its method) and how many passkeys. --reset
removes the second step, for someone who lost the phone, and resets the failed sign-ins; where
two-step sign-in is enforced, the next sign-in asks for a new setup. It needs the two-step
sign-in extension (sevenx_authentication_2fa). --remove-passkeys removes every passkey of the
account, as an administrator does for a lost or stolen device. The audit records
access.user.two_factor.remove and access.passkey.revoke. Without --apply nothing is changed.

./console exp:user:2fa jane
./console exp:user:2fa jane --reset --apply
./console exp:user:2fa jane --remove-passkeys --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--reset` |  | Den zweiten Anmeldeschritt entfernen (ein verlorenes Telefon) und die fehlgeschlagenen Anmeldungen zurücksetzen; ist die Anmeldung in zwei Schritten vorgeschrieben, verlangt die nächste Anmeldung eine neue Einrichtung |
| `--remove-passkeys` |  | Jeden Passkey des Kontos entfernen (ein verlorenes oder gestohlenes Gerät) |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuser2fa.php`, `kernel/private/classes/commands/expuser2fa.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
