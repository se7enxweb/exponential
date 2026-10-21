---
title: Eine Site für den öffentlichen Sektor aufbauen
audience: government, website-creator, project-manager, admin
category: public-sector
order: 10
summary: Index aller Anleitungen zum Aufbauen in der Hilfe für Behörden - Leistungskatalog, Formulare, Bürgerkonten, Nachrichten und Warnmeldungen, Veranstaltungen, Dokumente und Publikationen, Suche, Karten, Newsletter, Multisite, Intranets und Extranets - mit dem, was jede beantwortet, und einer Lesereihenfolge.
keywords: aufbauen, Site für den öffentlichen Sektor, Leistungskatalog, Formulare, Konten, Nachrichten, Warnmeldungen, Veranstaltungen, Dokumente, Suche, Karten, Newsletter, Multisite, Intranet, Extranet, building, public-sector site, service catalog, forms, accounts, news, alerts, events, documents, search, maps, newsletters
type: help
---
# Eine Site für den öffentlichen Sektor aufbauen

Dieser Bereich richtet sich an Site-Ersteller, Projektleiter und Administratoren, die die Teile einer Site für den
öffentlichen Sektor mit Exponential aufbauen. Jeder Ordner behandelt einen Teil (Leistungen, Formulare, Konten,
Nachrichten, Veranstaltungen, Dokumente, Suche, Karten, Newsletter, mehrere Sites, Intranets und Extranets) und zeigt,
was das Produkt bietet, wie man es einrichtet und wo es endet. Jede Aussage über das Produkt ist an diesem Repository
geprüft, und jede Lücke wird benannt.

## Inhalt

1. [Die Ordner](#die-ordner)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Wiederkehrende Themen](#wiederkehrende-themen)

## Die Ordner

| Ordner | Die Antwort in einer Zeile |
|---|---|
| [Leistungskatalog](../../../../eng-us/help/government/building/service-catalog/README.md) | Eine Klasse für Leistungen, Seiten in einfacher Sprache, Lebenslagen und Themen, Auffindbarkeit, Ansprechpartner, Links zu Online-Verfahren, Pflege |
| [Formulare](formulare/README.md) | Der Information Collector, barrierefreie Formulare, Spamschutz und Drittanbieter, CSRF-Token, Anhänge, Mail, Aufbewahrung, Beteiligungsverfahren und Umfragen |
| [Bürgerkonten](buergerkonten/README.md) | Registrierung, Anmeldung und Sitzungen, zweistufige Anmeldung, Passwortregeln, Kontenhygiene, Deaktivierung und Entfernung, Support |
| [Nachrichten und Warnmeldungen](../../../../eng-us/help/government/building/news-and-alerts/README.md) | Nachrichtenartikel, dringende Bekanntmachungen, RSS, Benachrichtigungen, Newsletter, Pressemitteilungen, Archiv und Ablauf, Metadaten |
| [Veranstaltungen](veranstaltungen/README.md) | Was mitgeliefert wird (Event, Event calendar, Multicalendar), Kalenderansichten, Listen kommender Termine, Anmeldungen, Änderungen, Barrierefreiheit, Archiv |
| [Dokumente und Publikationen](dokumente-und-publikationen/README.md) | Dateien, eine Dokumentenbibliothek, Versionen, Metadaten, PDF-Export, barrierefreie Dokumente, OpenDocument, Download-Berechtigungen |
| [Suche](../../../../eng-us/help/government/building/search/README.md) | Die eingebaute Suchmaschine, durchsuchbare Felder, Solr mit ezfind, Statistiken und die damit verbundenen Datenschutzpflichten |
| [Karten](../../../../eng-us/help/government/building/maps/README.md) | Das Feld `ezgmaplocation` mit Google Maps und was es nicht gibt |
| [Newsletter](../../../../eng-us/help/government/building/newsletters/README.md) | cjw_newsletter, Double-Opt-in, Probe, Listenpflege, Abmeldung mit einem Klick |
| [Multisite](../../../../eng-us/help/government/building/multisite/README.md) | Eine Installation für viele Abteilungen: Sites, Designs, gemeinsame Inhalte, Rollen, Betrieb |
| [Intranets](intranets/README.md) | Ein Siteaccess nur für Mitarbeiter, Sektionen und Rollen, Anmeldeoptionen, Sitzungen |
| [Extranets](extranets/README.md) | Auftragnehmer und Partner mit Zugang nur zu ihren eigenen Inhalten, Konten und deren Ende |

## Lesereihenfolge

1. **Leistungskatalog**: für die meisten Behörden der Kern der Site.
2. **Dokumente und Publikationen** und **Nachrichten und Warnmeldungen**: die Inhalte, die am schnellsten wachsen.
3. **Formulare** und **Bürgerkonten**: alles, was personenbezogene Daten sammelt.
4. **Suche**, **Veranstaltungen**, **Newsletter** und **Karten** nach Bedarf.
5. **Multisite**, **Intranets** und **Extranets**, wenn mehr als eine Zielgruppe oder Organisation die Plattform teilt.

Nach oben: [Behörden: hier beginnen](../verwaltungen-hier-beginnen.md). Die Planung je Art von Organisation steht in
[Verwaltungsebenen](../../../../eng-us/help/government/levels/README.md).

## Wiederkehrende Themen

| Thema | Wo es wiederkehrt | Lesen |
|---|---|---|
| Barrierefreiheit dessen, was Sie bauen | Formulare, Veranstaltungen, Dokumente, Bekanntmachungen | [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md) |
| Personenbezogene Daten und Aufbewahrung | Formulare, Konten, Veranstaltungen, Newsletter | [Standards zum Datenschutz](../../../../eng-us/standards/privacy.md) |
| Drittanbieter (CAPTCHA, Karten, Analyse, Teilen in sozialen Netzwerken) | Formulare, Karten, Nachrichten | die Datenschutzhinweise der jeweiligen Seite |
| Caches und dringende Inhalte | Bekanntmachungen, Veranstaltungen, Warnmeldungen | [Caches](../../../anleitungen/caches.md) |
| Rollen und Sektionen | jeder Ordner | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md), [Sektionen](../../../anleitungen/sektionen.md) |
| Daten, die Listen und Ablauf steuern | Nachrichten, Veranstaltungen, Bekanntmachungen, Leistungen | [Cronjobs](../../../anleitungen/cronjobs.md) |

## Verwandte Seiten

- [Verwaltungsebenen](../../../../eng-us/help/government/levels/README.md)
- [Website-Ersteller: hier beginnen](../../../../eng-us/help/creators/website-creators/website-creators-start-here.md)
- [Ihre Website planen](../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
