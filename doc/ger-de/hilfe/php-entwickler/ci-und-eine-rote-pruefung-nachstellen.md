---
title: CI und das Nachstellen einer roten Prüfung
audience: php-developer, developer
category: php-development
order: 120
summary: Die Continuous Integration des Exponential-Repositorys aus der Sicht eines PHP-Entwicklers - die Workflows Quality, PHPUnit, Performance und CI health, was blockiert, wie ein Pull Request getestet wird und der lokale Befehl für jeden fehlgeschlagenen Schritt.
keywords: CI, Continuous Integration, rote Prüfung, Fehlschlag nachstellen, Datenbanktests, PHPUnit-Matrix, continuous integration, GitHub Actions, quality.yml, phpunit.yml, performance.yml, ci-health.yml, php80 job, red check, gh run view, log-failed, reproduce failure, COMPOSER_ROOT_VERSION, database tests, PHPUnit matrix
type: help
---
# CI und das Nachstellen einer roten Prüfung

Diese Seite richtet sich an PHP-Entwickler, deren Commit oder Pull Request an `se7enxweb/exponential` eine rote Prüfung
bekommen hat. Sie erklärt, was läuft, was blockiert, und nennt den Befehl, der jeden fehlgeschlagenen Schritt auf Ihrer
Maschine nachstellt. Die vollständige Referenz ist [Continuous Integration: die Workflows](../../bc/6.0/ci.md).

## Inhalt

1. [Die Workflows](#die-workflows)
2. [Den fehlgeschlagenen Schritt finden](#den-fehlgeschlagenen-schritt-finden)
3. [Lokal nachstellen](#lokal-nachstellen)
4. [Warum CI und Ihre Maschine sich uneinig sind](#warum-ci-und-ihre-maschine-sich-uneinig-sind)
5. [Pull Requests](#pull-requests)

## Die Workflows

| Workflow | Läuft | Jobs | Blockiert |
|---|---|---|---|
| Quality (`quality.yml`) | bei jedem Push auf `main` und jedem Pull Request | Syntax, Manifest, Commit-Nachrichten, Code-Stil (PHP 8.3); PHPStan (8.3); Tests der Code-Stil-Sniffs (8.4); PHP-8.0-Syntax und Boot | ja; der Code-Stil ist eine Warnung, das Manifest bei Pull Requests eine Warnung |
| PHPUnit (`phpunit.yml`) | bei jedem Push und Pull Request | PHPUnit ohne Datenbank unter PHP 8.1 bis 8.5; Datenbanktests auf MySQL 8.0 und PostgreSQL 15 (PHP 8.3) | ja |
| Performance (`performance.yml`) | Änderungen an PHP, Templates, Einstellungen oder dem Benchmark | Mikro-Benchmark (PHP 8.5) gegen `tests/benchmark/baseline.json` | nein |
| CI health (`ci-health.yml`) | montags und von Hand | ein Bericht über Erfolgsquoten und instabile Tests | nein |

## Den fehlgeschlagenen Schritt finden

```bash
gh run list -R se7enxweb/exponential --limit 20
gh run view <run id> -R se7enxweb/exponential --log-failed      # only the failed steps' log
gh run rerun <run id> -R se7enxweb/exponential --failed         # run only the failed jobs again
```

## Lokal nachstellen

Führen Sie es im Wurzelverzeichnis eines Checkouts desselben Commits aus; `BASE` ist für einen Pull Request `origin/main`.

| Fehlgeschlagener Schritt | Befehl |
|---|---|
| PHP-Syntax geänderter Dateien | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.php' \| xargs -r -n1 php -l` |
| JavaScript-Syntax | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.js' ':!*.min.js' \| xargs -r -n1 node --check` |
| Datei-Manifest | `php bin/php/checkmanifest.php --all`, beheben mit `make manifest-fix` |
| Commit-Nachrichten | `sh .githooks/check-commit-range $BASE HEAD` |
| Code-Stil | `make phpcs BASE=$BASE` |
| PHPStan | einmal `make devtools`, dann `make phpstan` |
| Tests der Code-Stil-Sniffs | `vendor/bin/phpunit --testsuite quality --no-coverage` (PHP 8.4) |
| PHP-8.0-Syntax jeder Datei | `make lint80-all PHP80=/path/to/php8.0` |
| produktive Installation unter PHP 8.0 | in einem sauberen Checkout mit PHP 8.0: `composer install --no-dev --prefer-dist` |
| Boot-Smoke-Test | `php bin/php/ezexec.php tests/bin/bootsmoke.php` mit PHP 8.0 |
| PHPUnit | `php vendor/bin/phpunit <the failed test file>`, dann `php vendor/bin/phpunit` |
| Datenbanktests | `php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test` gegen eine Wegwerf-Datenbank |
| Mikro-Benchmark | `php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks` mit ausgeschaltetem OPcache |

Führen Sie den Abhängigkeitsschritt der PHPUnit-CI (`composer update` auf einer kopierten `composer.json`) niemals
innerhalb einer installierten Site aus: Er schreibt `vendor/` neu.

## Warum CI und Ihre Maschine sich uneinig sind

| Situation | Übliche Ursache | Was zu tun ist |
|---|---|---|
| ein Test besteht lokal, schlägt in der CI fehl | der Test liest etwas aus Ihrer Installation: `settings/override`, eine Datenbank, einen Cache in `var/` | führen Sie ihn in einem frischen Klon nur mit installiertem `vendor/` aus |
| ein Test besteht allein, schlägt im ganzen Lauf fehl | gemeinsamer Zustand mit einem anderen Test: ein statischer Cache, ein im Prozess gesetzter INI-Wert, eine Datei unter `var/` | führen Sie die beiden Dateien zusammen aus; setzen Sie den Zustand in `tearDown()` zurück |
| jeder PHPUnit-Zweig schlägt bei der Installation fehl | ein Release einer Abhängigkeit hat die Auflösung geändert, oder ein neu gepinntes Paket ist noch nicht auf Packagist | warten Sie auf das Release auf Packagist; prüfen Sie die Einschränkung |
| nur der Zweig PHP 8.1 schlägt fehl | eine API von PHPUnit 10.5 oder ein Verhalten von PHP 8.1 | lesen Sie das Log des Zweigs; vermeiden Sie neuere PHPUnit-APIs |
| nur `php80` schlägt fehl | Syntax oder eine Funktion neuer als 8.0, oder eine Abhängigkeit, die 8.1 braucht | [Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md) |

Die Jobs setzen `COMPOSER_ROOT_VERSION=dev-main`, weil ein Pull Request als losgelöster Merge-Commit ausgecheckt wird,
bei dem Composer das Wurzelpaket sonst `dev-<sha>` nennen würde und Pakete, die `se7enxweb/exponential: ^6.0.14`
verlangen, nicht installiert werden könnten.

## Pull Requests

- Ein Pull Request wird **als in `main` gemergt** getestet. Ein Branch, der grün war, kann rot werden, wenn sich `main`
  bewegt: Mergen Sie `main` in den Branch und pushen Sie erneut.
- Ein Pull Request, der mit `main` in Konflikt steht, bekommt **überhaupt keine Prüfungen**; lösen Sie den Konflikt.
- Ein neuer Push auf den Branch **bricht** den Lauf **ab**, den er ersetzt; ein abgebrochener Lauf ist kein Fehlschlag.
- Composer-Installationen werden bis zu dreimal versucht; jeder Job hat eine Zeitgrenze von 20 oder 30 Minuten.

## Verwandte Seiten

- [Spezifikation der Continuous Integration](../../spezifikationen/6.0/kontinuierliche-integration.md)
- [Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md)
- [Datenbank- und Live-Tests](datenbank-und-live-tests.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
