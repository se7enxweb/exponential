---
title: exp:expuserlist
audience: developer, admin
category: examples
summary: "Benutzerkonten auflisten: nach Zustand (aktiviert, deaktiviert, nicht aktiviert, wartend, gesperrt, nie angemeldet, inaktiv ...), Gruppe und Suche filtern, seitenweise."
keywords: Konsole, Befehl, exp:expuserlist, expuserlist, console, command, CLI, expuserlist.php, exp:user:list
type: generated
generated: exp:help:examples
---
# exp:expuserlist

Benutzerkonten auflisten: nach Zustand (aktiviert, deaktiviert, nicht aktiviert, wartend, gesperrt, nie angemeldet, inaktiv ...), Gruppe und Suche filtern, seitenweise.

Skript `bin/php/expuserlist.php`, Code in `kernel/private/classes/commands/expuserlist.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserlist --help
./console exp:user:list --help
php bin/php/expuserlist.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
List user accounts
Lists the accounts with their id, login, e-mail address, last visit, state and main group, the
same list as Users > Hygiene in the administration. Filter by state, by group (its members at
any depth) and by a text in the login, name or e-mail address; page with --limit and --offset.
Changes nothing.

./console exp:user:list
./console exp:user:list --state=disabled --group=Editors
./console exp:user:list --state=inactive --days=365 --sort=last_visit
./console exp:user:list --search=example.com --limit=100 --offset=100 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--state` | erforderlich | Nur Konten in diesem Zustand: all (Vorgabe), enabled, disabled, unactivated, pending, locked, never, inactive, orphaned, duplicate_login oder duplicate_email |
| `--group` | erforderlich | Eine Benutzergruppe per Knoten-ID oder Name: list, export und bulk nehmen nur ihre Mitglieder (unter ihr, in jeder Tiefe); create und import: die Gruppe der neuen Konten (für mehrere wiederholen, die erste ist die Hauptgruppe; Vorgabe [UserSettings] DefaultUserPlacement) |
| `--search` | erforderlich | Nur Konten, deren Anmeldename, Name oder E-Mail-Adresse diesen Text enthält |
| `--days` | erforderlich | Mit --state=inactive: seit so vielen Tagen nicht angemeldet (Vorgabe 180) |
| `--sort` | erforderlich | Die Reihenfolge: name (Vorgabe), login, email, last_visit, login_count oder id |
| `--order` | erforderlich | asc (Vorgabe) oder desc |
| `--limit` | erforderlich | Wie viele aufgelistet werden (Vorgabe 25, höchstens 500); bulk: wie viele Konten ein Lauf höchstens ändern darf (Vorgabe 500, höchstens 5000) |
| `--offset` | erforderlich | Zuerst so viele überspringen (die nächste Seite: --offset=25) |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserlist.php`, `kernel/private/classes/commands/expuserlist.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
