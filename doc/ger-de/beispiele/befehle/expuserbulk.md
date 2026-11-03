---
title: exp:expuserbulk
audience: developer, admin
category: examples
summary: Jedes Konto, das die Listenfilter auswählen, deaktivieren, aktivieren, löschen, freigeben, ablehnen oder entsperren, jedes so wie der einzelne Befehl; --apply braucht --confirm mit der Zahl, die der Probelauf gezeigt hat.
keywords: Konsole, Befehl, exp:expuserbulk, expuserbulk, console, command, CLI, expuserbulk.php, exp:user:bulk
type: generated
generated: exp:help:examples
---
# exp:expuserbulk

Jedes Konto, das die Listenfilter auswählen, deaktivieren, aktivieren, löschen, freigeben, ablehnen oder entsperren, jedes so wie der einzelne Befehl; --apply braucht --confirm mit der Zahl, die der Probelauf gezeigt hat.

Skript `bin/php/expuserbulk.php`, Code in `kernel/private/classes/commands/expuserbulk.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuserbulk --help
./console exp:user:bulk --help
php bin/php/expuserbulk.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
One action for many accounts
disable, enable, delete, approve, reject or unlock every account the filters of exp:user:list
select (--state, --group, --search, --days), at most --limit. Each account goes through the same
checks as the single command: protected accounts and the last administrator are listed and never
changed. The dry run lists the accounts and their number; --apply also needs --confirm=<that
number>, and runs only while the filter still selects exactly as many.

./console exp:user:bulk disable --state=inactive --days=730 --group=Members
./console exp:user:bulk disable --state=inactive --days=730 --group=Members --apply --confirm=42
./console exp:user:bulk approve --state=pending --search=example.edu --apply --confirm=17
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--state` | erforderlich | Nur Konten in diesem Zustand: all (Vorgabe), enabled, disabled, unactivated, pending, locked, never, inactive, orphaned, duplicate_login oder duplicate_email |
| `--group` | erforderlich | Eine Benutzergruppe per Knoten-ID oder Name: list, export und bulk nehmen nur ihre Mitglieder (unter ihr, in jeder Tiefe); create und import: die Gruppe der neuen Konten (für mehrere wiederholen, die erste ist die Hauptgruppe; Vorgabe [UserSettings] DefaultUserPlacement) |
| `--search` | erforderlich | Nur Konten, deren Anmeldename, Name oder E-Mail-Adresse diesen Text enthält |
| `--days` | erforderlich | Mit --state=inactive: seit so vielen Tagen nicht angemeldet (Vorgabe 180) |
| `--limit` | erforderlich | Wie viele aufgelistet werden (Vorgabe 25, höchstens 500); bulk: wie viele Konten ein Lauf höchstens ändern darf (Vorgabe 500, höchstens 5000) |
| `--confirm` | erforderlich | Mit --apply: die Zahl der Konten, die der Filter auswählt, wie sie der Probelauf gezeigt hat; die Aktion läuft nur, wenn sie noch stimmt |
| `--apply` |  | Die Änderung vornehmen; ohne die Option listet der Befehl nur auf, was er ändern würde (ein Probelauf) |
| `--dry-run` |  | Die Vorgabe, beibehalten für Skripte, die sie übergeben: auflisten, was sich ändern würde, nichts ändern |
| `--as` | erforderlich | Als dieser Benutzer handeln (Anmeldename, E-Mail-Adresse oder ID; Vorgabe der Benutzer aus [UserSettings] UserCreatorID); das Audit hält ihn als Handelnden fest |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuserbulk.php`, `kernel/private/classes/commands/expuserbulk.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
