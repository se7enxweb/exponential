# oss: Geschichte des Plattform-Repositorys

Die Geschichte von `oss`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Distributionen und Starter). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 10 Änderungen von 2023-12-21 bis 2026-04-12: 5 vom se7enxweb-Team und 5 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Metapaket für die Open-Source-Edition der Plattform (Linie 3.3 / 4.6).

## Wie es mit Exponential zusammenhängt

ibexa/oss in se7enxweb/oss umbenannt; jedes Paket von ezsystems durch seinen Fork bei se7enxweb ersetzt.

## Was Anwender davon haben

Ein einziges require ohne ein Paket ezsystems/*.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/oss
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 2 |
| Fehlerbehebungen | 2 |
| Verhaltens- und Upgrade-Änderungen | 3 |
| Werkzeuge | 3 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-03-25 | 3.3.0.1 | `2407521` | rename ibexa/oss -> se7enxweb/oss for 3.3.0.x branch |
| 2026-04-11 | v3.3.0.2 | `63289fd` | Replace ezsystems packages with se7enxweb versions |
| 2026-04-12 | v3.3.0.5 | `4db62cb` | add se7enxweb/ezplatform-alloyeditor-element-width to require |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (3)

- 2026-04-11 `63289fd` BC: Replace ezsystems packages with se7enxweb versions
- 2026-04-12 `d4e22fe` BC: use se7enxweb/* forks for design-engine, graphql, http-cache, query-fieldtype, richtext, search
- 2026-04-12 `d9319bb` BC: complete se7enxweb/* migration — zero ezsystems/* in require

### Weitere Änderungen am Fork (1)

- 2026-03-25 `2407521` Fehlerbehebung: rename ibexa/oss -> se7enxweb/oss for 3.3.0.x branch

### Fehlerbehebungen (1)

- 2026-04-12 `4db62cb` Fehlerbehebung: add se7enxweb/ezplatform-alloyeditor-element-width to require

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `5171945` [CI] IBX-4515: Include tests running on PHP 8.2 (#129) |
| 2024-02 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `b9a09cc` [Composer] Added conflict with friends-of-behat/mink-browserkit-driver:v1.6.2 (#136) |
| 2024-05 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `c01eb77` IBX-8154: Run tests on PHP 8.3 (3.3) (#150) |
| 2024-11 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `106ab6c` Unpacked symfony/serializer-pack dependency declaration |
| 2025-10 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `c4d2556` [CI] Add token selection for 3.3 (#241) |

## Verwandte Seiten

- [DXP-Skeleton und Metapaket](../../../eng-us/features/6.0/platform-dxp-skeleton.md)
- [Release-Changelog](../../../changelogs/extensions/oss.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/oss.md)
- [SQLite für die Plattform](../../../eng-us/features/6.0/platform-sqlite-install.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2024-02](monate/2024-02.md), [2024-05](monate/2024-05.md), [2024-11](monate/2024-11.md), [2025-10](monate/2025-10.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
