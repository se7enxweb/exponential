---
title: Karten und eingebettete Inhalte
audience: government, project-manager, editor, developer
category: public-sector
order: 240
summary: Für öffentliche Stellen, die Standorte, Videos und andere eingebettete Dienste zeigen - was die Erweiterung ezgmaplocation tut (ein Standort-Datentyp mit Google Maps), die Wirkung von Karten und Einbettungen anderer Dienste auf den Datenschutz, und Alternativen mit zwei Klicks oder selbst gehostet.
keywords: Karten, Google Maps, ezgmaplocation, GMapsKey, Standort-Datentyp, Einbettungen, Video, iframe, Drittanbieter, Datenschutz, Einwilligung, Zwei-Klick-Lösung, selbst gehostet, CSP frame-src, Barrierefreiheit, öffentlicher Sektor, maps, embeds, third party, privacy, consent
type: help
---
# Karten und eingebettete Inhalte

Diese Seite richtet sich an Projektleitungen, Redakteure und Entwickler. Öffentliche Websites zeigen Standorte von
Dienststellen, Veranstaltungsorte und Erklärvideos. Jede Karte und jedes Video, das von einem anderen Dienst geladen
wird, teilt diesem Dienst etwas über den Besucher mit. Exponential enthält keinen eigenen Kartendienst; die Erweiterung
`ezgmaplocation` nutzt Google Maps ([ezgmaplocation](../../../../eng-us/features/6.0/extensions/ezgmaplocation.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [ezgmaplocation](#ezgmaplocation)
2. [Einbettungen allgemein](#einbettungen-allgemein)
3. [Weniger eingreifende Möglichkeiten](#weniger-eingreifende-möglichkeiten)
4. [Verwandte Seiten](#verwandte-seiten)

## ezgmaplocation

- Ein Datentyp, der Breitengrad, Längengrad und eine Adresse an einem Objekt speichert; Redakteure finden den Punkt,
  indem sie im Bearbeitungsformular unter einer Google-Karte eine Adresse eingeben.
- Braucht einen Google-Maps-Schlüssel in `site.ini [SiteSettings] GMapsKey`.
- Ein Fetch-Filter kann Objekte nach ihrer Entfernung zu einem Punkt sortieren.
- Datenschutz: Die Adresssuchen der Redakteure gehen an Google; wenn Ihre Templates Besuchern Karten zeigen, laden
  auch deren Browser Google Maps.

Nur diese Funktionen sind dokumentiert; Exponential hat keine GIS-Funktionen.

## Einbettungen allgemein

Videoplattformen, Beiträge aus sozialen Medien, Karten und Formulare anderer Dienste, eingebettet mit einem iframe oder
einem Skript:

- Der Dienst erhält die Adresse und die Browserdaten des Besuchers und setzt oft eigene Speicherung;
- vor dem Laden ist in der Regel eine Einwilligung nötig (siehe „Cookie-Banner und Einwilligung“);
- die Content Security Policy muss den Host (`frame-src`, `script-src`) auf diesem Siteaccess erlauben;
- die Barrierefreiheit hängt vom Player oder Widget des Dienstes ab.

## Weniger eingreifende Möglichkeiten

| Bedarf | Möglichkeit |
|---|---|
| Zeigen, wo eine Dienststelle ist | ein statisches Kartenbild, das Sie selbst hosten, die Adresse als Text und ein Link „in einem Kartendienst öffnen“ |
| Ein Video zeigen | die Datei mit dem Medien-Datentyp (HTML5-Video) und Untertiteln in Ihrem Design hosten; oder ein Platzhalter mit zwei Klicks |
| Beiträge aus sozialen Medien zeigen | ein Link zum Konto; oder ein Screenshot mit Text |
| Wegbeschreibung | ein Link zum Kartendienst mit der Adresse; der Besucher entscheidet, dorthin zu gehen |

Text ist ohnehin immer nötig: Adresse und Wegbeschreibung in Worten dienen Screenreader-Nutzern und der Suche.

## Verwandte Seiten

- [ezgmaplocation](../../../../eng-us/features/6.0/extensions/ezgmaplocation.md)
- [Erweiterungen von Drittanbietern und Datenschutz](erweiterungen-von-drittanbietern.md)
- [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
