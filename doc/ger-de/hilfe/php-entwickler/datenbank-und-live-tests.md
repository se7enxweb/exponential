---
title: Datenbank- und Live-Tests
audience: php-developer, developer
category: php-development
order: 140
summary: Tests, die eine echte Datenbank oder einen echten Server brauchen - die Gruppe database und tests/runtests.php mit einem DSN und einer Datenbank pro Test, die MongoDB- und Live-HTTP-Tests und ihre Umgebungsvariablen, wie Sie Passwörter sicher übergeben und wie die CI MySQL und PostgreSQL ausführt.
keywords: Datenbanktests, Live-Tests, Integrationstests, Umgebungsvariablen, Passwörter, database tests, runtests.php, dsn, db-per-test, ezpDatabaseTestCase, group database, mongodb-live, EXP_TEST_MYSQL_USER, EXP_TEST_MONGO_URI, EXP_TEST_PUBLIC_HOST, live tests, PostgreSQL 15, MySQL 8.0, integration tests
type: help
---
# Datenbank- und Live-Tests

Diese Seite richtet sich an PHP-Entwickler, die Tests schreiben oder ausführen, die eine Datenbank, einen MongoDB-Server
oder eine laufende Site brauchen. Diese Tests werden aus dem normalen PHPUnit-Lauf herausgehalten, damit
`vendor/bin/phpunit` auf jedem Laptop und jedem CI-Runner besteht, und separat gegen Server ausgeführt, die Sie nennen.

## Inhalt

1. [Welche Tests live sind](#welche-tests-live-sind)
2. [Datenbanktests mit runtests.php](#datenbanktests-mit-runtestsphp)
3. [Einen Datenbanktest schreiben](#einen-datenbanktest-schreiben)
4. [Integrationstests für MongoDB und MySQL](#integrationstests-für-mongodb-und-mysql)
5. [Live-HTTP-Tests](#live-http-tests)
6. [Passwörter sicher übergeben](#passwörter-sicher-übergeben)
7. [In der CI](#in-der-ci)

## Welche Tests live sind

| Art | Markiert durch | Aus dem normalen Lauf ausgeschlossen |
|---|---|---|
| Datenbanktests (älteres Toolkit) | `@group database`, Klassen, die `ezpDatabaseTestCase` erweitern | ja |
| Integration von MongoDB und MySQL | `@group mongodb-live` | ja |
| echte Mail | `@group mail-live` | ja |
| Netzwerk | `@group network-live` | ja |
| Live-HTTP gegen eine Site | Umgebungsvariablen; übersprungen, wenn nicht gesetzt | sie werden übersprungen |

Ein Live-Test liest aus Umgebungsvariablen, wo sein Server ist, und überspringt sich unter Nennung der Variable, wenn
sie nicht gesetzt ist. Kein Test trägt einen eigenen Hostnamen, ein Konto oder ein Passwort.

## Datenbanktests mit runtests.php

Der ältere Runner `tests/runtests.php` legt das Schema in einer Datenbank an, die Sie nennen, optional eine Datenbank
pro Test, und führt die Gruppe database aus:

```bash
php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test
php tests/runtests.php --dsn postgresql://user@127.0.0.1/testdb --db-per-test
```

Verwenden Sie eine Wegwerf-Datenbank und ein Konto, das Datenbanken anlegen und löschen darf. Richten Sie ihn niemals
auf die Datenbank einer Site: Der Runner baut das Schema neu auf.

## Einen Datenbanktest schreiben

Das Toolkit in `tests/toolkit/` stellt `ezpDatabaseTestCase` bereit (`tests/toolkit/ezpdatabasetestcase.php`), das die
Datenbankverbindung für einen Testfall einrichtet:

```php
<?php
/**
 * @group database
 */
class acmeReportStorageTest extends ezpDatabaseTestCase
{
    public function testStoreAndFetch()
    {
        $item = new acmeReportItem( array( 'status' => 'open', 'amount' => 2 ) );
        $item->store();
        $fetched = acmeReportItem::fetch( $item->attribute( 'id' ) );
        $this->assertSame( 'open', $fetched->attribute( 'status' ) );
    }
}
```

Halten Sie Datenbanktests wenige und konzentriert auf das, was wirklich die Datenbank braucht (SQL, Transaktionen,
Speicherung), und testen Sie die Logik darum herum ohne eine ([PHPUnit ohne Datenbank](phpunit-ohne-datenbank.md)).

## Integrationstests für MongoDB und MySQL

| Tests | Variablen |
|---|---|
| `expMongoDBIntegrationTest`, `sevenxMongoDBIntegrationTest` (Teil MongoDB) | `EXP_TEST_MONGO_URI` (ein Verbindungs-String `mongodb://`), `EXP_TEST_MONGO_DATABASE` (Standard `exp`) |
| dieselben Tests (Teil MySQL) | `EXP_TEST_MYSQL_USER`, `EXP_TEST_MYSQL_PASSWORD`, `EXP_TEST_MYSQL_DATABASE`, `EXP_TEST_MYSQL_HOST` (Standard `localhost`), `EXP_TEST_MYSQL_PORT` (Standard 3306) |
| `expMongoDBDeclaredDefaultTest`, `TrashListPagedTest` | `EXP_TEST_MONGO_SCRATCH` (`<host>:<port>/<database>`, eine Datenbank, deren Name „scratch“ oder „test“ enthält) |

```bash
php vendor/bin/phpunit --testsuite mongodb-live
```

## Live-HTTP-Tests

Tests wie `ApiKeyLiveTest` und `RestContentPermissionLiveTest` rufen eine laufende Site über HTTP auf. Sie lesen
`EXP_TEST_PUBLIC_HOST` (sonst den Host der `SiteURL` des Standard-Siteaccess), `APIKEY_LIVE_BASES` und
`APIKEY_LIVE_ADMIN_PASSWORD`. Führen Sie sie gegen eine Testinstallation mit einem Testkonto aus, niemals gegen die
Produktion und niemals mit einem gemeinsam genutzten Administratorkonto.

Ein Live-Test, der prüft, was der Webserver im Audit-Trail festgehalten hat, liest das Audit-Verzeichnis der Site,
`expAuditConfig::siteLogDir()`, nicht das eigene Verzeichnis des Laufs unter `var/tmp`, das `tests/bootstrap.php`
einrichtet.

## Passwörter sicher übergeben

Übergeben Sie ein Passwort über die Umgebung, gelesen aus einer Datei, die nur Sie lesen können, nicht auf der
Kommandozeile, wo es in der Shell-History und der Prozessliste landet:

```bash
EXP_TEST_MYSQL_PASSWORD="$(cat ~/.exp-test-mysql-password)" EXP_TEST_MYSQL_USER=test EXP_TEST_MYSQL_DATABASE=testdb \
    php vendor/bin/phpunit tests/tests/lib/ezdb/mongodb/expMongoDBIntegrationTest.php
```

## In der CI

Der PHPUnit-Workflow führt **Database tests** auf MySQL 8.0 und PostgreSQL 15 aus (PHP 8.3, mit `pgcrypto` unter
PostgreSQL), mit `tests/runtests.php --dsn ... --db-per-test`. SQLite ist durch Unit-Tests der Treiber in
`tests/tests/lib/ezdb/` abgedeckt, MongoDB durch Unit-Tests des Adapters ohne Server; die Gruppe `mongodb-live` wird in
der CI nicht ausgeführt ([Datenbankunterstützung](../datenbankunterstuetzung.md#testabdeckung-ci)).

## Verwandte Seiten

- [Qualitätsprüfungen: Tests, die einen Live-Server brauchen](../../funktionen/6.0/qualitaetspruefungen.md#tests-die-einen-laufenden-server-brauchen)
- [Code für jede unterstützte Datenbank](code-fuer-jede-unterstuetzte-datenbank.md)
- [CI und das Nachstellen einer roten Prüfung](ci-und-eine-rote-pruefung-nachstellen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
