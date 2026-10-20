# ezpublish-kernel: Geschichte des Plattform-Repositorys

Die Geschichte von `ezpublish-kernel`, einem der Plattform-Repositorys rund um Exponential (Gruppe: Kernel). Lesen Sie sie, um zu erfahren, was das Repository Ihnen bietet, wie es mit Exponential zusammenhängt und wann es sich geändert hat. Das Änderungsverzeichnis enthält 31 Änderungen von 2023-12-22 bis 2026-04-10: 30 vom se7enxweb-Team und 1 aus der Upstream-Geschichte, die der Fork mitführt. Die Commit-Nachrichten stehen so, wie sie im Repository festgehalten sind, also meist auf Englisch.

## Was es ist

Fork des Kernels aus der Zeit von 2014.11, den der Symfony-Stack verwendet, der den Legacy-Kernel umschließt.

## Wie es mit Exponential zusammenhängt

Bleibt auf aktuellem PHP installierbar, damit sich Exponential Platform Nexus 1.x und Exponential Platform Legacy weiter bauen lassen.

## Was Anwender davon haben

Composer-Installationen auf PHP 8.1 bis 8.3 und neuer, Template-Korrekturen für PHP 8.5, funktionierende Cache-Konfiguration, SQLite-Installer.

Installieren Sie es mit Composer (ein Projekt, das die Plattform bereits verlangt, holt es von selbst):

```bash
composer require se7enxweb/ezpublish-kernel
```

## Anzahl nach Art

| Art | Änderungen |
|---|---|
| Funktionen | 5 |
| Fehlerbehebungen | 7 |
| Verhaltens- und Upgrade-Änderungen | 1 |
| Werkzeuge | 8 |
| Releases | 8 |
| Ohne Nutzen für Anwender | 2 |

## Releases des se7enxweb-Teams

| Datum | Tag | Commit | Änderung |
|---|---|---|---|
| 2025-06-11 | v8.0.0 | `d0ba8f675` | Update composer.json version bump |
| 2025-06-11 | v8.0.1 | `f5f432cbe` | Update composer.json version bump for twig dependency |
| 2025-07-01 | v7.5.32 | `66a78a034` | Update composer.json version bump |
| 2025-08-24 | v7.5.33 | `49c4ffdc3` | Update composer.json to include undesired package version number. Bugfix. |
| 2025-09-05 | v7.5.34 | `3ce50c5a1` | Update layout.html.twig replaced product name in debug layout template. Rebranding. |
| 2025-09-26 | v7.5.35 | `89c58e905` | Update kriswallsmith/buzz version constraints |
| 2026-01-13 | v7.5.36 | `95434bfae` | Testing composer workaround preventing installations. |
| 2026-04-09 | v7.5.41 | `23d2b55de` | add SQLite installer support + exponential-oss install type |

## Änderungen des se7enxweb-Teams, nach Thema

### Composer-Abhängigkeiten (7)

- 2025-06-11 `75dfdbf5b` Werkzeuge: Update composer.json switched package distro name to 7x. Increased PHP version support for testing.
- 2025-06-28 `3a5ce7adc` Werkzeuge: Update composer.json Reduce twig requirement
- 2025-06-28 `5ce71d41f` Werkzeuge: Update composer.json version downgrade for doctrine-dbal-schema.
- 2025-07-01 `28c99098f` Werkzeuge: Update composer.json swap vendor for dependency
- 2025-08-24 `8d2f36ecb` Werkzeuge: Update composer.json replaced package dependency liip for se7enxweb. Forking for changes.
- 2025-08-24 `824576e57` Werkzeuge: Update composer.json to include changes to further fork this package. Bugfixes.
- 2026-01-13 `95434bfae` Release: Testing composer workaround preventing installations.

### Versionsnummern (7)

- 2025-06-11 `d0ba8f675` Release: Update composer.json version bump
- 2025-06-11 `f5f432cbe` Release: Update composer.json version bump for twig dependency
- 2025-07-01 `61141719b` Release: Update composer.json version bump for package
- 2025-07-01 `54a42dcde` Release: Update composer.json package version bump
- 2025-07-01 `824059c6b` Release: Update composer.json package version bump for greater php support
- 2025-07-01 `66a78a034` Release: Update composer.json version bump
- 2025-08-24 `49c4ffdc3` Release: Update composer.json to include undesired package version number. Bugfix.

### Marke Exponential (3)

- 2025-09-05 `3ce50c5a1` Funktion: Update layout.html.twig replaced product name in debug layout template. Rebranding.
- 2025-09-05 `e1467651a` Funktion: Update cleandata.sql to replace product name in sql text strings. Rebranding.
- 2026-02-14 `654367b56` Funktion: Rebranding Default Installation Name and Doc URL. Rebranding.

### Weitere Änderungen am Fork (3)

- 2025-09-24 `13fd781f3` Fehlerbehebung: Update jsrouting-bundle version constraints
- 2025-09-26 `6b9bb39c1` Fehlerbehebung: Update PHP version requirement to include 8.3
- 2025-09-26 `89c58e905` Fehlerbehebung: Update kriswallsmith/buzz version constraints

### Kompatibilität mit PHP 8.x (2)

- 2025-08-24 `d297f8e66` Fehlerbehebung: Update ScriptHandler.php to include php 8.2+ tested support required by ezplatform 2.5 gpl. New PHP 8 Support.
- 2026-01-30 `529b8f3e3` Fehlerbehebung: Fix PHP 8.5 compatibility: Replace closure-based Twig functions with named methods

### Test-Werkzeuge (2)

- 2025-09-24 `a7c676028` Werkzeuge: Update doctrine/doctrine-bundle version constraint to test 3.2 integration
- 2026-02-09 `4be8f2faa` Werkzeuge: Bugfixes to enable reasonable cache within platform. Tested as working in nexus 1.0.0.2 github. Required to fix symfony/platform based cache problems.

### Unterstützung für SQLite (2)

- 2026-04-09 `23d2b55de` Funktion: add SQLite installer support + exponential-oss install type
- 2026-04-10 `d50c34bdb` Funktion: CoreInstaller was changed to substitute SqliteDbPlatform for bare SqlitePlatform during schema import to ensure composite primary keys are generated c

### Paket auf den Vendor se7enxweb umbenannt (1)

- 2026-01-28 `037cc410c` BC: Bugfix for 7x ezpublish-kernel to replace vendor name in configuration yaml. Tested.

### Fehlerbehebungen (1)

- 2026-02-10 `3063e284a` Fehlerbehebung: Updated config/cache.yml to patch to provide required missing yaml configuration block. Cache System Bugfix.

Außerdem: 2 Merge- oder Finanzierungs-Commits des Teams (ohne Nutzen für Anwender; siehe die Hinweise im Abdeckungsnachweis).

## Upstream-Geschichte im Fork, nach Monat

Der Fork enthält die vollständige Upstream-Geschichte. Die Tabelle zählt sie nach Monat und Art; die größten Änderungen jedes Monats (nach berührten Dateien) werden genannt.

| Monat | Änderungen | Funktionen | Fehlerbehebungen | BC | Sicherheit | Leistung | Doku | Werkzeuge | Release | Ohne Nutzen | Größte Änderungen |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2023-12 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `4e93c5beb` IBX-7021: Fixed fatal error in DownloadController |

## Verwandte Seiten

- [Spezifikation des SQLite-Installers](../../spezifikationen/6.0/platform-sqlite-installer.md)
- [Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- [Release-Changelog](../../../changelogs/extensions/ezpublish-kernel.md)
- [Paketübersicht](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Upgrade-Hinweise](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)
- [Übersicht des Ökosystems](../../../eng-us/history/ecosystem.md)
- [Vollständiges Änderungsverzeichnis dieses Repositorys](../aenderungsverzeichnis/ezpublish-kernel.md)
- [Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- Plattform-Ökosystem nach Monat: [2023-12](monate/2023-12.md), [2025-06](monate/2025-06.md), [2025-07](monate/2025-07.md), [2025-08](monate/2025-08.md), [2025-09](monate/2025-09.md), [2026-01](monate/2026-01.md), [2026-02](monate/2026-02.md), [2026-03](monate/2026-03.md), [2026-04](monate/2026-04.md)
