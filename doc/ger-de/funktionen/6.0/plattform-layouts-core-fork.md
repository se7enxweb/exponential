# Layouts auf der Plattform: der Fork von layouts-core

Diese Seite ist für Entwickler von Nexus- und DXP-Projekten auf der Symfony-basierten Plattform, die Exponential Layouts unter
PHP 8.4 oder 8.5 verwenden. Der Fork von se7enxweb des Layouts-Kerns lässt Block-Templates, die Parameter lesen, auf
diesen PHP-Versionen funktionieren.

| | |
|---|---|
| Repository | `se7enxweb/layouts-core` (Fork des Upstream-Layouts-Kerns, `netgen/layouts-core`) |
| Neuestes Tag | `2.0.0-se7enx.1` (19.04.2026) |
| Upstream-Releases in der Historie | `1.4.10` (06.09.2024), `1.4.11`, `1.4.12`, `1.4.13` (17.12.2025), `2.0.0` (25.02.2026) |
| Chronik | [layouts-core](../../geschichte/oekosystem/layouts-core.md): 450 Änderungen, eine davon vom Team von se7enxweb |
| Gegenstück in Exponential 6 | die Extensions `explayouts`, `explayouts_ui`, `explayouts_ui_api`; siehe [Exponential Layouts](../../bc/6.0/layouts.md) |

## Was es ist

Layouts ist der Seitenbaukasten der Plattformseite der Familie. Redakteure setzen eine Seite in einem visuellen Editor
aus Zonen und Blöcken zusammen, und Regeln entscheiden, welches Layout für welche Anfrage gilt. Das Kernpaket enthält das
Modell (Layouts, Zonen, Blöcke, Sammlungen, Regeln, Ziele, Bedingungen), das Parametersystem, die Blockdefinitionen und
die Ausgabe. Nexus-Websites (siehe [Nexus-Starter](plattform-nexus-starter.md)) bauen über die Layouts-Pakete
2.0.x darauf auf: Ibexa-Integration, Site-API-Integration, die Standardblöcke, die Layout-Administration unter
`/nglayouts/admin` und die Editor-App unter `/nglayouts/app`.

Das Konzept ist dasselbe wie in Exponential Layouts, das auf den Legacy-Kernel übertragen wurde: Layout-Typen mit
benannten Zonen, Blöcke mit Parametern, Sammlungen von Elementen und Regeln mit Zielen, die das Layout einer Seite
auswählen. Wer das eine kennt, kennt das andere.

## Verwenden

In einem Nexus- oder DXP-Projekt installieren Sie es nicht von Hand: Das Metapaket verlangt es. In Ihrem eigenen Projekt:

```bash
composer require se7enxweb/layouts-core
composer why netgen/layouts-core     # shows se7enxweb/layouts-core as the provider
```

Block-Templates lesen Parameter wie gewohnt. Diese Zeile stammt aus dem Template des Galerieblocks des Nexus-Starters
(`templates/nglayouts/themes/app/block/gallery/sushi_bar.html.twig`); es ist die Art von Zugriff, die unter PHP 8.4 ohne
den Fork scheitert:

```twig
{% if block.parameter('infinite_loop').value %}data-loop="true"{% endif %}
```

## Was der Fork ändert

Version 2.0 des Kerns verwendet durchgehend Sprachmittel von PHP 8.4. Eines davon bricht den Zugriff aus Templates:

- PHP 8.4 führte asymmetrische Sichtbarkeit ein (`private(set)`). Twig liest ein Attribut eines Objekts, indem es
  `ReflectionProperty::isInitialized()` prüft, bevor es nach einer Getter-Methode sucht. Bei Eigenschaften, die mit
  `private(set)` deklariert sind, scheitert diese Prüfung, Twig erreicht den Getter nie, und eine Seite, die in einem
  Block-Template einen Parameter liest, bricht mit „Call to undefined method“ ab.
- Der Fork (Commit `3ec0262b2`, 19.04.2026) fügt `Netgen\Layouts\Parameters\Parameter` ausdrückliche Getter-Methoden
  hinzu: `getName()`, `getParameterDefinition()`, `getValue()`, `isEmpty()` und `getValueObject()`, sodass Twig sie über
  die gewöhnliche Methodensuche auflöst. Derselbe Commit benennt das Paket in `se7enxweb/layouts-core` um und fügt
  `replace: netgen/layouts-core` hinzu.

Wegen der Deklaration `replace` wird jedes Layouts-Paket, das `netgen/layouts-core` verlangt, vom Fork
erfüllt. Das Metapaket `se7enxweb/exponential-platform-dxp` verlangt den Fork direkt (Commit „require
se7enxweb/layouts-core fork for PHP 8.4 private(set) Twig compatibility“, 19.04.2026). Das Tag `2.0.0-se7enx.1`
kennzeichnet den Fork auf Grundlage von Upstream `2.0.0`.

## Was Upstream in 2.0 geändert hat (übernommene Historie)

Zwischen Oktober 2025 und Februar 2026: Constructor Property Promotion und fehlende Typangaben (28.10.2025),
Deprecations in Symfony und Doctrine entfernt (10.11.2025), PHPUnit-Annotationen durch Attribute ersetzt (11.11.2025),
Werte der Kern-API auf Property Hooks umgestellt (26.11.2025), View-System und Konfigurationswerte auf Property Hooks
(27./28.11.2025), UUIDs auf die Komponente Symfony Uid umgestellt (13.12.2025) und Uuid v7 in Tests (31.12.2025).
Projekte, die Layout-Klassen erweitern, sollten die Upgrade-Hinweise von Upstream für 2.0 lesen, bevor sie von 1.4
wechseln.

## Grenzen

- Der Fork braucht PHP 8.4 (`^8.4`).
- Layouts 1.4 unter PHP unter 8.4 braucht den Fork nicht.

## Verwandte Seiten

- [Exponential Layouts](../../bc/6.0/layouts.md)
- Plattformfunktionen: [Administrationsoberfläche](plattform-admin-ui-fork.md), [DXP-Skeleton](plattform-dxp-skeleton.md), [Nexus-Starter](plattform-nexus-starter.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [Site-Bundles](plattform-site-bundles.md), [SQLite für Exponential Platform](plattform-sqlite-installation.md), [Legacy Bridge](legacy-bridge.md), [Datenbankverwaltung AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [Paketkarte der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md), [Bundle der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [Übersicht über das Ökosystem](../../geschichte/oekosystem.md), [Monate des Ökosystems](../../geschichte/oekosystem/monate/2026-04.md), [Änderungsverzeichnis](../../geschichte/aenderungsverzeichnis/README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
