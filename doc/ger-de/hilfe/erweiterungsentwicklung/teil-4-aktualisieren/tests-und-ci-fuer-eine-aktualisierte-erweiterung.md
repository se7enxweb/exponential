---
title: Tests und CI für eine aktualisierte Erweiterung
audience: developer
category: extension-development
order: 220
summary: Sorgen Sie dafür, dass eine aktualisierte Erweiterung aktuell bleibt - ein Lint auf jedem unterstützten PHP, kleine Tests ohne Datenbank, die Inline-Code verbieten, und ein GitHub-Actions-Workflow, mit dem Workflow von xrowextract als Vorbild.
keywords: Tests, Matrix der PHP-Versionen, Test auf Inline-Skripte, Test auf Inline-CSS, Upgrade, tests, CI, GitHub Actions, php -l, PHP versions matrix, PHPCompatibility, PHPStan, PHPUnit, inline script test, inline CSS test, xmllint, upgrade
type: help
---
# Tests und CI für eine aktualisierte Erweiterung

Teil 4, Kapitel 22. Ein Upgrade ist erst fertig, wenn etwas verhindert, dass es wieder rückgängig gemacht wird. Der
nächste Mitwirkende fügt ein `onclick` hinzu, ein Hosting-Unternehmen wechselt auf PHP 8.5, jemand kopiert ein altes
Schnipsel zurück. Dieses Kapitel fügt die billigsten Tests hinzu, die das erkennen, und führt sie automatisch auf
GitHub aus. Das Schreiben von Tests im Allgemeinen behandeln
[Tests für Erweiterungen](../teil-3-erweiterungsentwickler/tests-fuer-erweiterungen.md)
und das Tutorial [Ein PHPUnit-Test, von Hand](../teil-3-erweiterungsentwickler/tutorials/von-hand-phpunit-test.md).

## Was Sie testen sollten, nach Nutzen geordnet

| Test | Erkennt | Braucht |
|---|---|---|
| `php -l` für jede Datei, auf dem ältesten und neuesten PHP, das Sie unterstützen, mit allen Fehlerstufen | Parse-Fehler, entfernte Syntax, Deprecations zur Kompilierzeit (implizit nullable Parameter) | nur PHP |
| Kein Inline-Skript, kein Event-Handler-Attribut, kein `javascript:`-Link in Templates | Rückschritte bei CSP | nur PHP |
| Kein `<style>` und kein `style=""` in Templates | Rückschritte bei Inline-CSS | nur PHP |
| `xmllint --noout` für `extension.xml` und jede `translation.ts` | defektes XML | libxml2 |
| Die Version in `ezinfo.php` entspricht der in `extension.xml` | halb erledigte Releases | nur PHP |
| PHPCompatibility (ein Standard für PHP_CodeSniffer) für Ihr ältestes PHP | Funktionen und Syntax, die neuer als dieses PHP sind | Composer |
| PHPStan gegen die Klassen des Kernels | Aufrufe von Methoden, die nicht existieren, falsche Anzahl von Argumenten | den Quellcode des Kernels |
| Unit-Tests Ihrer eigenen Klassen | Logik | PHPUnit |

Die ersten fünf brauchen nichts außer PHP und laufen in Sekunden. Beginnen Sie dort.

## Tests ohne Datenbank

Die Erweiterungen, die dieses Projekt im Oktober 2026 aktualisiert hat, haben jeweils kleine Tests erhalten, die
Dateien lesen und weder den Kernel noch eine Datenbank brauchen. Es sind einfache PHP-Skripte mit PASS- und
FAIL-Zeilen, ausgeführt mit `php tests/<file>.php`. se7enxweb/expchangeclass hat zwei:
`tests/expchangeclassinlinescripttest.php` und `tests/expchangeclassadmininlinecsstest.php`. Die Prüfung auf
Inline-Skripte, auf ihren Kern reduziert:

```php
<?php
/**
 * The templates carry no inline JavaScript: no <script> element with code, no event handler attribute and no
 * javascript: link. Run with: php tests/myoldextinlinescripttest.php
 */
$failures = 0;
function check( $ok, $message )
{
    global $failures;
    echo ( $ok ? 'PASS' : 'FAIL' ), ": $message\n";
    if ( !$ok )
    {
        $failures++;
    }
}

$root = dirname( __DIR__ );
$templates = array();
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/design', FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $file )
{
    if ( substr( (string)$file, -4 ) === '.tpl' )
    {
        // template comments may mention <script>; leave them out
        $templates[substr( (string)$file, strlen( $root ) + 1 )] = preg_replace( '/\{\*.*?\*\}/s', '', file_get_contents( (string)$file ) );
    }
}

$scripts = $handlers = array();
foreach ( $templates as $path => $source )
{
    if ( preg_match( '/<script\b(?![^>]*\bsrc\s*=)(?![^>]*type\s*=\s*["\']application\/(?:ld\+)?json["\'])[^>]*>/i', $source ) )
    {
        $scripts[] = $path;
    }
    if ( preg_match( '/<[a-z][^>]*\son[a-z]+\s*=/i', $source ) || preg_match( '/=\s*["\']?\s*javascript:/i', $source ) )
    {
        $handlers[] = $path;
    }
}
check( count( $templates ) > 0 && !$scripts, 'no inline script in ' . count( $templates ) . ' templates' . ( $scripts ? ': ' . implode( ', ', $scripts ) : '' ) );
check( !$handlers, 'no event handler attribute or javascript: link' . ( $handlers ? ': ' . implode( ', ', $handlers ) : '' ) );
exit( $failures ? 1 : 0 );
```

Ein Test auf Inline-CSS ist derselbe mit zwei anderen Mustern: `/<style\b/i` und `/\sstyle\s*=/i`.

Warum so einfach? Weil er überall läuft: auf einem Laptop ohne Installation, auf den Runnern von GitHub, auf PHP 8.0.

## Ein Lint, der auch Deprecations erkennt

```bash
find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -d error_reporting=-1 -l 2>&1 | grep -v '^No syntax errors'
```

Mit `error_reporting=-1` geben PHP 8.4 und neuer die implizit nullable Parameter während des Lints als Deprecations
aus. Keine Ausgabe bedeutet sauber.

## Ein GitHub-Actions-Workflow

se7enxweb/xrowextract führt seine Prüfungen bei jedem Push und Pull Request auf jeder PHP-Version aus, die es
unterstützt. Sein Workflow `.github/workflows/check.yml`, gekürzt:

```yaml
name: check

on:
  push:
  pull_request:

permissions:
  contents: read

jobs:
  check:
    name: PHP ${{ matrix.php }}
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        php: ['8.1', '8.2', '8.3', '8.4', '8.5']
    steps:
      - name: Check out xrowextract
        uses: actions/checkout@v4
        with:
          path: xrowextract

      - name: Check out Exponential (kernel and library classes for PHPStan)
        uses: actions/checkout@v4
        with:
          repository: se7enxweb/exponential
          path: exponential

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          tools: composer
          coverage: none

      - name: bin/check.sh
        working-directory: xrowextract
        env:
          EXPONENTIAL_ROOT: ${{ github.workspace }}/exponential
        run: bin/check.sh --php="$(command -v php)"
```

Was es wert ist, übernommen zu werden:

- **Eine Matrix von PHP-Versionen** mit `fail-fast: false`, damit der Fehler einer Version den einer anderen nicht
  verdeckt.
- **Der Kernel, neben der Erweiterung ausgecheckt**, damit PHPStan Aufrufe von Kernel-Klassen ohne Installation
  prüfen kann.
- **`permissions: contents: read`**: Der Workflow kann den Code lesen und sonst nichts.
- **Ein Skript (`bin/check.sh`), das die Arbeit macht**, damit auf Ihrem Rechner dieselben Prüfungen laufen wie auf
  GitHub.

Ein kleinerer Workflow für eine Erweiterung mit nur den Tests ohne Datenbank:

```yaml
name: tests

on:
  push:
  pull_request:

permissions:
  contents: read

jobs:
  tests:
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        php: ['8.0', '8.5']
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          coverage: none
      - name: Lint
        run: find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -d error_reporting=-1 -l 2>&1 | (! grep -v '^No syntax errors')
      - name: Tests without a database
        run: for t in tests/*test.php; do php "$t" || exit 1; done
```

Das `(! grep ...)` macht aus „grep hat eine Zeile gefunden“ einen Fehlschlag.

## PHPCompatibility

Wenn Sie sicher sein wollen, dass keine Funktion hineingerutscht ist, die neuer als Ihr ältestes PHP ist, prüft
PHPCompatibility den Code gegen einen Bereich von PHP-Versionen. Der Kernel selbst führt es mit `testVersion` `8.0-`
aus ([PHP-8.0-Unterstützung](../../../../eng-us/bc/6.0/php-8.0-support.md)). Installieren Sie es als
Entwicklungsabhängigkeit der Erweiterung und führen Sie es im Workflow wie den Lint aus. Der Commit, der
expchangeclass auf PHP 8.0 erweitert hat (32196f0), nennt es unter den Prüfungen, die die Änderung begründet haben.

## Vor jedem Release

Führen Sie die ganze Reihe lokal aus, bevor Sie taggen, denn ein Tag lässt sich danach nicht mehr korrigieren
([Testen vor einem Release](../teil-7-veroeffentlichen/vor-einem-release-testen.md)).

## Verwandte Seiten

- Zurück: [Namen und Marken bei einem Upgrade](namen-und-marken-bei-einem-upgrade.md)
- Weiter: [Fallstudie: von eZChangeclass zu expchangeclass](fallstudie-expchangeclass.md)
- [Tests für Erweiterungen](../teil-3-erweiterungsentwickler/tests-fuer-erweiterungen.md)
- [Dokumentation von GitHub Actions](https://docs.github.com/en/actions)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
