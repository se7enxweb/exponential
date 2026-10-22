---
title: Förderergebnisse für Geldgeber
audience: non-profit, businesses, project-manager, editor
category: non-profits
order: 50
summary: Wie eine Non-Profit-Organisation aus dem, was ihre Exponential-Site aufzeichnet, den Wirkungsteil macht, den ein Geldgeber am Ende einer Förderung verlangt - Zahlen aus gesammelten Informationen, Newsletter-Listen, Aufrufzählern, Suchstatistiken und dem Audit-Protokoll zusammentragen, sie exportieren, ohne personenbezogene Daten preiszugeben, und eine Gliederung mit einem Platz für jeden Beleg.
keywords: Förderergebnisse, Wirkungsbericht, Belege, Zahlen, CSV-Export, gesammelte Informationen, Newsletter-Abonnenten, Aufrufzähler, Audit-Protokoll, Geldgeber, Wirkung, grant outcomes, outcome statement, evidence, figures, CSV export, collected information, newsletter subscribers, view counts, audit trail, funder, impact
type: help
---
# Förderergebnisse für Geldgeber

Diese Seite richtet sich an die Person, die am Ende eines Förderzeitraums an den Geldgeber schreibt. Sie zeigt, wo jede
Zahl in Exponential zu finden ist, wie Sie sie herausholen und wie Sie personenbezogene Daten aus dem heraushalten, was
Sie verschicken. Sie setzt voraus, dass die Messungen rechtzeitig eingeschaltet wurden; falls nicht, lesen Sie für den
nächsten Zeitraum [Wirkung mit Website-Daten messen](wirkung-mit-website-daten-messen.md).

## Inhalt

1. [Leistungen und Wirkungen](#leistungen-und-wirkungen)
2. [Wo die Zahlen liegen](#wo-die-zahlen-liegen)
3. [Die Zahlen herausholen](#die-zahlen-herausholen)
4. [Personenbezogene Daten heraushalten](#personenbezogene-daten-heraushalten)
5. [Eine Gliederung für den Geldgeber](#eine-gliederung-für-den-geldgeber)
6. [Checkliste](#checkliste)

## Leistungen und Wirkungen

| | Leistung | Wirkung |
|---|---|---|
| Bedeutet | Was Sie hergestellt haben | Was sich für Menschen verändert hat |
| Beispiel | 40 Informationsseiten in zwei Sprachen | Die Hilfeanfragen über das Formular sind gestiegen |
| Beleg in Exponential | Inhalte und Versionen mit Datum | Anzahl der gesammelten Informationen pro Monat |

Die Site liefert gute Belege für Leistungen und einige Belege für Wirkungen. Geschichten von Menschen, denen Sie
geholfen haben, Umfrageergebnisse und Aussagen von Partnerorganisationen kommen von außerhalb der Site.

## Wo die Zahlen liegen

| Zahl | Wo in Exponential | Quelle |
|---|---|---|
| Im Zeitraum veröffentlichte Seiten | Inhaltsbaum; Versionen mit Veröffentlichungsdatum; Kanal `content` des Audit-Protokolls | [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md), [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md) |
| Eingegangene Formulare (Anmeldungen, Anfragen) | **Setup > Gesammelte Informationen**: Zahlen pro Formular, **CSV exportieren** | [Formulareinsendungen lesen und exportieren](../../ersteller/website-ersteller/formulareinsendungen.md) |
| Newsletter-Abonnenten | Abonnementliste von `cjw_newsletter` mit Status (ausstehend, bestätigt, freigegeben ...) | [cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md) |
| Versandte Newsletter | Das Newsletter-Archiv | ebenda |
| Seitenaufrufe | Aufrufzähler pro Knoten (Cronjob-Teil `updateviewcount`) oder ein Analysedienst | [Besucherstatistiken und Analyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md) |
| Wonach gesucht wurde | Suchstatistiken, falls eingeschaltet (`LogSearchStats`) | ebenda |
| Spenden über den Shop | Bestellungen und Shop-Statistiken | [Shop-Dashboard](../../../funktionen/6.0/shop-dashboard.md) |
| Teilnahme an Veranstaltungen | Einsendungen des Anmeldeformulars der Veranstaltung | [Veranstaltungen und Kalender](../kommunikation/veranstaltungen-und-kalender-fuer-non-profit-organisationen.md) |

## Die Zahlen herausholen

1. **Formulare.** Unter **Setup > Gesammelte Informationen** zeigt die Übersicht die Anzahl der Einsendungen pro
   Formular. Verwenden Sie für einen Zeitraum **CSV exportieren** und zählen Sie die Zeilen nach Datum in einer
   Tabellenkalkulation. Der Export ist UTF-8, umfasst höchstens 50 000 Zeilen und wird ohne die Werte im
   Audit-Protokoll vermerkt ([Formulareinsendungen lesen und exportieren](../../ersteller/website-ersteller/formulareinsendungen.md)).
2. **Newsletter.** Zählen Sie die bestätigten Abonnenten in der Abonnementliste zu Beginn und am Ende des Zeitraums.
3. **Inhalte.** Lassen Sie sich die im Zeitraum angelegten Objekte in einem Ordner auflisten, oder bitten Sie einen
   Administrator, das Audit-Protokoll nach Veröffentlichungsereignissen zu durchsuchen (`exp:audit tail`,
   [Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)). Zum Lesen des Audit-Protokolls ist `audit/read` nötig.
4. **Aufrufe.** Nehmen Sie die Aufrufzähler oder die Zahlen des Analysedienstes für den Zeitraum.

Notieren Sie, woher jede Zahl stammt und an welchem Datum Sie sie erhoben haben. Ein Geldgeber kann danach fragen.

## Personenbezogene Daten heraushalten

- Nennen Sie **Anzahlen**, keine Namen. Löschen Sie die CSV-Exporte, sobald Sie gezählt haben.
- Kopieren Sie keine Formulareinsendungen, E-Mail-Adressen oder Namen von Spendern in das, was Sie verschicken.
- Wenn Sie eine ehrenamtliche Person oder einen Leistungsempfänger zitieren, fragen Sie vorher und bewahren Sie die
  Einwilligung auf.
- Siehe [Datenschutz für Spender und Ehrenamtliche](../betrieb/datenschutz-fuer-spender-und-ehrenamtliche.md).

## Eine Gliederung für den Geldgeber

| Abschnitt | Inhalt | Beleg |
|---|---|---|
| Zusammenfassung | Was die Förderung erreicht hat, in drei Sätzen | |
| Ziele und Ergebnisse | Jedes Ziel mit seiner geplanten und tatsächlichen Zahl | Zahlen aus der Tabelle oben |
| Leistungen | Was gebaut und veröffentlicht wurde, mit Adressen | Live-Links, Bildschirmfotos |
| Wirkungen | Was sich verändert hat, mit Zahlen und Geschichten | Gesammelte Informationen, Abonnenten, Geschichten |
| Barrierefreiheit und Teilhabe | Durchgeführte Tests, angebotene Sprachen | Testergebnisse, Erklärung zur Barrierefreiheit |
| Nachhaltigkeit | Wer die Site jetzt betreibt, Budget für das nächste Jahr | [Budgetposten](budgetposten-fuer-hosting-und-wartung.md) |
| Erkenntnisse | Was Sie anders machen würden | |

## Checkliste

- [ ] Jede Zahl hat eine Quelle und ein Datum.
- [ ] Nur Anzahlen; keine personenbezogenen Daten.
- [ ] CSV-Exporte nach dem Zählen gelöscht.
- [ ] Links zu Live-Seiten funktionieren.
- [ ] Der Abschnitt zur Nachhaltigkeit nennt eine Person und ein Budget.
- [ ] Das Dokument ist als Download auf der Site veröffentlicht, sofern der Geldgeber es erlaubt ([Jahresveröffentlichungen als Downloads](../transparenz/jahresveroeffentlichungen-als-downloads.md)).

## Verwandte Seiten

- [Fördermittel und die Website](README.md)
- [Wirkung mit Website-Daten messen](wirkung-mit-website-daten-messen.md)
- [Webanalyse und was Sie messen sollten](../../kunden/alltag/analyse-und-was-sie-messen-sollten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
