---
title: Anbieterabhängigkeit vermeiden
audience: government, project-manager, admin
category: public-sector
order: 110
summary: Die Arten von Abhängigkeit, in die eine öffentliche Stelle mit einer Webplattform geraten kann (Lizenz, Code, Daten, Hosting, Kompetenzen, Verträge), und was Exponential und gute Praxis jeweils dagegen bieten, mit einer kurzen Selbsteinschätzung.
keywords: Anbieterabhängigkeit, Vendor-Lock-in, Abhängigkeit, Unabhängigkeit, Ausstieg, offene Formate, Quellcode, Composer, GitHub, Wahl der Datenbank, Verträge, Kompetenzen, Souveränität, vendor lock-in, lock-in, independence, exit, open formats, source code, database choice, contracts, skills, sovereignty
type: help
---
# Anbieterabhängigkeit vermeiden

Diese Seite richtet sich an Führungskräfte und Architekten, die Dienstleister, Hoster oder sogar Plattformen wechseln
können wollen, ohne ihre Inhalte oder ihre Website zu verlieren. Abhängigkeit hat mehrere Ebenen; die Lizenz beseitigt
nur die erste. Die anderen vermeiden Sie dadurch, wie Sie bauen, hosten und Verträge schließen.

## Inhalt

1. [Ebenen der Abhängigkeit](#ebenen-der-abhängigkeit)
2. [Was Exponential pro Ebene bietet](#was-exponential-pro-ebene-bietet)
3. [Praktiken, die Sie frei halten](#praktiken-die-sie-frei-halten)
4. [Eine Selbsteinschätzung](#eine-selbsteinschätzung)

## Ebenen der Abhängigkeit

| Ebene | Das Risiko |
|---|---|
| Lizenz | Sie dürfen die Software nur mit Erlaubnis eines Anbieters nutzen |
| Code | nur ein Dienstleister hat oder versteht Ihren eigens entwickelten Code |
| Daten | Inhalte lassen sich nicht in brauchbarer Form herausholen |
| Hosting | die Plattform läuft nur bei einem Anbieter |
| Datenbank | die Plattform braucht eine bestimmte kommerzielle Datenbank |
| Kompetenzen | nur die Mitarbeitenden eines Dienstleisters können sie betreiben |
| Vertrag | der Vertrag macht den Ausstieg langsam oder teuer |

## Was Exponential pro Ebene bietet

| Ebene | Was vorhanden ist | Nachweis |
|---|---|---|
| Lizenz | GPL v2 oder später: Jeder darf sie betreiben, ändern und weitergeben | [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md) |
| Code | öffentlicher Quellcode auf GitHub, Composer-Pakete auf Packagist, vollständige Git-Historie | [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md), [Mit Git entwickeln](../../mit-git-entwickeln.md) |
| Daten | Exporte als CSV, JSON, XML; Pakete als `.ezpkg`; REST-API; Datenbank-Dumps je Datenbank | siehe „Inhalte und Pakete exportieren“ |
| Hosting | viele Betriebssysteme und Hostingmodelle, mit Stufen dokumentiert | [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) |
| Datenbank | sechs unterstützte Datenbanken, darunter drei Server aus freier Software und SQLite | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Kompetenzen | freie Dokumentation, Schulungsmaterial, ein Partnerprogramm, das jedem Unternehmen offensteht | [Kurskatalog](../../trainer/kurskatalog.md), [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) |
| Vertrag | überhaupt kein Lizenzvertrag | die Lizenz |

## Praktiken, die Sie frei halten

1. **Das Repository besitzen.** Ihre Erweiterungen, Designs und Einstellungen liegen in einem Git-Repository Ihrer
   Organisation.
2. **Keine Kernel-Patches.** Passen Sie mit Erweiterungen und Template-Overrides an, nie durch Bearbeiten von
   Kernel-Dateien, damit jeder Dienstleister Sie aktualisieren kann.
3. **Die Konten besitzen.** Domainnamen, Zertifikate, Hosting-Konten und das Administratorkonto gehören der Stelle.
4. **Die Installation dokumentieren.** Aktive Erweiterungen, Siteaccesses, Cronjobs, Overrides, Dienste von
   Drittanbietern.
5. **Freie und dokumentierte Formate bevorzugen** für Inhalte und Daten (siehe „Offene Formate und
   Datenportabilität“).
6. **Einen Ausstieg einmal testen**: eine Datensicherung anderswo wiederherstellen oder Inhalte in eine
   Testinstallation exportieren (siehe „Ausstiegsplanung“).
7. **Kompetenzen im Haus halten** (siehe „Das Wissen im Haus halten“).

## Eine Selbsteinschätzung

Antworten Sie mit Ja oder Nein; jedes „Nein“ ist eine Abhängigkeit, die zu beheben ist.

- [ ] Wir haben den vollständigen Code unserer Website in unserem eigenen Repository.
- [ ] Ein anderer Dienstleister könnte sie aus diesem Repository und der Dokumentation installieren.
- [ ] Wir haben einen aktuellen Datenbank-Dump und die Speicherdateien unter `var/`, und wir haben eine
  Wiederherstellung getestet.
- [ ] Wir könnten unsere wichtigsten Inhaltstypen heute als CSV oder JSON exportieren.
- [ ] Unser Hostingvertrag regelt den Datenexport und die Übergabe am Ende.
- [ ] Mindestens eine Person in unserer Organisation kann erklären, wie die Website aufgebaut ist.

## Verwandte Seiten

- [Ausstiegsplanung](ausstiegsplanung.md)
- [Inhalte und Pakete exportieren](inhalte-und-pakete-exportieren.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
