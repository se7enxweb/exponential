---
title: "PHP-Versionskompatibilität"
audience: designer, developer
category: theme-lifecycle
order: 70
summary: Den PHP-Code eines Themes (Operatoren, Module, Klassen) auf jeder PHP-Version lauffähig halten, die Exponential unterstützt - PHP 8.0 ist die älteste, PHP 7.4 ohne Support nutzbar - mit php -l pro Version, was typischerweise bricht und wie Sie den Bereich in composer.json angeben.
keywords: PHP-Version, PHP 8.0, PHP 8.5, Kompatibilität, php -l, Abkündigungen, composer require php, Operatoren, Module, PHP version, compatibility, deprecations, operators, modules
type: help
---
# PHP-Versionskompatibilität

Diese Seite ist für Designer, deren Theme PHP enthält: Template-Operatoren, Modul-Views, Klassen. Ein Theme nur aus
Templates und Stylesheets ist von PHP-Versionen nicht direkt betroffen.

## Die Fakten

- Das Changelog von 6.0.15 gibt an, dass **PHP 8.0 die älteste unterstützte Version** ist (5. Oktober 2026), und ab dem 29. Oktober 2026 ist PHP 7.4 ohne Support nutzbar ([Warum Exponential PHP 7.4 unterstützt](../../warum-exponential-php-7-4-unterstuetzt.md)).
- Die `composer.json` im Wurzelverzeichnis erlaubt PHP ab 8.0 (`^8.0 || ^8.1 || ... ^8.5` und spätere Minor-Versionen).
- Die veröffentlichten Themes geben eigene Bereiche an, zum Beispiel `sevenx_themes_media`
  `^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4` und `cjw_themes_jumper` `>=8.0`.

## Schritt für Schritt: ein Release prüfen

1. Listen Sie die PHP-Dateien des Themes auf: `git ls-files '*.php'`.
2. Führen Sie die Syntaxprüfung mit jeder PHP-Version aus, die Sie angeben, wenn sie installiert ist:

   ```bash
   for f in $(git ls-files '*.php'); do php8.0 -l "$f"; php8.5 -l "$f"; done
   ```

   (Die Namen der Programme hängen von Ihrem System ab.) Erwartet: `No syntax errors detected` für jede Datei und Version.
3. Rufen Sie die Seiten des Themes auf der ältesten und der neuesten angegebenen PHP-Version auf und lesen Sie das
   Fehlerlog auf Abkündigungshinweise.
4. Schreiben Sie den getesteten Bereich in `require.php` von `composer.json` und in die Kompatibilitätsmatrix
   ([Eine Kompatibilitätsmatrix](kompatibilitaetsmatrix.md)).

## Was typischerweise bricht

| Änderung in neuerem PHP | In einem Theme |
|---|---|
| Abgekündigte dynamische Eigenschaften (8.2) | Eine Operator-Klasse, die nicht deklarierte Eigenschaften setzt: deklarieren Sie sie |
| Strengere Typen in eingebauten Funktionen | `null` übergeben, wo eine Zeichenkette erwartet wird: vorher umwandeln oder prüfen |
| Entfernte Funktionen | Alte Helfer, die aus früherem Code kopiert wurden |

## Verwandte Seiten

- [Kompatibilität angeben](kompatibilitaet-angeben.md)
- [Template-Operatoren in einem Theme](../erweiterungen/template-operatoren-in-einem-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
