# PHP-Parser: Geschichte des Plattform-Repositorys

Die Geschichte von `PHP-Parser`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Framework-Forks). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 2 Änderungen von 2026-05-11 bis 2026-05-11, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork der Linie 0.9 von PHP-Parser (ersetzt nikic/php-parser 0.9.5).

## Wie es mit Exponential zusammenhängt

Bringt dem alten Stack Kompatibilität mit den Tokens von PHP 8.

## Was Anwender davon haben

Alte Werkzeuge können PHP-8-Code parsen.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/php-parser
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 1 |
| Verhaltens- und Upgrade-Änderungen | 1 |

## Änderungen des se7enxweb-Teams, nach Thema

### Kompatibilität mit PHP 8.x (1)

- 2026-05-11 `0fdb2d5a` Fehlerbehebung: Add PHP 8 token compatibility for 0.9.x (se7enxweb fork)

### Replace-Deklarationen für das Upstream-Paket (1)

- 2026-05-11 `83cf9a0f` BC: Add replace directive for nikic/php-parser 0.9.5 in composer.json

## Verwandte Seiten

- [Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/PHP-Parser.md)
- Plattform-Ökosystem nach Monat: [2026-05](monate/2026-05.md)
