---
title: "Migration zu Exponential: Kursüberblick"
audience: trainer, admin, developer
category: training
order: 1
summary: Ein Kurs für Trainer zur Migration von Websites zu Exponential 6 - Planung, Zuordnung, Import- und Exportwerkzeuge, die In-place-Wege von der 4.x-Linie und vom 5.x-Legacy-Stack, die beiden Wege von eZ Platform und Ibexa, WordPress, Drupal, Joomla und statisches HTML, der Abschied von Exponential und ein Abschlussprojekt. Acht Module, 25 Lektionen, etwa 22,5 Stunden Lektionen.
keywords: migration zu exponential, migrationskurs, kursüberblick, lesepfad, zeitplan, migrationsplanung, inhaltszuordnung, abschlussprojekt, exponential verlassen, migrating to exponential, migration course, course overview, reading path, schedule, migration planning, content mapping, xrowextract, 4.x, 5.x, eZ Platform, Ibexa, Nexus, Platform Legacy, WordPress, Drupal, Joomla, static HTML, leaving exponential, capstone
type: help
---
# Migration zu Exponential: Kursüberblick

Dieser Kurs vermittelt Administratoren, Entwicklern und Projektleitern, wie sie eine Website zu Exponential 6
umziehen und ein solches Projekt so planen, dass keine Inhalte, keine Adressen und keine Benutzer verloren gehen. Er
lehrt Methoden und Planung; die Fakten, auf die er sich stützt, stehen in den Migrationskapiteln des
Installationshandbuchs und im Migrations-Hub, auf die jede Lektion verlinkt, statt sie zu kopieren.

Exponential 6 ist die Fortführung des eZ Publish Legacy-Kernels, eines Produkts, das sein Hersteller nach fast 18
Jahren eingestellt hat. Der alte Name erscheint in diesem Kurs nur, um die Versionen zu bezeichnen, von denen die
Teilnehmenden migrieren.

## Für wen er gedacht ist

- **Administratoren**, die Datenbank, Dateien und Einstellungen einer Migration betreuen.
- **Entwickler**, die Importe schreiben, Erweiterungen auf PHP 8 portieren oder Templates neu schreiben.
- **Projektleiter**, die die Phasen, das Einfrieren, die Umstellung und den Rückweg planen (Module 1, 2 und 8).

Nach dem Kurs können die Teilnehmenden den richtigen Migrationsweg für eine Website wählen, eine Bestandsaufnahme
machen, eine Zuordnung und eine Weiterleitungstabelle schreiben, die Import- und Exportwerkzeuge von Exponential
nutzen, eine In-place-Migration von einer älteren Version des Kernels oder einen Umzug von einer Symfony-basierten
Plattform oder einem anderen System planen und einen vollständigen Migrationsplan mit Rückweg schreiben.

## Auf einen Blick

| | |
|---|---|
| Dauer | Etwa 22,5 Stunden Lektionen in 8 Modulen (25 Lektionen), zuzüglich Pausen |
| Form | Präsenz oder online; jede Lektion hat einen Präsentationsteil und eine praktische Übung |
| Niveau | Fortgeschrittene bis Experten |
| Voraussetzungen | Grundkenntnisse eines CMS; Umgang mit der Shell; SQL-Grundlagen für die Module 4 bis 6; PHP für die Lektionen 3.3 und 4.3; Grundlagen von Twig und TPL für Lektion 5.3 |
| Übungsumgebung | Eine Schulungsinstallation von Exponential 6 pro Person, mit Shell-Zugang und einem Administratorkonto |

## Übungsumgebung

- Eine Schulungsinstallation pro Person, installiert mit `php bin/php/console exp:install`
  ([Installation über die Kommandozeile](../../../installation/07-installation-ueber-die-konsole.md)). Die Standardinstallation verwendet
  SQLite in `var/storage/sqlite3/exponential.db`, das die Teilnehmenden mit `sqlite3 -readonly` nur lesend öffnen.
- Die Erweiterungen, die die `composer.json` im Wurzelverzeichnis verlangt, insbesondere xrowextract, aktiv auf der
  Schulungsinstallation.
- Die Kommandozeilenwerkzeuge `sqlite3`, `curl` und `unzip` auf den Schulungsrechnern.
- Jede Person hat einen Kursordner im Inhaltsbaum und kennt dessen Knoten-ID.
- Keine echten Kundendaten auf einer Schulungsinstallation. Testkonten verwenden Adressen unter `example.invalid`.
- Modul 6 ist ein Planungsmodul: Es wird keine Installation von eZ Platform, Ibexa oder Nexus benötigt.
- Die Hinweise für Trainer zu Übungsumgebungen: [Train the Trainer](../../../../eng-us/help/trainers/train-the-trainer/README.md), Modul 2.

## Module

| Modul | Dauer | Lektionen | Inhalt |
|---|---|---|---|
| [1. Eine Migration planen](modul-01-eine-migration-planen/README.md) | 3 Std. 15 Min. | 4 | Die Exponential-Familie und die Wege, Bestandsaufnahme und Umfang, Phasen und Checkliste, Probelauf, Umstellung und Rückweg |
| [2. Inhalte auf Exponential abbilden](modul-02-inhalte-zuordnen/README.md) | 2 Std. 45 Min. | 3 | Klassen, Datentypen und der Baum; Benutzer, Rollen und Passwörter; URLs, Weiterleitungen und Metadaten |
| [3. Import- und Exportwerkzeuge](modul-03-import-und-export-werkzeuge/README.md) | 2 Std. 45 Min. | 3 | Das Werkzeug wählen, Probeläufe mit xrowextract und wiederholbare Importe, ein eigenes Importskript |
| [4. Von der 4.x-Linie](modul-04-von-der-4x-linie/README.md) | 3 Std. 30 Min. | 4 | Bestandsaufnahme, das Datenbank-Runbook, Dateien, Einstellungen und Portierung auf PHP 8, Überprüfung |
| [5. Vom 5.x-Legacy-Stack](modul-05-vom-5x-legacy-stack/README.md) | 2 Std. 45 Min. | 3 | Varianten und die gemeinsame Datenbank, Herauslösen und INI, Feldtypen, Bildvarianten und Twig |
| [6. Von den Symfony-basierten Plattformen](modul-06-von-den-symfony-plattformen/README.md) | 2 Std. 30 Min. | 3 | Zwei Ziele, Weg A zu Nexus, Weg B hinunter zum Legacy-Kernel |
| [7. Von anderen Systemen](modul-07-von-anderen-systemen/README.md) | 2 Std. 45 Min. | 3 | WordPress, Drupal und Joomla, statisches HTML |
| [8. Exponential verlassen und das Abschlussprojekt](modul-08-wechsel-weg-und-abschlussprojekt/README.md) | 2 Std. 15 Min. | 2 | Der vollständige Export und ein Migrationsplan als Abschlussprojekt mit Bewertungsraster |

Die Dauer jedes Moduls enthält jeweils eine Pause von 15 Minuten.

## Lesepfad

Für Trainer, die den Kurs vorbereiten, in dieser Reihenfolge:

1. [Migration zu und von Exponential](../../../installation/migration/README.md): der Hub, seine Produkttabelle und seine
   Entscheidungshilfe.
2. [Die gemeinsame Migrations-Checkliste](../../../installation/migration/checkliste.md): der Rahmen jeder Lektion.
3. [Inhalte auf Exponential abbilden](../../../installation/migration/inhaltsmodell-zuordnen.md) und
   [Die Import- und Exportwerkzeuge von Exponential](../../../installation/migration/exponential-tools.md).
4. [Links, URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md): die Adresswerkzeuge, die
   die Teilnehmenden im Kurs verwenden.
5. Die Module 1 bis 3 dieses Kurses; probieren Sie anschließend deren Übungen auf Ihrer eigenen Schulungsinstallation
   aus.
6. Das Kapitel zu jedem Quellmodul, das Sie unterrichten:
   [Kapitel 14](../../../installation/14-migration-von-4x.md) für Modul 4,
   [Kapitel 15](../../../installation/15-migration-von-5x-legacy.md) für Modul 5,
   [Kapitel 16](../../../installation/16-migration-von-ez-platform-und-ibexa.md) für Modul 6 sowie die Anleitungen zu
   [WordPress](../../../installation/migration/wordpress.md), [Drupal](../../../installation/migration/drupal.md),
   [Joomla](../../../installation/migration/joomla.md) und [statischem HTML](../../../installation/migration/statisches-html.md)
   für Modul 7.
7. [Kapitel 17: Migrationsreferenz](../../../installation/17-migrationsreferenz.md): die Nachschlagetabellen für Fragen im
   Kurs.
8. [Migration weg von Exponential](../../../installation/migration/exponential-verlassen.md) und Modul 8.

Produktversionen und Versionslinien im Hub und in Kapitel 16 ändern sich mit der Zeit: Prüfen Sie sie am Tag Ihres
Kurses.

## Vorgeschlagene Zeitpläne

Aufgebaut aus den oben genannten Lektionsdauern (Pausen pro Tag hinzugefügt).

**Vier Tage, der vollständige Kurs**

| Tag | Inhalt |
|---|---|
| 1 | Modul 1, Modul 2 |
| 2 | Modul 3, Modul 4 Lektionen 4.1 und 4.2 |
| 3 | Modul 4 Lektionen 4.3 und 4.4, Modul 5, Modul 6 Lektion 6.1 |
| 4 | Modul 6 Lektionen 6.2 und 6.3, Modul 7, Modul 8 |

Tag 4 ist der längste; verschieben Sie Lektion 7.2 auf Tag 3, wenn Ihre Tage kurz sind, oder lassen Sie die
Quellmodule weg, die Ihre Teilnehmenden nicht brauchen.

**Zwei Tage: Planung und Werkzeuge für jede Quelle**

| Tag | Inhalt |
|---|---|
| 1 | Modul 1, Modul 2 |
| 2 | Modul 3, die Quell-Lektion, die die Gruppe braucht (zum Beispiel Lektion 7.1), Lektion 8.2 |

**Ein Tag: von WordPress zu Exponential**

Lektionen 1.1, 1.2, 2.1, 2.3, 3.2 und 7.1 (5 Stunden 45 Minuten Lektionen), mit einer abschließenden Diskussion des
Abschnitts zum Rückweg aus Lektion 1.4.

**Zwei halbe Tage für Projektleiter**

Halber Tag 1: Lektionen 1.1 bis 1.4. Halber Tag 2: Lektionen 2.2 und 2.3, dann Lektion 8.2 mit einem reinen
Planungsszenario (ohne den Nachweisschritt).

## Teilnehmende beurteilen

Jede Lektion endet mit einem Quiz mit belegten Antworten, und Lektion 8.2 enthält ein Abschlussprojekt mit einem
Bewertungsraster. Für einen schriftlichen Test und eine praktische Aufgabe zur Migration siehe
[Prüfungen und praktische Übungen](../../../../eng-us/help/trainers/assessments/README.md).
Jede Prüfung, die Sie durchführen, ist Ihr eigenes Kursmaterial; das ist keine offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind davon getrennt ([Zertifizierungen](../../partner/zertifizierungen.md)).

## Verwandte Seiten

- [Trainer: hier beginnen](../../../../eng-us/help/trainers/trainers-start-here.md)
- [Kurskatalog](../../../../eng-us/help/trainers/course-catalogue.md)
- [Train the Trainer: Kursüberblick](../../../../eng-us/help/trainers/train-the-trainer/README.md)
- [Migration zu und von Exponential](../../../installation/migration/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
