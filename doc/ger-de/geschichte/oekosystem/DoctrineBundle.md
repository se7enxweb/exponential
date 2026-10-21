# DoctrineBundle: Geschichte des Plattform-Repositorys

Die Geschichte von `DoctrineBundle`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Framework-Forks). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 2 Änderungen von 2026-01-30 bis 2026-01-30, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork des Symfony DoctrineBundle 1.5.

## Wie es mit Exponential zusammenhängt

Ersetzt doctrine/doctrine-bundle für den alten Stack.

## Was Anwender davon haben

Der alte Stack lässt sich auf aktuellem PHP installieren.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/doctrine-bundle
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Verhaltens- und Upgrade-Änderungen | 1 |
| Werkzeuge | 1 |

## Änderungen des se7enxweb-Teams, nach Thema

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-01-30 `8c6d938d` BC: Replace vendor name for package and twig require rule.

### Composer-Abhängigkeiten (1)

- 2026-01-30 `28024767` Werkzeuge: Replace vendor package via composer.json

## Verwandte Seiten

- [Framework-Forks](../../funktionen/6.0/plattform-php85-framework-forks.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/DoctrineBundle.md)
- Plattform-Ökosystem nach Monat: [2026-01](monate/2026-01.md)
