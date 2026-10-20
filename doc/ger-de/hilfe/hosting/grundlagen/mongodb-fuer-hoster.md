---
title: MongoDB für Hoster
audience: hosting, admin
category: hosting
order: 90
summary: MongoDB als Datenbank einer Exponential-Website aus Sicht eines Hosters - die benötigten Versionen von PHP, Server und Erweiterung, die Composer-Bibliothek, der Datenbankbenutzer, die Installation, die Indizes, die dokumentierten Grenzen, Backups und Zahlen zur Dimensionierung.
keywords: MongoDB, mongodb, expMongoDB, MongoDB Client, mongodb/mongodb, PECL mongodb, PHP 8.2, MongoDB 6.0, mongosh, createUser, readWrite, dbAdmin, create_indexes.js, mongodump, mongorestore, Grenzen, MONGO TODO, eztags, Hosting, limits, hosting
type: help
---
# MongoDB für Hoster

Diese Seite ist für Systemadministratoren bei einem Hosting-Anbieter, deren Kunden Exponential auf MongoDB betreiben
möchten. Sie beantwortet: was MongoDB vom Server und von PHP braucht, wie Sie den Benutzer der Website anlegen, damit
sich der Adapter verbinden kann, welche Schritte die Installation über die üblichen hinaus braucht, welche Grenzen Sie
einem Kunden nennen sollten, bevor er sich dafür entscheidet, und wie Sie sichern. MongoDB ist die dritte in der
offiziellen Reihenfolge. Quellen: [Datenbankunterstützung](../../datenbankunterstuetzung.md#3-mongodb),
[Kapitel 9.5](../../../installation/09-datenbanken.md#95-mongodb) und
[MongoDB als Datenbank](../../../../eng-us/features/6.0/mongodb-database-support.md).

## Inhalt

1. [Was es ist](#was-es-ist)
2. [Anforderungen](#anforderungen)
3. [Den Benutzer anlegen](#den-benutzer-anlegen)
4. [Einstellungen und Installation](#einstellungen-und-installation)
5. [Grenzen, die Sie dem Kunden nennen sollten](#grenzen-die-sie-dem-kunden-nennen-sollten)
6. [Backups](#backups)
7. [Zahlen zur Dimensionierung](#zahlen-zur-dimensionierung)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Was es ist

Der Adapter `expMongoDB` (`lib/ezdb/classes/expmongodb.php`) übersetzt die SQL-artigen Aufrufe des Kernels in
MongoDB-Operationen `find`, `aggregate`, Einfügen, Upsert und Löschen. Er kam mit 6.0.14. Er ist die Wahl für
Organisationen, die auf MongoDB standardisiert haben; allen anderen empfiehlt das Projekt zuerst SQLite oder PostgreSQL.

## Anforderungen

| | Minimum | Getestet |
|---|---|---|
| PHP | **8.2** | 8.5 |
| MongoDB-Server | **6.0** (der Assistent prüft 4.0) | 8.3 |
| PHP-Erweiterung `mongodb` (PECL) | **1.18** | aktuell |
| Composer-Bibliothek | `mongodb/mongodb` | die CI installiert `^1.21 \|\| ^2.0` |

Zwei Dinge übersieht man leicht:

- **PHP 8.2.** Der Rest von Exponential läuft auf 8.0; eine MongoDB-Website braucht 8.2 oder später.
- **Die Bibliothek.** `composer.json` schlägt `mongodb/mongodb` nur vor; fügen Sie sie der Website mit
  `composer require mongodb/mongodb` hinzu. Die PECL-Erweiterung allein genügt nicht.

## Den Benutzer anlegen

Der Adapter verbindet sich immer mit `mongodb://<user>:<password>@<server>:<port>/<database>`, und MongoDB nutzt die
Datenbank in der URI als Authentifizierungsdatenbank. Legen Sie den Benutzer **in der eigenen Datenbank der Website** an:

```bash
mongosh --eval '
  db.getSiblingDB("<customer>").createUser({
    user: "<customer>",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "<customer>" }, { role: "dbAdmin", db: "<customer>" } ]
  })'
```

Die Datenbank selbst entsteht beim ersten Schreiben. Ein Benutzer ist auch auf einem Server ohne Zugriffskontrolle
nötig: Mit leerem `User` lehnt der Treiber die URI ab („'default' authentication mechanism requires a username“).

## Einstellungen und Installation

```ini
[DatabaseSettings]
DatabaseImplementation=mongodb
Server=localhost
Port=27017
User=<customer>
Password=<the password>
Database=<customer>
```

1. **Installieren Sie über die Konsole** oder geben Sie dem Assistenten Zeit: Sein Konfigurationsschritt dauert auf
   MongoDB ein bis drei Minuten, erhöhen Sie also zuerst `request_terminate_timeout` von PHP-FPM.

   ```bash
   EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=mongodb \
       --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
   ```

2. **Legen Sie die Indizes an**, auf die sich die Abfragen des Kernels verlassen; ohne sie durchsuchen Abfragen von
   Teilbäumen und Nachschlagen von URL-Aliasen ganze Collections:

   ```bash
   mongosh "mongodb://<customer>@localhost:27017/<customer>" --file bin/mongodb/create_indexes.js
   ```

3. **Führen Sie die Cronjobs einmal aus**, um den Suchindex aufzubauen: `php runcronjobs.php --allow-root-user`.

> **Tipp**
> Nehmen Sie den Schritt mit den Indizes direkt nach der Installation in Ihr Bereitstellungsskript auf. Eine
> MongoDB-Website ohne ihre Indizes funktioniert, aber langsam, und die Ursache ist von außen nicht offensichtlich.

## Grenzen, die Sie dem Kunden nennen sollten

Aus [Grenzen von MongoDB](../../datenbankunterstuetzung.md#grenzen-von-mongodb):

- Der Adapter führt nie echtes SQL aus. Handgeschriebenes SQL, das er nicht übersetzen kann, liefert ein leeres Ergebnis
  und protokolliert eine Warnung (suchen Sie in den Logs nach `MONGO TODO`). Erweiterungen mit eigenem SQL brauchen
  vielleicht einen Weg für MongoDB.
- Die tiefe Neuerzeugung von URL-Aliasen nach dem Verschieben von Teilbäumen ist unvollständig; Listen von Rollen und
  Richtlinien können leer sein, obwohl Berechtigungen durchgesetzt werden; manche Cronjobs sind nicht portiert; `eztags`
  ist nicht nutzbar; der Bestellabschluss im Shop und die Zeitplanung von `ezflow` sind nicht von Anfang bis Ende getestet.
- Der SQL-Abfrage-Cache gilt nicht.
- Es gibt keine SQL-Update-Dateien: Die Upgrade-Prüfung vergleicht Collections und gibt die `mongosh`-Befehle für
  fehlende aus.
- In der CI des Projekts ist MongoDB durch Unit-Tests des Adapters abgedeckt; die Live-Tests brauchen laufende Server und
  gehören nicht zur CI.

> **Warnung**
> Machen Sie diese Grenzen zum Teil der Tarifbeschreibung oder der Auftragsbestätigung. Ein Kunde, der sich auf `eztags`
> oder auf eine Erweiterung mit eigenem SQL verlässt, sollte eine andere Datenbank wählen.

## Backups

```bash
mongodump --uri="mongodb://<customer>@localhost:27017/<customer>" --out=/backup/<customer>
mongorestore --uri="mongodb://<customer>@localhost:27017/<customer>" --drop /backup/<customer>
```

Ein vollständiges Backup enthält außerdem `var/storage`, `settings/override` und `settings/siteaccess`
([Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung)).

## Zahlen zur Dimensionierung

Die einzigen Zahlen, die das Repository für eine MongoDB-Installation nennt, stehen in Abschnitt 24.1 der
[Referenz zur MongoDB-Portierung](../../../../eng-us/bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md): RAM mindestens 512 MB, empfohlen
2 GB; Festplatte mindestens 2 GB, empfohlen 10 GB. Die Leistung folgt der Anzahl der Anweisungen pro Seite; messen Sie
eine echte Website mit `php bin/php/console exp:benchmark` ([Speicher, CPU und Festplatte dimensionieren](speicher-cpu-und-festplatte-dimensionieren.md)).

## Checkliste

- [ ] PHP 8.2 oder später für die Website, im Web und auf der Kommandozeile.
- [ ] PECL `mongodb` 1.18 oder später, und `mongodb/mongodb` im Composer-Projekt der Website verlangt.
- [ ] MongoDB-Server 6.0 oder später.
- [ ] Ein Benutzer mit `readWrite` und `dbAdmin` in der eigenen Datenbank der Website.
- [ ] `bin/mongodb/create_indexes.js` nach der Installation ausgeführt.
- [ ] Die Grenzen dem Kunden mitgeteilt.
- [ ] Backups mit `mongodump` geplant und eine Wiederherstellung getestet.

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)
- [Datenbankunterstützung: MongoDB](../../datenbankunterstuetzung.md#3-mongodb)
- [Kapitel 9.5: MongoDB](../../../installation/09-datenbanken.md#95-mongodb)
- [MongoDB als Datenbank](../../../../eng-us/features/6.0/mongodb-database-support.md)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
