---
title: PostgreSQL für Hoster
audience: hosting, admin
category: hosting
order: 80
summary: PostgreSQL, der empfohlene Datenbankserver für Exponential, aus Sicht eines Hosters - Versionen, die Regel zum Eigentümer der Datenbank, die Erweiterung pgcrypto, Einstellungen, die Installation mit exp:install, Sequenzen, Backups und die üblichen Probleme.
keywords: PostgreSQL, Postgres, pgsql, ezpostgresql, pgcrypto, digest, Eigentümer der Datenbank, PostgreSQL 13, PostgreSQL 15, createdb, createuser, template0, UTF8, pg_dump, pg_restore, Sequenzen, setval, pg_hba.conf, listen_addresses, Hosting, database owner, sequences, hosting
type: help
---
# PostgreSQL für Hoster

Diese Seite ist für Systemadministratoren bei einem Hosting-Anbieter, die PostgreSQL-Datenbanken für Exponential-Websites
bereitstellen. Sie beantwortet: welche PostgreSQL-Versionen Sie betreiben, wie Sie die Datenbank und den Benutzer jeder
Website so anlegen, dass der Installer gelingt, was die Erweiterung `pgcrypto` damit zu tun hat und wie Sie sichern und
Fehler suchen. PostgreSQL ist die zweite in der offiziellen Reihenfolge und der empfohlene Datenbankserver. Quellen:
[Datenbankunterstützung](../../../../en/us/help/database-support.md#2-postgresql) und
[Kapitel 9.4](../../../../en/us/install/09-databases.md#94-postgresql).

## Inhalt

1. [Warum PostgreSQL](#warum-postgresql)
2. [Versionen und Erweiterung](#versionen-und-erweiterung)
3. [Eine Datenbank pro Website anlegen](#eine-datenbank-pro-website-anlegen)
4. [pgcrypto](#pgcrypto)
5. [Einstellungen und Installation](#einstellungen-und-installation)
6. [Sequenzen](#sequenzen)
7. [Backups](#backups)
8. [Probleme und ihre Ursachen](#probleme-und-ihre-ursachen)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Warum PostgreSQL

Das Projekt nennt PostgreSQL den empfohlenen Datenbankserver: striktes SQL, Sperren auf Zeilenebene, eigene Replikation
und Wiederherstellung zu einem Zeitpunkt, und ein Datenbank-Testjob in der CI des Projekts bei jedem Push
(PostgreSQL 15). Es ist die erste Wahl, wenn eine Website einen Datenbankserver braucht, zum Beispiel wenn sich mehrere
Webserver eine Datenbank teilen ([Datenbankunterstützung](../../../../en/us/help/database-support.md#the-supported-databases-in-order)).

## Versionen und Erweiterung

| Element | Wert | Quelle |
|---|---|---|
| Untergrenze des Assistenten | 8.0 | `eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php` |
| Praktisches Minimum | **13** | der Installer legt `pgcrypto` an, ab PostgreSQL 13 eine vertrauenswürdige Erweiterung |
| Ab 15 | der Benutzer der Website muss Eigentümer der Datenbank sein | PostgreSQL lässt andere Benutzer keine Tabellen mehr in `public` anlegen |
| Funktionierend gesehen | 13.23 auf dem Entwicklungsserver; 15 in der CI | [Versionen](../../../../en/us/help/database-support.md#versions-checked-against-the-code) |
| PHP-Erweiterung | `pgsql` (`php-pgsql`, `php<version>-pgsql`) | |

Betreiben Sie ein Release, das Ihr Hersteller noch mit Sicherheitskorrekturen versorgt. Die Untergrenze des Assistenten
ist keine Empfehlung.

## Eine Datenbank pro Website anlegen

Die Installer legen weder Datenbanken noch Benutzer an. Legen Sie beides vor der Installation an, und machen Sie den
Benutzer der Website zum **Eigentümer** seiner Datenbank:

```bash
sudo -u postgres createuser --pwprompt <customer>
sudo -u postgres createdb --owner=<customer> --encoding=UTF8 --template=template0 <customer>
```

Mit `template0` können Sie die Kodierung unabhängig vom Standard des Servers wählen. Ein Panel, das
PostgreSQL-Datenbanken anlegt, setzt vielleicht einen anderen Eigentümer oder eine andere Kodierung; lesen Sie in der
Dokumentation Ihres Panels nach und vergleichen Sie mit den Befehlen oben
([Datenbanken und Benutzer in Control Panels](../control-panels/datenbanken-und-benutzer.md)).

> **Tipp**
> Eine Datenbank und ein Benutzer pro Website, der Benutzer als Eigentümer der Datenbank, ist das einfachste Modell für
> die Trennung zwischen Kunden und für einen späteren Umzug einer Website.

## pgcrypto

Der Treiber berechnet MD5 mit `digest()` aus `pgcrypto`. Der Installer führt `CREATE EXTENSION IF NOT EXISTS pgcrypto`
aus; als vertrauenswürdige Erweiterung (PostgreSQL 13 und später) darf der Eigentümer der Datenbank sie anlegen. Wenn
sie dem Server fehlt, installieren Sie das Contrib-Paket von PostgreSQL Ihrer Distribution und dann als Eigentümer oder
Superuser:

```bash
sudo -u postgres psql -d <customer> -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

Wenn sie fehlt, meldet der Setup-Assistent, dass die Funktion `digest` in der Datenbank nicht verfügbar ist und
Exponential ohne sie nicht laufen kann.

> **Hinweis**
> Installieren Sie das Contrib-Paket auf jedem PostgreSQL-Server, den Sie Exponential-Kunden anbieten. Es ist die eine
> Voraussetzung auf dem Server, die der Benutzer der Website nicht allein beheben kann.

## Einstellungen und Installation

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=<customer>
Password=<the password>
Database=<customer>
UsePersistentConnection=disabled
```

Die Installer schreiben dies nach `settings/override/site.ini.append.php`. Eine Installation über die Konsole, mit dem
Passwort aus der Umgebung, damit es nicht in der Shell-Historie landet:

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=pgsql \
    --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
```

`--db=postgresql` und `--db=postgres` werden ebenfalls akzeptiert; der Kickstarter nimmt `[database_choice] Type=pgsql`.
Der Setup-Assistent füllt Port 5432 und Benutzer `postgres` vor (`settings/setup.ini` `DefaultPort_pgsql`,
`DefaultUser_pgsql`). `UsePersistentConnection=enabled` nutzt `pg_pconnect` statt `pg_connect`; unter Velocity hält
jeder persistente Worker seine Verbindung ohnehin.

## Sequenzen

Spalten mit automatischem Hochzählen nutzen Sequenzen mit dem Namen `<table>_<column>_seq`. Nachdem Sie Daten mit
expliziten IDs geladen haben (ein Import, die Wiederherstellung ausgewählter Tabellen), korrigieren Sie sie, sonst
scheitert das nächste Einfügen mit einem doppelten Schlüssel:

```sql
SELECT setval('ezcontentobject_id_seq', (SELECT max(id) FROM ezcontentobject));
```

Der Schema-Installer tut das selbst, nachdem er Daten geladen hat.

## Backups

```bash
pg_dump -U <customer> -Fc <customer> > <customer>.dump
pg_restore -U <customer> -d <customer> --clean <customer>.dump
```

Ein vollständiges Backup enthält außerdem `var/storage`, `settings/override` und `settings/siteaccess`. Schalten Sie für
eine Wiederherstellung den Wartungsmodus ein, und leeren Sie danach jeden Cache
([Backup und Wiederherstellung](../../../../en/us/help/database-support.md#backup-and-restore)). Wiederherstellung zu einem Zeitpunkt und
Replikation sind eigene Funktionen von PostgreSQL; konfigurieren Sie sie mit der Dokumentation von PostgreSQL.

## Probleme und ihre Ursachen

| Symptom | Ursache |
|---|---|
| Der Assistent meldet, dass die Funktion `digest` nicht verfügbar ist | `pgcrypto` konnte nicht angelegt werden; installieren Sie das Contrib-Paket und legen Sie die Erweiterung an |
| Verbindung abgelehnt | `listen_addresses` in `postgresql.conf` oder eine fehlende Zeile in `pg_hba.conf` |
| Zugriff verweigert beim Anlegen von Tabellen | der Benutzer der Website ist nicht Eigentümer der Datenbank (PostgreSQL 15 und später) |
| Doppelter Schlüssel beim Einfügen nach einem Import | Sequenzen hinter ihren Tabellen; korrigieren Sie sie wie oben |
| Das SQL einer Erweiterung scheitert nur auf PostgreSQL | MySQL-Syntax wie Backticks als Anführungszeichen ([Hinweise für alle Engines](../../../../en/us/install/09-databases.md#98-cross-engine-notes)) |
| Keine Seite lädt, `error.log` meldet „Database implementation not supported“ | ein falsch geschriebenes `DatabaseImplementation` |

## Checkliste

- [ ] Ein unterstütztes PostgreSQL-Release, 13 oder später, mit installiertem Contrib-Paket.
- [ ] Eine Datenbank pro Website, Kodierung `UTF8`, im Eigentum des Benutzers der Website.
- [ ] `pgsql` im Web-PHP und im PHP der Kommandozeile.
- [ ] `pg_hba.conf` erlaubt den Benutzer der Website nur von den Webservern aus.
- [ ] Backups mit `pg_dump` geplant und eine Wiederherstellung getestet.

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)
- [Datenbankunterstützung: PostgreSQL](../../../../en/us/help/database-support.md#2-postgresql)
- [Kapitel 9.4: PostgreSQL](../../../../en/us/install/09-databases.md#94-postgresql)
- [Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)
- [Mehrere Web-Knoten](../betrieb/mehrere-web-knoten.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
