# Continuous Integration: die Workflows, einen Fehler nachstellen, die Regeln für Commit-Nachrichten

Lesen Sie diese Seite, wenn eine Prüfung eines Commits oder Pull Requests von `se7enxweb/exponential` rot ist, wenn Sie
einen der Workflows in `.github/workflows/` ändern oder wenn Sie wissen möchten, wie gesund die CI in letzter Zeit war.
Die Prüfungen selbst (Git-Hooks, Dateimanifest, Code-Stil, PHPStan, die Tests einer Änderung) sind in
[doc/eng-us/features/6.0/quality-checks.md](../../../eng-us/features/6.0/quality-checks.md) beschrieben; diese Seite
ist die Karte drumherum.

## Kurz gesagt

| | |
|---|---|
| Was läuft | **Quality** und **PHPUnit** bei jedem Push und Pull Request, **Performance**, wenn sich PHP, Templates oder Einstellungen ändern, und **CI health** einmal pro Woche. |
| Was blockiert | Quality und PHPUnit. Performance ist nur eine Warnung, und CI health berichtet nur. |
| Eine rote Prüfung | Öffnen Sie den Lauf, suchen Sie den fehlgeschlagenen Schritt in der Tabelle unten und führen Sie den Befehl dieses Schritts auf Ihrer Maschine aus. `gh run view <id> --log-failed` gibt nur das Log der fehlgeschlagenen Schritte aus. |
| Der Commit eines Beitragenden | Wird von seinem Autor zusammengefasst und auf `main` rebased, solange der Pull Request offen ist, und nach dem Merge nie umgeschrieben. Ein Betreff außerhalb von `Added:`/`Updated:`/`Fixed:`/`Removed:`/`Renamed:` ist für einen Beitragenden eine Warnung und nur für die eigenen Autoren des Projekts ein Fehler. |
| Wie gesund die CI ist | `python3 .github/scripts/ci_health_report.py --days 14`, oder die Zusammenfassung des wöchentlichen Laufs **CI health**. |

## Die Workflows

| Workflow | Läuft bei | Jobs | Blockiert |
|---|---|---|---|
| `quality.yml` (Quality) | jedem Push auf `main` und jedem Pull Request dorthin, und von Hand | **Syntax, manifest, commit messages, code style** (PHP 8.3, Node.js 22), **PHPStan** (PHP 8.3), **Code style sniff tests** (PHP 8.4), **PHP 8.0 (oldest supported) syntax and boot** | ja; der Code-Stil ist eine Warnung, und das Dateimanifest ist bei einem Pull Request eine Warnung (blockierend bei einem Push auf `main`) |
| `phpunit.yml` (PHPUnit) | jedem Push auf `main` und jedem Pull Request dorthin (kein Pfadfilter: Die Tests lesen auch Templates, Stylesheets, Skripte, Übersetzungen, Einstellungen und Dokumentation), und von Hand | **PHPUnit (PHP 8.1 to 8.5)**, eine Matrix ohne Datenbank; **Database tests** auf MySQL 8.0 und PostgreSQL 15 (PHP 8.3) | ja |
| `performance.yml` (Performance) | einem Push oder Pull Request, der PHP, Templates, `settings/`, die deutsche Übersetzung oder `tests/benchmark/` ändert | **Micro benchmark (PHP 8.5)** gegen `tests/benchmark/baseline.json`; ein Messpunkt über der Schwelle wird noch einmal gemessen, und nur der zweite Lauf zählt | nein (`continue-on-error`) |
| `ci-health.yml` (CI health) | montags 06:17 UTC, und von Hand | **CI health report** | nein; er berichtet nur |

Für alle gilt:

- **Das Wurzelpaket ist `dev-main`.** Die Workflows setzen `COMPOSER_ROOT_VERSION=dev-main`, weil ein Pull Request als
  losgelöster Merge-Commit ausgecheckt wird, bei dem Composer das Wurzelpaket `dev-<sha>` nennen würde und kein Paket,
  das `se7enxweb/exponential: ^6.0.14` verlangt (expai, expqueue), installiert werden könnte.
- **PHPUnit löst seine Abhängigkeiten bei jedem Lauf neu auf.** Der Installationsschritt kopiert `composer.json` nach
  `composer.ci-phpunit.json`, setzt die PHPUnit-Version für den Zweig (10.5 unter PHP 8.1, 11.5 unter 8.2 und 8.3, ab
  8.4 die Version aus `composer.json`), fügt `mongodb/mongodb` hinzu und führt `composer update` auf dieser Kopie aus.
  Ein Paket-Release, das die Auflösung bricht, bricht daher den Installationsschritt jedes Zweigs zugleich, ohne eine
  Änderung in diesem Repository.
- **Ein Pull Request wird so getestet, als wäre er in `main` gemergt.** Die Prüfungen sehen `main` plus den Branch,
  daher kann ein Branch, der grün war, rot werden, wenn sich `main` bewegt. Mergen Sie `main` in den Branch und pushen
  Sie erneut. Ein Pull Request mit Konflikten zu `main` erhält gar keine Prüfungen, weil GitHub keinen Merge-Commit zum
  Testen hat: Ein Pull Request mit leerer Prüfliste wird gelöst, indem Sie `main` in den Branch mergen, nicht indem Sie
  etwas erneut ausführen.
- **Nur ein überholter Lauf eines Pull Requests wird abgebrochen.** Quality, PHPUnit und Performance haben eine
  Concurrency-Gruppe pro Pull Request, und ein neuer Push auf den Branch bricht den Lauf ab, den er ersetzt. Ein Push
  auf `main` erhält eine eigene Gruppe für seinen Commit, sodass ein späterer Push ihn nie mittendrin abbricht und
  jeder Commit auf `main` ein vollständiges Ergebnis behält. Ein abgebrochener Lauf eines Pull Requests ist daher kein
  Fehlschlag; sehen Sie sich den neuesten Lauf des Branches an.
- **Netzwerkschritte werden wiederholt, hängende Jobs werden gestoppt.** Jede Installation oder Aktualisierung mit
  Composer wird bis zu dreimal versucht, im Abstand von 15 und 30 Sekunden. Die Produktionsinstallation mit PHP 8.0
  wiederholt nur bei einem Netzwerkfehler: Eine Anforderung, die PHP 8.0 nicht erfüllen kann, schlägt sofort fehl, denn
  genau das prüft der Schritt. Downloads von Composer werden pro PHP-Version gecacht. Jeder Job hat einen Timeout
  (20 oder 30 Minuten), sodass ein hängender Job fehlschlägt, statt die Warteschlange sechs Stunden lang zu blockieren.

## Einen Fehler auf Ihrer Maschine nachstellen

Suchen Sie zuerst den fehlgeschlagenen Lauf und Schritt:

```sh
gh run list -R se7enxweb/exponential --limit 20                 # the latest runs and their conclusion
gh run view <run id> -R se7enxweb/exponential --log-failed      # only the log of the failed steps
gh run rerun <run id> -R se7enxweb/exponential --failed         # run only the failed jobs again
```

Führen Sie dann den Befehl des Schritts im Wurzelverzeichnis eines Checkouts desselben Commits aus. `BASE` ist der
Commit vor dem Push, oder `origin/main` bei einem Pull Request.

| Fehlgeschlagener Schritt | Befehl |
|---|---|
| PHP syntax of changed files | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.php' \| xargs -r -n1 php -l` |
| JavaScript syntax of changed files | `git diff --name-only --diff-filter=ACMR $BASE HEAD -- '*.js' ':!*.min.js' \| xargs -r -n1 node --check` (Node.js 22 oder neuer) |
| File manifest (share/filelist.md5) | `php bin/php/checkmanifest.php --all`; beheben Sie es mit `make manifest-fix` und committen Sie `share/filelist.md5` (blockierend bei einem Push auf `main`, eine Warnung bei einem Pull Request: Wer mergt, aktualisiert es) |
| Commit messages | `sh .githooks/check-commit-range $BASE HEAD` (siehe die Regeln unten) |
| Code style of changed files | `make phpcs BASE=$BASE` (in der CI eine Warnung) |
| PHPStan | einmal `make devtools`, dann `make phpstan`; nur Befunde, die in `phpstan-baseline.neon` fehlen, schlagen fehl |
| Code style sniff tests | `vendor/bin/phpunit --testsuite quality --no-coverage` (PHP 8.4) |
| PHP 8.0 syntax of every PHP file | `make lint80-all PHP80=/path/to/php8.0` |
| Install the production dependencies with PHP 8.0 | in einem sauberen Checkout, mit PHP 8.0: `composer install --no-dev --prefer-dist`. Ein Fehlschlag nennt das Paket, dessen PHP-Anforderung zu hoch ist. |
| Boot smoke test | `php bin/php/ezexec.php tests/bin/bootsmoke.php` mit PHP 8.0 |
| Install dependencies (PHPUnit) | in einem sauberen Checkout: Kopieren Sie `composer.json` nach `composer.ci-phpunit.json`, setzen Sie `require-dev` so, wie es der Workflow tut, und führen Sie `COMPOSER=composer.ci-phpunit.json COMPOSER_ROOT_VERSION=dev-main composer update --with-all-dependencies --ignore-platform-req=php --ignore-platform-req=ext-mongodb` aus. Nie in einer installierten Website: Es schreibt `vendor/` neu. |
| Run PHPUnit | zuerst `php vendor/bin/phpunit <the failed test file>`, dann der ganze Lauf, `php vendor/bin/phpunit` |
| Run database tests | `php -d date.timezone=America/Los_Angeles tests/runtests.php --dsn mysql://user@127.0.0.1/testdb --db-per-test` gegen eine Wegwerf-Datenbank |
| Micro benchmark | `php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks` mit ausgeschaltetem OPcache für die CLI |

Die PHPUnit-Jobs haben keine installierte Website: kein `settings/override`, keine Datenbank, keine Caches unter
`var/`. Ein Test, der in einer Entwicklungsinstallation besteht und in der CI fehlschlägt, liest meist etwas aus dieser
Installation. Führen Sie ihn so aus, wie es die CI tut, in einem frischen Clone mit nur installiertem `vendor/`, bevor
Sie entscheiden, dass die CI falsch liegt. Ein Test, der allein besteht und im ganzen Lauf fehlschlägt, teilt Zustand
mit einem anderen Test (ein statischer Cache, ein im Prozess gesetzter INI-Wert, eine Datei unter `var/`); führen Sie
die beiden Dateien zusammen aus, um das Paar zu finden.

## Die lokalen Gates und die CI

Das Push-Gate des Teams führt die Prüfungen von `.github/workflows/quality.yml` und den Job ohne Datenbank von
`phpunit.yml` gegen eine committete Revision aus, in einem sauberen Git-Worktree davon:

| Modus | Was läuft | Wann |
|------|-----------|------|
| `--fast` | Syntax der geänderten PHP-Dateien (PHP 8.3 wie die CI, und PHP 8.0), `node --check` der geänderten Skripte (node 22), Commit-Nachrichten (dasselbe `.githooks/check-commit-range`, das die CI ausführt), die Packagist-Prüfung von `composer.json` (siehe unten), das Dateimanifest, der Code-Stil der geänderten Dateien (nur eine Warnung, wie in der CI), PHPStan und die PHPUnit-Testdateien der Änderung | bei jedem Commit und Push |
| Standard | wie `--fast`, mit dem ganzen PHPUnit-Lauf statt der geänderten Tests | größere Pushes |
| `--ci` | der Standardumfang plus die PHP-8.0-Syntax jeder Datei und die Produktionsinstallation mit PHP 8.0 samt Boot-Smoke-Test (der Job `php80` der CI) | vor einem Release |

Wie es der CI entspricht:

- **Erweiterungen.** Der saubere Baum erhält jede `se7enxweb/*`-Erweiterung, die `composer.json` verlangt, in dem
  Release, das das `composer update` der CI installiert (die höchste Version auf Packagist innerhalb der Bedingung),
  nicht den Checkout eines Entwicklers. Ein Checkout mit unveröffentlichter Arbeit ließ am 2026-10-16 einen
  Kernel-Test lokal bestehen und in der CI fehlschlagen.
- **Umgebung.** PHPUnit läuft mit `CI=1`, wie GitHub es setzt, und mit node im `PATH`: Die Design-Tests, die
  JavaScript der Administration ausführen, und die Prüfung, dass jede ausgelieferte Erweiterung vorhanden ist, schlagen
  fehl, statt übersprungen zu werden.
- **Auswahl der Tests in `--fast`.** Eine geänderte Datei führt die Tests aus, die ihr zugeordnet sind
  (`tests/test-map.json`), die nach ihren Klassen benannten Tests, die Tests, die ihre Klassen, ihren Pfad oder
  geänderte INI-Einstellungen nennen, und die Tests, die ein Verzeichnis durchlaufen, in dem die Datei liegt, und
  Dateien ihrer Art prüfen (jedes Template von admin4, jede `translation.ts`). Die letzte Regel schließt die Lücke, die
  am 2026-10-16/17 ein Inline-Skript-Tag eines Templates und das Markup einer Übersetzung durchgelassen hat.
- **Packagist.** Ändert der Bereich `composer.json`, muss jede `se7enxweb/*`-Bedingung in `require` und `require-dev`
  zu einem stabilen Release passen, das `https://repo.packagist.org/p2/<vendor>/<package>.json` bereits aufführt. Der
  PHPUnit-Installationsschritt der CI führt auf jedem Zweig `composer update` aus, daher lässt ein Festlegen auf eine
  Version, die getaggt wurde, aber nie Packagist erreicht hat (ein fehlender Webhook, ein Tippfehler, ein Release, das
  noch entsteht), alle zugleich fehlschlagen. Das Gate schlägt zuerst fehl und nennt das Paket, die Bedingung und die
  neueste Version, die Packagist hat; ein nicht erreichbares Packagist lässt es ebenfalls fehlschlagen.
  `GATES_ALLOW_UNPUBLISHED=1` macht aus dem Fehlschlag eine Warnung, nur für einen Notfall: Die CI bleibt rot, bis das
  Release auf Packagist ist. Die Reihenfolge beim Neufestlegen ist daher: die Erweiterung veröffentlichen, sie auf
  Packagist sehen, die neue Bedingung im Wurzelverzeichnis committen, die schnellen Gates auf diesem Commit ausführen
  und genau diesen Commit pushen.

Verbleibende Unterschiede, und warum:

- **Eine PHP-Version für PHPUnit.** Die CI führt PHPUnit unter PHP 8.1 bis 8.5 aus, mit PHPUnit 10.5 (8.1), 11.5 (8.2,
  8.3) und 13 (8.4, 8.5), bei jedem Lauf neu aufgelöst. Das lokale Gate führt PHP 8.5 gegen das `vendor/` der
  Installation aus (PHPUnit 13, das PHP 8.4 braucht). Ein Test, der nur an der API eines alten PHPUnit oder am
  Verhalten von PHP 8.1 bis 8.3 scheitert, wird nur in der CI gefunden. Die Syntax wird lokal mit 8.0 und 8.3 geprüft,
  daher gehört ein Parse-Fehler nicht dazu.
- **`vendor/` ist das der Installation, keine frische Auflösung.** Die CI führt jedes Mal `composer update` aus; das
  lokale Gate kopiert das installierte `vendor/`. Ein neueres Release einer Abhängigkeit kann das Ergebnis der CI
  zwischen zwei Läufen desselben Commits ändern. `--ci` macht für den Boot-Test ein frisches
  `composer install --no-dev` mit PHP 8.0.
- **PHPStan** läuft wie in der CI unter PHP 8.3. Das installierte `vendor/` verlangt PHP 8.4, daher schaltet das Gate
  im sauberen Baum nur für den Lauf von PHPStan die Plattformprüfung von Composer aus.
- **Keine Datenbank-Jobs.** Die MySQL- und PostgreSQL-Jobs der CI (`tests/runtests.php --dsn ...`) werden lokal nicht
  ausgeführt; der Lauf ohne Datenbank deckt dieselben Testdateien ab, außer denen, die eine Datenbank brauchen.
- **Performance** (der Micro-Benchmark) ist in der CI nur eine Warnung und wird vom Gate nicht ausgeführt.
- **Der Commit-Bereich beim Push.** Die CI prüft `before..sha` des Pushs; das Gate prüft `<base>..<rev>` mit
  `<base>` = `origin/main` nach einem Fetch, was bei einem Fast-Forward-Push derselbe Bereich ist.

## Die Regeln für Commit-Nachrichten

Jeder Commit des Projekts beginnt mit dem, was er tut: `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: `
(`.githooks/commit-msg`; die eigenen Betreffs von Git `Merge`, `Revert`, `fixup!` und `squash!` gehen ebenfalls durch).
Der Quality-Schritt **Commit messages** prüft die Commits des Pushs oder des Pull Requests mit
`.githooks/check-commit-range`:

- Ein Commit eines **Projektautors**, einer in `.githooks/commit-msg-authors` aufgeführten E-Mail-Adresse, muss dem
  Format folgen. Ein schlechter Betreff lässt die Prüfung fehlschlagen.
- Ein Commit von **jemand anderem** ist der Commit eines Beitragenden. Jeder Pull Request wird gemergt. Solange er
  offen ist, fasst sein Autor ihn zusammen und rebased ihn auf `main`, wie es die Reviews verlangen
  ([Code reviews](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md#code-reviews)); sobald er gemergt
  ist, wird die Historie von `main` nie umgeschrieben, und spätere Korrekturen kommen als neue Pull Requests. Ein
  Betreff wie `Changed: ...` oder `Fix typo` ist eine Warnung und lässt die Prüfung nie fehlschlagen: weder beim Pull
  Request noch beim Push von `main`, der den Merge trägt.
- Merge-Commits werden nicht geprüft.
- `EXP_COMMIT_MSG_STRICT_ALL=1 sh .githooks/check-commit-range origin/main HEAD` prüft jeden Commit als Commit des
  Projekts, für einen Beitragenden, der möchte, dass sein Branch dem Format folgt.

`make hooks` installiert den Hook, der jede Nachricht bei `git commit` prüft. Die Details stehen in
[quality-checks.md](../../../eng-us/features/6.0/quality-checks.md), „Pull requests“.

## Wie gesund die CI ist: der Bericht

`.github/scripts/ci_health_report.py` liest über die CLI `gh` die Läufe der letzten N Tage und gibt aus:

- die **Erfolgsquote** pro Repository und pro Workflow und Ereignis (`push`, `pull_request`, `schedule`,
  `workflow_dispatch`): Erfolge geteilt durch Erfolge plus Fehlschläge, beim letzten Versuch jedes Laufs. Abgebrochene
  und übersprungene Läufe werden gezählt, aber nicht in die Quote einbezogen.
- die **Schritte, die am häufigsten fehlschlagen**, als fehlgeschlagene Jobs: Jeder Zweig einer Matrix zählt einmal,
  sodass ein PHPUnit-Fehlschlag auf fünf PHP-Versionen fünfmal zählt.
- die **Tests, die am häufigsten fehlschlagen**, gelesen aus den PHPUnit-Listen von Fehlschlägen und Fehlern in den
  Logs fehlgeschlagener Test-Jobs (zuerst unzuverlässige Läufe, dann die neuesten, bis zu `--max-logs`).
- die **unzuverlässigen Läufe**: ein Lauf, der bei einer Wiederholung nach einem fehlgeschlagenen Versuch bestanden
  hat, oder ein fehlgeschlagener Lauf, dessen Commit in einem anderen Lauf desselben Workflows und Ereignisses
  bestanden hat. Ein Test, der in einem solchen Lauf fehlgeschlagen ist, wird als unzuverlässig gezählt.

```sh
python3 .github/scripts/ci_health_report.py                         # this repository, last 14 days
python3 .github/scripts/ci_health_report.py --days 7 --md ci.md     # also a Markdown file
python3 .github/scripts/ci_health_report.py --org se7enxweb --json ci.json   # every repository pushed to lately
python3 .github/scripts/ci_health_report.py --fail-under 90 --min-runs 5     # exit 1 below 90 % in any group
```

Er braucht Python 3.8 oder neuer und ein angemeldetes `gh` (oder `GH_TOKEN`). Er verwendet die GitHub-API: etwa einen
Aufruf pro Repository, einen pro fehlgeschlagenem oder wiederholtem Lauf und einen pro gelesenem Log. Das stündliche
Limit sind 5.000 Aufrufe, geteilt von allem, was dasselbe Token verwendet. `--org` nimmt nur die Repositories, in die
in den letzten N+60 Tagen gepusht wurde (GitHub stoppt geplante Workflows nach 60 Tagen ohne Aktivität);
`--all-repos` nimmt alle, zu je einem Aufruf. Ein Rate-Limit stoppt den Bericht mit einer Meldung, statt die
Repositories als Repositories ohne Läufe zu zählen. Jobs und Logs abgeschlossener Läufe werden in `--cache-dir`
gecacht (`var/tmp/ci_health_cache`, wenn `var/tmp` existiert), sodass ein zweiter Bericht nur die Lauflisten kostet.

Der Workflow **CI health** führt den Bericht jeden Montag für die letzten 7 Tage aus, schreibt ihn in das Log und die
Job-Zusammenfassung und behält Markdown und JSON 90 Tage lang als Artefakt `ci-health`. Führen Sie ihn von Hand aus
dem Tab Actions aus, um die Tage zu wählen oder jedes Repository eines Besitzers hinzuzufügen.

### Die Ausgangslage (2026-10-17)

Der erste Bericht, für 2026-10-03 bis 2026-10-17, vor den Reparaturen der CI in dieser Woche: 1.540 abgeschlossene
Läufe in diesem Repository, 61,7 % bestanden. Nach Workflow und Ereignis: PHPUnit bei Push 57,0 %, bei Pull Requests
30,4 %; Quality bei Push 63,7 %, bei Pull Requests 27,2 %; Performance bei Push 100 % (11 durch neuere Pushes
abgebrochen), bei Pull Requests 77,7 %. Die fehlgeschlagenen Jobs verteilten sich etwa gleich auf Installationen von
Abhängigkeiten (PHPUnit, Database tests, PHP 8.0, PHPStan und die Sniff-Tests) und echte Testfehlschläge. Die Tests,
die am häufigsten fehlschlugen, waren die Tests zur Qualität der Übersetzungen, zum Packer, zur Content Security Policy
der Administration und zur Tab-Reihenfolge der Administration. Sechs Läufe bestanden bei einer Wiederholung.
Vergleichen Sie spätere Berichte mit diesen Zahlen.

Ein Pull Request, dessen Branch `share/filelist.md5` veraltet lässt, erhält von der Quality-Prüfung eine Warnung, keinen
Fehlschlag; wer ihn mergt, aktualisiert das Manifest auf `main`.
