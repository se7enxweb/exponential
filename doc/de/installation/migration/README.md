---
title: Migration zu und von Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 5
summary: Der Einstiegspunkt für Migrationen. Eine geprüfte Tabelle aller Exponential-Produkte und ihrer Versionen, eine Entscheidungshilfe und je eine Anleitung pro Weg innerhalb der Exponential-Familie sowie von oder zu anderen Systemen (WordPress, Drupal, Joomla, Magento, Shopify, Contentful, AEM, Baukastensysteme, statische Websites, JavaScript-Anwendungen), mit den tatsächlich vorhandenen Import- und Exportwerkzeugen.
keywords: Migration, migrieren, umziehen, wechseln, Import, Export, Weiterleitungen, URL-Aliase, Umstellung, Rückfallplan, Exponential Platform, Nexus, DXP, Exponential Basic, Platform Legacy, WordPress, Drupal, Joomla, Magento, Shopify, Contentful, AEM, Wix, Squarespace, GoDaddy, Next.js, migration, migrate, move, switch, import, export, static site, redirects, URL aliases, cutover, rollback
type: install
featured: true
---
# Migration zu und von Exponential

Dies ist der Einstiegspunkt für den Umzug einer Website **zu** Exponential 6, **von** Exponential 6 zu einem anderen
Produkt der Exponential-Familie und **weg** von Exponential zu einem anderen System. Die Seite beginnt mit einer
Tabelle der Exponential-Produkte, so wie sie heute tatsächlich veröffentlicht sind, hilft Ihnen dann bei der Wahl
eines Weges und verweist auf je eine ausführliche Anleitung pro Weg. Jede Anleitung ist gleich aufgebaut: was Sie
exportieren können, wie es sich auf das Inhaltsmodell von Exponential abbilden lässt, welche Werkzeuge es gibt, wo
Sie ein eigenes Skript brauchen, URLs und Weiterleitungen, Metadaten für Suchmaschinen, Tests, Umstellung,
Rückfallplan und eine Checkliste.

[Inhalt des Installationshandbuchs](../README.md) · Beginnen Sie mit: [Der allgemeinen Migrations-Checkliste](checkliste.md)

## Inhalt

- [1. Die Exponential-Produkte und ihre Versionen (geprüft)](#1-die-exponential-produkte-und-ihre-versionen-geprüft)
- [2. Welcher Weg passt?](#2-welcher-weg-passt)
- [3. Migrationen innerhalb der Exponential-Familie](#3-migrationen-innerhalb-der-exponential-familie)
- [4. Migrationen von und zu anderen Systemen](#4-migrationen-von-und-zu-anderen-systemen)
- [5. Gemeinsame Referenzen](#5-gemeinsame-referenzen)
- [6. Wie die Angaben auf diesen Seiten geprüft wurden](#6-wie-die-angaben-auf-diesen-seiten-geprüft-wurden)

## 1. Die Exponential-Produkte und ihre Versionen (geprüft)

Exponential ist eine Familie von Produkten, die von 7x gepflegt werden. Ihre Namen ähneln sich, und ihre
Versionsnummern verraten nicht immer, welche Plattformgeneration sie enthalten. Prüfen Sie deshalb diese Tabelle,
bevor Sie irgendetwas planen. Jede Zeile wurde am 2026-10-18 aus dem GitHub-Repository des Produkts unter
`github.com/se7enxweb` gelesen: der Standard-Branch, die Branches, die Releases (`gh release list`), die Tags und die
`composer.json` des Standard-Branches oder des genannten Release-Tags. Wo sich eine Angabe nicht prüfen ließ, steht
das dabei.

### 1.1 Die Produkte

| Produkt | Repository und Composer-Paket | Was es laut seiner `composer.json` ist | Neueste Releases (GitHub) | Enthält den Legacy-Kernel von Exponential 6 |
|---|---|---|---|---|
| **Exponential 6** (der Kernel, zu dem diese Hilfe gehört) | `se7enxweb/exponential`, Typ `ezpublish-legacy` | Der Legacy-PHP-Kernel: INI-Einstellungen, TPL-Templates, die Administrations-Designs, fünf Datenbank-Engines, PHP `^8.0` bis `^8.5` | `v6.0.14` (neueste Release, 2026-06-07); `v6.0.0` bis `v6.0.14` monatlich seit 2024-01-01. Der Standard-Branch `main` ist die Linie 6.0.15 (`lib/version.php`) | Es *ist* der Kernel |
| **Exponential Starter** | `se7enxweb/exponential-starter` (seine `composer.json` nennt das Paket `se7enxweb/exponential`) | Ein sofort lauffähiges Exponential 6 mit enthaltenem vendor-Verzeichnis, auf SQLite abgestimmt (seine Beschreibung nennt 6.0.13) | `1.0.0.0` bis `1.0.0.5` (2026-06-17) | Ja |
| **Exponential Platform** | `se7enxweb/exponential-platform` (Standard-Branch `3.2`) | „Symfony / New Stack Only“: Forks von eZ Platform 3.2 (`se7enxweb/ezplatform-kernel ~1.3.42`, `ezplatform-admin-ui ~2.3.39`), Symfony 5, PHP `^7.3 \|\| ^8.1` bis `^8.4`; keine Legacy Bridge | `v3.2.9` (2025-09-28), die einzige GitHub-Release; Tags `v3.0.x` bis `v3.2.9`; außerdem die Branches `1.7`, `1.13`, `2.5`, `3.0`, `3.1` | Nein |
| **Exponential Platform OSS** | `se7enxweb/oss` | Das Open-Source-Metapaket. Branch `3.3` (Releases `v3.3.0.2` bis `v3.3.0.5`, 2026-04-12), Branches `4.0` bis `4.6`; `master` verlangt die `ibexa/*`-Pakete 5.0 und Symfony 7.4 | `v3.3.0.5` | Nein |
| **Exponential Platform DXP** (Metapaket) | `se7enxweb/exponential-platform-dxp` | Platform v5 auf `master`: PHP `>=8.3`, Symfony 7.4, `se7enxweb/exponential-platform-dxp-core ^5.0.1.0`, `se7enxweb/admin-ui ^5.0.5.0`, REST, GraphQL, HTTP-Cache, Solr- und Core-Suche, die Feldtypen RichText, Matrix und Query, Benachrichtigungen, Messenger, Cron, Standard-Design, JWT und nur `se7enxweb/layouts-core`. Branches `3.3`, `4.0` bis `4.6`, `4.6.x-LB`, `5.x-LB` | `0.0.0.1`, `v0.0.0.2` (2026-04-06) | Nein (auf den `-LB`-Branches baut Platform Legacy auf) |
| **Exponential Platform DXP core** | Repository `se7enxweb/core`, Paket `se7enxweb/exponential-platform-dxp-core` (`replace: ibexa/core`) | Der Repository-Kernel von Platform v4 und v5 | `v5.0.9` (2026-10-05); Tags `v4.6.15` bis `v4.6.24` für die Linie v4 | Nein |
| **Exponential Platform DXP 2.x** (Skeleton, Platform v5) | `se7enxweb/exponential-platform-dxp-skeleton` | Ein Projekt-Skeleton: `se7enxweb/exponential-platform-dxp dev-master`, PHP `>=8.3`. Seine Beschreibung: „Provides Ibexa DXP OSS 5.0.6“ | `v2.0.0.0` bis `v2.0.0.2` (2026-04-11); davor `0.0.0.1`, `0.0.0.2` | Nein |
| **Exponential Platform DXP 1.x** (Skeleton, Platform v4) | `se7enxweb/exponential-platform-v4x-dxp-skeleton` | Ein Projekt-Skeleton: `se7enxweb/exponential-platform-dxp 4.6.x-dev`, PHP `^7.4` bis `^8.5`. Seine Beschreibung: „Provides Ibexa DXP OSS v4.6.x“ | `v1.0.0.0` bis `v1.0.0.2` (2026-04-11) | Nein |
| **Exponential Platform DXP Starter** | `se7enxweb/exponential-platform-dxp-starter` (Paketname in seiner `composer.json`: `se7enxweb/exponential-platform-dxp-skeleton`) | Das Skeleton DXP 2.x sofort lauffähig, vendor-Verzeichnis enthalten, SQLite | `1.0.0.0` (2026-04-26) | Nein |
| **Exponential Platform Nexus** | `se7enxweb/exponential-platform-nexus` (Standard-Branch `master`) | Eine vollständige Website-Distribution: Netgen Layouts, die Netgen-Mediensite, Tags, Site API und eine Administration auf einer Generation von Exponential Platform. Eine Release-Linie pro Generation, siehe [1.2](#12-die-release-linien-von-nexus-und-platform-legacy) | `1.3.0.6` (als Latest markiert), `v1.2.0.1`, `v1.1.0.8`, `1.0.0.11`, `v2.5.0.7` (alle 2026-10-06) | Linien `1.0.0.x`, `v2.5.0.x`, `1.1.0.x`, `1.2.0.x`: ja. Linie `1.3.0.x`: nein |
| **Exponential Platform Nexus Starter** | `se7enxweb/exponential-platform-nexus-starter` | Nexus auf Platform v5 sofort lauffähig: `se7enxweb/exponential-platform-dxp dev-master`, Symfony 7.4, PHP `>=8.4` (seine Beschreibung nennt „Exponential 6.0.13“; seine `composer.json` enthält keinen Legacy-Kernel) | `1.0.0.0` (2026-04-26) | Nein, laut seiner `composer.json` |
| **CJW Exponential Platform Nexus** | `se7enxweb/cjw-exponential-platform-nexus` | Die CJW-Variante von Nexus auf der Generation 2.5: `se7enxweb/exponential ^6.0.12`, `se7enxweb/legacy-bridge ^2.1`, Netgen Layouts 1.4, vendor-Dateien enthalten | `1.0.0.6` bis `1.0.0.12` (2026-10-06) | Ja |
| **CJW Exponential Platform Nexus Starter** | `se7enxweb/cjw-exponential-platform-nexus-starter` (Paketname `se7enxweb/cjw-exponential-platform-nexus`) | Das CJW-Nexus sofort lauffähig, SQLite | `1.0.0.0` bis `1.0.0.3` (2026-07-04) | Ja |
| **Exponential Platform Legacy** | `se7enxweb/exponential-platform-legacy` (Standard-Branch `master`) | Die Hybridlösung: eine Generation von Exponential Platform plus der Exponential-6-Kernel auf einer Datenbank, verbunden durch `se7enxweb/legacy-bridge`. Eine Release-Linie pro Generation, siehe [1.2](#12-die-release-linien-von-nexus-und-platform-legacy) | `v5.0.3.1` (Latest, 2026-10-05), `v4.6.23.3`, `v3.3.44.8`, `v2.5.0.4` (alle 2026-10-05) | Ja, jede Linie |
| **Exponential Platform Legacy Starter** | `se7enxweb/exponential-platform-legacy-starter` | Platform Legacy v5 sofort lauffähig: `exponential-platform-dxp dev-5.x-LB`, `legacy-bridge ^5.0.0.0`, PHP `>=8.3` | `1.0.0.0` (2026-04-26) | Ja |
| **Exponential Basic** | `se7enxweb/exponentialbasic` (Standard-Branch `main`) | Die modernisierte Fortführung des klassischen Exponential-**2.x**-CMS auf PHP 8 (`php ^8.2`): eigene Kernel-Module (`ezarticle`, `ezimagecatalogue`, `ezforum`, `eztrade`, `ezcalendar`, `ezpoll` und weitere). Es ist eine andere Codebasis mit einem anderen Datenmodell als Exponential 6 | `v2.4.0.3` (2026-08-18), `v2.4.0.2`, `v2.4.0.1`, `v2.4.0.0`; Branches `2.4.0.0` bis `2.4.0.2`, `main`, `master` | Nein |
| **Exponential Basic Platform** | `se7enxweb/exponential-basic-platform` | Von seinem Repository als „Beta / Incomplete / Unstable“ beschrieben | keine | Nein |
| **Exponential Velocity** | `se7enxweb/exponential-velocity` | Der für Exponential 6 empfohlene PHP-Anwendungsserver; kein Inhaltsprodukt. Siehe [Die Website ausliefern](../08-die-website-ausliefern.md) | (für die Migrationsplanung nicht nötig) | entfällt |
| `exponential7` | `se7enxweb/exponential7` | „private development branch“: keine Branches, Tags oder Releases sichtbar | keine | **Nicht prüfbar**; planen Sie nicht damit |

Namen, die Sie vielleicht gehört haben, und was sie bedeuten:

| Sie haben gehört | Es ist |
|---|---|
| „Nexus 2.5“, „Nexus 4.x“, „Nexus 5.x“ | Plattform-**Generationen** innerhalb von Nexus, keine Versionsnummern von Nexus. Die Nexus-Versionen sind `1.0.0.x` und `v2.5.0.x` (Generation 2.5), `1.1.0.x` (3.3), `1.2.0.x` (4.6) und `1.3.0.x` (v5) |
| „Platform Legacy 2.5 / 4.x / 5.x“ | Richtig: Seine Versionen tragen die Generation: `v2.5.0.x`, `v3.3.44.x` (früher `v3.0.0.x`), `v4.6.23.x`, `v5.0.x` |
| „DXP 1.x / 2.x“ | Die beiden DXP-**Skeletons**: `v1.0.0.x` ist `exponential-platform-v4x-dxp-skeleton` (Generation 4.6), `v2.0.0.x` ist `exponential-platform-dxp-skeleton` (Generation v5) |
| „Exponential Platform“ ohne Zusatz | `se7enxweb/exponential-platform` `v3.2.9`, die reine Symfony-Distribution |
| „Exponential“, „Exponential 6“, „Exponential Legacy“ | `se7enxweb/exponential`, dieser Kernel |

### 1.2 Die Release-Linien von Nexus und Platform Legacy

Gelesen aus der `composer.json` jedes Release-Tags:

| Produkt und Linie | Neueste Release | Plattformgeneration | Symfony | PHP | Legacy-Kernel und Bridge |
|---|---|---|---|---|---|
| Nexus `1.0.0.x` | `1.0.0.11` | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5.40`) | 3.4 | `^7.1.3` bis `^8.6` | `se7enxweb/exponential ^6.0.12`, `legacy-bridge ^2.1` |
| Nexus `v2.5.0.x` (aus `master`) | `v2.5.0.7` | eZ Platform 2.5 (`~7.5.40`) | 3.4 | wie oben | wie oben |
| Nexus `1.1.0.x` | `v1.1.0.8` | 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `^8.0` | `legacy-bridge ^3.0`, `site-legacy-bundle ^2.0` |
| Nexus `1.2.0.x` | `v1.2.0.1` | 4.6 (`se7enxweb/oss ~4.6.0`) | 5.4 | `>=8.2` | `site-legacy-bundle v2.0.0` mit dem Legacy-Installer (`ezpublish_legacy/`) |
| Nexus `1.3.0.x` | `1.3.0.6` | v5 (`se7enxweb/exponential-platform-dxp dev-master`) | 7.4 | `>=8.4` | keiner in der `composer.json` |
| Platform Legacy `v2.5.0.x` | `v2.5.0.4` | eZ Platform 2.5 (`~7.5.33`) | 3.4 | `^7.1.3 \|\| ^8.1 \|\| ^8.2` | `se7enxweb/exponential ^6.0.12`, `legacy-bridge ^2.1` |
| Platform Legacy `v3.3.44.x` | `v3.3.44.8` | 3.3 (`se7enxweb/oss ~3.3.0.0`, `ezplatform-kernel ~1.3.43`) | 5.4 | `^8.0` | `legacy-bridge ^3.0.0.35` |
| Platform Legacy `v4.6.23.x` | `v4.6.23.3` | 4.6 (`exponential-platform-dxp 4.6.x-LB-dev`) | 5.4 | `^7.4` bis `^8.5` | `legacy-bridge ^4.0.0.0` |
| Platform Legacy `v5.0.x` | `v5.0.3.1` | v5 (`exponential-platform-dxp dev-5.x-LB`) | 7.4 | `>=8.3` | `legacy-bridge ^5.0.0.0` |

> **Warnung**
> Zwei Versionsfallen. **Platform Legacy `v5.0.3` ist keine v5-Release**: Ihre `composer.json` ist die der Linie 2.5
> (`ezpublish-kernel ~7.5.33`, `legacy-bridge ^2.1`). Ihre Release Notes nennen `v5.0.3.1` „the first correct 5.x
> release since v5.0.2“. Fordern Sie bei Composer `v5.0.3.1` (oder `v5.0.2`) an, niemals ein bloßes `^5.0`. Und
> **nennen Sie bei Nexus immer eine Version**: Das Repository enthält noch die Tags der Netgen-Mediensite, von der es
> abgespalten wurde (`3.1.4` bis `3.1.6`), sodass ein `composer create-project` ohne Version einen davon wählen kann.
> Einzelheiten: [Kapitel 16.1](../16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele).

### 1.3 Was jedes Produkt mitbringt, aus Sicht einer Migration

| | Exponential 6 | Platform 3.2 | DXP 1.x / 2.x | Nexus | Platform Legacy | Basic 2.4 |
|---|---|---|---|---|---|---|
| Inhaltsmodell | Klassen, Attribute, Orte | Inhaltstypen (Content Types), Felder, Orte (dieselben Tabellen) | wie Platform | wie seine Generation | beides, eine Datenbank | eigene Module (Artikel, Bildkatalog, Produkte) |
| Templates | TPL | Twig | Twig | Twig (und TPL auf den Legacy-Linien) | beides | eigene Templates |
| Visueller Seitenaufbau | Exponential Layouts (`se7enxweb/explayouts`) | keiner | nur `layouts-core` (keine Editor-Oberfläche in der `composer.json`) | Netgen Layouts mit seinem Editor | über den Legacy-Kernel oder Nexus | keiner |
| Legacy-Workflows, Shop, Newsletter, Informationssammlung | ja | nein | kein Workflow- oder Scheduler-Paket in der `composer.json` | auf den Legacy-Linien, über den Legacy-Kernel | ja | eigener Shop (`eztrade`) |
| REST | v1/v2 (`ezprestapi`), `expservices` | REST v2 | REST v2, GraphQL | wie seine Generation | beides | XML-RPC (`index_xmlrpc.php`) |
| Datenbank-Engines | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | wie die Plattform (MySQL/MariaDB, PostgreSQL) | wie die Plattform; SQLite in den Startern | wie seine Generation; SQLite in den Startern | gemeinsam | MySQL, PostgreSQL, SQLite, Informix (README) |

Die Spalte „Platform“ führt auf, was die `composer.json`-Dateien verlangen. Alles, was dort nicht aufgeführt ist,
gehört nicht zum Produkt, auch wenn das Upstream-Produkt, von dem es abgespalten wurde, es hatte.

## 2. Welcher Weg passt?

Beantworten Sie die Fragen der Reihe nach; das erste „Ja“ führt Sie zu Ihrer Anleitung.

| Frage | Wenn ja |
|---|---|
| Läuft Ihre Website auf einer älteren Release dieses Kernels (eZ Publish 3.x, 4.x oder 5.x, Community 2013.x/2014.x, die reine Legacy-Linie 5.90 oder LoveStack)? | [Ältere Releases dieses Kernels](aeltere-versionen.md) |
| Läuft sie auf Exponential 6, und Sie möchten den Symfony-Stack (Twig, REST v2/GraphQL) **und** die Legacy-Administration oder TPL-Seiten noch eine Weile behalten? | [Exponential Platform Legacy](exponential-platform-legacy.md) |
| Möchten Sie eine fertige Website mit einem visuellen Seitenbaukasten auf dem Symfony-Stack? | [Exponential Platform Nexus](exponential-platform-nexus.md) |
| Möchten Sie ein sauberes, aktuelles Symfony-Projekt als Grundlage (PHP 8.3+, Symfony 7.4)? | [Exponential Platform DXP](exponential-platform-dxp.md) |
| Sind Sie auf Exponential Platform 3.2.x (nur Symfony) und möchten dorthin oder von dort wechseln? | [Exponential Platform (nur Symfony)](exponential-platform.md) |
| Sind Sie auf eZ Platform oder Ibexa (beliebige Version)? | [Kapitel 16](../16-migration-von-ez-platform-und-ibexa.md), danach die Anleitung der Exponential-Familie für Ihr Ziel |
| Sind Sie auf Exponential Basic (der Linie 2.x)? | [Exponential Basic](exponential-basic.md) |
| Läuft die Website auf WordPress, Drupal, Joomla oder Magento? | [WordPress](wordpress.md), [Drupal](drupal.md), [Joomla](joomla.md), [Magento](magento.md) |
| Läuft sie auf einem gehosteten Baukasten oder Shop (Shopify, Wix, Squarespace, GoDaddy)? | [Shopify](shopify.md), [Website-Baukästen](website-baukaesten.md) |
| Läuft sie auf einem Enterprise- oder Headless-System (Contentful, Adobe AEM)? | [Contentful](contentful.md), [Adobe Experience Manager](adobe-aem.md) |
| Ist sie handgeschriebenes HTML, ein statischer Website-Generator oder eine eigene Anwendung? | [Statisches HTML](statisches-html.md), [JavaScript- und Node.js-Anwendungen](javascript-anwendungen.md), [Symfony-Anwendungen](symfony-anwendungen.md), [ColdFusion-Anwendungen](coldfusion-anwendungen.md), [Andere Anwendungen und Datenbanken](andere-anwendungen.md) |
| Verlassen Sie Exponential? | [Migration weg von Exponential](exponential-verlassen.md) |

Faustregeln, die für jeden Weg gelten:

- **Erst die Inhalte, dann die Darstellung.** Bringen Sie die Inhalte in das Inhaltsmodell des Ziels, lassen Sie die
  alte Website weiterlaufen und bauen Sie die neuen Templates auf einer Staging-Kopie gegen die migrierten Inhalte.
- **Eine Änderung nach der anderen.** Wechseln Sie nicht Produkt, PHP-Version, Datenbank-Engine und Design in einer
  einzigen Umstellung. Ziehen Sie zuerst auf das nächstliegende Ziel um, lassen Sie es sich einspielen, und ändern Sie
  dann das Nächste.
- **Halten Sie jede alte Adresse funktionsfähig.** Eine Migration, die Adressen verliert, verliert Suchmaschinen-Ranking
  und Links. Jede Anleitung hat einen Abschnitt zu URLs; die [Checkliste](checkliste.md#4-urls-und-weiterleitungen) enthält
  die Schritte.
- **Proben Sie.** Führen Sie die gesamte Migration vor der echten Umstellung mindestens zweimal auf Kopien durch, und
  messen Sie die Zeit.

## 3. Migrationen innerhalb der Exponential-Familie

| Anleitung | Richtungen | Kurz gesagt |
|---|---|---|
| [Exponential Platform Legacy](exponential-platform-legacy.md) | 6 zu Platform Legacy und zurück | Der sanfteste Weg: dieselbe Datenbank, beide Kernel nebeneinander |
| [Exponential Platform Nexus](exponential-platform-nexus.md) | 6 zu Nexus und zurück | Wählen Sie die Nexus-Linie nach der Generation; die Layouts-Daten brauchen einen eigenen Schritt |
| [Exponential Platform DXP](exponential-platform-dxp.md) | 6 zu DXP 1.x oder 2.x und zurück | Nur Symfony; XmlText zu RichText; die Templates werden neu geschrieben |
| [Exponential Platform (nur Symfony)](exponential-platform.md) | 6 zu Platform 3.2.9 und zurück | Eine ältere Generation; bevorzugen Sie für neue Arbeiten DXP 1.x oder Platform Legacy 3.3 |
| [Exponential Basic](exponential-basic.md) | Basic 2.4 zu 6 und zurück | Unterschiedliche Datenmodelle: eine per Skript durchgeführte Inhaltskonvertierung |
| [Ältere Releases dieses Kernels](aeltere-versionen.md) | eZ Publish 3.x bis 5.x, 2013.x bis 2017.x, LoveStack zu 6 | Die Kette der Datenbank-Updates an Ort und Stelle, dazu die Portierung auf PHP 8 |

## 4. Migrationen von und zu anderen Systemen

| Anleitung | Umfasst |
|---|---|
| [WordPress](wordpress.md) | WXR-Export, die REST-API, WP-CLI; Beiträge, Seiten, Medien, Kategorien und Schlagwörter, Autoren, Kommentare, Weiterleitungen |
| [Drupal](drupal.md) | JSON:API, die Migrate API; Nodes, Taxonomie, Medien, Pfade |
| [Joomla](joomla.md) | Die Web Services API; Beiträge, Kategorien, Schlagwörter, Menüs |
| [Magento und Adobe Commerce](magento.md) | CSV-Export über Data Transfer, REST und GraphQL; Kataloginhalte, CMS-Seiten, URL-Rewrites |
| [Shopify](shopify.md) | Produkt-CSV, die Admin GraphQL API, URL-Weiterleitungen |
| [Wix, Squarespace und GoDaddy](website-baukaesten.md) | Was jeder Baukasten exportieren kann und was von der Live-Website abgelesen werden muss |
| [Contentful](contentful.md) | `contentful space export`, die Content Delivery API und die Content Management API |
| [Adobe Experience Manager](adobe-aem.md) | Content-Pakete, das JSON des Sling GET Servlet, die Assets HTTP API |
| [Statische HTML-Websites](statisches-html.md) | Crawlen oder Dateien lesen, HTML zu XmlText, `.html`-Adressen beibehalten |
| [JavaScript- und Node.js-Anwendungen](javascript-anwendungen.md) | Next.js und andere Headless-Frontends, Markdown/MDX-Inhalte, Exponential als Inhaltsquelle nutzen |
| [Symfony-Anwendungen](symfony-anwendungen.md) | Doctrine-Entities zu Inhaltsklassen und eine Symfony-Anwendung neben Exponential betreiben |
| [ColdFusion-Anwendungen](coldfusion-anwendungen.md) | Die Datenbank und die Dateien der Anwendung auslesen |
| [Andere Anwendungen und Datenbanken](andere-anwendungen.md) | Tabellenkalkulationen, eigenes PHP, Intranets im Stil von SharePoint, beliebige SQL-Datenbanken |
| [Migration weg von Exponential](exponential-verlassen.md) | Jeder Export, den Exponential bietet, und wie Sie Inhalte an ein anderes System übergeben |

## 5. Gemeinsame Referenzen

| Seite | Verwenden Sie sie für |
|---|---|
| [Die allgemeine Migrations-Checkliste](checkliste.md) | Bestandsaufnahme, Einfrieren, Zuordnung, URLs, SEO, Tests, Umstellung, Rückfallplan, Abnahme |
| [Die Import- und Exportwerkzeuge von Exponential](exponential-tools.md) | Jedes vorhandene Werkzeug mit seinen tatsächlichen Optionen und das Grundgerüst eines eigenen Importskripts |
| [Inhalte auf Exponential abbilden](inhaltsmodell-zuordnen.md) | Klassen, Datentypen, Orte, Benutzer, Medien, Übersetzungen, URLs und Metadaten, für jede Quelle |
| [Kapitel 17: Migrationsreferenz](../17-migrationsreferenz.md) | Tabellen, Datentypen im Vergleich zu Feldtypen, INI im Vergleich zu YAML, TPL im Vergleich zu Twig, Passwort-Hashes |

## 6. Wie die Angaben auf diesen Seiten geprüft wurden

- **Exponential-Produkte**: GitHub-Repositories von `se7enxweb`, gelesen am 2026-10-18 (Standard-Branch, Branches,
  Releases, Tags, `composer.json` des Standard-Branches und der Release-Tags). Versionen entwickeln sich weiter;
  prüfen Sie das Repository erneut, bevor Sie eine Version festlegen.
- **Exponential-Werkzeuge**: der Quellcode dieses Repositorys und der Extensions, die es in der `composer.json`
  verlangt; jede Werkzeugseite nennt die Datei, aus der gelesen wurde.
- **Andere Systeme**: die eigene öffentliche Dokumentation der Hersteller, verlinkt aus jeder Anleitung. Wo die
  Dokumentation eines Herstellers eine Funktion nicht bestätigt hat, sagt die Anleitung „nicht geprüft“ und
  beschreibt sie nicht.
- **Geschichte**: eZ Publish war das Produkt, von dem dieser Kernel abstammt; sein Hersteller hat es nach fast 18
  Jahren eingestellt. Der alte Name erscheint auf diesen Seiten nur, um die Releases zu bezeichnen, von denen Sie
  migrieren.

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../eng-us/README-LICENSE.md)).
