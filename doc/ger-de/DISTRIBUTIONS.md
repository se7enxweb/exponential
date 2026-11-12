# Distributionen von Exponential

Diese Anleitung stellt jede Distribution von Exponential vor, die 7x in der GitHub-Organisation
[se7enxweb](https://github.com/se7enxweb) veröffentlicht: was sie ist und für wen, welche PHP-Versionen und Datenbanken
sie unterstützt, welchen Kernel und welche Administration sie betreibt, was sie mitbringt (Exponential Layouts, die
Media-Site, Demoinhalte), wie man sie installiert, ihr neuestes Release und wie es von ihr aus weitergeht. Am Ende
stehen die Site-Pakete, die die Installer von Exponential 6 anbieten, die PHP-Floor-Pakete und die häufigsten Fragen.

Alles hier wurde aus dem jeweiligen Repository der Distribution gelesen: ihrer `composer.json`, ihrer README und ihren
Tags und Releases (Stand 4. November 2026). Wo die Dokumentation einer Distribution ausführlicher ist, verweist diese
Seite darauf, statt sie zu wiederholen. Die Installation, in der Sie diese Seite lesen, betreibt
[Exponential 6](#3-exponential-6); [Exponential 6.0 installieren](INSTALL.md) installiert es.

> **Exponential und seine Geschichte.** Exponential führt den Code von eZ Publish fort, einem Content-Management-System,
> das sein Hersteller eingestellt hat; die Symfony-basierten Linien führen die freien Linien von eZ Platform und Ibexa
> Open Source fort. 7x pflegt sie alle für aktuelles PHP. „Legacy“ im Namen bedeutet den Kernel von Exponential 6, der
> neben einer Symfony-Plattform läuft, nicht alte Software
> ([Wer ist die Exponential Foundation?](hilfe/wer-ist-die-exponential-foundation.md#glossar)).

## Inhalt

1. [Der Schnellstart: welche soll ich wählen?](#1-der-schnellstart-welche-soll-ich-wählen)
2. [Alle Distributionen im Überblick](#2-alle-distributionen-im-überblick)
3. [Exponential 6](#3-exponential-6)
4. [Exponential Platform Legacy](#4-exponential-platform-legacy)
5. [Exponential Platform Nexus](#5-exponential-platform-nexus)
6. [Exponential Platform DXP](#6-exponential-platform-dxp)
7. [Exponential Platform 3.2.9 und Exponential Basic](#7-exponential-platform-329-und-exponential-basic)
8. [Die Site-Pakete von Exponential 6](#8-die-site-pakete-von-exponential-6)
9. [Die PHP-Floor-Pakete](#9-die-php-floor-pakete)
10. [Von einer Distribution zur anderen](#10-von-einer-distribution-zur-anderen)
11. [Demos und Downloads](#11-demos-und-downloads)
12. [Fragen und Fehlerbehebung](#12-fragen-und-fehlerbehebung)

## 1. Der Schnellstart: welche soll ich wählen?

Beantworten Sie zwei Fragen: Wollen Sie nur in PHP und den Templates von Exponential arbeiten, oder auch in Symfony?
Und wollen Sie ein Framework, auf dem Sie aufbauen, oder eine fertige Website mit Demoinhalten, die Sie umbauen?

| Sie wollen | Wählen Sie | Beginnen Sie mit |
|---|---|---|
| Ein CMS in reinem PHP, auf jeder Datenbank, von PHP 8.0 bis 8.5 | [Exponential 6](#3-exponential-6) | `composer create-project se7enxweb/exponential exponential` |
| Eine bestehende Website mit Exponential 6, mit Symfony daneben | [Exponential Platform Legacy](#4-exponential-platform-legacy) | der Linie der gewünschten Symfony-Generation |
| Eine fertige Symfony-Website mit Exponential Layouts und Demoinhalten | [Exponential Platform Nexus](#5-exponential-platform-nexus) | der Linie der gewünschten Plattform-Generation, immer mit Version |
| Ein neues Projekt nur mit Symfony, ohne den Kernel von Exponential 6 | [Exponential Platform DXP](#6-exponential-platform-dxp) | `composer create-project se7enxweb/exponential-platform-dxp-skeleton` |
| Bei eZ Platform 3.2 bleiben, mit aktuellem PHP | [Exponential Platform 3.2.9](#exponential-platform-329) | dem Branch `3.2` |
| Ein sehr kleines CMS zum Lernen oder für eine private Website | [Exponential Basic](#exponential-basic) | [basic.exponential.earth](https://basic.exponential.earth) |

Wenn Sie unsicher sind, beginnen Sie mit Exponential 6: Diese Linie dokumentiert das Hilfecenter, sie installiert sich
mit zwei Befehlen auf SQLite und läuft auf jeder Datenbank, die Exponential unterstützt.

## 2. Alle Distributionen im Überblick

| Distribution | Paket (Typ) | Kernel | Symfony | PHP | Layouts | Demoinhalte | Neuestes Release |
|---|---|---|---|---|---|---|---|
| Exponential 6 | `se7enxweb/exponential` (`ezpublish-legacy`) | Exponential 6 | keins | 8.0 bis 8.5; 7.4 nutzbar ohne Support | ja | über die Site-Pakete | `v6.0.14` (7. Juni 2026); 6.0.15 in Entwicklung |
| Exponential Platform Legacy | `se7enxweb/exponential-platform-legacy` (`project`) | Symfony-Plattform + Exponential 6 | 3.4, 5.4 oder 7.4 je nach Linie | je nach Linie, 7.4 bis 8.5 | auf den Linien 4.6 und 5.0 | Installer der Plattform | `v2.5.0.10`, `v3.3.44.14`, `v4.6.23.9`, `v5.0.3.7` |
| Exponential Platform Nexus | `se7enxweb/exponential-platform-nexus` (`project`) | Symfony-Plattform, auf den älteren Linien + Exponential 6 | 3.4, 5.4 oder 7.4 je nach Linie | je nach Linie, 8.0 bis 8.5 | ja | ja (Media-Site) | `1.0.0.11`, `v1.1.0.14`, `v1.2.0.5`, `1.3.0.11` |
| CJW Exponential Platform Nexus | `se7enxweb/cjw-exponential-platform-nexus` (`project`) | wie Nexus 1.0.0.x | 3.4 | wie Nexus 1.0.0.x | ja | ja (CJW) | `1.0.0.16` |
| Exponential Platform DXP | `se7enxweb/exponential-platform-dxp-skeleton` (`project`) auf dem Metapaket `se7enxweb/exponential-platform-dxp` | Symfony-Plattform v5 | 7.4 | 8.3 oder neuer | kein Editor | Installer der Plattform | Skeleton `v2.0.0.4`, Metapaket `v0.0.0.4` |
| Exponential Platform 3.2.9 | `se7enxweb/exponential-platform` (`project`) | Kernel von eZ Platform 3.2 | 5.x | 7.3, 8.1 bis 8.4 | nein | Installer der Plattform | `v3.2.9` (28. September 2025) |
| Exponential Basic | `se7enxweb/exponentialbasic` (`project`) | Exponential Basic (Linie 2.x) | keins | 8.2 oder neuer | nein | eigene | `v2.4.0.4` |

Alle sind freie Software unter der GNU GPL, Version 2 oder später. Die DXP-Pakete nennen „GPL-2.0-or-later or
proprietary“, die Lizenzwahl ihres Upstreams.

## 3. Exponential 6

**Was es ist.** Das Content-Management-System und Anwendungsframework, zu dem dieses Hilfecenter gehört: ein frei
anpassbares Inhaltsmodell, Versionen und Übersetzungen, Rollen und Richtlinien, Multisite, ein Shop, Workflows, eine
Template-Engine, REST und GraphQL und die Administrationsdesigns. Das Paket ist `se7enxweb/exponential`, das Repository
[se7enxweb/exponential](https://github.com/se7enxweb/exponential).

**Für wen.** Organisationen, die strukturierte Inhalte, mehrere Websites oder mehrere Sprachen aus einer Installation
betreiben, Hosting-Anbieter und Agenturen sowie Entwickler, die ohne Symfony auf dem Framework aufbauen.

| | |
|---|---|
| PHP | Die `composer.json` akzeptiert 7.4 bis 8.8; 8.0 ist die älteste unterstützte Version, 7.4 ist ohne Support nutzbar ([Warum Exponential PHP 7.4 unterstützt](hilfe/warum-exponential-php-7-4-unterstuetzt.md)). Die kontinuierliche Integration führt die Tests auf PHP 8.1 bis 8.5 aus und prüft Syntax und Start auf 8.0 und 7.4. |
| Datenbanken | SQLite, MySQL und MariaDB, PostgreSQL, MongoDB und Oracle (über die Extension [ezoracle](https://github.com/se7enxweb/ezoracle)) |
| Ausliefern | Exponential Velocity, der PHP-Webserver, den die README für jede Stufe empfiehlt, oder Apache, nginx, LiteSpeed, Caddy oder IIS mit PHP-FPM ([Installieren, Abschnitt 8](INSTALL.md#8-die-website-ausliefern-webserver-https-und-berechtigungen)) |
| Administration | das Design admin4 und die anderen, der Siteaccess editor und die Extension AdminUI |
| Enthält | die Extensions, die das PHP-Floor-Paket für die laufende PHP-Version auswählt: Exponential Layouts, Exponential Intelligence, die Website-Seitenleiste expwt und die übrigen ([Extensions einer neuen Installation](funktionen/6.0/erweiterungen-einer-neuen-installation.md)) |
| Inhalte und Design | ein Site-Paket, das bei der Installation gewählt wird ([Abschnitt 8](#8-die-site-pakete-von-exponential-6)) |
| Neuestes Release | `v6.0.14` (7. Juni 2026). 6.0.15 ist die Linie in Entwicklung; die [Release-Übersicht](release-uebersicht) und die [Branch-Übersicht](branch-uebersicht.md) nennen die Versionen. |

**Installieren.**

```bash
composer create-project se7enxweb/exponential exponential
cd exponential
php bin/php/console exp:install --apply --random-password --url=http://localhost:8087
```

Das installiert das Standard-Site-Paket `sevenx_multisite` auf SQLite. Jede Option, den Setup-Assistenten und den
Kickstarter beschreibt [Exponential 6.0 installieren](INSTALL.md), die Einstellungen danach
[Exponential 6.0 konfigurieren](CONFIGURING.md).

**Weg nach vorn.** Innerhalb der Linie 6.0: [Exponential 6.0 aktualisieren](UPDATE.md). Um später Symfony
hinzuzufügen, ohne die Installation zu verlassen: [Exponential Platform Legacy](#4-exponential-platform-legacy) und
die [Legacy-Bridge](funktionen/6.0/legacy-bridge.md).

## 4. Exponential Platform Legacy

**Was es ist.** Eine Symfony-Plattform mit einem vollständigen Exponential 6 darin: zwei Kernel, die sich eine
Datenbank teilen. Ihre `composer.json` verlangt `se7enxweb/exponential` (auf der Linie 2.5 direkt, auf den neueren
Linien über die Legacy-Bridge), sodass die Administration von Exponential und seine Templates neben der
Symfony-Administration der Plattform laufen (auf der Linie 2.5 unter `/legacy_admin/` und `/admin/`). Repository:
[se7enxweb/exponential-platform-legacy](https://github.com/se7enxweb/exponential-platform-legacy).

**Für wen.** Bestehende Exponential-Websites, die Symfony daneben wollen und Schritt für Schritt vorangehen, und Teams,
die beide Stacks zugleich brauchen.

Es gibt eine Linie je Symfony-Plattform-Generation. Sie wählen die Linie; jede hat ihren eigenen Branch und eigene Tags:

| Linie | Branch | Plattform-Kernel | Symfony | Bridge | PHP (`composer.json`) | Neuester Tag |
|---|---|---|---|---|---|---|
| 2.5 | `master` | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5`) | 3.4 LTS | `se7enxweb/legacy-bridge ^2.1` | `^7.1.3 \|\| ^8.0`; die README: 8.0 oder neuer, 8.0 bis 8.2 getestet | `v2.5.0.10` |
| 3.3 | `3.x` | eZ Platform 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `se7enxweb/legacy-bridge ^3.0` | `^8.0` | `v3.3.44.14` |
| 4.6 | `4.6.x` | Ibexa Open Source 4.6 (`se7enxweb/exponential-platform-dxp` 4.6) | 5.x | `se7enxweb/legacy-bridge ^4.0` | 7.4 bis 8.5; die Release Notes: installiert auf PHP 8.0 bis 8.5, auf 7.4 ohne Support nutzbar | `v4.6.23.9` |
| 5.0 | `5.x` | Plattform v5 (`se7enxweb/exponential-platform-dxp`) | 7.4 | `se7enxweb/legacy-bridge ^5.0` | 8.3 oder neuer | `v5.0.3.7` |

- **Exponential Layouts.** Die Linie 4.6 verlangt es (`se7enxweb/layouts-core`, `netgen/layouts-ibexa`,
  `netgen/layouts-standard`). Auf der Linie 5.0 ist es vorgeschlagen und wird von `composer create-project` ab PHP 8.4
  hinzugefügt; `EXP_WITHOUT_NETGEN_LAYOUTS=1` lässt es weg. Die Linien 2.5 und 3.3 bringen es nicht mit.
- **Datenbanken** (README der Linie 2.5): MySQL ab 5.7, MariaDB ab 10.0, PostgreSQL ab 9.5 und SQLite ab 3.35 für
  Entwicklung und Tests ([SQLite-Installation der Plattform](funktionen/6.0/plattform-sqlite-installation.md)).
- **Installieren** (Linie 2.5, aus ihrer README): `composer create-project --ignore-platform-req=php+
  se7enxweb/exponential-platform-legacy:~2.5.0.8 exponential_website`, danach die Schritte der README. Die anderen
  Linien: die README und die Installationsanleitung ihres Branches.

## 5. Exponential Platform Nexus

**Was es ist.** Ein fertiges Website-Projekt: die Symfony-Plattform mit der Netgen-Media-Site (ihrem Site-Bundle,
Designs und Demoinhalten), Exponential Layouts, Tags und der Netgen Site API, auf den älteren Linien mit Exponential 6
daneben. Repository: [se7enxweb/exponential-platform-nexus](https://github.com/se7enxweb/exponential-platform-nexus);
sein eigenes Buch (`doc/book/`) behandelt jede Linie ausführlich, und
[Exponential Platform Nexus: eine fertige Website in Minuten](funktionen/6.0/plattform-nexus-starter.md) ist die Übersicht
hier.

**Für wen.** Agenturen und Teams, die in Minuten eine vollständige Website mit Demoinhalten, Seitenbaukasten und
Administration wollen und sie dann zu ihrer eigenen umbauen.

| Linie | Plattform-Generation | Symfony | PHP | Kernel von Exponential 6 | Neuester Tag |
|---|---|---|---|---|---|
| 1.0.0.x | eZ Platform 2.5 | 3.4 | in der Praxis 8.1 oder neuer (das Minimum des Kernels); Composer mit `--ignore-platform-req=php+` | ja | `1.0.0.11` |
| 1.1.0.x | eZ Platform 3.3 | 5.4 | 8.0 oder neuer, 8.5 empfohlen | ja | `v1.1.0.14` |
| 1.2.0.x | Ibexa Open Source 4.6 | 5.4 | 8.2 oder neuer, 8.5 empfohlen | ja | `v1.2.0.5` |
| 1.3.0.x (aktuell) | Plattform v5 (DXP 5 OSS) mit Exponential Layouts 2, SQLite als Standard ohne Konfiguration | 7.4 | 8.4 oder neuer | nein | `1.3.0.11` |

Der Standard-Branch `master` trägt Tags `v2.5.0.x` im Upstream-Stil (bis `v2.5.0.9`); er ist keine Nexus-Linie und
nicht der Branch `1.0.0.x`, installieren Sie also nie von ihm. Neue Projekte beginnen auf 1.3.0.x.

- **Datenbanken** (das Buch, Kapitel 2): MySQL 8.0+, MariaDB 10.3+, PostgreSQL 14+ oder SQLite 3.35+ auf 1.1.0.x bis
  1.3.0.x; MySQL 5.7+ oder MariaDB 10.2+, PostgreSQL und SQLite auf 1.0.0.x (der Generation eZ Platform 2.5).
- **Installieren.** Klonen Sie den Branch und checken Sie den neuesten Tag der Linie aus, oder verwenden Sie
  `composer create-project` und **geben Sie immer eine Version an** (zum Beispiel
  `se7enxweb/exponential-platform-nexus:1.3.0.11`): Ohne Version installiert Composer die Upstream-Media-Site von
  Netgen, die Packagist unter demselben Namen führt.
- **Varianten.**
  - **CJW Exponential Platform Nexus** ([se7enxweb/cjw-exponential-platform-nexus](https://github.com/se7enxweb/cjw-exponential-platform-nexus),
    neuestes `1.0.0.16`): der Stack von 1.0.0.x mit den Demoinhalten von CJW und dem enthaltenen Composer-Verzeichnis
    `vendor`.
  - **CJW Exponential Platform Nexus Starter** ([se7enxweb/cjw-exponential-platform-nexus-starter](https://github.com/se7enxweb/cjw-exponential-platform-nexus-starter),
    neuester Tag `1.0.0.7`): eine sofort lauffähige Kopie, abgestimmt auf SQLite. Installieren Sie sie mit `git clone`.
  - **Exponential Platform Nexus Starter** ([se7enxweb/exponential-platform-nexus-starter](https://github.com/se7enxweb/exponential-platform-nexus-starter),
    Tag `1.0.0.0`): die Website auf Plattform v5 als eigenes Repository, PHP 8.4 oder neuer, laut Beschreibung
    „Currently In Public Beta“. Installieren Sie sie mit `git clone`.

## 6. Exponential Platform DXP

**Was es ist.** Eine Digital Experience Platform nur mit Symfony: die freie Fortführung der Linie von eZ Platform und
Ibexa Open Source, gepflegt von 7x. Das Skeleton
[se7enxweb/exponential-platform-dxp-skeleton](https://github.com/se7enxweb/exponential-platform-dxp-skeleton) ist das
Projekt, das Sie anlegen; es verlangt das Metapaket
[se7enxweb/exponential-platform-dxp](https://github.com/se7enxweb/exponential-platform-dxp). Übersicht hier:
[das DXP-Skeleton](funktionen/6.0/plattform-dxp-skeleton.md).

**Für wen.** Neue Symfony-Projekte und Teams, die eZ Platform oder Ibexa Open Source verlassen und aktuelles PHP wollen.

- **Plattform v5 (der Standard-Branch).** Symfony 7.4 LTS, PHP 8.3 oder neuer (8.3 oder 8.5 empfohlen), die
  Admin-Oberfläche unter `/adminui/`, REST, GraphQL, der HTTP-Cache, Solr und die Kernsuche. Laut Beschreibung des
  Skeletons stellt es Ibexa DXP OSS 5.0.6 bereit. Datenbanken (README): MySQL 8.0+, MariaDB 10.3+, PostgreSQL 14+ und
  SQLite 3.35+ für Entwicklung und Tests.
- **4.6.** Der Branch `4.6` des Skeletons und des Metapakets: PHP 7.4 bis 8.5 in der `composer.json`, Symfony 5. Er
  bleibt bei Flysystem 2 (`se7enxweb/flysystem ^2.5`), eine Grenze dieser Generation.
- **Was es nicht mitbringt.** Keinen Layout-Editor, keinen Freigabe-Workflow oder Zeitplaner und keinen Kernel von
  Exponential 6. Für Exponential 6 neben Symfony wählen Sie [Legacy](#4-exponential-platform-legacy) oder
  [Nexus](#5-exponential-platform-nexus).
- **Installieren.** `composer create-project se7enxweb/exponential-platform-dxp-skeleton exponential_website`, danach
  die Schritte seiner README.

## 7. Exponential Platform 3.2.9 und Exponential Basic

### Exponential Platform 3.2.9

[se7enxweb/exponential-platform](https://github.com/se7enxweb/exponential-platform), Branch `3.2`, neuestes Release
`v3.2.9` (28. September 2025): eine Plattform nur mit Symfony auf dem letzten freien Kernel von eZ Platform 3.2
(`se7enxweb/ezplatform-kernel`), PHP 7.3 und 8.1 bis 8.4. Ihre Beschreibung sagt „No Exponential 6.x Legacy Support“
und verweist dafür auf Exponential Platform Legacy. Für Teams, die eZ Platform 3.2 betreiben und dabei bleiben wollen.

### Exponential Basic

[se7enxweb/exponentialbasic](https://github.com/se7enxweb/exponentialbasic), neuestes Release `v2.4.0.4`: das kleine CMS
der Linie 2.x, laut Beschreibung „for everyone, educational purposes, personal use“, auf PHP 8.2 oder neuer mit MySQL,
PostgreSQL, SQLite oder Informix. Es ist eine eigene Codebasis, keine Version von Exponential 6, und hat eine eigene
Website, [basic.exponential.earth](https://basic.exponential.earth).

## 8. Die Site-Pakete von Exponential 6

Exponential 6 selbst bringt keine Website mit; der Installer installiert ein **Site-Paket** vom
[Paketserver](https://exponential.packages.exponential.earth/): seine Inhaltsklassen, Demoinhalte, sein Design und
die Siteaccess-Einstellungen. Sie gehören nur zu Exponential 6; die Symfony-Distributionen haben eigene Installer. Seit
6.0.15 schaltet jedes Site-Paket Extensions nur noch ein, und Composer installiert die Extensions selbst
([Site-Pakete installieren keine Extension-Pakete mehr](bc/6.0/seitenpakete-ohne-erweiterungspakete.md)).

| Site-Paket | Was Sie bekommen | Ohne Demoinhalte |
|---|---|---|
| `sevenx_multisite` (Standard) | 7x MultiSite: das offizielle Standarddesign von Exponential 6.0.15, mehrere Websites in einer Installation | `sevenx_multisite_clean` |
| `sevenx_site` | 7x Simple Website Interface: eine Website | `sevenx_site_clean` |
| `cjw_multisite` | CJW MultiSite: das Design von CJW Network | |
| `ezwebin_site` | Website Interface: das klassische Design für eine Website, für 6.0.15 neu gestaltet | `ezwebin_site_clean` |
| `ezflow_site` | Flow: Seiten aus Blöcken | `ezflow_site_clean` |
| `ezdemo_site` | Exponential Demo Site | `ezdemo_site_clean` |
| `plain_site` | eine fast leere Website, um alles selbst aufzubauen | |

Alle Site-Pakete von 6.0.15 tragen die Version `6.0-15-stable`. Wählen Sie eins mit `exp:install --package=<name>`,
mit `Site_package=` des Kickstarters oder im Schritt „Site-Paket“ des Setup-Assistenten ([Installieren](INSTALL.md)).
Ihre Quellen sind die `-ezpackage`-Repositories (`sevenx-ezpackage`, `cjw_multisite-ezpackage`, `ezwebin-ezpackage`,
`ezflow-ezpackage`, `ezdemo-ezpackage`), in denen eine Extension und ihr Site-Paket gemeinsam veröffentlicht werden.

## 9. Die PHP-Floor-Pakete

`se7enxweb/exponential-php-floor` ist ein Metapaket, das Exponential 6 verlangt, statt jede Extension selbst
aufzuführen. Composer wählt die Linie, die zur PHP-Version passt, unter der es läuft, sodass eine `composer.json` auf
jeder unterstützten PHP-Version installiert:

| Linie | PHP | Was sie auswählt | Neueste |
|---|---|---|---|
| 1.0 | 7.4 (ohne Support nutzbar) | die Releases der Extensions, die noch auf PHP 7.4 laufen | `1.0.6` |
| 1.1 | 8.0 | jede Extension, darunter Exponential Layouts, Exponential Velocity und die Anmeldung in zwei Schritten | `1.1.8` |
| 1.2 | 8.1 bis 8.3 | wie 1.1 | `1.2.8` |
| 1.3 | 8.4 und neuer | wie 1.2, mit den neuesten Releases, wo sie PHP 8.4 brauchen | `1.3.8` |

Sie verlangen es nie selbst: Es kommt mit `se7enxweb/exponential`. Seine README
([se7enxweb/exponential-php-floor](https://github.com/se7enxweb/exponential-php-floor)) nennt, was jedes Release
geändert hat.

## 10. Von einer Distribution zur anderen

| Von | Nach | Wie |
|---|---|---|
| Eine Website, die mit eZ Publish oder einem älteren Exponential gebaut wurde | Exponential 6 | [Von einer älteren Version aktualisieren](INSTALL.md#11-aktualisierung-von-einer-älteren-version) und [Aktualisieren](UPDATE.md) |
| Exponential 6 | Exponential Platform Legacy | dieselbe Datenbank und `ezpublish_legacy/`, verbunden über die [Legacy-Bridge](funktionen/6.0/legacy-bridge.md) |
| Legacy oder Nexus, eine Linie | die nächste Linie | eine Generation nach der anderen, nach der README des Ziel-Branches; für Nexus Kapitel 11 seines Buchs („upgrading between lines“) |
| Exponential Platform Legacy 5.0 oder Nexus 1.3.0.x | Exponential Platform DXP v5 | derselbe Kernel der Plattform v5, ohne den Kernel von Exponential 6 |

Ein Wechsel geschieht nie von selbst: Sichern Sie zuerst die Datenbank und `var/`, und testen Sie an einer Kopie.

## 11. Demos und Downloads

| Was | Wo |
|---|---|
| Exponential 6, der neueste Code mit dem 7x-Design | [demo.exponential.earth](https://demo.exponential.earth/), Administration unter [edit.demo.exponential.earth](https://edit.demo.exponential.earth/) ([Demomodus](hilfe/demomodus.md)) |
| Exponential 6, alle vier Stunden aus dem Hauptbranch neu gebaut | [latest.demo.exponential.earth](https://latest.demo.exponential.earth/) |
| Die CJW-Starter-Website | [site.cjw.demo.exponential.earth](https://site.cjw.demo.exponential.earth/) |
| Exponential Platform (Legacy, Nexus, DXP) | [platform.exponential.earth](https://platform.exponential.earth), Demo unter [platform.exponential.earth/demo](https://platform.exponential.earth/demo) |
| Exponential Basic | [basic.exponential.earth](https://basic.exponential.earth) |
| Pakete | [Packagist, Anbieter se7enxweb](https://packagist.org/packages/se7enxweb/), der [Paketserver](https://exponential.packages.exponential.earth/) für Site-Pakete |

## 12. Fragen und Fehlerbehebung

| Frage oder Symptom | Antwort |
|---|---|
| Hat Exponential Platform DXP einen Layout-Editor? | Nein. Es bringt keinen Layout-Editor, keinen Freigabe-Workflow, keinen Zeitplaner und keinen Kernel von Exponential 6 mit. Wählen Sie für Exponential Layouts Nexus oder Legacy 4.6 bzw. 5.0. |
| `composer create-project se7enxweb/exponential-platform-nexus` hat ein Projekt namens `netgen/media-site` angelegt | Es lief ohne Version und hat die Upstream-Media-Site installiert. Beginnen Sie neu mit einer Version, zum Beispiel `:1.3.0.11`. |
| Composer lehnt die Generation 2.5 auf aktuellem PHP ab | Verwenden Sie `--ignore-platform-req=php+`, wie die README sagt; schalten Sie die Sicherheitssperre von Composer nie ab. |
| Ein Tag von Nexus oder Legacy fehlt auf Packagist | Fünfteilige Tags (`v1.0.0.0.3`, `1.3.0.0.0`) gibt es nur in Git. Installieren Sie sie aus einem Klon. |
| Exponential Layouts fehlt auf Legacy 5.0 | Es wird nur ab PHP 8.4 hinzugefügt, und nicht, wenn `EXP_WITHOUT_NETGEN_LAYOUTS=1` gesetzt ist. |
| Nexus 1.3.0.x hat kein `/legacy_admin/` | Diese Linie betreibt die Plattform v5 ohne den Kernel von Exponential 6. Nehmen Sie für beides 1.2.0.x oder älter. |
| Welches PHP für Exponential 6? | Das neueste, das Sie betreiben können, 8.5 eingeschlossen; 8.0 ist die älteste unterstützte Version. Composer wählt die passende Floor-Linie selbst. |
| Kann ich mein Site-Paket auf einer Symfony-Distribution verwenden? | Nein. Site-Pakete sind für die Installer von Exponential 6; die Plattformen installieren ihre Inhalte mit eigenen Befehlen. |
| Ist „Legacy“ alte Software? | Nein. Es ist der Kernel von Exponential 6 neben einer Symfony-Plattform, und beide werden gepflegt. |

## Verwandte Seiten

- [Exponential 6.0 installieren](INSTALL.md), [Exponential 6.0 konfigurieren](CONFIGURING.md), [Exponential 6.0 aktualisieren](UPDATE.md)
- [Wer ist die Exponential Foundation?](hilfe/wer-ist-die-exponential-foundation.md): jedes Produkt der Familie, mit seinen Quellen
- [Die Produktfamilie kennen](hilfe/fuersprecher/die-produktfamilie-kennen.md): ein Satz je Produkt
- [Das Ökosystem der Exponential-Plattform](geschichte/oekosystem.md)
- [Exponential im Wettbewerb](wettbewerb/README.md): Exponential im Vergleich mit proprietären DXPs, SaaS und den Systemen in allen Sprachen
