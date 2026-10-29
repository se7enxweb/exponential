---
title: Oracle für Hoster
audience: hosting, admin
category: hosting
order: 110
summary: Oracle Database 19c und später als Datenbank einer Exponential-Website aus Sicht eines Hosters - die Erweiterung ezoracle, oci8 und der Instant Client, der Benutzer und seine Rechte, die Verbindungszeichenfolge, persistente Verbindungen und DRCP, Wartungsbefehle, Backups und das eigene Verhalten von Oracle.
keywords: Oracle, Oracle 19c, ezoracle, oci8, Instant Client, AL32UTF8, LD_LIBRARY_PATH, TNS_ADMIN, Verbindungszeichenfolge, Easy Connect, DRCP, persistente Verbindungen, ora-grant, datapump, expdp, impdp, ezoracle.ini, ActiveExtensions, Hosting, connect string, persistent connections, hosting
type: help
---
# Oracle für Hoster

Diese Seite ist für Systemadministratoren bei einem Hosting-Anbieter, deren Kunden Exponential auf Oracle betreiben,
meist auf einer Datenbank, die der Kunde bereits besitzt. Sie beantwortet: was die Webserver brauchen (PHP `oci8` und
einen Oracle-Client), was der DBA des Kunden bereitstellen muss, wie die Verbindung konfiguriert wird, welche
Einstellungen die Kosten für Verbindungen senken und wie die Datenbank gewartet und gesichert wird. Oracle ist die
fünfte in der offiziellen Reihenfolge. Quellen: [Datenbankunterstützung](../../datenbankunterstuetzung.md#5-oracle) und
[Kapitel 9.6](../../../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle); die vollständige Referenz sind die
eigenen Dateien `README.md` und `INSTALL` der Erweiterung in `extension/ezoracle/`.

## Inhalt

1. [Was es ist](#was-es-ist)
2. [Anforderungen](#anforderungen)
3. [Was der DBA bereitstellt](#was-der-dba-bereitstellt)
4. [Einstellungen und Installation](#einstellungen-und-installation)
5. [Persistente Verbindungen und DRCP](#persistente-verbindungen-und-drcp)
6. [Wartung und Backups](#wartung-und-backups)
7. [Das eigene Verhalten von Oracle](#das-eigene-verhalten-von-oracle)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Was es ist

Die Unterstützung für Oracle kommt von der Erweiterung **`ezoracle`**, die `composer.json` verlangt (`~2.3.7`); sie liegt
daher in jeder Installation in `extension/ezoracle`. Sie stellt den Treiber `eZOracleDB`, den Schema-Handler
`eZOracleSchema`, Konsolenbefehle (`ext:ezoracle:<name>`) und Cronjobs bereit. Sie wird nur für Websites aktiviert, die
Oracle nutzen; die Installer aktivieren sie für die neue Website.

## Anforderungen

| Element | Wert |
|---|---|
| Exponential | 6.0.15 oder später (frühere Versionen bieten Oracle in den Installern nicht an) |
| Oracle Database | **19c oder später** (der Assistent prüft 19.0); 11g und älter werden nicht unterstützt |
| Zeichensatz | `AL32UTF8`; `NLS_LANG` ist nicht nötig |
| PHP | 8.0 bis 8.5 mit `oci8` (PECL oci8 3.4 für PHP 8.4 und 8.5, 3.2 für 8.1 bis 8.3) |
| Oracle-Client | gegen ihn wird `oci8` gebaut; der Instant Client genügt, `ORACLE_HOME` ist nicht nötig |
| Funktionierend gesehen | Oracle AI Database 26ai Free (23.26), oci8 3.4.1, Instant Client 23.26, PHP 8.5 |

`oci8` selbst kommt von Ihrem Hersteller, aus EPEL oder von Remi. Wenn der Client außerhalb des Bibliothekspfads des
Systems liegt, setzen Sie `LD_LIBRARY_PATH` (und `TNS_ADMIN` für TNS-Aliase) **überall, wo PHP läuft**: im PHP-FPM-Pool
(`env[...]`), in der Umgebung von Velocity und in der Cron-Umgebung.

> **Warnung**
> Ein PHP, das `oci8` auf der Kommandozeile lädt, aber nicht im PHP-FPM-Pool (oder umgekehrt), ist das häufigste
> Oracle-Problem bei einem Hoster. Prüfen Sie auf jedem Server `php -m` und die Informationsseite des Web-PHP auf `oci8`.

## Was der DBA bereitstellt

Einen Oracle-Benutzer, der Sitzungen, Tabellen, Views, Trigger, Sequenzen und Prozeduren anlegen darf. Die Erweiterung
gibt das SQL für den DBA aus:

```bash
ORACLE_NEW_PASSWORD='<a strong password>' php extension/ezoracle/bin/php/ora-grant.php <customer> - USERS
```

(`-` nimmt das Passwort aus `ORACLE_NEW_PASSWORD`, sodass es nicht in der Prozessliste steht; `USERS` ist der
Tablespace.) Dasselbe gibt es als `php bin/php/console ext:ezoracle:ora-grant`. Für die Befehle zu Zustand und Berichten
vergeben Sie außerdem `SELECT_CATALOG_ROLE`.

## Einstellungen und Installation

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=<customer>
Password=<the password>
# Easy Connect host:port/service_name, a TNS alias or a full descriptor;
# Server and Port are not used by this driver
Database=db.example.com:1521/<service>
Charset=utf-8

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --apply --db=oracle \
    --db-host=db.example.com --db-port=1521 --db-name=<service> --db-user=<customer> --allow-root-user
```

`--db-name` ist der **Dienstname** (Standard `FREEPDB1`); eine vollständige Verbindungszeichenfolge oder
`@<tns alias>` wird unverändert verwendet. Für den Kickstarter setzen Sie `[database_choice] Type=oci8` (`oracle` wird
akzeptiert).

## Persistente Verbindungen und DRCP

Eine neue dedizierte Oracle-Sitzung kostet pro Anfrage einige zehn Millisekunden. Alles über die einfache Verbindung
hinaus steht in `ezoracle.ini` und wird in `settings/override/ezoracle.ini.append.php` überschrieben; jede Funktion ist
standardmäßig aus:

```ini
[ConnectionSettings]
Persistent=enabled
DRCP=enabled
ConnectionClass=EXPONENTIAL
KeepAliveInterval=60
```

- **Persistente Verbindungen** leben so lange wie der PHP-Prozess; `oci8.persistent_timeout`, `oci8.max_persistent` und
  `oci8.ping_interval` in `php.ini` steuern sie.
- **DRCP** (Database Resident Connection Pooling) teilt einen Pool von Serverprozessen zwischen allen PHP-Prozessen, sodass
  Hunderte Worker nicht Hunderte dedizierte Serverprozesse brauchen. Der DBA startet den Pool einmal:
  `EXECUTE DBMS_CONNECTION_POOL.START_POOL;`.
- **Die Reihenfolge der Erweiterungen zählt.** Wenn die Einstellungen aus einer Website-Erweiterung statt aus
  `settings/override/` kommen, führen Sie diese Erweiterung in `ActiveExtensions[]` **vor** `ezoracle` auf
  ([Kapitel 9.6](../../../installation/09-datenbanken.md#persistente-verbindungen-und-drcp)).

Den wirksamen Wert prüfen Sie mit `php bin/php/console exp:ini get ezoracle.ini/ConnectionSettings/DRCP --allow-root-user`.

## Wartung und Backups

Konsolenbefehle (`php bin/php/console ext:ezoracle:<name>`): `health`, `gather-stats`, `recompile`,
`purge-recyclebin`, `sequence-sync`, `schema-diff`, `indexes`, `report`, `datapump`, `ci-indexes`. Jeder gibt pro
Prüfung eine Zeile PASS, WARN, FAIL oder INFO aus. Die Cronjob-Teile `ezoracle` (nächtlich) und `ezoraclehealth`
(stündlich) führen sie aus, sobald sie in `ezoracle.ini [CronjobSettings]` aktiviert sind:

```text
15 3 * * *  cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoracle
0 * * * *   cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoraclehealth
```

**Backups**: `php bin/php/console ext:ezoracle:datapump` (siehe sein `--help`) oder Oracles `expdp`; Wiederherstellung
mit Oracles `impdp`. Langsame Anweisungen gehen nach `var/log/oracle-slow.log`, wenn `[LogSettings] SlowQueryThreshold`
gesetzt ist.

## Das eigene Verhalten von Oracle

| Verhalten | Einstellung |
|---|---|
| Oracle speichert `''` als NULL | `OracleEmptyStringForNull=enabled` liefert `''` für Textspalten |
| Zeichenketten werden mit Groß-/Kleinschreibung verglichen | `OracleCaseInsensitive=enabled`, dann `ext:ezoracle:ci-indexes --create` |
| `AS` vor einem Tabellenalias wird abgelehnt (`ORA-00933`) | das SQL der Erweiterung korrigieren |

Oracle ist nicht in der CI des Projekts; Tests nur für Oracle in der Kernel-Suite werden auf anderen Engines
übersprungen ([Testabdeckung](../../datenbankunterstuetzung.md#testabdeckung-ci)).

## Checkliste

- [ ] Exponential 6.0.15 oder später.
- [ ] Oracle 19c oder später in `AL32UTF8`.
- [ ] `oci8` im Web-PHP, im PHP der Kommandozeile und im PHP von Velocity geladen, mit den Client-Bibliotheken jeweils auffindbar.
- [ ] Der Benutzer aus der Ausgabe von `ora-grant.php` angelegt; `SELECT_CATALOG_ROLE` für die Zustandsprüfungen.
- [ ] `ezoracle` für die Website aktiv; persistente Verbindungen und DRCP erwogen.
- [ ] Backups mit dem DBA des Kunden vereinbart.

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)
- [Datenbankunterstützung: Oracle](../../datenbankunterstuetzung.md#5-oracle)
- [Kapitel 9.6: Oracle 19c und später](../../../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle)
- [Treiber für SQLite und Oracle](../../../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
