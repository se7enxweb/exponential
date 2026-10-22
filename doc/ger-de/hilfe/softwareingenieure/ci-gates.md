---
title: CI-Gates
audience: software-engineer, developer
category: software-engineering
order: 530
summary: Die Continuous Integration von Exponential als Satz von Gates - die Workflows Quality, PHPUnit, Performance und CI health, was blockiert und was nur warnt, wie Pull-Requests als in main gemergt getestet werden, wie Sie einen roten Schritt auf Ihrem Rechner reproduzieren, die lokalen Gates, die die CI spiegeln, und die verbleibenden Unterschiede.
keywords: CI, Continuous Integration, GitHub Actions, quality.yml, phpunit.yml, performance.yml, ci-health.yml, Gates, rote Prüfung, reproduzieren, Pull-Request, Merge, wackelige Tests, Packagist-Prüfung, Softwareingenieur, continuous integration, gates, red check, reproduce, pull request, flaky tests, Packagist check
type: help
---
# CI-Gates

Diese Seite richtet sich an Ingenieure, deren Änderung in der CI rot ist, die einen Pull-Request prüfen oder die Workflows
ändern. Sie fasst [Continuous Integration](../../bc/6.0/ci.md) zusammen, das die vollständige Tabelle der Schritte und
Befehle enthält.

## Inhalt

1. [Die Workflows](#die-workflows)
2. [Eigenschaften, die jeder Ingenieur kennen sollte](#eigenschaften-die-jeder-ingenieur-kennen-sollte)
3. [Einen roten Schritt reproduzieren](#einen-roten-schritt-reproduzieren)
4. [Die lokalen Gates](#die-lokalen-gates)
5. [Den Zustand der CI beobachten](#den-zustand-der-ci-beobachten)
6. [Bekannte Lücken](#bekannte-lücken)

## Die Workflows

| Workflow | Läuft bei | Was | Blockiert |
|---|---|---|---|
| Quality (`quality.yml`) | jedem Push auf `main` und jedem Pull-Request dorthin | Syntax geänderter PHP- und JavaScript-Dateien, Dateimanifest, Commit-Nachrichten, Codestil, PHPStan, Sniff-Tests, PHP-8.0-Syntax, Installation und Start | ja; der Codestil warnt, das Manifest warnt bei einem Pull-Request |
| PHPUnit (`phpunit.yml`) | jedem Push und Pull-Request, ohne Pfadfilter | PHPUnit ohne Datenbank auf PHP 8.1 bis 8.5; Datenbanktests auf MySQL 8.0 und PostgreSQL 15 (PHP 8.3) | ja |
| Performance (`performance.yml`) | Änderungen an PHP, Templates, Einstellungen, der deutschen Übersetzung oder dem Benchmark | der Micro-Benchmark gegen `tests/benchmark/baseline.json` | nein |
| CI health (`ci-health.yml`) | montags und von Hand | ein Bericht über Erfolgsquoten, fehlschlagende Schritte, fehlschlagende und wackelige Tests | nein |

## Eigenschaften, die jeder Ingenieur kennen sollte

- **Ein Pull-Request wird als in `main` gemergt getestet.** Ein grüner Branch kann rot werden, wenn sich `main` bewegt;
  mergen Sie `main` in den Branch und pushen Sie erneut. Ein Pull-Request, der mit `main` in Konflikt steht, erhält gar
  keine Prüfungen.
- **Abhängigkeiten werden in den PHPUnit-Jobs frisch aufgelöst**, ein Paket-Release kann also ein Ergebnis ohne Änderung
  im Repository verändern.
- **Das Hauptpaket ist `dev-main`** (`COMPOSER_ROOT_VERSION=dev-main`), sodass Pakete, die `se7enxweb/exponential` mit einer
  Bedingung verlangen, bei einem Pull-Request auflösen.
- **Nur ein überholter Lauf wird abgebrochen.** Ein abgebrochener Lauf bei einem Pull-Request ist kein Fehlschlag; sehen
  Sie sich den neuesten Lauf an.
- **Jobs haben Zeitlimits**, und Netzwerkschritte wiederholen sich, sodass ein hängender Job fehlschlägt, statt die
  Warteschlange zu blockieren.
- **Die Jobs haben keine installierte Website**: kein `settings/override`, keine Datenbank, keine Caches in `var/`.

## Einen roten Schritt reproduzieren

```sh
gh run list -R se7enxweb/exponential --limit 20
gh run view <run id> -R se7enxweb/exponential --log-failed
```

Führen Sie dann den Befehl des Schritts in einem Checkout desselben Commits aus:

| Fehlgeschlagener Schritt | Befehl |
|---|---|
| PHP-Syntax | `php -l` auf die geänderten Dateien |
| Dateimanifest | `php bin/php/checkmanifest.php --all`; korrigieren mit `make manifest-fix` |
| Commit-Nachrichten | `sh .githooks/check-commit-range $BASE HEAD` |
| Codestil | `make phpcs BASE=$BASE` |
| PHPStan | einmal `make devtools`, dann `make phpstan` |
| Sniff-Tests | `vendor/bin/phpunit --testsuite quality --no-coverage` |
| PHP-8.0-Syntax | `make lint80-all PHP80=/path/to/php8.0` |
| Start-Rauchtest | `php bin/php/ezexec.php tests/bin/bootsmoke.php` mit PHP 8.0 |
| PHPUnit | `php vendor/bin/phpunit <the failed test file>`, dann `php vendor/bin/phpunit` |
| Datenbanktests | `php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test` |
| Micro-Benchmark | `php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks` |

Ein Test, der auf Ihrer Installation besteht und in der CI fehlschlägt, liest meist etwas aus Ihrer Installation. Führen
Sie ihn in einem frischen Klon aus, in dem nur `vendor/` installiert ist, bevor Sie entscheiden, dass die CI falsch liegt.

## Die lokalen Gates

Das Push-Gate des Teams führt die Quality-Prüfungen und den PHPUnit-Job ohne Datenbank gegen eine committete Revision in
einem sauberen Worktree aus, in drei Modi: `--fast` (die Tests der Änderung), Standard (der ganze Lauf ohne Datenbank) und
`--ci` (zusätzlich PHP-8.0-Syntax jeder Datei und die Installation unter PHP 8.0 mit dem Starttest). Es prüft außerdem,
dass jede geänderte Bedingung für `se7enxweb/*` in `composer.json` zu einem Release passt, das Packagist bereits listet,
weil ein Festlegen auf eine unveröffentlichte Version jeden CI-Zweig auf einmal bricht. Die Befehle, die es ausführt, liegen
alle im Repository; Mitwirkende führen sie als einfache Befehle aus ([Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md)).

Verbleibende Unterschiede: Das lokale Gate führt PHPUnit auf einer PHP-Version gegen das installierte `vendor/` aus und
führt weder die Datenbankjobs noch den Benchmark aus.

## Den Zustand der CI beobachten

```sh
python3 .github/scripts/ci_health_report.py --days 14
```

gibt die Erfolgsquote je Workflow und Ereignis aus, die Schritte und Tests, die am häufigsten fehlschlagen, und die
wackeligen Läufe. Eine Ausgangsbasis vom Oktober 2026 ist auf der CI-Seite festgehalten, sodass spätere Berichte damit
verglichen werden können.

## Bekannte Lücken

- `main` hat keine Branch-Protection-Regel; eine rote Prüfung blockiert ein Merge also technisch nicht, sondern per
  Richtlinie.
- Datenbankjobs decken nur MySQL und PostgreSQL ab; PHPUnit läuft nicht auf PHP 8.0.
- Codestil und Leistung sind nur Warnungen; die Abdeckung wird nicht veröffentlicht.

## Verwandte Seiten

- [Teststrategie](teststrategie.md)
- [Statische Analyse und Codestil](statische-analyse-und-codestil.md)
- [CI für Erweiterungen](../ersteller/anwendungsentwickler/ci-fuer-erweiterungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
