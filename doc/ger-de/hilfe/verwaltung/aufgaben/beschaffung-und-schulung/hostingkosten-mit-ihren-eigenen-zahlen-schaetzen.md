---
title: Hostingkosten mit Ihren eigenen Zahlen schätzen
audience: government, project-manager, hosting
category: public-sector
order: 130
summary: Schritt für Schritt schätzen, was das Hosting einer Exponential-Site eine Behörde über eine Vertragslaufzeit kosten wird - Dimensionierung aus Messungen, die Kostenpositionen, eigener Betrieb oder Auslagerung und ein Arbeitsblatt - nur mit Ihren eigenen Zahlen und Angeboten.
keywords: Hostingkosten, Schätzung, Dimensionierung, Budget, eigenes Hosting, Managed Hosting, Arbeitsblatt, Gesamtkosten, Aufgabe zur Beschaffung, hosting costs, estimate, sizing, budget, in-house hosting, managed hosting, worksheet, total cost of ownership, procurement task
type: help
---
# Hostingkosten mit Ihren eigenen Zahlen schätzen

**Wer:** der Projektleiter mit einer IT-Fachkraft. **Wann:** beim Aufstellen des Budgets oder beim Vergleich von
Angeboten. Das Projekt veröffentlicht keine Preise für das Hosting; diese Seite gibt die Struktur, Sie liefern die
Zahlen. Fakten zur Dimensionierung:
[Speicher, CPU und Festplatte dimensionieren](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Arbeitsblatt](#arbeitsblatt)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Eine Vorstellung von Ihrem Verkehr, Ihrer Inhaltsmenge und Ihrem Speicher (aus den Statistiken Ihrer aktuellen Site).
- Mindestens ein Angebot oder die internen Sätze Ihres Rechenzentrums.

## Schritte

1. **Dimensionieren Sie anhand von Messungen**, nicht von Vermutungen: Die Seite zur Dimensionierung listet die Zahlen,
   die das Repository nennt, und wie man eine echte Site misst; eine Installation als Proof of Concept mit Ihren
   Inhalten hilft
   ([Einen Proof of Concept auf SQLite mit einem Befehl durchführen](einen-proof-of-concept-auf-sqlite-mit-einem-befehl-durchfuehren.md)).
   `exp:benchmark` misst die Geschwindigkeit unter Last ([Benchmarking](../../../../anleitungen/benchmarking.md)).
2. **Wählen Sie die Umgebungen**: Produktion, Staging und vielleicht Schulung; jede kostet.
3. **Wählen Sie das Modell**: Shared, VPS, dediziert, Container, Managed
   ([Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md)).
4. **Listen Sie die Kostenpositionen** im Arbeitsblatt unten auf, für jedes Jahr der Laufzeit.
5. **Rechnen Sie Personal hinzu**: Eigenes Hosting braucht Arbeitszeit für Betrieb, Updates und Bereitschaft;
   Managed Hosting enthält einen Teil davon
   ([Hostingkosten](../../beschaffung/gesamtbetriebskosten/hostingkosten.md)).
6. **Rechnen Sie Wachstum hinzu**: Der Speicher wächst mit den Uploads; Logs und Sicherungen wachsen ebenfalls.
7. **Vergleichen Sie** eigenen Betrieb und Auslagerung auf denselben Positionen
   ([Eigener Betrieb oder Cloud](../../beschaffung/gesamtbetriebskosten/eigenbetrieb-oder-cloud.md)).

## Arbeitsblatt

```text
Hosting estimate - <site>, term <years>              all amounts: your own figures or quotes

                                   Year 1    Year 2    Year 3 ...
Production server(s) / plan        <...>
Staging environment                <...>
Database server (if separate)      <...>
Storage and growth                 <...>
Backups: storage off-site          <...>
TLS certificates (often free)      <...>
Monitoring / uptime checks         <...>
Staff time: operations x rate      <...>
On-call cover                      <...>
Search service (Solr), if used     <...>
Third-party services (mail, CDN)   <...>
Total per year                     <...>
```

## Ergebnis

Eine jährliche Position für das Hosting im Modell der Gesamtkosten, mit aufgeschriebenen Annahmen.

## Fallstricke

- Nur die Produktion bepreisen, ohne Staging und Sicherungen.
- Beim eigenen Hosting die Arbeitszeit vergessen.
- Die Dimensionierung einer anderen Organisation verwenden, ohne die eigene Site zu messen.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Hosting-Angebote vergleichen](hosting-angebote-vergleichen.md)
- [Vorlage für das Arbeitsblatt der Gesamtkosten](../../beschaffung/gesamtbetriebskosten/vorlage-fuer-ein-tco-arbeitsblatt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
