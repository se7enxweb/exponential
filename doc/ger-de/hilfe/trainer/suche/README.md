---
title: "Suche: Kursüberblick"
audience: trainer, admin, developer
category: training
order: 1
summary: Der Kurs für Administratoren und Entwickler, die die Suche einer Exponential-Website betreiben, wie die Suche funktioniert, Indizierung, Suchen in der Praxis, Solr über ezfind, Feinabstimmung und Fehlersuche. Fünf Module, 20 Lektionen, etwa 10 Stunden Lektionen.
keywords: suchkurs, kursüberblick, lesepfad, suche, indizierung, solr, ezfind, suchstatistiken, inhaltsfinder, fehlersuche in der suche, search course, course overview, reading path, search, indexing, search statistics, content finder, troubleshooting search
type: help
---
# Suche: Kursüberblick

Dieser Kurs vermittelt Administratoren und Entwicklern, wie die Suche auf einer Exponential-Website funktioniert und
wie sie funktionsfähig bleibt: was die eingebaute Suchmaschine leistet, wann Solr über die Erweiterung ezfind die
bessere Wahl ist, wie Inhalte in den Index gelangen, was Redakteure und Besucher sehen, wenn sie suchen, und was zu
prüfen ist, wenn ein Ergebnis fehlt oder die Suche langsam ist.

## Für wen er gedacht ist

- Website-Administratoren, die Fragen wie „Warum finde ich meinen Artikel nicht?“ beantworten.
- Entwickler, die die Suche für eine Website einrichten oder eine Website von der eingebauten Suchmaschine auf Solr
  umstellen.
- Hosting-Mitarbeitende, die den Solr-Server neben einer Installation betreiben.

**Ergebnis:** Nach dem Kurs können die Teilnehmenden erklären, wie ein Element in den Suchindex gelangt, den Index neu
aufbauen, ein Such-Backend auswählen und einrichten, die Suchstatistiken lesen und eine Checkliste abarbeiten, wenn
Ergebnisse fehlen.

## Form und Dauer

| | |
|---|---|
| Dauer | 20 Lektionen, etwa 10 Stunden Lektionen (mit Pausen anderthalb bis zwei Tage) |
| Form | Präsenz oder online; praxisorientiert |
| Niveau | Fortgeschrittene; Modul 4 für Experten |
| Voraussetzungen | [Website-Administration](../website-administratoren/README.md) (mindestens die Module zu Caches und Cronjobs); ein Terminal |
| Übungsumgebung | Eine Schulungsinstallation pro Person, installiert mit `php bin/php/console exp:install` und ausgeliefert mit `exp:velocity start`; für Modul 4 eine Installation auf MySQL oder PostgreSQL, eine Java-Laufzeitumgebung und das Paket ezfind (siehe die Vorbereitungsliste des Moduls) |

## Module

| Modul | Lektionen | Dauer | Ergebnis |
|---|---|---|---|
| [1. Wie die Suche funktioniert](modul-01-wie-die-suche-funktioniert/README.md) | 4 | 1 Std. 45 Min. | Kennt die Orte, an denen gesucht wird, die eingebaute Suchmaschine, Solr über ezfind und weiß, wie man wählt |
| [2. Indizierung](modul-02-indizierung/README.md) | 4 | 2 Std. | Verfolgt ein Element bis in den Index, nutzt die verzögerte Indizierung, baut den Index neu auf, wählt durchsuchbare Felder |
| [3. Suchen in der Praxis](modul-03-suchen-in-der-praxis/README.md) | 4 | 2 Std. | Nutzt die Admin-Suche, die erweiterte Suche und den Inhaltsfinder; prüft die Website-Suche; liest Statistiken |
| [4. Solr und ezfind](modul-04-solr-und-ezfind/README.md) | 4 | 2 Std. 10 Min. | Installiert ezfind mit einem Solr-Server, nutzt exp:solr, indiziert mit Solr, arbeitet mit Relevanz und Facetten |
| [5. Feinabstimmung und Fehlersuche](modul-05-feinabstimmung-und-fehlersuche/README.md) | 4 | 2 Std. | Stimmt die Sucheinstellungen ab, behandelt Sprachen, findet fehlende Ergebnisse, misst die Leistung |

## Lesepfad

Für Trainer, die diesen Kurs vorbereiten, in dieser Reihenfolge:

1. [Trainer: hier beginnen](../trainer-hier-beginnen.md) und der [Kurskatalog](../kurskatalog.md).
2. [Suchen](../../suchen.md): die Suche, wie Redakteure und Besucher sie erleben.
3. [Such-Backends](../../hosting/grundlagen/such-backends.md): die eingebaute Suchmaschine und Solr aus Sicht eines
   Hosters.
4. [Webserver- und Solr-Befehle](../../../funktionen/6.0/befehle-fuer-webserver-und-solr.md): die Konsolenbefehle, die in
   Modul 4 verwendet werden.
5. [Cronjobs](../../../anleitungen/cronjobs.md): Die verzögerte Indizierung hängt von ihnen ab.
6. [Caches](../../../anleitungen/caches.md): Veraltete Ergebnisse sind oft eine Cache-Frage.
7. [Installation über die Kommandozeile](../../../installation/07-installation-ueber-die-konsole.md): die Übungsumgebung, einschließlich der
   Datenbankwahl, die Modul 4 braucht.
8. Die Module 1 bis 5 dieses Kurses, der Reihe nach. Proben Sie Modul 4 vor dem Kurs vollständig auf einem Rechner der
   Übungsumgebung mit Ihrer eigenen Java-Laufzeitumgebung; es ist das Modul, das am ehesten am Kurstag scheitert.

## Vorgeschlagene Zeitpläne

| Zeitplan | Plan |
|---|---|
| Zwei Tage | Tag 1: Module 1, 2 und 3. Tag 2: Module 4 und 5, dann eine Übung zur Fehlersuche auf einer eigenen Installation einer Person |
| Anderthalb Tage, nur eingebaute Suche | Tag 1: Module 1, 2 und 3. Tag 2 vormittags: Modul 5; Modul 4 entfällt |
| Fünf halbe Tage | Ein Modul pro halbem Tag, der Reihe nach |
| Selbststudium | Ein Modul pro Woche, mit den Übungen auf Ihrer eigenen Installation |

## Teilnehmende beurteilen

Jedes Modul schließt seine Lektionen mit einem Quiz und einem erwarteten Ergebnis ab, das der Trainer prüfen kann. Als
abschließende praktische Aufgabe verbirgt der Trainer auf der Installation einer Person ein Element vor der Suche (zum
Beispiel indem er einen Artikel als Entwurf belässt, ihn verbirgt oder die verzögerte Indizierung einschaltet, ohne
den Cronjob auszuführen), und die Person findet die Ursache mit der Checkliste aus Lektion 5.3. Das ist die eigene
Prüfung des Trainers, keine offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind davon getrennt
([Zertifizierungen](../../partner/zertifizierungen.md)). Der Kurs Prüfungen und praktische Übungen im Katalog enthält
weiteres Material.

## Verwandte Seiten

- [Trainer: hier beginnen](../trainer-hier-beginnen.md)
- [Kurskatalog](../kurskatalog.md)
- [Suchen](../../suchen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
