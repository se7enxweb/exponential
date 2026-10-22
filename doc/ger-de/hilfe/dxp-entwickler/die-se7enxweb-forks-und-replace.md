---
title: Die se7enxweb-Forks und replace
audience: dxp-developer
category: dxp-development
order: 6
summary: Warum ein DXP-Projekt se7enxweb-Pakete unter Upstream-Namen installiert, wie replace von Composer Bundles von Drittanbietern weiter funktionieren lässt, wie Sie prüfen, welches Paket eine Klasse liefert, und die eine Regel - installieren Sie nie einen Fork und sein Upstream zusammen.
keywords: Forks, replace, se7enxweb, ibexa, ezsystems, netgen, Composer, composer why, Paketübersicht, Autoloader, Upstream, Vendor, forks, package map, autoloader, upstream, vendor
type: help
---
# Die se7enxweb-Forks und replace

Diese Seite richtet sich an Entwickler, die in `composer.lock` Pakete `se7enxweb/*` und im Code Namensräume `Ibexa\`
sehen und verstehen möchten, wie beides zusammenpasst. Sie erklärt die Forks, den Mechanismus `replace` und wie Sie Ihr
Projekt prüfen.

## Inhalt

1. [Warum es Forks gibt](#warum-es-forks-gibt)
2. [Welche DXP-Pakete Forks sind](#welche-dxp-pakete-forks-sind)
3. [Wie replace funktioniert](#wie-replace-funktioniert)
4. [Ihr Projekt prüfen](#ihr-projekt-prüfen)
5. [Die Regel: nie beide](#die-regel-nie-beide)
6. [Die Namensräume bleiben gleich](#die-namensräume-bleiben-gleich)

## Warum es Forks gibt

Die DXP führt die Linie eZ Platform und Ibexa OSS fort. Wo ein Upstream-Paket für diese Linie eine Änderung brauchte,
hat 7x es unter dem Vendor `se7enxweb` geforkt: Produktbenennung in der Administration, Build-Pfade, die mit
ausschließlich installierten se7enxweb-Paketen funktionieren, Kompatibilität mit PHP 8.4 und die Befehlsnamen
`exponential:` ([Forks der Plattformpakete und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)).
Wo keine Änderung nötig war, verlangt die DXP das Upstream-Paket unverändert.

## Welche DXP-Pakete Forks sind

| se7enxweb-Paket im Metapaket | Ersetzt | Was der Fork ändert |
|---|---|---|
| `se7enxweb/exponential-platform-dxp-core` | `ibexa/core` | Produktbenennung in Templates für Benutzer, Befehlsnamen `exponential:` ([Befehlsnamen](../../spezifikationen/6.0/platform-konsolenbefehle.md)) |
| `se7enxweb/admin-ui` | `ibexa/admin-ui`, `ezsystems/ezplatform-admin-ui` | Branding, Paketidentität, Build-Pfade ([Administrationsoberfläche der Plattform](../../funktionen/6.0/plattform-admin-ui-fork.md)) |
| `se7enxweb/admin-ui-assets` | `ibexa/admin-ui-assets`, `ezsystems/ezplatform-admin-ui-assets` | gemeinsame Admin-Assets unter dem Vendor se7enxweb |
| `se7enxweb/fieldtype-richtext` | `ibexa/fieldtype-richtext`, `ezsystems/ezplatform-richtext` | XSL-Stylesheets und Webpack-Alias innerhalb des Forks |
| `se7enxweb/system-info` | `ibexa/system-info`, `ezsystems/ez-support-tools` | Ansicht der Systeminformationen |
| `se7enxweb/layouts-core` | `netgen/layouts-core` | Getter, die Twig unter PHP 8.4 braucht ([Layouts auf der Plattform](../../funktionen/6.0/plattform-layouts-core-fork.md)) |

Jedes andere Paket `ibexa/*` im Metapaket ist das Upstream-Paket. Die vollständige Tabelle der Forks der Familie ist
die [Übersicht der Plattformpakete](../../spezifikationen/6.0/platform-paketzuordnung.md).

## Wie replace funktioniert

Ein Paket, das ein anderes unter `replace` aufführt, sagt Composer, dass es dieses Paket liefert. Composer installiert
dann nur den Fork, und jedes Paket, das den Upstream-Namen verlangt, wird von ihm befriedigt. Seit dem 12.04.2026
deklarieren die Forks `replace` mit dem Platzhalter `*`, sodass der Fork jede Versionsbedingung erfüllt, die ein anderes
Paket an den Upstream-Namen stellt. Eine Ausnahme, die Sie in einem installierten Projekt sehen können:
`se7enxweb/fieldtype-richtext` ersetzt `ibexa/fieldtype-richtext` mit `self.version` und `ezsystems/ezplatform-richtext`
mit `*` (seine `composer.json`)
([Schritt 1](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md#schritt-1-pakete-werden-vom-vendor-se7enxweb-installiert)).

Das Ergebnis: Ein Bundle eines Drittanbieters, das `ibexa/admin-ui` verlangt, installiert sich in einem DXP-Projekt und
verwendet `se7enxweb/admin-ui`.

## Ihr Projekt prüfen

```bash
composer why ibexa/admin-ui                              # expect: se7enxweb/admin-ui replaces it
composer why netgen/layouts-core                         # expect: se7enxweb/layouts-core
composer show | grep -E "^(ibexa|ezsystems|netgen)/"     # upstream packages actually installed
```

Wird ein Upstream-Paket, dessen Fork Sie haben, als installiert aufgeführt, verlangt etwas eine Version, die der Fork
nicht abdeckt, oder die Lock-Datei ist älter als der Fork. Aktualisieren Sie an einer Kopie des Projekts und lesen Sie
den Plan, den Composer ausgibt.

## Die Regel: nie beide

Ein Projekt darf nie einen Fork und sein Upstream zusammen installiert haben. Beide definieren dieselben Klassen in
denselben Namensräumen, und der Autoloader würde unvorhersehbar eine davon laden. Symptome sind Klassen, die Methoden
"verlieren", Admin-Builds, die Dateien aus `vendor/ibexa/...` statt aus `vendor/se7enxweb/...` lesen, und Fehler, die
nur bei manchen Anfragen auftreten
([Die se7enxweb-Forks, nie beide installieren](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md#schritt-1-pakete-werden-vom-vendor-se7enxweb-installiert)).

## Die Namensräume bleiben gleich

Die Forks behalten die PHP-Namensräume (`Ibexa\...`), Service-IDs und Twig-Namensräume des Upstream. Code, der für die
Upstream-API geschrieben wurde, funktioniert unverändert; nur die Paketnamen in Composer und einige Texte für Benutzer
unterscheiden sich. Deshalb nennen diese Seiten Pakete `ibexa/*` und Klassen `Ibexa\`: Sie sind echte Bezeichner,
nicht der Name des Produkts.

## Verwandte Seiten

- [Das Metapaket lesen](das-metapaket-lesen.md)
- [Zu den Forks beitragen](zu-den-forks-beitragen.md)
- [Forks der Plattformpakete, Umbenennungen von Befehlen und Versionsregeln](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
