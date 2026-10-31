---
title: "Kompatibilität angeben"
audience: designer, developer
category: theme-lifecycle
order: 80
summary: Nutzern und Werkzeugen sagen, mit welchen Versionen von Exponential und PHP ein Theme-Release funktioniert - require in composer.json, die Voraussetzungen im README, die Abhängigkeiten in extension.xml und die Release Notes - einheitlich.
keywords: Kompatibilität angeben, Voraussetzungen, composer require, PHP-Bedingung, Voraussetzungen im README, Release Notes, extension.xml, declare compatibility, requirements, php constraint, README requirements, release notes
type: help
---
# Kompatibilität angeben

Diese Seite ist für Designer, die ein Theme veröffentlichen. Die Kompatibilität steht an mehreren Stellen; sie müssen
übereinstimmen.

## Wohin sie gehört

| Ort | Gelesen von | Beispiel |
|---|---|---|
| `composer.json` `require.php` | Composer, das eine Installation auf einem anderen PHP verweigert | `"php": "^8.0"` |
| `composer.json` `require` / `suggest` | Composer | Erweiterungen, die das Theme braucht oder mit denen es besser funktioniert ([require und suggest](../erweiterungen/composer-require-und-suggest.md)) |
| `extension.xml` `<dependencies>` | Der Ladereihenfolge des Kernels | `<requires>` für eine Erweiterung ([Abhängigkeiten deklarieren](../erweiterungen/abhaengigkeiten-in-extension-xml.md)) |
| „Voraussetzungen“ im README | Menschen | „Exponential 6.0 (getestet mit 6.0.15), PHP 8.0 bis 8.5“ |
| Release Notes | Menschen, die aktualisieren | „Getestet mit ...“, wenn es sich geändert hat |
| Kompatibilitätsmatrix | Menschen, die eine Version wählen | [Eine Kompatibilitätsmatrix](kompatibilitaetsmatrix.md) |

## Schritt für Schritt bei einem Release

1. Nehmen Sie die Versionen aus Ihrem Upgrade-Test ([Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md)).
2. Aktualisieren Sie `require.php`, wenn sich der Bereich geändert hat. Die Mindestversion anzuheben, ist eine Änderung,
   die Nutzer bemerken müssen: Sagen Sie es in den Notes und überlegen Sie, ob sie eine höhere Stelle in der
   Versionsnummer verdient ([Eine Versionsnummer wählen](../veroeffentlichung/eine-versionsnummer-waehlen.md)).
3. Aktualisieren Sie das README und die Matrix.
4. Führen Sie `composer validate` aus.

## Verwandte Seiten

- [PHP-Versionskompatibilität](php-versionskompatibilitaet.md)
- [Inkompatible Änderungen](inkompatible-aenderungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
