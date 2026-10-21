---
title: Schriftgutverwaltung und ein Web-CMS
audience: government, project-manager, editor, admin
category: public-sector
order: 20
summary: Für Zuständige für Schriftgut öffentlicher Stellen - wie sich eine mit Exponential betriebene Website zur Praxis der Schriftgutverwaltung verhält (ISO 15489-1 allgemein eingeordnet), welche Website-Inhalte und Systemdaten Schriftgut sein können, was Exponential von sich aus aufbewahrt und was in Ihr Schriftgutsystem gehört.
keywords: Schriftgutverwaltung, ISO 15489, Schriftgut, Records Management, Webschriftgut, Aktenführung, Archive, Aufbewahrung, Authentizität, Integrität, Zuverlässigkeit, Benutzbarkeit, Versionen, Audit-Trail, Schriftgutsystem, öffentlicher Sektor, records management, records, web records, record keeping, archives, retention, authenticity, integrity, reliability, usability, versions, audit trail, records system, public sector
type: help
---
# Schriftgutverwaltung und ein Web-CMS

Diese Seite richtet sich an Zuständige für Schriftgut, Archivarinnen und Archivare sowie Projektleitungen öffentlicher
Stellen. Eine Website veröffentlicht amtliche Informationen; einiges davon und einige der eigenen Daten des Systems
können Schriftgut sein, das Ihre Organisation aufbewahren muss. Exponential ist ein Veröffentlichungssystem, kein
System zur Schriftgutverwaltung; es hat Funktionen, die helfen, und übergibt Schriftgut bei Bedarf an Ihr
Schriftgutsystem.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Einordnung](#die-einordnung)
2. [Was Schriftgut sein kann](#was-schriftgut-sein-kann)
3. [Was Exponential von sich aus aufbewahrt](#was-exponential-von-sich-aus-aufbewahrt)
4. [Was es nicht tut](#was-es-nicht-tut)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Einordnung

ISO 15489-1:2016, „Records management - Concepts and principles“ (Information und Dokumentation -
Schriftgutverwaltung - Konzepte und Grundsätze; <https://www.iso.org/standard/62542.html>), ist die internationale
Referenz für die Schriftgutverwaltung. Allgemein gesprochen beschreibt sie Schriftgut als Nachweis geschäftlicher
Tätigkeit, mit Merkmalen wie Authentizität, Zuverlässigkeit, Integrität und Benutzbarkeit, das über seinen ganzen
Lebenszyklus unter Kontrolle gehalten wird. Diese Seite zitiert die Norm nicht; sie setzt ihre allgemeinen Gedanken in
Bezug zu einer Website.

## Was Schriftgut sein kann

| Eintrag | Warum es Schriftgut sein kann | Wo es in Exponential liegt |
|---|---|---|
| Veröffentlichte Bekanntmachungen, Beschlüsse, Ankündigungen | die amtliche Veröffentlichung einer Entscheidung | Inhaltsobjekte und ihre Versionen |
| Der Zustand einer Seite an einem Datum | was der Öffentlichkeit gesagt wurde, und wann | Versionen (innerhalb der Grenze der Versionsgeschichte) |
| Formulareinsendungen (Anträge, Beschwerden) | der Beginn eines Vorgangs | Informationssammlungen |
| Einwilligungsnachweise für E-Mails | Nachweis der Einwilligung | Einwilligungsprotokoll |
| Wer was im System geändert hat | Rechenschaft | Audit-Trail |

Ihr Aufbewahrungsplan entscheidet; diese Liste hilft Ihnen, an den richtigen Stellen zu suchen.

## Was Exponential von sich aus aufbewahrt

| Merkmal (allgemein) | Funktion |
|---|---|
| Integrität der Systemaufzeichnungen | Audit-Trail mit Hash-Kette, signierte Prüfpunkte und Archivmanifeste |
| Geschichte der Inhalte | Versionen mit Ersteller und Daten; Vergleichen; neue Entwürfe aus alten Versionen |
| Kontext | Sektionen, Objektzustände, Orte im Baum; Metadatenfelder Ihrer Klassen |
| Stabile Verweise | URL-Aliasse; alte Aliasse leiten nach Umbenennen oder Verschieben weiter |
| Kontrollierte Änderung | Rollen und Richtlinien; Freigabe-Workflows; der Papierkorb |

## Was es nicht tut

- Es hat **keine Funktionen der Schriftgutverwaltung**: keine Erklärung zu Schriftgut, keine Aussonderungsbefugnis,
  keinen eigenen Aktenplan, kein zertifiziertes Archiv.
- **Versionen werden nicht für immer aufbewahrt**: Die älteste archivierte Version wird entfernt, wenn ein Objekt die
  Grenze der Versionsgeschichte erreicht (standardmäßig 45). Siehe „Die Grenze der Versionsgeschichte“.
- **Audit-Einträge werden** am Ende ihrer Aufbewahrung **entfernt** (standardmäßig 730 Tage archiviert), sofern Sie sie
  nicht verlängern.

Planen Sie die Übergabe: Exportieren Sie, was Schriftgut ist, in Ihr Schriftgutsystem oder Archiv (siehe „Inhalte
exportieren“).

## Verwandte Seiten

- [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)
- [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)
- [Objektzustände](../../../anleitungen/objektzustaende.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
