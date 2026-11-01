---
title: "Sicherungspunkte und Rücknahme in der Praxis"
audience: developer, admin, software-engineer, hosting
category: migrations
order: 19
summary: Die Wege zurück nach einem Migrationslauf - die automatische Wiederherstellung nach einem Fehler, die Rücknahme eines ganzen Laufs, einer Migration mit ihrem down(), eines früheren Sicherungspunkts für Fachleute - mit den Befehlen, ihrer Ausgabe und dem, was jeder berührt.
keywords: Migration, Migrationen, Sicherungspunkt, Sicherungspunkte, Rücknahme, zurücknehmen, wiederherstellen, Sicherheits-Sicherungspunkt, Backup, migration, migrations, schema change, database change, caption, captions, rollback, roll back, restore, down(), safety caption, exp:migration:rollback, exp:migration:caption, exp:upgrade:captions, --expert, --confirm, --parts
featured: true
type: help
---
# Sicherungspunkte und Rücknahme in der Praxis

Jeder Migrationslauf mit `--apply` legt zuerst einen Sicherungspunkt an: einen sicheren Stand mit der Datenbank, dem
`var/`-Speicher und `settings/override`. Diese Seite geht die vier Wege zurück in der Reihenfolge durch, in der Sie sie
meist brauchen, an der News-Site der Rezepte. [Zurücknehmen und Sicherungspunkte](../../../anleitungen/migrationen-rueckgaengig-und-captions.md)
ist die Referenz.

## Was Sie am Ende haben

- einen gescheiterten Lauf gesehen, der automatisch wiederhergestellt wurde, und Sie wissen, wo sein gescheiterter
  Zustand aufbewahrt wird;
- einen ganzen Lauf, eine Migration und einen früheren Sicherungspunkt zurückgenommen;
- eine Checkliste für die Zeit nach einer Wiederherstellung.

## Bevor Sie beginnen

- Eine Entwicklungsinstallation mit eingeschalteten Sicherungspunkten (die Voreinstellung) und mindestens einer
  angewendeten Migration.
- Mit git_manager sind Sicherungspunkte dessen vollständige Sicherungspunkte; ohne ihn der Datenbank-Dump von
  `exp:upgrade` in `var/backup/upgrade/` (oder `[CaptionSettings] BackupDirectory`) ohne den Speicher.

## Schritte

### 1. Die Sicherungspunkte auflisten

```bash
./console exp:migration:caption --list
```

```text
Exponential migrations: the captions, newest first
  20261101-101502          migration-post    2026-11-01 10:15  sqlite     migration run 20261101-101500-3f9a1c in development post: 20261101100000_news_article_class
      ./console exp:migration:rollback 20261101-101502 --expert --confirm=20261101-101502 --apply
  20261101-101500          migration-pre     2026-11-01 10:15  sqlite     migration run 20261101-101500-3f9a1c in development pre: 1 migration(s) 20261101100000_news_article_class
      ./console exp:migration:rollback 20261101-101500-3f9a1c --apply
2 caption(s)
```

Es ist dieselbe Liste wie `./console exp:upgrade:captions`: Upgrades und Migrationen teilen ihre Sicherungspunkte.
Legen Sie vor einer riskanten Änderung einen von Hand an:
`./console exp:migration:caption --label="vor dem News-Import" --apply`.

### 2. Ein Fehler stellt den Sicherungspunkt von selbst wieder her

Wirft eine Migration eine Ausnahme, nimmt der Runner ihre Transaktion zurück, hält den gescheiterten Zustand in einem
Sicherungspunkt der Art `migration-failure` fest und stellt den Sicherungspunkt von vorher wieder her:

```text
  failed      20261101103000_news_import                    site               Imports the old news
              the import file is missing

Captions: pre 20261101-103001, failure 20261101-103001-2
Restore: restored (caption 20261101-103001)
WARN restored the caption 20261101-103001 taken before the run (database, storage, settings); the failed state is kept in the caption 20261101-103001-2
FAIL (exit 1)
```

Beheben Sie die Migration und starten Sie neu. Um zu sehen, was der Fehler hinterlassen hat, stellen Sie den
Fehler-Sicherungspunkt auf einer Kopie der Site wieder her, nie in Produktion.

### 3. Einen ganzen Lauf zurücknehmen

```bash
./console exp:migration:rollback 20261101-101500-3f9a1c
./console exp:migration:rollback 20261101-101500-3f9a1c --apply
```

Ohne `--apply` listet der Befehl, was er wiederherstellen würde. Mit `--apply`: zuerst ein Sicherheits-Sicherungspunkt
(`safety`) des aktuellen Stands (damit sich die Rücknahme selbst zurücknehmen lässt), dann die Teile von
`[CaptionSettings] RestoreParts[]` (`database`, `storage`, `settings`), dann die Prüfung. Die Zeile des Laufs bekommt
den Zustand `rolled-back`.

Alles nach dem Sicherungspunkt ist verloren, auch, was Redakteure seither veröffentlicht haben. Nehmen Sie einen Lauf
bald nach ihm zurück, oder nehmen Sie `down()`.

### 4. Eine Migration mit down() zurücknehmen

```bash
./console exp:migration:rollback 20261101100000_news_article_class --apply
```

```text
Exponential migrations: rollback with down()
Run 20261101-104210-a0b1c2, environment development, database sqlite (transactions: full), as admin

  undone      20261101100000_news_article_class             site               The class news_article
              removed the class news_article

Summary: 1 migration(s): 0 done, 1 pending, 0 not needed, 0 failed, 0 unknown; 1 undone
PASS
```

So lässt sich nur eine Migration im Zustand `done` mit einem `down()` zurücknehmen; ihre Aufzeichnung wird entfernt,
und sie steht wieder aus. Haben zwei Quellen dieselbe ID, ergänzen Sie `--source=site` oder
`--source=extension:<name>`.

### 5. Einen früheren Sicherungspunkt wiederherstellen (Fachleute)

```bash
./console exp:migration:rollback 20261031-193128 --expert --confirm=20261031-193128 --apply
./console exp:migration:rollback 20261031-193128 --expert --confirm=20261031-193128 --parts=database --apply
```

Jeder Sicherungspunkt der Liste lässt sich mit `--expert` und seiner in `--confirm` erneut getippten ID
wiederherstellen. `--parts` wählt Teile (`database`, `storage`, `settings`, `libraries`, `code`).
`--no-safety-caption` überspringt den Sicherheits-Sicherungspunkt; in Produktion wird das ohne `--force-no-caption`
abgelehnt. `--no-verify` überspringt die Prüfung.

### 6. Die Wiederherstellung live schalten

```bash
./console exp:upgrade:finish --apply
```

Der Befehl leert die Caches und lädt PHP-FPM und Velocity neu, damit kein Worker alte Klassen oder Seiten behält.

## Was jeder Weg zurück berührt

| | Wiederherstellung eines Sicherungspunkts (ein Lauf, ein früherer Sicherungspunkt, die automatische) | `down()` einer Migration |
|---|---|---|
| Datenbank | zurück auf den Dump; seither angelegte Tabellen werden gelöscht | was `down()` zurücknimmt |
| `var/`-Speicher | aus dem Sicherungspunkt von git_manager; seither hinzugekommene Dateien bleiben | was `down()` zurücknimmt |
| `settings/override` | zurückkopiert; seither hinzugekommene Dateien bleiben | was `down()` zurücknimmt |
| Caches, Suchindex | veraltet bis `exp:upgrade:finish --apply` | leeren, was die Migration berührt hat |
| Arbeit der Redakteure seither | verloren | bleibt |

## Engines

SQLite, PostgreSQL, MySQL/MariaDB und MongoDB werden von den Befehlen oben gesichert und wiederhergestellt. Oracle hat
keinen eingebauten Sicherungspunkt: exportieren Sie das Schema vor dem Lauf mit `expdp`. Bei einem Fehler hält der Lauf
mit `Restore: manual` an und gibt die Schritte aus: Wartungsmodus an, `impdp` Ihres Exports, `settings/override` und
`var/` zurücklegen, `./console exp:velocity deploy`, `./console exp:upgrade:verify --after`, Wartungsmodus aus.

## Prüfen

```bash
./console exp:migration:status
```

Der Verlauf führt den Lauf als `rolled-back`; das Audit-Protokoll hat `system.migration.rollback` mit dem Verb
`rollback-run`, `rollback-caption` oder `rollback`.

## Nächster Schritt

[Die Audit-Spur der Läufe](die-audit-spur-der-laeufe.md): wo jeder Lauf, Sicherungspunkt und jede Rücknahme
festgehalten wird.

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und Fehlerbehebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Zurücknehmen und Sicherungspunkte](../../../anleitungen/migrationen-rueckgaengig-und-captions.md),
  [Ein umkehrbares down() schreiben](ein-umkehrbares-down-schreiben.md),
  [Ausführen, Probelauf und Rücknahme](../ausfuehren-probelauf-und-rueckgaengig.md)
- [`exp:migration:rollback`](../../../beispiele/befehle/expmigrationrollback.md),
  [`exp:migration:caption`](../../../beispiele/befehle/expmigrationcaption.md),
  [`exp:upgrade:captions`](../../../beispiele/befehle/upgradecaptions.md),
  [migration.ini \[CaptionSettings\]](../../../beispiele/ini-einstellungen/migration/CaptionSettings.md)
- [Eine Datenbank wiederherstellen](../../datenbankadministratoren/eine-datenbank-wiederherstellen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
