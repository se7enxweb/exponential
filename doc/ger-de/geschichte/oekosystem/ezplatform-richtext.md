# ezplatform-richtext: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-richtext`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Feldtypen). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 7 Änderungen von 2025-09-28 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork des Feldtyps RichText von eZ Platform (Linie 3.x).

## Wie es mit Exponential zusammenhängt

Rich Text für Exponential Platform 3.x; Unterstützung für PHP 8.5, modernisiertes SCSS, Replace-Deklaration.

## Was Anwender davon haben

Rich Text funktioniert unter PHP 8.5 weiter.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-richtext
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 2 |
| Verhaltens- und Upgrade-Änderungen | 4 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v2.3.26 | `f8dbf98` | Merge pull request #1 from se7enxweb/fix-richtext-paths-2.3 |
| 2026-03-26 | v2.3.27, 2.3 | `d1bd3dd` | Add PHP 8.5 support |
| 2026-03-26 | v2.3.28 | `45f0fe4` | Add replace metadata for ezsystems richtext package |

## Änderungen des se7enxweb-Teams, nach Thema

### Replace-Deklarationen für das Upstream-Paket (4)

- 2026-03-26 `6902f46` BC: Add branch-alias for 2.3-se7enx
- 2026-03-26 `26167d2` BC: Remove se7enx branch-alias: prevent auto-resolution by external projects
- 2026-03-26 `45f0fe4` BC: Add replace metadata for ezsystems richtext package
- 2026-04-12 `86c7d54` BC: replace uses wildcard '*' not self.version

### Kompatibilität mit PHP 8.x (1)

- 2026-03-26 `d1bd3dd` Fehlerbehebung: Add PHP 8.5 support

### Fehlerbehebungen (1)

- 2026-04-11 `a25d6ee` Fehlerbehebung: migrate SCSS  → /, fix deprecated math functions

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Administrationsoberfläche der Plattform](../../funktionen/6.0/plattform-admin-ui-fork.md)
- [Release-Changelog](../../../changelogs/extensions/ezplatform-richtext.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-richtext.md)
- Plattform-Ökosystem nach Monat: [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
