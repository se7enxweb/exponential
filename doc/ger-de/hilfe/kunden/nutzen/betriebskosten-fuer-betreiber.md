---
title: Betriebskosten für Betreiber
audience: customer, businesses, project-manager
category: customer-success
order: 40
summary: Festhalten, was eine laufende Exponential-Website wirklich kostet - die Kostenposten, die ein Betreiber nach dem Start bezahlt, wo jeder abgerechnet wird, ein jährliches Kostenregister, die Kosten, die sich unbemerkt einschleichen, und die Fragen, die Rechnungen ehrlich halten.
keywords: Betriebskosten, laufende Kosten, TCO, Hosting-Kosten, Wartungskosten, Updates, Arbeitszeit, Dienste von Drittanbietern, Kostenregister, Budget, Rechnungen, cost of ownership, running costs, hosting costs, maintenance costs, updates, staff time, third-party services, cost register, budget, invoices
type: help
---
# Betriebskosten für Betreiber

Diese Seite ist für den Betreiber oder die Leitung, die die Rechnungen einer bereits laufenden Website bezahlt. Vor dem
Start sind die Gesamtbetriebskosten eine Schätzung ([Gesamtbetriebskosten](../../../../eng-us/help/businesses/costs/total-cost-of-ownership.md)).
Nach dem Start sind sie eine Aufzeichnung: was Sie tatsächlich bezahlen, an wen und wofür. Diese Seite hilft Ihnen, diese
Aufzeichnung zu führen und Kosten zu erkennen, die wachsen, ohne dass jemand es entschieden hat.

## Die Kostenposten nach dem Start

| Posten | Abgerechnet von | Wie oft | Hinweis |
|---|---|---|---|
| Lizenz für Exponential | Niemand | Nie | „Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos.“ (`README.md`, „License“) |
| Hosting | Ihr Hoster | Monatlich oder jährlich | Server oder ein Tarif, Backups, Überwachung ([Kostentreiber beim Hosting](../../../../eng-us/help/businesses/costs/hosting-cost-drivers.md)) |
| Updates auf neue Releases 6.0.x | Partner oder eigenes Personal | Mehrmals im Jahr | Korrekturen kommen mit dem nächsten Release 6.0.x (`SECURITY.md`) |
| Updates von Erweiterungen, PHP und Server | Partner oder Hoster | Wenn sie erscheinen | ([Budget für Upgrades und Wartung](../../../../eng-us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md)) |
| Support und Änderungen | Partner | Vertrag oder pro Anfrage | Eine Gewährleistung oder garantierte Reaktionszeit fügt ein Vertrag hinzu; die GPL hat keine (`LICENSE.md`, Abschnitte 11 und 12) |
| Inhaltsarbeit | Eigenes Personal | Laufend | Auf jeder Plattform meist der größte Posten |
| Dienste von Drittanbietern | Jeder Anbieter | Monatlich oder pro Nutzung | Zahlungsgebühren, E-Mail-Zustellung, CAPTCHA, ein gehosteter KI-Anbieter, wenn Sie einen nutzen |
| Schulung neuer Mitarbeitender | Eigenes Personal oder Partner | Bei Personalwechsel | Redakteure beginnen mit [Inhalte anlegen und bearbeiten](../../inhalte-bearbeiten.md) |
| Freiwillige Unterstützung des Projekts | Das Projekt | Wenn Sie es wählen | Sponsoring und Spenden sind freiwillig (`README.md`) |

## Ein jährliches Kostenregister

Führen Sie eine Zeile pro Rechnungsposten oder wiederkehrender Kosten. Zum Kopieren:

```text
COST REGISTER - [site]     Year: ______     Kept by: ______

Line                        | Supplier | Contract or quote | Per month | Per year | Notes
----------------------------|----------|-------------------|-----------|----------|------
Hosting                     |          |                   |           |          |
Partner support             |          |                   |           |          |
Updates and upgrades        |          |                   |           |          |
Changes and new features    |          |                   |           |          |
Payment provider fees       |          |                   |           |          |
Mail delivery               |          |                   |           |          |
AI provider (if used)       |          |                   |           |          |
Other services              |          |                   |           |          |
Own staff time (estimate)   |          |                   |           |          |
License for Exponential     | none     | README "License"  | 0         | 0        |
TOTAL                       |          |                   |           |          |
```

## Kosten, die sich einschleichen

| Kosten | Wie sie wachsen | Was zu tun ist |
|---|---|---|
| Hosting | Die Festplatte füllt sich mit Medien, Logs und Backups | Bitten Sie den Hoster jedes Quartal um die Nutzung; räumen Sie ungenutzte Medien auf ([Die Inhaltsprüfungen](../../../../eng-us/features/6.0/content-checks.md)) |
| KI-Anbieter | Redakteure nutzen den Assistenten mehr | Exponential Intelligence hat ein Nutzungsprotokoll mit den geschätzten Kosten jeder Anfrage und Grenzen pro Benutzer, Tag, Monat und Website (`README.md`, „Exponential Intelligence“) |
| Zahlungsgebühren | Wachsen mit den Verkäufen und mit Preisänderungen des Anbieters | Vergleichen Sie die Abrechnungen jährlich |
| Änderungsanfragen | Viele kleine Anfragen, jede einzeln abgerechnet | Sammeln Sie sie und bitten Sie um ein Angebot pro Quartal |
| Ausgelassene Updates | Ein ein Jahr lang ausgelassenes Update wird zu einer größeren Aufgabe | Halten Sie Updates im Plan ([Budget für Upgrades und Wartung](../../../../eng-us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md)) |
| Arbeitszeit | Manuelle Routinen, die eine Funktion erledigen könnte | Siehe [Funktionen, die Kunden oft übersehen](oft-uebersehene-funktionen.md) |

## Fragen, die Rechnungen ehrlich halten

An Ihren Partner:

- Welche Stunden wurden für Updates aufgewendet, welche für Änderungen, welche für die Behebung von Problemen?
- Welche wiederkehrenden Aufgaben könnten wir mithilfe der Hilfeseiten selbst erledigen?
- Was würde die Updates des nächsten Jahres kleiner machen?

An Ihren Hoster:

- Was umfasst unser Tarif: Backups, Überwachung, Wiederherstellungstests, E-Mail-Zustellung?
- Wie viel Speicherplatz und Datenverkehr nutzen wir, und wie nah sind wir an der nächsten Preisstufe?
- Welche Kosten würden sich ändern, wenn wir die Website mit Velocity ausliefern würden, das außer PHP nichts
  Installiertes braucht (`README.md`, „Requirements“)?

## Wann Sie erneut hinsehen

- Bei jeder Rechnung: Stimmt sie mit dem Register überein?
- Jedes Quartal: Nutzung beim Hosting, gegebenenfalls Nutzung der KI, offene Änderungsanfragen.
- Jedes Jahr: das vollständige Register, in der [jährlichen Überprüfung des Nutzens](jaehrliche-ueberpruefung-des-nutzens.md).

## Verwandte Seiten

- [Gesamtbetriebskosten](../../../../eng-us/help/businesses/costs/total-cost-of-ownership.md)
- [Wofür Sie nicht bezahlen](../../../../eng-us/help/businesses/costs/what-you-do-not-pay-for.md)
- [Budget für Upgrades und Wartung](../../../../eng-us/help/businesses/costs/budgeting-for-upgrades-and-maintenance.md)
- [Den ROI messen](den-roi-messen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
