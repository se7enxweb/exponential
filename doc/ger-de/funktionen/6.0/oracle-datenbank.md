# Unterstützung für die Datenbank Oracle

Diese Seite ist für Administratoren und Datenbankadministratoren, die eine Exponential-Website auf Oracle Database
betreiben. Sie ist die eine Seite für Oracle, wie jede andere Datenbank sie hat: die Unterstützungsstufe, was Server
und PHP brauchen, die Einstellungen, eine Installation, die Grenzen und die getesteten Versionen. Die ausführlicheren
Kapitel sind dort verlinkt, wo sie mehr sagen.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server und Azure SQL (weitere
> Datenbanken ab 6.0.15: [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md)). Einzelheiten:
> [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md#unterstützungsstufen-auf-einen-blick).

Der Treiber `eZOracleDB`, der Schema-Handler `eZOracleSchema`, Konsolenbefehle (`ext:ezoracle:<name>`) und Cronjobs
kommen mit der Erweiterung `ezoracle`. `composer.json` verlangt sie, sie liegt also in jeder Installation unter
`extension/ezoracle`; aktiviert wird sie nur für Websites, die Oracle nutzen.

## Inhalt

1. [Unterstützungsstufe](#unterstützungsstufe)
2. [Voraussetzungen](#voraussetzungen)
3. [PHP-Erweiterung](#php-erweiterung)
4. [Einstellungen](#einstellungen)
5. [Eine Website installieren](#eine-website-installieren)
6. [Grenzen](#grenzen)
7. [Getestete Versionen](#getestete-versionen)
8. [Verwandte Seiten](#verwandte-seiten)

## Unterstützungsstufe

| Datentabellen | Ganze Website | DFS | Cluster mit mehreren Servern | Setup-Assistent | `exp:install` / Kickstarter | xne-Upgrade getestet |
|---|---|---|---|---|---|---|
| ja (`Engine=oracle`) | ja | ja, im Kernel (`expDFSFileHandlerOracleBackend`, Oracle 12.1 und neuer) | ja, mit Lesereplikaten | ja, mit `oci8` | `--db=oracle` | ja: ein volles Upgrade einer per Kickstarter installierten Website und ein exaktes Zurückrollen |

## Voraussetzungen

- **Oracle Database 19c oder neuer** im Zeichensatz `AL32UTF8`. Der Treiber nutzt `STANDARD_HASH` und
  `OFFSET`/`FETCH`; 11g und älter werden nicht unterstützt.
- **Ein Benutzer**, der Sessions, Tabellen, Views, Trigger, Sequenzen und Prozeduren anlegen darf.
  `php extension/ezoracle/bin/php/ora-grant.php` gibt die Rechte aus, die ein DBA vergibt.

Die Vorbereitung Schritt für Schritt steht unter [Oracle einrichten](../../hilfe/datenbankadministratoren/oracle-einrichten.md).

## PHP-Erweiterung

`oci8` (PECL), gegen einen Oracle-Client gebaut; der Instant Client genügt, `ORACLE_HOME` ist damit nicht nötig. oci8
3.4 für PHP 8.4 und 8.5, 3.2 für PHP 8.1 bis 8.3, aus EPEL, Remi oder von Ihrem Hersteller. Der Setup-Assistent bietet
Oracle an, sobald das PHP, das ihn ausführt, `oci8` hat.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=exponential
Password=<das Passwort>
# die Verbindungsangabe: Easy Connect host:port/service_name, ein TNS-Alias oder ein vollständiger Deskriptor;
# Server und Port nutzt dieser Treiber nicht
Database=127.0.0.1:1521/FREEPDB1
Charset=utf-8

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

`ezoracle.ini [ConnectionSettings]` bringt persistente Verbindungen, Database Resident Connection Pooling (DRCP, damit
Hunderte PHP-Worker sich einen Pool von Serverprozessen teilen), Wiederholungen, Failover, Zeitlimits für Aufrufe und
Session-Tracing; alles ist standardmäßig aus
([Oracle: Verbindungen und Tuning](../../hilfe/datenbankadministratoren/oracle-verbindungen-und-tuning.md)). Kommen
diese Einstellungen aus einer Website-Erweiterung statt aus `settings/override/`, steht diese Erweiterung in
`ActiveExtensions[]` **vor** `ezoracle`.

## Eine Website installieren

```bash
export EXP_INSTALL_DB_PASSWORD='<das Passwort>'
php bin/php/console exp:install --db=oracle --db-host=127.0.0.1 --db-user=exponential --db-name=FREEPDB1
```

Ein Dienstname in `--db-name` wird zur Easy-Connect-Angabe `<db-host>:<db-port>/<dienst>`; ein Wert mit `/` oder `(`
wird so genommen, wie er ist, und `@alias` nennt einen TNS-Alias. Im Browser wählen Sie auf der Datenbankseite des
Setup-Assistenten Oracle; der Kickstarter nimmt `Type=oci8` in `[database_choice]`
([Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md)).

## Grenzen

- Oracle speichert `''` als NULL: `OracleEmptyStringForNull=enabled` liefert `''` für Textspalten, und seit 6.0.15
  wird ein Vergleich mit der leeren Zeichenkette als `IS NULL` / `IS NOT NULL` gesendet.
- Oracle vergleicht Zeichenketten mit Groß- und Kleinschreibung: `OracleCaseInsensitive=enabled`, mit linguistischen
  Indizes aus `ext:ezoracle:ci-indexes --create`.
- Oracle sortiert NULL aufsteigend hinter jeden Wert, wo MySQL und SQLite es zuerst sortieren.
- Der Upgrade-Befehl hat für Oracle keine eingebaute Sicherheitskopie: sichern Sie mit `expdp` oder
  `ext:ezoracle:datapump` und führen Sie das Upgrade mit `--no-backup` aus. xne macht seine eigene geprüfte Sicherung.
- Für die Installer ist Exponential 6.0.15 oder neuer nötig; frühere Versionen bieten Oracle dort nicht an.

## Getestete Versionen

Oracle ist nicht für die ganze Datenbank-Suite in der CI des Projekts; das DFS-Backend wird auf Oracle Free 23
getestet. Die Prüfung der Engine-Gleichheit und die Installationen laufen von Hand gegen Oracle AI Database 26ai Free
(23.26) mit oci8 3.4.1 und Instant Client 23.26 auf PHP 8.5. xne wurde auf Oracle einmal nachgewiesen: ein volles
Upgrade einer per Kickstarter installierten Website über `ezoracle` (das Upgrade des Kernels wandte fünf Schritte an,
die Gesundheitsprüfung bestand) und ein exaktes Zurückrollen jeder Datei und Tabelle.

## Verwandte Seiten

- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md) und [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md).
- [9.6 Oracle 19c und neuer](../../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle), das Kapitel für den
  Betrieb, und `README.md` und `INSTALL` der Erweiterung in `extension/ezoracle/`.
- [Oracle einrichten](../../hilfe/datenbankadministratoren/oracle-einrichten.md),
  [Oracle: Verbindungen und Tuning](../../hilfe/datenbankadministratoren/oracle-verbindungen-und-tuning.md).
- [Clustering auf Oracle](oracle-dfs-cluster.md) und [Clustering in Exponential 6.0](clustering-ueberblick.md).
