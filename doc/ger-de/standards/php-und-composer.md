---
title: Standards für PHP, PHP-FIG und Composer
audience: developer, admin
category: standards
order: 70
summary: Welche PHP-Versionen Exponential unterstützt, welche Empfehlungen der PHP-FIG (PSR-1 bis PSR-20) es befolgt oder nicht, wie es Composer und Packagist nutzt und wie sich seine Versionsnummern zu Semantic Versioning verhalten.
keywords: PHP-Versionen, Codierstandard, Autoloading, Klassenzuordnung, Versionsnummern, Versionsbeschränkung, PHP, PHP 8.0, PHP 8.5, PHP-FIG, PSR, PSR-1, PSR-2, PSR-3, PSR-4, PSR-6, PSR-7, PSR-11, PSR-12, PSR-14, PSR-15, PSR-16, PSR-17, PSR-18, PER Coding Style, Composer, Packagist, Semantic Versioning, SemVer, autoload, class map, version constraint
type: reference
---
# Standards für PHP, PHP-FIG und Composer

Diese Seite richtet sich an Entwickler, die Code für Exponential schreiben oder Extensions dafür paketieren, und an
Administratoren, die eine PHP-Version wählen oder mit Composer installieren. Sie beantwortet: Welche PHP-Versionen
werden unterstützt und getestet, welche Standardempfehlungen der PHP-FIG (PSRs) befolgt der Code, wie werden Composer
und Packagist genutzt, und wie verhalten sich die Versionsnummern von Exponential zu Semantic Versioning. Jede Aussage
nennt die Datei, anhand derer sie geprüft wurde.

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| Unterstützte PHP-Versionen | PHP 8.0 bis 8.5 | **Erfüllt** (getestet) | `composer.json` `"php"`, `.github/workflows/quality.yml` Job `php80`, Matrix von `phpunit.yml` 8.1 bis 8.5 |
| PSR-1 Basic Coding Standard | PSR-1 | **Teilweise erfüllt** | `<?php`-Tags, keine Seiteneffekte in Klassendateien; Klassennamen wie `expHelpAudience` sind kein StudlyCaps |
| PSR-2 / PSR-12 / PER Coding Style | PSR-12, PER-CS 2.0 | **Nicht erfüllt** (bewusst) | `phpcs.xml.dist` erzwingt den eigenen Stil des Kernels |
| PSR-4 Autoloading | PSR-4 | **Nicht erfüllt** | kein `psr-4` in `composer.json`; generierte Klassenzuordnungen `autoload/ezp_kernel.php` |
| PSR-3 Logger Interface | PSR-3 | **Nicht erfüllt** (nicht verwendet) | kein `Psr\Log` in `kernel/`, `lib/`, `bin/` |
| PSR-6 / PSR-16 Caching | PSR-6, PSR-16 | **Nicht erfüllt** (nicht verwendet) | kein `Psr\Cache`, `Psr\SimpleCache` |
| PSR-7 / PSR-15 / PSR-17 / PSR-18 HTTP | - | **Nicht erfüllt** (nicht verwendet) | kein `Psr\Http` im Code des Repositorys oder in Exponential Velocity |
| PSR-11 Container | PSR-11 | **Nicht erfüllt** (nicht verwendet) | kein `Psr\Container` |
| PSR-14 Event Dispatcher | PSR-14 | **Nicht erfüllt** (nicht verwendet) | kein `Psr\EventDispatcher` |
| Schema von `composer.json` | Composer 2 | **Erfüllt** | `composer validate` meldet „./composer.json is valid“ |
| Packagist | - | **Erfüllt** | `se7enxweb/exponential` und jede Abhängigkeit `se7enxweb/*` werden über Packagist aufgelöst |
| Semantic Versioning | SemVer 2.0.0 | **Teilweise erfüllt** | der Kernel `6.0.x` ist dreiteilig; viele Extensions und Velocity verwenden vier Teile (`0.0.4.10`) |

## Unterstützte PHP-Versionen

**Was es ist.** PHP wird von der PHP Group gepflegt; jede Minor-Version erhält zwei Jahre aktiven Support und ein oder
zwei Jahre Sicherheitskorrekturen.

**Wie Exponential es erfüllt.**

- `composer.json` `require`: `"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"`. Die
  Bereiche für 8.6 bis 8.8 sind vorausschauend; diese Versionen gibt es noch nicht, und sie sind nicht getestet.
- **PHP 8.0 ist die älteste unterstützte Version**, weil es das Standard-PHP von Red Hat Enterprise Linux 9 ist; siehe
  [Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md). PHP-Funktionen, die neuer als 8.0 sind, werden in
  `lib/phpcompat.php` definiert, das `autoload.php` nur lädt, wenn `PHP_VERSION_ID < 80100`.
- Exponential Velocity, der Anwendungsserver, läuft ab seinem Release 0.0.4.54 auf PHP 8.0 bis 8.5 und steht mit
  `~0.0.4.54` in `require` (bis dahin brauchte es PHP 8.1 und stand nur in `suggest`).
- Erforderliche PHP-Erweiterungen in `composer.json`: `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`,
  `reflection`, `session`, `spl`, `simplexml`. Datenbank- und Bilderweiterungen stehen in `suggest`.

**Wie es getestet wird** (`.github/workflows/`):

| Prüfung | PHP | Workflow, Job |
|---|---|---|
| Syntax jeder PHP-Datei in git, Produktions-`composer install --no-dev`, Boot-Smoke-Test (`tests/bin/bootsmoke.php`) | 8.0 | `quality.yml`, `php80` |
| PHPCompatibility, `testVersion` `8.0-` | (statisch) | `phpcs.xml.dist`, Code-Style-Schritt, der nur warnt |
| PHPUnit ohne Datenbank | 8.1, 8.2, 8.3, 8.4, 8.5 | `phpunit.yml`, `phpunit` |
| PHPUnit mit MySQL 8.0 und PostgreSQL 15 | 8.3 | `phpunit.yml`, `phpunit-db` |
| PHPStan | 8.3 (Analyse `phpVersion: 80100`) | `quality.yml`, `phpstan` |
| Micro-Benchmark | 8.5 | `performance.yml`, `micro` |

**Status: Erfüllt** für PHP 8.0 bis 8.5.

**Bekannte Lücken.**

- PHPUnit läuft nicht unter PHP 8.0 (PHPUnit 10 und später brauchen 8.1); 8.0 wird nur durch Syntax, Installation und
  Bootvorgang abgedeckt.
- `phpstan.neon.dist` analysiert als PHP 8.1 (`phpVersion: 80100`), nicht als 8.0.
- [Standardverteilung der Extensions](../funktionen/6.0/standard-extension-auslieferung.md) gibt die PHP-Anforderung noch
  mit `^8.1 || ^8.2 ...` an; `composer.json` sagt `^8.0`.

## Empfehlungen der PHP-FIG (PSRs)

**Was sie sind.** Die PHP Framework Interop Group (PHP-FIG) veröffentlicht PHP Standard Recommendations. Der Kernel von
Exponential wurde von eZ Publish 4 übernommen, das älter ist als die meisten von ihnen; das Projekt hat sich
entschieden, den Codestil dieses Kernels beizubehalten und die Schnittstellen-PSRs nicht zu übernehmen.
`CONTRIBUTING.md` sagt noch „Follow the PSR standards“; die tatsächlich geprüften Regeln sind die von `phpcs.xml.dist`
(siehe [Codierstandards](programmierstandards.md)).

### PSR-1, Basic Coding Standard

- Dateien beginnen mit dem vollständigen Tag `<?php` (`Generic.PHP.DisallowShortOpenTag` in `phpcs.xml.dist`) und sind
  UTF-8-kodiert.
- Klassendateien deklarieren Klassen und erzeugen keine Ausgabe; Skripte in `bin/php/` sind davon getrennt.
- Klassennamen folgen **nicht** StudlyCaps: `expHelpAudience`, `eZContentObject`, `ezpAutoloader` beginnen mit einem
  kleingeschriebenen Präfix.
- Klassenkonstanten sind großgeschrieben (`ExponentialSDK::VERSION_MAJOR` in `lib/version.php`). Methoden sind
  camelCase.
- PSR-1 verlangt, dass Klassen für das Autoloading nach PSR-0 oder PSR-4 in einem Namespace liegen; die Kernel-Klassen
  sind global.

**Status: Teilweise erfüllt.**

### PSR-2 (veraltet), PSR-12 und PER Coding Style

`phpcs.xml.dist` erzwingt bei mehreren Regeln von PSR-12 absichtlich das Gegenteil: ein Leerzeichen innerhalb von
Klammern (`foo( $a )`, `if ( $a )`), `array( ... )` statt `[ ... ]`, öffnende geschweifte Klammern von Funktionen in
einer eigenen Zeile (das hat PSR-12 auch, aber zusätzlich für Klassen und nicht für Kontrollstrukturen) und keine
Typdeklarationen. Es verwendet einen Sniff von PSR-2 (`PSR2.ControlStructures.ControlStructureSpacing`) mit
geänderten Eigenschaften.

**Status: Nicht erfüllt**, beabsichtigt.

### PSR-4, Autoloading Standard

- Die `composer.json` im Wurzelverzeichnis hat keinen Abschnitt `psr-4` oder `psr-0`; ihr `autoload` führt nur
  `files: ["autoload.php"]` auf.
- `autoload.php` registriert `ezpAutoloader` (Klassenzuordnungen) und `ezcBase::autoload` (Zeta Components).
- Die Klassenzuordnung des Kernels `autoload/ezp_kernel.php` und die Klassenzuordnung der Extensions (unter
  `var/autoload/`) werden von `bin/php/ezpgenerateautoloads.php` generiert; `post-install-cmd` und `post-update-cmd`
  von Composer führen es aus (`"legacy-scripts"` in `composer.json`).
- Etwa 390 Dateien in `kernel/` und `lib/` deklarieren einen Namespace (zum Beispiel `Exponential\Command\Kernel`,
  `Exponential\View\Kernel\Content`), aber ihre Pfade (etwa `kernel/private/classes/commands/clusterpurge.php`) bilden
  den Namespace nicht ab; auch sie werden über die Klassenzuordnung geladen.

**Status: Nicht erfüllt.** Klassenzuordnungen sind mit jeder Dateistruktur kompatibel, weshalb der Kernel sie verwendet.

> **Hinweis** Führen Sie nach dem Hinzufügen oder Umbenennen einer Kernel-Klasse `php bin/php/ezpgenerateautoloads.php -k`
> aus, für eine Klasse einer Extension `-e`. Eine Klasse, die in der Zuordnung fehlt, wird nicht gefunden.

### PSR-3, PSR-6, PSR-7, PSR-11, PSR-14, PSR-15, PSR-16, PSR-17, PSR-18

Eine Suche in `kernel/`, `lib/`, `bin/` und den Extensions im Repository nach `Psr\Log`, `Psr\Http`, `Psr\Container`,
`Psr\Cache`, `Psr\SimpleCache`, `Psr\EventDispatcher`, `Psr\Clock` und `Psr\Link` fand nichts.

| PSR | Was Exponential stattdessen verwendet |
|---|---|
| PSR-3 Logging | `eZDebug::writeError()`, `writeWarning()`, `writeNotice()`, `eZLog` und die Anfrageprotokolle |
| PSR-6 / PSR-16 Caching | Datei-Caches unter `var/`, in INI-Dateien konfigurierte Cache-Handler |
| PSR-7, PSR-15, PSR-17, PSR-18 HTTP | die eigene Anfrageverarbeitung des Kernels (`ezpKernelWeb`, `eZHTTPTool`); Velocity benötigt nur `php` und `ext-sockets` (laut seiner `composer.json`) und verwendet `Psr\Http` nicht |
| PSR-11 Container | statische Singletons wie `eZINI::instance()`, `eZDB::instance()` |
| PSR-14 Events | `ezpEvent` und die eigenen Listener des Kernels |

`vendor/psr/log` ist nur als Abhängigkeit von `mongodb/mongodb` vorhanden.

**Status: Nicht erfüllt** (nicht verwendet). Das bedeutet, dass sich Exponential nicht direkt an PSR-basierte
Bibliotheken anschließen lässt; dafür ist ein Adapter nötig.

## Composer

**Was es ist.** Composer ist der Abhängigkeitsmanager für PHP; `composer.json` folgt seinem veröffentlichten
JSON-Schema.

**Wie Exponential es erfüllt.**

- `composer validate --no-check-all --no-check-lock --no-check-publish` gibt für das Repository
  `./composer.json is valid` aus (für diese Seite ausgeführt).
- `name`: `se7enxweb/exponential`, `type`: `ezpublish-legacy` (der Typ, den das Legacy-Installer-Plugin versteht),
  `license`: `GPL-2.0-or-later` (ein SPDX-Bezeichner).
- `require` legt jede Extension `se7enxweb/*` mit einer Tilde-Beschränkung fest (`~6.0.12`, `~1.4.39`, `~1.0.0.46`):
  Die letzte Stelle ist variabel, die Stellen darüber nicht.
- `require-dev`: `phpunit/phpunit` `^9.6 || ^10.5 || ^11.5 || ^12.0 || ^13.4`, sonst nichts.
- `extra.branch-alias`: `"dev-main": "6.0.x-dev"`, sodass Pakete, die `se7enxweb/exponential: ^6.0.14` verlangen,
  gegen `main` aufgelöst werden können. Die CI setzt aus demselben Grund `COMPOSER_ROOT_VERSION=dev-main`.
- `config.allow-plugins` erlaubt nur die Legacy-Installer-Plugins.
- `composer.lock` wird nicht committet (`.gitignore`); jede Installation löst aus den Beschränkungen auf.
- Die Entwicklungswerkzeuge (PHP_CodeSniffer, PHPCompatibility, PHPStan) stehen in einer separaten
  `composer.dev.json` und werden mit `make devtools` in `.devtools/` installiert, nie in `vendor/`. Ihr
  `config.platform.php` ist `8.0.30`.

**Status: Erfüllt.**

## Packagist

**Was es ist.** Packagist.org ist das öffentliche Standard-Repository von Composer.

**Wie Exponential es erfüllt.**

- Das Wurzelpaket `se7enxweb/exponential` ist auf Packagist gelistet (seine `p2`-Metadaten antworteten am 18.10.2026).
- Für das GitHub-Repository ist ein Packagist-Webhook konfiguriert, sodass ein gepushter Tag Packagist erreicht (am
  18.10.2026 über die GitHub-API geprüft).
- Die Push-Prüfung kontrolliert, dass jede Beschränkung `se7enxweb/*` in `composer.json` zu einem Release passt, das
  Packagist bereits listet, bevor `composer update` in der CI daran scheitern würde
  ([Continuous Integration](../bc/6.0/ci.md), „Die lokalen Prüfungen und die CI“).

**Status: Erfüllt.**

> **Warnung** Packagist liest einen Tag einmal. Ein Tag, der verschoben wird, nachdem Packagist ihn gelesen hat, erzeugt
> zwei verschiedene Pakete mit derselben Version. Siehe [Versionsverwaltung und Releases](versionsverwaltung-und-releases.md).

## Semantic Versioning

**Was es ist.** Semantic Versioning 2.0.0 (semver.org) definiert `MAJOR.MINOR.PATCH`: genau drei Zahlen, mit der
Bedeutung jeder Erhöhung.

**Wie die Versionen von Exponential funktionieren.**

- **Der Kernel** wird als `6.0.x` versioniert: `lib/version.php` (`ExponentialSDK`, `VERSION_MAJOR = 6`,
  `VERSION_MINOR = 0`, `VERSION_RELEASE = 15`) und die Tags `v6.0.0` bis `v6.0.14`. 6.0.15 ist in Entwicklung
  ([Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)). Neue Funktionen landen in Patch-Releases der Linie 6.0, was
  nicht der Absicht von SemVer für PATCH entspricht.
- **Extensions** verwenden drei oder vier Stellen: `exp_adminui ~1.0.0.46`, `bcgooglesitemaps ~1.1.6.6`,
  `expai ~0.0.1.12` und Exponential Velocity `0.0.4.x`. Eine vierteilige Nummer ist kein gültiges SemVer 2.0.0, aber
  Composer akzeptiert bis zu vier numerische Stellen und vergleicht jede numerisch.
- **Die letzte Stelle zählt weiter.** Nach `0.0.4.9` kommt `0.0.4.10`, nie `0.0.5.0`
  ([Leitfaden zu Extensions](../anleitungen/extensions.md), „Veröffentlichen“;
  [Changelog von Velocity](../../changelogs/extensions/exponential-velocity.md)). Das Erhöhen einer höheren Stelle ist
  eine eigene Entscheidung darüber, was das Release bedeutet.
- **Tags sind dauerhaft**: Auf ein fehlerhaftes Release folgt ein neues, es wird nie neu erstellt.

**Status: Teilweise erfüllt.** Versionen sind geordnet und unveränderlich, und Composer behandelt sie korrekt, aber
vierteilige Nummern und neue Funktionen in Patch-Releases liegen außerhalb von SemVer 2.0.0.

> **Hinweis** Sortieren Sie Tags nach Version, nicht nach Name: `git tag -l 'v*' --sort=version:refname`. Nach Name
> sortiert steht `0.0.4.10` vor `0.0.4.8`.

## Bekannte Lücken

1. Kein Autoloading nach PSR-4; neue Klassen brauchen den Autoload-Generator.
2. Keine Schnittstellen von PSR-3, PSR-7, PSR-11, PSR-14 oder den Caching-PSRs; die Integration einer PSR-basierten
   Bibliothek braucht einen Adapter.
3. `CONTRIBUTING.md` verlangt „the PSR standards“ und verlinkt `CODING_STYLE.md`, das im Repository nicht existiert.
4. PHPUnit läuft nicht unter PHP 8.0; PHPStan analysiert als PHP 8.1.
5. Vierteilige Versionsnummern sind kein SemVer 2.0.0.
6. Die Beschränkung `php` nennt PHP-Versionen (8.6 bis 8.8), die nicht getestet sein können.

## Verwandte Seiten

- [Unterstützung für PHP 8.0](../bc/6.0/php-8.0-unterstuetzung.md)
- [Unterstützung für PHP 8](../bc/6.0/php8.md)
- [Codierstandards](programmierstandards.md)
- [Tests und Continuous Integration](tests-und-ci.md)
- [Versionsverwaltung und Releases](versionsverwaltung-und-releases.md)
- [Entwickeln mit Git](../hilfe/mit-git-entwickeln.md)
