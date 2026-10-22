---
title: "MongoDB: Grenzen und Betrieb"
audience: dba
category: database-administration
order: 18
summary: Was der MongoDB-Adapter von Exponential noch nicht kann, wie Sie unübersetztes SQL in den Logs finden, wie Upgrades ohne SQL-Update-Dateien funktionieren, Sicherungen mit mongodump, die Migrationsskripte in bin/mongodb und was Sie auf einer MongoDB-Website überwachen.
keywords: MongoDB, Grenzen, MONGO TODO, Adapter, unübersetztes SQL, eztags, ezflow, Shop, URL-Alias, Upgrade-Prüfung, Collections, createaudittables, mongodump, mongorestore, export_mysql.sh, mysql2ndjson.py, import_all.sh, validate_ndjson.sh, fixup_ndjson.py, limits, untranslated SQL, upgrade check
type: help
---
# MongoDB: Grenzen und Betrieb

Diese Seite richtet sich an Datenbankadministratoren, die entscheiden, ob MongoDB zu einer Website passt, oder die bereits
eine betreiben. Sie beantwortet: was der Adapter nicht unterstützt, wie Sie eine Funktion erkennen, die still nichts
zurückgibt, wie Upgrades und Schemaprüfungen ohne SQL funktionieren, wie Sie sichern und wie eine MySQL-Website nach MongoDB
umzieht. Quellen: [MongoDB als Datenbank](../../funktionen/6.0/mongodb-datenbankunterstuetzung.md),
[9.5 MongoDB](../../installation/09-datenbanken.md#95-mongodb) und Abschnitt 26 der
[Portierungsreferenz](../../bc/6.0/mongodb-kernel-unterstuetzung.md).

## Inhalt

1. [Wie der Adapter arbeitet](#wie-der-adapter-arbeitet)
2. [Bekannte Grenzen](#bekannte-grenzen)
3. [Unübersetztes SQL finden](#unübersetztes-sql-finden)
4. [Upgrades ohne SQL-Dateien](#upgrades-ohne-sql-dateien)
5. [Sicherungen](#sicherungen)
6. [Eine MySQL-Website nach MongoDB umziehen](#eine-mysql-website-nach-mongodb-umziehen)
7. [Leistung](#leistung)
8. [Checkliste](#checkliste)

## Wie der Adapter arbeitet

`expMongoDB` führt nie echtes SQL aus. Er zerlegt die SQL-förmigen Aufrufe des Kernels und macht daraus Operationen `find`,
`aggregate`, Insert, Upsert und Delete auf Collections, die wie die Tabellen des Kernels heißen (`ezcontentobject`,
`ezcontentobject_tree` und so weiter). Sein Schema-Handler ist `expMongoSchema` (`settings/dbschema.ini`, Typ `mongo`).
`eZDB::instance()->databaseName()` gibt `mongo` zurück.

## Bekannte Grenzen

- Handgeschriebenes SQL, das der Adapter nicht übersetzen kann, liefert ein **leeres Ergebnis** und protokolliert eine
  Warnung. Erweiterungen mit eigenem SQL können einen MongoDB-Weg brauchen.
- Die tiefe Neuerzeugung von URL-Aliasen nach dem Verschieben von Teilbäumen ist unvollständig.
- Listenseiten für Rollen und Richtlinien können leer sein, obwohl Berechtigungen durchgesetzt werden.
- Einige Cronjobs sind nicht portiert.
- `eztags` ist nicht verwendbar.
- Der Shop-Checkout und die Planung von `ezflow` sind nicht durchgängig getestet.
- Der SQL-Abfrage-Cache gilt nicht.
- Es gibt keine SQL-Update-Dateien.
- Fehler: `errorNumber()` meldet die Codes von MongoDB (`11000` für einen doppelten Schlüssel); der Treiber antwortet mit
  `false` und wirft nichts ([Fehlercodes und Meldungen der Datenbank](fehlercodes-und-meldungen.md)).

Testen Sie jede Erweiterung und jeden Workflow, den Ihre Website verwendet, auf einer Staging-Kopie, bevor Sie live gehen.

## Unübersetztes SQL finden

Durchsuchen Sie die Logs nach der Markierung, die der Adapter schreibt:

```bash
grep -rn "MONGO TODO" var/log/ | tail -20
```

Jeder Treffer nennt eine Anweisung, die statt Daten ein leeres Ergebnis geliefert hat. Eine Seite, die "nichts gefunden"
zeigt, wo andere Engines Inhalte zeigen, hat meist hier einen Treffer. Melden Sie Kernel-Anweisungen im
[Software-Tracker](https://github.com/se7enxweb/exponential/issues); korrigieren Sie das SQL Ihrer eigenen Erweiterungen
([SQL von Erweiterungen für jede Engine prüfen](portables-sql-fuer-erweiterungen.md)).

## Upgrades ohne SQL-Dateien

MongoDB hat kein Verzeichnis `update/database/`. Was es ersetzt:

- **Setup > Upgrade-Prüfung**, "Überprüfe Datenbankkonsistenz", vergleicht Collections statt Tabellen: fehlende
  Collections nach Funktion gruppiert, Collections, die nicht im Schema stehen, und die `mongosh`-Befehle, die die
  fehlenden anlegen ([Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md#6-die-prüfung-der-datenbankkonsistenz-ausführen)).
- `php update/common/scripts/6.0/createaudittables.php` legt die Audit-Index-Collections von 6.0.15 über den
  Schema-Handler an (zuerst `--dry-run`).
- Die Papierkorb-Dokumente erhalten beim Schreiben `trashed_by` und `trashed_via`; ältere werden mit ihren Standards
  gelesen, `movetrashrecords.php` läuft also wie auf den anderen Engines.
- Führen Sie `bin/mongodb/create_indexes.js` nach einem Upgrade erneut aus.

Siehe [11.3 Die Update-Dateien](../../installation/11-aktualisieren.md#113-die-update-dateien).

## Sicherungen

```bash
mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/$(date +%F)
mongorestore --uri="mongodb://exponential@localhost:27017/exponential" --drop /backup/2026-10-05
```

Die Werkzeuge fragen nach dem Passwort, wenn die URI keines enthält. Replica Sets, Oplog-basierte Sicherungen zu einem
Zeitpunkt und Snapshots sind eigene Funktionen von MongoDB und funktionieren wie mit jedem Client. Sichern Sie
`var/storage` und `settings/override` zusammen mit der Datenbank ([Sicherungen je Engine](sicherungen-je-engine.md)).

## Eine MySQL-Website nach MongoDB umziehen

Die Skripte in `bin/mongodb/` ziehen eine bestehende MySQL-Website um:

| Skript | Aufgabe |
|---|---|
| `export_mysql.sh` | schreibt jede Exponential-Tabelle aus MySQL als NDJSON (das Passwort kommt aus `MYSQL_PASSWORD`) |
| `mysql2ndjson.py` | der Konverter, den `export_mysql.sh` aufruft |
| `fixup_ndjson.py` | bearbeitet die Dateien nach: ganzzahlige `_id`s, `NULL`-Zeichenketten entfernt, bekannte Ganzzahlfelder umgewandelt |
| `validate_ndjson.sh` | prüft, dass jede Datei gültiges JSON ist, ein Objekt je Zeile; Exit-Code 1 bei Fehlern |
| `import_all.sh` | importiert die Dateien in der Reihenfolge ihrer Abhängigkeiten nach MongoDB |

Das Verfahren ist Abschnitt 18 der [Portierungsreferenz](../../bc/6.0/mongodb-kernel-unterstuetzung.md). Planen Sie ein
Wartungsfenster ein, legen Sie danach die Indizes an und führen Sie die Cronjobs aus
([Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md)).

## Leistung

Die Leistung folgt der Zahl der Anweisungen je Seite, weil jeder Kernel-Aufruf zu einer oder mehreren MongoDB-Operationen
wird. Die Indizes aus `create_indexes.js` sind der erste Hebel; die Caches vor der Datenbank der zweite
([Leistung und Skalierung](leistung-und-skalierung.md)).

## Checkliste

- [ ] Jede Erweiterung und jeder Workflow auf Staging getestet; kein `MONGO TODO` in den Logs nach einem vollständigen
      Durchklicken.
- [ ] `eztags`, Shop-Checkout und `ezflow` nicht gebraucht oder getestet.
- [ ] Indizes nach der Installation und nach jedem Upgrade angelegt.
- [ ] Upgrade-Prüfung nach jedem Upgrade ausgeführt; fehlende Collections angelegt.
- [ ] Sicherungen mit `mongodump` eingeplant und eine Wiederherstellung getestet.

## Verwandte Seiten

- [MongoDB einrichten](mongodb-einrichten.md)
- [Eine Engine wählen](eine-engine-waehlen.md)
- [MongoDB für Hoster](../hosting/grundlagen/mongodb-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
