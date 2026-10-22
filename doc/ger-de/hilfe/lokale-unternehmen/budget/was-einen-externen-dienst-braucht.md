---
title: Was einen externen Dienst braucht
audience: local-business, businesses, customer
category: local-business
order: 74
summary: Die klare Liste für lokale Unternehmen, was Exponential nicht von selbst erledigt - Unternehmenseinträge auf Karten- und Suchplattformen, Terminbuchung, Tischreservierung, Restaurantbestellungen, Kassenanbindung, Lagerbestand, Gutscheincodes, Kartenkacheln, CAPTCHA, Zahlungsabwicklung, E-Mail-Zustellung, Analyse - mit dem, wonach gesucht wurde, dem üblichen Weg, jede Lücke zu schließen, und wen Sie fragen.
keywords: externer Dienst, nicht eingebaut, Buchung, Termine, Reservierungen, Online-Bestellung, Kasse, POS, Google Business Profile, Unternehmenseintrag, Kartenanbieter, CAPTCHA, Zahlungsanbieter, Integration, lokales Unternehmen, outside service, not built in, booking, point of sale, business listing
type: help
---
# Was einen externen Dienst braucht

Diese Seite ist für Inhaber eines lokalen Unternehmens, die wissen wollen, bevor sie Geld ausgeben, was eine Website
mit Exponential **nicht** von selbst erledigt. Jeder Posten wurde im Repository geprüft: in den Blöcken `require` und
`suggest` der `composer.json`, im Verzeichnis `extension/` und in der vorhandenen Dokumentation, am 20. Oktober 2026.

## Inhalt

1. [Überhaupt keine Funktionen einer Website](#überhaupt-keine-funktionen-einer-website)
2. [Nicht eingebaut](#nicht-eingebaut)
3. [Eingebaut, aber auf einen externen Anbieter angewiesen](#eingebaut-aber-auf-einen-externen-anbieter-angewiesen)
4. [Wie Sie einen externen Dienst anbinden](#wie-sie-einen-externen-dienst-anbinden)
5. [Wen Sie fragen](#wen-sie-fragen)

## Überhaupt keine Funktionen einer Website

| Bedarf | Wo es erledigt wird |
|---|---|
| Ihr Unternehmensprofil auf Karten- und Suchplattformen (zum Beispiel Google Business Profile), mit Öffnungszeiten, Fotos, Rezensionen | auf der jeweiligen Plattform, durch Sie ([Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)) |
| Geschriebene Kundenrezensionen auf Bewertungsplattformen | auf diesen Plattformen ([Bewertungen und Rezensionen](../kontakt-und-formulare/bewertungen-und-rezensionen.md)) |
| Ihre Postfächer | ein E-Mail-Anbieter ([Hosting für ein kleines Unternehmen wählen](../einstieg/hosting-fuer-ein-kleines-unternehmen-waehlen.md#domain-e-mail-und-die-website)) |
| Buchhaltung, Rechnungen, Steuererklärungen | Ihre Buchhaltungssoftware und Ihr Steuerberater ([Rechnungen und Belege](../../kunden/handel/rechnungen-und-belege.md)) |

## Nicht eingebaut

Für Folgendes wurde keine Erweiterung gefunden:

| Bedarf | Üblicher Weg | Lesen |
|---|---|---|
| Terminbuchung mit freien Zeiten (Friseursalons, Praxen, Handwerk) | ein Buchungsdienst, verlinkt oder eingebettet | [Angebots- und Anfrageformulare](../kontakt-und-formulare/angebots-und-anfrageformulare.md#terminanfragen-sind-keine-buchungen) |
| Tischreservierungen | ein Reservierungsdienst | [Restaurantbestellungen und was nicht eingebaut ist](../einfacher-shop/restaurantbestellungen-und-was-nicht-eingebaut-ist.md) |
| Online-Essensbestellung mit Zeitfenstern, Küchenbons, Bestellung am Tisch | eine Bestell- oder Lieferplattform | ebenda |
| Anbindung an die Ladenkasse (POS) | eine von einem Partner gebaute Integration, oder keine | [Integrationen planen](../../kunden/wachstum/eine-integration-planen.md) |
| Lagerbestand zählen | eine Erweiterung oder Integration | [Was der eingebaute Shop kann](../../unternehmen/shops/was-der-eingebaute-shop-kann.md) |
| Gutschein- oder Rabattcodes an der Kasse | eine Erweiterung; bis dahin Gutscheine von Hand | [Ein kleiner Katalog](../einfacher-shop/ein-kleiner-katalog.md#gutscheine-und-geschenkkarten) |
| Versandetiketten, Tarife der Versanddienstleister, Sendungsverfolgung | das Portal des Versanddienstleisters oder ein Versand-Handler | [Versand und Abwicklung](../../unternehmen/shops/versand-und-bearbeitung.md) |
| Veranstaltungstickets mit Platzbegrenzung, iCal-Dateien | ein Ticketdienst | [Veranstaltungen und Angebote](../marketing/veranstaltungen-und-angebote.md#was-nicht-eingebaut-ist) |
| Ein Datentyp für Öffnungszeiten, „jetzt geöffnet“ | mit vorhandenen Datentypen abgebildet; „jetzt geöffnet“ per Template | [Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md) |
| Strukturierte Daten vom Typ LocalBusiness | Template-Arbeit | [Strukturierte Daten: was es gibt](../gefunden-werden/strukturierte-daten-was-es-gibt.md) |
| Live-Chat | das Skript eines Chat-Dienstes | fragen Sie die Person, die Ihre Website baut |

## Eingebaut, aber auf einen externen Anbieter angewiesen

| Funktion | Anbieter | Schlüssel oder Konto |
|---|---|---|
| Karte in `ezgmaplocation` | Google Maps | API-Schlüssel für die Google Maps JavaScript API ([Ihren Standort auf einer Karte zeigen](../karten-und-oeffnungszeiten/ihren-standort-auf-einer-karte-zeigen.md)) |
| Karten-Block, Google-Variante | Google Maps Embed | `GoogleMapsApiKey`; die OpenStreetMap-Variante braucht keinen |
| CAPTCHA | Google reCAPTCHA oder hCaptcha | Website- und geheimer Schlüssel ([Spam stoppen](../kontakt-und-formulare/spam-stoppen.md)) |
| Zahlungen | PayPal (`ezpaypal`) oder Authorize.Net (`ezauthorize`) | Händlerkonto ([Zahlungen](../einfacher-shop/zahlungen-paypal-und-authorize-net.md)) |
| Analyse über `bcwebsitestatistics` | Google Analytics | Tracking-Kennung ([Besuche messen](../gefunden-werden/besuche-messen.md)) |
| Newsletter-Zustellung | der E-Mail-Transport Ihres Servers oder ein SMTP-Anbieter | DNS-Einträge der Absenderdomain ([Newsletter für lokale Unternehmen](../marketing/newsletter-fuer-lokale-unternehmen.md)) |
| Entwürfe von Exponential Intelligence | ein vom Administrator gewählter KI-Anbieter | Schlüssel des Anbieters ([Exponential Intelligence](../../kunden/wachstum/exponential-intelligence.md)) |

## Wie Sie einen externen Dienst anbinden

| Weg | Aufwand | Hinweis |
|---|---|---|
| Ein Link oder eine Schaltfläche („Jetzt buchen“) | Minuten, durch einen Redakteur | der einfachste und robusteste Weg |
| Ein eingebettetes Widget (Skript oder Frame) | gering, durch den Ersteller | Datenschutzhinweis; die Content Security Policy muss es erlauben ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)) |
| Eine Integration über die REST-API oder Remote-Dienste | ein Projekt | [REST-API für Inhaber](../../kunden/wachstum/rest-api-fuer-betreiber.md), [Remote-Dienste](../../kunden/wachstum/remote-dienste-expservices.md) |
| Eine neue Erweiterung | ein Projekt; überlegen Sie, sie der Gemeinschaft zurückzugeben | [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md) |

## Wen Sie fragen

- Für Links und Widgets: die Person oder den Partner, der Ihre Website baut.
- Für Integrationen und neue Erweiterungen: einen Exponential-Partner ([Implementierungspartner](../../partner/implementierungspartner.md)).
- Wenn etwas von hier für alle gebaut werden sollte: [Funktionswünsche und gesponserte Funktionen](../../partner/funktionswuensche-und-gesponserte-funktionen.md).

## Verwandte Seiten

- [Das Kostenbild](das-kostenbild.md)
- [Integrationen planen](../../kunden/wachstum/eine-integration-planen.md)
- [FAQ zu Shops](../../unternehmen/shops/shop-faq.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
