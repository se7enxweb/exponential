---
title: Ein Machbarkeitsnachweis für Anwender
audience: adopter, project-manager, developer, businesses
category: adoption
order: 13
summary: Wie Anwender einen kurzen Machbarkeitsnachweis mit Exponential durchführen - die schwierigste Anforderung wählen, Umfang und Zeitrahmen, eine Stichprobe echter Inhalte, was gemessen wird, wer teilnehmen sollte und wie das Ergebnis für die Entscheidung festgehalten wird.
keywords: Machbarkeitsnachweis, Proof of Concept, POC, Pilot, schwierigste Anforderung, Zeitrahmen, Beispielinhalte, messen, Bewertung, Entscheidung, proof of concept, pilot, hardest requirement, time box, sample content, measure, evaluation, decision
type: help
---
# Ein Machbarkeitsnachweis für Anwender

Ein Machbarkeitsnachweis beantwortet eine Frage: Kann Exponential das, worüber wir uns die meisten Sorgen machen? Er
ist keine erste Version der Website.

## Inhalt

1. [Die Frage wählen](#die-frage-wählen)
2. [Umfang und Zeitrahmen](#umfang-und-zeitrahmen)
3. [Ihn durchführen](#ihn-durchführen)
4. [Das Ergebnis festhalten](#das-ergebnis-festhalten)

## Die Frage wählen

Wählen Sie die Anforderung, die die Bewertung beenden würde, wenn sie scheitert. Beispiele:

| Sorge | Machbarkeitsnachweis |
|---|---|
| Unsere Inhalte passen in kein Standard-CMS | Drei echte Inhaltstypen modellieren und zehn echte Objekte eingeben ([Erste Schritte der Inhaltsmodellierung](../planung/erste-schritte-der-inhaltsmodellierung.md)) |
| Unsere Redakteure müssen Seiten bauen | Zwei Landingpages mit Exponential Layouts bauen |
| Wir verkaufen in mehreren Sprachen | Ein Produkt in zwei Sprachen mit Preisen, bis durch die Kasse ([Onlineshops](../../unternehmen/shops/README.md)) |
| Unsere Apps müssen die Inhalte lesen | Inhalte über die REST-API lesen ([ezprestapi](../../../funktionen/6.0/erweiterungen/ezprestapi.md)) |
| Unsere alten Inhalte müssen mitkommen | Eine Stichprobe mit erhaltenen URLs migrieren ([Umziehen](../umziehen/README.md)) |
| Unsere Rechte sind komplex | Zwei Rollen nachbauen und testen ([Rollen und Rechte für die Einführung](../starten/rollen-und-rechte-fuer-die-einfuehrung.md)) |

## Umfang und Zeitrahmen

- Eine bis drei Fragen, nicht zehn.
- Ein im Voraus vereinbarter fester Zeitrahmen (eine Beispielannahme: ein bis zwei Wochen Arbeitszeit eines
  Entwicklers); hören Sie auf, wenn er endet.
- Eine Testinstallation auf SQLite reicht meist aus ([Demo und Testinstallation ausprobieren](demo-und-testinstallation-ausprobieren.md)).
- Nur erfundene oder anonymisierte Inhalte.

## Ihn durchführen

1. Schreiben Sie die Erfolgskriterien auf, bevor Sie beginnen.
2. Beziehen Sie einen künftigen Redakteur ein; seine Reaktion ist Teil des Ergebnisses.
3. Notieren Sie jeden Schritt, der die Dokumentation, die Foren oder einen Umweg brauchte.
4. Messen Sie, was für Sie zählt: Zeit zum Bauen, Schritte für einen Redakteur, Geschwindigkeit der Seite.

## Das Ergebnis festhalten

```text
Frage:                 <die getestete Anforderung>
Erfolgskriterien:      <wie vorher vereinbart>
Ergebnis:              <erfüllt / teilweise erfüllt / nicht erfüllt>
Belege:                <Bildschirmfotos, Schritte, Messungen>
Nötige Umwege:         <...>
Offene Fragen:         <...>
Empfehlung:            <weitermachen / aufhören / weiter testen>
```

Die Zusammenfassung fließt in den Bewertungsbogen und das Entscheidungsdokument ein
([Der Bewertungsbogen](bewertungsbogen.md), [Die Entscheidung festhalten](../entscheiden/die-entscheidung-festhalten.md)).

## Verwandte Seiten

- [Exponential bewerten](README.md)
- [Risiken und wie Sie sie gewichten](risiken-und-wie-sie-sie-gewichten.md)
- [Schätzen und Umfang festlegen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)
- [Projektvorlagen](../../fuehrung/projektvorlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
