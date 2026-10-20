# symfony: Geschichte des Plattform-Repositorys

Die Geschichte von `symfony`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Framework-Forks). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 18 Änderungen von 2025-08-24 bis 2026-04-09, alle vom se7enxweb-Team. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork des Frameworks Symfony 3.4, aufgeteilt in se7enxweb/symfony.

## Wie es mit Exponential zusammenhängt

Hält Installationen von Nexus 1.x auf Basis von Symfony 3.4 unter PHP 8.2 bis 8.5 lauffähig.

## Was Anwender davon haben

Rückgabetypen für PHP 8, Nullable-Typen, Korrekturen für Closures und Reflection.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/symfony
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Fehlerbehebungen | 8 |
| Dokumentation | 1 |
| Werkzeuge | 6 |
| Releases | 1 |
| Ohne Nutzen für Anwender | 2 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2026-01-13 | v3.4.50 | `5fc51171c` | Bugfix for composer.json validation test to pass. Bugfix. |

## Änderungen des se7enxweb-Teams, nach Thema

### Kompatibilität mit PHP 8.x (7)

- 2025-08-24 `9dcfb7a69` Fehlerbehebung: Update ErrorHandler.php adding tested php 8.2 support with hopes it will test working for php 8.4+. New PHP 8 Support.
- 2025-08-24 `df6faf1aa` Fehlerbehebung: Update ExceptionCaster.php to include bugfix for php8.4 support as tested with ezplatform 2.5 gpl and confirmed working. New PHP 8.4 Support.
- 2025-08-24 `118d7f4b0` Fehlerbehebung: Update LazyLoadingValueHolderGenerator.php to include php 8.2+ tested as working changes to reflect Zend to Laminas Library Transition. New PHP 8 Support
- 2026-01-28 `f2852d452` Fehlerbehebung: PHP 8.x: Add return types to VarDumper Data class
- 2026-01-28 `e39f6b0c1` Fehlerbehebung: PHP 8.x closure to method reference in WebProfilerExtension
- 2026-01-29 `fd017e42a` Fehlerbehebung: Add explicit nullable types and fix ReflectionProperty deprecation in ErrorHandler.php for PHP 8.1+
- 2026-02-10 `4e267ed5b` Fehlerbehebung: PHP85 Compatiblity bugfixes for deprecation warnings. Tested. Bugfixes.

### Composer-Abhängigkeiten (6)

- 2025-08-24 `acf172d10` Werkzeuge: Update composer.json changed package vendor name
- 2025-08-24 `f68a4f210` Werkzeuge: Update composer.json added tag to test deployment workflow. No change.
- 2025-08-24 `1b35573f6` Werkzeuge: Update composer.json initial attemp at working around breakdowns in forking package due to dependencies conflicts from other packages. Bugfix attempt.
- 2025-08-24 `cc1e3c14f` Werkzeuge: Update composer.json
- 2026-01-13 `5fc51171c` Release: Bugfix for composer.json validation test to pass. Bugfix.
- 2026-01-30 `66f8c749c` Werkzeuge: Replace twig/twig with se7enxweb/twig in composer.json

### Test-Werkzeuge (1)

- 2025-08-24 `cd6d0491d` Werkzeuge: Update ArrayNode.php tested as working bugfix for our env. Note: This is likely not required anylonger but we must test with it. Incomplete Patch.

### Dokumentation (1)

- 2026-02-14 `14065e4da` Dokumentation: Added bugfixes required by 7x Nexus to support default installations with more graceful features implmentations. Added support for change of behavior 

### Fehlerbehebungen (1)

- 2026-04-09 `feb97c92f` Fehlerbehebung: guard ini_set() in NativeFileSessionHandler against active/sent sessions

Außerdem: 2 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Verwandte Seiten

- [Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- [Release-Changelog](../../../changelogs/extensions/symfony.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../oekosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/symfony.md)
- Plattform-Ökosystem nach Monat: [2025-08](monate/2025-08.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
