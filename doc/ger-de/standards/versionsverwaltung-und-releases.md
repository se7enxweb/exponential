---
title: Versionsverwaltung und Releases
audience: developer, admin
category: standards
order: 100
summary: Die Git-Konventionen von Exponential - Branches, Merges, Commit-Nachrichten und Changelogs - und seine Release-Richtlinie - dauerhafte Tags, wie Versionsnummern gewählt werden, Extension-Metadaten und Packagist.
keywords: Git, Versionsverwaltung, Quellcodeverwaltung, Branch, main, Merge-Commit, Pull Request, Squash, Force-Push, Commit-Nachricht, Added, Updated, Fixed, Removed, Renamed, Changelog, Release, Tag, annotierter Tag, Versionierung, Semantic Versioning, Packagist, Webhook, ezinfo.php, extension.xml, gitattributes, gitignore, signierte Commits, Git LFS, source control, version control, merge commit, pull request, force push, commit message, annotated tag, versioning, signed commits
type: reference
---
# Versionsverwaltung und Releases

Diese Seite richtet sich an Entwickler, die Commits für Exponential oder eine seiner Extensions erstellen, an
Maintainer, die Releases erstellen, und an Administratoren, die wissen möchten, was eine Versionsnummer verspricht. Sie
beantwortet: Wie die Git-Historie geführt wird, wie Commit-Nachrichten geschrieben und geprüft werden, wo die
Changelogs liegen und wie Releases nummeriert, getaggt und veröffentlicht werden. Wenn Git neu für Sie ist, beginnen Sie
mit [Mit Git entwickeln](../hilfe/mit-git-entwickeln.md).

## Zusammenfassung

| Praxis | Status | Nachweis (kurz) |
|---|---|---|
| Ein Entwicklungs-Branch, `main` | **Erfüllt** | Standard-Branch `main`; die CI läuft auf `main`/`master` |
| Pull Requests werden von ihren Autoren, solange sie offen sind, zusammengefasst und auf `main` rebased und dann mit Merge-Commits gemergt | **Erfüllt** (als Richtlinie) | [Continuous Integration](../../eng-us/bc/6.0/ci.md), „The commit message policy“; Merge-Commits in `git log` |
| Unterzeichnete CLA, bevor ein Beitrag zum Kernel gemergt wird | **Erfüllt** (als Richtlinie) | `CONTRIBUTING.md`, „Copyright assignment (CLA)“; CLA-Kontrollkästchen in `.github/PULL_REQUEST_TEMPLATE.md`; keine automatische Prüfung |
| Kein Force-Push, kein Umschreiben veröffentlichter Historie | **Teilweise erfüllt** | eine Richtlinie; `main` hat keine Branch-Schutzregel (GitHub-API, 18.10.2026) |
| Präfixe im Commit-Betreff | **Erfüllt** | `.githooks/commit-msg`, `.githooks/check-commit-range`, `quality.yml` |
| Weitere Regeln für Commit-Nachrichten (Produktname, keine Nennung von Werkzeugen, keine privaten Pfade) | **Nicht geprüft** | nur Richtlinie, keine automatische Prüfung |
| Changelogs pro Version | **Erfüllt** | `doc/changelogs/<series>/<version>.md`, `doc/changelogs/extensions/` |
| Dauerhafte Tags und Releases | **Erfüllt** (als Richtlinie) | [Extensions-Leitfaden](../anleitungen/extensions.md), „Release it“; [Velocity-Changelog](../../changelogs/extensions/exponential-velocity.md) |
| Versionsnummerierung: die letzte Stelle zählt weiter | **Erfüllt** | auf `0.0.4.9` folgt `0.0.4.10` in der Velocity-Historie |
| Extension-Metadaten entsprechen dem Release | **Erfüllt** (als Richtlinie) | `ezinfo.php` `'Version'` und `extension.xml` `<version>` |
| Veröffentlichung auf Packagist | **Erfüllt** | Packagist-Webhook am Repository; die Push-Prüfung kontrolliert die Constraints |
| Signierte Commits oder Tags | **Nicht erfüllt** | 199 der letzten 200 Commits auf `main` tragen keine Signatur |
| Git LFS | **Nicht anwendbar** | keine LFS-Konfiguration in `.gitattributes` |

## Branches und Merges

- **`main` ist die Entwicklungslinie.** Es ist der Standard-Branch auf GitHub, `composer.json` gibt ihm den Alias
  `6.0.x-dev` (`extra.branch-alias`), und die CI-Workflows laufen bei Pushes und Pull Requests auf `main` (und
  `master`).
- **Gearbeitet wird auf Branches**, die per Pull Request oder per Merge nach `main` gelangen (`CONTRIBUTING.md`,
  „Making Changes“: „Always create a new branch for your work“).
- **Pull Requests werden von ihren Autoren, solange sie offen sind, zusammengefasst und rebased und dann mit einem
  Merge-Commit gemergt.** 7x bittet nachdrücklich darum und verlangt, dass Beitragende ihren Pull Request in logische
  Commits (meist einen) zusammenfassen und ihn, solange er offen ist, auf `main` rebasen (`git rebase -i origin/main`,
  `git push --force-with-lease` nur auf den Branch des Pull Requests), damit der Merge sauber ist
  ([Code-Reviews](../../../CONTRIBUTING.md#code-reviews)). Nach dem Merge wird die Historie von `main` nie
  umgeschrieben, und spätere Korrekturen kommen als neue Pull Requests
  ([Continuous Integration](../../eng-us/bc/6.0/ci.md), „The commit message policy“). Ein Commit eines Beitragenden mit
  nicht standardkonformem Betreff ergibt nur eine Warnung, sodass ein gemergter Pull Request `main` nie rot werden
  lässt. `git log --merges` auf `main` zeigt die Merge-Commits.
- **Ein Beitrag zum Kernel wird gemergt, sobald die CLA seines Autors vorliegt.** 7x und die Exponential Foundation
  verlangen für jeden Codebeitrag zum Kern-Kernel eine unterzeichnete Contributor Licensing Agreement, gleich ob er als
  Pull Request, Patch, Issue oder per Mail eingeht ([Übertragung des Urheberrechts (CLA)](../../../CONTRIBUTING.md#copyright-assignment-cla)).
  Mit Unterzeichnung und Einreichung überträgt der Beitragende das Urheberrecht am Beitrag an 7x, das damit dessen Eigentümer wird, und gibt die Kontrolle darüber auf: 7x darf ihn in jeder Weise verwenden, ändern, neu lizenzieren und verbreiten, und der Beitragende kann ihn weder zurückziehen noch an Bedingungen knüpfen. Die Vorlage für Pull Requests hat dafür ein Kontrollkästchen. Eigene Extensions der Benutzer
  sind nicht betroffen.
- **Kein Force-Push und kein Umschreiben veröffentlichter Historie.** Ein Commit auf `main` ist öffentlich; ein
  Umschreiben würde jeden späteren Commit verschieben und jeden Tag, der in diesen Bereich zeigt, verwaisen lassen. Die
  einzige Ausnahme ist der eigene Pull-Request-Branch eines Beitragenden, solange der Pull Request offen ist: Er wird
  wie oben zusammengefasst, rebased und mit `git push --force-with-lease` gepusht.

> **Warnung** Diese Regeln für Merge und Push sind Richtlinie. Die Repository-Einstellungen auf GitHub erlauben
> weiterhin Squash- und Rebase-Merges, und `main` hat weder eine Branch-Schutzregel noch ein Ruleset (geprüft über die
> GitHub-API am 18.10.2026). Technisch hindert nichts jemanden mit Schreibzugriff an einem Force-Push.

## Commit-Nachrichten

**Der Betreff beginnt mit dem, was die Änderung bewirkt:**

| Präfix | Für |
|---|---|
| `Added: ` | eine neue Funktion, Datei, Klasse, Einstellung oder Fähigkeit |
| `Updated: ` | eine Änderung an bestehendem Verhalten, Templates, Einstellungen oder Code |
| `Fixed: ` | eine Fehlerkorrektur |
| `Removed: ` | etwas Gelöschtes |
| `Renamed: ` | eine umbenannte Datei, Klasse, Einstellung oder Extension |

Beispiel: `Fixed: The redirect keeps the query string on the own host` (aus `.githooks/commit-msg`).

- Geprüft von `.githooks/commit-msg` bei `git commit` (nach `make hooks`) und von `.githooks/check-commit-range` in der
  CI (`quality.yml`, Schritt „Commit messages“) und in der Push-Prüfung. Die Git-eigenen Betreffs `Merge`, `Revert`,
  `fixup!`, `squash!` und `amend!` gehen durch. Merge-Commits werden nicht geprüft.
- Bei einem **Projektautor** (eine Adresse in `.githooks/commit-msg-authors`) schlägt die Prüfung bei einem schlechten
  Betreff fehl; ein **Beitragender** erhält eine Warnung. `EXP_COMMIT_MSG_STRICT_ALL=1` prüft jeden Autor streng.
- **Kombinieren Sie keine Änderungsarten** in einem Commit, wenn sie sich trennen lassen; berührt eine Änderung mehrere
  Bereiche, führen Sie diese im Body auf.

**Der Body sagt, warum und was**, geschrieben für einen Leser, der den Zusammenhang nicht kennt.

Weitere Regeln des Projekts, die kein Werkzeug prüft:

- **Nennen Sie das Produkt Exponential.** Kernel-Bezeichner wie `eZContentObject`, `eZINI` oder `bin/php/ezcache.php`
  sind Namen von Code und bleiben, wie sie sind; nur der Produktname wird als Exponential geschrieben.
- **Keine Nennung von Werkzeugen oder Assistenten**: keine „Generated by“-Fußzeilen, keine Co-Author-Trailer für
  Werkzeuge.
- **Keine privaten oder unveröffentlichten Pfade** in einer Commit-Nachricht, Tag-Nachricht, Versionsnotiz oder
  Pull-Request-Beschreibung: Beschreiben Sie, was wie überprüft wurde, nicht welcher private Helfer es getan hat.
- **Keine Zugangsdaten** und keine maschinenspezifischen Einstellungen in Commits: `.gitignore` schließt
  `/config.env.php`, `/composer.lock`, `/var/storage/`, `/var/log/`, `/.devtools/` und Ähnliches aus.

## Changelogs

- Kernel-Changelogs: `doc/changelogs/<series>/<version>.md`, zum Beispiel
  [6.0.15](../../changelogs/6.0/6.0.15.md) (in Entwicklung; „Not released yet“ in der ersten Zeile). Ältere Serien bis
  zurück zu 2.9 liegen in `doc/changelogs/`.
- Extension-Changelogs: `doc/changelogs/extensions/<name>.md`, zum Beispiel
  [exponential-velocity.md](../../changelogs/extensions/exponential-velocity.md).
- Die Einträge werden aus der Commit-Historie zusammengestellt und nach denselben Präfixen gruppiert (Added, Updated,
  Removed, Renamed; Korrekturen stehen im Velocity-Changelog unter Updated).
- Für ein Upgrade relevante Änderungen erhalten eine Seite in `doc/eng-us/bc/6.0/`, auf die der Changelog verlinkt.

## Releases

### Tags sind dauerhaft

Ein Tag und das daraus erstellte GitHub-Release sind nach dem Push unveränderlich. Composer und Packagist lesen einen Tag
einmal und speichern die gefundene Version zwischen; ein verschobener Tag ergäbe zwei verschiedene Pakete unter einer
Versionsnummer.

- **Löschen, verschieben oder erstellen Sie einen gepushten Tag nie neu**, und ersetzen Sie nie die Assets eines
  veröffentlichten GitHub-Releases.
- **Auf ein fehlerhaftes Release folgt die nächste Version**, deren Hinweise sagen, was am vorherigen falsch war.
- **Prüfen Sie vor dem Push**: ein sauberes Arbeitsverzeichnis, bestandene Tests und Build-Artefakte, die zu ihren
  Quellen passen.

Das ist im [Extensions-Leitfaden](../anleitungen/extensions.md), „Release it“ („A published tag can never be corrected“), und
im [Velocity-Changelog](../../changelogs/extensions/exponential-velocity.md) („A tag is permanent: a bad release is
corrected by the next one, never re-cut“) festgehalten.

### Die nächste Versionsnummer wählen

Immer anhand dessen, was bereits veröffentlicht ist, nie aus dem Gedächtnis:

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
gh release list --limit 10
```

- Sortieren Sie nach Version: Nach Namen sortiert steht `v0.0.4.10` vor `v0.0.4.8`.
- Vergleichen Sie mit `gh release list`: Ein Tag kann ohne Release existieren, und ein Release kann für einen Tag
  existieren, den der lokale Klon noch nicht geholt hat.
- **Die letzte Stelle zählt weiter**: Auf `0.0.4.9` folgt `0.0.4.10`, nie `0.0.5.0`. Das Erhöhen einer höheren Stelle
  ist eine eigene Entscheidung darüber, was das Release bedeutet. Composer und Packagist vergleichen jede Stelle
  numerisch, und ein Tilde-Constraint wie `~0.0.4.9` (`>=0.0.4.9 <0.0.5.0`) akzeptiert `0.0.4.10` bereits.

### Die Versionen des Kernels

- Die Kernel-Version steht in `lib/version.php` (`ExponentialSDK`: `VERSION_MAJOR = 6`, `VERSION_MINOR = 0`,
  `VERSION_RELEASE = 15`). Die `composer.json` im Wurzelverzeichnis hat kein Feld `version`; Composer nimmt die Version
  aus dem Tag, und `main` ist `6.0.x-dev`.
- Veröffentlichte Tags `v6.0.0` bis `v6.0.14` existieren; GitHub-Releases gibt es für `v6.0.10` bis `v6.0.14` (das
  neueste, „Exponential 6.0.14“, am 07.06.2026). `v6.0.10` bis `v6.0.12` sind leichtgewichtige Tags, `v6.0.13` und
  `v6.0.14` annotiert (geprüft mit `git for-each-ref`).

### Releases von Extensions

Jedes Release einer Extension schreibt seine Nummer in die Extension, in dem Commit, auf den der Tag zeigt:

1. Wählen Sie die Nummer wie oben.
2. Setzen Sie `'Version'` in `ezinfo.php` und `<metadata><version>` in `extension.xml` darauf, ohne das Präfix `v`
   (Tag `v1.8.4` ergibt `1.8.4`). Lassen Sie nie `//autogentag//` oder `dev-master` stehen. Beide Dateien nennen außerdem
   die Lizenz und eine `info_url` auf `https://github.com/se7enxweb/<repository>`. Beispiel:
   `extension/ezjscore/ezinfo.php` und `extension/ezjscore/extension.xml` sagen beide `1.8.4`.
3. Ziehen Sie jedes andere Versionsfeld nach, das das Repository führt (`version` in seiner `composer.json`, die
   Kopfzeile seiner eigenen `share/filelist.md5`), und aktualisieren Sie die Prüfsummen dieses Manifests.
4. Prüfen Sie: `php -l ezinfo.php` und `xmllint --noout extension.xml`.
5. Committen Sie („Updated: Version 1.8.4“ oder als Teil des letzten Commits), **dann** taggen Sie diesen Commit, dann
   pushen Sie beides. Taggen Sie nie zuerst und erhöhen Sie die Version danach: Der Tag trüge sonst für immer die alte
   Nummer.

Das Vorgehen mit Befehlen steht im [Extensions-Leitfaden](../anleitungen/extensions.md), Abschnitt „Release it“. Wie die
Info-Seite die beiden Dateien liest, steht unter [Info- und Paketseiten](../funktionen/6.0/info-und-paketseiten.md).

### Packagist

- Jedes `se7enxweb/*`-Repository ist ein Composer-Paket auf Packagist. Ein Webhook am GitHub-Repository informiert
  Packagist über neue Tags; das Wurzel-Repository hat einen (GitHub-API, 18.10.2026). Ein Paket ohne Webhook bleibt auf
  Packagist bei einer alten Version, bis es jemand von Hand aktualisiert.
- Das Neufestlegen der Constraints in der `composer.json` im Wurzelverzeichnis folgt dem Release: Veröffentlichen Sie
  die Extension, prüfen Sie die Version auf Packagist und committen Sie dann den neuen Constraint. Die Push-Prüfung
  lehnt einen Constraint ab, den Packagist noch nicht erfüllen kann
  ([Continuous Integration](../../eng-us/bc/6.0/ci.md), „Packagist“).

## Repository-Dateien

- **`.gitattributes`**: eine Zeile, `*.js linguist-detectable=false`, damit die Sprachstatistik von GitHub das
  Repository als PHP ausweist. Kein `export-ignore`, keine `eol`-Regeln, keine Git-LFS-Filter.
- **`.gitignore`**: Laufzeit- und Cache-Verzeichnisse (`/var/cache/`, `/var/log/`, `/var/storage/`, `/var/autoload/`),
  `/vendor` und `/composer.lock`, Entwicklungswerkzeuge (`/.devtools/`), das temporäre Composer-Overlay der CI
  (`/composer.ci-phpunit.json`), Maschineneinstellungen (`/config.env.php`), Editor-Ordner und private Arbeitsnotizen.
- **`share/filelist.md5`**: Jede committete Datei muss hier ihre aktuelle Prüfsumme haben; siehe
  [Tests und Continuous Integration](tests-und-ci.md).
- **Signierte Commits**: nicht erforderlich. 199 der letzten 200 Commits auf `main` haben keine Signatur
  (`git log --format=%G?`).
- **Git LFS**: wird nicht verwendet.

## Bekannte Lücken

1. Merge-Stil, kein Force-Push und kein Umschreiben der Historie sind nur Richtlinie: Es gibt keinen Branch-Schutz, und
   Squash- und Rebase-Merges sind in den Repository-Einstellungen erlaubt.
2. Die Regeln für Commit-Nachrichten über das Präfix hinaus (Produktname, keine Nennung von Werkzeugen, keine privaten
   Pfade) werden von keinem Werkzeug geprüft.
3. Commits und Tags sind nicht signiert.
4. Ältere Kernel-Tags (`v6.0.10` bis `v6.0.12`) sind leichtgewichtig; Tag-Nachrichten gibt es erst ab `v6.0.13`.
5. `doc/changelogs/README.md` verweist für neuere Releases noch auf die Release-Seite des alten Upstream-Repositorys.

## Verwandte Seiten

- [Mit Git entwickeln](../hilfe/mit-git-entwickeln.md)
- [Extensions-Leitfaden](../anleitungen/extensions.md)
- [Continuous Integration](../../eng-us/bc/6.0/ci.md)
- [Tests und Continuous Integration](tests-und-ci.md)
- [PHP-, PHP-FIG- und Composer-Standards](php-und-composer.md)
- [Programmierstandards](programmierstandards.md)
