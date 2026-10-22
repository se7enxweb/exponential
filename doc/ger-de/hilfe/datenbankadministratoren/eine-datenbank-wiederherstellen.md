---
title: Eine Datenbank wiederherstellen
audience: dba
category: database-administration
order: 29
summary: Das Verfahren zur Wiederherstellung der Datenbank einer Exponential-Website - Wartungsmodus, Velocity anhalten, der Wiederherstellungsbefehl je Engine, .restore von SQLite gegenüber dem Ersetzen der Datei, die danach zu leerenden Caches (einschließlich des SQL-Abfrage-Cache), Sequenzen bei PostgreSQL und Oracle und die Prüfungen vor dem Wiederöffnen - sowie wie Sie Wiederherstellungen testen.
keywords: Wiederherstellung, Datenbank wiederherstellen, Recovery, sqlite3 .restore, pg_restore, mongorestore, mysql restore, impdp, Data-Pump-Import, Wartungsmodus, Velocity anhalten, Caches leeren, querycache, Sequenzen, Wiederherstellungstest, Notfallwiederherstellung, restore, database restore, recovery, maintenance mode, clear caches, sequences, restore test, disaster recovery
type: help
---
# Eine Datenbank wiederherstellen

Diese Seite richtet sich an Datenbankadministratoren, die eine Exponential-Datenbank wiederherstellen, nach einem Ausfall,
einem misslungenen Upgrade oder für einen Wiederherstellungstest. Sie beantwortet: in welcher Reihenfolge Sie anhalten,
wiederherstellen und neu starten, welcher Befehl jede Engine wiederherstellt, was danach geleert werden muss und was Sie
prüfen, bevor die Website wieder öffnet. Quellen: [10.9 Wiederherstellung](../../installation/10-nach-der-installation.md#wiederherstellung) und
[SQLite sichern und wiederherstellen](../../installation/09-datenbanken.md#sqlite-sicherung-und--wiederherstellung).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Das Verfahren](#das-verfahren)
3. [Der Wiederherstellungsbefehl je Engine](#der-wiederherstellungsbefehl-je-engine)
4. [SQLite: .restore oder die Datei ersetzen](#sqlite-restore-oder-die-datei-ersetzen)
5. [Nach der Wiederherstellung](#nach-der-wiederherstellung)
6. [Prüfungen vor dem Wiederöffnen](#prüfungen-vor-dem-wiederöffnen)
7. [Wiederherstellungen testen](#wiederherstellungen-testen)
8. [Checkliste](#checkliste)

## Bevor Sie beginnen

- Wissen Sie, welche Sicherung Sie wiederherstellen und dass die Dateisicherung (`var/storage`, `settings/override`) vom
  selben Zeitpunkt stammt.
- Behalten Sie eine Kopie der aktuellen, defekten Datenbank: Sie brauchen vielleicht später etwas daraus.
- Informieren Sie die Redakteure: Alles, was nach der Sicherung veröffentlicht wurde, geht verloren.

## Das Verfahren

1. **Wartungsmodus an**, damit niemand schreibt:

   ```bash
   php bin/php/maintenance.php on --message="Restoring" --allow-root-user
   ```

2. **Unter Velocity den Server anhalten**, damit kein Worker die alte Datenbank hält:

   ```bash
   php bin/php/console exp:velocity stop --allow-root-user
   ```

3. **Die Datenbank wiederherstellen** (nächster Abschnitt) und die Dateien über die Installation entpacken; Eigentümer und
   Modus korrigieren.
4. **Jeden Cache leeren und die Autoloads neu erzeugen**:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. **Velocity starten, seinen Antwort-Cache leeren**, den Wartungsmodus ausschalten, Startseite und Administration öffnen:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --allow-root-user
   php bin/php/maintenance.php off --allow-root-user
   ```

## Der Wiederherstellungsbefehl je Engine

| Engine | Wiederherstellung |
|---|---|
| SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/exponential.db'"` |
| PostgreSQL | `pg_restore -U exponential -d exponential --clean exponential.dump` |
| MongoDB | `mongorestore --uri="mongodb://exponential@localhost:27017/exponential" --drop /backup/exponential` |
| MariaDB | `mariadb -u exponential -p exponential < exponential.sql` |
| Oracle | `php bin/php/console ext:ezoracle:datapump --import=db.dmp` oder `impdp` von Oracle |
| MySQL | `mysql -u exponential -p exponential < exponential.sql` |

## SQLite: .restore oder die Datei ersetzen

Prozesse halten die Datenbank offen: PHP-FPM-Worker, während sie eine Anfrage bearbeiten, Velocity-Worker während ihrer ganzen
Lebensdauer. Stellen Sie über die eigenen Sperren von SQLite wieder her, statt die Datei unter ihnen zu ersetzen. `.restore`
kopiert die Sicherung Seite für Seite unter einer Schreibsperre in die laufende Datenbank und gibt bei Erfolg nichts aus.

Meldet es einen Fehler (ein Schreiber hielt die Sperre, oder die Sicherung hat eine andere Seitengröße, die eine Datenbank im
WAL-Modus nicht übernehmen kann), ist die laufende Datenbank unverändert. Ersetzen Sie dann die Datei: Halten Sie jeden Prozess
an, der sie geöffnet haben kann (Velocity, PHP-FPM, Cron), verschieben Sie die alte Datenbank **und** ihre Dateien `-wal` und
`-shm` gemeinsam zur Seite, legen Sie die Sicherung an ihren Platz, korrigieren Sie Eigentümer und Modus
([SQLite-Dateien, Eigentümer und Rechte](sqlite-dateien-eigentuemer-und-rechte.md)) und starten Sie die Prozesse wieder.

## Nach der Wiederherstellung

- **Caches**: Sie enthalten noch Seiten, die aus den Inhalten vor der Wiederherstellung erzeugt wurden.
  `ezcache.php --clear-all` leert die Caches des Kernels einschließlich des SQL-Abfrage-Cache; um nach einem Schreibzugriff
  außerhalb von Exponential nur diesen zu leeren: `php bin/php/ezcache.php --clear-id=querycache --allow-root-user`
  ([Der SQL-Abfrage-Cache](sql-abfrage-cache.md)).
- **Sequenzen**: Ein vollständiges `pg_restore` stellt sie mit den Daten wieder her; nach der Wiederherstellung ausgewählter
  Tabellen korrigieren Sie sie ([PostgreSQL betreiben](postgresql-betrieb.md#sequenzen-korrigieren)). Auf Oracle meldet
  `ext:ezoracle:sequence-sync --dry-run` Sequenzen, die hinter ihren Tabellen zurückliegen.
- **Suchindex**: Waren die Tabellen des Suchindex nicht in der Sicherung, oder nach der Wiederherstellung in eine andere
  Engine, bauen Sie ihn mit `php bin/php/updatesearchindex.php --clean --allow-root-user` neu auf
  ([Die Tabellen des eingebauten Suchindex](tabellen-des-suchindex.md)).
- **MongoDB**: Führen Sie `bin/mongodb/create_indexes.js` aus, wenn die Indizes nicht wiederhergestellt wurden.

## Prüfungen vor dem Wiederöffnen

- Die Versionszeile passt zum Code:
  `SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';`
- Die Zeilenzahlen von `ezcontentobject`, `ezcontentobject_tree` und `ezcontentobject_attribute` sehen wie erwartet aus.
- Melden Sie sich in der Administration an, öffnen Sie einige Seiten, veröffentlichen Sie ein Testobjekt (eine
  Veröffentlichung übt die Schreibzugriffe, die ein Seitenaufruf nicht ausführt) und löschen Sie es.
- `var/log/error.log` zeigt nichts Neues.

## Wiederherstellungen testen

Testen Sie eine Wiederherstellung mindestens einmal und nach jedem größeren Upgrade auf einer anderen Maschine. Eine
praktische Übung:

1. Bringen Sie die Sicherung der letzten Nacht auf eine Staging-Maschine.
2. Stellen Sie sie mit diesem Verfahren wieder her; messen Sie die Zeit jedes Schritts.
3. Führen Sie die Prüfungen oben aus.
4. Notieren Sie die benötigte Zeit und was fehlte; das ist Ihre echte Wiederherstellungszeit.

Betriebsanleitungen für Hosting-Personal: [Wiederherstellung und Wiederherstellungstests](../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)
und [Aus einer Sicherung wiederherstellen](../hosting/runbooks/aus-einem-backup-wiederherstellen.md).

## Checkliste

- [ ] Wartungsmodus an; Velocity angehalten.
- [ ] Datenbank und Dateien vom selben Zeitpunkt wiederhergestellt.
- [ ] Alle Caches geleert, auch der Antwort-Cache von Velocity.
- [ ] Sequenzen, Suchindex und MongoDB-Indizes geprüft.
- [ ] Versionszeile, Zeilenzahlen, Anmeldung und eine Testveröffentlichung geprüft.
- [ ] Wartungsmodus aus.

## Verwandte Seiten

- [Sicherungen je Engine](sicherungen-je-engine.md)
- [Datenbankfehler beheben](datenbankfehler-beheben.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
