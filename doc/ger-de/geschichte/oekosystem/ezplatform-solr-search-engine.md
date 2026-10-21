# ezplatform-solr-search-engine: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-solr-search-engine`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Suche, Cache und API). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 6 Änderungen von 2024-03-26 bis 2026-04-12: 3 vom se7enxweb-Team und 3 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Anbindung der Suchmaschine Solr.

## Wie es mit Exponential zusammenhängt

Auf die Organisation se7enxweb umbenannt, mit einem Replace-Shim.

## Was Anwender davon haben

Volltextsuche über Solr.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-solr-search-engine
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 4 |
| Verhaltens- und Upgrade-Änderungen | 2 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-09-28 | v3.3.18 | `8d0ab01` | Update symfony/http-client version to 5.4.45 |
| 2026-04-12 | v3.3.19 | `4daf39b` | add replace shim for ezsystems/* original package |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2025-09-28 `d58e0e4` BC: Update package details for se7enxweb organization

### Weitere Änderungen am Fork (1)

- 2025-09-28 `8d0ab01` Fehlerbehebung: Update symfony/http-client version to 5.4.45

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-12 `4daf39b` BC: add replace shim for ezsystems/* original package

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-03 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `6ddce40` Fixed distribution link forcing https redirection |
| 2024-05 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `2fb300d` Fixed distribution link in generate-solr-config.sh forcing redirection |
| 2024-07 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `19ff229` IBX-8378: Fixed handling non-indexable field types |

## Verwandte Seiten

- [Release-Changelog](../../../changelogs/extensions/ezplatform-solr-search-engine.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-solr-search-engine.md)
- Plattform-Ökosystem nach Monat: [2024-03](monate/2024-03.md), [2024-05](monate/2024-05.md), [2024-07](monate/2024-07.md), [2025-09](monate/2025-09.md), [2026-04](monate/2026-04.md)
