# site-bundle: Geschichte des Plattform-Repositorys

Die Geschichte von `site-bundle`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Legacy Bridge und Site-Bundles). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 105 Änderungen von 2023-12-11 bis 2026-04-19: 2 vom se7enxweb-Team und 103 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Netgen Site Bundle: gemeinsame Website-Funktionen (Menüs, Verbindung zu Layouts, Website-Kontext) für Ibexa-Websites.

## Wie es mit Exponential zusammenhängt

Basis-Bundle von Nexus; in se7enxweb/site-bundle umbenannt und als Ersatz für netgen/site-bundle deklariert, damit es nicht doppelt installiert wird.

## Was Anwender davon haben

Website-Funktionen, auf denen Nexus aufbaut.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/site-bundle
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 57 |
| Fehlerbehebungen | 11 |
| Verhaltens- und Upgrade-Änderungen | 2 |
| Dokumentation | 5 |
| Werkzeuge | 17 |
| Ohne Nutzen für Anwender | 13 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-03-17 | 3.0.5.0 | `cf54aee` | Replaced vendor infos. Rebranding. |
| 2026-04-19 | 3.0.6 | `64e6c89` | add replace netgen/site-bundle:* — prevent dual install with upstream fork |

## Änderungen des se7enxweb-Teams, nach Thema

### Marke Exponential (1)

- 2026-03-17 `cf54aee` Funktion: Replaced vendor infos. Rebranding.

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-19 `64e6c89` BC: add replace netgen/site-bundle:* — prevent dual install with upstream fork

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 13 | 3 | 1 | 0 | 0 | 0 | 4 | 2 | 0 | 3 | `2828c52` NGSTACK-813 created configuration and logic for choosing between inline representation and automatic download; `4f271db` NGSTACK-813 option for default behaviour occurs on null instead of -1 |
| 2024-01 | 6 | 3 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 2 | `4121347` IOTA-384 simplify and rename compiler pass; `b31893d` IOTA-384 rename parameter |
| 2023-10 | 2 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `55c0024` IOTA-384 add support for direct download in richtext links; `cceee4b` IOTA-384 make class final |
| 2024-02 | 6 | 3 | 1 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | `b98525e` NGSTACK-673: update for breaking change in Site API; `a70fcb9` NGSTACK-822 switch from string mapped to sort clause to FQN |
| 2024-03 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `61bdf0b` Cast the location ID to int |
| 2024-07 | 47 | 31 | 8 | 0 | 0 | 0 | 0 | 6 | 0 | 2 | `cbaa44c` Fix issues with PHPStan; `8515255` NGSTACK-811 add tag content command |
| 2024-08 | 4 | 3 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `98e8d7d` NGSTACK-822 logic of ordering changes, string parameter replaced by QN; `a777492` NGSTACK-822 logic of ordering changes, string parameter replaced by QN |
| 2024-09 | 16 | 7 | 1 | 0 | 0 | 0 | 1 | 5 | 0 | 2 | `a4b2381` Restore compatibility with Twig 3.11; `c68a928` Compatibility with Twig 3.13 |
| 2025-05 | 2 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 0 | `f41dac6` Implement command to generate dynamic showcases; `41079d3` Update CS fixer rules |
| 2025-06 | 3 | 2 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | 1 | `804be60` Bump PHP to 8.2; `2135430` Conflict with older versions of netgen/layouts-ibexa |
| 2025-10 | 3 | 1 | 0 | 0 | 0 | 0 | 0 | 1 | 0 | 1 | `4b60ca2` NGSTACK-826 change ibexa Visibility criterion to Visible criterion from ibexa-search-extra bundle; `ae5d21e` NGSTACK-826 add 'ibexa-search-extra' bundle as dependency in composer.json |

## Verwandte Seiten

- [Site-Bundles](../../../eng-us/features/6.0/platform-site-bundles.md)
- [Release-Changelog](../../../changelogs/extensions/site-bundle.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/site-bundle.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2023-10](monate/2023-10.md), [2023-12](monate/2023-12.md), [2024-01](monate/2024-01.md), [2024-02](monate/2024-02.md), [2024-03](monate/2024-03.md), [2024-07](monate/2024-07.md), [2024-08](monate/2024-08.md), [2024-09](monate/2024-09.md), [2025-05](monate/2025-05.md), [2025-06](monate/2025-06.md), [2025-10](monate/2025-10.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
