# Migrationen: Engines und Leistung

Diese Seite sagt, auf welchen Datenbank-Engines jeder Teil der Migrations-Engine getestet wird, wie die Tests in einem
Projekt und in der CI laufen, und was ein Lauf kostet: Zeit und Speicher von 1.000 Migrationen, eines großen
Datenschritts und eines langen Laufs mit Checkpoints, gemessen auf jeder Engine, und wie man selbst misst.

[Anleitungen](README.md) · [Migrationen](migrationen.md) · Siehe auch: [SQL und Einstellungen](migrationen-sql-und-einstellungen.md),
[Pakete, Deploys und lange Läufe](migrationen-pakete-deploy-und-lange-laeufe.md),
[Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)

## Die Engine-Matrix

Jede Familie der Migrations-Engine hat einen Satz von Prüfungen (ein PHP-Trait,
`tests/tests/kernel/classes/migration/expMigration<Familie>EngineChecks.php`), der unverändert auf jeder Engine läuft,
über drei Testklassen: `expMigration<Familie>SqliteTest` (SQLite im Prozess, in den normalen Testsuiten),
`expMigration<Familie>DatabaseTest` (MySQL/MariaDB, PostgreSQL und Oracle, Gruppe `database`) und
`expMigration<Familie>MongoDBTest` (MongoDB, Testsuite `mongodb-live`). Jede Klasse arbeitet auf einer eigenen
Datenbank mit dem Kernel-Schema und den Grunddaten und entfernt sie danach; keine Datenbank einer Installation wird
geöffnet.

| Familie | Was ihre Prüfungen abdecken |
|---|---|
| Runner | PHP-Migrationen in Reihenfolge, Aufzeichnung und Laufprotokoll, Transaktionen je Engine, Zurücknehmen mit `down()`, Stapel mit Checkpoint |
| Record | Die Tabellen `exp_migration` und `exp_migration_run`, der Zustand jeder Migration, Läufe |
| Caption | Ein Sicherungspunkt vor einem Lauf, seine Wiederverwendung, die automatische Wiederherstellung nach einem Fehler (`mysqldump`, `pg_dump`, die SQLite-Datei, `mongodump`); auf Oracle die Schritte von Hand |
| Command | Die Befehle `exp:migration:*` vollständig: status, migrate, Probelauf, rollback, resume |
| UpgradeSource | Die Upgrade-Schritte von `exp:upgrade` als Migrationen in derselben Aufzeichnung |
| Yaml | YAML-Migrationen: Kopf, Schritte, Referenzen, Bedingungen, der down-Teil |
| ContentSteps | Die Schritte für Inhalte und Orte in mehreren Sprachen |
| StructureSteps | Klassen, Benutzer, Rollen, Bereiche, Sprachen und Objektzustände |
| TagAliasTrashFileSteps | Tags, URL-Aliase, der Papierkorb, Dateien und Bilder |
| CacheSearchAutoloadSteps | Die Caches, der Suchindex und die Autoloads, die ein Lauf berührt hat |
| LayoutHttpMailCommandSteps | Layouts, HTTP-Anfragen, Mails und freigegebene Konsolenbefehle |
| SqlSettingsSteps | Die Schema- und Daten-DSL, SQL-Dateien je Engine, `.mongodb.json`, INI-Migrationen |
| Recording | In der Administration aufgezeichnete Änderungen, als Schritte einer Migration geschrieben |
| Graphql | Der Schritttyp `graphql`: eine gespeicherte Abfrage und eine Mutation als Benutzer des Laufs, markierte Ereignisse, eine gescheiterte Migration |
| Performance | Die Messungen unten (PF-1 bis PF-3) |

Das Ergebnis der Matrix vom 2.11.2026, jede Zelle die Zahl der bestandenen Tests, keiner gescheitert oder übersprungen
(„n. z.“: die Familie hat für diese Engine keine Klasse; die SQLite-Prüfungen der Aufzeichnung laufen ohne Datenbank
in `expMigrationRecordTest`). Hier lief PostgreSQL 13, weil sein `pg_dump` nicht älter als der Server sein darf; die CI
nimmt PostgreSQL 15 mit passendem Werkzeug. MongoDB braucht mehr offene Dateien, als Docker voreinstellt: mit der
Voreinstellung brach `mongod` ab, während `mongorestore` die Indizes eines Sicherungspunkts neu aufbaute
(`--ulimit nofile=64000:64000`).

| Familie | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 | Oracle 26ai Free |
|---|---|---|---|---|---|
| CacheSearchAutoloadSteps | pass 4 | pass 4 | pass 4 | pass 4 | pass 4 |
| Caption | pass 2 | pass 2 | pass 2 | pass 2 | pass 2 |
| Command | pass 3 | pass 3 | pass 3 | pass 3 | pass 3 |
| ContentSteps | pass 3 | pass 3 | pass 3 | pass 3 | pass 3 |
| Graphql | pass 2 | pass 2 | pass 2 | pass 2 | pass 2 |
| LayoutHttpMailCommandSteps | pass 2 | pass 2 | pass 2 | pass 2 | pass 2 |
| Performance | pass 3 | pass 3 | pass 3 | pass 3 | pass 3 |
| Record | n. z. | pass 1 | pass 1 | pass 1 | pass 1 |
| Recording | pass 3 | pass 3 | pass 3 | pass 3 | pass 4 |
| Runner | pass 3 | pass 3 | pass 3 | pass 3 | pass 3 |
| SqlSettingsSteps | pass 3 | pass 3 | pass 3 | pass 3 | pass 3 |
| StructureSteps | pass 3 | pass 3 | pass 3 | pass 3 | pass 3 |
| TagAliasTrashFileSteps | pass 4 | pass 4 | pass 4 | pass 4 | pass 4 |
| UpgradeSource | pass 2 | pass 2 | pass 2 | pass 2 | pass 2 |
| Yaml | pass 2 | pass 2 | pass 2 | pass 2 | pass 2 |

Was sich zwischen den Engines unterscheidet, ist so gewollt und wird als solches geprüft: PostgreSQL und SQLite nehmen
eine gescheiterte Migration vollständig zurück, Schemaänderungen eingeschlossen; MySQL/MariaDB nehmen die Zeilen
zurück, behalten aber eine Tabelle, die eine gescheiterte Migration angelegt hat (eine Schemaänderung wird sofort
festgeschrieben); MongoDB hat keine Transaktionen, was eine gescheiterte Migration oder ein Stapel geschrieben hat,
bleibt also, und die Wiederherstellung nach einem Fehler kommt aus dem Sicherungspunkt. Oracle (hier 26ai Free, über
die Erweiterung ezoracle, die das Test-Toolkit für eine DSN `oracle://` lädt) nimmt die Zeilen einer gescheiterten
Migration zurück und behält ihre Schemaänderungen, wie MySQL/MariaDB; anders als dort wird auch eine Zeile
zurückgenommen, die nach einem `CREATE TABLE` geschrieben wurde, weil die Schemaänderung für sich festgeschrieben wird
und die Transaktion weiterläuft. Oracle hat keinen eingebauten Dump und keine eingebaute Wiederherstellung: ein Lauf
geht ohne Sicherungspunkt weiter, mit einer Warnung, und gibt nach einem Fehler die genauen Schritte aus, mit denen man
die Datenbank von Hand wiederherstellt (`impdp` mit dem Konto und dem Verbindungs-String der Verbindung;
[Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md)); genau das prüft die Familie Caption
dort. Die Spalte Oracle lief am 2.11.2026 gegen Oracle AI Database 26ai Free in Docker, mit ezoracle 2.3.10.

### Die Matrix ausführen

SQLite läuft bei jedem Testlauf mit. Für die anderen Engines gibt man den Tests einen Server; die Namen der
Datenbanken folgen der Namensregel für Tests (sie enthalten `test`):

```
# MySQL/MariaDB oder PostgreSQL: jede Migrationsfamilie, eine frische Datenbank je Test
php tests/runtests.php --dsn "mysql://root@127.0.0.1/testdb" --db-per-test --group database tests/tests/kernel/classes/migration
php tests/runtests.php --dsn "postgresql://postgres@127.0.0.1/testdb" --db-per-test --group database tests/tests/kernel/classes/migration

# Oracle: ein Konto mit dem Wort test im Namen (sein Schema wird vor jedem Test geleert), der Dienst des Servers;
# der Treiber aus extension/ezoracle oder aus dem Ordner, den EXP_TEST_EZORACLE_PATH nennt
php tests/runtests.php --dsn "oracle://exptest_testdb:<passwort>@127.0.0.1:1521/FREEPDB1" --db-per-test --group database tests/tests/kernel/classes/migration

# MongoDB: jede Klasse legt eine Datenbank exp_test_migration_* an und löscht sie wieder
EXP_TEST_MONGO_URI="mongodb://127.0.0.1:27017" vendor/bin/phpunit --testsuite mongodb-live --group mongodb-live --filter '^expMigration'
```

YAML-Migrationen brauchen `symfony/yaml` und die GraphQL-Familie `webonyx/graphql-php` (beide in der
mitgelieferten `composer.json`); ohne sie werden diese Prüfungen übersprungen, in der CI schlagen sie fehl. Die
Prüfungen der Sicherungspunkte brauchen das Dump-Werkzeug der Engine im `PATH` (auf Oracle keines).

### In der CI

Der Workflow `phpunit.yml` führt die SQLite-Klassen im PHPUnit-Job aus, und sein Datenbank-Job die Gruppe
`database` auf MySQL 8.0 und PostgreSQL 15 sowie die Klassen `expMigration*` von `mongodb-live` auf MongoDB 7, jede
als Dienst des Jobs. Ein Projekt mit eigenen Migrationen richtet seine CI genauso ein: `exp:migration:check` über
seine Dateien, dann `exp:migration:migrate --dry-run` gegen eine Wegwerf-Datenbank jeder Engine, die es unterstützt.

## Leistung

Drei Prüfungen der Familie Performance messen, was die Engine kostet, auf jeder Engine gleich. Jede prüft, was der
Lauf getan hat, hält eine großzügige Obergrenze für ihre Zeit ein und schreibt ihre Messwerte als eine JSON-Zeile in
die Datei, die `EXP_MIGRATION_PERF_REPORT` nennt:

- **PF-1, 1.000 Migrationen:** 1.000 YAML-Migrationen mit je einem Datenschritt (jede zehnte setzt die vorige
  voraus): Suche, Ordnen und Prüfsummen; Laden; der Lauf, der alle anwendet (je eine Zeile der Aufzeichnung und ein
  Eintrag im Laufprotokoll); ein zweiter Lauf ohne etwas zu tun; der Status jeder Migration.
- **PF-2, ein großer Datenschritt:** eine Migration fügt 5.000 Zeilen ein, eine zweite ändert alle, eine dritte
  löscht sie.
- **PF-3, ein langer Lauf mit Checkpoints:** eine PHP-Migration setzt einen Wert in 5.000 Zeilen in Stapeln von 250
  mit einem Checkpoint nach jedem Stapel, wird durch einen Fehler bei Zeile 2.600 angehalten und macht im zweiten Lauf
  beim letzten Checkpoint weiter.

Die Zahlen vom 2.11.2026, PHP 8.5, auf einem gemeinsam genutzten Entwicklungsserver, der zu der Zeit durch andere
Arbeit stark ausgelastet war (jede Engine in einem Docker-Container auf derselben Maschine). Sie zeigen die
Größenordnung und das Verhältnis der Engines zueinander, keinen Benchmark:

**PF-1, 1.000 Migrationen**

| Messung | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 | Oracle 26ai Free |
|---|---:|---:|---:|---:|---:|
| Suche, Ordnen, Prüfsummen (s) | 0,041 | 0,067 | 0,065 | 0,092 | 0,043 |
| Laden (s) | 0,48 | 0,32 | 0,38 | 0,29 | 0,16 |
| Lauf, 1.000 angewendet (s) | 3,9 | 38,8 | 17,0 | 13,8 | 29,6 |
| je Migration (ms) | 3,9 | 38,8 | 17,0 | 13,8 | 29,6 |
| Zweiter Lauf, nichts zu tun (s) | 0,028 | 0,031 | 0,030 | 0,061 | 0,060 |
| Status jeder Migration, je eine Abfrage (s) | 0,5 | 7,6 | 4,0 | 5,6 | 24,1 |
| Speicher (MiB) | 11,3 | 8,2 | 12,0 | 12,3 | 11,7 |

**PF-2, ein großer Datenschritt**

| Messung | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 | Oracle 26ai Free |
|---|---:|---:|---:|---:|---:|
| 5.000 Zeilen einfügen (s) | 0,9 | 71,7 | 14,3 | 10,4 | 7,4 |
| Zeilen je Sekunde | 5.432 | 70 | 351 | 479 | 678 |
| Alle ändern (s) | 0,23 | 0,16 | 0,10 | 0,22 | 0,26 |
| Alle löschen (s) | 0,07 | 0,18 | 0,13 | 0,17 | 0,26 |
| Speicher (MiB) | 2,2 | 2,2 | 2,2 | 2,2 | 2,2 |

**PF-3, 5.000 Zeilen in Stapeln von 250 mit Checkpoints**

| Messung | SQLite | MySQL 8.0 | PostgreSQL 13 | MongoDB 7 | Oracle 26ai Free |
|---|---:|---:|---:|---:|---:|
| Checkpoint beim Fehler | 2.500 | 2.500 | 2.500 | 2.500 | 2.500 |
| Erster Lauf, angehalten (s) | 0,04 | 2,85 | 2,06 | 6,09 | 1,91 |
| Zweiter Lauf, fortgesetzt (s) | 0,06 | 3,00 | 3,01 | 7,22 | 1,20 |
| Zeilen je Sekunde | 42.808 | 833 | 829 | 346 | 2.087 |
| Speicher (MiB) | 0,8 | 0,8 | 0,8 | 0,8 | 0,9 |

Was sie sagen:

- **Der eigene Aufwand der Engine ist klein.** Suche, Ordnen und Prüfsummen von 1.000 Dateien dauern 0,04 bis
  0,09 s, deutlich unter dem Ziel von unter 1 s (ohne Datenbank auch von `expMigrationLocatorTest`, ML-07, geprüft).
  Ein zweiter Lauf über 1.000 Migrationen, der nichts zu tun hat, kostet 0,03 bis 0,06 s. Die Aufzeichnung jeder der
  1.000 einzeln zu lesen (eine Abfrage je Migration, wie PF-1 sie prüft) dauert 0,5 bis 24 s (Oracle am langsamsten);
  `exp:migration:status` liest sie mit einer Abfrage.
- **Ein Lauf kostet Millisekunden je Migration plus das, was die Migration tut.** 1.000 Migrationen, die fast nichts
  tun, anzuwenden dauerte 4 s auf SQLite, 14 s auf MongoDB, 17 s auf PostgreSQL, 30 s auf Oracle und 39 s auf MySQL
  (4 bis 39 ms je Migration): je Migration öffnet der Runner eine Transaktion und schreibt sie fest, schreibt die Zeile der
  Aufzeichnung und den Eintrag im Laufprotokoll, und jedes Festschreiben wartet auf die Platte. Eine Site mit hundert
  offenen Migrationen ist in wenigen Sekunden fertig.
- **Große Datenschritte hängen an der Datenbank, nicht an der Engine.** Das Einfügen von PF-2 ist eine Migration mit
  `transactional: no`, jede Zeile wird also einzeln festgeschrieben: 70 Zeilen je Sekunde auf dem ausgelasteten
  MySQL-Server, 5.400 auf SQLite. Ändern und Löschen aller Zeilen sind je eine Anweisung und dauern überall einen
  Bruchteil einer Sekunde. Ein großes Einfügen bleibt besser in der Transaktion der Migration (die Vorgabe), oder man
  schreibt eine PHP-Migration mit `batches()`, die stapelweise festschreibt (PF-3: 350 bis 2.100 Zeilen je Sekunde auf
  den Servern, jede Zeile eine eigene Anweisung).
- **Lange Läufe machen dort weiter, wo sie aufgehört haben.** PF-3 hält bei Zeile 2.600 an, der Checkpoint steht auf
  2.500, dem letzten festgeschriebenen Stapel; der zweite Lauf bearbeitet nur den Rest. Der Checkpoint wird nach jedem
  Stapel geschrieben, eine Unterbrechung kostet also höchstens einen Stapel.
- **Der Speicher bleibt flach.** Keine der Prüfungen brauchte mehr als 12,3 MiB über dem, was vorher belegt war;
  1.000 geladene Migrationen sind der größte Teil.

### Selbst messen

```
EXP_MIGRATION_PERF_REPORT=var/tmp/perf.jsonl vendor/bin/phpunit tests/tests/kernel/classes/migration/expMigrationPerformanceSqliteTest.php
EXP_MIGRATION_PERF_REPORT=var/tmp/perf.jsonl php tests/runtests.php --dsn "mysql://root@127.0.0.1/testdb" --db-per-test \
    --group database tests/tests/kernel/classes/migration/expMigrationPerformanceDatabaseTest.php
```

`EXP_MIGRATION_PERF_COUNT` ändert die Zahl der Migrationen von PF-1 (Vorgabe 1000), `EXP_MIGRATION_PERF_ROWS` die
Zeilen von PF-2 und PF-3 (Vorgabe 5000). Jede Zeile des Berichts nennt die Engine, die Prüfung, die PHP-Version und
die Sekunden und MiB jedes Teils. Um einen echten Lauf einer Site zu messen, meldet
`./console exp:migration:migrate --apply --format=json` die `duration_ms` jeder Migration.

## Verwandte Seiten

- [Migrationen](migrationen.md), [SQL und Einstellungen](migrationen-sql-und-einstellungen.md),
  [Pakete, Deploys und lange Läufe](migrationen-pakete-deploy-und-lange-laeufe.md),
  [Zurücknehmen und Sicherungspunkte](migrationen-rueckgaengig-und-captions.md), [Fehlerbehebung](migrationen-fehlerbehebung.md)
