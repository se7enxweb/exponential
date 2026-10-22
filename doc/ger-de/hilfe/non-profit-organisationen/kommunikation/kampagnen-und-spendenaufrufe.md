---
title: Kampagnen und Spendenaufrufe
audience: non-profit, businesses, editor, sales-marketing
category: non-profits
order: 50
summary: Einen Spendenaufruf oder eine Aufklärungskampagne auf der Exponential-Website einer Non-Profit-Organisation durchführen - ein Ziel und eine Handlung planen, ein Kampagnenlayout in Exponential Layouts, eine Layout-Regel mit Zeitbedingung, die es ein- und ausschaltet, die Warnung vor vertippten Bedingungen, die Verbindung zu Spenden, Ehrenamtlichen und dem Newsletter, Messen und was nach der Kampagne zu tun ist.
keywords: Kampagne, Spendenaufruf, Aufklärungskampagne, Layout-Regel, Zeitbedingung, Landingpage, Handlungsaufforderung, Spenden-Button, Newsletter, Messen, Archiv, campaign, appeal, fundraising appeal, awareness campaign, Exponential Layouts, layout rule, time condition, landing page, call to action, donate button, measuring, archive
type: help
---
# Kampagnen und Spendenaufrufe

Diese Seite richtet sich an Fundraiser und Mitarbeitende in der Kommunikation. Eine Kampagne ist ein zeitlich begrenzter
Vorstoß: ein Winterspendenaufruf, eine Woche mit verdoppelten Spenden, ein Aktionstag, eine Werbeaktion für
Ehrenamtliche. Mit Exponential Layouts bauen Redakteure Kampagnenseiten ohne Entwickler, und eine Layout-Regel kann eine
Kampagne nach Datum ein- und ausschalten. Die Kurzfassung ist
[Kampagnen und Landingpages](../../unternehmen/gemeinnuetzige/kampagnen-und-landingpages.md).

## Inhalt

1. [Zuerst planen](#zuerst-planen)
2. [Die Kampagnenseite bauen](#die-kampagnenseite-bauen)
3. [Nach Datum ein- und ausschalten](#nach-datum-ein--und-ausschalten)
4. [Die Kampagne verbinden](#die-kampagne-verbinden)
5. [Messen](#messen)
6. [Nach der Kampagne](#nach-der-kampagne)
7. [Checkliste für Kampagnen](#checkliste-für-kampagnen)

## Zuerst planen

| Frage | Ihre Antwort |
|---|---|
| Das eine Ziel (Geld, Ehrenamtliche, Anmeldungen, Aufmerksamkeit) | |
| Die eine Handlung, um die Sie bitten | |
| Start- und Enddatum | |
| Wer baut, wer gibt frei, wer veröffentlicht | |
| Woran Sie erkennen, dass es funktioniert hat | |

Tragen Sie die Termine in den [Redaktionskalender](../../kunden/alltag/redaktionskalender.md) ein.

## Die Kampagnenseite bauen

1. Legen Sie im Layout-Editor ein Kampagnenlayout an, ausgehend von einem Entwurf
   ([Der Layout-Editor](../../layout-editor.md), [Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md)).
2. Verwenden Sie Blöcke: eine Überschrift mit Bild, einen kurzen Text, den Block **Button / Link** für die Handlung,
   eventuell eine Liste aktueller Nachrichten ([Layouttypen, Zonen und Blöcke](../../ersteller/website-ersteller/layouttypen-zonen-und-bloecke.md)).
3. Behalten Sie die gemeinsame Kopf- und Fußzeile bei, damit die Seite Teil Ihrer Website bleibt
   ([Gemeinsame Kopf- und Fußzeile](../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md)).
4. Sehen Sie sich die Vorschau an und lassen Sie dann die vereinbarte Person veröffentlichen. „Entwürfe gelten pro
   Layout, nicht pro Benutzer: Zwei Personen, die dasselbe Layout bearbeiten, überschreiben sich gegenseitig“
   ([Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md#entwürfe-und-veröffentlichen)).

Barrierefreiheit: [Barrierefreie Kampagnen und Dokumente](../barrierefreiheit/barrierefreie-kampagnen-und-dokumente.md#kampagnenseiten).

## Nach Datum ein- und ausschalten

Eine Layout-Regel kann eine Bedingung `time` mit `from` und/oder `to` tragen; sie „ist für Kampagnenlayouts gedacht,
die sich selbst ein- und ausschalten“ ([Layouts-Referenz, Bedingungen](../../../bc/6.0/layouts.md#bedingungen)). Um einen
Spendenaufruf vier Wochen lang auf der Startseite zu zeigen, ordnen Sie das Kampagnenlayout der Startseite mit einer
Zeitbedingung und einer höheren Priorität als das normale Layout zu; wenn der Zeitraum endet, gilt wieder das normale
Layout ([Layout-Regeln](../../ersteller/website-ersteller/layout-regeln.md)).

> **Warnung**
> „Ein unbekannter Bedingungstyp **trifft zu**, statt fehlzuschlagen. Ein Tippfehler in einer Bedingung erweitert die
> Regel daher stillschweigend, statt sie zu deaktivieren“ (dieselbe Referenz). Prüfen Sie die Regel nach dem Speichern
> auf der Website und noch einmal an dem Tag, an dem sie enden soll.

## Die Kampagne verbinden

| Handlung | Verbinden mit |
|---|---|
| Spenden | Der Spendenseite oder der Spendenplattform ([Spenden](../spenden/README.md)) |
| Ehrenamtlich mitmachen | Dem Formular für Ehrenamtliche ([Ehrenamtliche](../ehrenamtliche/README.md)) |
| Informiert bleiben | Der Anmeldeseite des Newsletters ([Unterstützer-Newsletter](unterstuetzer-newsletter-mit-cjw-newsletter.md)) |
| Zu einer Veranstaltung kommen | Der Veranstaltungsseite ([Veranstaltungen und Kalender](veranstaltungen-und-kalender-fuer-non-profit-organisationen.md)) |
| Teilen | Metadaten zum Teilen in sozialen Medien ([Metadaten und Teilen in sozialen Medien](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md)) |

Eine Haupthandlung pro Kampagne. Eine Seite mit fünf gleichrangigen Buttons fordert Besucher zum Auswählen auf statt zum
Handeln.

## Messen

| Ziel | Messgröße | Wo |
|---|---|---|
| Geld | Spenden im Zeitraum | Die Plattform oder Shop-Bestellungen |
| Ehrenamtliche | Bewerbungen im Zeitraum | Gesammelte Informationen |
| Anmeldungen | Neue bestätigte Abonnenten | `cjw_newsletter` |
| Aufmerksamkeit | Seitenaufrufe | Aufrufzähler oder ein Analysedienst |

Siehe [Wirkung mit Website-Daten messen](../foerdermittel/wirkung-mit-website-daten-messen.md). Notieren Sie den Ausgangswert,
bevor die Kampagne beginnt.

## Nach der Kampagne

- Prüfen Sie, dass die Zeitbedingung das Kampagnenlayout beendet hat.
- Danken Sie den Unterstützern: mit einer Nachricht und einer Newsletter-Ausgabe mit dem Ergebnis.
- Behalten Sie die Kampagnenseite mit ihren Ergebnissen als Archiv oder verschieben Sie sie; löschen Sie keine Seiten,
  auf die andere verlinkt haben. Verschobene Inhalte lassen sich weiterleiten
  ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)).
- Halten Sie für die nächste Kampagne fest, was funktioniert hat.

## Checkliste für Kampagnen

- [ ] Ein Ziel, eine Handlung, Termine vereinbart.
- [ ] Layout aus einem Entwurf gebaut, in der Vorschau geprüft, von der vereinbarten Person veröffentlicht.
- [ ] Regel mit Zeitbedingung zugeordnet und auf der Website geprüft.
- [ ] Buttons im abgemeldeten Zustand getestet; Spendenweg getestet.
- [ ] Barrierefreiheitstest vor dem Start bestanden.
- [ ] Ausgangswerte festgehalten; Ergebnis danach veröffentlicht.

Siehe auch die [Checkliste für Kampagnen-Landingpages](../../kunden/wachstum/checkliste-kampagnen-landingpages.md) und
[Vor einer Kampagne](../../kunden/nutzen/checklisten/vor-einer-kampagne.md).

## Verwandte Seiten

- [Kommunikation für Non-Profit-Organisationen](README.md)
- [Die Spendenseite](../spenden/die-spendenseite.md)
- [Landingpages mit Layouts (Inhaber)](../../kunden/wachstum/landingpages-mit-layouts.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
