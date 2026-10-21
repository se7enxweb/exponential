---
title: Kosten und Rendite
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 1
summary: Die Einstiegsseite des Bereichs Kosten. Was es kostet, eine Webanwendung auf Exponential zu besitzen und zu betreiben, wofür Sie nicht bezahlen und wie Sie die Rendite mit Ihren eigenen Zahlen berechnen.
keywords: Kosten, Gesamtbetriebskosten, TCO, Rendite, ROI, Budget, Lizenzgebühr, Hostingkosten, Ausfallkosten, Aktualisierungen, Wartung, Arbeitsblatt, costs, total cost of ownership, return on investment, budget, license fee, hosting cost, downtime cost, upgrades, maintenance, worksheet
type: help
---
# Kosten und Rendite

Dieser Bereich ist für Unternehmensinhaber, Führungskräfte und alle, die ein Budget oder einen Business Case für eine
Webanwendung auf Exponential vorbereiten. Er beantwortet drei Fragen: Was kostet es, sie zu besitzen und zu betreiben,
wofür bezahlen wir nicht, und wie zeigen wir, dass das Geld zurückkommt? Jede Seite erklärt die Methode in einfachen
Worten und verlinkt die technischen Seiten für die Personen, die die Arbeit machen.

> **Hinweis**
> Das Projekt veröffentlicht keine Preise, und diese Seiten erfinden keine. Wo eine Seite eine Berechnung zeigt, ist
> jeder Eingabewert als **Beispielannahme** gekennzeichnet, verwendet runde Zahlen in einer neutralen
> „Währungseinheit“ und dient nur dazu, den Rechenweg zu zeigen. Ersetzen Sie jeden Wert durch Ihre eigenen Angebote,
> Gehälter und Verkaufszahlen, bevor Sie das Ergebnis verwenden.

## Inhalt

1. [Was dieser Bereich abdeckt](#was-dieser-bereich-abdeckt)
2. [Die Seiten](#die-seiten)
3. [Eine empfohlene Reihenfolge](#eine-empfohlene-reihenfolge)
4. [Die Fakten, auf denen der Bereich aufbaut](#die-fakten-auf-denen-der-bereich-aufbaut)
5. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was dieser Bereich abdeckt

- **Gesamtbetriebskosten (TCO)**: alle Kosten über die Lebensdauer einer Website, nicht nur die erste Rechnung.
- **Wofür Sie nicht bezahlen**: die Lizenzgebühr, Gebühren pro Benutzer und separate Produkte, die Exponential bereits
  enthält.
- **Rendite (ROI)**: was Stabilität, weniger Störungen und geringere Wartung wert sind, in einer Form, die eine
  Finanzabteilung prüfen kann.
- **Drei durchgerechnete Beispiele**: ein kleiner Shop, ein Intranet und die Website einer gemeinnützigen
  Organisation, jeweils Schritt für Schritt.
- **Arbeitsblätter**: die Kosten eines Ausfalls und ein Budget für Aktualisierungen und Wartung.
- **Kostentreiber beim Hosting**: welche technischen Entscheidungen das Hosting billiger oder teurer machen.

## Die Seiten

| Seite | Was sie beantwortet |
|---|---|
| [Gesamtbetriebskosten](gesamtbetriebskosten.md) | Welche Kostenposten eine Website über fünf Jahre hat und wie man sie addiert |
| [Wofür Sie nicht bezahlen](wofuer-sie-nicht-bezahlen.md) | Lizenzgebühren, Gebühren pro Arbeitsplatz und Zusatzprodukte, die Sie aus dem Budget streichen können, mit Quellen |
| [Rendite der Stabilität](rendite-der-stabilitaet.md) | Wie Sie weniger Ausfälle und günstigere Aktualisierungen in eine ROI-Zahl übersetzen |
| [Durchgerechnetes Beispiel: ein kleiner Shop](durchgerechnetes-beispiel-kleiner-shop.md) | TCO und ROI für einen Online-Shop, Schritt für Schritt |
| [Durchgerechnetes Beispiel: ein Intranet](durchgerechnetes-beispiel-intranet.md) | TCO und der Wert der eingesparten Zeit für eine interne Website |
| [Durchgerechnetes Beispiel: eine gemeinnützige Organisation](durchgerechnetes-beispiel-gemeinnuetzige-organisation.md) | Eine Website mit kleinem Budget, teilweise von Ehrenamtlichen betrieben |
| [Arbeitsblatt: Kosten eines Ausfalls](arbeitsblatt-kosten-eines-ausfalls.md) | Was eine Stunde offline Ihre Organisation kostet |
| [Kostentreiber beim Hosting](kostentreiber-beim-hosting.md) | Welche Entscheidungen die Hostingrechnung steigen oder sinken lassen |
| [Budget für Aktualisierungen und Wartung](budget-fuer-aktualisierungen-und-wartung.md) | Ein Jahresbudget, um eine Website aktuell und sicher zu halten |

## Eine empfohlene Reihenfolge

1. Beginnen Sie mit den [Gesamtbetriebskosten](gesamtbetriebskosten.md), um die vollständige Liste der
   Kostenposten zu sehen.
2. Lesen Sie [Wofür Sie nicht bezahlen](wofuer-sie-nicht-bezahlen.md) und streichen Sie diese Posten von Ihrer Liste.
3. Füllen Sie das [Arbeitsblatt: Kosten eines Ausfalls](arbeitsblatt-kosten-eines-ausfalls.md) mit Ihren eigenen Zahlen aus.
4. Bauen Sie mit [Rendite der Stabilität](rendite-der-stabilitaet.md) den Business Case auf.
5. Wählen Sie das durchgerechnete Beispiel, das Ihrer Organisation am nächsten kommt, und übernehmen Sie seinen Aufbau.
6. Schließen Sie mit [Kostentreiber beim Hosting](kostentreiber-beim-hosting.md) und
   [Budget für Aktualisierungen und Wartung](budget-fuer-aktualisierungen-und-wartung.md) für das laufende Budget ab.

Ein leeres Formular für die Berechnung steht im Bereich Vorlagen:
[Vorlage ROI-Arbeitsblatt](../vorlagen/vorlage-roi-arbeitsblatt.md).

## Die Fakten, auf denen der Bereich aufbaut

| Fakt | Quelle |
|---|---|
| „Using Exponential under the terms of the GNU GPL is free of charge.“ (Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos.) | `README.md`, Abschnitt „License“ |
| Die Lizenz ist die GPL Version 2 oder neuer; das Ausführen des Programms „is not restricted“ (ist nicht eingeschränkt) | `COPYRIGHT.md`, `LICENSE.md` Abschnitt 0, [Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md) |
| SQLite 3 ist die Standarddatenbank und braucht keinen Datenbankserver | `README.md`, „Requirements“; [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Es läuft auf fast jedem Betriebssystem mit PHP 8.0 oder neuer | `README.md`, „Requirements“; [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) |
| Exponential Velocity, ein mitgelieferter Anwendungsserver, braucht außer PHP nichts weiter installiert | `README.md`, „Requirements“ und „How Exponential is served“ |
| 6.0 ist die gepflegte Linie; Sicherheitskorrekturen kommen mit dem nächsten Release 6.0.x, und veröffentlichte Versionen ändern sich nie | `SECURITY.md` |

## Benachbarte Bereiche

- [Zuverlässigkeit](../zuverlaessigkeit/README.md): die Praktiken, die Ausfälle und damit ihre Kosten niedrig halten.
- [Sicherheit und Datenschutz](../sicherheit/README.md): die Grundlagen, die ein Unternehmen zum Schutz von Kundendaten
  braucht.
- [Hosting und Partner auswählen](../auswahl/README.md): wohin das Geld für Hosting und Dienstleistungen geht.
- [Vorlagen](../vorlagen/README.md): kopierfertige Arbeitsblätter und Checklisten.
- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- Behörden haben einen eigenen Bereich: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Verwandte Seiten

- [Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Exponential sachlich vergleichen](../../geschaeft/exponential-sachlich-vergleichen.md)
- [Schätzen und Abgrenzen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
