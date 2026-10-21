---
title: "Website-Administration: Kursüberblick"
audience: trainer, admin
category: training
order: 1
summary: Der Kurs für Menschen, die eine Exponential-Website betreiben, mit dem Setup-Bereich und den Einstellungen, Benutzern, Rollen und Richtlinien, Sektionen, Siteaccesses und Zuständen, Caches, Cronjobs und Workflows, Logs und dem Audit-Protokoll, Sicherheit, Sicherungen und Upgrades. Zehn Module, 48 Lektionen, etwa 30 Stunden Unterricht.
keywords: website-administration, kursüberblick, lesepfad, administrator, benutzer, rollen, richtlinien, sektionen, siteaccesses, caches, cronjobs, logs, audit-protokoll, sicherheit, sicherungen, upgrades, pakete, site administration, course overview, roles, policies, audit trail, backups
type: help
---
# Website-Administration: Kursüberblick

Dieser Kurs richtet sich an die Menschen, die eine Exponential-Website im Alltag betreiben: Sie legen Konten an und
entscheiden, wer was tun darf, halten Einstellungen, Caches und Cronjobs in Ordnung, lesen die Logs und das
Audit-Protokoll, wenn etwas schiefgeht, halten die Website sicher, sichern sie und führen Upgrades durch. Unterrichtet
wird in der Administration (Design admin4) und auf der Kommandozeile der eigenen Schulungsinstallation jeder
Teilnehmerin und jedes Teilnehmers.

## Für wen er gedacht ist

- Administratoren von Websites und Intranets.
- Webmaster und Teamleiter, die eine Website von einer Agentur übernehmen.
- Support-Mitarbeiter von Hosting-Unternehmen, als erste Hälfte ihres Pfads vor Hosting und Betrieb.

**Ergebnis:** Nach dem Kurs können die Teilnehmenden eine Exponential-Website selbstständig betreiben: Benutzer,
Rollen und Sektionen verwalten, eine Einstellung finden und ändern, den richtigen Cache leeren, Cronjobs und Workflows
am Laufen halten, ein Problem mit den Logs und dem Audit-Protokoll untersuchen, die Sicherheitseinstellungen anwenden,
die Website sichern und wiederherstellen und ein Upgrade planen und prüfen.

## Format und Dauer

| | |
|---|---|
| Dauer | 48 Lektionen, etwa 30 Stunden Unterricht (fünf Tage mit Pausen) |
| Format | Präsenz oder online; praktisch im Browser und am Terminal |
| Niveau | Fortgeschrittene |
| Voraussetzungen | Redakteure 101 (oder vergleichbare Erfahrung als Redakteur); Grundkenntnisse der Kommandozeile |
| Übungsumgebung | Eine Schulungsinstallation pro Person, installiert mit `php bin/php/console exp:install` (siehe [Installation über die Kommandozeile](../../../installation/07-installation-ueber-die-konsole.md)) und gestartet mit `exp:velocity start`; die Teilnehmenden brauchen das Administratorkonto ihrer eigenen Installation und ein Terminal auf deren Rechner |

## Module

| Modul | Lektionen | Dauer | Ergebnis |
|---|---|---|---|
| [1. Orientierung als Website-Administrator](modul-01-orientierung/README.md) | 5 | 2 Std. 55 Min. | Kennt die Aufgabe, den Setup-Bereich, die Systeminformationen, die Einstellungsseite und exp:ini |
| [2. Benutzer und Gruppen](modul-02-benutzer-und-gruppen/README.md) | 5 | 3 Std. 10 Min. | Legt Konten an und verwaltet sie, setzt Passwörter zurück, prüft Konten, ändert Eigentümer |
| [3. Rollen und Richtlinien](modul-03-rollen-und-richtlinien/README.md) | 5 | 3 Std. 15 Min. | Baut, weist zu und testet Rollen; steuert den anonymen Zugriff und die Registrierung |
| [4. Sektionen, Siteaccesses und Objektzustände](modul-04-sektionen-siteaccesses-und-zustaende/README.md) | 4 | 2 Std. 50 Min. | Nutzt Sektionen und Objektzustände, liest Siteaccesses, verwaltet Inhaltssprachen |
| [5. Caches](modul-05-caches/README.md) | 5 | 2 Std. 55 Min. | Benennt und leert den richtigen Cache in der Administration und auf der Kommandozeile, füllt Caches vorab |
| [6. Cronjobs, Workflows und Benachrichtigungen](modul-06-cronjobs-workflows-und-benachrichtigungen/README.md) | 5 | 3 Std. 30 Min. | Führt Cronjobs aus und plant sie, baut einen Freigabe-Workflow, betreibt Benachrichtigungen sicher |
| [7. Logs und das Audit-Protokoll](modul-07-logs-und-das-audit-protokoll/README.md) | 5 | 3 Std. 15 Min. | Liest die Logs und den Log-Viewer, folgt einer Fehlerreferenz, ermittelt mit dem Audit-Protokoll |
| [8. Sicherheit](modul-08-sicherheit/README.md) | 5 | 3 Std. 20 Min. | Härtet eine Website, legt Anmelderichtlinien und die zweistufige Anmeldung, Sitzungen und eine Content Security Policy fest |
| [9. Sicherungen und Wartung](modul-09-sicherungen-und-wartung/README.md) | 4 | 2 Std. 55 Min. | Sichert eine Website und stellt sie wieder her, nutzt den Wartungsmodus, prüft Dateien und Speicher |
| [10. Upgrades, Erweiterungen und Pakete](modul-10-upgrades-erweiterungen-und-pakete/README.md) | 5 | 3 Std. 20 Min. | Plant und prüft ein Upgrade, nutzt ezupdate und git_manager, die Seite der Erweiterungen und Pakete |

## Lesepfad

Für einen Trainer, der sich auf diesen Kurs vorbereitet, in dieser Reihenfolge:

1. [Trainer: hier beginnen](../trainer-hier-beginnen.md) und der [Kurskatalog](../kurskatalog.md).
2. [Rundgang durch die Administration](../../rundgang-durch-die-administration.md) und [Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md).
3. [Installation über die Kommandozeile](../../../installation/07-installation-ueber-die-konsole.md) und
   [Nach der Installation](../../../installation/10-nach-der-installation.md): die Übungsumgebung.
4. [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md), [Systeminformationen](../../../anleitungen/systeminformationen.md)
   und [Der Befehl exp:ini](../../../funktionen/6.0/exp-ini-befehl.md).
5. [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md) und
   [Sektionen](../../../anleitungen/sektionen.md).
6. [Caches](../../../anleitungen/caches.md) und [Cronjobs](../../../anleitungen/cronjobs.md).
7. [Der Log-Viewer](../../../funktionen/6.0/log-betrachter.md) und [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md).
8. [Sicherheitshärtung](../../sicherheitshaertung.md), [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) und
   [Content Security Policy](../../../funktionen/6.0/content-security-policy.md).
9. [Wartungsmodus](../../../anleitungen/wartungsmodus.md) und [Upgrades durchführen](../../../anleitungen/aktualisieren.md).
10. Die Module 1 bis 10 dieses Kurses, der Reihe nach. Proben Sie jede Übung auf einer Installation, die genau wie die
    der Teilnehmenden eingerichtet ist; mehrere Übungen ändern Einstellungen oder stellen die Website wieder her, und
    jede Lektion sagt, wie Sie den Ausgangszustand wiederherstellen.

## Vorgeschlagene Zeitpläne

| Zeitplan | Ablauf |
|---|---|
| Fünf Tage | Tag 1: Module 1 und 2. Tag 2: Module 3 und 4. Tag 3: Module 5 und 6. Tag 4: Module 7 und 8. Tag 5: Module 9 und 10 |
| Vier Tage, intensiv | Tag 1: Module 1, 2 und Lektionen 3.1 bis 3.2. Tag 2: Lektionen 3.3 bis 3.5, Module 4 und 5. Tag 3: Module 6, 7 und Lektionen 8.1 bis 8.2. Tag 4: Lektionen 8.3 bis 8.5, Module 9 und 10 (kürzen Sie die Übungen von 9.4 und 10.4 zu Vorführungen) |
| Support-Mitarbeiter im Hosting | Module 1, 5, 6, 7, 9 und 10 (etwa 19 Stunden, drei Tage), dann Hosting und Betrieb |
| Zehn halbe Tage | Ein Modul pro halbem Tag, der Reihe nach |
| Selbststudium | Ein Modul pro Woche, mit den Übungen auf Ihrer eigenen Installation |

## Die Teilnehmenden prüfen

Jede Lektion hat ein Quiz und ein erwartetes Ergebnis, das der Trainer prüft. Als abschließende praktische Aufgabe
bekommen alle Teilnehmenden einen kurzen Auftrag auf ihrer eigenen Installation: eine Gruppe und einen Benutzer mit
einer Adresse unter example.invalid anlegen, ihm eine auf eine Sektion eingeschränkte Rolle geben, nachweisen, was der
Benutzer darf und was nicht, einen vorbereiteten Fehler in den Logs finden und eine Sicherung erstellen und
wiederherstellen. Das ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung; die Zertifizierungen des
Partnerprogramms sind davon getrennt ([Zertifizierungen](../../partner/zertifizierungen.md)). Der Kurs Prüfungen und
praktische Übungen im Katalog enthält weiteres Material.

## Verwandte Seiten

- [Trainer: hier beginnen](../trainer-hier-beginnen.md)
- [Kurskatalog](../kurskatalog.md)
- [Hosting und Betrieb: Kursüberblick](../hosting-betrieb/README.md)
- [Suche: Kursüberblick](../suche/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
