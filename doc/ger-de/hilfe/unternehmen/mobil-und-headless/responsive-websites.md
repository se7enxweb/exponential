---
title: Responsive Websites
audience: businesses, website-creator, sales-marketing
category: business-solutions
order: 20
summary: Was eine Exponential-Website auf Telefonen und Tablets gut funktionieren lässt - responsive Designs, Bildgrößen und -formate, schnelle Seiten, barrierefreie Formulare und ein Testplan, den ein Unternehmen selbst ausführen kann.
keywords: responsiv, mobile Website, Telefone, Tablets, responsives Design, srcset, Bildaliase, WebP, Leistung, Barrierefreiheit, Zoom, sevenx_themes_simple, Unternehmen, responsive, mobile website, phones, tablets, responsive design, image aliases, performance, accessibility, zoom, business
type: help
---
# Responsive Websites

Diese Seite ist für Inhaber und Marketingverantwortliche, die möchten, dass ihre Website und ihr Shop auf Telefonen und
Tablets gut funktionieren. Sie erklärt, welche Teile Exponential bereitstellt, welche zu Ihrem Design gehören und wie
Sie eine Website vor dem Start selbst testen. Es ist der erste Schritt jedes mobilen Plans: Eine App ersetzt keine
Website, die auf Telefonen funktioniert ([Mobile Strategie](mobile-strategie.md)).

## Inhalt

1. [Was „responsiv“ für Ihr Unternehmen bedeutet](#was-responsiv-für-ihr-unternehmen-bedeutet)
2. [Das Design entscheidet](#das-design-entscheidet)
3. [Bilder, die auf kleine Bildschirme passen](#bilder-die-auf-kleine-bildschirme-passen)
4. [Schnelle Seiten über mobile Verbindungen](#schnelle-seiten-über-mobile-verbindungen)
5. [Formulare, Bestellablauf und Barrierefreiheit](#formulare-bestellablauf-und-barrierefreiheit)
6. [Ein Testplan, den Sie selbst ausführen können](#ein-testplan-den-sie-selbst-ausführen-können)

## Was „responsiv“ für Ihr Unternehmen bedeutet

Eine responsive Website ordnet sich für den Bildschirm neu an: eine Spalte auf einem Telefon, mehr auf einem Desktop,
Menüs, die sich per Berührung öffnen, Text, der ohne Zoomen lesbar bleibt. Für ein Unternehmen bedeutet das, dass Kunden
ein Produkt finden, die Einzelheiten lesen und einen Kauf oder ein Formular abschließen können, auf welchem Gerät auch
immer sie es halten.

## Das Design entscheidet

In Exponential entscheidet das Design (Templates und Stylesheets), wie Seiten aussehen, nicht die Inhalte. Die Inhalte
bleiben dieselben; das Design passt sie dem Bildschirm an.

| Design | Responsiv? | Quelle |
|---|---|---|
| `sevenx_themes_simple`, das öffentliche Standarddesign | Beschrieben als „the light, responsive public design of the default installation“ (das leichte, responsive öffentliche Design der Standardinstallation), mit dem Stylesheet `main.css` und „its responsive companion `main.res.css`“ (seinem responsiven Begleiter `main.res.css`) | [sevenx_themes_simple](../../../../eng-us/features/6.0/extensions/sevenx_themes_simple.md) |
| Andere mitgelieferte Themes | Siehe ihre eigenen Seiten | [Ein Design oder Theme wählen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md#the-themes-that-ship) |
| Ihr eigenes Design | Was auch immer Ihre Designer bauen | [Ein Design oder Theme wählen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md#your-own-design) |

Wenn Sie ein Design in Auftrag geben, schreiben Sie „funktioniert auf Telefonen ab 320 px Breite, ohne horizontales
Scrollen und mit Zoom auf 200 %“ in das Briefing. Die Hinweise zur Barrierefreiheit für Website-Ersteller verlangen
genau das: „layouts that work at 200% zoom and on narrow screens, no horizontal scrolling“ (Layouts, die mit Zoom auf
200 % und auf schmalen Bildschirmen funktionieren, kein horizontales Scrollen,
[Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls)).

## Bilder, die auf kleine Bildschirme passen

Exponential speichert das hochgeladene Bild einmal und erzeugt **Bildaliase**: Varianten in festgelegten Größen. Ein
Design kann mit `srcset` zwei oder drei Aliase desselben Bildes anbieten, sodass ein Telefon eine kleine Datei und ein
großer Bildschirm eine große herunterlädt
([Responsive Bilder](../../../../eng-us/help/creators/website-creators/images-and-image-variations.md#responsive-images)).

| Einstellung (ausgeliefert) | Bedeutung |
|---|---|
| Redakteure dürfen hochladen | JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG |
| Seiten dürfen zeigen | JPEG, PNG, WebP, GIF |
| SVG | Beim Hochladen bereinigt und nie skaliert |

Quellen: [Bilder und Bildvarianten](../../../../eng-us/help/creators/website-creators/images-and-image-variations.md#converters-formats-and-quality)
und [SVG und Bildformate](../../../../eng-us/help/creators/website-creators/svg-and-image-formats.md).

> **Tipp**
> Fragen Sie Ihre Designer, welche Aliase das Design definiert, und bitten Sie Redakteure, Bilder hochzuladen, die groß
> genug für den größten Alias sind. Kleine Uploads wirken auf großen Bildschirmen unscharf; die Aliase können keine
> Details hinzufügen.

## Schnelle Seiten über mobile Verbindungen

Mobile Besucher haben oft langsamere Verbindungen. Exponential hat mehrere Cache-Ebenen, und die Anleitung für
Website-Ersteller listet „the levers, in order of effect“ (die Hebel, nach Wirkung geordnet,
[Leistung und Caching für Websites](../../../../eng-us/help/creators/website-creators/performance-and-caching.md)). Für Telefone relevant:

- Gepackte Stylesheets und Skripte über ezjscore, sodass eine Seite weniger Dateien lädt
  ([Stylesheets und Skripte für Ihre Website](../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md)).
- HTTP-Caching und CDNs für anonyme Besucher
  ([HTTP-Caching, Proxys und CDNs](../../../../eng-us/help/creators/website-creators/http-caching-and-cdns.md)).
- Unter Exponential Velocity ein Antwortcache und ein optionaler Navigationscache im Browser für wiederholte Besuche
  ([Offline und wiederholte Besuche](offline-und-wiederholte-besuche.md)).

## Formulare, Bestellablauf und Barrierefreiheit

An Formularen geben mobile Besucher auf. Die Hinweise zur Barrierefreiheit für Formulare helfen auch auf Telefonen:

- jedes Eingabefeld hat eine sichtbare Beschriftung;
- Pflichtfelder sind im Text gekennzeichnet;
- Fehler werden neben dem Feld angezeigt und oben zusammengefasst;
- CAPTCHAs haben eine barrierefreie Alternative (beide mitgelieferten CAPTCHA-Dienste bieten eine).

Quelle: [Barrierefreiheit für Website-Ersteller, Forms](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#forms).
Für den Bestellablauf des Shops siehe [Warenkorb und Bestellablauf](../shops/warenkorb-und-bestellablauf.md).

## Ein Testplan, den Sie selbst ausführen können

Verwenden Sie ein echtes Telefon, nicht nur ein schmales Desktop-Fenster.

- [ ] Die Startseite lädt und ist ohne Zoomen lesbar.
- [ ] Das Menü öffnet und schließt sich per Berührung; jeder Hauptbereich ist erreichbar.
- [ ] Die Suche funktioniert, und ihre Ergebnisse sind lesbar.
- [ ] Eine Produkt- oder Artikelseite zeigt Bilder in einer sinnvollen Größe, ohne horizontales Scrollen.
- [ ] Ein Formular (Kontakt, Anmeldung) lässt sich ausfüllen und abschicken, und seine Fehlermeldungen sind
      verständlich.
- [ ] Für einen Shop: in den Warenkorb legen, Menge ändern, zur Kasse gehen, die Bestätigungsmail bekommen.
- [ ] Telefonnummern und Adressen lassen sich antippen.
- [ ] Die Website funktioniert auf einem Desktop mit Textzoom auf 200 %.
- [ ] Jede Sprache und jede Marke besteht denselben Test
      ([Checkliste Mehrsprachigkeit](../../../../eng-us/help/businesses/multilingual/multilingual-checklist.md),
      [Checkliste für mehrere Marken](../../../../eng-us/help/businesses/multisite/multi-brand-checklist.md)).

## Verwandte Seiten

- [Mobile Strategie](mobile-strategie.md)
- [Checkliste mobil](checkliste-mobil.md)
- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Bilder und Bildvarianten](../../../../eng-us/help/creators/website-creators/images-and-image-variations.md)
- [Leistung und Caching für Websites](../../../../eng-us/help/creators/website-creators/performance-and-caching.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
