---
title: exp:expuseraudit
audience: developer, admin
category: examples
summary: "Das Benutzer-Auditprotokoll eines Kontos (Benutzer > Audit): was mit ihm geschah und was es tat, das Neueste zuerst, gefiltert nach Ereignis, Ergebnis und Tagen."
keywords: Konsole, Befehl, exp:expuseraudit, expuseraudit, console, command, CLI, expuseraudit.php, exp:user:audit
type: generated
generated: exp:help:examples
---
# exp:expuseraudit

Das Benutzer-Auditprotokoll eines Kontos (Benutzer > Audit): was mit ihm geschah und was es tat, das Neueste zuerst, gefiltert nach Ereignis, Ergebnis und Tagen.

Skript `bin/php/expuseraudit.php`, Code in `kernel/private/classes/commands/expuseraudit.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expuseraudit --help
./console exp:user:audit --help
php bin/php/expuseraudit.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The user audit log of an account
Lists the rows of the user audit log (Users > Audit, the table expuser_audit) about the account or
by it, newest first: time, event, result, who acted, about whom, and the siteaccess. Filter by an
event name or prefix, a result and a range of days. The values before and after are in the JSON
output; passwords and secrets are never in the log. Changes nothing.

./console exp:user:audit jane
./console exp:user:audit jane --event=access.user.password --from=2026-10-01
./console exp:user:audit jane --result=refused --limit=100 --format=json
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--event` | erforderlich | Nur Ereignisse mit diesem Namen oder einem Namen darunter (access.user.password) |
| `--result` | erforderlich | Nur Ereignisse mit diesem Ergebnis: success, refused oder failed |
| `--from` | erforderlich | Nur Ereignisse an oder nach diesem Tag (JJJJ-MM-TT) |
| `--to` | erforderlich | groups move: die Gruppe, unter die sie verschoben wird (Knoten-ID oder Name); audit: nur Ereignisse an oder vor diesem Tag (JJJJ-MM-TT) |
| `--limit` | erforderlich | Wie viele aufgelistet werden (Vorgabe 25, höchstens 500); bulk: wie viele Konten ein Lauf höchstens ändern darf (Vorgabe 500, höchstens 5000) |
| `--offset` | erforderlich | Zuerst so viele überspringen (die nächste Seite: --offset=25) |
| `--format` | erforderlich | text (Vorgabe) oder json (die maschinenlesbare Ausgabe exp-user/1) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[user]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expuseraudit.php`, `kernel/private/classes/commands/expuseraudit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
