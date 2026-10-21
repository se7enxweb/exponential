---
title: Branch-Geschichte
audience: developer, admin
category: development
summary: Wie die Exponential-Repositories heute Branches und Tags verwenden, welche Release-Linien gepflegt werden, auf welchem Branch oder Tag Sie eine Website aufbauen und welche Branches das ursprüngliche SVN-Repository von 2003 bis 2006 hatte.
keywords: Branch, Branches, main, master, Tag, Release, Release-Linie, 6.0, Pull Request, Merge, CI, Velocity, Nexus, Platform, DXP, Extension, SVN, Geschichte, history, release line
type: guide
---
# Branch-Geschichte

Diese Seite erklärt, wie die Exponential-Repositories Branches und Tags verwenden: wo die Entwicklung stattfindet, wie
ein Release entsteht, welche Linien gepflegt werden und auf welchem Branch oder Tag Sie eine Website aufbauen. Der
letzte Abschnitt bewahrt die Branches des ursprünglichen SVN-Repositorys aus den Jahren, in denen die Software
eZ Publish hieß.

Jeder hier genannte Branch und Tag wurde am 18.10.2026 mit den Repositories auf GitHub abgeglichen. Branches und Tags
entwickeln sich weiter; die Befehle unter jeder Überschrift zeigen den aktuellen Stand.

## Kurz gefasst

| Frage | Antwort |
|---|---|
| Wo findet die Entwicklung statt? | Auf `main` von [se7enxweb/exponential](https://github.com/se7enxweb/exponential), dem einzigen langlebigen Branch |
| Wie kommen Änderungen hinein? | Auf kurzlebigen Branches, die über Pull Requests in `main` gemergt werden, jeweils als Merge-Commit |
| Was ist ein Release? | Ein Tag `v6.0.<n>` auf `main`. Der neueste ist `v6.0.14`; `main` trägt 6.0.15 |
| Ändert sich ein Tag jemals? | Nein. Eine Korrektur kommt immer als nächste Version |
| Worauf baue ich eine Produktionswebsite auf? | Auf einem Release-Tag (Composer: eine Version wie `~6.0.14`) |
| Wo liegen die Extensions? | Jede in einem eigenen Repository, mit eigenem Standard-Branch und eigenen Release-Tags |

## Das Kernel-Repository

[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential) enthält den Kernel, die Designs der
Administrationsoberfläche, die mitgelieferten Extensions (`ezjscore`, `ezoe`, `ezformtoken`, `expservices`) und diese
Dokumentation.

### main, der einzige langlebige Branch

`main` ist der Standard-Branch und die Entwicklungslinie von 6.0. Er weist sich als 6.0.15 aus (`lib/version.php`) und
enthält die Änderungen aus dem [Changelog 6.0.15](../changelogs/6.0/6.0.15.md). Für Composer ist er `dev-main`, mit dem
Branch-Alias `6.0.x-dev`, sodass Pakete, die den Kernel mit einer Versionsbedingung wie `^6.0.14` verlangen, gegen ihn
aufgelöst werden.

Jede Arbeit gelangt auf demselben Weg nach `main`:

1. Eine Änderung beginnt auf einem kurzlebigen Branch (eine Funktion, eine Korrektur oder eine Welle zusammengehöriger
   Arbeit), in einem Fork oder im Repository selbst.
2. Der Branch wird als Pull Request gegen `main` vorgeschlagen.
3. Jeder Pull Request wird gemergt, als Merge-Commit. Solange er offen ist, fasst sein Autor ihn zusammen (Squash) und
   setzt ihn per Rebase auf `main` auf, wie die Reviews es verlangen; nach dem Merge wird `main` nie umgeschrieben,
   und eine spätere Korrektur kommt als neuer Pull Request, sodass `main` sicher bleibt.
4. Der Branch wird gelöscht oder einfach liegen gelassen; aus ihm wird nichts weiter gemergt.

`git log --merges origin/main` listet die Merges auf. Commits von Projektautoren beginnen mit `Added:`, `Updated:`,
`Fixed:`, `Removed:` oder `Renamed:`; daraus werden die Changelogs zusammengestellt.

### Die Prüfungen bei jedem Push und Pull Request

GitHub Actions führt bei einem Push auf `main` und bei einem Pull Request (auf dem Merge des Branches in `main`)
dieselben blockierenden Prüfungen aus. [Qualitätsprüfungen](../eng-us/features/6.0/quality-checks.md#ci) beschreibt jede davon;
kurz gefasst:

| Workflow | Was er prüft |
|---|---|
| Quality | PHP- und JavaScript-Syntax der geänderten Dateien, das Dateimanifest `share/filelist.md5`, die Commit-Nachrichten, PHPStan, die Code-Style-Sniffs und einen Job auf PHP 8.0 (Syntax jeder Datei, eine Produktionsinstallation, ein Boot-Test). Der Code-Style ist eine Warnung |
| PHPUnit | Die Testsuiten auf PHP 8.1, 8.2, 8.3, 8.4 und 8.5 sowie die Datenbanktests auf MySQL und PostgreSQL |
| Performance | Benchmarks, wenn sich PHP-Dateien, Templates, Einstellungen oder die Benchmarks ändern |
| CI health | Ein geplanter Bericht über den Zustand der Workflows |

Bei einem Pull Request sind eine veraltete Manifestzeile und die Commit-Nachricht eines Beitragenden Warnungen; wer
mergt, aktualisiert das Manifest auf `main`. [Bevor Sie pushen](../eng-us/features/6.0/quality-checks.md#before-you-push) listet
die Befehle auf, mit denen Sie die blockierenden Prüfungen lokal ausführen.

### Release-Tags

Ein Release ist ein Tag auf einem Commit von `main` mit dem Namen `v6.0.<n>`. Jedes hat eine Changelog-Seite in
`doc/changelogs/6.0` (die neueste: [Changelog 6.0.14](../changelogs/6.0/6.0.14.md)) und ein Release auf
[GitHub Releases](https://github.com/se7enxweb/exponential/releases):

| Tags | Anzahl | Hinweise |
|---|---|---|
| `v6.0.0` bis `v6.0.14` | 15 | Die Linie 6.0, vom 01.01.2024 bis zum 07.06.2026 |
| `2.9-3` bis `4.4.0` | 160 | Die Releases von 2002 bis 2010, mit der Historie importiert; ohne `v` im Namen |

Ein Tag ist dauerhaft. Composer und Packagist lesen ihn einmal, und ein „korrigierter“ Tag ergäbe zwei verschiedene
Pakete mit derselben Versionsnummer. Ein Fehler in einem Release wird durch das nächste Release behoben. Sortieren Sie
Tags nach Version, nie nach Namen (`v6.0.10` steht nach Namen sortiert vor `v6.0.8`):

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
```

### Release-Branches

Neben `main` führt das Repository ab 6.0.7 einen Branch pro Release: `6.0.7` bis `6.0.14`. Ab `6.0.8` zeigt jeder auf
den Commit seines Tags; `6.0.7` hat spätere Commits als `v6.0.7`. Sie dokumentieren jedes Release und sind kein Ort, an
dem die Entwicklung weitergeht; eine Korrektur geht nach `main` und in das nächste Release.

Einige ältere Branches stammen noch aus der Anfangszeit der Linie 6.0 (`6.0.0-beta2`, `6.0.1-beta1`, `6.0.8-devel`),
dazu einige Themen-Branches aus Pull Requests, die vor langer Zeit gemergt wurden. Keiner davon wird gepflegt.

```bash
git ls-remote --heads https://github.com/se7enxweb/exponential.git
```

## Release-Linien

| Linie | Status | Ort |
|---|---|---|
| 6.0.x | Gepflegt: Entwicklung auf `main`, Releases `v6.0.0` bis `v6.0.14` | se7enxweb/exponential |
| 5.x (5.0 bis 5.4, die Linie 5.90 von 2017.08 bis 2019.03) | Nicht gepflegt. 6.0 führt diese Codebasis fort: 6.0.0 begann mit dem letzten Open-Source-Release des Legacy-Kernels | Das archivierte Upstream-Repository; der [Changelog 6.0.0](../changelogs/6.0/6.0.0.md) erklärt den Wechsel |
| 4.x | Nicht gepflegt. Seine Tags bis `4.4.0` liegen im Kernel-Repository | se7enxweb/exponential (Tags) |
| 3.x | Nicht gepflegt. Seine Tags und die SVN-Branches unten werden als Geschichte aufbewahrt | se7enxweb/exponential (Tags) |

Die [Release-Geschichte](../eng-us/release-overview) listet die Releases von 2.9 bis 6.0.15 mit ihren Daten und Quellen auf.

## Extension-Repositories

Jede Extension, die Exponential mit Composer installiert, liegt in einem eigenen Repository unter
[github.com/se7enxweb](https://github.com/se7enxweb). Am 18.10.2026 verlangte die `composer.json` des Kernels 66 davon:

- 34 haben `master` als Standard-Branch und 32 haben `main`; in beiden Fällen ist er die Entwicklungslinie.
- Releases sind Tags, meist mit einem `v` (`eztags` `v2.4.21`, `explayouts` `v1.4.39`); einige haben keines
  (`cjw_newsletter` `4.2.12`, `enhancedselection2` `2.1.8`, `birthday` `1.3.3`, `ngclasslist` `1.1.4`,
  `exponential-legacy-installer` `2.2.4`).
- Der Kernel verlangt jede davon mit einer Tilde-Bedingung auf ihr aktuelles Release, etwa `~2.4.21`, die auch die
  späteren Releases derselben Linie akzeptiert (`2.4.22` und folgende).
- Jedes Release hebt die Version in `ezinfo.php` und `extension.xml` der Extension in dem Commit, der getaggt wird, auf
  die Nummer des Releases an, sodass eine installierte Kopie angibt, welches Release sie ist.

Die mit dem Kernel mitgelieferten Extensions tragen ihre eigene Version: `ezjscore` wird gleichzeitig mit seinem Spiegel
[se7enxweb/ezjscore](https://github.com/se7enxweb/ezjscore) veröffentlicht (beide `1.8.4`).

So sehen Sie die Branches und Tags einer Extension:

```bash
git ls-remote --heads --tags https://github.com/se7enxweb/eztags.git
```

## Velocity und die Plattform-Distributionen

| Repository | Standard-Branch | Releases |
|---|---|---|
| [exponential-velocity](https://github.com/se7enxweb/exponential-velocity), der Webserver | `main` | Tags `v0.0.4.<n>`; der neueste ist `v0.0.4.53` (15.10.2026). Die letzte Stelle zählt weiter: Auf `v0.0.4.9` folgte `v0.0.4.10`. Arbeit an Funktionen und Korrekturen findet auf kurzlebigen Branches `feat/...` und `fix/...` statt |
| [exponential-platform-nexus](https://github.com/se7enxweb/exponential-platform-nexus), die Symfony-Plattform mit Exponential 6 als Legacy-Kernel | `master` | Mehrere Linien, jede mit einem Branch: `1.0.0.x`, `1.1.0.x`, `1.2.0.x`, `1.3.0.x` und `2.5.0.x` (Tags bis `v2.5.0.7`); ältere nummerierte Branches von `1.7` bis `2.5.0.1` |
| [exponential-platform-legacy](https://github.com/se7enxweb/exponential-platform-legacy) | `master` | Unter anderem die Branches `3.x`, `4.6.x` und `5.x` neben älteren; das neueste Release ist `v5.0.3.1` (05.10.2026) |
| [exponential-platform-dxp](https://github.com/se7enxweb/exponential-platform-dxp) | `master` | Branches `3.3` und `4.0` bis `4.6`, dazu `4.6.x-LB` und `5.x-LB`; Release `v0.0.0.2` |
| [exponential-platform-dxp-skeleton](https://github.com/se7enxweb/exponential-platform-dxp-skeleton) | `master` | Branch `4.6`; Releases bis `v2.0.0.2` |
| [exponential-platform](https://github.com/se7enxweb/exponential-platform), die reine Symfony-Plattform 3.2 | `3.2` | Releases bis `v3.2.9` |
| [exponential-starter](https://github.com/se7enxweb/exponential-starter), [exponential-platform-nexus-starter](https://github.com/se7enxweb/exponential-platform-nexus-starter), [exponential-platform-legacy-starter](https://github.com/se7enxweb/exponential-platform-legacy-starter), [exponential-platform-dxp-starter](https://github.com/se7enxweb/exponential-platform-dxp-starter) | `main` | Sofort lauffähige Distributionen mit eigenen Release-Tags (`1.0.0.0` und folgende) |

Den Standard-Branch und die neuesten Releases eines Repositorys lesen Sie mit der GitHub CLI:

```bash
gh api repos/se7enxweb/exponential-platform-nexus --jq .default_branch
gh release list -R se7enxweb/exponential-platform-nexus --limit 5
```

## Auf welchem Branch oder Tag Sie eine Website aufbauen

| Sie möchten | Verwenden Sie |
|---|---|
| Eine Produktionswebsite, die gleich bleibt, bis Sie sich für eine Aktualisierung entscheiden | Ein Release: Composer-Version `~6.0.14` oder den Tag `v6.0.14` in einem Klon |
| Die neuesten Korrekturen vor ihrem Release, oder PHP 8.0 | `main` (`dev-main` für Composer). Noch kein veröffentlichter Tag akzeptiert PHP 8.0 |
| Eine Korrektur an einer Extension vor deren Release | Den Standard-Branch dieser Extension oder einen Branch mit der Korrektur, in einem Klon |
| Eigene Änderungen am Kernel oder an einer Extension | Einen eigenen Branch auf einem Tag oder auf `main`, damit sich Aktualisierungen mit Ihrer Arbeit zusammenführen lassen |

[Mit Git entwickeln](hilfe/mit-git-entwickeln.md) zeigt, wie Sie den Kernel und die Extensions als Git-Klone installieren,
einen Branch für Ihre Website beginnen, ihn aktuell halten und eine Korrektur als Pull Request zurückschicken.
[Versionen, Tags und Branches](installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches) listet auf, welches Release
welche PHP-Version benötigt.

## Historische Branches (ursprüngliches SVN, 3.x)

> **Hinweis:** Dieser Abschnitt stammt aus der Dokumentation der ursprünglichen Software. Er wurde von eZ Systems AS
> und den Beitragenden zwischen 2003 und 2006 verfasst, als die Software eZ Publish hieß, und wird hier inhaltlich
> unverändert zum Nachschlagen aufbewahrt. Sein Originaltext wird unter der Lizenz verbreitet, mit der er kam, der GNU
> General Public License, Version 2 (Copyright (C) 1999 - 2014 eZ Systems AS and contributors). Der Text darüber ist
> neue Dokumentation unter der GNU Free Documentation License 1.3 oder später.

Das ursprüngliche Repository war SVN, wo ein Branch ein Verzeichnis war: `stable/3.x` für die Pflege einer
Release-Linie und `unstable/...` für Entwicklungsarbeit. Ein Branch begann bei einer Revision („Revision“) und endete,
wenn er geschlossen wurde, bei einer letzten Revision. Diese Branches sind nicht im Git-Repository; die Release-Tags
derselben Jahre schon.

### Stabile Branches

| Branch | Begonnen bei Revision | Letzte Revision | Datum |
|---|---|---|---|
| `stable/3.0` | 2303 | nicht erfasst | 09.04.2003 |
| `stable/3.1` | 2708 | nicht erfasst | 12.06.2003 |
| `stable/3.2` | 3240 | nicht erfasst | 26.08.2003 |
| `stable/3.3` | 4648 | nicht erfasst | 02.01.2004 |
| `stable/3.4` | 6744 | nicht erfasst | 02.06.2004 |
| `stable/3.5` | 10325 | 10454 | 10.12.2004 |
| `stable/3.6` | 11989 | nicht erfasst | 26.05.2005 |

### Entwicklungs-Branches

| Branch | Begonnen bei Revision | End-Revision | Datum |
|---|---|---|---|
| `unstable/datatype-development-20030530` | 2628 | 2711 | 30.05.2003 |
| `unstable/2005-02-17-no-references` | 10879 | nicht erfasst | 17.02.2005 |
| `unstable/2006-02-10-multilang-v2` | 14785 | nicht erfasst | 20.02.2006 |

Der Datentyp-Branch wurde in Revision 2714 in den Trunk gemergt; die
[Release-Geschichte](../eng-us/release-overview) enthält seinen Eintrag und die Releases der Linien 3.x und 4.x.
