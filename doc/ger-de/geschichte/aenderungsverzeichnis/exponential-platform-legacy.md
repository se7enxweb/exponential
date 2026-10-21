# Änderungsverzeichnis: exponential-platform-legacy

Jede Änderung an `exponential-platform-legacy` seit Beginn der se7enxweb-Ära, die älteste zuerst: 46 Änderungen an 345 Dateien. Jede Zeile nennt das Datum, den Commit, die Art der Änderung, ihre Commit-Nachricht und wie viele Dateien sie berührt hat (+ hinzugefügte / − entfernte Zeilen). Release-Tags sind markiert. Die erzählende Dokumentation (Funktionen, BC-Hinweise, Spezifikationen, die Chronik) erklärt, was diese Änderungen bringen; dieses Verzeichnis ist die vollständige Aufzeichnung, auf der sie beruht. Die Commit-Nachrichten stehen hier so, wie sie im Repository festgehalten sind, also meist auf Englisch.

| Art | Änderungen |
|---|---|
| Aktualisiert | 31 |
| Hinzugefügt | 7 |
| Sonstiges | 5 |
| Zusammengeführt | 3 |

## 2025-07 (6 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-07-01 | `1e0297e` | Aktualisiert | Update composer.json replaced package vendor name | 1 | +2 / −2 |  |
| 2025-07-01 | `f5538cb` | Aktualisiert | Update composer.json increased package php support | 1 | +1 / −1 |  |
| 2025-07-01 | `6eedef3` | Aktualisiert | Update composer.json switch package dep vendor for Exponential and version | 1 | +1 / −1 |  |
| 2025-07-01 | `5d53cb6` | Aktualisiert | Update composer.json change from gplv2 only to gplv2 or later | 1 | +1 / −1 |  |
| 2025-07-01 | `c01377f` | Aktualisiert | Update composer.json homepage url vendor name change | 1 | +1 / −1 |  |
| 2025-07-01 | `0beb934` | Aktualisiert | Update composer.json replaced package dependencies vendor name. Removed older behat bundle as not supported at this time. | 1 | +4 / −5 |  |

## 2025-08 (27 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-08-24 | `333d353` | Aktualisiert | Updated: Updating composer.json configuration for basis of a php8.2+ possible installation. This change set allows composer to install all the packages successfully. this is the first major progress point. after composer started running post package installation scripts (code already installed) it fails due to sub package dependencies like symfony / ez platform / sensio / and all other symfony based ecosystem packages that went previously unmaintained. Rejoice, 7x has already successfully patched this platform post packages installation so the composer install scripts run successfully all we need to do is fork various repositories and patch the files and release them as composer packages (forked by 7x). this list is long. more as it happens. Feature stable composer2 php8.2 installation. | 1 | +22 / −15 |  |
| 2025-08-24 | `61e235b` | Aktualisiert | Update: Changes required to install and boot ezplatform 2.5 gpl based site | 11 | +103 / −11 |  |
| 2025-08-24 | `fef26da` | Aktualisiert | Updated: Minor path bugfix for composer bagsed autoloads workaround patch. Bugfix. | 1 | +3 / −2 |  |
| 2025-08-24 | `c6bf309` | Aktualisiert | Updated: Path to default pagelayout example fix. Bugfix. | 1 | +1 / −1 |  |
| 2025-08-24 | `dbceda7` | Aktualisiert | Updated: Updated composer.json to fork further required composer packages for ezplatform 2.5 gpl to run with php 8.2+. Bugfix. | 1 | +2 / −2 |  |
| 2025-08-24 | `23adafb` | Aktualisiert | Update config.yml updated configuration paths to support error free installation via composer package post install scripts. Bugfix. | 1 | +3 / −3 |  |
| 2025-08-24 | `ca14ee1` | Zusammengeführt | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2025-08-25 | `40cf39f` | Aktualisiert | Updated: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. | 5 | +35 / −20 |  |
| 2025-08-25 | `294783f` | Aktualisiert | Updated: Updated these files to beable to install this now forked package and it's depencies. Stable progress point. Bugfixes. | 1 | +2 / −2 |  |
| 2025-08-25 | `528da80` | Aktualisiert | Update composer.json change package name. Forking for changes. | 1 | +3 / −3 |  |
| 2025-08-25 | `2287268` | Aktualisiert | Update composer.json testing pulling latest changes from child package. | 1 | +1 / −1 |  |
| 2025-08-25 | `559dddf` | Aktualisiert | Update composer.json testing changegs to allow dev-main to exponential 6 install. Testing. | 1 | +3 / −1 |  |
| 2025-08-25 | `7a6abda` | Aktualisiert | Update composer.json replaced Exponential package name with exponential. Testing. | 1 | +1 / −1 |  |
| 2025-08-25 | `9b35c25` | Aktualisiert | Update composer.json testing with release version instead of dev-main. Testing. | 1 | +1 / −1 | v2.5.0.0 |
| 2025-08-25 | `544d935` | Aktualisiert | Update composer.json minor change to support replacement installer. testing | 1 | +1 / −0 |  |
| 2025-08-25 | `ae60784` | Aktualisiert | Update composer.json version bump for ezplatform-admin-ui-assets which just got a manual missing files merge bugfix. Testing. | 1 | +1 / −1 |  |
| 2025-08-25 | `5e5af1c` | Aktualisiert | Updated: Updated documentation for package. Clarifictaions only. Added md documentation. Moved default readme to doc folder for safe keeping. | 9 | +788 / −75 |  |
| 2025-08-25 | `d283978` | Zusammengeführt | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2025-08-25 | `0627f49` | Aktualisiert | Updated: Updated documentation for package. Clarifictaions only. Replaced text of new README.md. Documentation. | 1 | +43 / −47 |  |
| 2025-08-25 | `6892bf6` | Sonstiges | Create FUNDING.yml | 1 | +3 / −0 |  |
| 2025-08-25 | `370b0ed` | Aktualisiert | Updated: Updated README.md documentation for package. Removed Logo Images. Documentation. | 1 | +0 / −3 |  |
| 2025-08-25 | `96c2f6c` | Zusammengeführt | Merge remote-tracking branch 'refs/remotes/origin/master' | 0 | +0 / −0 |  |
| 2025-08-25 | `b3f0465` | Aktualisiert | Updated: Updated installation documentation for package. Clarifictaions only. Documentation. | 1 | +11 / −1 |  |
| 2025-08-25 | `531199a` | Aktualisiert | Updated: Updated installation documentation for package. Clarifictaions only. Documentation. | 1 | +4 / −0 |  |
| 2025-08-29 | `a17b406` | Hinzugefügt | Added: Added software example .htaccess mod_rewrite configuration to web dir. Feature improvement. | 1 | +55 / −0 |  |
| 2025-08-29 | `987808a` | Hinzugefügt | Added: Added software example .htaccess mod_rewrite configuration to doc/apache2 dir. Feature improvement. | 1 | +55 / −0 |  |
| 2025-08-29 | `d143c44` | Hinzugefügt | Added: Added software example robots.txt configuration to web dir. Feature improvement. | 1 | +2 / −0 |  |

## 2025-09 (3 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2025-09-14 | `8344b2b` | Aktualisiert | Updated: Version bump to 1.5.33 to include rebranded logo in ezplatform-admin-ui composer package. Rebranding | 1 | +1 / −1 |  |
| 2025-09-14 | `f71c97e` | Aktualisiert | Updated: Required changes to boot admin after rebranding and further test results. Bugfix. | 1 | +1 / −1 |  |
| 2025-09-14 | `eb660eb` | Aktualisiert | Updated: Required changes to dump jsroutes. We fork so they don't have to maintain. Missed change in first release caused class conflict. Core Bugfix. | 1 | +2 / −2 | v2.5.0.1 origin/2.5.0.1 |

## 2026-03 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-03-02 | `88da983` | Sonstiges | chore: add GitHub Sponsors funding metadata | 1 | +1 / −1 |  |

## 2026-04 (8 Änderungen)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-04-09 | `8e94fb2` | Sonstiges | docs: rewrite README and INSTALL.md for Exponential Platform Legacy 2.5.0.x | 2 | +965 / −137 |  |
| 2026-04-09 | `b231844` | Sonstiges | chore: upgrade PHPUnit to ^11.5 and modernize phpunit.xml.dist | 2 | +14 / −15 |  |
| 2026-04-09 | `c168757` | Sonstiges | chore: set PHPUnit to ^10.5 (max compatible with Symfony 3.4 stack) | 2 | +2 / −2 |  |
| 2026-04-09 | `7021d7e` | Hinzugefügt | feat: add SQLite installer support for local dev and testing | 6 | +2034 / −0 |  |
| 2026-04-09 | `31d165d` | Aktualisiert | Updated: Version Bump for exponential package requirements to latest release in composer.json. Upgrade, | 1 | +1 / −1 |  |
| 2026-04-10 | `9e922cc` | Hinzugefügt | Added: Six new theme directories ported from 3.x to provide the full design chain required for the site siteaccess to render correctly with notices, developer bar, footer, and content views | 266 | +11203 / −19 |  |
| 2026-04-10 | `2194e96` | Aktualisiert | Updated: README.md and doc/INSTALL.md to document full SQLite and Oracle database support | 2 | +729 / −10 | v2.5.0.2 origin/2.5.0.2 |
| 2026-04-21 | `b417ef6` | Hinzugefügt | feat: add project app SCSS/webpack build, brand image, and asset pipeline | 7 | +9757 / −13 | v2.5.0.3 |

## 2026-07 (1 Änderung)

| Datum | Commit | Art | Änderung | Dateien | +/− | Release |
|---|---|---|---|---|---|---|
| 2026-07-08 | `8479bb7` | Hinzugefügt | Added: Added .gitattributes file to update GitHub listed language usage to match other repositories in account. Repo Maintinence | 1 | +9 / −0 |  |
