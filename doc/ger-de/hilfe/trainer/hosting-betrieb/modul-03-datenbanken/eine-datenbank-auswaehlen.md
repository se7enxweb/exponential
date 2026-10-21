---
title: "Lektion 3.1: Eine Datenbank wählen"
audience: trainer, hosting, admin
category: training
order: 31
summary: Die Teilnehmenden nennen die sechs unterstützten Datenbanken in der offiziellen Reihenfolge des Projekts, vergleichen sie für ein Hosting-Angebot und finden in der Administration und auf der Kommandozeile heraus, welche Datenbank eine Website verwendet. 40 Minuten.
keywords: hosting und betrieb, datenbank, offizielle reihenfolge, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, exp:ini, systeminformationen, hosting and operations, database, official order, System information
type: help
---
# Lektion 3.1: Eine Datenbank wählen

Exponential 6.x unterstützt sechs Datenbanken, und das Projekt empfiehlt sie in einer festen Reihenfolge.
Hosting-Mitarbeitende müssen sagen können, welche ein Tarif enthält, was der Betrieb jeder kostet und welche Datenbank
die Website eines Kunden tatsächlich verwendet. Diese Lektion gibt ihnen die Reihenfolge, den Vergleich und die zwei
Prüfungen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 3: Datenbanken](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren, Support-Mitarbeitende im Hosting und Produktmanager |
| Voraussetzungen | [Modul 2](../modul-02-velocity/README.md); ein Administratorkonto auf der Schulungsinstallation der Person |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die sechs unterstützten Datenbanken in der offiziellen Reihenfolge aufzählen und erklären, was die Reihenfolge
  bedeutet;
- sie anhand der Fragen vergleichen, die für einen Hoster wichtig sind: ein zu betreibender Server, mehrere Webserver,
  PHP-Erweiterung, Sicherungswerkzeug;
- eine Datenbank für einen Tarif mit einem Rechner und für einen Tarif mit mehreren Webservern wählen;
- die Datenbank einer Website unter **Setup**, **Systeminformationen** und mit `exp:ini` finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Datenbankunterstützung](../../../datenbankunterstuetzung.md) und
[Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md).

1. **Die Reihenfolge** (4 Minuten). Lesen Sie die Unterstützungserklärung laut vor: 1. SQLite 3 (der Standard),
   2. PostgreSQL, 3. MongoDB, 4. MariaDB, 5. Oracle, 6. MySQL. Microsoft SQL Server gilt als mit zusätzlichem Aufwand
   möglich und hat in 6.0.x keinen Treiber. Die Reihenfolge ist die Reihenfolge der Empfehlung für neue Websites; sie
   bedeutet nicht, dass die späteren weniger gut funktionieren. Bitten Sie die Teilnehmenden, in ihrem eigenen Material
   dieselbe Reihenfolge zu verwenden.
2. **Nebeneinander** (6 Minuten). Zeigen Sie die Vergleichstabelle der Hosting-Seite: Wert von
   `DatabaseImplementation`, PHP-Erweiterung, Wert für `exp:install --db=`, ob sich mehrere Webserver die Datenbank
   teilen können (alle außer SQLite), und das Sicherungswerkzeug.
3. **Wählen** (5 Minuten). Mehr als ein Rechner schreibt in die Datenbank: ein Datenbankserver, PostgreSQL zuerst. Ein
   einzelner Rechner, gemeinsam genutzt oder nicht: SQLite deckt das ab, ohne dass etwas zu betreiben ist. Kunden, die
   MongoDB oder Oracle mitbringen: als Option mit ihren Anforderungen anbieten.
4. **Welche Datenbank verwendet diese Website?** (5 Minuten). Zeigen Sie die Karte **Database** auf
   **Systeminformationen** und den Befehl `exp:ini get`.

### Häufige Fragen

**Ist SQLite ein Spielzeug für die Entwicklung?** Nein. Kapitel 9 erklärt, warum es eine Produktivdatenbank für einen
Rechner ist: WAL, eine für einen Webserver eingerichtete Verbindung und eine Schreibwarteschlange (Lektion 3.2).

**Warum steht MySQL zuletzt?** Nicht, weil es weniger gut funktioniert, sondern weil das Projekt für neue Websites
zuerst die anderen empfiehlt. Es wird in der CI des Projekts getestet.

**Kann ich Microsoft SQL Server anbieten?** In 6.0.x gibt es keinen Treiber; es gilt als mit zusätzlichem Aufwand
möglich. Setzen Sie es nicht auf ein Bestellformular.

**Welcher Zeichensatz?** Überall UTF-8: `utf8mb4` für MySQL und MariaDB, `UTF8` für PostgreSQL, `AL32UTF8` für Oracle.

### Stolperfallen

- **Die Teilnehmenden ordnen die Liste aus Gewohnheit um.** Korrigieren Sie es jedes Mal behutsam; die Reihenfolge ist
  Teil der Unterstützungserklärung des Projekts.
- **„Unterstützt“ gegenüber „getestet“.** Die Vergleichstabelle sagt, welche Datenbanksysteme in der CI des Projekts
  laufen. Versprechen Sie nicht mehr, als die Tabelle sagt.
- **Einstellungsdateien von Hand lesen.** `settings/override/site.ini.append.php` kann ein Passwort enthalten.
  Verwenden Sie `exp:ini get` für einen Wert, statt die Datei am Beamer auszugeben.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation (installiert mit `exp:install`, standardmäßig SQLite).

1. Melden Sie sich in der Administration Ihrer Schulungs-Website als Administrator an.
2. Öffnen Sie **Setup**, dann **Systeminformationen**.
3. Suchen Sie die Karte **Database** und notieren Sie das Datenbanksystem, die Serverversion oder Datei, den
   Zeichensatz, die Anzahl der Tabellen und die Größe.
4. Scrollen Sie zu **Database connection** und notieren Sie, was dort steht.
5. Fragen Sie in einem Terminal im Wurzelverzeichnis der Installation nach dem Treiber:

   ```bash
   php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation
   ```

6. Prüfen Sie, welche PHP-Datenbankerweiterungen Ihr PHP hat: `php -m | grep -iE 'sqlite3|pgsql|mysqli|mongodb|oci8'`.
7. Öffnen Sie [Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
   und schreiben Sie für zwei gedachte Tarife je einen Satz: einen Tarif mit einem Rechner für kleine Websites und einen
   Tarif mit zwei Webservern hinter einem Load Balancer.

## Erwartetes Ergebnis

- Die Schritte 3 und 5 stimmen überein: Auf einer Standard-Schulungsinstallation ist das Datenbanksystem SQLite und der
  Treiber `sqlite3`.
- Schritt 6 listet mindestens `sqlite3` auf.
- Schritt 7: Der Tarif mit einem Rechner kann SQLite verwenden; der Tarif mit zwei Servern braucht einen
  Datenbankserver, PostgreSQL zuerst.
- Der Trainer prüft das, indem er drei Teilnehmende nach ihren Sätzen aus Schritt 7 fragt.

## Quiz

1. Bringen Sie in die offizielle Reihenfolge: MySQL, SQLite 3, MariaDB, PostgreSQL.
2. Welche unterstützte Datenbank können sich mehrere Webserver nicht teilen?
   A. PostgreSQL B. MariaDB C. SQLite 3 D. MongoDB
3. Welchen Wert von `DatabaseImplementation` verwenden MySQL und MariaDB?
4. Wo in der Administration sehen Sie das Datenbanksystem und seine Größe?
5. Richtig oder falsch: Eine Datenbank weiter hinten in der Reihenfolge funktioniert weniger gut als eine weiter vorn.

<details>
<summary>Antworten</summary>

1. SQLite 3, PostgreSQL, MariaDB, MySQL.
2. **C.** WAL verwendet gemeinsamen Speicher auf einem Rechner; ein Netzwerkdateisystem ist kein Ersatz.
3. `ezmysqli`.
4. **Setup**, **Systeminformationen**, die Karte **Database**
   ([Datenbankunterstützung](../../../datenbankunterstuetzung.md#welche-datenbank-verwendet-meine-site)).
5. **Falsch.** Die Reihenfolge ist eine Empfehlung für neue Websites, keine Qualitätsrangliste.

</details>

## Weiterführende Informationen

- [Datenbankunterstützung](../../../datenbankunterstuetzung.md): die Unterstützungserklärung und jede Einzelheit.
- [Kapitel 9: Datenbanken](../../../../installation/09-datenbanken.md): die Referenz für Betreiber.
- [Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md): der
  Vergleich für Hoster.
- [Stand von Microsoft SQL Server](../../../hosting/grundlagen/stand-von-microsoft-sql-server.md): was „mit zusätzlichem
  Aufwand möglich“ bedeutet.

## Verwandte Seiten

- [Modul 3: Datenbanken](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
