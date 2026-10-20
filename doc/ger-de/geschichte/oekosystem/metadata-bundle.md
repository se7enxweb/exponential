# metadata-bundle: Geschichte des Plattform-Repositorys

Die Geschichte von `metadata-bundle`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Feldtypen). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 4 Änderungen von 2026-03-02 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Bundle mit einem Feldtyp für Metadaten, kompatibel mit der Legacy-Erweiterung xrowmetadata.

## Wie es mit Exponential zusammenhängt

In se7enxweb/metadata-bundle umbenannt, mit Unterstützung für Ibexa 5.x / PHP 8.4.

## Was Anwender davon haben

Dasselbe Feld für SEO-Metadaten funktioniert im Legacy-Kernel und im neuen Stack.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/metadata-bundle
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 1 |
| Verhaltens- und Upgrade-Änderungen | 2 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-03-26 | v3.0.1 | `1de801a` | Rename to se7enxweb/metadata-bundle, switch to ezsystems/ezplatform-kernel, add PHP 8.5 support |
| 2026-03-26 | v3.0.2 | `070db38` | Add replace for netgen/metadata-bundle |
| 2026-04-12 | v5.0.0 | `8e526bd` | feat(5.0.x): Ibexa 5.x / PHP 8.4 support |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-03-26 `1de801a` BC: Rename to se7enxweb/metadata-bundle, switch to ezsystems/ezplatform-kernel, add PHP 8.5 support

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-03-26 `070db38` BC: Add replace for netgen/metadata-bundle

### Kompatibilität mit PHP 8.x (1)

- 2026-04-12 `8e526bd` Fehlerbehebung: feat(5.0.x): Ibexa 5.x / PHP 8.4 support

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- [Erweiterung xrowmetadata](../../../eng-us/features/6.0/extensions/xrowmetadata.md)
- [Release-Changelog](../../../changelogs/extensions/metadata-bundle.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/metadata-bundle.md)
- Plattform-Ökosystem nach Monat: [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
