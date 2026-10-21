---
title: Gesammelte Anträge exportieren und löschen
audience: government, admin, editor
category: public-sector
order: 290
summary: Aufgabe für das Team für Informationsfreiheit - die Einsendungen eines Antragsformulars unter Setup > Gesammelte Informationen als CSV für die Fallakte exportieren und sie dann gemäß der Aufbewahrungsregel von der Website entfernen.
keywords: gesammelte Informationen, CSV exportieren, Sammlungen entfernen, Aufbewahrung, Anträge, personenbezogene Daten, collected information, export CSV, remove collections, retention, infocollector, requests, personal data
type: help
---
# Gesammelte Anträge exportieren und löschen

Diese Aufgabe richtet sich an die Personen, die Anträge bearbeiten (Richtlinie `infocollector/read`). Hintergrund:
[Anträge aus gesammelten Informationen bearbeiten](../../transparenz-und-informationsfreiheit/antraege-aus-gesammelten-informationen-bearbeiten.md).

## Schritte

1. **Setup > Gesammelte Informationen** (`/infocollector/overview`).
2. Drücken Sie auf der Karte des Antragsformulars **CSV exportieren**: `<name>-collected.csv`, UTF-8, eine Zeile je
   Einsendung (höchstens 50 000, die neuesten zuerst).
3. Speichern Sie die Datei nur dort, wo Fallakten aufbewahrt werden dürfen.
4. Wenn die Fälle angelegt sind und Ihre Aufbewahrungsregel es erlaubt: Markieren Sie die Karte des Formulars,
   **Ausgewählte entfernen**, lesen Sie die Bestätigung (sie sagt, wie viele Sammlungen entfernt werden und dass das
   Formular bleibt), **Entfernen**.

Um nur einige Einsendungen zu entfernen: Öffnen Sie die **Sammlungen** des Formulars, markieren Sie sie,
**Ausgewählte entfernen**.

## Prüfen

Die Karte zeigt keine (oder weniger) Sammlungen; das Audit-Protokoll verzeichnet `data.export.csv` und
`data.infocollection.remove` mit Anzahlen, niemals mit Werten.

## Probleme

| Problem | Hinweis |
|---|---|
| Versehentlich entfernt | das Entfernen kann nicht rückgängig gemacht werden; nur eine Datenbanksicherung hat die Daten |
| Der Export öffnet sich mit verstümmelten Zeichen | als UTF-8 importieren |

## Verwandte Seiten

- [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
