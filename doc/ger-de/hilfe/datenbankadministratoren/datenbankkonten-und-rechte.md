---
title: Datenbankkonten und Rechte
audience: dba
category: database-administration
order: 24
summary: Das Datenbankkonto, das eine Exponential-Website auf jeder Engine braucht, und die minimalen Rechte, die funktionieren - Rechte für MySQL und MariaDB, Eigentümerschaft bei PostgreSQL, MongoDB-Rollen in der Datenbank der Website, Oracle-Rechte aus ora-grant.php, Dateirechte bei SQLite - sowie die eigenen Konten für Replikate, den DFS-Cluster und Sicherungen.
keywords: Datenbankbenutzer, Konto, Rechte, minimale Rechte, GRANT, LOCK TABLES, CREATE TEMPORARY TABLES, Eigentümer, PostgreSQL 15 public schema, readWrite, dbAdmin, ora-grant, SELECT_CATALOG_ROLE, root, Superuser, Sicherungskonto, nur lesend, Replikat-Konto, DFS DBUser, database user, account, privileges, least privilege, owner, backup account, read-only, replica account
type: help
---
# Datenbankkonten und Rechte

Diese Seite richtet sich an Datenbankadministratoren, die die Konten anlegen, mit denen sich eine Exponential-Website
verbindet, und jedes Recht begründen müssen. Sie beantwortet: welches Konto die Website auf jeder Engine braucht, welche
Rechte erforderlich sind und warum und welche weiteren Konten (Replikate, Cluster, Sicherungen) eine Umgebung brauchen kann.
Die mitgelieferten Standards sind für die Produktion nicht sicher: `site.ini [DatabaseSettings] User=root` und
`file.ini [eZDFSClusteringSettings] DBUser=root` sind Platzhalter, die zu ersetzen sind.

## Inhalt

1. [Ein Konto je Website](#ein-konto-je-website)
2. [MySQL und MariaDB](#mysql-und-mariadb)
3. [PostgreSQL](#postgresql)
4. [MongoDB](#mongodb)
5. [Oracle](#oracle)
6. [SQLite](#sqlite)
7. [Weitere Konten](#weitere-konten)
8. [Was das Projekt nicht dokumentiert](#was-das-projekt-nicht-dokumentiert)
9. [Checkliste](#checkliste)

## Ein Konto je Website

Exponential verbindet sich mit einem Konto, genannt in `site.ini [DatabaseSettings] User` (oder bei SQLite über das
Dateisystem). Dasselbe Konto führt Seitenaufrufe, Veröffentlichungen, Cronjobs und die Update-Dateien eines Upgrades aus, es
braucht also das Recht, das Schema seiner eigenen Datenbank zu ändern, und nichts außerhalb davon. Verwenden Sie nie ein
administratives Konto (`root`, einen Superuser, `SYS`). Geben Sie jeder Website ihr eigenes Konto und ihre eigene Datenbank
oder ihr eigenes Schema.

## MySQL und MariaDB

```sql
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

| Recht | Warum |
|---|---|
| `SELECT`, `INSERT`, `UPDATE`, `DELETE` | normale Arbeit |
| `CREATE`, `DROP`, `INDEX`, `ALTER` | der Installer und die Update-Dateien |
| `LOCK TABLES` | `lock()` des Treibers verwendet `LOCK TABLES` |
| `CREATE TEMPORARY TABLES` | der Kernel legt für einige Operationen temporäre Tabellen an |

Kein globales Recht, kein `SUPER`, kein Recht, Datenbanken aufzulisten. Beschränken Sie den Host-Teil auf die Webserver.
Quelle: [9.3](../../installation/09-datenbanken.md#datenbank-und-benutzer-anlegen).

## PostgreSQL

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

Machen Sie das Konto zum **Eigentümer** der Datenbank: Ab PostgreSQL 15 dürfen andere Benutzer in ihrem Schema `public` keine
Tabellen mehr anlegen. Als Eigentümer darf es auch `pgcrypto` anlegen, ab PostgreSQL 13 eine vertrauenswürdige Erweiterung.
Es braucht keine Superuser-Rechte, kein `CREATEDB` und kein `CREATEROLE`. Beschränken Sie es in `pg_hba.conf` auf die
Webserver und diese Datenbank.

## MongoDB

Legen Sie den Benutzer in der eigenen Datenbank der Website an, weil der Adapter diese Datenbank als
Authentifizierungsdatenbank verwendet: Rollen `readWrite` und `dbAdmin` nur auf dieser Datenbank
([MongoDB einrichten](mongodb-einrichten.md#den-benutzer-anlegen)). Ein Benutzer ist auch auf einem Server ohne Zugriffskontrolle
erforderlich.

## Oracle

`php extension/ezoracle/bin/php/ora-grant.php` gibt die Rechte aus, die ein DBA ausführt: Das Konto darf Sessions, Tabellen,
Views, Trigger, Sequenzen und Prozeduren anlegen. Fügen Sie `SELECT_CATALOG_ROLE` hinzu, wenn Sie die Befehle `health` und
`report` verwenden; für `datapump` READ und WRITE auf das Verzeichnisobjekt
([Oracle mit ezoracle einrichten](oracle-einrichten.md#das-konto-anlegen)).

## SQLite

Es gibt keine Datenbankkonten. Das Dateisystem ist die Zugriffskontrolle: Jeder Prozess, der die Website verwendet, muss das
Verzeichnis und seine Dateien über eine gemeinsame Gruppe schreiben können; niemand sonst sollte sie lesen können
([SQLite-Dateien, Eigentümer und Rechte](sqlite-dateien-eigentuemer-und-rechte.md)).

## Weitere Konten

| Konto | Wo es konfiguriert wird | Braucht |
|---|---|---|
| Lesereplikate (MySQL, MariaDB) | `SlaverServerUser[]`, `SlaverServerPassword[]` | Lesezugriff; `CREATE TEMPORARY TABLES` für einige Lesewege ([Lesereplikate](mysql-lesereplikate.md)) |
| Metadaten des DFS-Clusters | `file.ini [eZDFSClusteringSettings] DBUser`, `DBPassword` und `config.cluster.php` | Lesen und Schreiben auf die Cluster-Tabelle(n) in ihrer eigenen Datenbank ([Clustering](clustering-dfs-und-die-datenbank.md)) |
| Sicherungen | Ihr Sicherungswerkzeug | Lesezugriff und, für konsistente MySQL-Dumps, was `mysqldump --single-transaction` auf Ihrem Release braucht; verwenden Sie die Optionsdateien der Werkzeuge, keine Passwörter auf der Kommandozeile |
| Schemawerkzeuge (`ezsqldumpschema.php`, `ezsqlinsertschema.php`) | `--user`, `--password` auf der Kommandozeile | wie das Konto der Website; das Passwort ist in der Prozessliste sichtbar, führen Sie sie also auf einer Maschine aus, die Sie kontrollieren, oder mit einem vorübergehenden Passwort |

## Was das Projekt nicht dokumentiert

Das Projekt dokumentiert und testet ein Konto je Website, das sowohl die Website betreibt als auch Schemaänderungen anwendet.
Eine Aufteilung in ein Laufzeitkonto ohne DDL-Rechte und ein Migrationskonto beschreibt und testet das Projekt nicht.
Verlangt Ihre Richtlinie sie, geben Sie dem Migrationskonto die DDL-Rechte, lassen Sie dem Laufzeitkonto
`CREATE TEMPORARY TABLES` und `LOCK TABLES` und testen Sie jede Update-Datei und die Einrichtung zuerst auf Staging.

## Checkliste

- [ ] Keine Website verbindet sich als `root`, Superuser oder `SYS`.
- [ ] Jede Website hat ihr eigenes Konto, beschränkt auf ihre eigene Datenbank oder ihr eigenes Schema.
- [ ] Beschränkungen nach Host oder Netz erlauben nur die Webserver (und Cron-Hosts).
- [ ] `DBUser` des DFS-Clusters ist nicht das mitgelieferte `root`.
- [ ] Sicherungskonten lesen nur, was sie sichern.
- [ ] Passwörter liegen nur in `settings/override/` und in der Cluster-Datei ([Die Datenbank absichern](die-datenbank-absichern.md)).

## Verwandte Seiten

- [Die Datenbank absichern](die-datenbank-absichern.md)
- [Datenbanken und Benutzer in Hosting-Panels](../hosting/control-panels/datenbanken-und-benutzer.md)
- [Benutzer und Pools je Kunde](../hosting/sicherheit/benutzer-und-pools-pro-kunde.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
