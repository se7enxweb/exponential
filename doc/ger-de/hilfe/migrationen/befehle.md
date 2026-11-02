---
title: "Migrationsbefehle"
audience: developer, admin, software-engineer, php-developer, hosting
category: migrations
order: 20
summary: Jeder Befehl exp:migration in einer Tabelle - was er tut, wann man ihn braucht, seine meistgenutzten Optionen und ein Link auf seine erzeugte Referenzseite - mit den gemeinsamen Optionen, den Exit-Codes, der Maschinenausgabe und den exp:upgrade-Befehlen, die mit Migrationen zusammenarbeiten.
keywords: Migrationsbefehle, Konsole, Kommandozeile, Optionen, Exit-Codes, Probelauf, Sicherungspunkt, Rücknahme, exp:migration, exp:migration:status, exp:migration:migrate, exp:migration:dry-run, exp:migration:rollback, exp:migration:resume, exp:migration:caption, exp:migration:push, exp:migration:check, exp:migration:generate, exp:migration:convert, exp:migration:repo:split, console, command line, CLI, options, exit codes, json, exp-migration/1, --apply, --format, migration commands
type: help
---
# Migrationsbefehle

Die Migrationsbefehle laufen im Wurzelverzeichnis der Installation als `./console exp:migration:<name>` (dasselbe
Programm wie `php bin/php/expmigration<name>.php`). Führen Sie sie als der Benutzer aus, unter dem der Webserver läuft;
als root mit `--allow-root-user`. Jeder Befehl, der schreibt, ist ein **Probelauf**, bis er `--apply` bekommt. Die
vollständige Liste der Optionen jedes Befehls steht auf seiner erzeugten Referenzseite, erstellt aus den
Optionsdefinitionen des Befehls selbst.

## Inhalt

1. [Die Befehle](#die-befehle)
2. [Nach Aufgabe](#nach-aufgabe)
3. [Optionen, die die meisten Befehle teilen](#optionen-die-die-meisten-befehle-teilen)
4. [Exit-Codes und die Maschinenausgabe](#exit-codes-und-die-maschinenausgabe)
5. [Die exp:upgrade-Befehle, die mit Migrationen arbeiten](#die-expupgrade-befehle-die-mit-migrationen-arbeiten)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Befehle

| Befehl | Was er tut | Meistgenutzte Optionen | Referenz |
|---|---|---|---|
| `exp:migration:status` | jede Migration mit ihrem Zustand (`done`, `pending`, `not-needed`, `failed`, `unknown`), die verwaisten, die letzten Läufe, die wartenden Pushes, die Sperre | `--fail-on-pending`, `--runs`, `--environment` | [expmigrationstatus](../../beispiele/befehle/expmigrationstatus.md) |
| `exp:migration:migrate` | der Probelauf und mit `--apply` der Lauf: Sicherungspunkt, Migrationen der Reihe nach, Wiederherstellung bei einem Fehler, neuer Sicherungspunkt, Commit, Tag, Push | `--apply`, `--only`, `--path`, `--environment`, `--login`, `--no-commit`, `--no-push`, `--force --reason` | [expmigrationmigrate](../../beispiele/befehle/expmigrationmigrate.md) |
| `exp:migration:dry-run` | der Plan jeder ausstehenden Migration; `--simulate` führt sie in einer Transaktion aus, die zurückgerollt wird (PostgreSQL, SQLite), `--diff` zeigt, was sich geändert hat | `--simulate`, `--diff`, `--only`, `--path` | [expmigrationdryrun](../../beispiele/befehle/expmigrationdryrun.md) |
| `exp:migration:rollback` | nimmt einen Lauf (seinen Sicherungspunkt von vorher), eine Migration (ihr `down()`) oder, für Fachleute, einen beliebigen Sicherungspunkt zurück | `<run-id>`, `<migration-id>`, `--expert --confirm=<caption-id>`, `--parts` | [expmigrationrollback](../../beispiele/befehle/expmigrationrollback.md) |
| `exp:migration:resume` | ein neuer Lauf dessen, was ein gescheiterter oder unterbrochener Lauf ausstehend ließ | `--apply`, `--only` | [expmigrationresume](../../beispiele/befehle/expmigrationresume.md) |
| `exp:migration:caption` | ein Sicherungspunkt der Installation jetzt, oder die Liste der Sicherungspunkte | `--apply`, `--list`, `--label` | [expmigrationcaption](../../beispiele/befehle/expmigrationcaption.md) |
| `exp:migration:push` | pusht die Läufe, deren Push gescheitert ist | `--apply`, `--limit` | [expmigrationpush](../../beispiele/befehle/expmigrationpush.md) |
| `exp:migration:check` | die Prüfung der Migrationsdateien ohne Datenbank: Namen, Kopf, Reihenfolge, PHP-Syntax, die Klasse | `--path`, `--environment` | [expmigrationcheck](../../beispiele/befehle/expmigrationcheck.md) |
| `exp:migration:generate` | schreibt eine neue PHP-Migration aus dem Gerüst | `--skeleton <name>`, `--description`, `--author`, `--source=extension:<name>`, `--apply` | [expmigrationgenerate](../../beispiele/befehle/expmigrationgenerate.md) |
| `exp:migration:convert` | wandelt YAML-Migrationen des Referenz-Bundles in YAML-Migrationen von Exponential um; was sich nicht umwandeln lässt, steht in der neuen Datei | `<Datei oder Ordner>`, `--source=extension:<name>`, `--apply` | [expmigrationconvert](../../beispiele/befehle/expmigrationconvert.md) |
| `exp:migration:repo:split` | trennt den Migrationsordner mit seiner Historie in ein eigenes Repository ab | `--to`, `--remote`, `--branch`, `--apply` | [expmigrationreposplit](../../beispiele/befehle/expmigrationreposplit.md) |

## Nach Aufgabe

| Aufgabe | Befehle | Rezept |
|---|---|---|
| eine Migration schreiben | `exp:migration:generate --skeleton <name> --apply`, dann `exp:migration:check` | [das Tutorial](../../anleitungen/migrationen-tutorial.md) |
| sehen, was sich ändern würde | `exp:migration:migrate`, `exp:migration:dry-run --simulate --diff` | [Migrationen testen](rezepte/migrationen-testen.md) |
| die ausstehenden Migrationen anwenden | `exp:migration:migrate --apply` | [Mit Migrationen ausliefern](rezepte/mit-migrationen-ausliefern.md) |
| einen Pull-Request in CI absichern | `exp:migration:check`, `exp:migration:status --fail-on-pending` | [Migrationen testen](rezepte/migrationen-testen.md) |
| zurückgehen | `exp:migration:rollback <run-id> --apply`, `exp:migration:rollback <migration-id> --apply` | [Sicherungspunkte und Rücknahme in der Praxis](rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md) |
| einen unterbrochenen Lauf beenden | `exp:migration:resume --apply` | [Große Datenmigrationen](rezepte/grosse-datenmigrationen.md) |
| pushen, was das Remote nicht erreicht hat | `exp:migration:push --apply` | [Tags pushen](rezepte/tags-pushen.md) |
| von Hand einen Sicherungspunkt nehmen | `exp:migration:caption --apply --label="before the import"` | [Sicherungspunkte und Rücknahme in der Praxis](rezepte/sicherungspunkte-und-ruecknahme-in-der-praxis.md) |

## Optionen, die die meisten Befehle teilen

| Option | Bedeutung |
|---|---|
| `--apply` | die Änderungen vornehmen; ohne sie ein Probelauf, der nichts ändert |
| `--format=json` | die Maschinenausgabe `exp-migration/1` statt Text |
| `--environment=<name>` | die Migrationen für eine andere Umgebung als die der Installation zählen (`site.ini [AdminEnvironment] Level` oder `EXP_ENV` mit `Level=auto`) |
| `--login=<login>` | als dieser Benutzer laufen statt als `migration.ini [RunSettings] RunAsUser` |
| `--only=<ids>` | nur diese Migrationen (eine Komma-Liste oder wiederholt) |
| `--path=<dateien oder ordner>` | nur die Migrationen in diesen Dateien oder Ordnern; `exp:migration:check` prüft sie statt der Migrationsordner |
| `--run-id=<id>`, `--progress=<datei>` | den Lauf vor dem Start benennen und ihn über eine Datei mit JSON-Zeilen verfolgen (was die Administration nutzt) |

Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`,
`--quiet`, `--allow-root-user`).

## Exit-Codes und die Maschinenausgabe

| Code | Bedeutet |
|---|---|
| 0 | erledigt, oder nichts zu tun |
| 1 | eine Migration ist gescheitert, eine Prüfung hat ein Problem gefunden, die Sperre war belegt, eine geänderte Datei wurde abgelehnt, oder etwas steht mit `exp:migration:status --fail-on-pending` aus |
| 2 | Bedienung: eine Option ist falsch oder fehlt, oder nicht committeter Code hat einen Lauf verhindert |
| 3 | die Datenbank ist nicht erreichbar |
| 4 | interner Fehler |

`--format=json` schreibt `exp-migration/1`: den Lauf, die Umgebung, die Engine und ihren Transaktionsmodus, jede
Migration mit Zustand, Aktion und Meldungen, die Sicherungspunkte, das Repository, eine Zusammenfassung, die Fehler, die
Warnungen und den Exit-Code ([Die Maschinenausgabe](../../spezifikationen/6.0/migrationsdateiformat.md#die-maschinenausgabe)).

```bash
./console exp:migration:status --fail-on-pending --format=json > var/log/migration-status.json || echo "pending or failed"
```

## Die exp:upgrade-Befehle, die mit Migrationen arbeiten

Die Upgrade-Schritte des Kernels sind Migrationen der Quelle `kernel`, und `exp:upgrade` teilt die Aufzeichnung, die
Sperre und die Sicherungspunkte mit den Migrationen:

| Befehl | Mit Migrationen |
|---|---|
| [`exp:upgrade`](../../beispiele/befehle/upgrade.md) | legt die Aufzeichnungstabellen an (Schritt `6.0.15-migration-tables`); führt jede Stufe aus, auch `code`, `libraries` und `finish`, die ein Migrationslauf ihm überlässt |
| [`exp:upgrade:status`](../../beispiele/befehle/upgradestatus.md) | die Schritte und ihr Zustand; ein von einem Migrationslauf angewendeter Schritt ist auch hier `done` |
| [`exp:upgrade:captions`](../../beispiele/befehle/upgradecaptions.md) | listet die Sicherungspunkte von Upgrades und von Migrationsläufen (Arten `migration-pre`, `migration-post`, `migration-failure`) |
| [`exp:upgrade:rollback`](../../beispiele/befehle/upgraderollback.md) | stellt jeden Sicherungspunkt wieder her, auch einen, den ein Migrationslauf genommen hat |
| [`exp:upgrade:finish`](../../beispiele/befehle/upgradefinish.md) | nach einem Migrationslauf, der Schritte angewendet hat, oder nach einer Wiederherstellung: Deploy, Caches, Smoke-Check |
| [`exp:upgrade:verify`](../../beispiele/befehle/upgradeverify.md) | findet Abweichungen: einen als erledigt aufgezeichneten Schritt, dessen Wirkung fehlt |

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md), [Alle Rezepte](rezepte/README.md), [Häufige Fragen](faq.md)
- [Die Anleitung](../../anleitungen/migrationen.md), [das Tutorial](../../anleitungen/migrationen-tutorial.md),
  [PHP-Migrationen](../../anleitungen/migrationen-php.md),
  [Zurücknehmen und Sicherungspunkte](../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Das Migrations-Repository](../../anleitungen/migrationen-repositories.md)
- [In der Administration](in-der-administration.md), [Einstellungen](einstellungen.md),
  [Richtlinien, Ansichten und Ereignisse](richtlinien-ansichten-und-ereignisse.md)
- [Das Dateiformat](../../spezifikationen/6.0/migrationsdateiformat.md),
  [Die Funktion](../../funktionen/6.0/migrationen.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md),
  [Fehlermeldungen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Alle Konsolenbefehle](../../beispiele/befehle/README.md), [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
