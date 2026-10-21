# ezplatform-design-engine: Geschichte des Plattform-Repositorys

Die Geschichte von `ezplatform-design-engine`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Administrationsoberfläche). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 3 Änderungen von 2026-03-31 bis 2026-04-12, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fallback-Mechanismus für Designs (Theme-Kette) für die Plattform.

## Wie es mit Exponential zusammenhängt

Ersetzt ezsystems/ezplatform-design-engine mit einer Replace-Deklaration mit Platzhalter.

## Was Anwender davon haben

Der Theme-Fallback funktioniert auch in Installationen mit Forks weiter.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezplatform-design-engine
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 2 |
| Werkzeuge | 1 |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (1)

- 2026-03-31 `0d6416a` Werkzeuge: Bugfix for composer validation of composer.json for packagist.org package to update normally. Bugfix.

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-04-11 `1f401d1` BC: add replace shim for ezsystems counterpart package

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-04-12 `87c62d1` BC: replace ezsystems/ezplatform-design-engine with wildcard version

## Verwandte Seiten

- [Administrationsoberfläche der Plattform](../../../eng-us/features/6.0/platform-admin-ui-fork.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezplatform-design-engine.md)
- Plattform-Ökosystem nach Monat: [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
