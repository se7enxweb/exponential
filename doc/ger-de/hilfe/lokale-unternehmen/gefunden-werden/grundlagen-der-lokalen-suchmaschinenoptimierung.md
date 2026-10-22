---
title: Grundlagen der lokalen Suchmaschinenoptimierung
audience: local-business, businesses, customer, editor
category: local-business
order: 21
summary: Was Suchmaschinen von der Website eines lokalen Unternehmens brauchen - Name, Adresse und Telefon überall gleich, Ort und Branche in Titeln und Text, eine Seite pro Leistung, eine Kontaktseite, schnelle Seiten auf Smartphones - mit der Exponential-Funktion hinter jedem Schritt und einer Checkliste.
keywords: lokale Suchmaschinenoptimierung, lokale Suche, Name Adresse Telefon, NAP, Titel, Beschreibung, Leistungsseiten, Ort, Standortseite, Sitemap, mobil, Ladezeit, Checkliste, local SEO, local search, title, description, service pages, page speed, checklist
type: help
---
# Grundlagen der lokalen Suchmaschinenoptimierung

Diese Seite ist für Inhaber oder Redakteure der Website eines lokalen Unternehmens, die wollen, dass die Website
erscheint, wenn Menschen in der Nähe nach dem suchen, was das Unternehmen tut. Sie ist eine kurze Liste von
Gewohnheiten. Keine davon braucht Programmierung, sobald die Website eingerichtet ist.

## Inhalt

1. [Überall derselbe Name, dieselbe Adresse und dieselbe Telefonnummer](#überall-derselbe-name-dieselbe-adresse-und-dieselbe-telefonnummer)
2. [Sagen Sie, was Sie tun und wo](#sagen-sie-was-sie-tun-und-wo)
3. [Eine Seite pro Leistung](#eine-seite-pro-leistung)
4. [Eine vollständige Kontakt- und Standortseite](#eine-vollständige-kontakt--und-standortseite)
5. [Technische Grundlagen, die Ihr Ersteller einmal einrichtet](#technische-grundlagen-die-ihr-ersteller-einmal-einrichtet)
6. [Gewohnheiten, die es funktionsfähig halten](#gewohnheiten-die-es-funktionsfähig-halten)
7. [Checkliste](#checkliste)

## Überall derselbe Name, dieselbe Adresse und dieselbe Telefonnummer

Suchmaschinen vergleichen, was Ihre Website sagt, mit dem, was Kartenplattformen, Verzeichnisse und Ihre Profile in
sozialen Medien sagen. Schreiben Sie den Namen des Unternehmens, die Straßenadresse und die Telefonnummer überall
**genau gleich**: dieselben Abkürzungen, dasselbe Telefonformat. Halten Sie sie auf der Website in einem
Inhaltsobjekt, das in der Fußzeile und auf der Kontaktseite angezeigt wird
([Ihre Seiten planen](../einstieg/ihre-seiten-planen.md#schreiben-sie-jede-tatsache-nur-einmal)), damit es nur eine Stelle zum
Ändern gibt.

## Sagen Sie, was Sie tun und wo

- Der **Seitentitel** der Startseite nennt Branche und Ort: „Bäckerei in Flussdorf - Beispielbäckerei“, nicht nur
  „Startseite“. Titel stammen aus dem Namen der Seite oder aus dem Feld von `xrowmetadata`, wenn es ausgefüllt ist
  ([Titel und Beschreibungen mit xrowmetadata](titel-und-beschreibungen-mit-xrowmetadata.md)).
- Der **erste Absatz** der Startseite sagt dasselbe in einem ganzen Satz.
- Wenn Sie mehrere Orte bedienen (ein Installateur, eine mobile Friseurin), listen Sie sie als Text auf einer Seite
  „Unser Einzugsgebiet“ auf. Legen Sie nicht für jeden Ort eine fast identische Seite an: Das wirkt auf Suchmaschinen
  wie Spam.

## Eine Seite pro Leistung

Eine Seite namens „Heizungsreparatur“ mit einer klaren Beschreibung, Preisen oder einer Preisspanne, Fotos und einem
Kontaktlink wird öfter gefunden als eine lange Seite „Leistungen“. In Exponential ist jede Leistung ein Artikel in
einem Ordner „Leistungen“, und ihr Name wird automatisch zur Adresse (`/Services/Boiler-repair`)
([URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)).

## Eine vollständige Kontakt- und Standortseite

| Element | Wie |
|---|---|
| Adresse als Text | im gemeinsamen Kontaktobjekt |
| Karte | [Ihren Standort auf einer Karte zeigen](../karten-und-oeffnungszeiten/ihren-standort-auf-einer-karte-zeigen.md) |
| Öffnungszeiten, einschließlich Feiertagszeiten | [Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md) |
| Telefon als wählbarer Link | ein Link mit `tel:` im Rich-Text-Editor oder im Template |
| Anfahrt: Parken, öffentliche Verkehrsmittel, Barrierefreiheit des Eingangs | Text |
| Kontaktformular | [Ein Kontaktformular für Ihr Unternehmen](../kontakt-und-formulare/ein-kontaktformular-fuer-ihr-unternehmen.md) |

## Technische Grundlagen, die Ihr Ersteller einmal einrichtet

| Grundlage | Exponential-Funktion | Lesen |
|---|---|---|
| Lesbare Adressen | URL-Aliase, eingebaut | [Sprechende URLs und verschobene Seiten](sprechende-urls-und-verschobene-seiten.md) |
| Titel und Beschreibung pro Seite | `xrowmetadata` oder eigene Felder | [Seitenmetadaten und Teilen in sozialen Medien](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md) |
| Eine Sitemap | Cronjob von `bcgooglesitemaps` | [Sitemaps und robots.txt](sitemaps-und-robots-txt.md) |
| `robots.txt` mit der Adresse der Sitemap | im Web-Wurzelverzeichnis mitgeliefert | [robots.txt für Ihre Website](../../ersteller/website-ersteller/robots-txt.md) |
| HTTPS und ein einziger Hostname | Einrichtung des Webservers | [Live gehen: Hosts und HTTPS](../../ersteller/website-ersteller/livegang-hosts-und-https.md) |
| Funktioniert auf Smartphones | ein responsives Design; das öffentliche Standarddesign ist responsiv | [Mobile Besucher](../marketing/mobile-besucher.md) |
| Schnelle Seiten | Caching, Velocity | [Leistung und Caching](../../ersteller/website-ersteller/leistung-und-caching.md) |
| Strukturierte Daten | teilweise; siehe die Seite | [Strukturierte Daten: was es gibt](strukturierte-daten-was-es-gibt.md) |

## Gewohnheiten, die es funktionsfähig halten

- Aktualisieren Sie Feiertagszeiten **vor** dem Feiertag, auf der Website und auf den Plattformen.
- Ergänzen Sie ab und zu eine Neuigkeit oder ein Angebot; eine Website, die sich ändert, ist eine Website, die lebt.
- Geben Sie jedem Foto einen kurzen Alternativtext, der es beschreibt („Frisches Roggenbrot auf der Theke“); das hilft
  Besuchern mit Screenreadern und Suchmaschinen ([Alternativtext für Bilder](../../verwaltung/barrierefreiheit/alternativtext-fuer-bilder.md)).
- Verwenden Sie Linktexte, die sagen, wohin sie führen („Öffnungszeiten“, nicht „hier klicken“).
- Sehen Sie sich an, wonach Besucher auf Ihrer eigenen Website suchen ([Website-Suche und wonach Besucher suchen](../../kunden/alltag/website-suche-und-wonach-besucher-suchen.md)).

## Checkliste

- [ ] Name, Adresse und Telefon auf der Website und auf jeder Plattform gleich geschrieben.
- [ ] Der Titel der Startseite nennt Branche und Ort.
- [ ] Eine Seite pro Hauptleistung, jede mit eigenem Titel und eigener Beschreibung.
- [ ] Kontaktseite mit Adresse, Karte, Öffnungszeiten, Telefonlink, Anfahrt und einem Formular.
- [ ] Sitemap per Cronjob erzeugt und in der `robots.txt` angemeldet.
- [ ] Überall HTTPS, ein einziger Hostname.
- [ ] Website auf einem Smartphone getestet.
- [ ] Feiertagszeiten für das Jahr geplant ([Feiertags- und Saisonöffnungszeiten](../karten-und-oeffnungszeiten/feiertags-und-saisonoeffnungszeiten.md)).

## Verwandte Seiten

- [SEO-Grundlagen für Exponential-Websites](../../ersteller/website-ersteller/seo-grundlagen.md)
- [SEO-Prüfungen](../../kunden/alltag/seo-pruefungen.md)
- [Monatliche SEO-Checkliste](../../kunden/alltag/monatliche-seo-checkliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
