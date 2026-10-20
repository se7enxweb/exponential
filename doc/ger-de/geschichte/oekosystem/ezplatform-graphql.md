# ezplatform-graphql: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-graphql`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Suche, Cache und API). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 4 Änderungen von 2025-09-28 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

GraphQL-Server für das Content-Repository.

## Wie es mit Exponential zusammenhängt

Auf se7enxweb umbenannt, ersetzt bdunogier/ezplatform-graphql-bundle.

## Was Anwender davon haben

GraphQL-API in Installationen mit Forks.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-graphql
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 4 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v2.3.18 | `7c97b44` | Replace se7enxweb/graphql-php with ibexa/graphql-php |
| 2026-04-12 | v2.3.20 | `be8418d` | use se7enxweb vendor path for package root dir |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (3)

- 2025-09-28 `92a4adb` BC: Update package names from ezsystems to se7enxweb
- 2025-09-28 `7c97b44` BC: Replace se7enxweb/graphql-php with ibexa/graphql-php
- 2026-04-12 `be8418d` BC: use se7enxweb vendor path for package root dir

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-12 `86aad7d` BC: add replace shim for ezsystems/ezplatform-graphql

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-graphql.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-graphql.md)
- Plattform-Ökosystem nach Monat: [2025-09](monate/2025-09.md), [2026-04](monate/2026-04.md)
