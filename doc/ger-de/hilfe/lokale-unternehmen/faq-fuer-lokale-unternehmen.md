---
title: FAQ für lokale Unternehmen
audience: local-business, businesses, customer
category: local-business
order: 2
summary: Kurze Antworten auf die Fragen, die Inhaber von Geschäften, Restaurants, Handwerksbetrieben, Friseursalons und Praxen zu einer Website mit Exponential stellen - Kosten, Selbermachen, Karten, Öffnungszeiten, lokale Suche, Formulare und Spam, Rezensionen, Verkaufen, Buchen, Newsletter, Sprachen und Betrieb -, jeweils mit einem Verweis auf die ausführliche Seite.
keywords: FAQ, häufige Fragen, Fragen, lokales Unternehmen, kleines Unternehmen, Kosten, Öffnungszeiten, Karte, lokale Suchmaschinenoptimierung, Buchung, Rezensionen, Shop, Newsletter, Sprachen, Hosting, local business, local SEO, opening hours
type: help
---
# FAQ für lokale Unternehmen

Diese Seite ist für Inhaber und Mitarbeitende lokaler Unternehmen, die eine schnelle Antwort suchen. Jede Antwort
verweist auf die Seite mit den Einzelheiten.

## Inhalt

1. [Kosten und Wahl](#kosten-und-wahl)
2. [Gefunden werden](#gefunden-werden)
3. [Karten und Öffnungszeiten](#karten-und-öffnungszeiten)
4. [Kontakt, Formulare und Rezensionen](#kontakt-formulare-und-rezensionen)
5. [Verkaufen und Buchen](#verkaufen-und-buchen)
6. [Marketing](#marketing)
7. [Die Website betreiben](#die-website-betreiben)

## Kosten und Wahl

**Gibt es eine Lizenzgebühr?**
Nein. Exponential ist freie Software unter der GNU GPL, Version 2 oder später. Sie bezahlen für Arbeit, Hosting und
externe Dienste ([Budget](budget/README.md)).

**Was wird es kosten?**
Das hängt von der Arbeit und den Diensten ab, die Sie wählen. [Das Kostenbild](budget/das-kostenbild.md) führt
jeden Posten auf, und das [durchgerechnete Beispiel](budget/durchgerechnetes-beispiel-website-eines-lokalen-unternehmens.md) zeigt die
Rechnung mit Beispielannahmen. Füllen Sie das [Budget-Arbeitsblatt](budget/budget-arbeitsblatt.md) mit echten Angeboten
aus.

**Kann ich sie selbst bauen?**
Seiten bearbeiten, ja, nach einer kurzen Einführung. Installieren, Einrichten und Aktualisieren braucht jemanden mit
technischem Wissen ([Selbst machen oder Hilfe holen](einstieg/selbst-machen-oder-hilfe-holen.md)).

**Wo finde ich jemanden, der sie baut?**
Einen Freiberufler aus der Region oder einen Exponential-Partner ([Mit einem lokalen Partner oder Freiberufler arbeiten](einstieg/mit-einem-lokalen-partner-oder-freiberufler-arbeiten.md)).

## Gefunden werden

**Steht meine Website auf der ersten Seite der Suchergebnisse?**
Das kann niemand versprechen. Exponential gibt Ihnen lesbare Adressen, Titel und Beschreibungen pro Seite, Sitemaps
und eine `robots.txt`; der Rest sind gute Inhalte und korrekte Einträge ([Grundlagen der lokalen Suchmaschinenoptimierung](gefunden-werden/grundlagen-der-lokalen-suchmaschinenoptimierung.md)).

**Aktualisiert Exponential mein Profil auf Karten- und Suchplattformen?**
Nein. Dieser Eintrag wird auf der jeweiligen Plattform gepflegt ([Unternehmenseinträge außerhalb von Exponential](gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)).

**Gibt die Website strukturierte Daten vom Typ LocalBusiness aus?**
Nicht von Haus aus. Manche Themes geben Brotkrumenpfade, Artikel und Rezepte aus; ein LocalBusiness-Block ist
Template-Arbeit ([Strukturierte Daten: was es gibt](gefunden-werden/strukturierte-daten-was-es-gibt.md)).

## Karten und Öffnungszeiten

**Kann ich eine Karte zeigen?**
Ja: mit dem Feld `ezgmaplocation` (Google Maps, braucht einen API-Schlüssel) oder dem Karten-Block des Layout-Editors
(Google mit Schlüssel, OpenStreetMap ohne) ([Ihren Standort auf einer Karte zeigen](karten-und-oeffnungszeiten/ihren-standort-auf-einer-karte-zeigen.md)).

**Gibt es ein Feld für Öffnungszeiten?**
Nein. Öffnungszeiten werden mit einem Textblock, einer kleinen Klasse mit einer Matrix oder einer Tabelle abgebildet
([Öffnungszeiten ohne besonderen Datentyp](karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md)).

**Werden Feiertagszeiten automatisch angezeigt?**
Nein. Sie bearbeiten sie von Hand; ein Hinweis lässt sich zeitlich planen, wenn Ihr Administrator datumsabhängiges
Veröffentlichen eingerichtet hat ([Feiertags- und Saisonöffnungszeiten](karten-und-oeffnungszeiten/feiertags-und-saisonoeffnungszeiten.md)).

**Wir haben zwei Geschäfte. Eine Website oder zwei?**
Meist eine, mit einer Seite pro Standort ([Mehrere Standorte](karten-und-oeffnungszeiten/mehrere-standorte.md)).

## Kontakt, Formulare und Rezensionen

**Können Kunden mir Nachrichten schicken?**
Ja, mit Formularen auf Basis des Informationssammlers; die Nachrichten werden gespeichert und Ihnen per E-Mail
geschickt ([Ein Kontaktformular für Ihr Unternehmen](kontakt-und-formulare/ein-kontaktformular-fuer-ihr-unternehmen.md)).

**Wie stoppe ich Spam?**
Formular-Token sind standardmäßig aktiv; ergänzen Sie reCAPTCHA oder hCaptcha, beide brauchen Schlüssel vom Anbieter
([Spam stoppen](kontakt-und-formulare/spam-stoppen.md)).

**Können Kunden auf meiner Website Rezensionen hinterlassen?**
Sternebewertungen mit `ezstarrating` und Kommentare, ja; ein System für geschriebene Rezensionen, nein. Die meisten
Rezensionen stehen auf externen Plattformen ([Bewertungen und Rezensionen](kontakt-und-formulare/bewertungen-und-rezensionen.md)).

## Verkaufen und Buchen

**Kann ich online verkaufen?**
Ja, der Shop ist im Kernel eingebaut, mit PayPal über `ezpaypal` ([Ein einfacher Shop](einfacher-shop/README.md)).
Lesen Sie zuerst [Brauchen Sie einen Shop?](einfacher-shop/brauchen-sie-einen-shop.md).

**Können Kunden Termine oder Tische buchen?**
Nicht mit etwas Eingebautem. Verlinken Sie einen Buchungsdienst, oder verwenden Sie ein Anfrageformular und bestätigen
Sie telefonisch ([Was einen externen Dienst braucht](budget/was-einen-externen-dienst-braucht.md)).

**Kann mein Restaurant Essensbestellungen annehmen?**
Vorbestellungen zur Abholung, ja, über den Shop; Live-Bestellungen mit Zeitfenstern, nein
([Restaurantbestellungen und was nicht eingebaut ist](einfacher-shop/restaurantbestellungen-und-was-nicht-eingebaut-ist.md)).

**Kann ich Geschenkgutscheine verkaufen?**
Ja, als Produkte; Gutscheincodes an der Kasse sind nicht eingebaut ([Ein kleiner Katalog](einfacher-shop/ein-kleiner-katalog.md#gutscheine-und-geschenkkarten)).

**Ist es mit meiner Ladenkasse verbunden?**
Eine Kassenanbindung (POS) ist nicht eingebaut.

## Marketing

**Kann ich einen Newsletter versenden?**
Ja, `cjw_newsletter` wird mit Exponential ausgeliefert; Ihr Hoster muss E-Mails versenden können
([Newsletter für lokale Unternehmen](marketing/newsletter-fuer-lokale-unternehmen.md)).

**Kann die Website für Touristen auf Englisch und Deutsch sein?**
Ja, mit Übersetzungen und einer Adresse pro Sprache ([Mehrsprachig für Urlaubsregionen](marketing/mehrsprachig-fuer-urlaubsregionen.md)).

**Funktioniert sie auf Smartphones?**
Das öffentliche Standarddesign ist responsiv; testen Sie es selbst mit [Mobile Besucher](marketing/mobile-besucher.md).

## Die Website betreiben

**Wie viel Zeit kostet das pro Woche?**
Etwa fünfzehn Minuten für eine kleine Website ohne Shop ([Wöchentliche und monatliche Routine](im-betrieb/woechentliche-und-monatliche-routine.md)).

**Wer macht die Sicherungen und Updates?**
Ihr Hoster oder Partner; lassen Sie sich das einmal im Jahr nachweisen ([Sicherungen, Updates und Sicherheit](im-betrieb/sicherungen-updates-und-sicherheit.md)).

**Was, wenn mein Partner nicht mehr mit mir arbeitet?**
Halten Sie die Domain, ein Administratorkonto und die Konten bei externen Diensten auf den Namen des Unternehmens,
damit ein anderer Partner übernehmen kann ([Eine Website übernehmen](../kunden/partner-und-hoster/eine-website-uebernehmen.md)).

## Verwandte Seiten

- [Lokale Unternehmen: hier beginnen](lokale-unternehmen-hier-beginnen.md)
- [Glossar für lokale Unternehmen](glossar-fuer-lokale-unternehmen.md)
- [FAQ für Unternehmen](../unternehmen/faq-fuer-unternehmen.md)
- [FAQ für Kunden](../kunden/faq-fuer-kunden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
