---
title: Git-Hooks und make-Ziele
audience: php-developer, developer
category: php-development
order: 110
summary: Die lokalen Prüfungen des Exponential-Repositorys - die Hooks pre-commit, commit-msg und pre-push in .githooks/, jedes make-Ziel von make check bis make lint80-all, das Datei-Manifest share/filelist.md5 und das Format der Commit-Nachrichten.
keywords: Git-Hooks, make-Ziele, Datei-Manifest, Commit-Nachrichten, Format der Commit-Nachricht, git hooks, .githooks, pre-commit, commit-msg, pre-push, Makefile, make check, make lint, make lint80, make manifest-check, make manifest-fix, make phpcs, make phpstan, share/filelist.md5, checkmanifest.php, commit message format, no-verify
type: help
---
# Git-Hooks und make-Ziele

Diese Seite richtet sich an PHP-Entwickler, die zum Exponential-Repository committen oder deren Erweiterung genauso
geprüft werden soll. Das Repository prüft jede Änderung auf dieselbe Weise auf Ihrer Maschine (Git-Hooks und `make`)
und in der CI (dem Quality-Workflow), sodass eine Prüfung, die lokal besteht, auch in der CI besteht.

## Inhalt

1. [Einmal einrichten](#einmal-einrichten)
2. [Die Hooks](#die-hooks)
3. [Die make-Ziele](#die-make-ziele)
4. [Das Datei-Manifest](#das-datei-manifest)
5. [Commit-Nachrichten](#commit-nachrichten)
6. [Einen Hook überspringen](#einen-hook-überspringen)

## Einmal einrichten

```bash
make devtools   # PHP_CodeSniffer, PHPCompatibility and PHPStan into .devtools/ (composer.dev.json)
make hooks      # git config core.hooksPath .githooks
```

`make hooks` gibt `Git hooks enabled: pre-commit, commit-msg, pre-push` aus. Ohne `make devtools` laufen die Hooks
weiterhin und überspringen Code-Stil und PHPStan mit einem Hinweis.

## Die Hooks

| Hook | Prüfung | Lehnt ab |
|---|---|---|
| pre-commit | `php -l` auf jeder vorgemerkten PHP-Datei | einen Syntaxfehler |
| pre-commit | `php -l` mit PHP 8.0, wenn eines gefunden wird (`EXP_PHP80`, `php8.0`, `php80`, `/opt/plesk/php/8.0/bin/php`) | Syntax von PHP 8.1 und neuer |
| pre-commit | `node --check` auf vorgemerktem JavaScript (nicht `*.min.js`), wenn node installiert ist | einen Syntaxfehler |
| pre-commit | `php bin/php/checkmanifest.php --staged` | eine vorgemerkte Datei, deren Prüfsumme in `share/filelist.md5` fehlt oder veraltet ist |
| pre-commit | `php bin/php/checkcodestyle.php --staged` | eine Änderung, die Verstöße gegen den Code-Stil hinzufügt |
| commit-msg | das Format des Betreffs | einen Betreff ohne eines der fünf Präfixe |
| pre-push | PHPStan gegen `phpstan-baseline.neon` | einen Befund, der nicht in der Baseline steht |

## Die make-Ziele

| Ziel | Tut |
|---|---|
| `make check` | `lint`, `manifest-check`, `phpcs` und `phpstan`, wie die CI |
| `make lint` | `php -l` auf den PHP-Dateien, die seit `origin/main` geändert wurden (`BASE=<ref>` für eine andere Basis) |
| `make lint80` | dasselbe mit PHP 8.0 (`PHP80=/path/to/php` oder `EXP_PHP80`) |
| `make lint80-all` | PHP-8.0-Syntax jeder PHP-Datei in git, 8 parallel, wie der CI-Job `php80` (etwa 25 Sekunden) |
| `make manifest-check` | `php bin/php/checkmanifest.php --all`: Jeder Eintrag in `share/filelist.md5` existiert und stimmt überein |
| `make manifest-fix` | `php bin/php/checkmanifest.php --fix`: schreibt veraltete Prüfsummen neu, fügt fehlende Dateien hinzu, entfernt gelöschte |
| `make phpcs` | `php bin/php/checkcodestyle.php --base=$(BASE)`: Code-Stil geänderter Dateien, nur neue Verstöße schlagen fehl |
| `make phpstan` | PHPStan, nur Befunde, die in der Baseline fehlen, schlagen fehl |
| `make phpstan-baseline` | akzeptiert alle aktuellen Befunde als neue Baseline (selten, in einer eigenen Änderung) |

## Das Datei-Manifest

`share/filelist.md5` listet `md5  path` für die Dateien der Distribution auf. Die Upgrade-Prüfung der Administration
(**Setup > Upgrade-Prüfung**, Dateikonsistenz) vergleicht die installierten Dateien damit, sodass ein veralteter Eintrag
dort auf jeder Installation als „geänderte“ Datei erscheint. Nach dem Ändern oder Hinzufügen einer Datei:

```bash
make manifest-fix
git add share/filelist.md5
```

`var/`, `extension/ezoe/` und das Manifest selbst liegen außerhalb davon. In der CI blockiert die Manifest-Prüfung bei
einem Push auf `main` und ist bei einem Pull Request eine Warnung, wo die Person, die den Merge durchführt, es
aktualisiert ([Prüfung der Dateikonsistenz](../../funktionen/6.0/dateikonsistenzpruefung.md)).

## Commit-Nachrichten

Der Betreff beginnt mit dem, was die Änderung tut:

| Präfix | Für |
|---|---|
| `Added: ` | eine neue Funktion, Datei, Klasse oder Fähigkeit |
| `Updated: ` | geändertes oder verbessertes Verhalten |
| `Fixed: ` | eine Fehlerbehebung |
| `Removed: ` | gelöschte Funktionen, Dateien oder Abhängigkeiten |
| `Renamed: ` | umbenannte Dateien, Klassen oder Einstellungen |

Die eigenen Betreffs von Git, `Merge`, `Revert`, `fixup!` und `squash!`, gehen ebenfalls durch. Bei Commits der eigenen
Autoren des Projekts (Adressen in `.githooks/commit-msg-authors`) lässt ein falscher Betreff die CI fehlschlagen; bei
einem Mitwirkenden ist es eine Warnung. Schreiben Sie den Produktnamen in Nachrichten als Exponential, und beschreiben
Sie, warum und was sich geändert hat ([Commit-Nachrichten](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md)).

```bash
sh .githooks/check-commit-range origin/main HEAD                                  # as CI checks a range
EXP_COMMIT_MSG_STRICT_ALL=1 sh .githooks/check-commit-range origin/main HEAD      # every commit as a project commit
```

## Einen Hook überspringen

`git commit --no-verify` und `git push --no-verify` überspringen die Hooks im Notfall. Die CI führt dieselben Prüfungen
aus, sodass das Problem dort zurückkehrt; beheben Sie es, statt es zu überspringen.

## Verwandte Seiten

- [Qualitätsprüfungen](../../funktionen/6.0/qualitaetspruefungen.md)
- [CI und das Nachstellen einer roten Prüfung](ci-und-eine-rote-pruefung-nachstellen.md)
- [PHP-Änderungen beitragen](php-aenderungen-beitragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
