---
title: Oracle mit ezoracle einrichten
audience: dba
category: database-administration
order: 19
summary: Wie Sie Oracle Database mit der Erweiterung ezoracle für eine Exponential-Website vorbereiten - Anforderungen (19c oder neuer, AL32UTF8, oci8 und ein Oracle-Client), die von ora-grant.php ausgegebenen Rechte, die Formen des Connect-Strings, die Installation über die Konsole, der site.ini-Block und Oracles eigene Verhaltensweisen (leere Zeichenkette als NULL, Vergleich mit Beachtung der Groß-/Kleinschreibung, kein AS vor einem Tabellenalias).
keywords: Oracle, ezoracle, eZOracleDB, eZOracleSchema, 19c, AL32UTF8, oci8, Instant Client, ora-grant.php, SELECT_CATALOG_ROLE, Easy Connect, TNS-Alias, Connect-String, FREEPDB1, exp:install --db=oracle, OracleEmptyStringForNull, OracleCaseInsensitive, ORA-00933, Einrichtung, TNS alias, connect string, setup
type: help
---
# Oracle mit ezoracle einrichten

Diese Seite richtet sich an Oracle-Datenbankadministratoren, die ein Schema für eine Exponential-Website vorbereiten. Die
Unterstützung für Oracle kommt aus der Erweiterung `ezoracle` (Version 2.3.7, von `composer.json` als `~2.3.7` verlangt,
daher in `extension/ezoracle` jeder Installation vorhanden und nur für Websites aktiviert, die Oracle verwenden). Sie
liefert den Treiber `eZOracleDB`, den Schema-Handler `eZOracleSchema`, Konsolenbefehle und Cronjobs. Diese Seite
beantwortet: was erforderlich ist, welche Rechte das Konto braucht, wie Sie sich verbinden und installieren und welche
Verhaltensweisen von Oracle wichtig sind. Quellen:
[9.6 Oracle 19c und neuer](../../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle) und die eigenen Dateien
`README.md` und `INSTALL` der Erweiterung in `extension/ezoracle/`.

## Inhalt

1. [Anforderungen](#anforderungen)
2. [Das Konto anlegen](#das-konto-anlegen)
3. [Connect-Strings](#connect-strings)
4. [Installieren](#installieren)
5. [Die Einstellungen](#die-einstellungen)
6. [Oracles eigene Verhaltensweisen](#oracles-eigene-verhaltensweisen)
7. [Checkliste](#checkliste)

## Anforderungen

- **Exponential 6.0.15 oder neuer**; frühere Versionen bieten Oracle in den Installern nicht an.
- **Oracle Database 19c oder neuer**; der Assistent prüft 19.0. Der Treiber verwendet `STANDARD_HASH` und `OFFSET`/`FETCH`.
  Getestet mit Oracle AI Database 26ai Free (23.26). Die Unterstützungsaussage des Projekts nennt 19c als Minimum
  ([Datenbankunterstützung](../datenbankunterstuetzung.md#5-oracle)); das `README.md` der Erweiterung beschreibt einen
  Ausweichweg für 11.2 und 12.1, der nur durch ihre Unit-Tests abgedeckt ist. Planen Sie mit 19c.
- Datenbankzeichensatz **AL32UTF8**. Der Treiber verbindet sich in AL32UTF8; `NLS_LANG` wird nicht gebraucht.
- PHP 8.0 bis 8.5 mit **oci8** (PECL oci8 3.4 für PHP 8.4 und 8.5, 3.2 für 8.1 bis 8.3), gegen einen Oracle-Client gebaut;
  der Instant Client genügt ([PHP-Erweiterungen je Engine](php-erweiterungen-je-engine.md#oracle-oci8-und-der-client)).
- Oracle ist nicht in der CI des Projekts; reine Oracle-Kernel-Tests werden auf anderen Engines übersprungen. Testen Sie
  Upgrades auf einer Staging-Kopie.

## Das Konto anlegen

Das Konto darf Sessions, Tabellen, Views, Trigger, Sequenzen und Prozeduren anlegen. Die Erweiterung gibt das SQL aus, das
ein DBA ausführt:

```bash
ORACLE_NEW_PASSWORD='<a strong password>' php extension/ezoracle/bin/php/ora-grant.php exponential - USERS
```

`-` nimmt das Passwort aus `ORACLE_NEW_PASSWORD`, sodass es nicht in der Prozessliste erscheint; `USERS` ist der
Tablespace. Dasselbe steht als `php bin/php/console ext:ezoracle:ora-grant` zur Verfügung. Lesen Sie das ausgegebene SQL
und führen Sie es dann als DBA aus. Für die Befehle `health` und `report` vergeben Sie zusätzlich `SELECT_CATALOG_ROLE`:
Sie lesen `V$`-Views, und ohne die Rolle sagen die betroffenen Prüfungen das.

## Connect-Strings

`site.ini [DatabaseSettings] Database` ist der Connect-String; `Server` und `Port` verwendet dieser Treiber nicht. Er kann
sein:

- Easy Connect: `127.0.0.1:1521/FREEPDB1`;
- Easy Connect Plus: `tcp://host:port/service?connect_timeout=5`;
- ein TNS-Alias (mit `TNS_ADMIN` für jeden Prozess gesetzt);
- ein vollständiger `(DESCRIPTION=...)`-Deskriptor.

`ezoracle.ini [ConnectionSettings]` kann stattdessen einen Deskriptor über mehrere Listener bauen
([Oracle: persistente Verbindungen, DRCP und Tuning](oracle-verbindungen-und-tuning.md)).

## Installieren

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=oracle \
    --db-host=127.0.0.1 --db-port=1521 --db-name=FREEPDB1 --db-user=exponential --allow-root-user
```

`--db-name` ist der Service-Name (Standard `FREEPDB1`); daraus wird `host:port/service` gebaut. Ein vollständiger
Connect-String oder `@<tns alias>` wird so verwendet, wie er angegeben ist. `--db=oci8` und `--db=ezoracle` werden ebenfalls
akzeptiert. Die Installer aktivieren die Erweiterung für die neue Website. Im Einrichtungsassistenten erscheint Oracle, wenn
`oci8` geladen und `extension/ezoracle` vorhanden ist. Für den Kickstarter setzen Sie `[database_choice] Type=oci8` und den
Connect-String als `Database`.

## Die Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=exponential
Password=<the password>
Database=127.0.0.1:1521/FREEPDB1
Charset=utf-8
# Oracle stores '' as NULL; enabled returns '' for NULL in text columns
OracleEmptyStringForNull=disabled
# enabled: ALTER SESSION SET NLS_COMP=LINGUISTIC NLS_SORT=<sort>
OracleCaseInsensitive=disabled
OracleCaseInsensitiveSort=BINARY_CI

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

## Oracles eigene Verhaltensweisen

| Verhalten | Wirkung auf Exponential | Was zu tun ist |
|---|---|---|
| `''` wird als NULL gespeichert | Eine leere Zeichenkette kommt als NULL zurück | `OracleEmptyStringForNull=enabled` gibt für Textspalten `''` zurück |
| Zeichenketten werden binär verglichen (mit Groß-/Kleinschreibung) | Ein für MySQL geschriebener Login oder eine Suche findet in anderer Schreibweise nichts | `OracleCaseInsensitive=enabled`, dann linguistische Indizes mit `ext:ezoracle:ci-indexes --create` anlegen |
| `AS` ist nur vor einem Spaltenalias erlaubt | `ORA-00933` im SQL einer Erweiterung, das `AS` vor einen Tabellenalias schreibt | Die Erweiterung korrigieren ([SQL von Erweiterungen prüfen](portables-sql-fuer-erweiterungen.md)) |

Der Treiber führt bei jedem Verbinden ein `ALTER SESSION SET` aus (Zahlenformat, Längensemantik, `CURSOR_SHARING` und die
NLS-Einstellungen, wenn `OracleCaseInsensitive` eingeschaltet ist); unter [Der SQL-Abfrage-Cache](sql-abfrage-cache.md) steht,
wie er diese Anweisung behandelt.

## Checkliste

- [ ] Oracle 19c oder neuer, Zeichensatz `AL32UTF8`.
- [ ] `oci8` in jedem PHP geladen, das die Website verwendet; Client-Bibliotheken werden von PHP-FPM, Velocity und Cron gefunden.
- [ ] Das Konto hat die Rechte aus `ora-grant.php` und für Gesundheitsberichte `SELECT_CATALOG_ROLE`.
- [ ] `ezoracle` steht in `ActiveExtensions[]`.
- [ ] Entscheidungen zu `OracleEmptyStringForNull` und `OracleCaseInsensitive` getroffen.
- [ ] Sicherungen mit Data Pump eingeplant ([Sicherungen je Engine](sicherungen-je-engine.md)).

## Verwandte Seiten

- [Oracle: persistente Verbindungen, DRCP und Tuning](oracle-verbindungen-und-tuning.md)
- [Wartungsbefehle und Cronjobs für Oracle](oracle-wartungsbefehle.md)
- [Oracle für Hoster](../hosting/grundlagen/oracle-fuer-hoster.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
