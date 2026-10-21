---
title: "Modul 3: Die Administrationskurse prüfen"
audience: trainer, admin
category: training
order: 30
summary: Schriftliche Tests mit belegten Lösungsschlüsseln und praktische Aufgaben mit Bewertungsrastern für die Administrationskurse, Website-Administration, Suche und Multisite. Etwa 6 Stunden 25 Minuten, wenn alle sechs durchgeführt werden.
keywords: prüfungen und praktische übungen, administrationskurse, website-administration, suche, multisite, schriftlicher test, praktische aufgabe, bewertungsraster, rollen, sektionen, caches, verzögerte indizierung, siteaccess, assessments and practical exercises, site administration, search, written test, practical task, rubric, roles, sections, delayed indexing
type: help
---
# Modul 3: Die Administrationskurse prüfen

Dieses Modul enthält die Prüfungen für die Kurse, die Administratoren besuchen: Website-Administration, Suche sowie
Multisite und Seitenpakete. Jeder Kurs hat einen schriftlichen Test und eine praktische Aufgabe. Die Aufgaben ändern
Einstellungen, daher brauchen alle Teilnehmenden eine eigene Schulungsinstallation und eine Shell darin.

> **Hinweis**
> Dies sind die eigenen Prüfungen des Trainers, keine offizielle Zertifizierung. Passen Sie die Fragen an die Version
> und das Seitenpaket Ihrer Schulungsinstallationen an.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Dauer | 6 Stunden 25 Minuten für alle sechs; jedes Paar 1 Stunde 50 Minuten bis 2 Stunden 10 Minuten |
| Lektionen | 6 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [3.1 Website-Administratoren, schriftlicher Test](website-administratoren-schriftlicher-test.md) | 45 Min. | Vierzehn Fragen zu Rollen, Sektionen, Caches, Workflows und Wartung |
| [3.2 Website-Administratoren, praktische Aufgabe](website-administratoren-praktische-aufgabe.md) | 85 Min. | Sektion, eingeschränkte Rolle, Testbenutzer, ein Cache, ein Cronjob und ein Wartungsfenster |
| [3.3 Suche, schriftlicher Test](suche-schriftlicher-test.md) | 40 Min. | Zwölf Fragen zu Suche, Indizierung, Neuaufbau und Solr |
| [3.4 Suche, praktische Aufgabe](suche-praktische-aufgabe.md) | 70 Min. | Auf drei Arten suchen, verzögerte Indizierung nachstellen, indizieren, neu aufbauen und wiederherstellen |
| [3.5 Multisite, schriftlicher Test](multisite-schriftlicher-test.md) | 40 Min. | Zwölf Fragen zu Siteaccesses, Matching, Proxys und Einstellungen pro Website |
| [3.6 Multisite, praktische Aufgabe](multisite-praktische-aufgabe.md) | 75 Min. | Host-Matching mit Testnamen, Nachweis mit curl, eine Empfehlung, Wiederherstellung |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation pro Teilnehmer, frisch installiert, auf einem lokalen Port bereitgestellt, mit dem
      Mail-Transport auf `file`.
- [ ] Administratorzugang und eine Shell im Wurzelverzeichnis der Installation für alle Teilnehmenden.
- [ ] Ein Seitenpaket mit Inhalten in mehreren Ordnern (für Lektion 3.4).
- [ ] Die Namen des öffentlichen Siteaccess und des Administrations-Siteaccess Ihres Seitenpakets (für Lektion 3.6).
- [ ] Ausgedruckte Fragen und Bewertungsraster; die Lösungsschlüssel getrennt aufbewahrt.
- [ ] Ein Plan zum Zurücksetzen, denn diese Aufgaben ändern Einstellungen
      ([Die Übungsumgebung zurücksetzen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md)).

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 bis 0:45 | Lektion 3.1 |
| 0:45 bis 2:10 | Lektion 3.2 |
| 2:10 bis 2:50 | Lektion 3.3 |
| 2:50 bis 4:00 | Lektion 3.4 |
| 4:00 bis 4:40 | Lektion 3.5 |
| 4:40 bis 5:55 | Lektion 3.6 |
| 5:55 bis 6:25 | Zurücksetzen der Installationen und Notieren der Ergebnisse |
| Gesamt | 6 Stunden 25 Minuten, ohne Pausen |

## Verwandte Seiten

- [Modul 1: Prüfungen gestalten](../modul-01-pruefungen-gestalten/README.md)
- [Modul 2: Die redaktionellen Kurse prüfen](../modul-02-redaktionelle-kurse/README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
