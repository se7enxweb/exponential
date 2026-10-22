---
title: Kommunikation für Non-Profit-Organisationen
audience: non-profit, businesses, editor, sales-marketing
category: non-profits
order: 10
summary: Wie eine Non-Profit-Organisation über ihre Exponential-Website mit Unterstützern, Ehrenamtlichen und den Menschen, für die sie da ist, in Kontakt bleibt - der Unterstützer-Newsletter mit cjw_newsletter, Veranstaltungen mit den Veranstaltungsklassen von ezwebin, mehrere Sprachen sowie Kampagnen- und Spendenaufrufseiten mit Exponential Layouts - und was nicht eingebaut ist.
keywords: Kommunikation, Unterstützer, Newsletter, Veranstaltungen, Veranstaltungskalender, mehrsprachig, Sprachen, Kampagnen, Spendenaufrufe, Non-Profit-Organisation, communications, supporters, cjw_newsletter, events, event calendar, multilingual, campaigns, appeals, Exponential Layouts, non-profit
type: help
---
# Kommunikation für Non-Profit-Organisationen

Dieser Ordner richtet sich an Mitarbeitende in der Kommunikation, Fundraiser und Redakteure. Die Website einer
Non-Profit-Organisation ist nicht nur eine Broschüre: Hier abonnieren Unterstützer den Newsletter, hier werden
Veranstaltungen angekündigt, hier laufen Spendenaufrufe, und oft finden Menschen hier Hilfe in ihrer eigenen Sprache.

## Inhalt

1. [Was es gibt](#was-es-gibt)
2. [Die Seiten dieses Ordners](#die-seiten-dieses-ordners)
3. [Wie die Kanäle zusammenspielen](#wie-die-kanäle-zusammenspielen)
4. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)

## Was es gibt

| Kanal | Was Exponential mitliefert | Quelle |
|---|---|---|
| Unterstützer-Newsletter | `cjw_newsletter`: Listen, Double-Opt-in, Ausgaben als Inhalte, Versand per Cronjob, Bounces, Sperrliste, Archiv | [cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md) |
| Veranstaltungen | die `ezwebin`-Klassen `event`, `event_calendar` und `multicalendar` mit Kalender- und Programmansichten | [Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md) |
| Sprachen | bis zu 62 Inhaltssprachen pro Installation, Übersetzung pro Objekt, Sprachumschalter | [Die Grenze von 62 Sprachen](../../verwaltung/sprachen/die-grenze-von-62-sprachen.md) |
| Kampagnenseiten | Exponential Layouts mit Layout-Regeln, die sich nach Datum ein- und ausschalten lassen | [Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md) |
| Feeds | RSS 2.0, Atom, Podcast-Feeds | [RSS-Feeds für Ihre Website](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md) |
| Vorschauen in sozialen Medien | Titel, Beschreibung und Bild zum Teilen | [Metadaten und Teilen in sozialen Medien](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md) |

Alles wird über `composer.json` installiert; für nichts davon fallen Lizenzgebühren an.

## Die Seiten dieses Ordners

| Seite | Was sie beantwortet |
|---|---|
| [Unterstützer-Newsletter mit cjw_newsletter](unterstuetzer-newsletter-mit-cjw-newsletter.md) | Listen, Einwilligung, Schreiben und Versenden, Zustellung, Listenpflege |
| [Veranstaltungen und Kalender für Non-Profit-Organisationen](veranstaltungen-und-kalender-fuer-non-profit-organisationen.md) | Veranstaltungsseiten, Kalender, Anmeldung, was fehlt |
| [Mehrsprachige Non-Profit-Websites](mehrsprachige-non-profit-websites.md) | Menschen mit einem kleinen Team in mehreren Sprachen erreichen |
| [Kampagnen und Spendenaufrufe](kampagnen-und-spendenaufrufe.md) | Aufrufseiten, die sich nach Datum ein- und ausschalten, und ihre Verbindung zum Spenden |

## Wie die Kanäle zusammenspielen

| Anlass | Website | Newsletter | Veranstaltung | Kampagne |
|---|---|---|---|---|
| Ein Spendenaufruf beginnt | Das Kampagnenlayout schaltet sich ein | Eine Ausgabe kündigt ihn an | | Spenden-Button |
| Eine Benefizveranstaltung | Veranstaltungsseite mit Anmeldeformular | Eine Ausgabe lädt ein | Veranstaltung im Kalender | Die Kampagnenseite verlinkt darauf |
| Das Jahr endet | Jahresveröffentlichung | Eine Ausgabe dankt den Unterstützern | | Dankesbotschaft |
| Ein neues Angebot in einer neuen Sprache | Übersetzte Seiten | Ausgabe in dieser Sprache | | |

Ein einfacher [Redaktionskalender](../../kunden/alltag/redaktionskalender.md) hält alles im Gleichtakt.

## Was nicht eingebaut ist

| Nicht eingebaut | Planen Sie stattdessen |
|---|---|
| Beiträge in sozialen Medien direkt von der Website | Von Hand oder mit einem externen Werkzeug posten; geteilte Links tragen Ihre Metadaten |
| Ticketing, Platzbegrenzungen, iCal-Export, wiederkehrende Veranstaltungen | Siehe [Veranstaltungen und Kalender für Non-Profit-Organisationen](veranstaltungen-und-kalender-fuer-non-profit-organisationen.md#was-nicht-eingebaut-ist) |
| SMS-Kampagnen | Gehören nicht zu den dokumentierten Funktionen von `cjw_newsletter`; nutzen Sie einen externen Dienst mit Einwilligung |
| Spenderbeziehungsmanagement (CRM) | Ein externes CRM; die Website ist keines |

## Verwandte Seiten

- [Non-Profit-Organisationen: hier beginnen](../non-profit-organisationen-hier-beginnen.md)
- [Newsletter (Unternehmen)](../../unternehmen/newsletters/README.md)
- [Kampagnen und Landingpages (Unternehmen)](../../unternehmen/gemeinnuetzige/kampagnen-und-landingpages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
