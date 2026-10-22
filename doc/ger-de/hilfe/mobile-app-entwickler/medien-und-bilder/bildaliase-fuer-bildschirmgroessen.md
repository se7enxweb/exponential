---
title: Bildaliase für Bildschirmgrößen
audience: mobile-app-developer, app-creator, developer, designer
category: mobile-apps
order: 520
summary: Bilder in der richtigen Größe für Telefone und Tablets erhalten - die mitgelieferten Bildaliase (reference, small, tiny, medium, large, rss), sie mit expimage::aliases auflisten, mit expimage::url die URL eines Alias anfordern, in image.ini Aliase für die Zellen und Pixeldichten der App ergänzen, und Caching.
keywords: Bildalias, Bildvariante, image.ini, AliasList, reference, small, tiny, medium, large, rss, expimage::url, expimage::urls, expimage::aliases, absolute_url, Pixeldichte, Retina, Cache, Mobile App, image alias, image variation, screen density, retina, cache, mobile app
type: help
---
# Bildaliase für Bildschirmgrößen

Diese Seite richtet sich an App-Entwickler, die Bilder möchten, die zum Bildschirm passen, und an den Designer oder
Entwickler, der auf dem Server Größen für die App ergänzt. Exponential nennt eine benannte Größe einen **Bildalias** und
erzeugt jeden bei Bedarf aus dem Original.

## Inhalt

1. [Die mitgelieferten Aliase](#die-mitgelieferten-aliase)
2. [Die URL eines Alias anfordern](#die-url-eines-alias-anfordern)
3. [Aliase für die App](#aliase-für-die-app)
4. [Pixeldichten und die Wahl einer Größe](#pixeldichten-und-die-wahl-einer-größe)
5. [Caching](#caching)

## Die mitgelieferten Aliase

Aus `settings/image.ini [AliasSettings] AliasList[]` ([Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md#die-mitgelieferten-aliasse)):

| Alias | Regel |
|---|---|
| `reference` | auf 600 x 600 verkleinert; die Grundlage der anderen |
| `small` | passt in 100 x 100 |
| `tiny` | passt in 30 x 30 |
| `medium` | passt in 200 x 200 |
| `large` | passt in 300 x 300 |
| `rss` | 88 x 31, für Feeds |

Themes und Websites ergänzen eigene. Um zu sehen, was Ihre Installation hat, rufen Sie den öffentlichen Dienst auf:

```bash
curl -s 'https://example.com/ezjscore/call/expimage::aliases?ContentType=json'
```

Er antwortet für jeden Alias mit Name, Breite, Höhe, Referenz und Filtern.

## Die URL eines Alias anfordern

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expimage::url::<node id>::image::medium?ContentType=json'
```

Argumente: Knoten-ID, der Bezeichner des Bildfelds, der Name des Alias. `data` enthält `url`, `absolute_url`, `width`,
`height`, `mime_type`, `filename` und `filesize`. Die Aliasdatei wird erzeugt, wenn sie fehlt. Ein unbekannter Alias
antwortet mit `404` „No image alias“; ein Bild ohne diese Variante antwortet ebenfalls mit `404`. Der Dienst braucht
`content/read` auf dem Knoten.

`expimage::urls::<node id>::<field identifier>` antwortet mit jedem konfigurierten Alias auf einmal. Er erzeugt jeden
Alias, der noch nicht existiert, und leistet beim ersten Aufruf für ein neues Bild daher mehr Arbeit, als Sie vielleicht
brauchen; verwenden Sie lieber `url` für die eine oder zwei Größen, die ein Bildschirm zeigt.

Die REST-API selbst liefert keine Alias-URLs ([Bilder in REST-Antworten](bilder-in-rest-antworten.md)). Ein
Listenbildschirm mit einem Bild je Zeile braucht daher einen `expimage::url`-Aufruf je Element oder ein Backend bzw.
eine Serverfunktion, die die URL der Listenantwort hinzufügt ([Ein Backend für Ihre App](../anmeldung/ein-backend-fuer-ihre-app.md)).

## Aliase für die App

Die mitgelieferten Größen wurden für Webseiten gemacht. Bitten Sie das Website-Team um Aliase, die nach ihrem Einsatz in
der App benannt sind, in der `image.ini.append.php` einer Design- oder App-Erweiterung:

```ini
# extension/<your extension>/settings/image.ini.append.php
[AliasSettings]
AliasList[]=app_cell
AliasList[]=app_hero

[app_cell]
Reference=reference
Filters[]
Filters[]=geometry/scaledownonly=480;480

[app_hero]
Reference=
Filters[]
Filters[]=geometry/scalewidthdownonly=1600
```

Das Muster und der Rat, Aliase nach ihrem Zweck zu benennen, stammen aus
[Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md#eigene-aliasse-hinzufügen);
die Aliasnamen und Größen oben sind Beispiele. Nach einer Änderung müssen auf dem Server die Caches geleert werden
([Nach dem Ändern eines Alias](../../ersteller/website-ersteller/bilder-und-bildvarianten.md#nach-der-änderung-eines-alias)).

## Pixeldichten und die Wahl einer Größe

- Wählen Sie den Alias, dessen Breite mindestens der Breite der Ansicht in Pixeln mal dem Skalierungsfaktor des
  Bildschirms entspricht (eine Zelle von 160 Punkten auf einem 3x-Bildschirm braucht 480 Pixel).
- `scaledownonly` vergrößert ein kleines Original nie, daher kann ein Alias kleiner als seine Regel sein; lesen Sie
  `width` und `height` aus der Antwort, statt sie anzunehmen.
- Halten Sie die Zahl der Aliase klein: Jeder ist eine Datei je Bild auf dem Server.

## Caching

Aliasdateien werden einmal erzeugt und dann als Dateien ausgeliefert. Speichern Sie Bilder in der App mit dem Bildcache
Ihrer Plattform anhand ihrer URL. Speichern Sie das Ergebnis von `expimage::url` mit dem Element, und wenn sich
`dateModified` des Elements ändert, fordern Sie die URL erneut an und entfernen Sie das alte Bild aus dem Cache, falls
die URL gleich geblieben ist ([Caching in der App](../offline-und-leistung/caching-in-der-app.md)).

## Verwandte Seiten

- [Bilder in REST-Antworten](bilder-in-rest-antworten.md)
- [SVG und Bildformate](../../ersteller/website-ersteller/svg-und-bildformate.md)
- [expimage-Dienste (Referenz)](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#bilder-und-aliase-expimage-18-dienste)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
