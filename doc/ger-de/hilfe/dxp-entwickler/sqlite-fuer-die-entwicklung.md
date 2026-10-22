---
title: SQLite für die Entwicklung
audience: dxp-developer
category: dxp-development
order: 11
summary: Ein DXP-Projekt ohne Datenbankserver auf SQLite betreiben - der DSN, der synchrone Messenger-Transport, die Installation, Dateiberechtigungen, die zwei typischen Fehler und die Grenzen, die SQLite von stark genutzten Produktionswebsites fernhalten.
keywords: SQLite, Entwicklung, DATABASE_URL, data_dev.db, MESSENGER_TRANSPORT_DSN, sync, schreibgeschützte Datenbank, readonly database, no such table, pdo_sqlite, Laptop, Demo, ohne Netzverbindung, development, air-gapped
type: help
---
# SQLite für die Entwicklung

Diese Seite richtet sich an Entwickler, die ein DXP-Projekt auf einem Laptop, in einer Test-Pipeline oder auf einem
Demo-Server betreiben möchten, ohne einen Datenbankserver zu installieren. Die Unterstützung von SQLite gehört zu dem,
was die DXP gegenüber der Upstream-Linie ergänzt
([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)).

## Inhalt

1. [Fünf Schritte](#fünf-schritte)
2. [Zwei Fehler, denen Sie begegnen werden](#zwei-fehler-denen-sie-begegnen-werden)
3. [Grenzen](#grenzen)
4. [Später auf eine Serverdatenbank umziehen](#später-auf-eine-serverdatenbank-umziehen)

## Fünf Schritte

1. **Prüfen Sie die Erweiterungen.**

   ```bash
   php -m | grep -i sqlite      # expect SQLite3 and pdo_sqlite
   ```

2. **Setzen Sie den DSN** in `.env.local` und entfernen Sie `DATABASE_DRIVER`, `DATABASE_HOST` und die übrigen Teile:

   ```bash
   DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
   MESSENGER_TRANSPORT_DSN=sync://
   ```

   Die Messenger-Zeile ist wichtig: Der Standard-Transport über Doctrine braucht eine zweite Datenbankverbindung, die
   SQLite ihm unter Umständen nicht gibt; `sync://` verarbeitet Nachrichten in derselben Anfrage
   ([Messenger und Benachrichtigungen](messenger-und-benachrichtigungen.md)).

3. **Installieren Sie.** Der Befehl legt `var/data_dev.db` (oder `var/data_prod.db`) an, importiert die Ausgangsinhalte
   und legt die Doctrine-Tabellen an.

   ```bash
   php bin/console exponential:install exponential-oss
   ```

4. **Lassen Sie den Webserver die Datei schreiben.** SQLite schreibt bei jeder Bearbeitung in die Datei:

   ```bash
   chmod 664 var/data_dev.db
   chown "$USER":www-data var/data_dev.db     # your web server's group
   ```

5. **Leeren Sie den Cache:** `php bin/console cache:clear`.

## Zwei Fehler, denen Sie begegnen werden

| Fehler | Ursache | Abhilfe |
|---|---|---|
| `no such table: ibexa_section` | die Datenbankdatei wurde nie initialisiert | den Installationsbefehl ausführen; existiert eine leere Datei, diese zuerst entfernen und erneut installieren |
| `attempt to write a readonly database` | die Installation lief unter einem anderen Benutzer als der Webserver | Eigentümer und Modus wie in Schritt 4 korrigieren |

## Grenzen

Aus der Anleitung des Skeletons:

| Grenze | Auswirkung |
|---|---|
| keine gleichzeitigen Schreibtransaktionen | Schreibvorgänge stauen sich unter Last und können eine Zeitüberschreitung erreichen |
| keine Replikation und kein Clustering | nur ein Server |
| kein FULLTEXT-Index | die Volltextsuche fällt auf `LIKE`-Abfragen zurück |
| Sperren auf Dateiebene | die Datei muss auf einem lokalen Dateisystem liegen, nicht auf NFS oder CIFS |

SQLite ist richtig für Entwicklung, automatisierte Tests, Demos und Installationen ohne Netzverbindung. Verwenden Sie
für jede öffentliche Website oder Website mit mehreren Benutzern MySQL 8.0, MariaDB 10.6 oder PostgreSQL 14 oder neuer.

## Später auf eine Serverdatenbank umziehen

Die Anleitung des Skeletons stellt ein SQLite-Projekt mit `sqlite3-to-mysql` auf MySQL oder mit `pgloader` auf
PostgreSQL um. Entfernen Sie danach den SQLite-DSN und den Transport `sync://` aus `.env.local`
([Datenbanken, zwischen Engines umstellen](datenbanken-mysql-postgresql-sqlite.md#zwischen-engines-umstellen)).

## Verwandte Seiten

- [SQLite für Exponential Platform](../../funktionen/6.0/plattform-sqlite-installation.md)
- [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md)
- [Datenbankunterstützung in Exponential 6](../datenbankunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
