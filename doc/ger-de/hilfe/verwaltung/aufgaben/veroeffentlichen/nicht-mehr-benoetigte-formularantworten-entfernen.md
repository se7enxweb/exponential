---
title: Nicht mehr benötigte Formularantworten entfernen
audience: government, editor
category: public-sector
order: 520
summary: Für Redakteure, die Formulare bearbeiten - Einsendungen unter Setup > Gesammelte Informationen entfernen, wenn ihre Aufbewahrungsfrist endet, für ganze Formulare oder einzelne Antworten, mit der Bestätigung und dem Eintrag im Audit, und die Kopien an anderen Orten nicht vergessen.
keywords: Antworten entfernen, Einsendungen löschen, Gesammelte Informationen, Aufbewahrung, Ausgewählte entfernen, data.infocollection.remove, personenbezogene Daten, Speicherbegrenzung, Aufräumen, remove responses, delete submissions, collected information, retention, Remove selected, personal data, storage limitation, cleanup
type: help
---
# Nicht mehr benötigte Formularantworten entfernen

Diese Seite ist für Redakteure, die Formulareinsendungen bearbeiten. Exponential bewahrt sie auf, bis jemand sie
entfernt; es gibt kein automatisches Löschen. Das planmäßige Entfernen sorgt dafür, dass personenbezogene Daten nur so
lange wie nötig aufbewahrt werden
([Gesammelte Informationen, 3](../../../../anleitungen/gesammelte-informationen.md#3-sammlungen-entfernen),
[Aufbewahrung gesammelter Informationen](../../../../../eng-us/help/government/building/forms/retention-of-collected-information.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Kennen Sie die Aufbewahrungsfrist des Formulars (von Ihrer Organisation festgelegt).
- Exportieren Sie zuerst, wenn die Daten noch für einen Bericht gebraucht werden
  ([Formularantworten als CSV exportieren](formularantworten-als-csv-exportieren.md)).
- Ihre Rolle hat `infocollector/read`.

## Schritte

**Alle Antworten eines oder mehrerer Formulare:**

1. **Setup > Gesammelte Informationen**. Sortieren Sie nach der ersten Sammlung, um die ältesten zu finden.
2. Markieren Sie die Formulare, deren Antworten wegsollen, und klicken Sie auf **Ausgewählte entfernen**.
3. Lesen Sie die Bestätigung: Sie sagt, wie viele Sammlungen wegfallen, listet die Formulare mit ihren Zahlen und sagt,
   dass die Formulare selbst bleiben. Klicken Sie auf **Entfernen**.

**Einige Antworten eines Formulars:**

1. Klicken Sie auf der Karte des Formulars auf **Sammlungen**.
2. Markieren Sie die zu entfernenden Antworten (zum Beispiel Spam oder die, die älter als die Frist sind),
   **Ausgewählte entfernen**, bestätigen Sie.

## Das Ergebnis prüfen

- [ ] Die Meldung sagt, wie viele Sammlungen von wie vielen Formularen entfernt wurden.
- [ ] Die Zahl auf der Karte ist gesunken.
- [ ] Das Entfernen ist im Audit als `data.infocollection.remove` aufgezeichnet.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Kopien vergessen | löschen Sie auch die zugehörigen E-Mails im empfangenden Postfach und alte CSV-Exporte |
| Stattdessen ein Formularobjekt entfernen | die Antworten bleiben in der Datenbank, in der Übersicht rot gezählt; stellen Sie das Formular aus dem Papierkorb wieder her, um sie ordnungsgemäß zu entfernen |
| Vor dem Ende der Frist entfernen | manche Antworten sind vielleicht Akten (Beteiligungsverfahren); prüfen Sie es |
| Das Entfernen ist endgültig | für Sammlungen gibt es keinen Papierkorb; exportieren Sie im Zweifel zuerst |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Formularantworten als CSV exportieren](formularantworten-als-csv-exportieren.md)
- [Datenschutzstandards](../../../../../eng-us/standards/privacy.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
