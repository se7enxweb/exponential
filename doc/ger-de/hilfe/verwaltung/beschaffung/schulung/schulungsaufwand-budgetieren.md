---
title: Schulungsaufwand budgetieren
audience: government, trainer, project-manager
category: public-sector
order: 50
summary: Wie Sie den Schulungsaufwand für ein Exponential-Projekt in einer öffentlichen Stelle aus den veröffentlichten Kurslängen und Ihren eigenen Personenzahlen und Tagessätzen schätzen - ein Arbeitsblatt mit Formeln und ohne erfundene Preise.
keywords: Schulungsbudget, Schulungskosten, Aufwand, Personentage, Kurslänge, Tagessatz, Plätze, Schätzung, Arbeitsblatt, Gesamtbetriebskosten, öffentlicher Sektor, training budget, training cost, effort, person days, course length, day rate, seats, estimate, worksheet, total cost of ownership, public sector
type: help
---
# Schulungsaufwand budgetieren

Diese Seite richtet sich an die Projektleitung, die eine Schulungszeile in ein Budget oder ein Modell der
Gesamtbetriebskosten aufnehmen muss. Sie gibt eine Methode und ein Arbeitsblatt. Die einzigen Zahlen, die sie verwendet,
sind die im [Kurskatalog](../../../trainer/kurskatalog.md) veröffentlichten Kurslängen; jeden Preis, jeden Satz und
jede Personenzahl füllen Sie aus.

## Inhalt

1. [Woraus Schulungskosten bestehen](#woraus-schulungskosten-bestehen)
2. [Stunden aus dem Katalog](#stunden-aus-dem-katalog)
3. [Das Arbeitsblatt](#das-arbeitsblatt)
4. [Wiederkehrende Schulung](#wiederkehrende-schulung)
5. [Oft vergessene Kosten](#oft-vergessene-kosten)

## Woraus Schulungskosten bestehen

| Posten | Bezahlt an | Hängt ab von |
|---|---|---|
| Zeit der Trainerinnen und Trainer (Vorbereitung und Durchführung) | einen Partner oder die Zeit Ihrer eigenen Trainerin oder Ihres eigenen Trainers | Kurslänge, Zahl der Termine |
| Zeit der Teilnehmenden | Ihre Organisation (Personal, das seine normale Arbeit nicht erledigt) | Kurslänge mal Teilnehmende |
| Schulungsinstallation (Lab) | Ihre IT oder den Lieferanten | eine Installation; SQLite braucht keinen Datenbankserver |
| Anpassung des Materials | die Trainerin oder den Trainer | wie stark Sie das freie Material anpassen |
| Räume, Ausstattung, Reisen | Ihre Organisation | in Präsenz oder online |
| Prüfung und Bescheinigungen | die Trainerin oder den Trainer | ob Sie prüfen |

Das Kursmaterial selbst kostet nichts: Es ist freie Dokumentation unter der GNU FDL 1.3
([Freie Dokumentation](../../../freie-dokumentation.md)). Exponential hat keine Lizenzgebühr je Benutzer oder Arbeitsplatz
([Fragen zur Lizenzierung](../../../projekt/lizenz-faq.md)).

## Stunden aus dem Katalog

| Kurs | Lektionen | Stunden (ohne Pausen) |
|---|---|---|
| Editors 101 | 27 | etwa 19,5 |
| Editors 201 | 23 | etwa 16 |
| Exponential Layouts | 23 | etwa 17,5 |
| Administration der Website | 48 | etwa 30 |
| Hosting und Betrieb | 28 | etwa 20 |
| Newsletter | 20 | etwa 16 |
| Suche | 20 | etwa 10 |
| Train the Trainer | 34 | etwa 21 |

Quelle: [Kurskatalog](../../../trainer/kurskatalog.md). Viele Kurse bieten kürzere Zeitpläne aus ausgewählten
Modulen; die Tagesprogramme der [Workshops](../../../trainer/workshops/README.md) sind eine weitere Möglichkeit.
Rechnen Sie Pausen zu den Stunden hinzu, wenn Sie Tage planen.

## Das Arbeitsblatt

```text
Training effort - <project>                                   all values are your own inputs

Course            Hours   Sessions  Participants  Trainer prep factor
Editors 101       19.5    <s1>      <p1>          <f1>
Site admin        30      <s2>      <p2>          <f2>
...

Trainer hours     = sum over courses of Hours x Sessions x (1 + prep factor)
Participant hours = sum over courses of Hours x Participants
Trainer cost      = Trainer hours x <your trainer rate, or partner's quoted rate>
Staff time cost   = Participant hours x <your internal hourly cost>
Lab               = <set-up hours> x <IT rate>  (+ hosting, if any)
Other             = rooms + travel + material adaptation
Total             = Trainer cost + Staff time cost + Lab + Other
```

Der Vorbereitungsfaktor ist Ihre Schätzung, wie lange eine Trainerin oder ein Trainer je Unterrichtsstunde vorbereitet;
wer denselben Kurs oft unterrichtet, braucht weniger. Bitten Sie einen Partner um ein Angebot je Termin und vergleichen
Sie Angebote bei denselben Stunden.

## Wiederkehrende Schulung

Das erste Jahr trägt die Schulung zum Start. Spätere Jahre tragen:

- die Einarbeitung neuer Beschäftigter (schätzen Sie sie aus Ihrer Personalfluktuation; siehe „Einarbeitung neuer
  Beschäftigter“ in diesem Ordner);
- Auffrischungen, wenn die Website Funktionen hinzubekommt oder ein Upgrade die Administration verändert;
- die Schulung des Personals eines neuen Lieferanten, wenn der Vertrag den Besitzer wechselt.

Nehmen Sie dafür eine jährliche Zeile in das Budget auf, nicht nur die Schulung zum Start.

## Oft vergessene Kosten

- **Die Zeit der Teilnehmenden** ist oft der größte Posten und wird nicht in Rechnung gestellt; zählen Sie sie trotzdem.
- **Proben** auf der Version, die Sie verwenden werden, damit Übungen funktionieren
  ([Übungen schreiben, die funktionieren](../../../trainer/train-the-trainer/modul-03-einen-kurs-gestalten/uebungen-schreiben-die-funktionieren.md)).
- **Vorkehrungen zur Barrierefreiheit** für Teilnehmende
  ([Vorkehrungen und inklusive Praxis](../../../trainer/train-the-trainer/modul-05-barrierefreiheit-und-inklusion/nachteilsausgleich-und-inklusive-praxis.md)).
- **Übersetzung** des Materials, wenn das Personal in einer anderen Sprache geschult wird, als die Hilfe anbietet.

## Verwandte Seiten

- [Schulung in der Beschaffung](README.md)
- [Ein Schulungsplan für eine öffentliche Stelle](schulungsplan-fuer-eine-oeffentliche-stelle.md)
- Der Teil zu den Gesamtbetriebskosten dieses Abschnitts zur Beschaffung ([Beschaffung](../README.md))
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
