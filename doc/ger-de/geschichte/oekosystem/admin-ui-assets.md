# admin-ui-assets: Geschichte des Plattform-Repositorys

Die Geschichte von `admin-ui-assets`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Administrationsoberfläche). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 44 Änderungen von 2024-01-23 bis 2026-04-04: 3 vom se7enxweb-Team und 41 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Externe JavaScript- und CSS-Abhängigkeiten der Administrationsoberfläche von Platform v5.

## Wie es mit Exponential zusammenhängt

Wird von se7enxweb/admin-ui verlangt; in se7enxweb/admin-ui-assets umbenannt, mit Replace-Deklarationen.

## Was Anwender davon haben

Die Admin-Assets werden vom Vendor se7enxweb installiert.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/admin-ui-assets
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 18 |
| Fehlerbehebungen | 2 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Werkzeuge | 11 |
| Ohne Nutzen für Anwender | 12 |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (1)

- 2026-04-04 `fb65cf36` Werkzeuge: Update package name and license in composer.json

Außerdem: 2 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-01 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `c4d58590` IBX-7411 Added chartjs-plugin-datalabels package (#20) |
| 2024-02 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `27863be9` IBX-7411: Fixed path for chartjs and chartjs plugin (#21) |
| 2024-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `397e64ed` Set up branch to become 5.0 in the future |
| 2024-04 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `ce8b3a13` IBX-8119: Upgraded minimum PHP version to 8.3 |
| 2024-05 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `6e3dfb4e` Updated copyright year to 2024 |
| 2024-06 | 1 | 0 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | `6fd31b17` IBX-8139: Dropped class_alias BC layer statements from all classes (#23) |
| 2025-01 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `f39e3f2b` Updated copyright year to 2025 |
| 2025-02 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `f1141eb4` IBX-8470: Upgraded codebase to Symfony 6 (#24) |
| 2025-05 | 6 | 2 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 1 | `585f4712` IBX-9939: Design System aliases (#30); `5faaa4e5` IBX-9916: Upgrade frontend dependencies (#29) |
| 2025-06 | 8 | 3 | 0 | 0 | 0 | 0 | 0 | 3 | 0 | 2 | `0a913972` Added code style configuration; `a0a70291` IBX-10162: Removed taggify dependency (#34) |
| 2025-07 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `4706321b` Update Design System version to 1.0.0-rc1 (#37); `baaded20` Update Design System to 1.0.0 (#38) |
| 2025-08 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `b90a4acc` IBX-10352: Escape output printed in GitHub Actions; `37028185` IBX-10552: [CKEditor] Packages version bump (#39) |
| 2025-09 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `0b673819` Fixed conflicts after merge (#40) |
| 2025-10 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `9c0c11cf` IBX-10792: Prepare scripts for dev version of admin-ui-assets (#43); `111e7ac6` IBX-10749: Add ids-core to assets (#41) |
| 2025-11 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `ef9c1114` Update prepare next script to update branch instead of overwriting (#44) |
| 2025-12 | 4 | 1 | 0 | 0 | 0 | 0 | 0 | 2 | 0 | 1 | `d95d707b` Update ibexa Design System version to ^v1.0.0 (#45); `005ced50` [Composer] Fixed license information |
| 2026-04 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `070c589c` Added empty workflow to trigger on DS update (#46) |

## Verwandte Seiten

- [Administrationsoberfläche der Plattform](../../../eng-us/features/6.0/platform-admin-ui-fork.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/admin-ui-assets.md)
- Plattform-Ökosystem nach Monat: [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-04](monate/2024-04.md), [2024-05](monate/2024-05.md), [2024-06](monate/2024-06.md), [2025-01](monate/2025-01.md), [2025-02](monate/2025-02.md), [2025-05](monate/2025-05.md), [2025-06](monate/2025-06.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2025-10](monate/2025-10.md), [2025-11](monate/2025-11.md), [2025-12](monate/2025-12.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
