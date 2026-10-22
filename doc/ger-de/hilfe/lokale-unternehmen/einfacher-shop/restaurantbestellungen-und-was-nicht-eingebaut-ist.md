---
title: Restaurantbestellungen und was nicht eingebaut ist
audience: local-business, businesses, customer
category: local-business
order: 45
summary: Was ein Restaurant, Café oder Imbiss mit einer Exponential-Website tun kann - Speisekartenseiten, Angebote, Gutscheine, Catering-Anfragen, ein Shop für Vorbestellungen zur Abholung - und was nicht eingebaut ist - Tischreservierungen, Lieferzeitfenster, Küchenbons, Bestellung am Tisch, Kassenanbindung - mit den üblichen Wegen, jede Lücke zu schließen.
keywords: Restaurant, Café, Imbiss, Speisekarte, Online-Bestellung, Tischreservierung, Lieferung, Küche, Kasse, POS, Gutscheine, Catering, Allergene, externer Dienst, lokales Unternehmen, restaurant, cafe, takeaway, menu, online ordering, table reservation, delivery, kitchen, vouchers, allergens, outside service, local business
type: help
---
# Restaurantbestellungen und was nicht eingebaut ist

Diese Seite ist für Inhaber von Restaurants, Cafés, Imbissen und Bäckereien. Exponential erledigt den Website-Teil
gut: Speisekarten, Angebote, Veranstaltungen, Fotos, Gutscheine, ein Kontaktformular. Live-Essensbestellungen und
Reservierungen sind eine andere Art von Software, und sie sind **nicht eingebaut**. Gesucht in `composer.json` und
`extension/` am 20. Oktober 2026: keine Erweiterung für Buchung, Reservierung, Lieferzeitfenster oder Kasse (POS).

## Inhalt

1. [Was gut funktioniert](#was-gut-funktioniert)
2. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
3. [Wege, die Lücken zu schließen](#wege-die-lücken-zu-schließen)
4. [Die Speisekarte](#die-speisekarte)
5. [Allergene und Ernährungshinweise](#allergene-und-ernährungshinweise)
6. [Checkliste](#checkliste)

## Was gut funktioniert

| Bedarf | Wie | Lesen |
|---|---|---|
| Speisekarte mit Preisen | Ordner mit Artikeln oder eine Matrixtabelle | [Die Speisekarte](#die-speisekarte) |
| Tages- oder Wochenangebote | ein Artikel oder eine Veranstaltung, nach Datum veröffentlicht und versteckt | [Veranstaltungen und Angebote](../marketing/veranstaltungen-und-angebote.md) |
| Öffnungszeiten, Schließungen an Feiertagen | Objekt mit den Öffnungszeiten, Hinweise | [Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md) |
| Geschenkgutscheine | ein kleiner Shop mit PayPal | [Ein kleiner Katalog](ein-kleiner-katalog.md#gutscheine-und-geschenkkarten) |
| Catering- und Gruppenanfragen | ein Formular des Informationssammlers | [Angebots- und Anfrageformulare](../kontakt-und-formulare/angebots-und-anfrageformulare.md) |
| Vorbestellungen zur Abholung (Torten, Feiertagsmenüs) | der Shop, mit Abholung | [Mehrwertsteuer, Versand und Abholung](mehrwertsteuer-versand-und-abholung.md#abholung-im-geschäft) |
| Veranstaltungen (Weinabend, Livemusik) | die Klasse `event` in einem Veranstaltungskalender | [Veranstaltungen und Angebote](../marketing/veranstaltungen-und-angebote.md) |
| Newsletter für Stammgäste | `cjw_newsletter` | [Newsletter für lokale Unternehmen](../marketing/newsletter-fuer-lokale-unternehmen.md) |

## Was nicht eingebaut ist

| Bedarf | Stand | Warum es wichtig ist |
|---|---|---|
| Tischreservierungen mit Verfügbarkeit | nicht eingebaut | braucht Live-Verfügbarkeit pro Uhrzeit und Tisch |
| Zeitfenster für Lieferung oder Abholung | nicht eingebaut | der Shop hat an der Kasse keine Wahl eines Zeitfensters |
| Bestellungen an die Küche schicken (Drucker, Bildschirm) | nicht eingebaut | Bestellungen erscheinen nur im Reiter Web-Shop und per E-Mail |
| Bestellung am Tisch per QR-Code | nicht eingebaut | |
| Anbindung an die Ladenkasse (POS) | nicht eingebaut | Preise und Verkäufe werden nicht abgeglichen |
| Bestand von Gerichten („nur noch 5“) | nicht eingebaut | der Shop hat keine Lagerzählung |
| Prüfung des Liefergebiets anhand der Adresse | nicht eingebaut | |

Der Shop ist ein allgemeiner Web-Shop: Er eignet sich gut für Gutscheine und Vorbestellungen einen Tag im Voraus, ist
aber nicht für warmes Essen gemacht, das für die nächsten dreißig Minuten bestellt wird.

## Wege, die Lücken zu schließen

| Lücke | Üblicher Weg | Aufwand |
|---|---|---|
| Reservierungen | ein Reservierungsdienst; eine Schaltfläche „Tisch reservieren“, die darauf verlinkt, oder sein Widget, vom Ersteller eingebettet | gering, plus die Gebühr des Dienstes |
| Bestellungen für Lieferung und Mitnahme | eine Bestell- oder Lieferplattform; ein Link zu Ihrer Seite dort | gering, plus Provision |
| Einfache Vorbestellungen | der eingebaute Shop mit Abholung, Bestellungen jeden Morgen geprüft | mittel |
| Nur telefonische Reservierungen | Telefonnummer gut sichtbar auf jeder Seite, als wählbarer Link | keiner |
| Ein eigenes Buchungsmodul | von einem Partner als Erweiterung entwickelt | groß; bitten Sie um ein Angebot ([Integrationen planen](../../kunden/wachstum/eine-integration-planen.md)) |

Ein eingebettetes Widget lädt Skripte eines anderen Unternehmens: Nennen Sie es in der Datenschutzerklärung, und wenn
die Website eine Content Security Policy sendet, muss der Ersteller die Hosts des Dienstes erlauben
([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).

## Die Speisekarte

- Ein Ordner „Speisekarte“ mit einem Artikel pro Gang (Vorspeisen, Hauptgerichte, Desserts) oder eine Seite mit einer
  Matrixtabelle (Gericht, Beschreibung, Preis).
- Halten Sie Preise an einer Stelle: auf der Speisekartenseite, nicht zusätzlich in einem PDF, das veralten wird. Wenn
  Sie doch ein PDF anbieten, ersetzen Sie es bei jeder Preisänderung ([Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md)).
- Schreiben Sie die Speisekarte als Text, nicht nur als Foto der gedruckten Karte: Text wird von Suchmaschinen und
  Screenreadern gelesen.
- Saisonale Speisekarte: Bereiten Sie sie als Entwurf vor, und veröffentlichen Sie sie am Tag selbst
  ([Saisonale Änderungen](../im-betrieb/saisonale-aenderungen.md)).

## Allergene und Ernährungshinweise

Viele Länder schreiben Allergeninformationen für Lebensmittel vor. Halten Sie sie bei jedem Gericht fest (ein Textfeld
oder eine Spalte in der Tabelle), und halten Sie sie aktuell, wenn sich Rezepte ändern. Was Sie zeigen müssen, ist eine
rechtliche Frage für Ihren Berater; Exponential speichert und zeigt nur, was Sie eintippen.

## Checkliste

- [ ] Speisekarte als Text, Preise an einer Stelle.
- [ ] Reservierungen: Dienst gewählt oder Telefon gut sichtbar.
- [ ] Bestellungen: Plattform-Link, Vorbestellungs-Shop oder keines, entschieden.
- [ ] Gutscheine im Shop, falls gewünscht.
- [ ] Allergeninformationen aktuell.
- [ ] Externe Dienste in der Datenschutzerklärung aufgeführt.

## Verwandte Seiten

- [Was einen externen Dienst braucht](../budget/was-einen-externen-dienst-braucht.md)
- [Brauchen Sie einen Shop?](brauchen-sie-einen-shop.md)
- [Veranstaltungen und Angebote](../marketing/veranstaltungen-und-angebote.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
