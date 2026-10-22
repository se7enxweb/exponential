---
title: PostgreSQL einrichten
audience: dba
category: database-administration
order: 12
summary: Wie Sie PostgreSQL für eine Exponential-Website vorbereiten - der Benutzer und eine UTF8-Datenbank, deren Eigentümer er ist, die Erweiterung pgcrypto, die der Treiber für MD5 braucht, der Einstellungsblock, die Installer und die serverseitigen Einstellungen (listen_addresses, pg_hba.conf), die die erste Verbindung am häufigsten verhindern.
keywords: PostgreSQL, Postgres, createuser, createdb, Eigentümer, UTF8, template0, pgcrypto, digest, CREATE EXTENSION, pg_hba.conf, listen_addresses, ezpostgresql, pgsql, exp:install --db=pgsql, PostgreSQL 15 public schema, owner, setup
type: help
---
# PostgreSQL einrichten

Diese Seite richtet sich an Datenbankadministratoren, die PostgreSQL, den empfohlenen Datenbankserver des Projekts, für
eine Exponential-Website vorbereiten. Sie beantwortet: wie Sie das Konto und die Datenbank anlegen, warum das Konto
Eigentümer der Datenbank sein muss, warum `pgcrypto` gebraucht wird und wie Sie es bereitstellen, wie die Einstellungen
aussehen und wie Sie den Installer ausführen. Quelle: [9.4 PostgreSQL](../../installation/09-datenbanken.md#94-postgresql);
Treiber `lib/ezdb/classes/ezpostgresqldb.php`.

## Inhalt

1. [Anforderungen](#anforderungen)
2. [Den Benutzer und die Datenbank anlegen](#den-benutzer-und-die-datenbank-anlegen)
3. [pgcrypto](#pgcrypto)
4. [Die Verbindung erlauben](#die-verbindung-erlauben)
5. [Die Einstellungen](#die-einstellungen)
6. [Installieren](#installieren)
7. [Erste Prüfungen](#erste-prüfungen)
8. [Checkliste](#checkliste)

## Anforderungen

- PostgreSQL **13 oder neuer** (der Assistent akzeptiert 8.0, aber `pgcrypto` ist erst ab 13 eine vertrauenswürdige
  Erweiterung, und darauf verlässt sich der Installer). Die CI des Projekts verwendet PostgreSQL 15; der
  Entwicklungsserver 13.
- Die PHP-Erweiterung `pgsql` in jedem PHP, das die Website verwendet ([PHP-Erweiterungen je Engine](php-erweiterungen-je-engine.md)).
- Das Contrib-Paket von PostgreSQL Ihrer Distribution, das `pgcrypto` enthält, falls der Server es noch nicht hat.

## Den Benutzer und die Datenbank anlegen

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

- **Der Eigentümer ist wichtig.** Ab PostgreSQL 15 dürfen gewöhnliche Benutzer im Schema `public` einer Datenbank, deren
  Eigentümer sie nicht sind, keine Tabellen mehr anlegen. Macht man das Konto der Website zum Eigentümer, erhält es, was
  der Installer und jede spätere Update-Datei brauchen, und nicht mehr.
- **`--template=template0`** lässt Sie die Kodierung unabhängig vom Standard des Servers wählen.
- **`--pwprompt`** fragt nach dem Passwort, statt es von der Kommandozeile zu nehmen.

Keiner der Installer legt die Datenbank oder den Benutzer an; legen Sie sie zuerst an.

## pgcrypto

Der Treiber berechnet MD5 mit `encode(digest(..., 'md5'), 'hex')`, und `digest()` kommt aus `pgcrypto`. Der Installer
führt `CREATE EXTENSION IF NOT EXISTS pgcrypto` aus, wenn der Server die Erweiterung mitbringt; als vertrauenswürdige
Erweiterung darf der Eigentümer der Datenbank sie anlegen. Fehlt sie auf dem Server, installieren Sie das Contrib-Paket
und dann, als Eigentümer oder Superuser:

```bash
sudo -u postgres psql -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

Fehlt sie, meldet der Einrichtungsassistent: "The 'digest' function is not available in your database, and Exponential
cannot run without it." Legen Sie die Erweiterung an und klicken Sie erneut auf Weiter. Auch die Update-Dateien brauchen
`digest()` ([11.3 Hinweise je Datenbank](../../installation/11-aktualisieren.md#hinweise-je-datenbank)).

## Die Verbindung erlauben

"Connection refused" oder ein Authentifizierungsfehler bei der ersten Verbindung liegt meist an der eigenen Konfiguration
des Servers:

- `listen_addresses` in `postgresql.conf` muss die Adresse enthalten, mit der sich die Webserver verbinden (für eine
  Datenbank auf derselben Maschine genügt `localhost`);
- `pg_hba.conf` braucht eine Zeile, die dem Benutzer der Website von den Adressen der Webserver aus den Zugriff auf die
  Datenbank der Website mit einer Passwortmethode erlaubt.

Laden Sie PostgreSQL nach einer Änderung neu. Maßgeblich ist das Kapitel
[Client Authentication](https://www.postgresql.org/docs/current/client-authentication.html) von PostgreSQL.

## Die Einstellungen

In `settings/override/site.ini.append.php` (die Installer schreiben sie):

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
# 5432 is the server's default
Port=5432
User=exponential
Password=<the password>
Database=exponential
# enabled: pg_pconnect instead of pg_connect
UsePersistentConnection=disabled
```

Die Aliase `postgresql` und `pgsql` nennen denselben Treiber.

## Installieren

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=pgsql \
    --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
```

`exp:install` nimmt das Passwort aus `EXP_INSTALL_DB_PASSWORD`, sodass es nicht in der Prozessliste erscheint.
`--db=postgresql` und `--db=postgres` werden ebenfalls akzeptiert. Der Einrichtungsassistent füllt Port 5432 und Benutzer
`postgres` vor (`settings/setup.ini DefaultPort_pgsql`, `DefaultUser_pgsql`); ersetzen Sie den Benutzer durch das Konto der
Website. Der Kickstarter nimmt `[database_choice] Type=pgsql`.

## Erste Prüfungen

```bash
psql -U exponential -d exponential -c '\dx'                       # pgcrypto is listed
psql -U exponential -d exponential -c "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
psql -U exponential -d exponential -c '\dt' | wc -l               # the kernel's tables are there
```

Öffnen Sie dann **Setup > Systeminformationen**: Die Karte Datenbank zeigt PostgreSQL, die Serverversion und `UTF8`.

## Checkliste

- [ ] PostgreSQL 13 oder neuer, ein Release mit Sicherheitsunterstützung.
- [ ] Das Konto der Website ist Eigentümer der Datenbank; die Datenbank ist `UTF8`.
- [ ] `pgcrypto` existiert in der Datenbank der Website.
- [ ] `pg_hba.conf` erlaubt nur die Webserver, mit einer Passwortmethode.
- [ ] Das Konto ist kein Superuser.
- [ ] Sicherungen mit `pg_dump -Fc` sind eingeplant ([Sicherungen je Engine](sicherungen-je-engine.md)).

## Verwandte Seiten

- [PostgreSQL betreiben: Sequenzen, Groß-/Kleinschreibung und Stolperfallen](postgresql-betrieb.md)
- [Datenbankkonten und Rechte](datenbankkonten-und-rechte.md)
- [PostgreSQL für Hoster](../hosting/grundlagen/postgresql-fuer-hoster.md)
- [Datenbanken und Benutzer in Hosting-Panels](../hosting/control-panels/datenbanken-und-benutzer.md#2-postgresql)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
