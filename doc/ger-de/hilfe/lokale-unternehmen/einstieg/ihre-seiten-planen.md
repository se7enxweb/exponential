---
title: Ihre Seiten planen
audience: local-business, businesses, customer, website-creator
category: local-business
order: 12
summary: Eine Seitenliste für die Website eines lokalen Unternehmens - Geschäft, Restaurant, Handwerksbetrieb, Friseursalon oder Praxis - mit der Inhaltsklasse hinter jeder Seite, dem Ort, an dem Öffnungszeiten und Kontaktdaten stehen, damit sie nur einmal geschrieben werden, und wie Sie den Baum klein halten.
keywords: Seitenplan, Seitenübersicht, Inhaltsbaum, Inhaltsklassen, Speisekarte, Leistungsseite, Teamseite, Öffnungszeiten, Kontaktseite, lokales Unternehmen, Ordner, Artikel, page plan, site map, content tree, content classes, local business
type: help
---
# Ihre Seiten planen

Diese Seite ist für Inhaber oder die Person, die die Website baut, und die vor Arbeitsbeginn eine Seitenliste wollen.
In Exponential ist jede Seite ein **Inhaltsobjekt** einer **Inhaltsklasse** (Ordner, Artikel, Produkt,
Feedback-Formular und so weiter), das in einem **Inhaltsbaum** steht. Ein guter Plan ist ein kurzer Baum, in dem jede
Tatsache an genau einer Stelle steht.

## Inhalt

1. [Eine Seitenliste nach Art des Unternehmens](#eine-seitenliste-nach-art-des-unternehmens)
2. [Welche Klasse für welche Seite](#welche-klasse-für-welche-seite)
3. [Schreiben Sie jede Tatsache nur einmal](#schreiben-sie-jede-tatsache-nur-einmal)
4. [Halten Sie den Baum klein](#halten-sie-den-baum-klein)
5. [Arbeitsblatt](#arbeitsblatt)

## Eine Seitenliste nach Art des Unternehmens

| Seite | Geschäft | Restaurant oder Café | Handwerk | Friseursalon | Praxis |
|---|---|---|---|---|---|
| Startseite | ja | ja | ja | ja | ja |
| Was wir anbieten | Sortiment | Speisekarte | Leistungen und Einzugsgebiet | Behandlungen und Preise | Behandlungen, Hinweise zur Versicherung |
| Öffnungszeiten und Standort | ja | ja | Servicezeiten, Gebiet | ja | ja |
| Über uns, Team | ja | ja | ja, mit Qualifikationen | Stylisten | Behandelnde, mit Qualifikationen |
| Kontakt | Formular | Formular, Link zur Reservierung | Formular für Angebotsanfragen | Link zur Buchung | Link für Termine, Hinweis für Notfälle |
| Neuigkeiten oder Angebote | Angebote | Tagesgerichte, Veranstaltungen | - | Angebote | Schließungen an Feiertagen |
| Shop | wenn Sie online verkaufen | Gutscheine | - | Geschenkkarten, Produkte | - |
| Rechtliche Seiten | Impressum, Datenschutz | ebenso | ebenso | ebenso | ebenso, dazu berufsrechtliche Regeln |

Rechtliche Seiten unterscheiden sich von Land zu Land; fragen Sie Ihren Berater, was Sie veröffentlichen müssen. Dieser
Leitfaden ist keine Rechtsberatung.

## Welche Klasse für welche Seite

Die mitgelieferten Seitenpakete bringen die Klassen mit, die die meisten kleinen Websites brauchen. Die Pakete von
`ezwebin` enthalten `folder`, `article`, `frontpage`, `feedback_form`, `event`, `event_calendar`, `product` und andere
(`extension/ezwebin/packages/`; [ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin.md)).

| Seite | Klasse | Hinweis |
|---|---|---|
| Startseite | `frontpage` oder ein Layout aus dem Layout-Editor | [Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md) |
| Bereichsseiten (Leistungen, Speisekarte) | `folder` | ein Ordner listet auf, was unter ihm liegt |
| Eine Leistung, eine Behandlung | `article` | Titel, Einleitung, Text, Bild |
| Teammitglied | `article` oder eine eigene Klasse | fragen Sie die Person, bevor Sie ihr Foto veröffentlichen |
| Kontakt | `feedback_form` | [Ein Kontaktformular für Ihr Unternehmen](../kontakt-und-formulare/ein-kontaktformular-fuer-ihr-unternehmen.md) |
| Veranstaltungen, Angebote | `event` in einem `event_calendar` | [Veranstaltungen und Angebote](../marketing/veranstaltungen-und-angebote.md) |
| Produkte, Gutscheine | `product` | [Ein kleiner Katalog](../einfacher-shop/ein-kleiner-katalog.md) |
| Öffnungszeiten | eine kleine eigene Klasse oder ein Textblock | [Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md) |

Wie Sie Felder wählen, erklären [Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md) und
[Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md).

## Schreiben Sie jede Tatsache nur einmal

Ihre Adresse, Telefonnummer und Öffnungszeiten erscheinen an vielen Stellen: in der Fußzeile, auf der Kontaktseite, auf
der Karte, vielleicht in einem Banner. Wenn sie an jeder Stelle eingetippt werden, ist nach der nächsten Änderung eine
davon falsch. Bitten Sie die Person, die die Website baut:

- Adresse, Telefon und Öffnungszeiten in **einem Inhaltsobjekt** zu halten und es über die Templates in der Fußzeile
  und auf der Kontaktseite anzuzeigen ([Gemeinsame Kopf- und Fußzeile](../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md));
- die Karte in dasselbe Objekt wie die Adresse zu legen, damit ein Umzug nur eine Änderung bedeutet;
- **Beziehungen** zu verwenden, wenn eine Seite etwas zeigen muss, das an anderer Stelle gepflegt wird
  ([Beziehungen und Wiederverwendung](../../ersteller/website-ersteller/relationen-und-wiederverwendung.md)).

## Halten Sie den Baum klein

- Für die meisten lokalen Unternehmen reichen zwei Ebenen: Startseite, dann Bereiche, dann einzelne Seiten.
- Die Namen der Ordner werden zu Adressen (`/Services/Boiler-repair`), wählen Sie deshalb vor dem Livegang kurze,
  klare Namen ([Den Inhaltsbaum gliedern](../../ersteller/website-ersteller/den-inhaltsbaum-strukturieren.md)).
- Eine Seite pro Leistung, nicht eine Seite pro Suchbegriff. Suchmaschinen und Besucher bevorzugen beide eine klare
  Seite.
- Alte Seiten werden in den Papierkorb verschoben oder versteckt, nicht dem Verfall überlassen ([Archivieren und der Papierkorb](../../kunden/alltag/archivieren-und-der-papierkorb.md)).

## Arbeitsblatt

Übernehmen Sie diese Tabelle in Ihr [Website-Briefing](../vorlagen/website-briefing-fuer-ein-lokales-unternehmen.md):

| Seitenname | Adresse | Klasse | Wer sie schreibt | Wer sie aktualisiert, wie oft | Fertig bis |
|---|---|---|---|---|---|
| Startseite | `/` | frontpage | | | |
| Leistungen | `/Services` | folder | | | |
| Öffnungszeiten und Standort | `/Visit-us` | Klasse für Öffnungszeiten | | | |
| Kontakt | `/Contact` | feedback_form | | | |
| | | | | | |

## Verwandte Seiten

- [Was die Website eines kleinen Unternehmens braucht](was-die-website-eines-kleinen-unternehmens-braucht.md)
- [Seitenübersicht und Inhaltsinventar](../../ersteller/website-ersteller/sitemap-und-inhaltsbestandsaufnahme.md)
- [Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
