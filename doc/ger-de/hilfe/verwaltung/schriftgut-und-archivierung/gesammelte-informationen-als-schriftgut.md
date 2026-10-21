---
title: Formulareinsendungen als Schriftgut
audience: government, admin, project-manager
category: public-sector
order: 240
summary: Für öffentliche Stellen, deren Webformulare Vorgänge auslösen (Anträge, Beschwerden, Bewerbungen) - wie Exponential Einsendungen speichert, sie in ein Vorgangs- oder Schriftgutsystem exportiert, sie nach Plan entfernt und welche Audit-Einträge zu Exporten und Entfernungen entstehen.
keywords: Formulareinsendungen, Informationssammlung, Gesammelte Informationen, Schriftgut, Vorgänge, Anträge, Beschwerden, CSV exportieren, infocollector, Entfernen, Aufbewahrung, Audit, data.export.csv, data.infocollection.remove, Vorgangsverwaltung, öffentlicher Sektor, form submissions, information collection, collected information, records, cases, requests, complaints, CSV export, removal, retention, case management, public sector
type: help
---
# Formulareinsendungen als Schriftgut

Diese Seite richtet sich an Zuständige für Schriftgut, Administratoren und die Teams, die Webformulare erhalten. Eine
Beschwerde oder ein Antrag, der über die Website gesendet wird, kann der Beginn eines Vorgangs und ein Schriftstück
sein. Exponential speichert jede Einsendung als Sammlung
([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)); es ist kein System zur Vorgangsverwaltung,
daher sollte das Schriftgut in das System wandern, das den Vorgang verwaltet.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wo die Einsendungen liegen](#wo-die-einsendungen-liegen)
2. [Sie in Ihr Vorgangssystem überführen](#sie-in-ihr-vorgangssystem-überführen)
3. [Sie entfernen](#sie-entfernen)
4. [Verwandte Seiten](#verwandte-seiten)

## Wo die Einsendungen liegen

- **Setup > Gesammelte Informationen** (`/infocollector/overview`, Richtlinie `infocollector/read`): die Formulare mit
  Kennzahlen; pro Formular die Liste der Sammlungen, jede mit allen Werten.
- Eine E-Mail an den Empfänger, wenn das Formular eine sendet: eine weitere Kopie, in einem Postfach.
- Nichts entfernt Einsendungen automatisch.

## Sie in Ihr Vorgangssystem überführen

| Weg | Wie |
|---|---|
| Per E-Mail | die E-Mail des Formulars an ein Funktionspostfach, das Ihr Vorgangssystem liest |
| Per Export | **CSV exportieren** pro Formular (`infocollector/export/<object id>`): ID der Sammlung, Zeit des Sendens und der Änderung (UTC), Benutzer-ID der sendenden Person, eine Spalte pro Feld; im Audit als `data.export.csv` mit der Anzahl der Zeilen aufgezeichnet |
| Per Entwicklung | ein Workflow oder eine Erweiterung, die jede Einsendung an das Vorgangssystem übergibt; die Arbeit Ihrer Entwickler |

Halten Sie die ID der Sammlung im Vorgang fest, damit die Verbindung zum Original nachvollziehbar bleibt, solange es
existiert.

## Sie entfernen

Wenn die Einsendung sicher im Vorgangssystem liegt (oder ihre Aufbewahrung beendet ist):

1. Setup > Gesammelte Informationen, öffnen Sie das Formular und markieren Sie die Sammlungen (oder markieren Sie in der
   Übersicht ganze Formulare).
2. **Ausgewählte entfernen**; die Bestätigung sagt, wie viele entfernt werden; die Formulare selbst bleiben.
3. Löschen Sie exportierte CSV-Dateien, die nicht mehr gebraucht werden.

Entfernungen werden als `data.infocollection.remove` aufgezeichnet. Sammlungen von Objekten, die entfernt wurden oder im
Papierkorb liegen, werden nicht aufgeführt (die Übersicht zählt sie rot); stellen Sie das Objekt aus dem Papierkorb
wieder her, um sie zu erreichen.

## Verwandte Seiten

- [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)
- [Formulareinsendungen](../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Formulare zur Informationssammlung und Datenschutz](../datenschutz/formulare-des-information-collectors.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
