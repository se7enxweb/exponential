# mediata-ezpage-fieldtype-bundle-main: Geschichte des Plattform-Repositorys

Die Geschichte von `mediata-ezpage-fieldtype-bundle-main`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Feldtypen). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 7 Änderungen von 2026-03-16 bis 2026-03-26, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Der Feldtyp Page (Landing Page) für Ibexa 4.

## Wie es mit Exponential zusammenhängt

Am 2026-03-16 importiert, getestet für Ibexa 4.6, mit einem Twig-Operator für die Verwendung in reinem Symfony.

## Was Anwender davon haben

Der Feldtyp Page ist auf Platform 4.6 / 3.3 verfügbar.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/mediata-ezpage-fieldtype-bundle
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 1 |
| Fehlerbehebungen | 1 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Dokumentation | 1 |
| Werkzeuge | 1 |
| Releases | 1 |
| Ohne Nutzen für Anwender | 1 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-03-16 | 1.0.0 | `c3c9150` | Updated version requirement ibexa/core from 4.5 to 4.6 in composer.json. Upgrade. |
| 2026-03-16 | 1.0.1 | `9c2db8e` | Added twig operator to bundle to provide for pure symfony / platform access to ezflow / ezpage fieldtype block data for display within twig templates. |
| 2026-03-26 | 1.0.2 | `4630a40` | replace ibexa/core ~4.6.0 with ezsystems/ezplatform-kernel ^1.3, expand PHP to ^8.5 |

## Änderungen des se7enxweb-Teams, nach Thema

### Test-Werkzeuge (1)

- 2026-03-16 `6f03e05` Werkzeuge: Initial Import of ibexa4 tested as working bundle. Enhancements.

### Dokumentation (1)

- 2026-03-16 `0e42de9` Dokumentation: Added LICENSE.md Documentation. Doc.

### Fehlerbehebungen (1)

- 2026-03-16 `94c8b14` Fehlerbehebung: Bugfix for syntax error (trailing comma).

### Composer-Abhängigkeiten (1)

- 2026-03-16 `c3c9150` Release: Updated version requirement ibexa/core from 4.5 to 4.6 in composer.json. Upgrade.

### Design und Templates (1)

- 2026-03-16 `9c2db8e` Funktion: Added twig operator to bundle to provide for pure symfony / platform access to ezflow / ezpage fieldtype block data for display within twig templates.

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-03-26 `4630a40` BC: replace ibexa/core ~4.6.0 with ezsystems/ezplatform-kernel ^1.3, expand PHP to ^8.5

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- [Release-Changelog](../../../changelogs/extensions/mediata-ezpage-fieldtype-bundle-main.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/mediata-ezpage-fieldtype-bundle-main.md)
- Plattform-Ökosystem nach Monat: [2026-03](monate/2026-03.md)
