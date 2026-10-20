# twig: Geschichte des Plattform-Repositorys

Die Geschichte von `twig`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Framework-Forks). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 15 Änderungen von 2023-09-14 bis 2026-05-11: 9 vom se7enxweb-Team und 6 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork von Twig 2.x (ersetzt twig/twig).

## Wie es mit Exponential zusammenhängt

Template-Engine der Linie Symfony 3.4, sicher für PHP 8.5 gemacht.

## Was Anwender davon haben

Templates werden unter PHP 8.4 und 8.5 ohne Deprecation-Meldungen dargestellt.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/twig
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 3 |
| Fehlerbehebungen | 3 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Sicherheit | 1 |
| Werkzeuge | 4 |
| Ohne Nutzen für Anwender | 3 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-08-24 | v2.16.2 | `9e69bb01` | Update NameExpression.php bugfix for php 8.2+ support. Tested working. |
| 2026-01-30 | v2.16.3 | `5d0b35d1` | Update twig/twig version to 2.16.2 |
| 2026-04-09 | v2.16.4 | `d45d841d` | detect PHP 8.5 closure names in CallExpression::reflectCallable() |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (3)

- 2025-08-24 `6aabd856` Werkzeuge: Update composer.json replaced package vendor name
- 2025-08-24 `6209f2d2` Werkzeuge: Update composer.json added tag to test deploy workflow. No change.
- 2026-01-30 `8818ed45` Werkzeuge: Update twig/twig version constraint in composer.json

### Kompatibilität mit PHP 8.x (3)

- 2025-08-24 `9e69bb01` Fehlerbehebung: Update NameExpression.php bugfix for php 8.2+ support. Tested working.
- 2026-04-09 `d45d841d` Fehlerbehebung: detect PHP 8.5 closure names in CallExpression::reflectCallable()
- 2026-05-11 `85503dec` Fehlerbehebung: fix(php8.4+): explicit nullable type parameters throughout src/

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-01-30 `8095fbff` BC: Add replace section for twig/twig in composer.json

### Design und Templates (1)

- 2026-01-30 `5d0b35d1` Funktion: Update twig/twig version to 2.16.2

Außerdem: 1 Merge- oder Finanzierungs-Commit des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-09 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `a18da161` Add SourcePolicyInterface to selectively enable the Sandbox based on a template's Source |
| 2023-12 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `0c9cc7ef` End of maintenance for the 2.x branch; `a4974b29` feature #3893 Add SourcePolicyInterface to selectively enable the Sandbox based on a template's Source |
| 2024-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `a1d84cfb` Bump CI action/cache |
| 2024-09 | 2 | 0 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | 1 | `2102dd13` Fix a security issue when an included sandboxed template has been loaded before without the sandbox context; `19185947` Prepare the 2.16.1 release |

## Verwandte Seiten

- [Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- [Release-Changelog](../../../changelogs/extensions/twig.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/twig.md)
- Plattform-Ökosystem nach Monat: [2023-09](monate/2023-09.md), [2023-12](monate/2023-12.md), [2024-03](monate/2024-03.md), [2024-09](monate/2024-09.md), [2025-08](monate/2025-08.md), [2026-01](monate/2026-01.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md), [2026-05](monate/2026-05.md)
