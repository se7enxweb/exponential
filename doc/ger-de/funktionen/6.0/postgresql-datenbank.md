# Unterstützung für die Datenbank PostgreSQL

Diese Seite ist für Administratoren und Datenbankadministratoren, die eine Exponential-Website auf PostgreSQL
betreiben. Sie ist die eine Seite für PostgreSQL, wie jede andere Datenbank sie hat: die Unterstützungsstufe, was
Server und PHP brauchen, die Einstellungen, eine Installation, die Grenzen und die getesteten Versionen. Die
ausführlicheren Kapitel sind dort verlinkt, wo sie mehr sagen.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server und Azure SQL (weitere
> Datenbanken ab 6.0.15: [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md)). Einzelheiten:
> [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md#unterstützungsstufen-auf-einen-blick).

PostgreSQL ist der empfohlene Datenbankserver: strenges SQL, Sperren auf Zeilenebene, eigene Replikation und
Wiederherstellung auf einen Zeitpunkt, und er wird bei jedem Push in der CI getestet.

## Inhalt

1. [Unterstützungsstufe](#unterstützungsstufe)
2. [Voraussetzungen](#voraussetzungen)
3. [PHP-Erweiterung](#php-erweiterung)
4. [Einstellungen](#einstellungen)
5. [Eine Website installieren](#eine-website-installieren)
6. [Grenzen](#grenzen)
7. [Getestete Versionen](#getestete-versionen)
8. [Datenbanken, die das Protokoll von PostgreSQL sprechen](#datenbanken-die-das-protokoll-von-postgresql-sprechen)
9. [Verwandte Seiten](#verwandte-seiten)

## Unterstützungsstufe

| Datentabellen | Ganze Website | DFS | Cluster mit mehreren Servern | Setup-Assistent | `exp:install` / Kickstarter | xne-Upgrade getestet |
|---|---|---|---|---|---|---|
| ja (`Engine=postgresql`) | ja | ja, im Kernel (`expDFSFileHandlerPostgresqlBackend`) | ja, mit Lesereplikaten | ja, mit `pgsql` | `--db=pgsql` | ja: 6.0.14 auf 6.0.15 und ein exaktes Zurückrollen, auch eines fehlgeschlagenen Schritts |

## Voraussetzungen

- **PostgreSQL 13 oder neuer.** Der Installer legt `pgcrypto` an, ab PostgreSQL 13 eine vertrauenswürdige
  Erweiterung, die der Eigentümer der Datenbank anlegen darf; der Treiber berechnet MD5 mit ihrem `digest()`. Fehlt sie
  dem Server, installieren Sie das contrib-Paket von PostgreSQL Ihrer Distribution.
- **Eine Datenbank, die dem Benutzer der Website gehört**, in `UTF8`. Ab PostgreSQL 15 dürfen andere Benutzer im
  Schema `public` einer fremden Datenbank keine Tabellen mehr anlegen:

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

Die Vorbereitung des Servers Schritt für Schritt (`pg_hba.conf`, `listen_addresses`, die Rechte) steht unter
[PostgreSQL einrichten](../../hilfe/datenbankadministratoren/postgresql-einrichten.md).

## PHP-Erweiterung

`pgsql`, mit PHP ausgeliefert (`php-pgsql`, `php<version>-pgsql`). Der Setup-Assistent bietet PostgreSQL und die
Datenbanken seines Protokolls an, sobald das PHP, das ihn ausführt, `pgsql` hat. Eine Datentabellen-Verbindung oder
eine Importquelle über PDO nutzt `pdo_pgsql`.

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<das Passwort>
Database=exponential
UsePersistentConnection=disabled
```

`ezpostgresql`, `postgresql` und `pgsql` nennen alle den Treiber `eZPostgreSQLDB`. Der Block gehört in
`settings/override/site.ini.append.php` (die Installer schreiben ihn für Sie); danach den INI-Cache leeren
(`php bin/php/ezcache.php --clear-tag=ini`). Lesereplikate: `UseSlaveServer=enabled` mit `SlaveServerArray[]`; nur
einfache SELECTs anonymer GET- und HEAD-Anfragen gehen an ein Replikat, und nur an eines, das aktuell ist
([Lesereplikat](../../bc/6.0/postgresql-lesereplikat.md)).

## Eine Website installieren

```bash
export EXP_INSTALL_DB_PASSWORD='<das Passwort>'
php bin/php/console exp:install --db=pgsql --db-host=localhost --db-user=exponential --db-name=exponential
```

Im Browser wählen Sie auf der Datenbankseite des Setup-Assistenten PostgreSQL. Der Kickstarter nimmt `Type=pgsql` in
`[database_choice]` ([Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md)).

## Grenzen

- Keine, die Exponentials eigenem Code eigen wären. Erweiterungscode, der für MySQL geschrieben ist (Backticks,
  `SET`-Anweisungen nur für MySQL), scheitert hier; schreiben Sie portables SQL
  ([Hinweise über Engines hinweg](../../installation/09-datenbanken.md#98-hinweise-über-engines-hinweg)).
- Spalten mit automatischer Nummerierung sind Sequenzen namens `<tabelle>_<spalte>_seq`. Nach dem Laden von Zeilen
  mit festen IDs setzen Sie die Sequenz auf die höchste ID; der Schema-Installer tut das selbst.
- PostgreSQL sortiert NULL aufsteigend hinter jeden Wert, wo MySQL und SQLite es zuerst sortieren.

## Getestete Versionen

PostgreSQL 13, 15, 18 und 19 in der CI für die ganze Datenbank-Suite bei jedem Push, 13 bis 19 für das DFS-Backend;
18.7 und 19.0 zudem für Installation, Upgrade-Sicherung, die Datenbankstufe des Upgrades, die Umwandlung von MySQL
nach PostgreSQL und das Lesereplikat ([PostgreSQL 18 und 19](postgresql-18-und-19.md)). Die Integrationssuite von xne
aktualisiert eine echte 6.0.14-Website auf PostgreSQL auf den aktuellen Code und rollt sie exakt zurück.

## Datenbanken, die das Protokoll von PostgreSQL sprechen

CockroachDB, YugabyteDB, Neon, Supabase und Amazon Aurora PostgreSQL installieren eine ganze Website auf derselben
Erweiterung, mit je einem Profil, und Google Cloud Spanner (über PGAdapter) nimmt Datentabellen auf:
[Datenbanken mit dem PostgreSQL-Protokoll](postgresql-wire-datenbanken.md). Verwaltete PostgreSQL-Dienste wie Amazon
RDS for PostgreSQL und Google Cloud SQL sind für den Treiber einfaches PostgreSQL.

## Verwandte Seiten

- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md) und [Eine Datenbank wählen](../../hilfe/datenbank-waehlen.md).
- [9.4 PostgreSQL](../../installation/09-datenbanken.md#94-postgresql), das Kapitel für den Betrieb.
- [PostgreSQL einrichten](../../hilfe/datenbankadministratoren/postgresql-einrichten.md) und
  [PostgreSQL im Betrieb](../../hilfe/datenbankadministratoren/postgresql-betrieb.md).
- [Clustering auf PostgreSQL](postgresql-dfs-cluster.md) und [Clustering in Exponential 6.0](clustering-ueberblick.md).
