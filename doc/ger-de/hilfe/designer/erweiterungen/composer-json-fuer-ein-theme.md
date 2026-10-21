---
title: "composer.json für ein Theme"
audience: designer, developer
category: theme-extensions
order: 300
summary: Die composer.json schreiben, die ein Theme mit composer require installierbar macht - Name, Beschreibung, Typ ezpublish-legacy-extension, Lizenz, Autoren, Support, require, suggest und der Name der Erweiterung - mit einem vollständigen Beispiel eines veröffentlichten Themes.
keywords: composer.json, Composer, Packagist, ezpublish-legacy-extension, type, extra, ezpublish-legacy-extension-name, Lizenz, Autoren, Support, composer validate, license, authors, support
type: help
---
# composer.json für ein Theme

Diese Seite ist für Designer, die möchten, dass Nutzer ihr Theme mit einem Befehl installieren:
`composer require yourvendor/mytheme`.

## Ein vollständiges Beispiel

`cjw_themes_jumper` veröffentlicht dies (nur bei `keywords` gekürzt):

```json
{
    "name": "se7enxweb/cjw_themes_jumper",
    "description": "CJW Themes Jumper: the design starter for Exponential, served by the starter siteaccesses (port of the Nexus v2 starter look)",
    "type": "ezpublish-legacy-extension",
    "keywords": [ "Exponential", "Exponential 6", "legacy extension", "design", "theme" ],
    "homepage": "https://github.com/se7enxweb/cjw_themes_jumper",
    "license": "GPL-2.0-or-later",
    "authors": [
        { "name": "7x", "homepage": "https://se7enx.com", "email": "info@se7enx.com", "role": "Author" }
    ],
    "support": {
        "issues": "https://github.com/se7enxweb/cjw_themes_jumper/issues",
        "source": "https://github.com/se7enxweb/cjw_themes_jumper"
    },
    "require": {
        "php": ">=8.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "cjw_themes_jumper"
    }
}
```

## Die Felder

| Feld | Warum es wichtig ist |
|---|---|
| `name` | `vendor/package`, kleingeschrieben. Es ist der Name, den Nutzer verlangen und Packagist listet |
| `description` | Wird auf Packagist und in Suchergebnissen angezeigt |
| `type: ezpublish-legacy-extension` | Lässt den Legacy-Installer das Paket unter `extension/` ablegen |
| `license` | Ein SPDX-Bezeichner, dieselbe Lizenz wie in der Datei `LICENSE` |
| `authors`, `homepage`, `support` | Wer es gemacht hat und wo man fragt |
| `require` | PHP-Versionen, das Installer-Plugin, Erweiterungen, ohne die das Theme nicht funktioniert |
| `suggest` | Erweiterungen, mit denen es besser funktioniert ([require und suggest](composer-require-und-suggest.md)) |
| `extra.ezpublish-legacy-extension-name` | Der Ordnername unter `extension/` ([Der Composer-Installer und der Ordnername](composer-installer-und-erweiterungsname.md)) |
| `version` | Meist weggelassen: Packagist liest die Version aus dem Tag. Wenn Sie es behalten, halten Sie es synchron |

## Schritt für Schritt

1. Legen Sie `composer.json` im Wurzelverzeichnis des Themes an und verwenden Sie das Beispiel mit Ihren Werten.
2. Prüfen Sie sie:

   ```bash
   composer validate composer.json
   ```

   Erwartet: `./composer.json is valid`. Warnungen über eine fehlende `version` oder eine Bedingung `*` können Sie lesen
   und akzeptieren oder beheben.
3. Committen Sie sie mit dem Release, das sie einführt.
4. Testen Sie die Installation, bevor Sie veröffentlichen ([Eine Composer-Installation testen](composer-installation-lokal-testen.md)).

## Verwandte Seiten

- [Ein Theme bei Packagist einreichen](../veroeffentlichung/bei-packagist-einreichen.md)
- [Composer-Paket oder .ezpkg](composer-oder-ezpkg.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
