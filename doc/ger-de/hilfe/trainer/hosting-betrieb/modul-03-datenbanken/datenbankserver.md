---
title: "Lektion 3.3: Datenbankserver für eine Website"
audience: trainer, hosting, admin
category: training
order: 33
summary: Die Teilnehmenden legen eine PostgreSQL- oder MariaDB-Datenbank und einen Benutzer mit genau den Rechten an, die Exponential braucht, installieren mit exp:install eine Schulungs-Website darauf und erkennen die üblichen Probleme mit Verbindung, Zeichensatz und Sequenzen. 45 Minuten.
keywords: hosting und betrieb, PostgreSQL, MariaDB, MySQL, MongoDB, Oracle, datenbank anlegen, utf8mb4, pgcrypto, sequenzen, InnoDB, exp:install --db, EXP_INSTALL_DB_PASSWORD, replikate, hosting and operations, create database, sequences, replicas
type: help
---
# Lektion 3.3: Datenbankserver für eine Website

Wenn eine Website einen Datenbankserver braucht, legen Hosting-Mitarbeitende die Datenbank, ihren Benutzer und seine
Rechte an und übergeben dem Installationsprogramm die Verbindung. Diese Lektion zeigt die Schritte für PostgreSQL und
MariaDB (und MySQL, das denselben Treiber verwendet), die Rechte, die genügen, und die Probleme, denen
Support-Mitarbeitende danach begegnen. MongoDB und Oracle behandeln eigene Seiten für Hoster.

> **Hinweis**
> Verwenden Sie einen Datenbankserver auf der eigenen virtuellen Maschine der Person. Legen Sie im Kurs nie Datenbanken
> oder Benutzer auf einem gemeinsam genutzten Server eines Kunden an.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 3: Datenbanken](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 3.2](sqlite-im-produktivbetrieb.md); PostgreSQL oder MariaDB auf der virtuellen Maschine der Person installiert, mit der PHP-Erweiterung `pgsql` oder `mysqli` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine PostgreSQL-Datenbank anlegen, die dem Benutzer der Website gehört, oder eine MariaDB-Datenbank in `utf8mb4` mit
  Rechten pro Datenbank;
- mit `exp:install --db=...` eine Website darauf installieren, ohne das Passwort in die Shell-History zu schreiben;
- erklären, warum `pgcrypto` und die Sequenzen von PostgreSQL wichtig sind;
- die üblichen Probleme erkennen: die Seite „database cannot be reached“, `?` statt Buchstaben mit Akzenten, doppelte
  Schlüssel nach einem Import.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 9, Abschnitte 9.3 und 9.4](../../../../installation/09-datenbanken.md#93-mysql-und-mariadb),
[PostgreSQL für Hoster](../../../hosting/grundlagen/postgresql-fuer-hoster.md) und
[MariaDB für Hoster](../../../hosting/grundlagen/mariadb-fuer-hoster.md).

1. **PostgreSQL** (5 Minuten). `createuser --pwprompt`, dann `createdb --owner=... --encoding=UTF8
   --template=template0`. Der Eigentümer ist wichtig: Ab PostgreSQL 15 dürfen gewöhnliche Benutzer keine Tabellen im
   Schema `public` einer Datenbank anlegen, die ihnen nicht gehört. `pgcrypto` stellt `digest()` bereit; das
   Installationsprogramm legt es an, wenn der Server es mitbringt (ab PostgreSQL 13 eine vertrauenswürdige
   Erweiterung). Nach einem Import mit expliziten IDs bleiben die Sequenzen zurück; korrigieren Sie sie mit `setval`.
2. **MariaDB und MySQL** (5 Minuten). Legen Sie die Datenbank in `utf8mb4` an; vergeben Sie `SELECT, INSERT, UPDATE,
   DELETE, CREATE, DROP, INDEX, ALTER, LOCK TABLES, CREATE TEMPORARY TABLES` nur auf dieser Datenbank. Kein globales
   Recht ist nötig. Das Installationsprogramm legt InnoDB-Tabellen an, wenn der Server es anbietet.
3. **Installieren** (3 Minuten). Übergeben Sie das Passwort in `EXP_INSTALL_DB_PASSWORD`, damit es nicht in der
   Shell-History landet.
4. **Andere Datenbanksysteme** (2 Minuten). MongoDB braucht PHP 8.2, eine Composer-Bibliothek und Indizes; Oracle
   braucht `oci8` und ist meist die eigene Datenbank des Kunden. Verweisen Sie auf ihre Seiten für Hoster.

### Häufige Fragen

**Können Lesezugriffe an ein Replikat gehen?** Bei MySQL und MariaDB schickt `UseSlaveServer=enabled` mit den
Replikatlisten Lesezugriffe an ein pro Anfrage zufällig gewähltes Replikat; Schreibzugriffe gehen an `Server`. Durch
die Replikationsverzögerung kann eine Seite Inhalte zeigen, die einige Sekunden alt sind.

**Warum kommen Buchstaben mit Akzenten als `?` zurück?** Die Verbindung ist auf einen anderen Zeichensatz
zurückgefallen. Schalten Sie `[DebugSettings] AlwaysLog[]=warning` ein, laden Sie eine Seite und suchen Sie in
`var/log/warning.log` nach „could not set the connection charset“.

**Der Assistent lehnt den Zeichensatz der Datenbank ab.** Sie wurde in `latin1` oder einem anderen Ein-Byte-Zeichensatz
angelegt; legen Sie sie in `utf8mb4` neu an.

**PostgreSQL lehnt die Verbindung ab.** Prüfen Sie `listen_addresses` in `postgresql.conf` und `pg_hba.conf`.

### Stolperfallen

- **Passwörter am Beamer.** Verwenden Sie für die Schulungsdatenbank ein Wegwerf-Passwort und zeigen Sie nie ein
  echtes.
- **Alles vergeben.** Teilnehmende, die `GRANT ALL ON *.*` gewohnt sind, sollen sehen, dass Rechte pro Datenbank
  genügen.
- **In eine vorhandene Schulungsdatenbank installieren.** `exp:install` installiert in die angegebene Datenbank;
  verwenden Sie für die Übung eine neue, leere Datenbank.

## Praktische Übung

Verwenden Sie Ihre eigene virtuelle Maschine mit PostgreSQL (Schritte 1 bis 4) oder MariaDB (Schritte 1a bis 4a) und
eine frische Kopie des Codes für eine zweite Schulungsinstallation.

1. PostgreSQL: Legen Sie den Benutzer und die Datenbank an (geben Sie auf Nachfrage ein Schulungspasswort ein):

   ```bash
   sudo -u postgres createuser --pwprompt exptraining
   sudo -u postgres createdb --owner=exptraining --encoding=UTF8 --template=template0 exptraining
   ```

   1a. MariaDB: Führen Sie im Client `mariadb` als Administrator aus:

   ```sql
   CREATE DATABASE exptraining CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'exptraining'@'localhost' IDENTIFIED BY '<a training password>';
   GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
         LOCK TABLES, CREATE TEMPORARY TABLES ON exptraining.* TO 'exptraining'@'localhost';
   ```

2. Sehen Sie sich den Installationsbefehl mit `--help` an und suchen Sie die Werte für `--db`:
   `php bin/php/console exp:install --help`.
3. Installieren Sie, mit dem Passwort in der Umgebung:

   ```bash
   read -rs EXP_INSTALL_DB_PASSWORD; export EXP_INSTALL_DB_PASSWORD
   php bin/php/console exp:install --apply --db=pgsql --db-host=127.0.0.1 --db-name=exptraining --db-user=exptraining
   ```

   3a. MariaDB: dasselbe mit `--db=mariadb`.
4. PostgreSQL: Prüfen Sie, dass `pgcrypto` vorhanden ist:
   `psql -h 127.0.0.1 -U exptraining -d exptraining -c '\dx'`.

   4a. MariaDB: Prüfen Sie das Tabellenformat einer Tabelle:
   `mariadb -u exptraining -p -e "SHOW TABLE STATUS FROM exptraining LIKE 'ezcontentobject'\G"`.
5. Starten Sie die Website mit `php bin/php/console exp:velocity start` und öffnen Sie sie.
6. Öffnen Sie in der Administration **Setup**, **Systeminformationen** und prüfen Sie die Karte **Database**.
7. Führen Sie `unset EXP_INSTALL_DB_PASSWORD` aus und prüfen Sie, dass `history | tail -5` kein Passwort zeigt.

## Erwartetes Ergebnis

- Die Installation läuft durch und die Website antwortet.
- Schritt 4 listet `pgcrypto` auf, oder Schritt 4a zeigt `Engine: InnoDB`.
- Schritt 6 zeigt PostgreSQL oder MariaDB mit der Serverversion und `UTF8` oder `utf8mb4`.
- Schritt 7: Das Passwort erscheint nicht in der Shell-History.
- Der Trainer prüft das, indem er jede Person bittet, Datenbanksystem und Zeichensatz der Karte **Database** laut
  vorzulesen.

## Quiz

1. Warum muss der PostgreSQL-Benutzer Eigentümer der Datenbank der Website sein?
2. Welche MariaDB-Rechte braucht der Benutzer der Website?
   A. `ALL PRIVILEGES ON *.*` B. `SUPER` C. Rechte pro Datenbank einschließlich `LOCK TABLES` und
   `CREATE TEMPORARY TABLES` D. nur Leserechte
3. Wofür wird `pgcrypto` gebraucht?
4. Nach einem Import mit expliziten IDs schlagen Einfügungen in PostgreSQL mit einem doppelten Schlüssel fehl. Was ist
   falsch?
5. Wie übernimmt `exp:install` das Datenbankpasswort, ohne es in der Shell-History zu zeigen?

<details>
<summary>Antworten</summary>

1. Ab PostgreSQL 15 dürfen gewöhnliche Benutzer keine Tabellen im Schema `public` einer Datenbank anlegen, die ihnen
   nicht gehört.
2. **C.** Kein globales Recht ist nötig ([Datenbank und Benutzer anlegen](../../../../installation/09-datenbanken.md#datenbank-und-benutzer-anlegen)).
3. Der Treiber berechnet MD5 mit `digest()`, das aus `pgcrypto` kommt.
4. Die Sequenzen sind hinter ihren Tabellen zurückgeblieben; korrigieren Sie sie mit `setval`.
5. Aus der Umgebungsvariablen `EXP_INSTALL_DB_PASSWORD`.

</details>

## Weiterführende Informationen

- [PostgreSQL für Hoster](../../../hosting/grundlagen/postgresql-fuer-hoster.md)
- [MariaDB für Hoster](../../../hosting/grundlagen/mariadb-fuer-hoster.md) und
  [MySQL für Hoster](../../../hosting/grundlagen/mysql-fuer-hoster.md)
- [MongoDB für Hoster](../../../hosting/grundlagen/mongodb-fuer-hoster.md) und
  [Oracle für Hoster](../../../hosting/grundlagen/oracle-fuer-hoster.md)
- [Datenbanken und Benutzer in Hosting-Panels](../../../hosting/control-panels/datenbanken-und-benutzer.md)

## Verwandte Seiten

- [Modul 3: Datenbanken](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
