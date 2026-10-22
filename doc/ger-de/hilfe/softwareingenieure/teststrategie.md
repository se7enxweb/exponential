---
title: Teststrategie
audience: software-engineer, developer
category: software-engineering
order: 500
summary: Wie Exponential getestet wird und wie Sie Ihre eigenen Änderungen testen - die Schichten von Syntaxprüfungen und statischer Analyse über PHPUnit-Suiten ohne Datenbank, Datenbanktests, Tests gegen laufende Installationen, Design- und Qualitätstests und Benchmarks bis zu manuellen Prüfungen - was wo läuft (Hooks, lokale Gates, CI) und wie Sie die Tests für eine Änderung wählen.
keywords: Teststrategie, PHPUnit, Testsuiten, ohne Datenbank, Datenbanktests, Live-Tests, Designtests, Qualitätstests, Testpyramide, test-map.json, Hooks, CI, Regression, Softwareingenieur, testing strategy, test suites, DB-less, database tests, live tests, design tests, quality tests, test pyramid
type: help
---
# Teststrategie

Diese Seite richtet sich an Ingenieure, die die Tests einer Änderung planen, prüfen, ob eine Änderung ausreichend getestet
ist, oder bewerten, wie gut Exponential selbst getestet ist. Der Standard mit Zahlen und bekannten Lücken ist
[Tests und Continuous Integration](../../standards/tests-und-ci.md).

## Inhalt

1. [Die Schichten](#die-schichten)
2. [Was wo läuft](#was-wo-läuft)
3. [Die Tests für eine Änderung wählen](#die-tests-für-eine-änderung-wählen)
4. [Wie ein guter Test hier aussieht](#wie-ein-guter-test-hier-aussieht)
5. [Bekannte Lücken](#bekannte-lücken)

## Die Schichten

| Schicht | Werkzeug | Findet | Geschwindigkeit |
|---|---|---|---|
| Syntax | `php -l` (PHP 8.3 und 8.0), `node --check` | Parse-Fehler, Syntax neuer als PHP 8.0 | Sekunden |
| Dateimanifest | `bin/php/checkmanifest.php` | eine Datei, deren Prüfsumme in `share/filelist.md5` fehlt oder veraltet ist | Sekunden |
| Codestil | `bin/php/checkcodestyle.php`, `phpcs.xml.dist` | neue Stilverstöße, Typdeklarationen, Inkompatibilitäten mit PHP 8.0 | Sekunden |
| Statische Analyse | PHPStan Stufe 1 mit einer Baseline | neue Befunde in `kernel/` und `lib/` | eine Minute |
| Unit- und Komponententests ohne Datenbank | PHPUnit, die meisten Suiten | Logik, Parsen, Rendern, Sicherheitsregeln | Minuten |
| Design- und Qualitätstests | PHPUnit-Suiten `design` und `quality` | Inline-CSS oder -Skripte in Admin-Templates, Token-Regeln, Übersetzungen, die Sniffs selbst | Minuten |
| Datenbanktests | `tests/runtests.php --dsn ... --db-per-test` | SQL auf MySQL 8.0 und PostgreSQL 15 | länger |
| Tests gegen laufende Installationen | Suiten, die eine installierte Website brauchen, oder in Umgebungsvariablen genannte Live-Server | Integration mit einer echten Website, MongoDB, HTTP | ohne diese übersprungen |
| Start-Rauchtest | `tests/bin/bootsmoke.php` auf PHP 8.0 | eine Klasse, die nicht lädt, eine fehlende Kompatibilitätsfunktion | Sekunden |
| Benchmarks | `bin/php/benchmark.php micro` | Leistungsregressionen (nur Warnung) | eine Minute |

## Was wo läuft

| Wo | Was |
|---|---|
| `git commit` (nach `make hooks`) | Syntax, Manifest, Codestil der gestagten Dateien, Format der Commit-Nachricht |
| `git push` (nach `make hooks` und `make devtools`) | PHPStan gegen die Baseline |
| CI, Quality | Syntax, Manifest, Commit-Nachrichten, Codestil (Warnung), PHPStan, Sniff-Tests, PHP-8.0-Lint, Installation und Start |
| CI, PHPUnit | der Lauf ohne Datenbank auf PHP 8.1 bis 8.5 und Datenbanktests auf MySQL 8.0 und PostgreSQL 15 |
| CI, Performance | der Micro-Benchmark (Warnung) |

Details: [CI-Gates](ci-gates.md) und [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md).

## Die Tests für eine Änderung wählen

1. **Die Tests, die den geänderten Dateien zugeordnet sind.** `tests/test-map.json` ordnet Quelldateien den Testdateien zu,
   die sie abdecken; zusammen mit den Tests, die nach den geänderten Klassen benannt sind, wählt es die Tests einer
   Änderung aus.
2. **Ein neuer Test, der ohne die Änderung fehlschlägt und mit ihr besteht.** Die Anleitung zum Mitwirken verlangt genau
   das.
3. **Die Suite des Bereichs**, zum Beispiel `php vendor/bin/phpunit --testsuite kernel-classes`.
4. **Der ganze Lauf ohne Datenbank** vor einem größeren Push: `php vendor/bin/phpunit`.
5. **Die Datenbanktests**, wenn sich SQL geändert hat.
6. **Eine Prüfung auf einer laufenden Website** für alles, was rendert: heller und dunkler Modus, die unterstützten
   Breiten, keine Konsolenfehler.

## Wie ein guter Test hier aussieht

- Er braucht keine Datenbank, sofern er nicht SQL testet, und läuft so auf jedem CI-Zweig
  ([Tests ohne Datenbank und Testsuiten](tests-ohne-datenbank-und-testsuiten.md)).
- Er liest nichts aus der Installation eines Entwicklers: kein `settings/override`, keine Caches in `var/`, keine
  installierte Website. Die CI hat nichts davon.
- Er hinterlässt keinen Zustand: keinen statischen Cache, keinen INI-Wert und keine Datei unter `var/`, die der nächste
  Test sieht.
- Ein Test, der einen Live-Server braucht, liest dessen Ort aus Umgebungsvariablen und wird mit dem Namen der Variablen
  übersprungen, wenn sie nicht gesetzt ist; er trägt keinen eigenen Hostnamen, kein Konto und kein Passwort.

## Bekannte Lücken

Aus dem Standard: Einige Testdateien gehören zu keiner Suite, die Suiten gegen laufende Installationen werden in der CI
übersprungen, die Datenbankjobs decken nur MySQL und PostgreSQL ab (nicht SQLite, MariaDB, Oracle oder MongoDB-Server),
PHPUnit läuft nicht auf PHP 8.0, die Abdeckung wird gesammelt, aber nicht veröffentlicht oder durchgesetzt, und die Hooks
sind optional. Planen Sie darum herum: Ist Ihre Änderung spezifisch für eine Engine ohne CI-Job, testen Sie sie selbst
auf dieser Engine.

## Verwandte Seiten

- [Anwendungen testen](../ersteller/anwendungsentwickler/anwendungen-testen.md)
- [Tests für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tests-fuer-erweiterungen.md)
- [Checkliste für Code-Reviews](checkliste-fuer-code-reviews.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
