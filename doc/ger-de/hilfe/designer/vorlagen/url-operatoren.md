---
title: URL-Operatoren
audience: designer, developer
category: templates
order: 240
summary: Links und Asset-URLs bauen, die unter jedem Siteaccess und Installationspfad funktionieren - ezurl für Links der Website, ezroot für Pfade ab der Wurzel, ezdesign und ezimage für Design-Dateien, der Anführungszeichen-Parameter (double, single, no) und vollständige Server-URLs, dazu exturl und ezsys.
keywords: ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, Links, URL, Anführungszeichen, vollständige URL, absolute URL, Präfix des Siteaccess, index.php, kanonischer Link, links, url, quotes, full url, absolute url, siteaccess prefix, canonical link
type: help
---
# URL-Operatoren

Diese Seite ist für Designer, die Links schreiben. Schreiben Sie nie `/news/article` oder `/extension/mytheme/...` von Hand: Die
Website kann unter einem Pfadpräfix, mit einem Siteaccess in der URL oder auf einem anderen Host laufen. Die URL-Operatoren aus
`kernel/common/ezurloperator.php` bauen korrekte URLs.

## Inhalt

1. [Die Operatoren](#die-operatoren)
2. [Anführungszeichen](#anführungszeichen)
3. [Vollständige URLs](#vollständige-urls)
4. [Beispiele](#beispiele)
5. [ezsys und exturl](#ezsys-und-exturl)

## Die Operatoren

| Operator | Für | Fügt den Pfad des Siteaccess hinzu? |
|---|---|---|
| `ezurl` | Links zu Seiten der Website (`'/content/search'`, der `url_alias` eines Knotens) | ja |
| `ezroot` | Pfade ab der Wurzel der Installation (Speicherdateien in `var/...`, URLs von Bildaliasen) | nein |
| `ezdesign` | jede Datei innerhalb eines Designs, über die Kaskade gefunden (`'stylesheets/print.css'`) | nein |
| `ezimage` | eine Datei im Ordner `images/` eines Designs, über die Kaskade gefunden | nein |

## Anführungszeichen

Standardmäßig geben die Operatoren die URL **mit doppelten Anführungszeichen** zurück, sodass Sie das Attribut ohne
Anführungszeichen schreiben:

```tpl
<a href={$node.url_alias|ezurl}>...</a>
```

Der erste Parameter ändert das: `'single'` für einfache Anführungszeichen, `'no'` für keine:

```tpl
<a href="{$node.url_alias|ezurl( 'no' )}#comments">...</a>
srcset="{$img.url|ezroot( 'no' )} 480w"
```

Mit `'no'` sind Sie für die Anführungszeichen um das Attribut verantwortlich.

## Vollständige URLs

Der zweite Parameter `'full'` fügt Schema und Host hinzu (aus `eZSys::serverURL()`), für kanonische Links, Open-Graph-Tags und
Mails:

```tpl
<link rel="canonical" href={$node.url_alias|ezurl( 'double', 'full' )}>
<meta property="og:url" content={$node.url_alias|ezurl( 'double', 'full' )}>
```

## Beispiele

```tpl
<a href={'/'|ezurl}>{'Home'|i18n( 'design/mytheme' )}</a>
<a href={'/user/login'|ezurl}>{'Sign in'|i18n( 'design/mytheme' )}</a>
<form action={'/content/search'|ezurl} method="get">...</form>
<img src={'logo.svg'|ezimage} alt="{$site.title|wash}">
<img src={$node.data_map.image.content['medium'].url|ezroot} alt="...">
<link rel="manifest" href={'images/favicon/site.webmanifest'|ezdesign}>
```

Ein Link zu einem Knoten: `$node.url_alias|ezurl`. Ein Link zu einer View mit Parametern:
`concat( $node.url_alias, '/(offset)/', $offset )|ezurl`.

## ezsys und exturl

- `ezsys( 'wwwdir' )`, `ezsys( 'indexdir' )`, `ezsys( 'indexfile' )`, `ezsys( 'sitedir' )`, `ezsys( 'querystring' )` geben
  Fakten über die Installation und die Anfrage zurück (die Attribute, die `eZSys::attributes()` listet). Ein anderer Name ist
  eine Template-Warnung.
- `exturl` registriert eine externe URL in der URL-Tabelle des Kernels (der Liste der Links, die die Administration prüfen
  kann) und gibt sie zurück. Für einen gewöhnlichen externen Link schreiben Sie die URL direkt (`href="https://..."`) und
  maskieren sie, wenn sie aus Inhalten stammt.

## Verwandte Seiten

- [Bilder in einem Design](../theme-design/bilder-in-einem-design.md)
- [Wash und Maskierung](wash-und-maskierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
