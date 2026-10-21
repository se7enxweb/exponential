---
title: "Lektion 12.3: Prüfungen, bevor Sie eine Änderung teilen"
audience: trainer, developer
category: training
order: 123
summary: Die Teilnehmenden führen die Prüfungen aus, die eine Kernel-Änderung vor dem Push bestehen muss (Syntax, Dateimanifest, Commit-Nachrichten, Codestil, PHPStan und die Tests der Änderung), installieren die Git-Hooks und übertragen dieselben Gewohnheiten auf ihre Erweiterung. 45 Minuten.
keywords: entwickler, qualitätsprüfungen, php -l, checkmanifest, filelist.md5, präfixe für commit-nachrichten, Added Updated Fixed Removed Renamed, git-hooks, make hooks, make devtools, phpcs, checkcodestyle, phpstan, test-map, CI-gates, developers course, quality checks, commit message prefixes, git hooks, CI gates
type: help
---
# Lektion 12.3: Prüfungen, bevor Sie eine Änderung teilen

Eine Änderung, die den Build kaputt macht, kostet alle Zeit. Die Continuous Integration des Projekts führt bei jedem
Push und jedem Pull Request eine feste Liste von Prüfungen aus; dieselben Prüfungen laufen auf dem Rechner einer
Entwicklerin oder eines Entwicklers in wenigen Minuten. In dieser Lektion führen die Teilnehmenden sie aus,
installieren die Git-Hooks, die einige davon automatisch ausführen, und übernehmen dieselbe Routine für ihre eigene
Erweiterung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 12: Tests und Qualität](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 12.2](einen-test-schreiben.md); der Kernel als Git-Klon |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Syntax geänderter PHP-Dateien mit `php -l` und die von Templates mit `eztemplatecheck.php` prüfen;
- das Dateimanifest mit `bin/php/checkmanifest.php` prüfen und korrigieren;
- Commit-Nachrichten mit den Präfixen `Added:`, `Updated:`, `Fixed:`, `Removed:` und `Renamed:` schreiben;
- die Git-Hooks mit `make hooks` und die Entwicklungswerkzeuge mit `make devtools` installieren;
- die Tests einer Änderung ausführen und erklären, welche Prüfungen in CI blockieren.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Qualitätsprüfungen](../../../../../eng-us/features/6.0/quality-checks.md) und
[Tests und Continuous Integration](../../../../../eng-us/standards/testing-and-ci.md).

1. **Was CI prüft** (4 Minuten). Der Quality-Workflow: PHP-Syntax geänderter Dateien, JavaScript-Syntax, das
   Dateimanifest, Commit-Nachrichten, PHPStan (blockierend), Codestil (Warnung). Der PHPUnit-Workflow: die Suites
   unter PHP 8.1 bis 8.5 und ein Datenbank-Job.
2. **Das Manifest** (3 Minuten). `share/filelist.md5` führt eine Prüfsumme pro Datei der Distribution; die
   Upgrade-Prüfung vergleicht Installationen damit. Eine Kernel-Änderung aktualisiert es im selben Commit: `php
   bin/php/checkmanifest.php --staged` prüft, `--fix` schreibt es neu.
3. **Commit-Nachrichten** (2 Minuten). Der Betreff beginnt mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder
   `Renamed: ` und sagt, was die Änderung tut.
4. **Hooks und Werkzeuge** (3 Minuten). `make hooks` setzt `core.hooksPath` auf `.githooks`: Der Pre-Commit-Hook
   führt für vorgemerkte Dateien `php -l`, die Manifest- und die Codestil-Prüfung aus; commit-msg prüft den Betreff;
   pre-push führt PHPStan aus, wenn die Werkzeuge installiert sind. `make devtools` installiert PHP_CodeSniffer und
   PHPStan nach `.devtools/`.
5. **Tests einer Änderung** (3 Minuten). Die Befehle in [Qualitätsprüfungen](../../../../../eng-us/features/6.0/quality-checks.md)
   wählen die Testdateien aus, die die geänderten Dateien abdecken, und führen jede in einem eigenen Prozess aus.

### Häufige Fragen

**Braucht meine Erweiterung ein Manifest?** Nur wenn sie eines mitliefert, wie es einige Erweiterungen für die
Upgrade-Prüfung tun. Das Manifest des Kernels deckt die Dateien des Kernels ab.

**Warum ist der Codestil nur eine Warnung?** Alter Code folgt dem Stil nicht überall; gemeldet werden nur neue
Verstöße in geänderten Dateien.

**Muss ich die Hooks installieren?** Sie werden pro Klon eingeschaltet und sind empfohlen: Sie fangen Fehler ab,
bevor ein Commit existiert.

### Stolperfallen

- **Pushen, ohne die Prüfungen auszuführen.** CI schlägt fehl, und die Änderung wartet. Führen Sie die Befehle für
  „vor dem Push“ aus.
- **Das Manifest von Hand korrigieren.** Verwenden Sie `--fix`; Handarbeit geht leicht schief.
- **Eine Commit-Nachricht über Werkzeuge.** Beschreiben Sie, was sich im Produkt geändert hat und warum, nicht welches
  Werkzeug verwendet wurde.

## Praktische Übung

Im Kernel-Klon Ihrer Entwicklungsinstallation, auf Ihrem Zweig `my-dev`.

1. Installieren Sie die Hooks: `make hooks`, und prüfen Sie: `git config core.hooksPath`.
2. Nehmen Sie eine kleine Änderung an einer Dokumentationsseite in `doc/` vor (korrigieren Sie einen Tippfehler, den
   Sie im Kurs gefunden haben) und merken Sie sie vor.
3. Prüfen Sie das Manifest für die vorgemerkten Dateien: `php bin/php/checkmanifest.php --staged`.
4. Committen Sie zuerst mit einem falschen Betreff: `git commit -m "typo"`. Lesen Sie die Ablehnung des Hooks.
5. Committen Sie mit einem korrekten Betreff: `git commit -m "Fixed: Typo in <page name>"`.
6. Prüfen Sie die Syntax der seit `origin/main` geänderten PHP-Dateien:
   `git diff --name-only --diff-filter=AM origin/main..HEAD -- '*.php' | xargs -r -n1 php -l`.
7. Prüfen Sie die Commit-Nachrichten Ihres Zweigs: `sh .githooks/check-commit-range origin/main HEAD`.
8. Schreiben Sie für Ihre Erweiterung dieselbe Routine als kurze Checkliste auf: `php -l` der geänderten PHP-Dateien,
   `eztemplatecheck.php` des Design-Ordners, die Tests der Erweiterung, ein Commit-Betreff mit Präfix.

## Erwartetes Ergebnis

- Schritt 1 gibt `.githooks` aus.
- Schritt 3 meldet die Dokumentationsdatei als veraltet oder fehlend im Manifest oder besteht, wenn Dokumentation
  nicht aufgeführt ist; mit `--fix` wird das Manifest aktualisiert.
- Schritt 4 wird mit einer Meldung zum Betreff abgelehnt; Schritt 5 wird angenommen.
- Schritt 7 besteht für den Commit aus Schritt 5.
- Schritt 8: Alle Teilnehmenden haben eine schriftliche Checkliste für `mysite_hello`.

## Quiz

1. Mit welchen fünf Präfixen darf ein Commit-Betreff beginnen?
2. Welche Prüfung ist in CI nur eine Warnung?
   A. PHPStan B. Codestil C. Commit-Nachrichten D. PHP-Syntax
3. Welcher Befehl schreibt veraltete Prüfsummen des Dateimanifests neu?
4. Was tut `make hooks`?
5. Warum führen Sie die Tests einer Änderung vor dem vollständigen Lauf aus?

<details>
<summary>Antworten</summary>

1. `Added:`, `Updated:`, `Fixed:`, `Removed:` und `Renamed:`.
2. **B.** Codestil; gemeldet werden nur neue Verstöße in geänderten Dateien.
3. `php bin/php/checkmanifest.php --fix` (oder `make manifest-fix`).
4. Es setzt `git config core.hooksPath .githooks`, sodass die Hooks des Projekts bei Commit und Push laufen.
5. Sie sind in wenigen Minuten fertig und finden die meisten Probleme; der vollständige Lauf bleibt die Prüfung vor
   einem Release oder einer großen Änderung.

</details>

## Weiterführende Literatur

- [Qualitätsprüfungen](../../../../../eng-us/features/6.0/quality-checks.md).
- [Prüfung der Dateikonsistenz](../../../../../eng-us/features/6.0/file-consistency-check.md): wofür das Manifest da ist.
- [Versionsverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md).

## Verwandte Seiten

- [Modul 12: Tests und Qualität](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
