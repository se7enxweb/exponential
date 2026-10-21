---
title: "Von Hand: Ihre eigene Tabelle mit eZPersistentObject"
audience: developer, app-creator
category: extension-development
order: 180
summary: Eine Tabelle für Grüße - eine datenbankneutrale db_schema.dba, das SQL, das der Kernel daraus für drei Datenbanken erzeugt, die Installation, die Klasse und die Verwendung auf einer Seite.
keywords: Tutorial, von Hand, eigene Tabelle, Datenbankschema, persistentes Objekt, Installation, tutorial, by hand, eZPersistentObject, own table, db_schema.dba, eZDbSchema, generateSchemaFile, ezsqlinsertschema, mysql, postgresql, sqlite, store, fetchObjectList
type: help
---
# Von Hand: Ihre eigene Tabelle mit eZPersistentObject

Tutorial 17. Sie geben `mysite_hello` eine Tabelle mit Grüßen: das Schema im datenbankneutralen `.dba`-Format,
installiert auf Ihrer Datenbank, und die Klasse `mysiteHelloGreeting`, die sie liest und schreibt. Die `.dba`-Datei hat
`php -l` bestanden, und das unten gezeigte SQL haben die Schema-Handler des Kernels (`eZDbSchema`) daraus erzeugt.

## 1. Das Schema

`extension/mysite_hello/share/db_schema.dba`:

```php
<?php
// This array contains the database schema
$schema = array (
  'mysite_hello_greeting' =>
  array (
    'name' => 'mysite_hello_greeting',
    'fields' =>
    array (
      'id' => array ( 'type' => 'auto_increment', 'default' => false ),
      'name' => array ( 'length' => 100, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
      'user_id' => array ( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
      'created' => array ( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
    ),
    'indexes' =>
    array (
      'PRIMARY' => array ( 'type' => 'primary', 'fields' => array ( 0 => 'id' ) ),
      'mysite_hello_greeting_created' => array ( 'type' => 'non-unique', 'fields' => array ( 0 => 'created' ) ),
    ),
  ),
  '_info' => array ( 'format' => 'generic' ),
);
?>
```

## 2. Was der Kernel daraus macht

`eZDbSchema::instance( array( 'type' => <engine>, ... ) )->generateSchemaFile( $schema )` hat daraus Folgendes gemacht:

MySQL:

```text
CREATE TABLE mysite_hello_greeting (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  created int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY mysite_hello_greeting_created ( created )
);
```

PostgreSQL:

```text
CREATE SEQUENCE IF NOT EXISTS mysite_hello_greeting_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS mysite_hello_greeting (
  id integer DEFAULT nextval('mysite_hello_greeting_id_seq'::text) NOT NULL,
  name character varying(100) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL
);
CREATE INDEX mysite_hello_greeting_created ON mysite_hello_greeting USING btree ( created );
ALTER TABLE ONLY mysite_hello_greeting ADD CONSTRAINT mysite_hello_greeting_pkey PRIMARY KEY ( id );
```

SQLite:

```text
CREATE TABLE mysite_hello_greeting (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(100) NOT NULL DEFAULT '',
  user_id INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) NOT NULL DEFAULT '0'
);
CREATE INDEX mysite_hello_greeting_created ON mysite_hello_greeting ( created );
```

Eine Datei, drei Datenbanken: Deshalb liefern Erweiterungen die `.dba` aus (und können für alle, die SQL bevorzugen, das
SQL pro Datenbank in `sql/<engine>/schema.sql` hinzufügen).

## 3. Installieren

Auf Ihrer Entwicklungsdatenbank (die Optionen des Befehls listet `--help` auf):

```bash
php bin/php/ezsqlinsertschema.php --type=<mysql|postgresql|sqlite> --user=<user> extension/mysite_hello/share/db_schema.dba <database>
```

Liefern Sie für eine echte Erweiterung stattdessen einen idempotenten Installationsbefehl aus, wie es `expqueue` mit
`./console ext:expqueue:install-schema` tut
([Datenbankschema, .dba-Dateien und Upgrades](../datenbankschema-und-dba-dateien.md)).

## 4. Die Klasse

Kopieren Sie `mysiteHelloGreeting` aus
[Persistente Objekte und Ihre eigenen Tabellen](../persistente-objekte-und-tabellen.md#die-klasse) nach
`extension/mysite_hello/classes/mysitehellogreeting.php`, dann `php bin/php/ezpgenerateautoloads.php -e`.

## 5. Verwenden

Speichern Sie auf der Seite zum Zusammenstellen aus Tutorial 13 den Gruß, wenn die Vorschau gültig ist, und leiten Sie
weiter:

```php
if ( $preview !== false && $http->hasPostVariable( 'SaveButton' ) )
{
    $greeting = mysiteHelloGreeting::create( $preview );
    $greeting->store();
    return $Module->redirectToView( 'compose' );
}
```

(und ergänzen Sie `'SaveButton' => 'Save'` in den `single_post_actions` der View, mit einer Schaltfläche im
Formular). Listen Sie die neuesten Grüße mit `mysiteHelloGreeting::fetchNewest( 10 )` auf und übergeben Sie sie an das
Template.

## Verwandte Seiten

- Zurück: [Von Hand: ein Cronjob-Teil](von-hand-cronjob-teil.md)
- Weiter: [Von Hand: eine ezjscore-Serverfunktion](von-hand-ajax-serverfunktion.md)
- [Persistente Objekte und Ihre eigenen Tabellen](../persistente-objekte-und-tabellen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
