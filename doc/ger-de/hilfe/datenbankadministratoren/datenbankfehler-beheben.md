---
title: Datenbankfehler beheben
audience: dba
category: database-administration
order: 43
summary: Eine Tabelle, die beim Symptom beginnt, mit den Datenbankproblemen, denen eine Exponential-Website auf jeder Engine begegnet - die Website wirkt ausgefallen, fehlende Tabellen, schreibgeschützte SQLite-Dateien, Sperr-Timeouts, Fragezeichen durch den Zeichensatz, pgcrypto, Sequenzen, Fehler bei MongoDB-URI und -Bibliothek, Eigenheiten von Oracle, gescheiterte Update-Dateien - mit Ursache und Lösung für jedes und der Reihenfolge, in der Sie untersuchen.
keywords: Fehlerbehebung, Datenbankfehler, database cannot be reached, Database implementation not supported, fehlende Tabellen, readonly database, database is busy, Fragezeichen, digest function, duplicate key, MongoDB URI, MongoDB\Client not found, ORA-00933, CLOB, SET storage_engine, duplicate column name, error.log, troubleshooting, database error, missing tables, question marks
type: help
---
# Datenbankfehler beheben

Diese Seite richtet sich an Datenbankadministratoren, die gerufen werden, wenn eine Exponential-Website ein Datenbankproblem hat.
Sie beantwortet: wo Sie zuerst nachsehen und für jedes häufige Symptom die Ursache und die Lösung. Quellen:
[12.10 Datenbanken](../../installation/12-fehlerbehebung.md#1210-datenbanken) und die Stolperfallen jeder Engine in
[Kapitel 9](../../installation/09-datenbanken.md).

## Inhalt

1. [Wo Sie zuerst nachsehen](#wo-sie-zuerst-nachsehen)
2. [Jede Engine](#jede-engine)
3. [SQLite](#sqlite)
4. [MySQL und MariaDB](#mysql-und-mariadb)
5. [PostgreSQL](#postgresql)
6. [MongoDB](#mongodb)
7. [Oracle](#oracle)
8. [Upgrades](#upgrades)
9. [Wann Sie um Hilfe bitten](#wann-sie-um-hilfe-bitten)

## Wo Sie zuerst nachsehen

1. `var/log/error.log`: Die meisten Datenbankprobleme werden dort benannt ([Log-Betrachter](../hosting/betrieb/log-viewer-und-log-rotation.md)).
2. Die wirksame Einstellung: `php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user`.
3. **Setup > Systeminformationen**, wenn die Administration noch lädt.
4. Das eigene Log des Datenbankservers.
5. Für eine genaue Sicht die Debug-Ausgabe nur für Ihre Adresse ([Langsame Abfragen finden](langsame-abfragen-finden.md)).

## Jede Engine

| Symptom | Ursache | Lösung |
|---|---|---|
| Jede Inhaltsseite scheitert; `error.log` sagt "Database implementation not supported" | ein falsch geschriebenes `DatabaseImplementation`; der Kernel fällt auf eine Null-Datenbank zurück | den Alias korrigieren, den INI-Cache leeren, Velocity neu starten |
| Die Seite "database cannot be reached" | falscher Host, Benutzer, falsches Passwort oder falsche Datenbank, oder der Server ist ausgefallen; der Treiber löst `eZDBNoConnectionException` aus | die Anmeldung mit dem Client der Engine vom Webserver aus testen |
| Nach einem Engine-Wechsel lädt keine Seite | `DatabaseImplementation` nennt noch die alte Engine | in `settings/override/site.ini.append.php` setzen, den INI-Cache leeren |
| Eine Änderung der Einstellungen wirkt nicht | INI-Cache nicht geleert, Velocity nicht neu gestartet oder eine Siteaccess-Datei überschreibt sie | `exp:ini where`, leeren, neu starten |
| Seiten zeigen nach einer manuellen SQL-Änderung oder einer Wiederherstellung alte Daten | Caches, einschließlich des SQL-Abfrage-Cache | `php bin/php/ezcache.php --clear-all --allow-root-user` und `exp:velocity cache clear` |
| Funktioniert im Browser, scheitert in Cron | dem PHP der Kommandozeile fehlt die Datenbankerweiterung | `php -m` ([PHP-Erweiterungen](php-erweiterungen-je-engine.md)) |

## SQLite

| Symptom | Ursache | Lösung |
|---|---|---|
| "database is busy: the transaction could not start within N s ..." | ein anderer Schreibzugriff hielt die Schreibsperre während der ganzen `SQLiteTransactionWait` | nichts wurde geschrieben; erneut versuchen; den langen Schreiber finden ([Schreibwarteschlange](sqlite-schreibwarteschlange-und-sperren.md)) |
| "attempt to write a readonly database" | ein `-shm`, `-wal` oder das Verzeichnis ist für diesen Prozess nicht schreibbar | gemeinsame Gruppe, `2775`, Umask `0002` ([Rechte](sqlite-dateien-eigentuemer-und-rechte.md)) |
| Die Website meldet fehlende Tabellen | ein falscher Wert für `Database` hat eine neue, leere Datei geöffnet | den Pfad im Fehler mit `ls var/storage/sqlite3/` vergleichen |
| Langsam nach einer Installation oder einem Import | keine Statistiken | `sqlite3 var/storage/sqlite3/exponential.db ANALYZE` |
| "SQLite3 extension was not found" | `sqlite3` in diesem PHP nicht geladen | installieren |
| Die Datenbankdatei lässt sich herunterladen | der Webserver liefert `var/` aus | mitgelieferte Rewrite-Regeln oder Velocity; mit `curl -sI` prüfen |

## MySQL und MariaDB

| Symptom | Ursache | Lösung |
|---|---|---|
| `?`, wo Buchstaben mit Akzent standen | die Verbindung ist auf den Standardzeichensatz des Servers zurückgefallen | `AlwaysLog[]=warning`, nach "could not set the connection charset" suchen ([utf8mb4](mysql-und-mariadb-zeichensatz-und-innodb.md)) |
| Der Assistent lehnt den Zeichensatz der Datenbank ab | die Datenbank ist `latin1` oder ähnlich | sie in `utf8mb4` neu anlegen |
| Emoji gehen verloren | die Verbindung `utf8` (`utf8mb3`) | eine bekannte Grenze der Verbindung |
| Transaktionen werden nicht zurückgerollt | MyISAM-Tabellen | `ezconvertmysqltabletype.php --newtype=InnoDB` |
| Ein Replikat zeigt alte Inhalte | Replikationsverzögerung | die Verzögerung überwachen ([Lesereplikate](mysql-lesereplikate.md)) |

## PostgreSQL

| Symptom | Ursache | Lösung |
|---|---|---|
| "The 'digest' function is not available in your database ..." | `pgcrypto` fehlt | `CREATE EXTENSION IF NOT EXISTS pgcrypto;` als Eigentümer oder Superuser |
| Connection refused | `listen_addresses`, `pg_hba.conf` | die Webserver zulassen ([PostgreSQL einrichten](postgresql-einrichten.md#die-verbindung-erlauben)) |
| Doppelter Schlüssel beim Insert nach einem Import | Sequenzen hinter ihren Tabellen | `setval()` ([PostgreSQL betreiben](postgresql-betrieb.md#sequenzen-korrigieren)) |
| Keine Berechtigung, eine Tabelle anzulegen (PostgreSQL 15+) | das Konto ist nicht Eigentümer der Datenbank | es zum Eigentümer machen |
| Das SQL einer Erweiterung scheitert nur hier | Backticks oder reine MySQL-Anweisungen | [SQL von Erweiterungen prüfen](portables-sql-fuer-erweiterungen.md) |

## MongoDB

| Symptom | Ursache | Lösung |
|---|---|---|
| "Failed to parse MongoDB URI: ... 'default' authentication mechanism requires a username" | kein `User` angegeben | einen Benutzer in der Datenbank der Website anlegen ([MongoDB einrichten](mongodb-einrichten.md#den-benutzer-anlegen)) |
| `Class "MongoDB\Client" not found` | die Bibliothek `mongodb/mongodb` fehlt | `composer require mongodb/mongodb` |
| Teilbaum- und URL-Alias-Abfragen sind langsam | Indizes nicht angelegt | `bin/mongodb/create_indexes.js` |
| Eine Liste oder Seite ist leer, die anderswo nicht leer ist | SQL, das der Adapter nicht übersetzen kann | `grep -rn "MONGO TODO" var/log/` ([Grenzen](mongodb-grenzen-und-betrieb.md)) |
| Der Einrichtungsassistent läuft in ein Timeout | der Konfigurationsschritt dauert Minuten | `request_terminate_timeout` erhöhen oder über die Konsole installieren |

## Oracle

| Symptom | Ursache | Lösung |
|---|---|---|
| Der Treiber wird nicht gefunden | `ezoracle` nicht aktiv oder `oci8` nicht geladen | `extension/ezoracle`, `ActiveExtensions[]`, `php -m` prüfen |
| `ORA-00933` im SQL einer Erweiterung | `AS` vor einem Tabellenalias | die Erweiterung korrigieren |
| Eine leere Zeichenkette kommt als NULL zurück | Oracle speichert `''` als NULL | `OracleEmptyStringForNull=enabled` |
| Ein Login funktioniert nur in einer Schreibweise | binärer Vergleich | `OracleCaseInsensitive=enabled`, dann `ext:ezoracle:ci-indexes --create` |
| CLOB-Werte werden gekürzt | ein älteres `ezoracle` oder ein Client, der lange CLOBs über `LobPrefetch` 2000 kürzt | die Erweiterung aktualisieren |
| Einstellungen in Ihrer Website-Erweiterung werden ignoriert | sie steht nach `ezoracle` | sie davor setzen ([Oracle-Tuning](oracle-verbindungen-und-tuning.md#welche-einstellungen-gewinnen)) |

## Upgrades

| Symptom | Ursache | Lösung |
|---|---|---|
| `SET storage_engine=InnoDB;` scheitert | eine Update-Datei von vor Oktober 2026 | die aktuelle Datei nehmen (`SET default_storage_engine`) |
| "duplicate column name" auf SQLite | die 6.0.15-Datei lief ein zweites Mal | die Datenbank ist bereits aktualisiert |
| Eine Update-Datei ist mittendrin gescheitert | | die Sicherung wiederherstellen und die Datei von vorn anwenden |
| Jeder Insert scheitert auf PostgreSQL nach dem Wechsel von 5.x | alte Sequenznamen `<table>_s` | die aktuelle Update-Datei 6.0 benennt sie um |
| "The audit index classes are not in the autoload array" | Autoloads veraltet | `php bin/php/ezpgenerateautoloads.php -k` |

## Wann Sie um Hilfe bitten

Sammeln Sie die Fehlerzeilen, die Engine und ihre Version und den Bericht aus **Setup > Systeminformationen** (er enthält nie
Passwörter) und nutzen Sie dann die Wege unter [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md). Melden Sie
Sicherheitsprobleme vertraulich, nie in einem öffentlichen Issue.

## Verwandte Seiten

- [Fehlercodes und Meldungen der Datenbank](fehlercodes-und-meldungen.md)
- [Betriebsanleitung für eine ausgefallene Website](../hosting/runbooks/website-nicht-erreichbar.md)
- [Die Datenbank überwachen](die-datenbank-ueberwachen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
