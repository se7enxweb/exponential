# site-legacy-bundle: Geschichte des Plattform-Repositorys

Die Geschichte von `site-legacy-bundle`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Legacy Bridge und Site-Bundles). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 10 Änderungen von 2026-03-16 bis 2026-04-17, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Netgen Site Legacy Bundle: Verbindung zwischen dem neuen und dem Legacy-Kernel.

## Wie es mit Exponential zusammenhängt

Auf die Service-Namen von eZ Platform 3.3 portiert; die Copyright-Templates der Administration tragen die Marke Exponential.

## Was Anwender davon haben

Legacy-Seiten der Administration in Nexus zeigen die richtige Marke und funktionieren auf Platform 3.3.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/site-legacy-bundle
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 1 |
| Fehlerbehebungen | 7 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Releases | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-03-16 | v2.0.0 | `46a0468` | Update package name and description in composer.json |
| 2026-03-25 | v2.0.2 | `facbd9c` | use se7enxweb/site-bundle ^2.0 and se7enxweb/legacy-bridge ^3.0 for eZ Platform 3.3 compatibility |
| 2026-03-26 | v2.0.3 | `67e6451` | downgrade netgen/metadata-bundle to ^3.0 for eZ Platform 3.3, expand PHP to ^8.5 |
| 2026-03-26 | v2.0.4 | `825e30e` | port all Ibexa namespace/service refs to eZ Platform 3.3 equivalents |
| 2026-03-26 | v2.0.5 | `6cef564` | Fix ImageVariationPass: use named service IDs not FQCN for eZ Platform 3.3 |
| 2026-03-26 | v2.0.6 | `3943714` | Fix search service alias for eZ Platform container |
| 2026-03-26 | v2.0.7 | `3c93610` | Fix XMLText Site API service wiring |
| 2026-03-26 | v2.0.8 | `c46cd8f` | Fix XMLText Site API namespaces |
| 2026-03-27 | v2.0.9 | `a19585e` | replace ibexa_render_field with ez_render_field for eZ Platform 3.x |
| 2026-04-17 | v2.1.0 | `f2deb2e` | rebrand: update admin copyright templates to Exponential / 7x branding |

## Änderungen des se7enxweb-Teams, nach Thema

### Fehlerbehebungen (7)

- 2026-03-26 `67e6451` Fehlerbehebung: downgrade netgen/metadata-bundle to ^3.0 for eZ Platform 3.3, expand PHP to ^8.5
- 2026-03-26 `825e30e` Fehlerbehebung: port all Ibexa namespace/service refs to eZ Platform 3.3 equivalents
- 2026-03-26 `6cef564` Fehlerbehebung: Fix ImageVariationPass: use named service IDs not FQCN for eZ Platform 3.3
- 2026-03-26 `3943714` Fehlerbehebung: Fix search service alias for eZ Platform container
- 2026-03-26 `3c93610` Fehlerbehebung: Fix XMLText Site API service wiring
- 2026-03-26 `c46cd8f` Fehlerbehebung: Fix XMLText Site API namespaces
- 2026-03-27 `a19585e` Fehlerbehebung: replace ibexa_render_field with ez_render_field for eZ Platform 3.x

### Composer-Abhängigkeiten (1)

- 2026-03-16 `46a0468` Release: Update package name and description in composer.json

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-03-25 `facbd9c` BC: use se7enxweb/site-bundle ^2.0 and se7enxweb/legacy-bridge ^3.0 for eZ Platform 3.3 compatibility

### Marke Exponential (1)

- 2026-04-17 `f2deb2e` Funktion: rebrand: update admin copyright templates to Exponential / 7x branding

## Verwandte Seiten

- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- [Release-Changelog](../../../changelogs/extensions/site-legacy-bundle.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/site-legacy-bundle.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
