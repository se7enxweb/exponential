---
title: Das Datenbank-Update auf 6.0.15 anwenden
audience: dba
category: database-administration
order: 31
summary: Ein Verfahren Schritt für Schritt für den Datenbankteil eines Upgrades von einem beliebigen 6.0.x-Release auf 6.0.15 - Sicherung, Wartungsmodus, die Datei dbupdate-6.0.0-6.0.15.sql je Engine, was sie hinzufügt, die Audit-Tabellen mit createaudittables.php, die Papierkorb-Einträge, die Versionsprüfung und die Sonderfälle von SQLite, Oracle, MongoDB und Websites, die von 5.x kommen.
keywords: 6.0.15, Upgrade, dbupdate-6.0.0-6.0.15.sql, Update-Datei, createaudittables.php, movetrashrecords.php, expaudit, expmail, expbookmark_folder, ezrss_export_opml_item, password_hash, trashed, trashed_by, 6.0.15stable, SQLite duplicate column, MongoDB, Oracle, upgrade, update file
type: help
---
# Das Datenbank-Update auf 6.0.15 anwenden

Diese Seite richtet sich an Datenbankadministratoren, die den Datenbankteil eines Upgrades von 6.0.0 bis 6.0.14 (oder einem
früheren Checkout der Linie 6.0.15) auf 6.0.15 anwenden. Sie ist die Datenbankseite von
[11.6 Von einem beliebigen 6.0.x bis heute](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand): Den Code stellt bereit,
wer die Website ausrollt, die Datenbank Sie. Folgen Sie den Schritten der Reihe nach.

## Inhalt

1. [Was das Update hinzufügt](#was-das-update-hinzufügt)
2. [Schritt 1: vorbereiten](#schritt-1-vorbereiten)
3. [Schritt 2: die Datei anwenden](#schritt-2-die-datei-anwenden)
4. [Schritt 3: die Audit-Tabellen](#schritt-3-die-audit-tabellen)
5. [Schritt 4: die Papierkorb-Einträge](#schritt-4-die-papierkorb-einträge)
6. [Schritt 5: prüfen](#schritt-5-prüfen)
7. [Sonderfälle](#sonderfälle)
8. [Checkliste](#checkliste)

## Was das Update hinzufügt

`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (MySQL, PostgreSQL, SQLite) hält die Version `6.0.15stable` fest und
fügt die Tabellen und Spalten der Linie hinzu:

| Funktion | Tabellen und Spalten |
|---|---|
| Fußzeile im PDF-Export | `ezpdf_export.show_footer`, `footer_text` |
| OPML- und Podcast-Exporte | `ezrss_export.opml_head`, `podcast_head`, Tabelle `ezrss_export_opml_item` |
| Audit-Index | `expaudit_cursor`, `expaudit_event`, `expaudit_file` |
| Lesezeichenordner | `expbookmark_folder`, `ezcontentbrowsebookmark.folder_id`, `priority` |
| E-Mail-Einstellungen | `expmail_category`, `expmail_consent_log`, `expmail_pending`, `expmail_preference`, `expmail_suppression` |
| Wer ein Objekt in den Papierkorb gelegt hat | `ezcontentobject_trash.trashed_by`, `trashed_via` |
| Persönliche API-Schlüssel | `expapikey` |

Auf MySQL und PostgreSQL nimmt sie außerdem, wo sie fehlen, die drei Änderungen der Datei von 5.4 auf 6.0.0 vor (das breitere
`ezuser.password_hash`, `ezcontentobject_trash.trashed` und auf PostgreSQL die Sequenznamen `<table>_<column>_seq`) und lässt
eine Datenbank, die sie hat, wie sie ist.

## Schritt 1: vorbereiten

```bash
php bin/php/maintenance.php on --message="Upgrading" --allow-root-user
```

Erstellen Sie eine Sicherung der Datenbank und von `settings/override` ([Sicherungen je Engine](sicherungen-je-engine.md)).
Lesen Sie die Versionszeile; sie sollte eine 6.0-Version unter `6.0.15stable` sein.

## Schritt 2: die Datei anwenden

```bash
mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
psql -U USER -d DATABASE -f update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
sqlite3 var/storage/sqlite3/exponential.db < update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

Führen Sie die Datei Ihrer Engine aus, **einmal, ganz, der Reihe nach**. Hat die Datenbank eine Spalte bereits, scheitert die
Anweisung und nennt sie; überspringen Sie nur diese Anweisung. Die MySQL-Datei hat keine Zeile `SET default_storage_engine`,
weil jede ihrer Tabellen ihre Engine nennt.

## Schritt 3: die Audit-Tabellen

Der Audit-Trail ist ab 6.0.15 standardmäßig eingeschaltet. Legen Sie seine Index-Tabellen an und indizieren Sie die bisher
geschriebenen Audit-Dateien:

```bash
php update/common/scripts/6.0/createaudittables.php --dry-run
php update/common/scripts/6.0/createaudittables.php
```

Das Skript funktioniert auf jeder Engine (MySQL, MariaDB, PostgreSQL, SQLite, Oracle und MongoDB, jeweils über ihren
Schema-Handler) und lässt bestehende Tabellen in Ruhe, es kann also erneut laufen. `--dry-run` zeigt, welche Tabellen fehlen;
`--no-index` legt nur die Tabellen an. Bricht es mit "The audit index classes are not in the autoload array" ab, führen Sie
zuerst `php bin/php/ezpgenerateautoloads.php -k` aus.

Dasselbe Verzeichnis enthält `createapikeytable.php`, das die Tabelle der persönlichen API-Schlüssel (`expapikey`) über den
Schema-Handler jeder Engine anlegt und eine bestehende Tabelle in Ruhe lässt. Die SQL-Update-Dateien von MySQL, PostgreSQL und
SQLite legen dieselbe Tabelle an; auf Oracle und MongoDB führen Sie das Skript aus (zuerst `--dry-run`)
([API-Schlüssel](../../anleitungen/api-schluessel.md#10-das-datenmodell-und-das-upgrade)).

## Schritt 4: die Papierkorb-Einträge

Solange `trashed_by` und `trashed_via` nicht existieren, schreibt der Kernel nach `<VarDir>/trash/trashed.json`, wer ein Objekt
in den Papierkorb gelegt hat. Verschieben Sie diese Einträge nach der Update-Datei in die Tabelle:

```bash
php update/common/scripts/6.0/movetrashrecords.php
```

Siehe [die Papierkorb-Ansicht](../../bc/6.0/papierkorb.md#wo-verschoben-von-gehalten-wird).

## Schritt 5: prüfen

```bash
php bin/php/console exp:checkdbfiles --no-verify-branches     # prints nothing
php bin/php/ezcache.php --clear-all --allow-root-user
```

- Die Versionszeile lautet `6.0.15stable`.
- **Setup > Upgrade-Prüfung**, "Überprüfe Datenbankkonsistenz", listet keine fehlende Tabelle
  ([Das Schema nach einem Upgrade prüfen](das-schema-nach-einem-upgrade-pruefen.md)).
- Melden Sie sich an, veröffentlichen Sie ein Testobjekt, legen Sie es in den Papierkorb und stellen Sie es wieder her.
- Wartungsmodus aus: `php bin/php/maintenance.php off --allow-root-user`.

## Sonderfälle

| Fall | Was zu tun ist |
|---|---|
| SQLite, Datei bereits angewendet | Ein zweiter Lauf scheitert bei jedem `ALTER TABLE` mit "duplicate column name"; die Datenbank ist bereits aktualisiert. Führen Sie die Datei nie absichtlich zweimal aus. |
| Eine Website aus 5.x, die eine ältere Kopie der Datei angewendet hat | Ihr können die drei Änderungen von 5.4 fehlen. Wenden Sie die Datei nicht erneut an: Wenden Sie die aktuelle Datei von 5.4 auf 6.0.0 Ihrer Engine an (sie kann erneut laufen) und setzen Sie dann die Versionszeile zurück: `UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';` |
| Oracle | Keine 6.0.15-SQL-Datei im Kernel; die Audit-Tabellen kommen aus `createaudittables.php`. Führen Sie danach `ext:ezoracle:schema-diff` aus. |
| MongoDB | Keine SQL-Datei; führen Sie `createaudittables.php` aus, dann die Upgrade-Prüfung für fehlende Collections und `create_indexes.js`. |
| `SET storage_engine=InnoDB;` scheitert | Eine Update-Datei von vor Oktober 2026; nehmen Sie die aktuelle Datei. |

## Checkliste

- [ ] Wartungsmodus an; Sicherung erstellt.
- [ ] Die 6.0.15-Datei der Engine einmal angewendet.
- [ ] `createaudittables.php` ausgeführt (zuerst trocken).
- [ ] `movetrashrecords.php` ausgeführt.
- [ ] Versionszeile `6.0.15stable`; Upgrade-Prüfung sauber; Testveröffentlichung erledigt.
- [ ] Caches geleert; Wartungsmodus aus.

## Verwandte Seiten

- [Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md)
- [Aktualisieren](../../anleitungen/aktualisieren.md)
- [Audit](../../bc/6.0/audit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
