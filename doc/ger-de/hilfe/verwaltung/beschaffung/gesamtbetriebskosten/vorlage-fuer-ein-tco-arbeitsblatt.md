---
title: Vorlage für ein TCO-Arbeitsblatt
audience: government, project-manager
category: public-sector
order: 120
summary: Ein Arbeitsblatt zum Kopieren und Ausfüllen für die Gesamtbetriebskosten einer Website oder eines Intranets auf Exponential, mit Zeilen für einmalige, wiederkehrende und interne Kosten, Risiko und Ausstieg, einem Verzeichnis der Annahmen und einem Vergleichsblock für mehrere Angebote. Alle Werte sind Lücken.
keywords: TCO-Arbeitsblatt, Kostenvorlage, Vorlage für eine Tabellenkalkulation, Budgetvorlage, Verzeichnis der Annahmen, Angebotsvergleich, Gesamtbetriebskosten, TCO worksheet, cost template, spreadsheet template, budget template, assumptions register, bid comparison, total cost of ownership
type: help
---
# Vorlage für ein TCO-Arbeitsblatt

Diese Seite richtet sich an die Person, die die Gesamtbetriebskosten eines Content-Management-Systems ausfüllt. Kopieren
Sie die Blöcke unten in eine Tabellenkalkulation oder ein Dokument. Jeder Wert ist eine Lücke; der Aufbau folgt
[Der Aufbau eines TCO-Modells](aufbau-des-tco-modells.md) und den Zeilen der [Kostenkategorien](kostenkategorien.md).

> **Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung.

## Inhalt
1. [Kopf](#kopf)
2. [Verzeichnis der Annahmen](#verzeichnis-der-annahmen)
3. [Einmalige Kosten](#einmalige-kosten)
4. [Wiederkehrende Kosten](#wiederkehrende-kosten)
5. [Risiko und Ausstieg](#risiko-und-ausstieg)
6. [Summen und Vergleich](#summen-und-vergleich)
7. [Gut ausfüllen](#gut-ausfüllen)

## Kopf

```text
Project:            <name>
Option / bid:       <bidder or internal option>       Product: Exponential <version>
Horizon:            <n> years, from <date>
Currency:           <...>          Discount rate (if your rules require): <...>
Prepared by:        <name, role>   Date: <date>     Reviewed by: <name>
```

## Verzeichnis der Annahmen

Jede unten verwendete Zahl braucht hier eine Zeile mit ihrer Quelle.

```text
ID   Assumption                          Value     Source (quote, record, PoC measurement)   Confidence (H/M/L)
A1   Horizon                             <...>     <...>                                       <...>
A2   Internal hourly rate per role       <...>     <finance department>                        <...>
A3   Supplier day rate                   <...>     <bid>                                       <...>
A4   Kernel license fee                  0         GPL-2.0-or-later, no fee                    H
A5   ...
```

## Einmalige Kosten

```text
Line                                         Qty     Unit       Rate     Amount    Assumption IDs
Tender, evaluation, proof of concept          <...>   hours      <...>    <...>     <...>
Implementation: content model                 <...>   days       <...>    <...>     <...>
Implementation: design and templates          <...>   days       <...>    <...>     <...>
Integrations                                  <...>   days       <...>    <...>     <...>
Content migration                             <...>   days       <...>    <...>     <...>
Hardening and penetration test                <...>   days       <...>    <...>     <...>
Accessibility audit and statement             <...>   days       <...>    <...>     <...>
Hosting setup                                 <...>   lump sum   -        <...>     <...>
Initial training: trainers                    <...>   days       <...>    <...>     <...>
Initial training: participants' time          <...>   hours      <...>    <...>     <...>
Internal project staff                        <...>   hours      <...>    <...>     <...>
One-off subtotal                                                          <...>
```

## Wiederkehrende Kosten

```text
Line                                   Per year   Years   Amount   Assumption IDs
License (kernel)                       0          <n>     0        A4
Paid extensions or services            <...>      <n>     <...>    <...>
Hosting: production                    <...>      <n>     <...>    <...>
Hosting: staging, training             <...>      <n>     <...>    <...>
Backups and storage                    <...>      <n>     <...>    <...>
Support contract                       <...>      <n>     <...>    <...>
Releases, updates, regression tests    <...>      <n>     <...>    <...>
Periodic accessibility / security test <...>      <n>     <...>    <...>
Editors' extra time                    <...>      <n>     <...>    <...>
Site administrator                     <...>      <n>     <...>    <...>
System administration (if in-house)    <...>      <n>     <...>    <...>
Onboarding training                    <...>      <n>     <...>    <...>
Contract management                    <...>      <n>     <...>    <...>
Recurring subtotal                                        <...>
```

## Risiko und Ausstieg

```text
Risk                                   Probability   Impact   Allowance   Notes
Supplier change mid-horizon            <...>         <...>    <...>       see exit plans
Upgrade larger than planned            <...>         <...>    <...>       custom code volume
Security incident                      <...>         <...>    <...>       <...>
Exit at end of horizon: export, redirects, parallel running   <...>
Risk and exit subtotal                                        <...>
```

## Summen und Vergleich

```text
                      Option A    Option B    Option C
One-off               <...>       <...>       <...>
Recurring             <...>       <...>       <...>
Risk and exit         <...>       <...>       <...>
TCO (nominal)         <...>       <...>       <...>
TCO (discounted)      <...>       <...>       <...>
Largest 3 lines       <...>       <...>       <...>
Lowest-confidence assumptions: <IDs>
```

## Gut ausfüllen

- Verwenden Sie für jede Möglichkeit denselben Zeithorizont, dieselben Sätze und Annahmen, außer wo sich die Angebote
  wirklich unterscheiden.
- Füllen Sie die interne Zeit aus Messungen im Proof of Concept aus, nicht aus Vermutungen
  ([Plan für einen Proof of Concept](../bewertung/plan-fuer-einen-proof-of-concept.md)).
- Prüfen Sie die Form Ihres Ergebnisses gegen das [durchgerechnete Beispiel](durchgerechnetes-beispiel.md); dominiert eine Zeile,
  stellen Sie sicher, dass ihre Annahmen am besten belegt sind.
- Bewahren Sie das Arbeitsblatt in der Vergabeakte auf; ein Debriefing kann fragen, wie der Preis verglichen wurde.

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Ein durchgerechnetes TCO-Beispiel](durchgerechnetes-beispiel.md)
- [Beschaffung: Übersicht](../README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
