---
title: Continuous Integration für Erweiterungen
audience: app-creator, developer
category: app-creation
order: 510
summary: CI für eine Erweiterung so einrichten, wie es der Kernel tut - GitHub-Actions-Jobs für die PHP-Syntax auf jeder unterstützten Version, PHPUnit, statische Analyse, Prüfung der Commit-Nachrichten, minimale Rechte und Tests gegen den Kernel von Exponential.
keywords: CI, Continuous Integration, GitHub Actions, Workflow, PHP-Matrix, statische Analyse, Commit-Nachrichten, Rechte, Anwendungsentwickler, continuous integration, workflow, PHP matrix, PHPUnit, PHPStan, phpcs, lint, commit messages, permissions, app creator
type: help
---
# Continuous Integration für Erweiterungen

Diese Seite ist für Anwendungsentwickler, die jeden Push ihrer Erweiterung automatisch prüfen lassen möchten. Die
eigenen Workflows des Kernels in `.github/workflows/` sind ein erprobtes Vorbild
([GitHub-Actions-Workflows](../../../../eng-us/standards/testing-and-ci.md#github-actions-workflows)).

## Inhalt

1. [Was die CI des Kernels tut](#was-die-ci-des-kernels-tut)
2. [Ein Workflow für eine Erweiterung](#ein-workflow-für-eine-erweiterung)
3. [Tests gegen den Kernel](#tests-gegen-den-kernel)
4. [Regeln](#regeln)

## Was die CI des Kernels tut

| Workflow | Prüft |
|---|---|
| `quality.yml` | PHP- und JavaScript-Syntax geänderter Dateien, das Dateimanifest, Commit-Nachrichten, Code-Stil (Warnung), PHPStan |
| `phpunit.yml` | PHPUnit auf PHP 8.1 bis 8.5 und einen Job mit MySQL und PostgreSQL |
| `performance.yml` | einen Mikro-Benchmark gegen eine Baseline (nur Warnung) |
| `ci-health.yml` | einen wöchentlichen Bericht über Erfolgsquoten und instabile Tests |

## Ein Workflow für eine Erweiterung

```yaml
# .github/workflows/ci.yml
name: CI
on: [push, pull_request]
permissions:
  contents: read
jobs:
  lint:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: ['8.0', '8.1', '8.2', '8.3', '8.4', '8.5']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
      - run: find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-interaction
      - run: vendor/bin/phpunit tests/
```

Prüfen Sie die Syntax auf jeder PHP-Version, die der Kernel unterstützt (8.0 bis 8.5,
[Anforderungen](../../../installation/02-voraussetzungen.md)), denn Installationen laufen auf allen davon.

## Tests gegen den Kernel

Tests, die Kernel-Klassen brauchen, laufen in einem Checkout von `se7enxweb/exponential`, in dessen `extension/` Ihre
Erweiterung liegt: Checken Sie beides aus, führen Sie im Kernel `composer install` aus, kopieren oder verlinken Sie Ihre
Erweiterung, erzeugen Sie die Autoloads neu und führen Sie dann Ihre Tests mit dem Bootstrap aus der `phpunit.xml` des
Kernels aus. Halten Sie diese Tests nach Möglichkeit frei von Datenbankzugriffen
([PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md)).

## Regeln

- Minimale Rechte: `permissions: contents: read`; kein `pull_request_target`.
- Ein Timeout pro Job.
- Versionen der Actions festlegen; bewusst aktualisieren.
- Keine Geheimnisse in Logs; Geheimnisse nur dort, wo ein Job sie wirklich braucht, und nie bei Pull Requests aus Forks.
- Eine rote CI blockiert das Release-Tag ([Versionen und Releases](versionen-und-releases.md)).

## Verwandte Seiten

- [Anwendungen testen](anwendungen-testen.md)
- [Qualitätsprüfungen](../../../../eng-us/features/6.0/quality-checks.md)
- [Verhaltensänderungen der CI](../../../bc/6.0/ci.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
