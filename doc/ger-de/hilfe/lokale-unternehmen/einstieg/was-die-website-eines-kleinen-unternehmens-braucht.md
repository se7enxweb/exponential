---
title: Was die Website eines kleinen Unternehmens braucht
audience: local-business, businesses, customer
category: local-business
order: 11
summary: Die fünf Aufgaben der Website eines lokalen Unternehmens - gefunden werden, die Grundfragen beantworten, Vertrauen aufbauen, Kontakt annehmen und manchmal verkaufen - mit der Exponential-Funktion oder dem externen Dienst hinter jeder Aufgabe und dem, was Sie am Anfang weglassen können.
keywords: Website für kleine Unternehmen, lokales Unternehmen, Anforderungen, gefunden werden, Öffnungszeiten, Kontakt, Vertrauen, online verkaufen, Mindestumfang einer Website, small business website, local business, requirements, be found, opening hours, sell online
type: help
---
# Was die Website eines kleinen Unternehmens braucht

Diese Seite ist für Inhaber eines kleinen Unternehmens, die entscheiden, was die Website leisten muss. Die meisten
lokalen Websites erfüllen fünf Aufgaben. Bringen Sie die ersten drei in Ordnung, bevor Sie für die letzten beiden Geld
ausgeben.

## Inhalt

1. [Die fünf Aufgaben](#die-fünf-aufgaben)
2. [Aufgabe 1: gefunden werden](#aufgabe-1-gefunden-werden)
3. [Aufgabe 2: die Grundfragen beantworten](#aufgabe-2-die-grundfragen-beantworten)
4. [Aufgabe 3: Vertrauen aufbauen](#aufgabe-3-vertrauen-aufbauen)
5. [Aufgabe 4: Kontakt annehmen](#aufgabe-4-kontakt-annehmen)
6. [Aufgabe 5: verkaufen oder buchen](#aufgabe-5-verkaufen-oder-buchen)
7. [Was Sie am Anfang weglassen können](#was-sie-am-anfang-weglassen-können)
8. [Checkliste](#checkliste)

## Die fünf Aufgaben

| Aufgabe | Frage eines Besuchers | Eingebaut oder Erweiterung | Externer Dienst |
|---|---|---|---|
| Gefunden werden | „Gibt es eine Bäckerei in meiner Nähe?“ | URL-Aliase, `xrowmetadata`, `bcgooglesitemaps`, `robots.txt` | ein Unternehmenseintrag auf Karten- und Suchplattformen |
| Die Grundfragen beantworten | „Haben sie gerade geöffnet? Wo sind sie?“ | gewöhnliche Seiten, `ezgmaplocation`, ein Karten-Block | Kartenkacheln (Google mit Schlüssel oder OpenStreetMap) |
| Vertrauen aufbauen | „Sind sie gut?“ | Fotos, Teamseite, Sternebewertungen (`ezstarrating`) | Rezensionen auf externen Plattformen |
| Kontakt annehmen | „Kann ich eine Frage stellen?“ | Formulare mit dem Informationssammler, `recaptcha` oder `hcaptcha` | der CAPTCHA-Anbieter |
| Verkaufen oder buchen | „Kann ich online bestellen oder buchen?“ | der eingebaute Shop, `ezpaypal` | Zahlungsanbieter; ein Buchungsdienst für Termine |

## Aufgabe 1: gefunden werden

Die meisten neuen Kunden eines lokalen Unternehmens finden es über eine Suche auf dem Smartphone. Zwei Dinge
entscheiden, ob sie Sie finden:

- **Ihr Eintrag auf Karten- und Suchplattformen.** Er wird auf diesen Plattformen eingerichtet, nicht auf Ihrer
  Website. Siehe [Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md).
- **Ihre eigene Website.** Sie sollte einen klaren Titel pro Seite haben, Ihren Ort und Ihre Branche im Text, lesbare
  Adressen und eine Sitemap. Exponential macht von selbst lesbare Adressen aus Seitennamen
  ([URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)); der Rest steht in
  [Grundlagen der lokalen Suchmaschinenoptimierung](../gefunden-werden/grundlagen-der-lokalen-suchmaschinenoptimierung.md).

## Aufgabe 2: die Grundfragen beantworten

Besucher wollen fünf Fakten in Sekunden: was Sie tun, wo Sie sind, wann Sie geöffnet haben, wie man Sie erreicht und
was es kostet. Stellen Sie sie auf die Startseite oder einen Klick entfernt:

- **Adresse und Karte**: [Ihren Standort auf einer Karte zeigen](../karten-und-oeffnungszeiten/ihren-standort-auf-einer-karte-zeigen.md).
- **Öffnungszeiten**: Exponential hat keinen Datentyp für Öffnungszeiten; die
  [Seite zu Öffnungszeiten](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md) zeigt drei Wege, sie
  abzubilden.
- **Telefon und E-Mail**: als Text, mit der Telefonnummer als Link, den Smartphones wählen können.
- **Preise oder eine Speisekarte**: eine Seite, eine Tabelle oder Produkte in einem [kleinen Katalog](../einfacher-shop/ein-kleiner-katalog.md).

## Aufgabe 3: Vertrauen aufbauen

Echte Fotos Ihrer Räume und Ihrer Arbeit, die Namen (oder Vornamen) des Teams, wenn es einverstanden ist, wie lange Sie
schon im Geschäft sind, Zertifikate und Mitgliedschaften. Exponential speichert Bilder und erzeugt automatisch
Varianten in passender Größe ([Fotos, die verkaufen](../marketing/fotos-die-verkaufen.md)). Sternebewertungen auf Seiten
sind mit `ezstarrating` möglich, geschriebene Rezensionen sind aber nicht eingebaut ([Bewertungen und Rezensionen](../kontakt-und-formulare/bewertungen-und-rezensionen.md)).

## Aufgabe 4: Kontakt annehmen

Ein Kontaktformular mit dem Informationssammler speichert jede Nachricht und schickt sie Ihnen per E-Mail
([Ein Kontaktformular für Ihr Unternehmen](../kontakt-und-formulare/ein-kontaktformular-fuer-ihr-unternehmen.md)). Schützen Sie es
vor Spam ([Spam stoppen](../kontakt-und-formulare/spam-stoppen.md)). Eine Telefonnummer und eine E-Mail-Adresse auf jeder
Seite sind für viele Menschen immer noch der schnellste Weg.

## Aufgabe 5: verkaufen oder buchen

- **Waren, Gutscheine oder Geschenkkarten verkaufen**: Das erledigt der im Kernel eingebaute Shop, mit PayPal über
  `ezpaypal` ([Ein einfacher Shop](../einfacher-shop/README.md)).
- **Termine, Tische, Kurse mit Plätzen**: Im Repository wurde kein Buchungsmodul gefunden. Die meisten kleinen
  Unternehmen verlinken einen Buchungsdienst ([Was einen externen Dienst braucht](../budget/was-einen-externen-dienst-braucht.md)).

## Was Sie am Anfang weglassen können

| Weglassen | Später ergänzen, wenn |
|---|---|
| Einen Shop | Sie Produkte haben, die Menschen aus der Ferne kaufen wollen |
| Einen Newsletter | Sie eine Liste von Kunden haben, die von Ihnen hören wollen |
| Eine zweite Sprache | Besucher aus dem Ausland Fragen stellen, die Sie auf der Website beantworten könnten |
| Einen Blog oder Neuigkeiten | Sie jeden Monat etwas schreiben können |

Mit Exponential können Sie alles später ergänzen, ohne die Website neu zu bauen: Ein Shop besteht aus ein paar
Inhaltsklassen und Einstellungen, eine Sprache aus einem Siteaccess und Übersetzungen ([Eine Sprache hinzufügen](../../kunden/wachstum/eine-sprache-hinzufuegen.md)).

## Checkliste

- [ ] Die fünf Fakten (was, wo, wann, Kontakt, Preis) sind entschieden.
- [ ] Sie wissen, welche der fünf Aufgaben Ihre erste Version erfüllt.
- [ ] Externe Dienste, die Sie brauchen, stehen auf einer Liste mit einer verantwortlichen Person.
- [ ] Die Entscheidungen stehen im [Website-Briefing](../vorlagen/website-briefing-fuer-ein-lokales-unternehmen.md).

## Verwandte Seiten

- [Ihre Seiten planen](ihre-seiten-planen.md)
- [Was Sie bauen können](../../ersteller/website-ersteller/was-sie-bauen-koennen.md)
- [Onlineshops und kleine Läden](../../unternehmen/shops/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
