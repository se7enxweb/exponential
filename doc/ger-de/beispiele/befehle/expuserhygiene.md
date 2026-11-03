---
title: exp:expuserhygiene
audience: developer, admin
category: examples
summary: "Die Zahlen von Benutzer > Hygiene: Konten je Zustand (deaktiviert, nicht aktiviert, wartend, gesperrt, nie angemeldet, inaktiv, verwaist, doppelte Anmeldenamen und Adressen), jeweils mit dem Befehl, der sie auflistet."
keywords: Konsole, Befehl, exp:expuserhygiene, expuserhygiene, console, command, CLI, expuserhygiene.php, exp:user:hygiene
type: generated
generated: exp:help:examples
---
# exp:expuserhygiene

Die Zahlen von Benutzer > Hygiene: Konten je Zustand (deaktiviert, nicht aktiviert, wartend, gesperrt, nie angemeldet, inaktiv, verwaist, doppelte Anmeldenamen und Adressen), jeweils mit dem Befehl, der sie auflistet.

Skript `bin/php/expuserhygiene.php`, Code in `kernel/private/classes/commands/expuserhygiene.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserhygiene --help
./console exp:user:hygiene --help
php bin/php/expuserhygiene.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The state of the accounts at a glance
Counts the accounts in each state of Users > Hygiene: all, enabled, disabled, unactivated, pending,
locked, never signed in, inactive (not signed in for --days days), orphaned, and logins and e-mail
addresses more than one account has; each with the exp:user:list command that lists them. Changes
nothing. Run it before a clean-up, and from a monitoring job with --format=json.

./console exp:user:hygiene
./console exp:user:hygiene --days=365 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--days` | erforderlich | Mit --state=inactive: seit so vielen Tagen nicht angemeldet (Vorgabe 180) |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserhygiene.php`, `kernel/private/classes/commands/expuserhygiene.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
