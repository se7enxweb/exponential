---
title: "Modul 4: Die technischen Kurse prüfen"
audience: trainer, developer, designer, hosting
category: training
order: 40
summary: Schriftliche Tests mit belegten Lösungsschlüsseln und praktische Aufgaben mit Bewertungsrastern für die technischen Kurse, Entwickler, Theme-Design sowie Hosting und Betrieb. Etwa 7 Stunden 15 Minuten, wenn alle sechs durchgeführt werden.
keywords: prüfungen und praktische übungen, technische kurse, entwickler, theme-design, hosting und betrieb, schriftlicher test, praktische aufgabe, bewertungsraster, erweiterung, theme-erweiterung, sicherung, wiederherstellung, assessments and practical exercises, developers, theme design, hosting and operations, written test, practical task, rubric, extension, backup, restore
type: help
---
# Modul 4: Die technischen Kurse prüfen

Dieses Modul enthält die Prüfungen für die technischen Kurse: Entwickler, Theme-Design sowie Hosting und Betrieb. Die
praktischen Aufgaben sind hier die längsten des Kurses, weil es echte Aufgaben aus dem Arbeitsalltag sind: eine
Erweiterung mit Zugriffskontrolle, ein Theme für einen Siteaccess, eine Sicherung, die als sichere Testkopie
wiederhergestellt wird.

> **Hinweis**
> Dies sind die eigenen Prüfungen des Trainers, keine offizielle Zertifizierung. Jede Aufgabe läuft auf der eigenen
> Schulungsinstallation der Teilnehmenden, mit einer Shell.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Dauer | 7 Stunden 15 Minuten für alle sechs; jedes Paar 2 Stunden 20 Minuten bis 2 Stunden 30 Minuten |
| Lektionen | 6 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [4.1 Entwickler, schriftlicher Test](entwickler-schriftlicher-test.md) | 45 Min. | Vierzehn Fragen zu Erweiterungen, Views, Templates, Git, REST und Releases |
| [4.2 Entwickler, praktische Aufgabe](entwickler-praktische-aufgabe.md) | 105 Min. | Eine geschützte Begrüßungsseite als Erweiterung, ein Override, zwei Git-Commits |
| [4.3 Theme-Designer, schriftlicher Test](theme-designer-schriftlicher-test.md) | 40 Min. | Dreizehn Fragen zu Theme-Erweiterungen, Registrierung, Ladereihenfolge, CSP und Versionen |
| [4.4 Theme-Designer, praktische Aufgabe](theme-designer-praktische-aufgabe.md) | 100 Min. | Ein Theme nur für den öffentlichen Siteaccess, ohne Inline-Code, mit Pfadkommentaren nachverfolgt |
| [4.5 Hosting und Betrieb, schriftlicher Test](hosting-betrieb-schriftlicher-test.md) | 45 Min. | Vierzehn Fragen zu Berechtigungen, Sicherungen, Deployments, Überwachung und Wiederherstellungen |
| [4.6 Hosting und Betrieb, praktische Aufgabe](hosting-betrieb-praktische-aufgabe.md) | 100 Min. | Eine konsistente Sicherung, eine sichere wiederhergestellte Kopie, ein über seine Referenz gefundener Fehler, ein Wiederherstellungsprotokoll |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation pro Teilnehmer, für Lektion 4.2 aus einem Git-Klon, für Lektion 4.6 auf SQLite,
      über einen lokalen Port ausgeliefert, Mail-Transport `file`.
- [ ] Eine Shell, ein Editor und Git für alle Teilnehmenden; für Lektion 4.6 `sqlite3` installiert.
- [ ] Für Lektion 4.6: ein zweites leeres Verzeichnis pro Teilnehmer und pro Installation eine erzeugte Fehlerseite,
      deren Referenz notiert ist.
- [ ] Ausgedruckte Fragen und Bewertungsraster, die Lösungsschlüssel getrennt aufbewahrt.
- [ ] Ein Plan zum Zurücksetzen und Aufräumen: Erweiterungen, Themes und Testkopien werden nach der Bewertung entfernt
      ([Die Übungsumgebung zurücksetzen](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md)).

## Zeitplan

| Zeit | Teil |
|---|---|
| 0:00 bis 0:45 | Lektion 4.1 |
| 0:45 bis 2:30 | Lektion 4.2 |
| 2:30 bis 3:10 | Lektion 4.3 |
| 3:10 bis 4:50 | Lektion 4.4 |
| 4:50 bis 5:35 | Lektion 4.5 |
| 5:35 bis 7:15 | Lektion 4.6 |
| Gesamt | 7 Stunden 15 Minuten, ohne Pausen |

## Verwandte Seiten

- [Modul 1: Prüfungen gestalten](../modul-01-pruefungen-gestalten/README.md)
- [Modul 3: Die Administrationskurse prüfen](../modul-03-administrationskurse/README.md)
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
