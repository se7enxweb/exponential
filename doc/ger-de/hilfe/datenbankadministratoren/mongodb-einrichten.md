---
title: MongoDB einrichten
audience: dba
category: database-administration
order: 17
summary: Wie Sie MongoDB für eine Exponential-Website vorbereiten - die Anforderungen, ein Benutzer in der eigenen Datenbank der Website mit readWrite und dbAdmin, warum ein Benutzer auch ohne Zugriffskontrolle nötig ist, der Einstellungsblock, die Installation über die Konsole, die Indizes aus bin/mongodb/create_indexes.js und der erste Lauf der Cronjobs.
keywords: MongoDB, mongosh, createUser, readWrite, dbAdmin, Authentifizierungsdatenbank, Verbindungs-URI, expMongoDB, Erweiterung mongodb, mongodb/mongodb, MongoDB\Client, create_indexes.js, exp:install --db=mongodb, request_terminate_timeout, Einrichtung, authentication database, connection URI, setup
type: help
---
# MongoDB einrichten

Diese Seite richtet sich an Datenbankadministratoren, die MongoDB für eine Exponential-Website vorbereiten. MongoDB wird über
einen Adapter unterstützt, `expMongoDB` (`lib/ezdb/classes/expmongodb.php`), der die SQL-förmigen Aufrufe des Kernels in
MongoDB-Operationen übersetzt; er kam mit 6.0.14. Diese Seite beantwortet: was der Adapter braucht, wie Sie den Benutzer
anlegen, wie die Einstellungen aussehen, wie Sie installieren und welche Indizes Sie anlegen. Lesen Sie
[MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md), bevor Sie MongoDB für die Produktion wählen. Quelle:
[9.5 MongoDB](../../installation/09-datenbanken.md#95-mongodb).

## Inhalt

1. [Anforderungen](#anforderungen)
2. [Den Benutzer anlegen](#den-benutzer-anlegen)
3. [Die Einstellungen](#die-einstellungen)
4. [Installieren](#installieren)
5. [Die Indizes anlegen](#die-indizes-anlegen)
6. [Den Suchindex aufbauen](#den-suchindex-aufbauen)
7. [Checkliste](#checkliste)

## Anforderungen

| | Minimum | Getestet |
|---|---|---|
| PHP | 8.2 | 8.5 |
| MongoDB-Server | 6.0 (der Assistent prüft 4.0) | 8.3 |
| PHP-Erweiterung `mongodb` | 1.18 | 2.3.3 |
| Composer-Bibliothek `mongodb/mongodb` | von Ihnen installiert (`composer.json` schlägt sie nur vor) | `^1.21 \|\| ^2.0` in der CI |

Ohne die Bibliothek lautet der Fehler `Class "MongoDB\Client" not found`
([PHP-Erweiterungen je Engine](php-erweiterungen-je-engine.md#mongodb-erweiterung-und-bibliothek)).

## Den Benutzer anlegen

Der Adapter verbindet sich mit der URI `mongodb://<user>:<password>@<server>:<port>/<database>`. MongoDB verwendet die
Datenbank im Pfad der URI als Authentifizierungsdatenbank, wenn kein `authSource` angegeben ist, legen Sie den Benutzer also
**in der Datenbank der Website** an:

```bash
mongosh --eval '
  db.getSiblingDB("exponential").createUser({
    user: "exponential",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "exponential" }, { role: "dbAdmin", db: "exponential" } ]
  })'
```

`passwordPrompt()` fragt nach dem Passwort, statt es in die Shell-Historie zu schreiben. `readWrite` deckt die Arbeit der
Website ab; `dbAdmin` lässt den Schema-Handler (`expMongoSchema`) Collections und Indizes anlegen. Die Datenbank selbst
entsteht durch den ersten Schreibzugriff; es gibt kein `CREATE DATABASE`.

**Der Benutzer ist erforderlich**, auch auf einem Testserver ohne Zugriffskontrolle: Der Adapter schreibt immer
`<user>:<password>@` in die URI, und mit leerem `User` lehnt der Treiber sie ab mit "Failed to parse MongoDB URI: ...
'default' authentication mechanism requires a username".

## Die Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=mongodb
Server=localhost
# empty: 27017
Port=27017
User=exponential
Password=<the password>
Database=exponential
```

Benutzername und Passwort werden vom Adapter URL-kodiert, Sonderzeichen sind also sicher. Ein Client je Verbindungs-URI wird
für die Lebensdauer des Prozesses behalten, ein Velocity-Worker verwendet seine Verbindungen also wieder.

## Installieren

Auf MongoDB kann der Konfigurationsschritt des Einrichtungsassistenten ein bis drei Minuten dauern. Erhöhen Sie vorher
`request_terminate_timeout` von PHP-FPM, oder installieren Sie über die Konsole:

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=mongodb \
    --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
```

`--db=mongo` wird ebenfalls akzeptiert; der Kickstarter nimmt `[database_choice] Type=mongodb`.

## Die Indizes anlegen

Die Abfragen des Kernels verlassen sich auf Indizes, die die Installation nicht anlegt. Ohne sie durchsuchen
Teilbaumabfragen und URL-Alias-Suchen ganze Collections:

```bash
mongosh "mongodb://exponential@localhost:27017/exponential" --file bin/mongodb/create_indexes.js
```

`mongosh` fragt nach dem Passwort. Das Skript enthält 45 Aufrufe von `createIndex` auf den Collections für Inhalte, Baum,
URL-Aliase und andere, mehrere davon eindeutig (zum Beispiel `ezcontentobject.id`, `ezcontentobject.remote_id`,
`ezcontentobject_tree.node_id`). Führen Sie es nach einem Upgrade, das Collections hinzufügt, erneut aus; bestehende
Indizes lässt MongoDB, wie sie sind.

## Den Suchindex aufbauen

Führen Sie die Cronjobs einmal aus, um den eingebauten Suchindex aufzubauen:

```bash
php runcronjobs.php --allow-root-user
```

Planen Sie sie dann ein wie auf jeder anderen Engine ([Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md)).

## Checkliste

- [ ] PHP 8.2+, Erweiterung `mongodb` 1.18+, Bibliothek `mongodb/mongodb` in jedem PHP installiert, das die Website verwendet.
- [ ] MongoDB 6.0 oder neuer mit eingeschalteter Zugriffskontrolle.
- [ ] Der Benutzer existiert in der Datenbank der Website, nur mit `readWrite` und `dbAdmin`.
- [ ] `bin/mongodb/create_indexes.js` wurde ausgeführt.
- [ ] Die Cronjobs liefen einmal und sind eingeplant.
- [ ] Sicherungen mit `mongodump` sind eingeplant ([Sicherungen je Engine](sicherungen-je-engine.md)).

## Verwandte Seiten

- [MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md)
- [MongoDB als Datenbank](../../funktionen/6.0/mongodb-datenbankunterstuetzung.md)
- [MongoDB für Hoster](../hosting/grundlagen/mongodb-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
