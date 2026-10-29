# Qualitätsprüfungen: Git-Hooks, Dateimanifest, Code-Stil und PHPStan

Diese Seite richtet sich an Entwickler, die zu Exponential committen. Seit Oktober 2026 prüft das Repository jede
Änderung auf dieselbe Weise auf dem Rechner des Entwicklers (Git-Hooks) und in der CI (der Workflow Quality):
PHP-Syntax, das Dateimanifest `share/filelist.md5`, die Commit-Nachricht, den Code-Stil geänderter Dateien und die
statische Analyse mit PHPStan. Vorhandener Code wurde dafür weder geändert noch neu formatiert; die Prüfungen betrachten,
was eine Änderung hinzufügt.

## Einrichten

```sh
make devtools   # PHP_CodeSniffer and PHPStan into .devtools/ (composer.dev.json), never into vendor/
make hooks      # git config core.hooksPath .githooks
```

`make devtools` ist optional: Ohne diesen Schritt prüfen die Hooks weiterhin Syntax, Manifest und Commit-Nachricht und
überspringen Code-Stil und PHPStan mit einem Hinweis.

## Was wann läuft

| Wann | Prüfung | Lehnt ab |
|---|---|---|
| `git commit` (pre-commit) | `php -l` auf jeder vorgemerkten PHP-Datei | einen Syntaxfehler |
| `git commit` (pre-commit) | `php -l` mit PHP 8.0, wenn `EXP_PHP80` es nennt, `php8.0` oder `php80` im PATH liegt oder `/opt/plesk/php/8.0/bin/php` existiert | Syntax von PHP 8.1 und neuer: Enums, readonly-Eigenschaften, `never`, `new` in Initialisierern, First-Class-Callables, ... |
| `git commit` (pre-commit) | `node --check` auf jeder vorgemerkten JavaScript-Datei (nicht `*.min.js`), wenn node installiert ist | einen Syntaxfehler |
| `git commit` (pre-commit) | `php bin/php/checkmanifest.php --staged` | eine vorgemerkte Datei, deren Prüfsumme in `share/filelist.md5` fehlt oder veraltet ist |
| `git commit` (pre-commit) | `php bin/php/checkcodestyle.php --staged` | eine Änderung, die einer PHP-Datei Verstöße gegen den Code-Stil hinzufügt |
| `git commit` (commit-msg) | Format des Betreffs | einen Betreff, der nicht mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: ` beginnt |
| `git push` (pre-push) | PHPStan gegen `phpstan-baseline.neon` | einen Befund, der nicht in der Baseline steht |

Überspringen Sie die Hooks im Notfall mit `git commit --no-verify` oder `git push --no-verify`; die CI führt dieselben
Prüfungen aus.

## Die make-Ziele

| Ziel | Wirkung |
|---|---|
| `make check` | lint, manifest-check, phpcs und phpstan, wie in der CI |
| `make lint` | `php -l` auf den seit `origin/main` geänderten PHP-Dateien (`BASE=...`, um mit einer anderen Referenz zu vergleichen) |
| `make lint80` | dasselbe mit PHP 8.0 (`PHP80=/path/to/php` oder `EXP_PHP80`; gefunden wie im pre-commit-Hook) |
| `make lint80-all` | PHP-8.0-Syntax jeder PHP-Datei in Git, so wie der CI-Job `php80` sie prüft (etwa 25 s) |
| `make manifest-check` | jeder Eintrag von `share/filelist.md5` existiert und stimmt; darin fehlende Git-Dateien sind Warnungen |
| `make manifest-fix` | schreibt veraltete Prüfsummen neu, fügt fehlende Dateien an ihrer sortierten Stelle ein, entfernt Einträge gelöschter Dateien |
| `make phpcs` | Code-Stil der seit `origin/main` geänderten PHP-Dateien; nur neue Verstöße scheitern |
| `make phpstan` | PHPStan; nur Befunde, die in der Baseline fehlen, scheitern |
| `make phpstan-baseline` | übernimmt alle aktuellen Befunde als neue Baseline |

## Das Dateimanifest

`share/filelist.md5` listet `md5  path` für die Dateien der Distribution auf; die System-Aktualisierungsprüfung der
Administrationsoberfläche vergleicht die Dateien damit, sodass ein veralteter Eintrag dort als geänderte Datei
erscheint. Führen Sie nach dem Ändern oder Hinzufügen einer Datei `make manifest-fix` aus und committen Sie das Manifest
mit der Änderung. `var/`, `extension/ezoe/` und das Manifest selbst liegen außerhalb davon.

## Code-Stil

`phpcs.xml.dist` beschreibt den Stil von Exponential 6.0.15: `array( ... )` statt `[ ... ]`, ein Leerzeichen innerhalb
der Klammern von Kontrollstrukturen, Aufrufen und Funktionsdeklarationen (`if ( $a )`, `foo( $a, $b )`), die öffnende
geschweifte Klammer einer Funktion auf einer eigenen Zeile, `!$a` ohne Leerzeichen, Schlüsselwörter und Konstanten in
Kleinbuchstaben, Unix-Zeilenenden, Leerzeichen statt Tabulatoren und keine Leerzeichen am Zeilenende sowie keine aus PHP
entfernten Funktionen. Jede Regel wurde vor ihrer Aufnahme am vorhandenen Code gemessen.

Zwei weitere Teile des Stils:

- **Keine Typdeklarationen von PHP 7 und neuer.** Der Sniff `bin/phpcs/Exponential/Sniffs/TypeDeclarations/` meldet
  `declare( strict_types=1 )`, skalare, Pseudo- und Union-Parametertypen (`int $a`, `string|array $a`), Rückgabetypen,
  Eigenschaftstypen und Konstantentypen. Die Parameterhinweise, die PHP 5 schon kannte, bleiben erlaubt: `array`,
  `callable`, `self` und Klassennamen, auch nullable (`?array`, `?eZINI`), wie der Kernel sie durchgehend verwendet.
  Dateien unter `tests/` dürfen das von PHPUnit verlangte `: void` deklarieren. Sein Test ist
  `tests/tests/bin/phpcs/NoTypeDeclarationsSniffTest.php` (`php vendor/bin/phpunit --testsuite quality`).
- **Kompatibilität mit PHP 8.0.** PHPCompatibility (mit den Entwicklungswerkzeugen installiert) prüft auf `testVersion`
  `8.0-`: keine Syntax oder Funktionen, die neuer als PHP 8.0 sind (`array_is_list()`, `fsync()`, `$eol` von
  `fputcsv()`, Enums, `never`, readonly, `json_validate()`, ...), und keine Funktionen, die spätere Versionen entfernt
  haben oder als veraltet markieren. Eine neuere Funktion ist hinter `function_exists()` in Ordnung oder wenn sie in
  `lib/phpcompat.php` definiert ist, sofern sie sich in PHP schreiben lässt; siehe
  [Unterstützung für PHP 8.0](../../bc/6.0/php-8.0-unterstuetzung.md). `composer.dev.json` löst die Werkzeuge für PHP 8.0.30 auf.

Alte Dateien folgen nicht jeder Regel, deshalb vergleicht die Prüfung die Zahl der Verstöße jeder geänderten Datei mit
ihrer vorherigen Version und scheitert nur, wenn eine Änderung Verstöße hinzufügt. In der CI ist der Code-Stil vorerst
eine Warnung.

## PHPStan

`phpstan.neon.dist` analysiert `kernel/` und `lib/` auf Level 1. `phpstan-baseline.neon` enthält die 3033 Befunde, die
der Code bei Einführung der Prüfung hatte; nur neue Befunde scheitern. Zwei Pfade sind ausgenommen und in der
Konfiguration vermerkt: die Kommandozeilenskripte in `kernel/private/classes/commands/` (sie deklarieren globale
Hilfsfunktionen) und `dfs_filter_iterator.php` (seine Iterator-Methoden brauchen zuerst vorläufige Rückgabetypen).
Erhöhen Sie das Level oder verkleinern Sie die Baseline in kleinen, separaten Änderungen, und erzeugen Sie sie mit
`make phpstan-baseline` neu.

## Tests, die einen laufenden Server brauchen

Einige Tests sprechen mit einer echten Datenbank oder einem echten Webserver. Sie lesen aus Umgebungsvariablen, wo
dieser zu finden ist, und werden mit dem Namen der Variable übersprungen, wenn sie nicht gesetzt ist, sodass ein
einfaches `vendor/bin/phpunit` auf einem Rechner ohne diese Server (ein CI-Runner, ein Laptop) erfolgreich ist. Kein
Test enthält einen eigenen Hostnamen, ein eigenes Konto oder ein eigenes Passwort.

| Tests | Variablen |
| --- | --- |
| `expMongoDBIntegrationTest`, `sevenxMongoDBIntegrationTest` (MongoDB-Teil) | `EXP_TEST_MONGO_URI` (ein Connection-String `mongodb://`), `EXP_TEST_MONGO_DATABASE` (Standard `exp`) |
| `expMongoDBIntegrationTest`, `sevenxMongoDBIntegrationTest` (MySQL-Teil) | `EXP_TEST_MYSQL_USER`, `EXP_TEST_MYSQL_PASSWORD`, `EXP_TEST_MYSQL_DATABASE`, `EXP_TEST_MYSQL_HOST` (Standard `localhost`), `EXP_TEST_MYSQL_PORT` (Standard 3306) |
| `expMongoDBDeclaredDefaultTest`, `TrashListPagedTest` | `EXP_TEST_MONGO_SCRATCH` (`<host>:<port>/<database>`, eine Datenbank, deren Name „scratch" oder „test" enthält) |
| Live-HTTP-Tests (`ApiKeyLiveTest`, `RestContentPermissionLiveTest`, ...) | `EXP_TEST_PUBLIC_HOST` (sonst der Host aus `SiteURL` des Standard-Siteaccess), `APIKEY_LIVE_BASES`, `APIKEY_LIVE_ADMIN_PASSWORD` |

Übergeben Sie ein Passwort über die Umgebung, nicht auf der Kommandozeile, damit es nicht in der Shell-Historie oder in
der Prozessliste landet, zum Beispiel aus einer Datei, die nur Sie lesen können:

```sh
EXP_TEST_MYSQL_PASSWORD="$(cat ~/.exp-test-mysql-password)" EXP_TEST_MYSQL_USER=test EXP_TEST_MYSQL_DATABASE=testdb \
    php vendor/bin/phpunit tests/tests/lib/ezdb/mongodb/expMongoDBIntegrationTest.php
```

Ein Testlauf schreibt seine eigenen Audit-Datensätze in ein Verzeichnis unter `var/tmp` (`tests/bootstrap.php`). Ein
Live-Test, der prüft, was ein Webserver aufgezeichnet hat, liest das Audit-Verzeichnis der Website,
`expAuditConfig::siteLogDir()`, nicht `expAuditConfig::get()['logDir']`, das das Verzeichnis des Laufs nennt.

## CI

`.github/workflows/quality.yml` läuft bei jedem Push und Pull Request auf `main`: PHP-Syntax der geänderten Dateien mit
PHP 8.3, JavaScript-Syntax der geänderten Skripte (`node --check` mit Node.js 22, das ein ES-Modul in einer
`.js`-Datei liest), das Manifest, die Commit-Nachrichten der gepushten oder vorgeschlagenen Commits (der Commit eines
Mitwirkenden warnt nur), PHPStan und die Tests der eigenen Sniffs
(ein eigener Job, auf PHP 8.4) blockieren; der Code-Stil der geänderten Dateien, einschließlich Typdeklarationen und
Kompatibilität mit PHP 8.0, wird als Warnung gemeldet.

Der Job `php80` ist die Prüfung für die älteste unterstützte Version, PHP 8.0 (das mitgelieferte PHP von RHEL 9), an
einer Stelle:

1. `make lint80-all`: `php -l` mit PHP 8.0 auf jeder PHP-Datei in Git (blockierend).
2. `composer install --no-dev` mit PHP 8.0. Er übergibt `--ignore-platform-req=php`, weil `composer.json` und einige
   se7enxweb-Pakete noch 8.1 deklarieren (Velocity tat das bis zu seinem Release 0.0.4.54); was der Job prüft, ist, dass
   der Code unter 8.0 läuft.
3. `php bin/php/ezexec.php tests/bin/bootsmoke.php`: startet den Kernel ohne Datenbank, lädt jede Klasse der
   Autoload-Arrays von Kernel und Extensions, prüft `lib/phpcompat.php` und rendert ein Template mit `wash`. Eine
   Warnung, bis syndication und ezstarrating mit ihren korrigierten Deklarationen veröffentlicht sind, die heute kein
   PHP 8 laden kann.

Der pre-commit-Hook und `make lint80` führen dieselbe Syntaxprüfung auf geänderten Dateien aus, wenn ein
PHP-8.0-Binärprogramm verfügbar ist.

Die Installationen der Abhängigkeiten in den Workflows lösen das Root-Paket als `dev-main` auf. Sein Branch-Alias
`"dev-main": "6.0.x-dev"` in `composer.json` (seit 12.10.2026) erlaubt es einem benötigten Paket, das
`se7enxweb/exponential` mit einer Versionsbeschränkung nennt, etwa `^6.0.14` von expqueue und expai, dagegen aufgelöst
zu werden; ohne ihn bricht die Installation mit „could not be resolved" ab. Ein Push auf `main` erhält `dev-main` vom
ausgecheckten Branch. Ein Pull Request wird als losgelöster Merge-Commit ausgecheckt, bei dem Composer keinen Branch
findet und das Root-Paket `dev-<sha>` oder `1.0.0` nennt, deshalb setzen die drei Workflows
`COMPOSER_ROOT_VERSION=dev-main` (seit 15.10.2026). Bis dahin scheiterten bei jedem Pull Request die Installationen und
mit ihnen PHPStan, PHPUnit, der PHP-8.0-Job und die Sniff-Tests, und der Benchmark führte nichts aus.

### Pull Requests

Ein Pull Request erhält dieselben blockierenden Prüfungen wie ein Push auf `main`, auf dem Merge des Branches in
`main`. Zwei davon hängen vom Branch ab, nicht vom Code:

- **Das Dateimanifest.** Jeder Commit, der eine Datei hinzufügt, ändert oder entfernt, aktualisiert ihre Zeile in
  `share/filelist.md5`; `make hooks` lässt den pre-commit-Hook einen Commit ablehnen, der das vergisst. Findet die CI
  eine veraltete Zeile, gibt der Job die Änderung an `share/filelist.md5`, die sie passend macht, als Annotation, im
  Protokoll und in der Job-Zusammenfassung aus. Führen Sie im Branch `make manifest-fix`
  (`php bin/php/checkmanifest.php --fix`) aus und committen Sie das Ergebnis, z. B. `Updated: The file manifest carries
  the checksums of ...`. Bei einem Pull Request ist die Prüfung eine **Warnung** (eine Annotation `::warning::` und
  ein Vermerk in der Job-Zusammenfassung), niemals ein Fehler: Sie läuft auf dem Merge des Branches in das aktuelle
  `main`, sodass der Mitwirkende die zusammengeführten Prüfsummen nicht erzeugen kann, wenn `main` dieselben Dateien
  geändert hat, und die Person, die den Pull Request zusammenführt, das Manifest auf `main` im selben Push
  aktualisiert. Bei einem Push auf `main` bleibt die Prüfung **blockierend**: Eine veraltete Zeile auf `main` lässt
  die Aktualisierungsprüfung jeder Website die Datei als geändert aufführen.
- **Die Commit-Nachrichten.** `.githooks/check-commit-range <base> <head>` prüft jeden Commit des Pushs oder des Pull
  Requests; die CI und die lokalen Prüfschritte führen dasselbe Skript aus. Ein Commit eines Projektautors (eine in
  `.githooks/commit-msg-authors` aufgeführte Adresse) beginnt mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder
  `Renamed: `, sonst scheitert die Prüfung; formulieren Sie ihn vor dem Merge mit `git rebase -i` um. Ein Commit von
  jemand anderem ist ein Commit eines Mitwirkenden: Jeder Pull Request wird zusammengeführt (sein Autor squasht und
  rebased ihn auf `main`, solange er offen ist, und `main` wird nach dem Merge nie umgeschrieben), und ein Betreff wie
  `Changed: ...` oder `Fix typo` ist eine Warnungsannotation, niemals ein Fehler, auf dem Pull Request und später auf
  `main`. Ein Merge-Commit wird
  nicht geprüft. `EXP_COMMIT_MSG_STRICT_ALL=1` macht jeden Autor streng, für einen Mitwirkenden, der die Prüfung des
  Projekts auf seinem eigenen Branch möchte.

Wenn `main` `share/filelist.md5` seit Beginn des Branches geändert hat, führen Sie `main` in den Branch zusammen und
führen `make manifest-fix` erneut aus, statt den Konflikt von Hand zu lösen.

Der PHPUnit-Workflow läuft bei jedem Push und Pull Request (seit 17.10.2026). Bis dahin lief er nur, wenn eine
`.php`-Datei, `composer.json`, `composer.lock` oder `phpunit.xml` geändert wurde, obwohl die Tests auch Templates,
Stylesheets, Skripte, Übersetzungen, INI-Dateien und Dokumentation lesen: Eine Änderung nur an diesen führte keinen
Test aus, und ihr Fehler zeigte sich später bei einem nicht zusammenhängenden Commit.
`tests/tests/bin/CiWorkflowPullRequestTest.php` (Suite `quality`) hält die Workflows in diesem Zustand: kein
Pfadfilter für PHPUnit, `COMPOSER_ROOT_VERSION=dev-main` in allen dreien, kein `pull_request_target` und keine
Schreibberechtigung, sodass der Code eines Forks nie mit einem Token läuft, das das Repository ändern kann.

Der erste Pull Request eines neuen Mitwirkenden aus einem Fork wartet auf die Freigabe durch einen Maintainer, bevor
irgendein Workflow läuft (die Repository-Einstellung „Require approval for first-time contributors"). Ein Lauf, den
niemand freigibt, verfällt und wird als fehlgeschlagen ohne Jobs aufgeführt; er sagt nichts über den Code aus. Geben Sie
ihn unter Actions frei, oder pushen Sie erneut, nachdem der erste Pull Request dieses Mitwirkenden zusammengeführt
wurde.

Die Jobs `Database tests` führen jede Suite von `phpunit.xml` mit `tests/runtests.php --dsn ... --db-per-test` aus und
scheitern bei einem Fehlschlag oder Fehler (vor dem 15.10.2026 endete der Runner immer mit 0, und der Job war
erfolgreich, was auch immer die Tests meldeten; außerdem nannte er das Verzeichnis `tests`, wodurch Dateien liefen, die
`phpunit.xml` ausschließt).

### Bevor Sie pushen

Die blockierenden Prüfungen des Workflows Quality betrachten die Commits des Pushs, sodass sie auf dem committeten
`HEAD` ausgeführt werden können, bevor er den Rechner verlässt:

```sh
git diff --name-only --diff-filter=AM origin/main..HEAD -- '*.php' | xargs -r -n1 php -l   # syntax of changed files
php bin/php/checkmanifest.php --all                                                        # the file manifest
sh .githooks/check-commit-range origin/main HEAD                                           # the commit messages
make phpstan                                                                               # needs make devtools
```

Ein Fehler hier lässt auch den Workflow scheitern. Der Code-Stil bleibt in der CI eine Warnung.

### Nur die Tests einer Änderung ausführen

Der gesamte PHPUnit-Lauf (mehr als 10.000 Tests) dauert auf einem ausgelasteten Rechner lange. Für einen alltäglichen
Commit genügt es, die Testdateien auszuführen, die die von der Änderung berührten Dateien abdecken, jede in einem
eigenen PHPUnit-Prozess, damit der globale Zustand einer Datei (ein statischer Cache, ein im Prozess gesetzter INI-Wert)
das Ergebnis einer anderen nicht verändern kann. Der vollständige Lauf bleibt die Prüfung vor einem Release oder einem
großen Push, und die CI führt weiterhin alles aus.

Eine Testdatei gehört zu einer Änderung, wenn:

1. `tests/test-map.json` sie für eine geänderte Datei nennt. Die Datei hat drei Abschnitte: `map`, von Hand angelegte
   Einträge (ein Pfad oder ein Muster, bei dem `*` auch `/` erfasst) für Code, den Tests erreichen, ohne ihn zu nennen,
   etwa eine Modulansicht, die ein Test über das Modul ausführt; `generated`, die Quellpfade, die Testdateien wörtlich
   nennen; und `ignore`, Dateien, die bewusst kein PHPUnit-Test abdeckt (Dokumentation, Übersetzungen, das Manifest).
2. Sie nach einer Klasse benannt ist, die die geänderte Datei deklariert, oder nach der Datei selbst, oder wenn sie eine
   dieser Klassen oder Funktionen, den Pfad der geänderten Datei oder eine INI-Einstellung nennt, die die Änderung
   hinzufügt oder ändert.
3. Sie selbst eine geänderte Testdatei ist.

Eine geänderte Datei, die keiner dieser Wege findet, ist eine ungetestete Änderung. Das ist eine Warnung, kein Fehler,
zeigt aber, wo ein Test fehlt. Eine Änderung an `tests/bootstrap.php`, `tests/toolkit/` oder `phpunit.xml` berührt jeden
Test; führen Sie dafür die gesamte Suite aus.

Mit git, grep und xargs (und `jq` für die Map), im Wurzelverzeichnis der Installation:

```sh
BASE=origin/main
mkdir -p var/tmp
{
    # 3. changed test files
    git diff --name-only --diff-filter=AM "$BASE"..HEAD -- 'tests/tests/*Test.php'
    for f in $(git diff --name-only --diff-filter=AM "$BASE"..HEAD -- ':!tests/tests/*Test.php'); do
        # 1. the explicit map (exact paths; patterns need a look at the file)
        jq -r --arg f "$f" '(.map[$f] // []) + (.generated[$f] // []) | .[]' tests/test-map.json
        # 2. test files that name a class the changed file declares
        case "$f" in *.php)
            for c in $(sed -nE 's/^\s*((abstract|final|readonly)\s+)*(class|interface|trait|enum)\s+([A-Za-z_][A-Za-z0-9_]*).*/\4/p' "$f"); do
                grep -rliw --include='*Test.php' -- "$c" tests/tests
            done ;;
        esac
    done
} | sort -u > var/tmp/tests-of-change.txt
# one PHPUnit process per test file, three at a time, at low priority
xargs -r -P 3 -I{} sh -c 'nice -n 15 php vendor/bin/phpunit --no-coverage --do-not-cache-result "{}" \
    > /dev/null 2>&1 && echo "PASS {}" || echo "FAIL {}"' < var/tmp/tests-of-change.txt
```

Führen Sie eine fehlschlagende Datei erneut allein aus (`php vendor/bin/phpunit <file>`), um den Fehler zu sehen. Eine
hier aufgeführte Testdatei, die außerhalb der Suites von `phpunit.xml` liegt, gehört auch nicht zum vollständigen Lauf;
lassen Sie sie weg. Eine Klasse, die die meisten Testdateien verwenden (`eZINI`, `eZUser`), findet einen großen Teil der
Suite; behalten Sie die nach der Klasse benannten Dateien und die aus dem Verzeichnis der geänderten Datei. Wenn ein
neuer Test Code abdeckt, den seine Datei nicht nennt, fügen Sie im selben Commit einen Eintrag zu `map` in
`tests/test-map.json` hinzu.

Tests, die den gesamten Baum durchsuchen, nennen keine Datei, deshalb führt die Map sie über Muster: Jede PHP-Datei und
jedes Skript in `bin/` führt `expFileModeLimitsTest` aus (kein `chmod()`, `mkdir()` oder `umask()` über die
Modusgrenzen hinaus), jede INI-Datei führt `expIniRoundTripTest` aus, und `composer.json` führt beide sowie
`AdminStylingRulesTest` aus, weil ein neu festgelegtes Paket sein eigenes PHP, eigene INI-Dateien und Admin-Templates
mitbringt. Jeder dieser Fälle erreichte einmal `main` über eine Änderung, deren eigene Tests erfolgreich waren
(Oktober 2026).

Der Copyright-Hinweis wird ebenso geprüft: Jede PHP-, JavaScript-, Template-, Stylesheet-, XML-, INI-, SQL- und
Textdatei, jedes Skript in `bin/`, `LICENSE`, `composer.json` und jeder Katalog führt `CopyrightNoticeTest` aus, der
bei jeder Zeile fehlschlägt, die 7x oder die Exponential Foundation in einer anderen Form als diesen beiden:
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ oder
„Exponential Copyright © 1998-2026 Exponential Foundation and 7x“ als Rechteinhaber nennt (Ausnahmen mit Begründung: `tests/tests/bin/copyright-notice-allowlist.json`);
die Fußzeilen, die Anmeldeseiten, die Copyright-Seite, der Hinweis der Hilfe, die Konsole und die Kataloge führen
außerdem `CopyrightFooterNoticeTest` aus. Siehe [Urheberrecht und Copyleft](../../hilfe/urheberrecht-und-copyleft.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
