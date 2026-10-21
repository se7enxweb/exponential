# ezplatform-admin-ui-assets: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-admin-ui-assets`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Administrationsoberfläche). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 3 Änderungen von 2025-06-03 bis 2026-04-12: 2 vom se7enxweb-Team und 1 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Externe Asset-Abhängigkeiten der Administrationsoberfläche 2.x / 3.x.

## Wie es mit Exponential zusammenhängt

In se7enxweb/ezplatform-admin-ui-assets umbenannt, mit einer Replace-Deklaration.

## Was Anwender davon haben

Die Admin-Assets werden vom Vendor se7enxweb installiert.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-admin-ui-assets
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 1 |
| Verhaltens- und Upgrade-Änderungen | 2 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-04-12 | v5.3.6 | `12de333` | add replace shim for ezsystems/* original package |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-04-11 `a6a16e7` BC: rename package to se7enxweb/ezplatform-admin-ui-assets

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-12 `12de333` BC: add replace shim for ezsystems/* original package

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2025-06 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `219b71b` IBX-9793: Replace taggify with fork in package.json |

## Verwandte Seiten

- [Administrationsoberfläche der Plattform](../../funktionen/6.0/plattform-admin-ui-fork.md)
- [Release-Changelog](../../../changelogs/extensions/ezplatform-admin-ui-assets.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-admin-ui-assets.md)
- Plattform-Ökosystem nach Monat: [2025-06](monate/2025-06.md), [2026-04](monate/2026-04.md)
