---
title: "Lokale Unternehmen: hier beginnen"
audience: local-business, businesses, customer
category: local-business
order: 1
summary: Die Einstiegsseite für kleine Unternehmen mit einem Standort - Geschäfte, Restaurants, Cafés, Handwerksbetriebe, Friseursalons und Praxen -, die eine Website wollen, die gefunden, geschätzt und genutzt wird, mit einem realistischen Kostenbild, lokaler Suche, Karten, Öffnungszeiten, einem einfachen Shop, Kontaktformularen, Newslettern und einer klaren Liste dessen, was einen externen Dienst braucht.
keywords: lokales Unternehmen, kleines Unternehmen, ein Standort, Geschäft, Restaurant, Café, Handwerk, Friseursalon, Praxis, lokale Suchmaschinenoptimierung, Karte, Öffnungszeiten, Kontaktformular, einfacher Shop, Newsletter, Budget, Partner, Freiberufler, hier beginnen, local business, small business, local SEO, opening hours, start here
featured: true
type: help
---
# Lokale Unternehmen: hier beginnen

Dieser Bereich ist für kleine Unternehmen mit einer Adresse oder einigen wenigen: ein Geschäft in der Fußgängerzone,
ein Restaurant oder Café, ein Installateur oder Elektriker, ein Friseursalon, eine Zahnarzt- oder Physiotherapiepraxis,
eine Bäckerei, eine Fahrradwerkstatt. Sie wollen eine Website, die Menschen in der Nähe bei ihrer Suche finden, die
ihnen sagt, wann Sie geöffnet haben und wie man Sie erreicht, über die sie Ihnen eine Nachricht schicken oder etwas
kaufen können und die nicht Ihre Abende auffrisst.

Die Seiten sind für die Inhaberin oder den Inhaber geschrieben oder für die eine Person, die sich um die Website
kümmert. Sie sagen in einfacher Sprache, was Exponential kann, was es nicht kann, was Sie selbst erledigen können und
wann es sich lohnt, jemanden zu bezahlen. Jede Seite verweist auf die technische Seite für die Person, die die
Einrichtung übernimmt.

> **Hinweis**
> Jede hier beschriebene Funktion wurde im Exponential-Repository (`composer.json`, `extension/`, `settings/`) oder in
> der vorhandenen Dokumentation geprüft, und die Quelle ist genannt oder verlinkt. In diesem Bereich stehen keine
> Preise echter Anbieter, keine Statistiken und keine Kundennamen. Wo eine Seite Zahlen verwendet, sind sie als
> **Beispielannahmen** gekennzeichnet, die Sie durch Ihre eigenen ersetzen.

## Inhalt

1. [Was Sie bekommen, in einer Minute](#was-sie-bekommen-in-einer-minute)
2. [Was Sie außerhalb von Exponential erledigen](#was-sie-außerhalb-von-exponential-erledigen)
3. [Die Abschnitte dieses Leitfadens](#die-abschnitte-dieses-leitfadens)
4. [Ihre ersten zwei Wochen](#ihre-ersten-zwei-wochen)
5. [Wenn Sie nicht sicher sind, ob das zu Ihnen passt](#wenn-sie-nicht-sicher-sind-ob-das-zu-ihnen-passt)
6. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was Sie bekommen, in einer Minute

| Sie wollen | Was Exponential bietet | Lesen |
|---|---|---|
| Seiten, die Sie im Browser bearbeiten können | die Administration und den Editor, keine Programmierung für alltägliche Änderungen | [Inhalte bearbeiten](../inhalte-bearbeiten.md) |
| In der lokalen Suche gefunden werden | lesbare Adressen, Titel und Beschreibungen pro Seite (`xrowmetadata`), XML-Sitemaps (`bcgooglesitemaps`), eine mitgelieferte `robots.txt` | [Gefunden werden](gefunden-werden/README.md) |
| Eine Karte mit Ihrer Adresse | den Datentyp `ezgmaplocation` und einen Karten-Block im Layout-Editor | [Karten und Öffnungszeiten](karten-und-oeffnungszeiten/README.md) |
| Öffnungszeiten auf jeder Seite | keinen besonderen Datentyp; eine kleine Inhaltsklasse oder ein Textblock erledigt das | [Öffnungszeiten ohne besonderen Datentyp](karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md) |
| Ein Kontaktformular, das nicht mit Spam vollläuft | den Informationssammler, Formular-Token, reCAPTCHA oder hCaptcha | [Kontakt und Formulare](kontakt-und-formulare/README.md) |
| Ein paar Produkte, Gutscheine oder Geschenkkarten verkaufen | den im Kernel eingebauten Shop, PayPal über `ezpaypal` | [Ein einfacher Shop](einfacher-shop/README.md) |
| Einen Newsletter für Stammkunden | `cjw_newsletter` | [Newsletter für lokale Unternehmen](marketing/newsletter-fuer-lokale-unternehmen.md) |
| Sternebewertungen auf Seiten | `ezstarrating` (nur Sterne, keine geschriebenen Rezensionen) | [Bewertungen und Rezensionen](kontakt-und-formulare/bewertungen-und-rezensionen.md) |
| Eine Website in zwei oder drei Sprachen für Gäste | mehrsprachige Inhalte und Sprachadressen | [Mehrsprachig für Urlaubsregionen](marketing/mehrsprachig-fuer-urlaubsregionen.md) |
| Keine Lizenzgebühr | freie Software unter der GNU GPL, Version 2 oder später | [Lizenzierung für Käufer](../geschaeft/lizenzen-fuer-kaeufer.md) |

Alle oben genannten Erweiterungen stehen im `require`-Block der `composer.json` des Projekts und gehören damit zur
Standarddistribution. Jede muss trotzdem für Ihre Website eingeschaltet und eingerichtet werden.

## Was Sie außerhalb von Exponential erledigen

Manches, was ein lokales Unternehmen braucht, ist gar keine Funktion einer Website oder braucht einen eigenen Dienst.
Die vollständige Liste steht in [Was einen externen Dienst braucht](budget/was-einen-externen-dienst-braucht.md). Die
Kurzfassung:

- **Ihr Eintrag auf Karten- und Suchplattformen** (ein Unternehmensprofil mit Fotos, Öffnungszeiten und Rezensionen)
  wird auf diesen Plattformen angelegt und aktuell gehalten, nicht in Exponential.
- **Terminbuchung und Tischreservierung**: Im Repository wurde kein Buchungsmodul gefunden. Verlinken Sie einen
  Buchungsdienst, oder lassen Sie einen Partner eines bauen.
- **Online-Bestellungen für ein Restaurant** (Lieferzeitfenster, Küchenbons, Bestellung am Tisch): nicht eingebaut.
- **Eine Kassenanbindung (POS)** zwischen Ladenkasse und Website: nicht eingebaut.
- **Kartenkacheln und CAPTCHA-Prüfungen** kommen von externen Anbietern; manche brauchen einen API-Schlüssel.
- **Die Zahlungsabwicklung** übernimmt ein Zahlungsanbieter wie PayPal; Exponential verbindet sich mit ihm.

## Die Abschnitte dieses Leitfadens

| Abschnitt | Was er behandelt |
|---|---|
| [Einstieg](einstieg/README.md) | was die Website eines kleinen Unternehmens braucht, die Seiten planen, selbst machen oder Hilfe holen, Hosting |
| [Gefunden werden](gefunden-werden/README.md) | Grundlagen der lokalen Suche, Titel und Beschreibungen, Sitemaps, Adressen, strukturierte Daten, externe Einträge, Besucherzahlen |
| [Karten und Öffnungszeiten](karten-und-oeffnungszeiten/README.md) | eine Karte Ihres Standorts, Öffnungszeiten, Feiertagszeiten, mehrere Standorte |
| [Ein einfacher Shop](einfacher-shop/README.md) | ob Sie einen Shop brauchen, ein kleiner Katalog, Zahlungen, Mehrwertsteuer und Abholung, Restaurants |
| [Kontakt und Formulare](kontakt-und-formulare/README.md) | ein Kontaktformular, Spam, Angebotsanfragen, Bewertungen, Nachrichten und Datenschutz |
| [Marketing](marketing/README.md) | Veranstaltungen und Angebote, Newsletter, Fotos, Gäste aus dem Ausland, Smartphones |
| [Budget](budget/README.md) | das Kostenbild, ein durchgerechnetes Beispiel, ein Arbeitsblatt, externe Dienste |
| [Im Betrieb](im-betrieb/README.md) | die wöchentliche Routine, saisonale Änderungen, Sicherungen und Updates |
| [Checklisten](checklisten/README.md) | Checkliste zum Livegang, jährliche Überprüfung |
| [Vorlagen](vorlagen/README.md) | ein Website-Briefing, eine Vorlage für Öffnungszeiten |

Kurze Antworten stehen in den [FAQ](faq-fuer-lokale-unternehmen.md), die Begriffe im [Glossar](glossar-fuer-lokale-unternehmen.md)
und eine vorgeschlagene Lesereihenfolge im [Lesepfad](lesepfad-fuer-lokale-unternehmen.md).

## Ihre ersten zwei Wochen

| Tage | Tun | Lesen |
|---|---|---|
| 1-2 | Schreiben Sie auf, was die Website leisten muss: gefunden werden, Öffnungszeiten zeigen, Nachrichten annehmen, verkaufen? | [Was die Website eines kleinen Unternehmens braucht](einstieg/was-die-website-eines-kleinen-unternehmens-braucht.md) |
| 3 | Füllen Sie das Briefing aus | [Website-Briefing für ein lokales Unternehmen](vorlagen/website-briefing-fuer-ein-lokales-unternehmen.md) |
| 4 | Entscheiden Sie, wer baut und wer betreibt | [Selbst machen oder Hilfe holen](einstieg/selbst-machen-oder-hilfe-holen.md) |
| 5 | Versehen Sie es mit Zahlen, als Ihre eigenen Annahmen | [Budget-Arbeitsblatt](budget/budget-arbeitsblatt.md) |
| 6-7 | Wählen Sie das Hosting | [Hosting für ein kleines Unternehmen wählen](einstieg/hosting-fuer-ein-kleines-unternehmen-waehlen.md) |
| 8-10 | Planen Sie Ihre Seiten und Ihre Öffnungszeiten | [Ihre Seiten planen](einstieg/ihre-seiten-planen.md), [Vorlage für Öffnungszeiten](vorlagen/inhaltsvorlage-fuer-oeffnungszeiten.md) |
| 11-14 | Bereiten Sie Livegang und lokale Suche vor | [Checkliste zum Livegang](checklisten/checkliste-zum-livegang.md), [Grundlagen der lokalen Suchmaschinenoptimierung](gefunden-werden/grundlagen-der-lokalen-suchmaschinenoptimierung.md) |

## Wenn Sie nicht sicher sind, ob das zu Ihnen passt

Exponential ist ein vollständiges Content-Management-System, kein Baukasten-Abonnement. Das hat für ein kleines
Unternehmen Vorteile (keine Lizenzgebühr, Ihre Inhalte bleiben Ihre, die Website kann zu einem Shop oder einer zweiten
Sprache wachsen) und einen Preis: Jemand muss es installieren, hosten und aktuell halten. Wenn in Ihrem Unternehmen
niemand diese Aufgabe übernehmen will, planen Sie von Anfang an ein, einen Hoster oder Partner dafür zu bezahlen.
[Selbst machen oder Hilfe holen](einstieg/selbst-machen-oder-hilfe-holen.md) geht diese Entscheidung ehrlich durch.

## Benachbarte Bereiche

| Bereich | Für |
|---|---|
| [Unternehmen: hier beginnen](../unternehmen/unternehmen-hier-beginnen.md) | größere Organisationen; Shops, Intranets, Mitgliederseiten |
| [Onlineshops und kleine Läden](../unternehmen/shops/README.md) | den vollständigen Leitfaden zum eingebauten Shop |
| [Kunden: hier beginnen](../kunden/kunden-hier-beginnen.md) | Betreiber einer laufenden Exponential-Website |
| [Bereich für Shop-Betreiber](../kunden/handel/README.md) | tägliche und wöchentliche Routinen für einen laufenden Shop |
| [Website-Ersteller](../ersteller/website-ersteller/website-ersteller-hier-beginnen.md) | wer auch immer die Website baut |
| [Gemeinnützige Organisationen](../non-profit-organisationen/non-profit-organisationen-hier-beginnen.md) | Wohltätigkeitsorganisationen, Vereine und Gemeinschaftsgruppen |
| [Privatpersonen](../privatpersonen/privatpersonen-hier-beginnen.md) | eine Person mit einer eigenen Website |
| [Nicht-technische Menschen](../../../eng-us/help/non-technical-people/non-technical-people-start-here.md) | alle, die lieber keine technischen Seiten lesen |

## Verwandte Seiten

- [FAQ für lokale Unternehmen](faq-fuer-lokale-unternehmen.md)
- [Lesepfad für lokale Unternehmen](lesepfad-fuer-lokale-unternehmen.md)
- [Glossar für lokale Unternehmen](glossar-fuer-lokale-unternehmen.md)
- [Unternehmen: hier beginnen](../unternehmen/unternehmen-hier-beginnen.md)
- [Kunden: hier beginnen](../kunden/kunden-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
