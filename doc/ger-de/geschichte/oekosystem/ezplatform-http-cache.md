# ezplatform-http-cache: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-http-cache`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Suche, Cache und API). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 7 Änderungen von 2024-11-28 bis 2026-04-12: 6 vom se7enxweb-Team und 1 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

HTTP-Cache-Verarbeitung (Varnish / Symfony) für die Plattform.

## Wie es mit Exponential zusammenhängt

Unterstützung für PHP 8.5, Replace-Deklaration.

## Was Anwender davon haben

Caching über einen Reverse Proxy funktioniert unter PHP 8.5.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-http-cache
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 1 |
| Verhaltens- und Upgrade-Änderungen | 4 |
| Releases | 1 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v2.3.17 | `fdd5c57` | Update package name and dependencies in composer.json |
| 2026-03-26 | v2.3.18 | `2b7205c` | Add PHP 8.5 support |
| 2026-03-26 | v2.3.19 | `fabe744` | Add replace metadata for ezsystems http-cache package |

## Änderungen des se7enxweb-Teams, nach Thema

### Replace-Deklarationen für das Upstream-Paket (4)

- 2026-03-26 `74a81c0` BC: Add branch-alias for 2.3-se7enx
- 2026-03-26 `fc50ab1` BC: Remove se7enx branch-alias: prevent auto-resolution by external projects
- 2026-03-26 `fabe744` BC: Add replace metadata for ezsystems http-cache package
- 2026-04-12 `4703d2d` BC: replace uses wildcard '*' not self.version

### Composer-Abhängigkeiten (1)

- 2025-09-28 `fdd5c57` Release: Update package name and dependencies in composer.json

### Kompatibilität mit PHP 8.x (1)

- 2026-03-26 `2b7205c` Fehlerbehebung: Add PHP 8.5 support

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-11 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 |  |

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-http-cache.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-http-cache.md)
- Plattform-Ökosystem nach Monat: [2024-11](monate/2024-11.md), [2025-09](monate/2025-09.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
