---
title: exp:expuserpending
audience: developer, admin
category: examples
summary: "Die wartenden Registrierungen auflisten: auf die Bestätigung der E-Mail-Adresse, auf einen Administrator oder auf einen Freigabeeintrag, die neuesten zuerst."
keywords: Konsole, Befehl, exp:expuserpending, expuserpending, console, command, CLI, expuserpending.php, exp:user:pending
type: generated
generated: exp:help:examples
---
# exp:expuserpending

Die wartenden Registrierungen auflisten: auf die Bestätigung der E-Mail-Adresse, auf einen Administrator oder auf einen Freigabeeintrag, die neuesten zuerst.

Skript `bin/php/expuserpending.php`, Code in `kernel/private/classes/commands/expuserpending.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserpending --help
./console exp:user:pending --help
php bin/php/expuserpending.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
List the registrations that wait
Lists the accounts whose registration is not finished, with what each waits for: email (the
person has not confirmed the address), administrator (an administrator activates it) or workflow
(an approval item waits for an approver). exp:user:approve approves or rejects one. Changes
nothing; the same as exp:user:list --state=pending.

./console exp:user:pending
./console exp:user:pending --search=example.org --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--search` | erforderlich | Nur Konten, deren Anmeldename, Name oder E-Mail-Adresse diesen Text enthält |
| `--limit` | erforderlich | Wie viele aufgelistet werden (Vorgabe 25, höchstens 500); bulk: wie viele Konten ein Lauf höchstens ändern darf (Vorgabe 500, höchstens 5000) |
| `--offset` | erforderlich | Zuerst so viele überspringen (die nächste Seite: --offset=25) |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserpending.php`, `kernel/private/classes/commands/expuserpending.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
