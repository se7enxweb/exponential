---
title: "Eine Kompatibilitätsmatrix"
audience: designer, developer
category: theme-lifecycle
order: 60
summary: Eine Tabelle führen, welche Theme-Versionen mit welchen Versionen von Exponential, PHP, Server-Engine und Datenbank getestet wurden und welche Browser und Breiten Sie prüfen - wie Sie sie ehrlich füllen und wo Sie sie veröffentlichen.
keywords: Kompatibilitätsmatrix, getestete Versionen, Exponential-Versionen, PHP-Versionen, Browser, Velocity, FPM, Datenbank, Support-Tabelle, compatibility matrix, tested versions, browsers, database, support table
type: help
---
# Eine Kompatibilitätsmatrix

Diese Seite ist für Designer, die möchten, dass Nutzer auf einen Blick sehen, ob ein Theme zu ihrer Installation passt.

## Was Sie festhalten

| Dimension | Festzuhaltende Werte |
|---|---|
| Exponential | Die getesteten Releases oder der getestete Commit auf `main`, z. B. 6.0.14, 6.0.15 (Entwicklung) |
| PHP | Jede Version, auf der Sie das Theme ausgeführt haben (Exponential 6.0 unterstützt PHP 8.0 und später) |
| Server-Engine | Exponential Velocity, PHP-FPM mit Apache oder nginx, FrankenPHP, wenn Ihr Theme PHP-Code hat, der sich unterscheiden könnte |
| Datenbank | Nur wenn das Theme Tabellen oder Abfragen mitliefert |
| Browser und Breiten | Aktuelle Browser; 1440, 960 mit 200 % Zoom, 375 px |
| Modi | Hell, dunkel, rechts nach links, falls unterstützt |

## Eine Vorlage

| Theme | Exponential | PHP | Getestet auf |
|---|---|---|---|
| 1.0.25 | 6.0.15 (dev) | 8.3, 8.5 | Velocity, Apache + PHP-FPM |
| 1.0.24 | 6.0.14 | 8.1, 8.3 | Apache + PHP-FPM |

Schreiben Sie nur, was Sie getestet haben. „Sollte funktionieren“ gehört in eine eigene, deutlich gekennzeichnete Spalte
oder nirgendwohin.

## Schritt für Schritt

1. Führen Sie die Matrix in `doc/compatibility.md` des Themes oder im README.
2. Fügen Sie bei jedem Release beim Upgrade-Test eine Zeile hinzu ([Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md)).
3. Spiegeln Sie sie in `require.php` von `composer.json` und in den Voraussetzungen des README ([Kompatibilität angeben](kompatibilitaet-angeben.md)).

## Verwandte Seiten

- [PHP-Versionskompatibilität](php-versionskompatibilitaet.md)
- [Eine Support-Richtlinie für Ihr Theme](support-richtlinie-fuer-ihr-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
