---
title: "Modul 1: Erste Schritte"
audience: trainer, editor, admin
category: training
order: 10
summary: Die Landkarte von CJW Newsletter, eine Schulungsinstallation, die keine Mail senden kann, und das Newsletter-Dashboard. Zwei Stunden.
keywords: newsletter mit cjw newsletter, erste schritte, überblick, schulungsinstallation, datei-transport, dashboard, newsletters with cjw newsletter, getting started, overview, training installation, file transport
type: help
---
# Modul 1: Erste Schritte

Dieses Modul gibt den Teilnehmenden die Landkarte des Newsletter-Systems, macht die Schulungsinstallation sicher
(jede Mail wird in eine Datei geschrieben) und bringt ihnen bei, das Dashboard zu lesen, bevor sie handeln. Es wird
noch keine Ausgabe geschrieben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Dauer | 2 Stunden |
| Lektionen | 3 |
| Niveau | Einsteiger bis Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [1.1 Was ein Newsletter-System leistet](was-ein-newsletter-system-leistet.md) | 30 Minuten | Den Newsletter-Bereich erkunden und den Baum aus System, Liste, Ausgabe und Artikel zeichnen |
| [1.2 Eine sichere Schulungsinstallation vorbereiten](eine-sichere-schulungsinstallation-vorbereiten.md) | 60 Minuten | Jeden Mail-Transport auf Dateien stellen, das Ergebnis prüfen, Testadressen unter `example.invalid` wählen |
| [1.3 Das Newsletter-Dashboard](das-newsletter-dashboard.md) | 30 Minuten | Das Dashboard Abschnitt für Abschnitt lesen und mit dem Statusbefehl vergleichen |

## Dieses Modul vorbereiten

- [ ] Eine Schulungsinstallation mit aktiver Erweiterung `cjw_newsletter`, angelegten Tabellen und einem
      Newsletter-Baum mit mindestens einem System und einer Liste (siehe die Datei `INSTALL` der Erweiterung und die
      [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)).
- [ ] `site.ini [MailSettings] Transport=file` und alle drei Schlüssel `TransportMethod*` auf `file` gesetzt, geprüft
      in der Einstellungsansicht (Lektion 1.2 vorab vom Trainer erledigt).
- [ ] Ein Schulungskonto für die Administration pro Person; Shell-Zugang für die Administratoren im Raum.
- [ ] Nirgends in der Datenbank ein echter Abonnent: nur Adressen unter `example.invalid`.
- [ ] Das Dashboard zeigt kein Problem außer dem Hinweis, dass Mails in Dateien geschrieben werden.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 bis 0:30 | Lektion 1.1 |
| 0:30 bis 1:30 | Lektion 1.2 |
| 1:30 bis 2:00 | Lektion 1.3 |

## Verwandte Seiten

- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
