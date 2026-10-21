---
title: SVG und Bildformate
audience: website-creator, admin
category: website-creation
order: 360
summary: Welche Bildformate die Redaktion hochladen darf, welche Formate Seiten anzeigen, wie SVG bereinigt und sicher ausgeliefert wird, und wann Sie auf einer Website SVG, WebP, AVIF, PNG oder JPEG verwenden.
keywords: SVG, WebP, AVIF, PNG, JPEG, GIF, Bildformate, Logo, Symbole, Bereinigung, Website-Ersteller, image formats, AllowedMIMETypes, SanitizeSVG, expSvgSanitizer, ConversionRules, AllowedOutputFormat, logo, icons, website creator
type: help
---
# SVG und Bildformate

Diese Seite richtet sich an Website-Ersteller, die Bildformate für eine Website wählen. Sie erklärt, was Exponential
annimmt, was es umwandelt und wie SVG-Bilder sicher gemacht werden.

## Inhalt

1. [Was die Redaktion hochladen darf](#was-die-redaktion-hochladen-darf)
2. [Was Seiten anzeigen](#was-seiten-anzeigen)
3. [SVG](#svg)
4. [Welches Format wofür](#welches-format-wofür)
5. [Die Einstellungen ändern](#die-einstellungen-ändern)

## Was die Redaktion hochladen darf

`settings/image.ini [ImageUploadSettings]`:

| Einstellung | Ausgeliefert |
|---|---|
| `ValidateMIMEType` | `enabled`: die Datei muss ihrem Inhalt nach einer der erlaubten Typen sein |
| `AllowedMIMETypes[]` | `image/jpeg`, `image/png`, `image/gif`, `image/webp`, `image/avif`, `image/tiff`, `image/bmp`, `image/svg+xml` |
| `SanitizeSVG` | `enabled` |

## Was Seiten anzeigen

Bildvarianten werden gemäß `[MIMETypeSettings] ConversionRules[]` umgewandelt: WebP und GIF in PNG, alles andere in
JPEG, und `[OutputSettings] AllowedOutputFormat[]` (JPEG, PNG, WebP, GIF) listet auf, was Seiten anzeigen dürfen. Die
Variante `original` ist die hochgeladene Datei. Animierte GIFs bleiben unskaliert (`[AnimatedGIF]`). Siehe
[Bilder und Bildvarianten](bilder-und-bildvarianten.md).

## SVG

Seit dem 15. Oktober 2026 nimmt der Bild-Datentyp auf jeder Website SVG an (`.svg` und `.svgz`)
([SVG-Bilder](../../../bc/6.0/svg-bilder.md)):

- Ein SVG kann Skripte enthalten, deshalb wird es **niemals so gespeichert, wie es hochgeladen wurde**: Der Bereiniger des
  Kernels, `expSvgSanitizer`, behält nur Elemente und Attribute, die als sicher bekannt sind (keine Skripte, keine
  Ereignisattribute, kein `foreignObject`, keine externen Verweise), und die gespeicherte Datei ist seine Ausgabe.
- Jeder Upload-Weg bereinigt: das Bearbeitungsformular, Uploads, Mehrfach-Upload, der Rich-Text-Editor, WebDAV,
  Paketimporte.
- Jede Bildvariante eines SVG ist die bereinigte Datei selbst; SVGs werden nie skaliert.
- Die Datei wird mit Headern ausgeliefert, die verhindern, dass sie beim direkten Öffnen etwas ausführt; liefern Sie sie
  mit den auf der SVG-Seite aufgeführten Headern aus, wenn Sie eigene Webserver-Regeln verwenden.

Verwenden Sie SVG für Logos, Symbole und Diagramme, nicht für Fotos.

## Welches Format wofür

| Bild | Format |
|---|---|
| Logo, Symbole, einfache Illustrationen | SVG |
| Fotos | JPEG oder hochgeladenes WebP/AVIF (Bildvarianten werden zu JPEG, sofern Sie die Regeln nicht ändern) |
| Screenshots, Bilder mit Text oder Transparenz | PNG |
| Animationen | wo möglich ein kurzes Video statt GIF |

## Die Einstellungen ändern

SVG vollständig ablehnen:

```ini
# settings/override/image.ini.append.php
[ImageUploadSettings]
SanitizeSVG=disabled
```

(`disabled` lehnt SVG ab, unabhängig davon, was `AllowedMIMETypes[]` sagt, sodass nie ein unbereinigtes SVG gespeichert
wird.)

Behalten Sie WebP-Bildvarianten als WebP statt PNG, indem Sie `ConversionRules[]` ändern; testen Sie das Ergebnis in den
Browsern, die Ihre Besucher verwenden, und leeren Sie dann die erzeugten Bildvarianten
(`php bin/php/cache.php imagealias clear --allow-root-user`).

## Verwandte Seiten

- [Bilder und Bildvarianten](bilder-und-bildvarianten.md)
- [Schutzmechanismen für Weiterleitungen, Abfragen und Bildvarianten](../../../bc/6.0/schutz-fuer-weiterleitungs-query-und-bildaliase.md)
- [Sicherheit für Anwendungen](../anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
