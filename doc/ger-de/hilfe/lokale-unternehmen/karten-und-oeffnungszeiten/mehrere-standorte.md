---
title: Mehrere Standorte
audience: local-business, businesses, customer, website-creator
category: local-business
order: 34
summary: Zwei oder mehr Filialen, einen Marktstand oder einen mobilen Dienst auf einer Exponential-Website zeigen - ein Standortobjekt pro Adresse mit eigenen Öffnungszeiten und eigenem Kartenpunkt, ein Ordner für Standorte, eine Liste der nächstgelegenen Standorte mit dem Filter von ezgmaplocation und wann eine zweite Website sinnvoll ist.
keywords: mehrere Standorte, Filialen, Marktstand, mobiler Dienst, Einzugsgebiet, Ordner für Standorte, ezgmaplocation, Entfernungsfilter, Multisite, lokales Unternehmen, several locations, branches, multiple locations, market stall, mobile service, area served, distance filter, local business
type: help
---
# Mehrere Standorte

Diese Seite ist für lokale Unternehmen mit mehr als einer Adresse: ein zweites Geschäft, ein Stand samstags auf dem
Markt, eine Praxis mit zwei Räumen in verschiedenen Orten oder ein Dienst, der zum Kunden kommt. Die Regel aus
[Ihre Seiten planen](../einstieg/ihre-seiten-planen.md#schreiben-sie-jede-tatsache-nur-einmal) gilt weiterhin: Jede Tatsache wird
einmal geschrieben, jetzt einmal **pro Standort**.

## Inhalt

1. [Ein Objekt pro Standort](#ein-objekt-pro-standort)
2. [Eine Seite mit den Standorten](#eine-seite-mit-den-standorten)
3. [Nächstgelegener Standort](#nächstgelegener-standort)
4. [Mobile Dienste und Einzugsgebiete](#mobile-dienste-und-einzugsgebiete)
5. [Eine Website oder mehrere](#eine-website-oder-mehrere)
6. [Checkliste](#checkliste)

## Ein Objekt pro Standort

Der Ersteller legt eine Klasse **Standort** an (oder erweitert die Klasse für Öffnungszeiten aus
[Öffnungszeiten ohne besonderen Datentyp](oeffnungszeiten-ohne-besonderen-datentyp.md#möglichkeit-2-eine-kleine-klasse-für-öffnungszeiten)):

| Feld | Datentyp | Hinweis |
|---|---|---|
| Name | Textzeile | „Beispielbäckerei Bahnhofstraße“ |
| Adresse | Textabschnitt | wie auf dem Schild |
| Telefon | Textzeile | die eigene Nummer des Standorts |
| Kartenpunkt | Google Maps Location (`ezgmaplocation`) | braucht einen API-Schlüssel für die Karte ([Ihren Standort auf einer Karte zeigen](ihren-standort-auf-einer-karte-zeigen.md)) |
| Reguläre Öffnungszeiten | Matrix | eine Zeile pro Tag |
| Hinweis | Textabschnitt | Ausnahmen für diesen Standort |
| Foto | Bild | die Fassade, damit man sie wiedererkennt |
| Leitung oder Ansprechperson | Textzeile | eine Funktion, nicht unbedingt ein Personenname |

Jeder Standort hat dann eine eigene Seite und Adresse (`/Locations/Station-Road`), was gut für die lokale Suche ist:
eine Seite pro Adresse.

## Eine Seite mit den Standorten

Ein **Ordner** „Standorte“, der die Standortobjekte enthält, listet sie automatisch auf. Der Ersteller kann die Liste
mit Name, Adresse, heutigen Öffnungszeiten und einem Link zu jeder Seite anzeigen. Auf der Startseite verweist ein
kurzer Block „Unsere Standorte“ darauf.

Der Karten-Block des Layout-Editors zeigt einen Punkt pro Block; für eine Seite mit allen Standorten auf einer Karte
schreibt der Ersteller ein kleines Template aus den Feldern von `ezgmaplocation`. Das ist Template-Arbeit, keine
Einstellung.

## Nächstgelegener Standort

`ezgmaplocation` liefert einen erweiterten Attributfilter mit, `ezgmlLocationFilter`, der Objekte innerhalb einer
Entfernung zu einem Punkt holt und nach Entfernung sortiert (`extension/ezgmaplocation/classes/ezgmllocationfilter.php`,
[ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md#beispiel-objekte-in-der-nähe-eines-punktes-finden)). Damit kann
ein Ersteller „Standorte in Ihrer Nähe“ aus einer Postleitzahl oder der Position des Browsers bauen. Das ist eine
Aufgabe für Entwickler; die Position des Besuchers muss mit Einwilligung abgefragt werden.

## Mobile Dienste und Einzugsgebiete

Ein Installateur, eine mobile Friseurin oder ein Reinigungsdienst hat einen Sitz und ein Gebiet, kein Ladengeschäft.
Statt einer Kartennadel:

- eine Seite „Unser Einzugsgebiet“, die Orte und Stadtteile als Text auflistet;
- die Adresse des Sitzes nur, wenn Kunden dorthin kommen dürfen;
- auf Kartenplattformen ein Einzugsgebiet statt einer öffentlichen Adresse (auf der Plattform eingestellt).

Ein Marktstand, der an bestimmten Tagen da ist: ein Standortobjekt mit der Adresse des Marktes und nur diesen Tagen bei
den Öffnungszeiten.

## Eine Website oder mehrere

| Situation | Empfehlung |
|---|---|
| Dasselbe Unternehmen, derselbe Name, zwei oder drei Adressen | eine Website, ein Ordner „Standorte“ |
| Verschiedene Marken oder sehr unterschiedliche Angebote pro Standort | erwägen Sie getrennte Websites in einer Installation ([Eine Website hinzufügen](../../kunden/wachstum/eine-website-hinzufuegen.md)) |
| Standorte in verschiedenen Ländern und Sprachen | eine Website mit Sprachen oder eine pro Land ([Mehrsprachig für Urlaubsregionen](../marketing/mehrsprachig-fuer-urlaubsregionen.md)) |

Exponential betreibt mehrere Websites aus einer Installation ([Multisite-Marken](../../unternehmen/multisite/README.md)),
aber für die meisten kleinen Unternehmen ist eine Website einfacher und günstiger zu betreiben.

## Checkliste

- [ ] Ein Standortobjekt pro Adresse, jedes mit Öffnungszeiten, Telefon und Kartenpunkt.
- [ ] Jeder Standort hat eine eigene Seite.
- [ ] Die Fußzeile zeigt den Hauptstandort oder einen Link zu allen Standorten.
- [ ] Jeder Standort hat einen eigenen Eintrag auf Kartenplattformen, passend zu seiner Seite.
- [ ] Feiertagshinweise werden pro Standort gesetzt.

## Verwandte Seiten

- [Ihren Standort auf einer Karte zeigen](ihren-standort-auf-einer-karte-zeigen.md)
- [Feiertags- und Saisonöffnungszeiten](feiertags-und-saisonoeffnungszeiten.md)
- [Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
