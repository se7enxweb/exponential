# layouts-core: Geschichte des Plattform-Repositorys

Die Geschichte von `layouts-core`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Layouts und Rezepte). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 450 Änderungen von 2024-09-06 bis 2026-04-19: 1 vom se7enxweb-Team und 449 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Der Kern von Netgen Layouts (Page-Builder-Engine in PHP/Symfony), Fork se7enxweb/layouts-core.

## Wie es mit Exponential zusammenhängt

Die Engine hinter den Layouts von Nexus auf Platform v5. explayouts ist das Legacy-Gegenstück in Exponential, das demselben Modell folgt.

## Was Anwender davon haben

Layout-Page-Builder unter PHP 8.4: Getter-Shims lassen Twig die Parameter lesen.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/layouts-core
```

## Sicherheits-, Leistungs- und Upgrade-Änderungen in der Geschichte

Diese Änderungen sind im Abdeckungsnachweis eingeordnet und werden sonst nirgends auf dieser Seite genannt. Die meisten sind Upstream-Geschichte, die der Fork mitführt; Datum und Betreffzeile des Commits sind der Eintrag im Änderungsverzeichnis (siehe das [vollständige Änderungsverzeichnis](../aenderungsverzeichnis/README.md)). Die Betreffzeile sagt, was sich geändert hat; die genaue Wirkung zeigt der Commit im Repository.

### Sicherheit (4)

- 2025-11-11 `1d5032714` Replace usage of ezyang html purifier with Symfony Html Sanitizer component
- 2025-12-15 `e5cdcd61e` Remove custom wrapper around Symfonys HtmlSanitizer
- 2025-12-16 `932903874` Rename ApiCsrfValidationListener to AppCsrfValidationListener
- 2025-12-17 `1a35168f3` Do not run CSRF validation on app forms

### Leistung (1)

- 2025-10-28 `08e55d125` Various little code optimizations

### Verhalten und Upgrade (9)

- 2025-07-15 `bb12b1e1f` Move SYMFONY_DEPRECATIONS_HELPER variable to Composer config
- 2025-07-15 `3c95d79a2` Remove support for Symfony 6.2 and 6.3
- 2025-11-10 `3b06316da` Remove deprecations across the board: symfony, doctrine and so on
- 2025-11-11 `f3c9d6625` Use DsnParser in Doctrine, fix more deprecations
- 2025-11-11 `29fb2386b` Fix some more deprecations
- 2025-11-12 `ea6574da1` Remove wrong deprecation
- 2025-11-17 `e004a96d7` Use PHPUnit deprecations reporter
- 2025-11-20 `49bcbc946` Remove deprecations
- 2025-12-03 `1bfea6fdd` Remove usage of deprecated DI Extension class

<!-- rev2-listed-rows:end -->

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 164 |
| Fehlerbehebungen | 32 |
| Verhaltens- und Upgrade-Änderungen | 11 |
| Sicherheit | 4 |
| Leistung | 1 |
| Dokumentation | 11 |
| Werkzeuge | 196 |
| Releases | 3 |
| Ohne Nutzen für Anwender | 28 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-04-19 | 2.0.0-se7enx.1 | `3ec0262b2` | add Twig/PHP 8.4 private(set) getter shims to Parameter class |

## Änderungen des se7enxweb-Teams, nach Thema

### Kompatibilität mit PHP 8.x (1)

- 2026-04-19 `3ec0262b2` Fehlerbehebung: add Twig/PHP 8.4 private(set) getter shims to Parameter class

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-09 | 9 | 0 | 0 | 1 | 0 | 0 | 0 | 7 | 0 | 1 | `03acf1f33` Fix CS; `40987764e` Fix deprecation in Twig 3.12 |
| 2024-05 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `79b509c4a` Support yield rendering strategy in Twig 3.9+ |
| 2025-02 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `0206f2477` Expose block definition plugin classes; `21ea622d7` Add a DynamicParameter class to declare which dynamic parameters a handler has |
| 2025-07 | 19 | 2 | 1 | 2 | 0 | 0 | 0 | 12 | 0 | 2 | `14f39b6ea` Upgrade PHPStan to 2.x; `3c525cd18` Make LinkValue internal properties only accept strings |
| 2025-09 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `d89ff1fd2` Use include Twig function instead of tag; `5a02c8595` Remove obsolete  tag |
| 2025-10 | 25 | 16 | 1 | 0 | 0 | 1 | 0 | 4 | 0 | 3 | `493bc5ea5` Add missing parameter and return typehints; `4a0824a52` Add constructor property promotion |
| 2025-11 | 165 | 64 | 17 | 6 | 1 | 0 | 2 | 67 | 0 | 8 | `501549e21` Replace PHPUnit annotations with attributes; `e88254df7` Update core API values to use property hooks |
| 2025-12 | 197 | 71 | 11 | 1 | 3 | 0 | 8 | 94 | 2 | 7 | `02befe412` Switch uuids to Symfony Uid component; `1dfede980` Switch mocks to stubs in tests |
| 2026-01 | 21 | 6 | 1 | 1 | 0 | 0 | 1 | 6 | 0 | 6 | `0de13279b` Remove usage of friends-of-behat/suite-settings-extension; `e17483b3f` Remove usage of default_index_method, it is deprecated in Symfony 8.1 |
| 2026-02 | 6 | 0 | 0 | 0 | 0 | 0 | 0 | 4 | 1 | 1 | `e8a47e536` Remove unneeded with() calls for stubs; `f65d58795` Limit node to v14 |

## Verwandte Seiten

- [Fork von layouts-core](../../funktionen/6.0/plattform-layouts-core-fork.md)
- [Exponential Layouts](../../bc/6.0/layouts.md)
- [Release-Changelog](../../../changelogs/extensions/layouts-core.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/layouts-core.md)
- Plattform-Ökosystem nach Monat: [2024-05](monate/2024-05.md), [2024-09](monate/2024-09.md), [2025-02](monate/2025-02.md), [2025-07](monate/2025-07.md), [2025-09](monate/2025-09.md), [2025-10](monate/2025-10.md), [2025-11](monate/2025-11.md), [2025-12](monate/2025-12.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-04](monate/2026-04.md)
