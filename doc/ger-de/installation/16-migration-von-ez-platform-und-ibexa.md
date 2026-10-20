# 16. Migration von eZ Platform und Ibexa

Dieses Kapitel überführt eine Site, die auf eZ Platform 1.x, 2.x oder 3.x oder auf Ibexa DXP oder Ibexa OSS 3.3, 4.x
oder 5.0 läuft, auf eine der Exponential-Plattformen. Es gibt zwei Ziele. **Exponential Platform Nexus** (und die
anderen Distributionen von Exponential Platform) ist der gleichwertige Weg: derselbe Symfony-Stack, dieselbe Generation
des Datenbankschemas und dieselben PHP-Namespaces, wobei die Pakete gegen die se7enxweb-Forks ausgetauscht werden.
**Exponential 6.0**, der Legacy-Kernel, den dieses Repository enthält, ist der Weg nach unten: Die Inhalte wandern in
die Welt von INI und TPL, entweder für sich allein oder innerhalb von **Exponential Platform Legacy**, wo sich der
Legacy-Kernel und ein Symfony-Stack eine Datenbank teilen. Das Kapitel beginnt damit, was jedes Ziel ist, und mit einer
Entscheidungstabelle; danach beschreibt es jeden Weg Schritt für Schritt: Pakete, Namespaces, Konfiguration, Datenbank je
Version (mit dem SQL), Feldtypen, Templates, Suche, Caches, Benutzer, Bilder und URL-Aliase, die Portierung eigener
Bundles und eine echte Portierung vom Symfony-Stack auf den Legacy-Kernel. Es endet mit den häufigen Problemen, der
Prüfung, dem Rollback, einem Plan und einer Checkliste. Es deckt jeden Punkt der Migrations- und Update-Seiten des
Herstellers ab und sagt, wo der Exponential-Code von ihnen abweicht.

[Inhalt](README.md) · Zurück: [15. Migration vom 5.x-Legacy-Stack](15-migration-von-5x-legacy.md) · Weiter: [17. Migrationsreferenz](17-migrationsreferenz.md)

## Inhalt

- [16.1 Die beiden Ziele](#161-die-beiden-ziele)
- [16.2 Einen Weg wählen](#162-einen-weg-wählen)
- [16.3 Die Seiten des Herstellers und wo dieses Kapitel sie abdeckt](#163-die-seiten-des-herstellers-und-wo-dieses-kapitel-sie-abdeckt)
- [16.4 Bevor Sie beginnen: Bestandsaufnahme, Einfrieren und Sicherungen](#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen)
- [16.5 Weg A: zu Exponential Platform Nexus](#165-weg-a-zu-exponential-platform-nexus)
- [16.6 Weg B: hinunter zum Legacy-Kernel von Exponential 6.0](#166-weg-b-hinunter-zum-legacy-kernel-von-exponential-60)
- [16.7 Häufige Probleme](#167-häufige-probleme)
- [16.8 Die Migration prüfen](#168-die-migration-prüfen)
- [16.9 Rollback](#169-rollback)
- [16.10 Ein Plan und seine Phasen](#1610-ein-plan-und-seine-phasen)
- [16.11 Checkliste](#1611-checkliste)
- [Referenzen](#referenzen)

Konventionen: Befehle für ein Symfony-Projekt laufen aus dessen Projektwurzel (`php bin/console ...`); Befehle für den
Legacy-Kernel laufen aus der Legacy-Wurzel (`php bin/php/...`), also dem Installationsverzeichnis von Exponential 6.0
oder dem Verzeichnis `ezpublish_legacy/` eines Symfony-Projekts. `USER`, `DATABASE` und Ähnliches sind Platzhalter. Alte
Produktnamen erscheinen nur dort, wo sie das System bezeichnen, von dem Sie migrieren.

## 16.1 Die beiden Ziele

### Wovon Sie migrieren

| Quelle | Symfony | Kernel-Paket | Haupt-Konfigurationsschlüssel | PHP-Namespace der API |
|---|---|---|---|---|
| eZ Platform 1.x (1.7 LTS, 1.13 LTS) | 2.8 / 3.4 | `ezsystems/ezpublish-kernel` 6.x | `ezpublish:` | `eZ\Publish\API\...` |
| eZ Platform 2.x (2.5 LTS) | 3.4 | `ezsystems/ezpublish-kernel` 7.5 | `ezpublish:` | `eZ\Publish\API\...` |
| eZ Platform 3.0 bis 3.2, Ibexa DXP / OSS 3.3 LTS | 5.x (5.4 ab 3.3.13) | `ezsystems/ezplatform-kernel` 1.x | `ezplatform:` | `eZ\Publish\API\...` |
| Ibexa DXP / OSS 4.0 bis 4.6 LTS | 5.4 | `ibexa/core` 4.x | `ibexa:` | `Ibexa\Contracts\Core\...` |
| Ibexa DXP / OSS 5.0 | 7.3, später 7.4 | `ibexa/core` 5.x | `ibexa:` | `Ibexa\Contracts\Core\...` |

Das Datenbankschema hat sich auf dieser Linie dreimal geändert: eZ Platform 3.0 entfernte etwa 80 Tabellen des
Legacy-Schemas, 4.0 behielt die Tabellennamen `ez*` und fügte Tabellen `ibexa_*` hinzu, und 5.0 benannte jede
Kerntabelle in `ibexa_*` um (Abschnitte [16.5.6](#1656-die-datenbank-generation-für-generation) und [16.6.1](#1661-was-beide-gemeinsam-haben)).

### Ziel A: Exponential Platform Nexus und die Distributionen von Exponential Platform

**Exponential Platform Nexus** (Repository und Paket `se7enxweb/exponential-platform-nexus`) ist eine fertige
Site-Distribution, die von 7x gepflegt wird: Netgen Layouts, das Design der Netgen Media Site, Netgen Tags, die Netgen
Site API und eine Administrationsoberfläche auf einer Ibexa-kompatiblen Symfony-Plattform, deren Pakete se7enxweb-Forks
sind. Ihre GitHub-Beschreibung nennt sie „Platform v2/v3/v4/v5 + Exponential v6.x (Legacy) + Netgen Media Site + NG Layouts + Symfony
7.4/5.4/3.4 PHP 8.0-8.5“. Sie ist nicht ein Stack, sondern eine Linie je Plattformgeneration, jede auf einem eigenen
Branch mit einer eigenen Tag-Serie; abgelesen aus der `composer.json` jedes Release-Tags:

| Nexus-Linie (Tags) | Plattformgeneration | Symfony | PHP (`composer.json`) | Legacy-Kernel enthalten |
|---|---|---|---|---|
| `1.0.0.x` (`v1.0.0.0.1` bis `1.0.0.10`) | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5.40`) | 3.4 (`se7enxweb/symfony v3.4.55`) | `^7.1.3 \|\| ... \|\| ^8.6` | ja: `se7enxweb/exponential ^6.0.12`, `se7enxweb/legacy-bridge ^2.1` |
| `1.1.0.x` (`v1.1.0.0` bis `v1.1.0.7`) | eZ Platform 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `^8.0` | ja: `se7enxweb/legacy-bridge ^3.0`, `se7enxweb/site-legacy-bundle ^2.0` |
| `1.2.0.x` (`v1.2.0.0`, „Ibexa 4.6 / v4 first release“) | Ibexa OSS 4.6 (`se7enxweb/oss ~4.6.0`) | 5.4 | `>=8.2` | ja: `se7enxweb/site-legacy-bundle v2.0.0` zieht `se7enxweb/ibexa-legacy-bridge` 4.x und `se7enxweb/exponential` nach (`dev-main` im Lock) |
| `1.3.0.x` (`1.3.0.0.0`, `1.3.0.1` bis `1.3.0.5`, die neueste Release) | Platform v5 (`se7enxweb/exponential-platform-dxp`, Core `v5.0.7` im Lock) | 7.4 | `>=8.4` | nein |
| `v2.5.0.x` (Tags `v2.5.0.0` bis `v2.5.0.6` des Nexus-Repositorys; `v2.5.0.0` und `v2.5.0.1` nennen das Paket noch `se7enxweb/exponential-platform-legacy`) | die 2.5-Linie von Exponential Platform Legacy (deren eigene 2.5-Tags `v2.5.0.0` bis `v2.5.0.3` sind), als Nexus verpackt | 3.4 | wie 1.0.0.x | ja |

Zwei Fallen, wenn Sie eine Nexus-Linie mit Composer installieren. **Fünfteilige Tags sind nicht auf Packagist**:
`v1.0.0.0.1` bis `v1.0.0.0.3` und `1.3.0.0.0` gibt es nur in git; verlangen Sie also `1.0.0.10`, `v1.1.0.7`, `v1.2.0.0`
oder `1.3.0.5`. Und **nennen Sie immer eine Version**: Das Repository ist ein Fork der Netgen Media Site und behält
deren Tags, die Packagist unter dem Nexus-Namen aufführt; `composer create-project se7enxweb/exponential-platform-nexus`
ohne Version installiert daher `3.1.6`, die Upstream-`netgen/media-site`. Der Standard-Branch `master` ist die
2.5-Linie; klonen Sie mit `--branch 1.3.0.x` (oder der gewünschten Linie).

Die Nexus-5-Linie gibt es auch als eigenen Starter, `se7enxweb/exponential-platform-nexus-starter`; diesen betreibt die
Referenzinstallation hinter diesem Handbuch (SQLite, Symfony 7.4, `se7enxweb/exponential-platform-dxp-core v5.0.7`).
Neben Nexus gibt es schlichte Distributionen ohne die Demo-Site: `se7enxweb/exponential-platform` (3.2.9, „runs the
Exponential Platform kernel on Symfony 5.1/5.4 LTS (full-stack, single kernel — no LegacyBridge)“),
`se7enxweb/oss` (das 3.3-Metapaket, `replace: ibexa/oss`), `se7enxweb/exponential-platform-v4x-dxp-skeleton`
(4.6.x, PHP 7.4 bis 8.5, Symfony 5.4) und `se7enxweb/exponential-platform-dxp-skeleton` mit dem Metapaket
`se7enxweb/exponential-platform-dxp` (Platform v5, PHP 8.3 oder neuer, Symfony 7.4). Die vollständige Paketliste steht in
der [Paketübersicht der Plattform](../spezifikationen/6.0/platform-paketzuordnung.md); die Nexus-Linien werden in
[Exponential Platform Nexus](../../en/us/features/6.0/platform-nexus-starter.md) vorgestellt und die Skeletons auf der Seite
[DXP-Skeleton](../../en/us/features/6.0/platform-dxp-skeleton.md).

Drei Tatsachen entscheiden, wie eine Migration zu diesen Zielen funktioniert:

1. **Die Forks ersetzen die Upstream-Namen.** Jeder Fork deklariert das Upstream-Paket unter `replace` (seit
   2026-04-12 mit `*`), sodass Composer nur den Fork installiert und Pakete Dritter, die Namen `ibexa/*` oder
   `ezsystems/*` verlangen, weiterhin aufgelöst werden. Der Kernel von Platform v5, `se7enxweb/exponential-platform-dxp-core`,
   deklariert `"replace": {"ibexa/core": "*"}`; die v5-Administration `se7enxweb/admin-ui` ersetzt `ibexa/admin-ui` und
   `ezsystems/ezplatform-admin-ui`.
2. **Die PHP-Namespaces bleiben die des Upstreams.** Die Forks behalten die Namespaces `Ibexa\...` (4.x, 5.x) und `eZ\...` /
   `EzSystems\...` (2.5, 3.3); `se7enxweb/admin-ui` lädt `Ibexa\AdminUi\` automatisch, die v5-Bundles sind als
   `Ibexa\Bundle\Core\IbexaCoreBundle` usw. registriert. Eigener Code, der für dieselbe Generation geschrieben wurde, läuft
   unverändert. Geändert haben sich die Namen der Konsolenbefehle, das Branding, der Lizenzwortlaut und die
   Datenbank-Installer ([Paket-Forks und Umbenennungen von Befehlen](../../en/us/bc/6.0/platform-package-forks-and-command-renames.md)).
3. **Nexus baut auf der Open-Source-Edition auf.** Seine `config/bundles.php` (Nexus 5) registriert die Bundles für
   Ibexa Core, die Legacy-Suchmaschine, Solr, den HTTP-Cache, REST, GraphQL, die Administrationsoberfläche, RichText,
   Matrix, den Query-Feldtyp, Cron, Messenger und die Design-Engine, dazu Netgen Layouts und etwa 25 weitere
   Netgen-Bundles; es gibt kein Bundle für Page Builder, Form Builder, redaktionellen Workflow, Elasticsearch,
   Personalisierung oder Commerce. Inhalte der kommerziellen Feldtypen brauchen eine Entscheidung, bevor sie umziehen
   können ([16.5.9](#1659-seitenaufbau-page-builder-gegenüber-netgen-layouts)).

### Ziel B: Exponential 6.0, allein oder als Exponential Platform Legacy

**Exponential 6.0** ist der Legacy-Kernel in diesem Repository: Composer-Paket `se7enxweb/exponential`, Typ
`ezpublish-legacy`, `lib/version.php` 6.0.15, PHP 8.0 bis 8.5, fünf Datenbank-Engines, INI-Einstellungen, TPL-Templates,
die Legacy-Administrationsoberfläche und Exponential Velocity als eigener Webserver ([Kapitel 1](01-einfuehrung.md)).

**Exponential Platform Legacy** (`se7enxweb/exponential-platform-legacy`) ist die Hybridlösung: Die README ihrer
2.5-Linie nennt sie „a hybrid-kernel ... DXP/CMS built on the Exponential (Legacy) 6.x kernel bridged to Symfony 3.4 LTS via
LegacyBridge 2.x“. Der Legacy-Kernel ist eine Composer-Abhängigkeit (`se7enxweb/exponential ^6.0.12`), die
`se7enxweb/exponential-legacy-installer` nach `ezpublish_legacy/` installiert; der Symfony-Stack und der Legacy-Kernel
teilen sich über `se7enxweb/legacy-bridge` eine Datenbank. Es gibt eine Linie je Plattformgeneration:

| Release-Linie | Plattform | PHP (`composer.json`) | Bridge |
|---|---|---|---|
| `v2.5.0.x` (`v2.5.0.0` bis `v2.5.0.3`) | eZ Platform 2.5, `se7enxweb/ezpublish-kernel ~7.5.33`, Symfony 3.4 | `^7.1.3 \|\| ^8.1 \|\| ^8.2` | `se7enxweb/legacy-bridge ^2.1` |
| `v3.0.0.x` (`v3.0.0.0` bis `v3.0.0.14`), danach `v3.3.44.x` (`v3.3.44.0` bis `v3.3.44.7`), Branch `3.x` | eZ Platform 3.3, `se7enxweb/ezplatform-kernel ~1.3` (`~1.3.43` in `v3.3.44.7`), Symfony 5.4 | `^8.0` | `^3.0`, ab `v3.3.44.3` `^3.0.0.34`, ab `v3.3.44.4` `^3.0.0.35` |
| `v4.6.23.x` (`v4.6.23.0` bis `v4.6.23.2`), Branch `4.6.x` | Ibexa-4.6-kompatibel, `se7enxweb/exponential-platform-dxp 4.6.x-LB-dev` | `^7.4` bis `^8.5` | `^4.0.0.0` |
| `v5.0.x` (`v5.0.0` bis `v5.0.2`), Branch `5.x` | Platform v5, `exponential-platform-dxp dev-5.x-LB` | `>=8.3` | `^4.0.0.0` (`v5.0.0`), `^5.0.0.0` (`v5.0.1`, `v5.0.2`) |

**`v5.0.3` ist keine v5-Release.** Das Tag, das GitHub als „Latest“ markiert (8. Juli 2026), wurde von `master`
geschnitten, der 2.5-Linie: Seine `composer.json` verlangt `se7enxweb/ezpublish-kernel ~7.5.33` und
`se7enxweb/legacy-bridge ^2.1`, PHP `^7.1.3 || ^8.1 || ^8.2`, und Packagist liefert es mit diesem Inhalt aus. Tags sind
dauerhaft, also bleibt es bestehen. Verlangen Sie für die v5-Linie `v5.0.2` oder den Branch `5.x`; niemals eine bloße
Bedingung `^5.0`, die zu `v5.0.3` aufgelöst wird.

Exponential 6.0 ist damit sowohl ein eigenes Ziel als auch die Legacy-Hälfte jeder Release von Exponential Platform
Legacy. Inhalte „hinunter zum Legacy-Kernel“ zu bringen heißt, sie in eine Datenbank zu bringen, die der 6.0-Kernel
lesen kann; ob daneben ein Symfony-Stack weiterläuft, ist eine zweite Entscheidung ([16.6.8](#1668-einen-symfony-stack-neben-dem-legacy-kernel-behalten)).

## 16.2 Einen Weg wählen

| Sie haben oder wollen | Weg A: Exponential Platform Nexus | Weg B: Exponential 6.0 (oder Platform Legacy) |
|---|---|---|
| Eigene Symfony-Bundles, Twig-Templates, REST- oder GraphQL-Clients | Behalten Sie sie; dieselbe Generation bedeutet dieselben APIs | Neu schreiben als Legacy-Extensions, TPL-Templates und Module; REST über die Legacy-REST-Schicht |
| Inhalte in RichText (`ezrichtext`) | Bleiben, wie sie sind | Konvertieren oder anpassen ([16.6.3](#1663-feldtypen)) |
| Landing Pages aus Page Builder (`ezlandingpage`, kommerziell) | Kein Page Builder; mit Netgen Layouts neu aufbauen | Keine Entsprechung; mit Exponential Layouts neu aufbauen |
| Redakteure, die die Symfony-Administration kennen | Dieselbe Generation der Administrationsoberfläche | Die Legacy-Administration (Design `admin3`), ein anderes Werkzeug |
| Ein Team, das INI, TPL und Legacy-Extensions kennt | Muss den Symfony-Stack lernen | Arbeitet mit dem, was es kennt |
| Server mit PHP 8.0 oder 8.1 | Nexus 1.0.0.x / 1.1.0.x (oder die Platform-3.x-Forks) | Ganz 6.0 |
| PHP 8.4 oder 8.5 | Nexus 1.3.0.x (braucht 8.4) oder ältere Linien mit den [Framework-Forks](../../en/us/features/6.0/platform-php85-framework-forks.md) | Ganz 6.0 |
| Ein Server ohne separaten Webserver, SQLite unter Last | Nexus 5 kann mit SQLite laufen (Standard für die Entwicklung) | Velocity und der SQLite-Treiber sind Produktionsfunktionen ([Kapitel 8](08-die-website-ausliefern.md), [Kapitel 9](09-datenbanken.md)) |
| Geringstes Risiko für eine Live-Site | Am höchsten: dieselbe Generation, ein Paketaustausch | Höchster Aufwand: eine Daten- und Template-Migration |
| Beide Welten für einen Übergang | Nexus 1.0.0.x, 1.1.0.x und 1.2.0.x oder Platform Legacy betreiben beide Kernel auf einer Datenbank | Dieselben Produkte, von der anderen Seite gesehen |

Faustregeln:

- **Zuerst dieselbe Generation.** Wenn Ihre Site auf Ibexa 4.6 läuft, ist der kürzeste sichere Schritt Nexus 1.2.0.x
  (oder das 4.6-Skeleton), nicht Nexus 5. Wechseln Sie die Generation danach, mit den Update-Schritten des Herstellers,
  auf den Exponential-Forks.
- **Weg B ist eine Neufassung der Darstellung und des eigenen Codes**, nicht der Inhalte. Die Inhaltstabellen werden
  geteilt ([16.6.1](#1661-was-beide-gemeinsam-haben)); Templates, Konfiguration und eigener PHP-Code nicht.
- **Mischen Sie die Wege nicht in einer Umstellung.** Wechseln Sie zuerst auf die passende Linie von Exponential
  Platform (Weg A), lassen Sie sie sich einspielen, und entscheiden Sie erst dann, ob Sie hinunter zum Legacy-Kernel gehen.

## 16.3 Die Seiten des Herstellers und wo dieses Kapitel sie abdeckt

Der Hersteller dokumentiert Migration und Updates unter „Update and migration“ in seiner Dokumentation. Jede der
folgenden Seiten wurde vollständig gelesen; die rechte Spalte sagt, wo dieses Kapitel ihre Punkte abdeckt und wo
Exponential abweicht.

| Seite des Herstellers | Abgedeckt in | Wo Exponential abweicht |
|---|---|---|
| [Migration vom 4.x- und 5.x-Legacy-Stack (Seite des Herstellers)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/) (vom 4.x/5.x-Legacy-Stack zum Symfony-Stack) | [16.5.11](#16511-hinweise-je-quellversion), [16.6](#166-weg-b-hinunter-zum-legacy-kernel-von-exponential-60) | Der Hersteller sagt, den Symfony-Stack Seite an Seite mit dem Legacy-Kernel zu betreiben, sei wegen XmlText, des Page-Felds und des Schemas „practically impossible“. Die Branches der se7enxweb-Legacy-Bridge behalten das Legacy-Schema für 2.5, 3.3, 4.6 und v5 bei ([Legacy Bridge](../../en/us/features/6.0/legacy-bridge.md)), und Exponential 6.0 ist selbst ein gepflegtes Ziel; die Richtung lässt sich also umkehren |
| [Migration vom 5.x-Platform-Stack (Seite des Herstellers)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/) (5.4 / 2014.11 nach 1.7, dann 1.13, dann 2.x) | [16.5.11](#16511-hinweise-je-quellversion), [16.6.5](#1665-konfiguration-siteaccess-yaml-zu-ini) (Bild-Aliase), [16.7](#167-häufige-probleme) | Der Legacy-Kernel akzeptiert die Sortierfelder 6 und 7, XmlText, die ezflow-Page und Star Rating, die der Hersteller als nicht unterstützt aufführt; auf der Legacy-Seite muss für sie nichts konvertiert werden |
| [Häufige Migrationsprobleme](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/) | [16.7](#167-häufige-probleme) | Befehlsnamen tragen auf Exponential Platform v5 das Präfix `exponential:` |
| [Ibexa DXP aktualisieren](https://doc.ibexa.co/en/5.0/update_and_migration/update_ibexa_dxp/), [von 1.13 und 2.x](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_from_1.x_2.x/), [Anwendung auf 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_app_to_2.5/), [Datenbank auf 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_db_to_2.5/) | [16.5.2](#1652-die-quelle-auf-die-letzte-release-ihrer-linie-bringen), [16.5.11](#16511-hinweise-je-quellversion) | Pakete von Exponential Platform 2.5 kommen von se7enxweb, nicht von `updates.ez.no` |
| [Von 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/update_from_2.5/), [auf 3.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.2/), [Code an v3 anpassen](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/adapt_code_to_v3/) (Templates, Konfiguration, Feldtypen, Signal Slots, Online-Editor, Workflow, Extensions, REST, Sonstiges), [auf 3.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.3/), [Abkündigungen in v3.0](https://doc.ibexa.co/en/5.0/release_notes/ez_platform_v3.0_deprecations/) | [16.5.2](#1652-die-quelle-auf-die-letzte-release-ihrer-linie-bringen), [16.5.5](#1655-namespaces-bundles-und-konfigurationsschlüssel), [16.5.6](#1656-die-datenbank-generation-für-generation), [16.5.10](#16510-eigene-bundles-portieren) | Das SQL von 2.5 nach 3.0 liegt im Verzeichnis `upgrade/db/` von `se7enxweb/exponential-platform`; der Hersteller verwendet `ibexa/installer` |
| [Von 3.3 auf 3.3.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/update_from_3.3/), [auf 4.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/to_4.0/), [Abkündigungen in v4.0](https://doc.ibexa.co/en/5.0/release_notes/ibexa_dxp_v4.0_deprecations/) | [16.5.5](#1655-namespaces-bundles-und-konfigurationsschlüssel), [16.5.6](#1656-die-datenbank-generation-für-generation), [16.5.7](#1657-administration-twig-feldtypen-rest-und-graphql) | Die `exponential:`-Befehle der 3.x- / 4.6-Forks behalten `ibexa:` und `ezplatform:` als Aliase |
| Kleinere 4.x-Updates: [4.0 auf 4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.0/to_4.1/), [4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.1/update_from_4.1/), [4.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.2/update_from_4.2/), [4.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.3/update_from_4.3/), [4.4](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.4/update_from_4.4/), [4.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.5/update_from_4.5/), [4.6.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_from_4.6/) | [16.5.11](#16511-hinweise-je-quellversion) | Die meisten 4.x-Schritte betreffen die kommerziellen Editionen (`ibexa:migrations:*`, Commerce, Firmenkonten); die Open-Source-Edition braucht nur die Token-Tabellen und die Spalte der Klassengruppe |
| [Von 4.6 auf 5.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_to_5.0/), [5.0.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_5.0/update_from_5.0/) | [16.5.6](#1656-die-datenbank-generation-für-generation), [16.5.7](#1657-administration-twig-feldtypen-rest-und-graphql), [16.6.4](#1664-die-datenbank-schritt-für-schritt) | Die Befehle von Exponential v5 heißen nur `exponential:*`; die SQLite-Engine des Exponential-Installers hat kein Skript zum Umbenennen der Tabellen |
| Die ältere [2.5-Migrationsseite](https://doc.ibexa.co/en/2.5/migrating/migrating_from_ez_publish_platform/) | wie die 5.0-Seite | Derselbe Inhalt mit Dateinamen `.yml` (`ezplatform.yml`, `ezpublish.yml`) |

Die alten Adressen auf `doc.ezplatform.com` (`/en/latest/migrating/...`, `/en/2.5/migrating/...`) leiten auf die oben
genannten Seiten von `doc.ibexa.co` um; die versionsbezogenen `updating`-Seiten von 2.5 gibt es nicht mehr (404), und
ihr Inhalt steht in den aufgeführten Update-Seiten.

## 16.4 Bevor Sie beginnen: Bestandsaufnahme, Einfrieren und Sicherungen

### Wissen, was Sie haben

Führen Sie diese Abfragen auf einer Kopie der Produktionsdatenbank aus (gezeigt für MySQL oder MariaDB; die Anweisungen
sind reines SQL und laufen unverändert in `psql` und `sqlite3`). Sie verwenden die Tabellennamen `ez*` von 1.x bis 4.x;
verwenden Sie auf einer 5.0-Datenbank die Namen `ibexa_*` aus der [Umbenennungstabelle](#die-tabellenumbenennungen-von-50-und-ihre-umkehrung).

```sql
-- Which release the database says it is (2.5 writes ezpublish-version; 3.0 and later write ezplatform-release)
SELECT name, value FROM ezsite_data;

-- Field types in use, with the number of content type fields of each
SELECT data_type_string, COUNT(*) FROM ezcontentclass_attribute WHERE version = 0 GROUP BY data_type_string ORDER BY 2 DESC;

-- Which content types use a given field type (example: ezrichtext)
SELECT DISTINCT contentclass_id FROM ezcontentclass_attribute WHERE data_type_string = 'ezrichtext';

-- Locations sorted by content type identifier (6) or name (7): unsupported by the Symfony stack
SELECT node_id, parent_node_id, sort_field FROM ezcontentobject_tree WHERE sort_field IN (6, 7);

-- Password hash types (1-3 MD5 variants, 5 plain text, 6 bcrypt, 7 PHP default)
SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;

-- Languages
SELECT id, locale, name, disabled FROM ezcontent_language;

-- Sizes that decide the downtime: versions, fields, locations
SELECT COUNT(*) FROM ezcontentobject_version;
SELECT COUNT(*) FROM ezcontentobject_attribute;
SELECT COUNT(*) FROM ezcontentobject_tree;
```

Notieren Sie die Antworten; die meisten späteren Schritte hängen von ihnen ab. Listen Sie außerdem auf:

- die Bundles in `config/bundles.php` (3.x und später) oder `app/AppKernel.php` (1.x, 2.x) und für jedes Bundle Dritter,
  ob es für die Zielgeneration existiert;
- den eigenen Code unter `src/` und seine Nutzung der Kernel-API, der Ereignisse (Signal Slots vor 3.0), der Feldtypen
  und der Twig-Extensions;
- die Siteaccesses, ihr Matching und ihre Hosts; die Bildvariationen; die Suchmaschine; den HTTP-Cache (Symfony-Proxy,
  Varnish, Fastly); die Cron-Einträge; den IO-Handler (lokale Dateien, DFS-Cluster);
- die PHP-Version jedes Servers.

### Einfrieren und sichern

Kündigen Sie für die Umstellung einen redaktionellen Stopp an. Sichern Sie dann die Datenbank und den Binärspeicher:

```bash
mysqldump -u USER -p --single-transaction DATABASE > before-migration.sql     # MySQL or MariaDB
pg_dump -U USER DATABASE > before-migration.sql                               # PostgreSQL
sqlite3 var/data_prod.db ".backup before-migration.db"                         # SQLite (the file DATABASE_URL names)
tar czf storage-before-migration.tgz public/var                                # 3.x and later; web/var on 1.x and 2.x
```

Stellen Sie den Dump vor dem Beginn einmal auf einer Testmaschine wieder her: Eine Sicherung, die nie wiederhergestellt
wurde, ist keine Sicherung. Jeder Befehl dieses Kapitels, der eine Datenbank verändert, wird zuerst auf einer solchen
Kopie ausgeführt.

## 16.5 Weg A: zu Exponential Platform Nexus

### 16.5.1 Die Generation wählen

| Ihre Quelle | Ziellinie | Woher die Schritte der Generation kommen |
|---|---|---|
| eZ Platform 1.x (1.7, 1.13) | zuerst mit den Schritten des Herstellers auf 2.5, dann wie 2.5 | [16.5.2](#1652-die-quelle-auf-die-letzte-release-ihrer-linie-bringen) |
| eZ Platform 2.5 | Nexus `1.0.0.x`, Exponential Platform Legacy `v2.5.0.x` | dieselbe Generation, nur Paketaustausch |
| eZ Platform 3.0 bis 3.2, Ibexa 3.3 | Nexus `1.1.0.x`, Exponential Platform Legacy `v3.3.44.x`, `se7enxweb/oss` 3.3, `se7enxweb/exponential-platform` 3.2.9 | zuerst auf 3.3.latest |
| Ibexa 4.0 bis 4.6 | Nexus `1.2.0.x`, `se7enxweb/exponential-platform-v4x-dxp-skeleton`, Exponential Platform Legacy `v4.6.23.x` | zuerst auf 4.6.latest |
| Ibexa 5.0 | Nexus `1.3.0.x` / der Nexus-Starter, `se7enxweb/exponential-platform-dxp-skeleton`, Exponential Platform Legacy `v5.0.x` | dieselbe Generation |

Eine Nexus-Linie bringt eine Demo-Site mit (Layouts, Design, Inhalte). Wenn Sie eine bestehende Site migrieren, wollen
Sie deren Code und Pakete, nicht deren Demo-Inhalte: Legen Sie das Projekt aus der Nexus-Linie (oder dem passenden
Skeleton) an, übertragen Sie dann Ihre eigenen Verzeichnisse `src/`, `templates/`, `translations/`, `assets/`, Ihre
Konfiguration und Ihren Speicher hinein und richten Sie es auf Ihre Datenbank aus. Führen Sie `exponential:install`
niemals gegen Ihre Produktionsdatenbank aus: Der Befehl legt ein neues Repository an.

### 16.5.2 Die Quelle auf die letzte Release ihrer Linie bringen

Die Forks folgen der letzten Upstream-Release jeder Linie, daher muss die Quelle zuerst dort ankommen. Die Abfolge des
Herstellers ([Ibexa DXP aktualisieren](https://doc.ibexa.co/en/5.0/update_and_migration/update_ibexa_dxp/)) ist:

- v1.13 oder eine 2.x unter 2.5: auf 2.5 LTS (der Hersteller nennt 2.5.30, später 2.5.32), dann 3.3 LTS, dann 4.6 LTS, dann 5.0;
- 2.5: auf 3.3 LTS (3.3.43), dann 4.6, dann 5.0;
- 3.3: auf die neueste 3.3, dann 4.6, dann 5.0;
- 4.x unter 4.6: auf 4.6 LTS und deren neuesten Patch (4.6.32), dann 5.0.

Die Datenbankskripte jedes Schritts, wie der Hersteller sie benennt:

| Schritt | MySQL / MariaDB | PostgreSQL | Hinweise |
|---|---|---|---|
| 1.x auf 2.2 | `vendor/ezsystems/ezpublish-kernel/data/update/mysql/dbupdate-7.1.0-to-7.2.0.sql` (und `-dfs.sql` für eine DFS-Datenbank) | `.../postgres/...` | utf8 auf utf8mb4; kann an Index-Kollisionen scheitern (Indizes wurden gekürzt), entfernen Sie die Duplikate und wiederholen Sie; setzen Sie dann `charset: utf8mb4` für die Doctrine-Verbindung |
| auf 2.3 | `dbupdate-7.2.0-to-7.3.0.sql` | ebenso | fügt `ezcontentobject_trash.trashed` hinzu |
| auf 2.5 | `dbupdate-7.4.0-to-7.5.0.sql`, dann `7.5.2-to-7.5.3`, `7.5.4-to-7.5.5`, `7.5.6-to-7.5.7` | ebenso | 2.5.9: danach neu indizieren |
| 2.5 auf 3.0 | `upgrade/db/mysql/ezplatform-2.5.latest-to-3.0.0.sql` | `upgrade/db/postgresql/...` | in `se7enxweb/exponential-platform`; der Hersteller liefert `vendor/ibexa/installer/upgrade/db/mysql/ezplatform-2.5-to-ibexa-3.3.0.sql` |
| 3.2.3 auf 3.2.4 | `upgrade/db/mysql/ezplatform-3.2.3-to-3.2.4.sql` | ebenso | in `se7enxweb/exponential-platform` |
| 3.3.x-Patches | `ibexa-3.3.1-to-3.3.2.sql`, `3.3.6-to-3.3.7`, `3.3.8-to-3.3.9`, `3.3.24-to-3.3.25`, `3.3.33-to-3.3.34` | ebenso | ab 3.3.7 legt die Open-Source-Edition ohne `ibexa/installer` die Tabelle `ibexa_setting` von Hand an |
| 3.3 auf 4.0 | `vendor/ibexa/installer/upgrade/db/mysql/ibexa-3.3.latest-to-4.0.0.sql` | `.../postgresql/...` | Open-Source-Edition: nur `ALTER TABLE ezcontentclassgroup ADD COLUMN is_system BOOLEAN NOT NULL DEFAULT false;` |
| 4.x-Minor-Versionen | `ibexa-4.0.0-to-4.1.0.sql`, `4.1.latest-to-4.2.0`, `4.2.latest-to-4.3.0`, `4.4.latest-to-4.5.0`, `4.5.latest-to-4.6.0` und Patches | ebenso | Open-Source-Edition: 4.1 bis 4.4 brauchen keine; 4.5 legt `ibexa_token_type` und `ibexa_token` an; 4.6 fügt `ibexa_token.revoked` hinzu |
| 4.6 auf 5.0 | `vendor/ibexa/installer/upgrade/db/mysql/ibexa-4.6.latest-to-5.0.0.sql` | `.../postgresql/...` | die Open-Source-Edition erhält die Anweisungen auf der Seite des Herstellers selbst; siehe [16.5.6](#1656-die-datenbank-generation-für-generation) |

Die beiden Regeln des Herstellers für diese Skripte gelten unverändert: Sichern Sie vorher und leeren Sie danach die
Caches, und **übergeben Sie `mysql` oder `psql` niemals `--force`**: Eine fehlschlagende Anweisung muss den Lauf
anhalten, damit Sie ihre Ursache beheben können.

Was das Skript von 2.5 auf 3.0 aus `se7enxweb/exponential-platform` tut (MySQL-Version; die PostgreSQL-Datei tut
dasselbe in ihrer Syntax):

```sql
START TRANSACTION;
DELETE FROM ezsite_data WHERE name IN ('ezpublish-version', 'ezplatform-release');
INSERT INTO ezsite_data (name, value) VALUES ('ezplatform-release', '3.0.0');
COMMIT;
ALTER TABLE ezcontentclass_attribute MODIFY data_text1 VARCHAR(255);
ALTER TABLE ezcontentclass_attribute ADD COLUMN is_thumbnail TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `ezkeyword_attribute_link`
    ADD COLUMN `version` INT(11) NOT NULL,
    ADD KEY `ezkeyword_attr_link_oaid_ver` (`objectattribute_id`, `version`);
-- then fills ezkeyword_attribute_link.version from the current versions,
-- and sets the ezuser login pattern '^[^@]+$' where it is empty
```

Zwei dieser Zeilen spielen später eine Rolle, falls Sie jemals hinunter zum Legacy-Kernel gehen: Die Zeile
`ezpublish-version` wird gelöscht, und `ezkeyword_attribute_link.version` ist unter MySQL `NOT NULL` ohne Standardwert
([16.6.4](#1664-die-datenbank-schritt-für-schritt)).

Die Update-Seiten des Herstellers führen außerdem die Schritte ohne Datenbankbezug je Release auf (Änderungen am
Flex-Endpunkt, die Umstellungen auf Symfony 5.3 und 5.4 innerhalb von 3.3, die Änderungen an den VCL für Varnish und
Fastly, die BREACH-Änderungen von 3.3.41 und 4.6.14, die Bereinigung mit `ibexa:content:remove-duplicate-fields`, das
Rector-Paket von 4.6.19). Folgen Sie ihnen am Upstream-Code; sie werden hier nicht wiederholt.

### 16.5.3 Die Pakete austauschen

Innerhalb derselben Generation ist der Austausch eine Änderung in Composer. Vorher, ein Open-Source-Projekt mit 4.6:

```json
{
    "require": {
        "php": "^7.4 || ^8.0",
        "ibexa/oss": "~4.6.0",
        "netgen/layouts-ibexa": "~1.4.0"
    }
}
```

Nachher, wie Nexus `v1.2.0.0` es verlangt:

```json
{
    "require": {
        "php": ">=8.2",
        "se7enxweb/oss": "~4.6.0",
        "se7enxweb/site-legacy-bundle": "v2.0.0",
        "netgen/layouts-ibexa": "~1.4.0"
    }
}
```

Und für Platform v5 das Metapaket aus der Anleitung zum DXP-Skeleton:

```json
{
    "require": {
        "se7enxweb/exponential-platform-dxp": "*"
    }
}
```

Die Reihenfolge der Arbeit, auf einer Kopie des Projekts:

1. Legen Sie einen Branch an. Legen Sie eine Kopie von `composer.json` beiseite.
2. Ersetzen Sie das Upstream-Metapaket durch das se7enxweb-Metapaket derselben Generation (Tabelle in [16.5.1](#1651-die-generation-wählen)).
   Behalten Sie Ihre eigenen Anforderungen; entfernen Sie Pakete, die nur Abhängigkeiten des Upstream-Metapakets waren
   (`composer why <package>` sagt Ihnen, wer ein Paket braucht).
3. Aktualisieren Sie auf der Kopie und lesen Sie den Plan, den Composer ausgibt, bevor Sie ihn annehmen. Prüfen Sie
   dann, dass kein Upstream-Paket noch neben seinem Fork installiert ist; Fork und Upstream-Paket teilen sich
   Namespaces, und der Autoloader würde beliebig eines von beiden nehmen:

   ```bash
   composer why ibexa/admin-ui                              # expect se7enxweb/admin-ui
   composer show | grep -E "^(ibexa|ezsystems|netgen)/"    # upstream packages that remain, each needs a reason
   ```

4. Verlangen Sie Tags, nicht die Aliase für Entwicklungs-Branches, die am 2026-03-26 hinzugefügt und wieder entfernt
   wurden (Schritt 2 von [Paket-Forks und Umbenennungen von Befehlen](../../en/us/bc/6.0/platform-package-forks-and-command-renames.md)).
5. Installieren Sie die Flex-Rezepte, auf die das Skeleton verweist: Die Exponential-Skeletons stellen den Endpunkt
   `se7enxweb/sevenx-recipes` vor die Upstream-Rezepte, sodass die Rezepte die fork-bewussten sind.
6. `php bin/console cache:clear`, danach der Asset-Build Ihrer Generation (Webpack Encore: `yarn install` und das
   Build-Skript des Projekts, dann `php bin/console assets:install --symlink --relative public`).

Versionsbedingungen: PHP ist die erste Hürde. Nexus `1.3.0.x` verweigert PHP unter 8.4; das v5-Skeleton unter 8.3; das
4.6-Skeleton akzeptiert 7.4 bis 8.5; die 3.x-Forks 7.3 bis 8.5. Unter PHP 8.4 oder 8.5 brauchen die älteren Stacks die
[Framework-Forks](../../en/us/features/6.0/platform-php85-framework-forks.md), und der Layouts-2.0-Stack braucht den
[Fork von layouts-core](../../en/us/features/6.0/platform-layouts-core-fork.md). Die PHP-Migrationsanleitungen des Herstellers
([8.0](https://www.php.net/manual/en/migration80.php) bis [8.5](https://www.php.net/manual/en/migration85.php)) gelten
für Ihren eigenen Code wie überall.

### 16.5.4 Namen der Konsolenbefehle

Die Exponential-Forks gaben den Befehlen am 2026-04-07 einen primären Namen mit `exponential:`
([Befehlsnamen](../spezifikationen/6.0/platform-konsolenbefehle.md)).

| Generation | Primärer Name | Alte Namen |
|---|---|---|
| 3.x-Kernel-Fork `se7enxweb/ezplatform-kernel` ab `v1.3.45` | `exponential:*` (`exponential:reindex` ...) | `ibexa:*` und `ezplatform:*` bleiben als abgekündigte Aliase |
| 3.x-Kernel-Fork bis `v1.3.44` (einschließlich `v1.3.43`, `v1.3.44`) | `ibexa:*` (`ibexa:reindex` ...), wie im Upstream | `ezplatform:*` |
| 4.6-Core-Fork (`se7enxweb/core`, Branch `4.6`) | `exponential:*` | `ibexa:*` und `ezplatform:*` bleiben als abgekündigte Aliase |
| Nexus 1.2.0.x | `ibexa:*` (`ibexa:reindex` ...): seine `composer.lock` installiert das Upstream-Paket `ibexa/core` `4.6.x-dev`, nicht den Fork | keine |
| Platform v5 (`se7enxweb/exponential-platform-dxp-core` v5.0.7, die Version, die der Nexus-Starter installiert) | `exponential:*` | in dieser Version sind keine Aliase registriert: `ibexa:reindex` und die anderen umbenannten Befehle gibt es nicht |
| Pakete, die nicht geforkt wurden (Cron, GraphQL, ...) | Upstream-Name | `ibexa:cron:run`, `ibexa:graphql:generate-schema` |
| Projekte mit Nexus 1.1.0.x und 1.2.0.x | ihr eigenes `exponential:reindex` (`src/.../ExponentialReindexCommand.php`), ein Proxy, der nur `--siteaccess` akzeptiert und `ibexa:reindex` ausführt (oder meldet, dass dieser nicht registriert ist, und abbricht) | `ibexa:reindex` mit allen seinen Optionen |
| Legacy Bridge `3.x` ab `v3.0.0.30`, `4.x` ab `v4.0.0.2`, jedes `5.x`-Tag | `exponential:legacy:*` | `ezpublish:legacy:*`, `ezpublish:configure`, `ezpublish:legacybundles:install_extensions` bleiben als abgekündigte Aliase |
| Legacy Bridge `3.x` bis `v3.0.0.29`, `4.x` `v4.0.0.0` und `v4.0.0.1` | `ezpublish:*` | nur diese Namen existieren |
| Legacy Bridge auf `master`, `v2.1.10` bis `v2.1.12` (Generation 2.5) | `ezpublish:*` (`ezpublish:legacy:init` und die übrigen) | nur diese Namen existieren |

Der v5-Kernel registriert `exponential:install`, `exponential:reindex`, `exponential:urls:regenerate-aliases`,
`exponential:images:normalize-paths`, `exponential:images:resize-original`, `exponential:io:migrate-files`,
`exponential:user:validate-password-hashes`, `exponential:user:expire-password`, `exponential:content:cleanup-versions`,
`exponential:content:remove-duplicate-fields`, `exponential:copy-subtree`, `exponential:delete-content-translation`,
`exponential:timestamps:to-utc`, `exponential:check-urls`, `exponential:content-type-group:set-system` und
`exponential:debug:config-resolver` (Alias `exponential:debug:config`). Schreiben Sie Cron-Einträge und
Deployment-Skripte vor der Umstellung um:

```bash
grep -rnE "bin/console +(ibexa|ezplatform|ezpublish):" /etc/cron.d ./deploy 2>/dev/null
```

Jede ausgegebene Zeile nennt einen alten Befehl; auf Platform v5 muss jeder davon geändert werden, auf 3.x und 4.6
funktioniert er weiterhin.

### 16.5.5 Namespaces, Bundles und Konfigurationsschlüssel

Innerhalb einer Generation ändert sich nichts: Die Forks behalten die Upstream-Namespaces. Über Generationen hinweg
wenden Sie die Umbenennungen des Herstellers an, auf den Exponential-Forks genau wie im Upstream:

| Änderung | 2.5 | 3.x | 4.x / 5.0 |
|---|---|---|---|
| Haupt-Konfigurationsschlüssel | `ezpublish:` | `ezplatform:` | `ibexa:` |
| Namespace des Config-Resolvers | `ezsettings` | `ezsettings` | `ibexa.site_access.config` |
| Kernel-API | `eZ\Publish\API\Repository\ContentService` | ebenso | `Ibexa\Contracts\Core\Repository\ContentService` |
| Interface des Config-Resolvers | `eZ\Publish\Core\MVC\ConfigResolverInterface` | ebenso | `Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface` |
| Feldtyp-SPI | `eZ\Publish\SPI\FieldType\FieldType` (Interface) | ebenso, jetzt eine abstrakte Klasse: `extends FieldType` | `Ibexa\Contracts\Core\FieldType\FieldType` |
| Administrationsoberfläche | `EzSystems\EzPlatformAdminUi\` | ebenso | `Ibexa\AdminUi\` (Contracts `Ibexa\Contracts\AdminUi\`) |
| REST | `eZ\Publish\Core\REST` | `EzSystems\EzPlatformRest` | `Ibexa\Rest\` |
| RichText | im Kernel | `EzSystems\EzPlatformRichText\` | `Ibexa\FieldTypeRichText\` |
| HTTP-Cache | `EzSystems\PlatformHttpCacheBundle\` | ebenso | `Ibexa\Bundle\HttpCache\` |
| Solr | `EzSystems\EzPlatformSolrSearchEngine\` | ebenso | `Ibexa\Solr\` |
| GraphQL | `EzSystems\EzPlatformGraphQL\` | ebenso | `Ibexa\GraphQL\` |
| Projektstruktur | `app/`, `src/AppBundle`, `web/` | `config/`, `src/` (`App\`), `templates/`, `public/` | wie 3.x |
| Registrierung der Bundles | `app/AppKernel.php` | `config/bundles.php` | ebenso; unter 4.0 jeden Eintrag entfernen, der mit `eZ`, `EzSystems`, `Ibexa\Platform`, `Silversolutions`, `Siso` beginnt |

Für die 4.x-Umbenennungen bietet der Hersteller `ibexa/compatibility-layer` an (letztes Bundle in
`config/bundles.php`), das die alten Klassen-, Service-, Routen-, Twig- und Konfigurationsnamen zur Laufzeit abbildet;
unter 5.0 wird es nicht unterstützt, dort verwendet der Hersteller `ibexa/rector` (`composer require --dev ibexa/rector`,
`IbexaSetList::IBEXA_46` setzen, dann `php vendor/bin/rector --dry-run`), um den Code umzuschreiben. Keines von beiden
hat einen Exponential-Fork in der [Paketübersicht](../spezifikationen/6.0/platform-paketzuordnung.md); beide sind
Entwicklungswerkzeuge, die Sie während der Migration ausführen und danach entfernen.

Umbenennungen von Konfigurationsschlüsseln in 4.0, denen die meisten Projekte begegnen (vollständige Liste auf der
Abkündigungsseite des Herstellers zu 4.0): `ezplatform` und `ezpublish` zu `ibexa`, `ez_doctrine_schema` zu
`ibexa_doctrine_schema`, `ez_io` zu `ibexa_io`, `ez_platform_http_cache` zu `ibexa_http_cache`, `ez_search_engine_solr`
zu `ibexa_solr`, `ezdesign` zu `ibexa_design_engine`, `ezplatform_graphql` zu `ibexa_graphql`, `ezrichtext` zu
`ibexa_fieldtype_richtext`, `ezplatform_support_tools` zu `ibexa_system_info`. Die Konfigurationsdateien folgen:
`ezplatform.yaml` zu `ibexa.yaml`, `ezplatform_admin_ui.yaml` zu `ibexa_admin_ui.yaml`, `ezplatform_http_cache.yaml` zu
`ibexa_http_cache.yaml`, `ezplatform_solr.yaml` zu `ibexa_solr.yaml`; das Nexus-5-Projekt hat genau diese Namen unter
`config/packages/`.

Signal Slots (vor 3.0) wurden zu Symfony-Ereignissen mit einem Vorher- und einem Nachher-Ereignis je Operation; 3.0
entfernte außerdem dynamische Einstellungen (`$setting$`), das Auflösen der Konfiguration in Konstruktoren,
`ContainerAwareCommand`, die Container-Parameter `.class` und den eZc-Datenbank-Handler (verwenden Sie die
Doctrine-Verbindung `ezpublish.persistence.connection`, unter 4.x und später `ibexa.persistence.connection`). Abschnitt
[16.5.10](#16510-eigene-bundles-portieren) zeigt den Code.

### 16.5.6 Die Datenbank, Generation für Generation

| Generation | Kerntabellen | Was die Generation hinzufügt | Unterschied bei Exponential |
|---|---|---|---|
| 2.5 | das vollständige Legacy-Schema (129 Tabellen in `data/mysql/schema.sql` des 7.5-Kernels) | `ezcontentclass_attribute_ml`, `eznotification`, `ezgmaplocation`, Tabellen für Kommentare und Star Rating | die Exponential-2.5-Linie betreibt den Legacy-Kernel auf denselben Tabellen |
| 3.0 bis 3.3 | 50 Tabellen in `schema.yaml` von `ezplatform-kernel` | `ibexa_setting` (3.3.7), Spalten `is_thumbnail`, `ezkeyword_attribute_link.version`, `ezcontentobject.is_hidden`, `ezuser.password_updated_at`, `ezsearch_object_word_link.language_mask` | die 3.0-Seite des Herstellers führt etwa 80 Legacy-Tabellen zum Entfernen auf; die Branches der se7enxweb-Bridge behalten sie für den Legacy-Kernel |
| 4.0 bis 4.6 | dieselben 50 `ez*`-Tabellen plus `ibexa_setting`, `ibexa_token_type`, `ibexa_token` (Core-Branch `4.6`) | `ezcontentclassgroup.is_system` (4.0), `ibexa_token.revoked` (4.6) | keiner |
| 5.0 | 52 `ibexa_*`-Tabellen | jede Kerntabelle umbenannt, Spalten `contentclass_id` und `contentclassattribute_id` umbenannt | der Exponential-Installer kann zusätzlich das Schema des Legacy-Kernels daneben laden und unterstützt SQLite |

#### Die Tabellenumbenennungen von 5.0 und ihre Umkehrung

Das Skript des Herstellers von 4.6 auf 5.0 benennt die Kerntabellen um; für die Open-Source-Edition, die kein
`ibexa/installer` hat, druckt die Seite des Herstellers die Anweisungen selbst ab. Der Kernteil der Umbenennungstabelle,
wie die Seite ihn aufführt, mit den Spaltenumbenennungen:

| Name in 4.x | Name in 5.0 |
|---|---|
| `ezbinaryfile` | `ibexa_binary_file` |
| `ezcobj_state`, `ezcobj_state_group`, `ezcobj_state_group_language`, `ezcobj_state_language`, `ezcobj_state_link` | `ibexa_object_state`, `ibexa_object_state_group`, `ibexa_object_state_group_language`, `ibexa_object_state_language`, `ibexa_object_state_link` |
| `ezcontent_language` | `ibexa_content_language` |
| `ezcontentbrowsebookmark` | `ibexa_content_bookmark` |
| `ezcontentclass` | `ibexa_content_type` |
| `ezcontentclass_attribute` (`contentclass_id`) | `ibexa_content_type_field_definition` (`content_type_id`) |
| `ezcontentclass_attribute_ml` (`contentclass_attribute_id`) | `ibexa_content_type_field_definition_ml` (`content_type_field_definition_id`) |
| `ezcontentclass_classgroup` (`contentclass_id`) | `ibexa_content_type_group_assignment` (`content_type_id`) |
| `ezcontentclass_name` (`contentclass_id`) | `ibexa_content_type_name` (`content_type_id`) |
| `ezcontentclassgroup` | `ibexa_content_type_group` |
| `ezcontentobject` (`contentclass_id`) | `ibexa_content` (`content_type_id`) |
| `ezcontentobject_attribute` (`contentclassattribute_id`) | `ibexa_content_field` (`content_type_field_definition_id`) |
| `ezcontentobject_link` (`contentclassattribute_id`) | `ibexa_content_relation` (`content_type_field_definition_id`) |
| `ezcontentobject_name`, `ezcontentobject_trash`, `ezcontentobject_tree`, `ezcontentobject_version` | `ibexa_content_name`, `ibexa_content_trash`, `ibexa_content_tree`, `ibexa_content_version` |
| `ezdfsfile` | `ibexa_dfs_file` (bei Verwendung von DFS in deren eigener Datenbank, dort umbenannt) |
| `ezgmaplocation`, `ezimagefile`, `ezkeyword`, `ezmedia` | `ibexa_map_location`, `ibexa_image_file`, `ibexa_keyword`, `ibexa_media` |
| `ezkeyword_attribute_link` | `ibexa_keyword_field_link` |
| `eznode_assignment`, `eznotification`, `ezpackage` | `ibexa_node_assignment`, `ibexa_notification`, `ibexa_package` |
| `ezpolicy`, `ezpolicy_limitation`, `ezpolicy_limitation_value`, `ezrole`, `ezsection` | `ibexa_policy`, `ibexa_policy_limitation`, `ibexa_policy_limitation_value`, `ibexa_role`, `ibexa_section` |
| `ezpreferences` | `ibexa_preferences` auf der Seite des Herstellers; `ibexa_user_preference` in der `schema.yaml` des Exponential-v5-Kernels. Prüfen Sie, welche Ihre 5.0-Datenbank hat |
| `ezsearch_object_word_link` (`contentclass_id`, `contentclass_attribute_id`), `ezsearch_word` | `ibexa_search_object_word_link` (`content_type_id`, `content_type_field_definition_id`), `ibexa_search_word` |
| `ezsite_data` | `ibexa_site_data` |
| `ezurl`, `ezurl_object_link` | `ibexa_url`, `ibexa_url_content_link` |
| `ezurlalias`, `ezurlalias_ml`, `ezurlalias_ml_incr`, `ezurlwildcard` | `ibexa_url_alias`, `ibexa_url_alias_ml`, `ibexa_url_alias_ml_incr`, `ibexa_url_wildcard` |
| `ezuser`, `ezuser_accountkey`, `ezuser_role`, `ezuser_setting` | `ibexa_user`, `ibexa_user_accountkey`, `ibexa_user_role`, `ibexa_user_setting` |

Das vollständige Skript benennt außerdem die Indizes und Fremdschlüssel um sowie die Tabellen der kommerziellen Pakete
(`ezpage_*`, `ezform_*`, `ezeditorialworkflow_*`, `ezdatebasedpublisher_scheduled_entries`, `ezsite*`); in der
Open-Source-Edition können Fehler zu diesen fehlenden Tabellen, so der Hersteller, gefahrlos ignoriert werden („can
safely be ignored“). Lesen Sie die umgekehrte Richtung dieser Tabelle in [16.6.4](#1664-die-datenbank-schritt-für-schritt).

Auch die Feldtyp-Bezeichner werden in 5.0 umbenannt (`ezstring` zu `ibexa_string`, `ezrichtext` zu `ibexa_richtext`,
`ezimage` zu `ibexa_image` usw., insgesamt 28). Die alten Bezeichner bleiben unterstützt;
`php bin/console debug:container --tag=ibexa.field_type` listet beide auf (Spalten `alias` und `legacy_alias`).
Feld-Templates folgen den neuen Namen (`{% block ezstring_field %}` wird zu `{% block ibexa_string_field %}`).

**SQLite.** Die Exponential-Installer können eine Plattform-Datenbank in SQLite anlegen (3.x- und 4.6-Kernel-Forks,
v5-Core; [SQLite für Exponential Platform](../../en/us/features/6.0/platform-sqlite-install.md)). Ein Upgrade-Skript für SQLite
gibt es weder im Upstream noch in den Exponential-Repositorys: Um eine SQLite-Datenbank über eine Generation hinweg zu
bringen, konvertieren Sie sie mit den Konvertierungsrezepten der Skeleton-Anleitung nach MySQL oder PostgreSQL,
aktualisieren Sie sie dort und konvertieren Sie sie zurück, wenn Sie möchten.

**Das Legacy-Schema in einer v5-Datenbank.** Der `CoreInstaller` von `se7enxweb/exponential-platform-dxp-core` lädt das
Schema des Legacy-Kernels (`ezpublish_legacy/kernel/sql/<engine>/...`) nach dem Plattform-Schema, wenn dieses
Verzeichnis existiert, wobei jedes `CREATE TABLE` zu `CREATE TABLE IF NOT EXISTS` wird; so sind Tabellen ohne
Gegenstück in 5.0 (`ezbasket`, `ezdiscountrule` und Ähnliche) für den Legacy-Kernel vorhanden. Das geschieht nur bei
einer Neuinstallation; eine migrierte Datenbank erhält diese Tabellen über [16.6.4](#1664-die-datenbank-schritt-für-schritt).

### 16.5.7 Administration, Twig, Feldtypen, REST und GraphQL

**Administration.** Die Administrationsoberfläche ist die des Upstreams der jeweiligen Generation, unter v5 als
„Exponential Platform DXP“ gebrandet (`se7enxweb/admin-ui`, [Administrationsoberfläche der Plattform](../../en/us/features/6.0/platform-admin-ui-fork.md)).
Nexus 5 liefert sie unter `/adminui/` aus (Siteaccess `adminui`); prüfen Sie Ihre eigene Siteaccess-Liste, denn die
Siteaccess-Namen von Nexus (`fh_eng`, `bold_eng`, `bold_ger`, `adminui`) sind die der Demo. Eigene Tabs und Menüs der
Administration sowie die Konfiguration des Universal Discovery Widget ziehen um wie im Upstream: 3.0 änderte die
Konfiguration von Tabs und UDW, 4.0 stellte das Back Office auf Bootstrap 5 und den Online-Editor auf CKEditor 5 um und
benannte CSS-Klassen von `ez-` in `ibexa-` sowie JavaScript-Ereignisse von `ez-` in `ibexa-` um.

**Twig.** Die Funktionen folgen der Generation, nicht Exponential: Es gibt keine Twig-Funktionen `exponential_*`. Der
v5-Kernel registriert `ibexa_render_field`, `ibexa_content_name`, `ibexa_field_value`, `ibexa_image_alias`,
`ibexa_render`, `ibexa_path` und `ibexa_url`. Vorher und nachher für ein 3.3-Template, das nach 4.x oder 5.0 umzieht:

```twig
{# 3.3 #}
<h1>{{ ez_content_name(content) }}</h1>
{{ ez_render_field(content, 'body') }}
<a href="{{ path('ez_urlalias', {'locationId': location.id}) }}">...</a>
{% set image = ez_image_alias(content.getField('image'), content.versionInfo, 'medium') %}

{# 4.x and 5.0 #}
<h1>{{ ibexa_content_name(content) }}</h1>
{{ ibexa_render_field(content, 'body') }}
<a href="{{ ibexa_path(location) }}">...</a>
{% set image = ibexa_image_alias(content.getField('image'), content.versionInfo, 'medium') %}
```

Template-Verweise verwenden seit 3.0 die `@`-Notation (`@ibexadesign/...` mit der Design-Engine,
`@IbexaCore/content_fields.html.twig` unter 4.x). Nexus-Templates verwenden überwiegend die Funktionen der Netgen Site
API: Im Nexus-5-Projekt kommt `ng_render_field` 223-mal vor, `ibexa_path` 110-mal, `ng_image_alias` 27-mal,
`ng_view_content` 12-mal und `nglayouts_render_result` 9-mal; Templates, die für die Netgen Site API auf Ibexa
geschrieben wurden, ziehen unverändert um.

**Feldtypen.** Innerhalb einer Generation behält jeder Kern-Feldtyp seine Speicherung. RichText bleibt DocBook-XML in
`data_text`; XmlText (`ezxmltext`) wird von 3.x und später nicht unterstützt, außer um es mit
`se7enxweb/ezplatform-xmltext-fieldtype` in RichText zu konvertieren (`ezxmltext:convert-to-richtext`, [16.5.11](#16511-hinweise-je-quellversion)).
Der Matrix-Feldtyp (`ezmatrix`) von 2.5 und später speichert ein anderes Format als der Legacy-Datentyp Matrix; eine
Legacy-Matrix wird mit `ezplatform:migrate:legacy_matrix` (2.5) konvertiert.

**REST.** Das REST-Präfix des v5-Stacks ist `/api/ibexa/v2` (`ibexa.rest.path_prefix` in den Standardeinstellungen von
`ibexa/rest`, verwendet von `config/routes/ibexa_rest.yaml` in Nexus 5; das Muster der Security-Firewall ist
`^/api/ibexa`). 4.0 benannte das Präfix von `/api/ezp/v2/` und die Medientypen von `application/vnd.ez.api.*` in
`application/vnd.ibexa.api.*` um; Clients, die für 3.3 oder älter geschrieben wurden, müssen geändert oder der
Präfix-Parameter muss überschrieben werden. Nexus 5 authentifiziert REST mit JWT (`php bin/console lexik:jwt:generate-keypair`).

**GraphQL.** Das Schema wird je Projekt erzeugt: `php bin/console ezplatform:graphql:generate-schema` (2.5),
`ibexa:graphql:generate-schema` (3.x und später; in den Exponential-Forks nicht umbenannt). 4.0 benannte das Argument
`id` in `contentId`, `_info` in `_contentInfo` und `<ContentType>Content` in `<ContentType>Item` um; 5.0 entfernt
zuerst das 4.6-Schema (`rm -r config/graphql`, dann erzeugen) und macht die Paginierung von RelationList dauerhaft.
Nexus 5 beantwortet GraphQL unter `/graphql`.

### 16.5.8 Suche, HTTP-Cache, Cron, Benutzer, Bilder und URL-Aliase

**Suche.** Nexus 5 liest die Engine aus `SEARCH_ENGINE` in `.env` (standardmäßig legacy; Solr optional mit
`SOLR_DSN`). Solr wird mit dem Fork `se7enxweb/ezplatform-solr-search-engine` (3.x) und `ibexa/solr` (v5) unterstützt;
Elasticsearch ist ein kommerzielles Paket und nicht Teil von Nexus. Bauen Sie nach einer Migration den Index neu auf:

```bash
php bin/console exponential:reindex                      # v5, and 3.x/4.6 forks
php bin/console exponential:reindex --iteration-count=100
php bin/console ibexa:reindex                            # 3.x/4.6 forks, alias
php bin/console ezplatform:reindex                       # 2.5
```

Ab 4.6 braucht die Solr-Konfiguration die Suchkomponente `spellcheck`; erzeugen Sie die Solr-Konfiguration mit dem
Skript des Herstellers neu und starten Sie Solr neu. 3.0 entfernte `ezplatform:solr_create_index`; verwenden Sie den
Befehl zum Neuindizieren.

**HTTP-Cache.** `ibexa/http-cache` (v5) und `se7enxweb/ezplatform-http-cache` (3.x). Nexus 5 legt die Purge-Art mit
`HTTPCACHE_PURGE_TYPE` fest (`local` für den Symfony-Proxy, `varnish` für Varnish). Die VCL-Dateien liegen in
`vendor/ibexa/http-cache/docs/varnish/vcl/` (`varnish5.vcl`, `varnish6.vcl`, `varnish7.vcl`, `parameters.vcl`).
Die VCL-Änderungen des Herstellers in 3.3.14, 3.3.41 (keine Komprimierung von REST- und JSON-Antworten) und 5.0.8
gelten; nehmen Sie die VCL der Version, die Sie installieren, nicht die, die Sie hatten. 3.0 änderte den
User-Context-Header in `X-User-Context-Hash` und den Purge auf `PURGEKEY` mit `xkey-softpurge`.
`php bin/console fos:httpcache:invalidate:path / --all` leert alles, wenn ein Proxy im Einsatz ist.

**Cron und Messenger.** Ein Cron-Eintrag führt die Symfony-Cronjobs aus:

```bash
*/5 * * * * /usr/bin/php /var/www/site/bin/console ibexa:cron:run --env=prod
```

Nexus 1.0.0.x führt zusätzlich die Legacy-Cronjobs über die Bridge aus (`ngsite.cron`:
`bin/console ezpublish:legacy:script runcronjobs.php`). Unter v5 ist der Messenger-Transport `MESSENGER_TRANSPORT_DSN`;
Nexus 5 liefert `doctrine://default?auto_setup=0` aus, und unter SQLite setzen Sie `sync://`, weil der
Doctrine-Transport eine zweite Verbindung verlangt ([SQLite für Exponential Platform](../../en/us/features/6.0/platform-sqlite-install.md)).

**Benutzer und Passwort-Hashes.** Die Hash-Typen sind in jeder Generation und im Legacy-Kernel gleich nummeriert:

| Typ | Name | Legacy 6.0 | 2.5 | 3.x, 4.x, 5.0 |
|---|---|---|---|---|
| 1, 2, 3 | MD5 (Passwort, Benutzer, Site) | ja | ja | in 3.0 entfernt: die Anmeldung schlägt fehl, Benutzer müssen ihr Passwort zurücksetzen |
| 4 | MySQL `PASSWORD()` | ja | nein | nein |
| 5 | Klartext | ja | ja | in 3.0 entfernt |
| 6 | bcrypt | ja | ja | ja |
| 7 | PHP-Standard (`password_hash`) | ja, der Standard (`HashType=php_default`) | ja, der Standard | ja, der Standard |
| 256 | ungültig | nein | nein | nur 5.0 (`PASSWORD_HASH_INVALID`) |

Suchen Sie vor einem Wechsel auf 3.x oder später die alten Hashes mit der Abfrage aus [16.4](#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen)
oder mit dem Befehl:

```bash
php bin/console ezplatform:user:validate-password-hashes            # 2.5
php bin/console exponential:user:validate-password-hashes           # Exponential 3.x, 4.6 and v5
```

Benutzer mit den Typen 1 bis 5 brauchen ein neues Passwort (die Hinweise des Herstellers zu 3.3.28 und 4.2 verlangen
nach dem Advisory IBEXA-SA-2022-009 ebenfalls, Passwörter zu widerrufen). Nexus 5 hasht Symfony-Passwörter mit
`password_hashers: PasswordAuthenticatedUserInterface: 'auto'` in `security.yaml`. Sitzungen ziehen nicht mit um: Nach
der Umstellung melden sich alle neu an.

**Bilder.** Originale bleiben dort, wo die IO-Konfiguration sie ablegt (`ibexa.site_access.config.default.io.root_dir`,
`%webroot_dir%/$var_dir$/$storage_dir$`, also `public/var/<site>/storage`; `web/var/...` unter 1.x und 2.x), und ihre
Pfade sind in `ezimagefile` und im XML des Felds gespeichert. Kopieren Sie dieses Verzeichnis zusammen mit der
Datenbank. Variationen werden je Siteaccess unter `image_variations` definiert (Nexus 5: `config/app/packages/image.yaml`),
mit Filtersätzen von LiipImagine; sie werden bei Bedarf neu erzeugt. `exponential:images:normalize-paths` repariert
Pfade mit nicht druckbaren Zeichen ([16.7](#167-häufige-probleme)).

**URL-Aliase.** Die Aliase liegen in `ezurlalias_ml` (`ibexa_url_alias_ml` unter 5.0) und ziehen mit der Datenbank um.
Der Slug-Konverter verwendet in jeder Generation standardmäßig die Transformation `urlalias`
(`SlugConverter::DEFAULT_CONFIGURATION`) und wird unter `ibexa: url_alias: slug_converter: transformation:` konfiguriert
(4.x, 5.0). Sites, die mit einer anderen Transformation vom Legacy-Kernel kamen, behalten diese mit der Transformation
`urlalias_compat` oder `urlalias_iri`, wie die 5.x-Migrationsseite des Herstellers beschreibt. Bauen Sie Aliase nur
neu auf, wenn die Prüfung in [16.8](#168-die-migration-prüfen) fehlschlägt:

```bash
php bin/console exponential:urls:regenerate-aliases
```

### 16.5.9 Seitenaufbau: Page Builder gegenüber Netgen Layouts

Nexus baut Seiten mit Netgen Layouts auf (`netgen/layouts-*` 1.4 unter 2.5 bis 4.6, 2.0 unter v5, der Fork
`se7enxweb/layouts-core` unter PHP 8.4). Der kommerzielle Page Builder (`ezlandingpage`, Tabellen `ezpage_*`) hat in
Nexus kein Gegenstück, und kein Werkzeug konvertiert Landing Pages in Layouts. Planen Sie es als inhaltliche Aufgabe:

1. Listen Sie die Landing Pages auf: `SELECT COUNT(*) FROM ezcontentobject_attribute WHERE data_type_string = 'ezlandingpage';`
2. Bauen Sie für jedes Landing-Page-Layout ein Layout in Netgen Layouts mit denselben Zonen und einer Regel, die es dem
   Ort zuordnet, und legen Sie die Blöcke (Listen, Inhalte, HTML) als Layout-Blöcke mit Collections neu an.
3. Entfernen Sie das Feld `ezlandingpage` aus den Inhaltsklassen, sobald die Layouts live sind, oder behalten Sie das
   Feld und lassen Sie das Template es bis dahin ignorieren.

Die ezflow-Migration des Herstellers (`ezsystems/ezflow-migration-toolkit`, `ezflow:migrate`) und die Migration von
Landing Pages (`ezsystems/ezplatform-page-migration`, `ezplatform:page:migrate`, 2.2 und später) sind Werkzeuge der
kommerziellen Edition für den Umzug in den Page Builder; sie sind nicht Teil der Exponential-Pakete. Inhalte aus dem
Form Builder (`ezform`) und die Tabellen des redaktionellen Workflows sind ebenfalls kommerziell; exportieren Sie vor
dem Umzug, was Sie brauchen.

### 16.5.10 Eigene Bundles portieren

Eine Checkliste für jedes Bundle unter `src/` oder in Ihren eigenen Paketen:

- [ ] Läuft es auf der letzten Release der Quellgeneration? Beheben Sie das zuerst.
- [ ] Die Namespaces der Kernel-API folgen der Zielgeneration ([16.5.5](#1655-namespaces-bundles-und-konfigurationsschlüssel)).
- [ ] Feldtypen: `extends FieldType`, `getName( Value $value, FieldDefinition $fieldDefinition, string $languageCode ): string`
      (3.0), Service-Tags umbenannt (`ezpublish.fieldType` zu `ezplatform.field_type`, später `ibexa.field_type`;
      `ezpublish.storageEngine.legacy.converter` zu `ezplatform.field_type.legacy_storage.converter`, später
      `ibexa.field_type.storage.legacy.converter`).
- [ ] Signal Slots durch Event-Subscriber ersetzt (3.0).
- [ ] Query-Typen getaggt (`ezplatform.query_type`, später `ibexa.query_type`) oder automatisch konfiguriert; sie werden
      nicht mehr über den Klassennamen gefunden (3.0).
- [ ] Befehle erweitern `Symfony\Component\Console\Command\Command` mit Konstruktor-Injektion; unter Symfony 7 mit dem
      Attribut `#[AsCommand]`.
- [ ] Routen: von Annotation zu Attribut (5.0); Controller werden als `App\Controller\X::action` referenziert.
- [ ] Symfony-Ereignisklassen: `GetResponseEvent` zu `RequestEvent`, `FilterResponseEvent` zu `ResponseEvent`,
      `GetResponseForExceptionEvent` zu `ExceptionEvent` (3.0).
- [ ] Twig-Extensions: `Twig\Extension\AbstractExtension`, `Twig\TwigFunction` (3.0); Twig 3.24 unter Nexus 5.
- [ ] Container-Parameter `*.class` durch Klassennamen ersetzt (3.0).
- [ ] Config-Resolver zum Zeitpunkt des Aufrufs verwendet, nicht im Konstruktor (3.0).
- [ ] JavaScript: `ez-`-Ereignisse und -Klassen zu `ibexa-` (4.0); CKEditor-5-Plugins statt AlloyEditor (4.0).
- [ ] REST-Clients: `/api/ibexa/v2`, `application/vnd.ibexa.api.*` (4.0).
- [ ] Bundles Dritter: Es gibt eine Release für die Ziel-Version von Symfony oder einen se7enxweb-Fork
      ([Paketübersicht](../spezifikationen/6.0/platform-paketzuordnung.md)).

Vorher und nachher, ein Signal Slot aus 2.5, aus dem ein Event-Subscriber für 4.x wurde:

```php
// 2.5: src/AppBundle/Slot/OnPublishSlot.php
namespace AppBundle\Slot;

use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot;

class OnPublishSlot extends Slot
{
    public function receive( Signal $signal )
    {
        if ( !$signal instanceof Signal\ContentService\PublishVersionSignal ) {
            return;
        }
        // $signal->contentId, $signal->versionNo
    }
}
```

```php
// 4.x and 5.0: src/EventSubscriber/OnPublishSubscriber.php
namespace App\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OnPublishSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ PublishVersionEvent::class => 'onPublish' ];
    }

    public function onPublish( PublishVersionEvent $event ): void
    {
        $content = $event->getContent();
        // $content->id, $content->versionInfo->versionNo
    }
}
```

Und ein Befehl aus 2.5, aus dem ein v5-Befehl wurde:

```php
// 2.5
class ReportCommand extends ContainerAwareCommand
{
    protected function configure() { $this->setName( 'app:report' ); }
    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $contentService = $this->getContainer()->get( 'ezpublish.api.service.content' );
    }
}
```

```php
// v5 (Symfony 7.4)
#[AsCommand( name: 'app:report' )]
final class ReportCommand extends Command
{
    public function __construct( private readonly ContentService $contentService )
    {
        parent::__construct();
    }

    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        return Command::SUCCESS;
    }
}
```

`ContentService` ist dort `Ibexa\Contracts\Core\Repository\ContentService`; Autowiring injiziert ihn.

### 16.5.11 Hinweise je Quellversion

**eZ Platform 1.x.** Gehen Sie zuerst mit den Schritten des Herstellers auf 2.5 (Branch anlegen, das Upstream-Tag
mergen, `composer.json` und `composer.lock` auflösen, dann die Datenbankskripte aus [16.5.2](#1652-die-quelle-auf-die-letzte-release-ihrer-linie-bringen)).
2.2 stellte die Datenbank auf utf8mb4 und die Speicherung der Landing Pages um; 2.5 brachte Webpack Encore (Node.js und
Yarn) neben Assetic. Sites, die vom 5.x-Stack kamen, stoßen bei der Migration der Landing Pages auf eine Exception,
wenn interne Entwürfe von Landing Pages existieren (sie haben keine Zeile in `ezcontentobject_name`): Setzen Sie vor dem
Umzug `InternalDraftsCleanUpLimit` und `InternalDraftsDuration` in der Legacy-`content.ini` auf 0 (in 6.0 ist die Dauer
das Array `InternalDraftsDuration[days]`, `[hours]`, `[minutes]`, `[seconds]`) und führen Sie den Cronjob
`internal_drafts_cleanup` aus. Sites, die von 5.x kamen, begegnen außerdem `defaultLayout`-Fehlern für das Page-Feld;
die temporäre Layout-Konfiguration des Herstellers steht auf der 2.5-Datenbankseite.

**eZ Platform 2.5 LTS.** Die ergiebigste Generation für Weg B: Der 7.5-Kernel verwendet das gesamte Legacy-Schema, und
Exponential führt darauf sowohl Nexus `1.0.0.x` als auch Exponential Platform Legacy `v2.5.0.x`, mit dem Legacy-Kernel
und der Bridge. Für Weg A innerhalb von 2.5: Tauschen Sie die Pakete aus, behalten Sie die Struktur mit `app/` und die
Konfiguration mit `ezpublish:` bei. Die Befehle der Legacy Bridge dieser Linie behalten ihre Namen mit `ezpublish:`.

**eZ Platform 3.3 / Ibexa 3.3 LTS.** Vor 3.3 stellte der Hersteller das Projekt auf die Symfony-5-Struktur um
(`config/`, `templates/`, `public/`, kein `AppBundle`), auf Symfony Flex und Composer 2.0.13 oder neuer sowie auf den
Skeleton-Merge (`git merge v3.3.43 --allow-unrelated-histories` aus `ibexa/content-skeleton` usw.). Bei Exponential
sind die Ziele Nexus `1.1.0.x` und Exponential Platform Legacy `v3.3.44.x` (beide mit der Bridge 3.x), `se7enxweb/oss`
3.3 und `se7enxweb/exponential-platform` 3.2.9. Innerhalb von 3.3 wechselte Symfony von 5.3 auf 5.4 (3.3.13).

**Ibexa 4.x.** 4.0 benannte Namespaces, Konfigurationsschlüssel, Twig-Funktionen, Routen und das REST-Präfix um
([16.5.5](#1655-namespaces-bundles-und-konfigurationsschlüssel), [16.5.7](#1657-administration-twig-feldtypen-rest-und-graphql)).
Die kleineren Updates bis 4.6 sind überwiegend kommerziell: Migrationen für Taxonomie, Produktkatalog, Firmenkonten,
Dashboards und Aktivitätsprotokoll laufen über `ibexa:migrations:*`, das die Open-Source-Edition nicht hat. Für ein
Open-Source-Quellsystem besteht die Datenbankarbeit von 4.x aus der Spalte der Klassengruppe, den Token-Tabellen und der
Spalte `revoked`; der Rest ist das Composer-Update. Bei Exponential ist das Ziel Nexus `1.2.0.x` oder das
4.6-Skeleton. 4.6 braucht für den Asset-Build Node.js 18 oder neuer; die Anleitung zum Exponential-4.6-Skeleton
verwendet Node.js 20.

**Ibexa 5.0.** PHP 8.3 oder neuer, Symfony 7.3 (7.4 ab 5.0.7, wo `cache:clear` den Persistenz-Cache nicht mehr leert:
verwenden Sie `php bin/console cache:pool:clear <pool>`). Die Tabellen heißen `ibexa_*`. Bei Exponential: Nexus
`1.3.0.x` (PHP 8.4 oder neuer) oder das v5-Skeleton (8.3 oder neuer); jeder umbenannte Kernel-Befehl heißt nur noch
`exponential:*`. Der Exponential-v5-Kernel ist `se7enxweb/exponential-platform-dxp-core`; die installierte Referenz
verwendet v5.0.7.

## 16.6 Weg B: hinunter zum Legacy-Kernel von Exponential 6.0

### 16.6.1 Was beide gemeinsam haben

Das Inhaltsmodell jeder Generation ist das des Legacy-Kernels: Inhaltsklassen, Objekte, Versionen, Attribute, Orte,
URL-Aliase, Rollen, Sektionen, Zustände und Sprachen sind dieselben Tabellen mit derselben Bedeutung. Ein Vergleich der
Tabellenlisten (6.0.15 `kernel/sql/mysql/kernel_schema.sql`, 128 Tabellen; 2.5 `data/mysql/schema.sql`; 3.3 und 4.6
`schema.yaml`; v5 `schema.yaml`):

| Tabellen | Legacy 6.0 | 2.5 | 3.3 / 4.6 | 5.0 |
|---|---|---|---|---|
| Inhalte, Klassen, Orte, Versionen, Namen, Verknüpfungen, URL-Aliase, URLs, Rollen und Richtlinien, Sektionen, Zustände, Sprachen, Benutzer, Schlüsselwörter, Medien, Binärdateien, Bilder, Suchwörter, Papierkorb, Lesezeichen, Benachrichtigungen des Symfony-Stacks (`eznotification`) | ja (außer `eznotification`) | ja | ja, `ez*` | ja, umbenannt in `ibexa_*` |
| Shop, Workflow, Zusammenarbeit, Legacy-Benachrichtigungen, Informationssammlung, RSS, PDF-Export, Sitzungen, ausstehende Aktionen, geplante Skripte, Aufrufzähler, Wunschliste, MwSt., Rabatte, Bestellungen | ja | ja | laut den 3.0-Hinweisen des Herstellers entfernt (etwa 80 Tabellen) | nein |
| `ezcontentclass_attribute_ml`, `ezgmaplocation`, `ibexa_setting`, `ibexa_token`, `ibexa_token_type` | nein | `_ml` und `ezgmaplocation` | ja | umbenannt oder beibehalten |
| `expaudit_*`, `expmail_*`, `expbookmark_folder`, `ezrss_export_opml_item` | ja, mit 6.0.15 hinzugefügt | nein | nein | nein |

Spalten, die der Symfony-Stack gemeinsamen Tabellen hinzugefügt hat: `ezcontentobject.is_hidden`,
`ezuser.password_updated_at`, `ezcontentclass_attribute.is_thumbnail`, `ezkeyword_attribute_link.version`,
`ezsearch_object_word_link.language_mask`. Im 4.6-Schema sind sie alle nullable oder haben einen Standardwert, sodass
der Legacy-Kernel, der sie nicht kennt, weiterhin Zeilen einfügen kann; die Ausnahme ist
`ezkeyword_attribute_link.version` nach dem MySQL-Skript von 2.5 auf 3.0, das sie als `NOT NULL` ohne Standardwert
hinzufügt. Der Legacy-Kernel fügt Schlüsselwort-Verknüpfungen mit
`INSERT INTO ezkeyword_attribute_link ( keyword_id, objectattribute_id )` ein
(`kernel/classes/datatypes/ezkeyword/ezkeyword.php`), was dann im strikten SQL-Modus fehlschlägt; geben Sie der Spalte
einen Standardwert ([16.6.4](#1664-die-datenbank-schritt-für-schritt)). Spalten, die der Legacy-Kernel hinzugefügt hat:
`ezcontentbrowsebookmark.folder_id` und `priority`, `ezpdf_export.show_footer` und `footer_text`,
`ezrss_export.opml_head` und `podcast_head` (6.0.15).

### 16.6.2 Was Sie verlieren und was Sie gewinnen

| Sie verlieren | Sie gewinnen |
|---|---|
| Die Symfony-Administrationsoberfläche, ihren Inhaltsbaum, UDW und den Online-Editor CKEditor 5 | Die Legacy-Administration (`admin3`, responsiv), den Online Editor (`ezoe`) für XmlText |
| Twig-Templates, Symfony-Controller, die Netgen Site API | TPL-Templates mit dem Override-System, Template-Fetch-Funktionen, Module und Views |
| RichText als gespeichertes DocBook, bis es konvertiert ist | XmlText mit Custom Tags, die Legacy-Inhaltsdatentypen (Matrix, Option, Price, Star Rating mit seiner Extension, ezflow-Page) |
| REST v2 und GraphQL des Symfony-Stacks | Die Legacy-REST-Schicht (`rest.ini`), die Legacy-JSON-Aufrufe von `ezjscore` |
| Page Builder, Form Builder, Workflow (kommerziell) | Die Legacy-Workflow-Ereignisse, Informationssammlung, Shop, RSS-Import und -Export, Newsletter (`cjw_newsletter`) |
| Netgen Layouts auf Symfony | Exponential Layouts, die Legacy-Portierung von Netgen Layouts ([16.6.7](#1667-eine-durchgeführte-portierung-netgen-layouts-und-die-media-site)) |
| Messenger, Symfony-Cron | `runcronjobs.php` und die Cronjob-Teile |
| | Velocity als Webserver, SQLite als Produktions-Engine, Treiber für MongoDB und Oracle, das Audit-Protokoll und das Subsystem für Mail-Einstellungen von 6.0.15 |

### 16.6.3 Feldtypen

Der Legacy-Kernel registriert seine Datentypen in `content.ini` (`[DataTypeSettings] AvailableDataTypes[]`, 35 in der
Standarddatei; Extensions fügen weitere hinzu). Jeder `data_type_string`, den Ihre Bestandsaufnahme zeigt, braucht einen
Legacy-Datentyp, sonst kann der Legacy-Kernel das Feld nicht laden.

| Symfony-Feldtyp | Legacy 6.0 | Was zu tun ist |
|---|---|---|
| `ezstring`, `eztext`, `ezinteger`, `ezfloat`, `ezboolean`, `ezemail`, `ezurl`, `ezdate`, `ezdatetime`, `eztime`, `ezcountry`, `ezisbn`, `ezauthor`, `ezkeyword`, `ezselection`, `ezbinaryfile`, `ezmedia`, `ezimage`, `ezobjectrelation`, `ezobjectrelationlist`, `ezuser` | derselbe Bezeichner, dieselbe Speicherung | nichts |
| `ezgmaplocation` | die Extension `ezgmaplocation` (dieselbe Tabelle) | die Extension aktivieren |
| `eztags` (Netgen Tags) | die Extension `eztags` | sie aktivieren; dieselben Tabellen |
| `ezxmltext` | eingebaut | nichts; das ist das Legacy-Format |
| `ezrichtext` (DocBook) | kein Datentyp | konvertieren oder das DocBook behalten und die Ausgabe anpassen (siehe unten) |
| `ezmatrix` (2.5 und später) | ein Legacy-`ezmatrix` mit anderem Format | es gibt keine Rückkonvertierung; exportieren Sie die Werte und geben Sie sie neu ein, oder behalten Sie das Feld im Symfony-Stack |
| `ezimageasset` | kein Datentyp | ändern Sie das Feld in `ezobjectrelation`: Beide halten die ID des Zielinhalts in `data_int`, sodass der Wert erhalten bleibt; prüfen Sie, dass `ezcontentobject_link` die Relationszeilen enthält, die der Legacy-Kernel erwartet |
| `ezcontentquery` | kein Datentyp | durch einen Template-Fetch ersetzen (`fetch( 'content', 'list', ... )`) |
| `ezlandingpage`, `ezform` | kein Datentyp (kommerziell) | mit Exponential Layouts und Informationssammlung neu aufbauen; das Feld entfernen |
| Bezeichner aus 5.0 (`ibexa_string` und Ähnliche) | nein | wenn die 5.0-Datenbank die neuen Bezeichner verwendet hat, setzen Sie sie zurück (`UPDATE ezcontentclass_attribute SET data_type_string = 'ezstring' WHERE data_type_string = 'ibexa_string';` und dasselbe auf `ezcontentobject_attribute`) |

#### RichText zu XmlText

Kein Werkzeug im Upstream oder in den Exponential-Repositorys konvertiert RichText zurück in XmlText: Die Konvertierung
von `se7enxweb/ezplatform-xmltext-fieldtype` (`ezxmltext:convert-to-richtext`, `ezxmltext:import-xml`) geht nur in eine
Richtung, und `ezplatform-richtext` hat keine XmlText-Ausgabe. Die Zuordnung, die Sie brauchen, ist die Umkehrung des
Vorwärts-Stylesheets `lib/FieldType/XmlText/Input/Resources/stylesheets/eZXml2Docbook_core.xsl` dieses Pakets:

| RichText (DocBook) | XmlText |
|---|---|
| `<section>` (Wurzel) | `<section>` (Wurzel, mit den Namespaces `xmlns:image`, `xmlns:xhtml`, `xmlns:custom` von XmlText) |
| `<para>` | `<paragraph>` |
| `<literallayout>` | `<paragraph>` mit Kindelementen `<line>` |
| `<title ezxhtml:level="N">` | `<header level="N">` |
| `<emphasis>` | `<emphasize>` |
| `<emphasis role="strong">` | `<strong>` |
| `<emphasis role="underlined">`, `role="strikedthrough"` | `<custom name="underline">`, `<custom name="strike">` |
| `<subscript>`, `<superscript>` | `<custom name="sub">`, `<custom name="sup">` |
| `<blockquote>` | `<custom name="quote">` |
| `<itemizedlist>`, `<orderedlist>`, `<listitem>` | `<ul>`, `<ol>`, `<li>` |
| `<informaltable>` oder `<table>` mit `<caption>`, `<tbody>`, `<tr>`, `<th>`, `<td>` | `<table>` mit `<tr>`, `<th>`, `<td>` |
| `<link xlink:href="ezurl://N">` | `<link url_id="N">` |
| `<link xlink:href="ezlocation://N">`, `ezcontent://N` | `<link node_id="N">`, `<link object_id="N">` |
| `<anchor>` | `<anchor>` |
| `<ezembed xlink:href="ezlocation://N">`, `ezcontent://N` (und `<ezembedinline>`) | `<embed node_id="N">`, `<embed object_id="N">` (und `<embed-inline>`) |
| `<eztemplate>`, `<eztemplateinline>` mit `<ezcontent>` und `<ezconfig>` | `<custom name="...">` mit seinen Attributen |
| `<programlisting>` | `<literal>` |

Zwei Wege, sie anzuwenden:

1. **Das gespeicherte XML konvertieren.** Schreiben Sie anhand der Tabelle ein XSL-Stylesheet oder ein Skript, führen
   Sie es auf einer Kopie über `ezcontentobject_attribute.data_text` aus, wo `data_type_string = 'ezrichtext'` ist,
   validieren Sie das Ergebnis mit dem Legacy-Input-Handler für XmlText und ändern Sie dann `data_type_string` am
   Klassenattribut und an den Objektattributen in `ezxmltext`. Nur so bleiben die Felder im Online Editor bearbeitbar.
2. **Das DocBook behalten und rendern.** So ging die Portierung der Netgen Media Site auf den Legacy-Kernel vor: Die
   importierten Felder sind `ezxmltext`-Attribute, die noch DocBook enthalten. Der 6.0-Kernel rendert die
   DocBook-Elemente `ezembed` (mit `xlink:href`), `para` und `literallayout` in seinem XHTML-Output-Handler (Commit
   `d7bebcb39b`, „Render imported Ibexa docbook elements in XHTML output“) und erlaubt an ihnen `class`-Attribute
   (`5857c5baa8`); die Theme-Extension `se7enxweb/sevenx_themes_media` konvertiert die übrigen Elemente beim Rendern
   (`convertDocBookToEzXml()` in `autoloads/sevenxthemesmediaoperators.php`: `para` zu `paragraph`, `emphasis` zu
   `emphasize`, die Listen, `subscript`, `superscript`, `literallayout` zu `literal`, `xlink:href` zu `href`). Das
   reicht zur Anzeige; als Grundlage für die Bearbeitung taugt es nicht. Konvertieren Sie, bevor Redakteure diese
   Felder anfassen.

Eine dritte Möglichkeit, `se7enxweb/richtext-datatype-bundle` (aus `NetgenRichTextDataTypeBundle`, von den Linien 2.5
und 4.6 von Platform Legacy verlangt), fügt dem Legacy-Kernel einen Datentyp `ezrichtext` hinzu; seine README sagt, es
sei ein Prototyp, der „only shows the raw XML content of the field in a text area“.

### 16.6.4 Die Datenbank, Schritt für Schritt

Die Reihenfolge: Machen Sie die Daten, die es nur auf der Symfony-Seite gibt, auf der Quelle Legacy-kompatibel
(Feldtypen oben), bringen Sie dann das Schema auf den Stand des 6.0.15-Schemas, setzen Sie danach die Versionszeilen und
bauen Sie zuletzt neu auf, was der Legacy-Kernel selbst vorhält.

**Schritt 1: Sichern und den Symfony-Stack anhalten** (oder in den Wartungsmodus versetzen), wie in [16.4](#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen).

**Schritt 2 (nur 5.0): die Tabellennamen zurückholen.** Machen Sie entweder die Umbenennungen aus
[der Umbenennungstabelle](#die-tabellenumbenennungen-von-50-und-ihre-umkehrung) rückgängig, oder behalten Sie die Namen `ibexa_*` und
lassen Sie den Legacy-Kernel sein SQL mit `se7enxweb/sevenx_exponential_platform_v5_database_translator` übersetzen,
einer Legacy-Extension, die die Treiber für MySQL, SQLite und PostgreSQL ableitet und ausgehend Legacy-Namen sowie
eingehend Ibexa-Namen umschreibt (sie registriert sich mit `[DatabaseSettings] ImplementationAlias[...]` in ihrer
`site.ini.append.php`). Die umgekehrte Umbenennung, MySQL 8.0 oder MariaDB 10.5.2 und neuer (für `RENAME COLUMN`),
erste Zeilen:

```sql
RENAME TABLE ibexa_content TO ezcontentobject;
ALTER TABLE ezcontentobject RENAME COLUMN content_type_id TO contentclass_id;
RENAME TABLE ibexa_content_field TO ezcontentobject_attribute;
ALTER TABLE ezcontentobject_attribute RENAME COLUMN content_type_field_definition_id TO contentclassattribute_id;
RENAME TABLE ibexa_content_type TO ezcontentclass;
RENAME TABLE ibexa_content_type_field_definition TO ezcontentclass_attribute;
ALTER TABLE ezcontentclass_attribute RENAME COLUMN content_type_id TO contentclass_id;
-- ... one line per row of the rename table, columns included
```

PostgreSQL verwendet `ALTER TABLE ibexa_content RENAME TO ezcontentobject;` und dieselbe Form mit `RENAME COLUMN`.
Indizes behalten ihre Namen mit `ibexa_`, sofern Sie sie nicht ebenfalls umbenennen; die Abfragen des Legacy-Kernels
nennen keine Indizes, aber `ezsqldiff.php` meldet sie in Schritt 3 als Unterschiede. Übernehmen Sie die Liste aus Ihrer
eigenen `ibexa-4.6.latest-to-5.0.0.sql` (oder von der Seite des Herstellers), statt sie abzutippen, und prüfen Sie die
Zeile `ezpreferences` ([16.5.6](#1656-die-datenbank-generation-für-generation)).

**Schritt 3: das Schema auf den Stand des 6.0.15-Schemas bringen.** Der Legacy-Kernel liefert sein Schema als
`share/db_schema.dba` (128 Tabellen, einschließlich der Ergänzungen von 6.0.15) und ein Vergleichswerkzeug,
`bin/php/ezsqldiff.php`. Es nimmt zwei Schemas entgegen, eine `SOURCE` und ein `MATCH`, jeweils eine laufende Datenbank
oder eine Schemadatei, und gibt das SQL aus, das **das zweite (`MATCH`) in das erste (`SOURCE`) überführt**; `--reverse`
vertauscht das. Die Richtung verwechselt man leicht, und das Ergebnis der falschen Richtung ist eine Datei voller
`DROP TABLE`-Zeilen für die Legacy-Tabellen; nennen Sie daher das Referenzschema zuerst und Ihre Datenbank als zweites:

```bash
php bin/php/ezsqldiff.php --type=mysql --host=HOST --user=USER --password=PASSWORD share/db_schema.dba DATABASE > to-6.0.15.sql
php bin/php/ezsqldiff.php --type=postgresql --host=HOST --user=USER --password=PASSWORD share/db_schema.dba DATABASE > to-6.0.15.sql
```

(`--type` akzeptiert `mysql` und `postgresql`; `--check-only` setzt nur den Exit-Status.) Eine schnelle
Plausibilitätsprüfung der Richtung: `grep -c '^CREATE TABLE' to-6.0.15.sql` sollte auf einer Datenbank ab 3.x groß sein
(die entfernten Legacy-Tabellen), und `grep '^DROP TABLE' to-6.0.15.sql` sollte nur Tabellen der Symfony-Seite
auflisten (`ibexa_*`, `ezcontentclass_attribute_ml`). Listet es `ezbasket`, `ezworkflow` und Ähnliche auf, sind die
Argumente vertauscht. Lesen Sie `to-6.0.15.sql`, bevor Sie es ausführen. Es enthält vier Arten von Anweisungen:

- `CREATE TABLE` für die Legacy-Tabellen, die Ihrer Datenbank fehlen (alle entfernten auf einer 3.x- oder
  4.x-Datenbank, die `exp*`-Tabellen von 6.0.15 auf jeder Quelle): behalten Sie sie;
- geänderte Spaltendefinitionen und Indexänderungen (`ALTER TABLE ... CHANGE`/`ALTER COLUMN`, `DROP INDEX`,
  `CREATE INDEX`): behalten Sie sie, nachdem Sie jede einzelne gelesen haben; eine Anweisung, die eine Spalte schmaler
  macht als die darin liegenden Daten, schlägt im strikten Modus fehl oder schneidet Werte ab, vergleichen Sie sie also
  zuerst mit den Daten;
- `ALTER TABLE ... ADD` für Legacy-Spalten (`folder_id`, `show_footer`, `opml_head` ...): behalten Sie sie;
- `DROP TABLE` und `DROP COLUMN` für das, was nur der Symfony-Stack hat (`ibexa_*`, `ezcontentclass_attribute_ml`,
  `is_thumbnail`, `password_updated_at` ...). Entfernen Sie diese Zeilen, solange der Symfony-Stack noch gebraucht
  werden könnte (Rollback oder eine Bridge); der Legacy-Kernel ignoriert diese Tabellen und Spalten.

Führen Sie es dann aus, ohne `--force`:

```bash
mysql -u USER -p DATABASE < to-6.0.15.sql
psql -U USER -d DATABASE -f to-6.0.15.sql
```

**Schritt 4: die Spalte, die der Legacy-Kernel nicht füllen kann.** Auf einer Datenbank, die das MySQL-Skript von 2.5
auf 3.0 durchlaufen hat:

```sql
ALTER TABLE ezkeyword_attribute_link MODIFY version INT(11) NOT NULL DEFAULT 0;   -- MySQL, MariaDB
ALTER TABLE ezkeyword_attribute_link ALTER COLUMN version SET DEFAULT 0;           -- PostgreSQL
```

**Schritt 5: die Versionszeilen.** 3.0 und später löschen `ezpublish-version`; die Legacy-Update-Dateien führen darauf
nur ein `UPDATE` aus, fügen Sie die Zeilen also ein:

```sql
DELETE FROM ezsite_data WHERE name IN ('ezpublish-version', 'ezpublish-release');
INSERT INTO ezsite_data (name, value) VALUES ('ezpublish-version', '6.0.15stable');
INSERT INTO ezsite_data (name, value) VALUES ('ezpublish-release', '1');
```

Die Werte sind diejenigen, die `update/database/*/6.0/dbupdate-6.0.0-6.0.15.sql` schreibt.

Eine 1.x- oder 2.x-Datenbank hat noch eine Zeile `ezpublish-version`, aber sie nennt keine Legacy-Release: Die
Update-Dateien des Symfony-Kernels schreiben ihre eigene Kernel-Version hinein (`6.4.0` aus einer Neuinstallation, was
die sauberen Daten von 2.5 noch immer schreiben, `6.13.0` nach der Datei des Herstellers von 5.4 auf 6.13, `7.5.0` bis
`7.5.7` unter 2.5). Lesen Sie sie nicht als Legacy-Version und beginnen Sie die Legacy-Kette aus
[Kapitel 11](11-aktualisieren.md) nicht von ihr aus: Zu diesen Nummern gehört keine Legacy-Update-Datei. Eine 2.5-Datenbank
enthält bereits die Schemaänderungen der Upstream-Dateien `6.12/`, `7.2/` und `7.3/`, die Kapitel 11 beschreibt
([11.3](11-aktualisieren.md#die-verzeichnisse-612-72-und-73)); gehen Sie die Schritte 3 und 5 also wie bei jeder anderen
Quelle: Die Ausgabe von `ezsqldiff.php` in Schritt 3 ist dort kurz (die `exp*`-Tabellen und -Spalten von 6.0.15, einige
Indizes), und Schritt 5 ersetzt die Zeile.

**Schritt 6: Einstellungen, Speicher und Caches.** Richten Sie `settings/override/site.ini.append.php` auf die
Datenbank aus (`[DatabaseSettings] DatabaseImplementation`, `Server`, `Port`, `User`, `Password`, `Database`) und
kopieren Sie den Binärspeicher von `public/var/<site>/storage` (oder `web/var/...`) nach `var/<VarDir>/storage` der
Legacy-Wurzel, wobei Sie denselben Namen für `VarDir` beibehalten (`[FileSettings] VarDir`, `StorageDir`), weil die in
der Datenbank gespeicherten Bildpfade damit beginnen. Dann:

```bash
php bin/php/ezcache.php --clear-all
php bin/php/updatesearchindex.php --clean
```

Der Legacy-Suchindex (`ezsearch_word`, `ezsearch_object_word_link`) ist dieselbe Engine wie die Legacy-Suche des
Symfony-Stacks, bauen Sie ihn aber trotzdem neu auf: Der Symfony-Stack schreibt `language_mask` und die Spaltennamen
von 5.0, und ein sauberer Neuaufbau ist günstiger als eine Prüfung. URL-Aliase brauchen nichts;
`php bin/php/updateniceurls.php` steht bereit, falls die Prüfung in [16.8](#168-die-migration-prüfen) fehlende findet.

**SQLite.** `ezsqldiff.php` beherrscht nur MySQL und PostgreSQL. Konvertieren Sie eine SQLite-Quelle nach MySQL oder
PostgreSQL, führen Sie die Schritte dort aus und bringen Sie das Ergebnis mit der in [Kapitel 9](09-datenbanken.md)
beschriebenen Konvertierung in den SQLite-Treiber des Legacy-Kernels; oder installieren Sie 6.0 auf SQLite und
übertragen Sie die Inhalte auf Anwendungsebene.

**MySQL-Zeichensatz.** Der Symfony-Stack verwendet seit 2.2 utf8mb4-Verbindungen. Der Standard des Legacy-Kernels,
`Charset=utf-8`, wird auf der Verbindung auf das Drei-Byte-`utf8` von MySQL abgebildet, sodass Zeichen außerhalb der
Basic Multilingual Plane (Emoji) nicht hindurchgelangen ([Kapitel 9](09-datenbanken.md)). Prüfen Sie die Inhalte vor dem
Umzug darauf.

### 16.6.5 Konfiguration: Siteaccess-YAML zu INI

| Symfony (YAML, Schlüssel von 4.x / 5.0) | Legacy (INI) |
|---|---|
| `ibexa.siteaccess.list: [site, admin]` | `settings/override/site.ini.append.php`: `[SiteSettings] SiteList[]=site`, `[SiteAccessSettings] AvailableSiteAccessList[]=site`, `AvailableSiteAccessList[]=admin` |
| `ibexa.siteaccess.default_siteaccess: site` | `[SiteSettings] DefaultAccess=site` |
| `ibexa.siteaccess.match: { URIElement: 1 }` | `[SiteAccessSettings] MatchOrder=uri`, `URIMatchType=element` |
| `ibexa.siteaccess.match: { Map\Host: { www.example.com: site } }` | `[SiteAccessSettings] MatchOrder=host`, `HostMatchType=map`, `HostMatchMapItems[]=www.example.com;site` |
| `ibexa.siteaccess.groups` | keine Gruppen: Einstellungen, die sich Siteaccesses teilen, kommen nach `settings/override/`, der Rest nach `settings/siteaccess/<name>/` |
| `ibexa.system.<sa>.languages: [eng-GB, ger-DE]` | `settings/siteaccess/<sa>/site.ini.append.php`: `[RegionalSettings] Locale=eng-GB`, `ContentObjectLocale=eng-GB`, `SiteLanguageList[]=eng-GB`, `SiteLanguageList[]=ger-DE` |
| `ibexa.system.<sa>.design: app` | `[DesignSettings] SiteDesign=app`, `AdditionalSiteDesignList[]=...` |
| `ibexa.system.<sa>.content.view_cache: true` | `[ContentSettings] ViewCaching=enabled` |
| `ibexa.system.<sa>.image_variations` | `image.ini.append.php`: `[AliasSettings] AliasList[]=name`, dann ein Block `[name]` mit `Reference=` und `Filters[]=` |
| Regeln unter `ibexa.system.<sa>.content_view` | Blöcke in `override.ini.append.php` |
| `ibexa.url_alias.slug_converter.transformation: urlalias` | `[URLTranslator] TransformationGroup=urlalias` (bei beiden der Standard) |
| `DATABASE_URL` in `.env.local` | `[DatabaseSettings]` in `settings/override/site.ini.append.php` |
| `ibexa.system.<sa>.languages` mit Fallback für nicht übersetzte Inhalte | `[RegionalSettings] ShowUntranslatedObjects`, Reihenfolge von `SiteLanguageList[]` |

Eine Bildvariation, in beiden Formen (die Filternamen sind gemeinsam; die Migrationsseite des Herstellers zeigt
dasselbe Paar):

```yaml
ibexa:
    system:
        site_group:
            image_variations:
                articleimage:
                    reference: null
                    filters:
                        - { name: geometry/scalewidth, params: [770] }
```

```ini
# settings/siteaccess/site/image.ini.append.php
[AliasSettings]
AliasList[]=articleimage

[articleimage]
Reference=
Filters[]=geometry/scalewidth=770
```

Eine Regel für Inhalts-Views, in beiden Formen:

```yaml
ibexa:
    system:
        site_group:
            content_view:
                full:
                    article:
                        template: '@ibexadesign/full/article.html.twig'
                        match:
                            Identifier\ContentType: article
```

```ini
# settings/siteaccess/site/override.ini.append.php
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

### 16.6.6 Templates: Twig zu TPL

TPL maskiert Ausgaben nicht von selbst: Jeder Wert, der aus Inhalten oder aus der Anfrage stammt, erhält `|wash`. Das ist
die eine Regel, die eine Portierung, wenn man sie vergisst, in eine Cross-Site-Scripting-Lücke verwandelt.

| Twig (Symfony-Stack) | TPL (Legacy) |
|---|---|
| `{{ ibexa_content_name(content) }}`, `{{ location.contentInfo.name }}` | `{$node.name|wash}` |
| `{{ ibexa_render_field(content, 'body') }}`, `{{ ng_render_field(content.fields.body) }}` | `{attribute_view_gui attribute=$node.data_map.body}` |
| `ibexa_field_value(content, 'title')` | `$node.data_map.title.content` |
| `ibexa_field_is_empty(content, 'image')` | `$node.data_map.image.has_content|not` |
| `ibexa_image_alias(field, versionInfo, 'medium').uri`, `ng_image_alias(field, 'medium')` | `{$node.data_map.image.content['medium'].url|ezroot}` oder `{attribute_view_gui attribute=$node.data_map.image image_class='medium'}` |
| `{{ ibexa_path(location) }}`, `{{ path(location) }}` | `{$node.url_alias|ezurl}` (`|ezurl('no')` ohne Anführungszeichen) |
| `{{ ibexa_url(location) }}` (absolut) | `{$node.url_alias|ezurl('no', 'full')}` |
| `{{ asset('build/app.css') }}` | `{'stylesheets/app.css'|ezdesign}` |
| `{{ 'Read more'|trans }}` | `{'Read more'|i18n('design/site')}` |
| `{% set x = ... %}` | `{def $x=...}` (beim ersten Mal), `{set $x=...}` |
| `{% if %}{% elseif %}{% else %}{% endif %}` | `{if}{elseif}{else}{/if}` |
| `{% for item in items %}{% endfor %}` | `{foreach $items as $item}{/foreach}` |
| `{% include 'parts/teaser.html.twig' with {item: item} %}` | `{include uri='design:parts/teaser.tpl' item=$item}` |
| `{% extends 'pagelayout.html.twig' %}` mit Blöcken | das `pagelayout.tpl` des Siteaccess, das `{$module_result.content}` ausgibt |
| `{{ render(controller('App\\Controller\\X::list')) }}` | ein `fetch()` im Template, ein Template-Operator oder eine Modul-View |
| ein Query-Typ hinter einer Inhalts-View | `fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'class_filter_type', 'include', 'class_filter_array', array( 'article' ), 'limit', 10, 'sort_by', $node.sort_array ) )` |
| `{{ object.published|date('d.m.Y') }}` | `{$node.object.published|l10n('shortdate')}` oder `|datetime('custom', '%d.%m.%Y')` |
| `{{ dump(x) }}` | `{$x|attribute(show)}` |
| `nglayouts_render_zone`, ein Layout | `fetch( 'explayouts', 'resolve_layout', hash() )` und `{include uri='design:explayouts/layout.tpl' layout=$layout}` |

Mit `se7enxweb/ngsymfonytools` und der Legacy Bridge kann ein TPL-Template während eines Übergangs auch ein
Twig-Template einbinden (`symfony_include`); siehe [Site-Bundles](../../en/us/features/6.0/platform-site-bundles.md) und
[ngsymfonytools](../../en/us/features/6.0/extensions/ngsymfonytools.md). Zum Template-System selbst lesen Sie die
[Anleitung zu Templates und Design](../anleitungen/templates-und-design.md).

### 16.6.7 Eine durchgeführte Portierung: Netgen Layouts und die Media Site

Die Exponential-Installation hinter diesem Handbuch ist diesen Weg mit einer ganzen Site gegangen: Die Netgen Media Site,
wie sie auf dem Symfony-Stack läuft (die Nexus-Referenz), wurde mit ihren Layouts, ihrem Design und ihren Inhalten auf
den Legacy-Kernel 6.0 portiert. Das Vorgehen, in der Reihenfolge, in der es durchgeführt wurde:

1. **Der Seitenbaukasten wurde zu Legacy-Extensions.** Exponential Layouts (`se7enxweb/explayouts` und seine
   Schwester-Repositorys `explayouts_core`, `explayouts_standard`, `explayouts_site_api`, `explayouts_ui`,
   `explayouts_ui_api` und weitere) implementiert Netgen Layouts für den Legacy-Kernel neu: `eZPersistentObject`-Klassen
   auf `explayouts_*`-Tabellen statt Doctrine auf `nglayouts_*`, Block- und Layout-Typen in `explayouts.ini` statt in
   YAML, Block-Templates in der Design-Kaskade statt in Twig, Regeln, die `expLayoutsResolver` auflöst, und der Editor
   als Legacy-Modul. „The port follows the Netgen Layouts data shape, so a Netgen XML/JSON export can be
   imported with minimal transformation“ ([Exponential Layouts](../../en/us/bc/6.0/LAYOUTS.md#migration-and-parity),
   [Import und Export](../../en/us/bc/6.0/LAYOUTS.md#import-export-and-share-links)).
2. **Die Daten zogen über ihre IDs um.** Regeln, Collections und manuelle Collection-Einträge verweisen per ID auf
   Inhalte und Orte, daher wurden die IDs zusammen mit den Inhalten konsistent übernommen (und dort neu zugeordnet, wo
   sie sich ändern mussten); ein Layout-Block zeigt dann nach dem Umzug dieselben Inhalte.
3. **Das Twig-Design wurde von Hand zu TPL.** Das Media-Design liegt in `se7enxweb/sevenx_themes_media`; ein Konverter
   erzeugte die ersten Entwürfe, aber die Templates werden seitdem von Hand gepflegt, und die FAQ der Extension selbst
   sagt, dass sie nicht neu erzeugt werden dürfen. Rechnen Sie mit demselben: Ein Konverter von Twig zu TPL trifft die
   Syntax, nicht die Semantik (Rendern von Feldern, Bild-Aliase, Maskierung, die Fetch-Logik hinter Query-Typen).
4. **RichText blieb DocBook** und wird gerendert, wie in [16.6.3](#1663-feldtypen) beschrieben.
5. **Gemeinsame Layouts und verknüpfte Zonen** (Kopfbereich, Fußbereich, Pre-Footer) wurden als gemeinsame Layouts neu
   aufgebaut, auf deren Zonen andere Layouts verweisen ([Exponential Layouts](../../en/us/bc/6.0/LAYOUTS.md#shared-layouts-and-linked-zones)).

Lesen Sie [Exponential Layouts](../../en/us/bc/6.0/LAYOUTS.md) zum Datenmodell und zum Editor, die
[API des Layout-Editors](../spezifikationen/6.0/explayouts-ui-api.md) zur JSON-Schnittstelle und den
[Fork von layouts-core](../../en/us/features/6.0/platform-layouts-core-fork.md) zur Symfony-Seite.

### 16.6.8 Einen Symfony-Stack neben dem Legacy-Kernel behalten

Sie müssen den Symfony-Stack nicht abschalten. Exponential Platform Legacy und Nexus 1.0.0.x, 1.1.0.x und 1.2.0.x
betreiben beide Kernel über die Legacy Bridge auf einer Datenbank: Der Siteaccess `legacy_admin` (`legacy_mode: true`)
liefert die Legacy-Administration aus, die anderen Siteaccesses liefern Twig aus, und die Änderungen der Redakteure
erscheinen auf beiden Seiten. Wählen Sie den Branch der Bridge, der zur Plattformgeneration passt:

| Plattform | Bridge (`se7enxweb/legacy-bridge`) | Voraussetzungen |
|---|---|---|
| eZ Platform 2.5 | `master` (`v2.1.10` bis `v2.1.12`; kein Branch `2.1.x`) | `se7enxweb/exponential ^6.0.10`, `se7enxweb/ezpublish-kernel ^7.5.24` |
| eZ Platform 3.3 | `3.x` (`3.0.0.1` bis `v3.0.0.37`) | PHP `^8.0`, `se7enxweb/exponential ^6.0.12`, Kernel `~1.3`, Symfony 5.4 |
| Ibexa-4.6-kompatibel | `4.x` (`v4.0.0.0` bis `v4.0.0.3`) | PHP `^8.0`, `se7enxweb/exponential dev-main` |
| Platform v5 | `5.x` (`v5.0.0.0`, `v5.0.1` bis `v5.0.10`) | PHP `^8.4`, `ibexa/core ^5.0` (der v5-Core-Fork ersetzt es), Symfony 7.4, `se7enxweb/exponential dev-main`, `se7enxweb/sevenx_exponential_platform_v5_database_translator` (`v5.0.10`) |

Die Wurzel der Konfiguration ist `ez_publish_legacy:` (`enabled`, `root_dir`, `legacy_aware_routes`,
`clear_all_spi_cache_from_legacy`, `clear_all_spi_cache_on_symfony_clear_cache` und je Siteaccess `legacy_mode` sowie
`templating.view_layout` / `module_layout`), die Befehle stehen in [16.5.4](#1654-namen-der-konsolenbefehle), Installation
und Caching auf der Seite zur [Legacy Bridge](../../en/us/features/6.0/legacy-bridge.md) und in ihrer
[Spezifikation](../spezifikationen/6.0/legacy-bridge-bundle.md). Auf einer 5.0-Datenbank braucht die Legacy-Hälfte die
Tabellennamen aus Schritt 2 von [16.6.4](#1664-die-datenbank-schritt-für-schritt).

Das ist zugleich die sanfteste Form von Weg B: Lassen Sie die Symfony-Site live, geben Sie den Redakteuren die
Legacy-Administration, portieren Sie die Templates Inhaltsklasse für Inhaltsklasse und schalten Sie den öffentlichen
Siteaccess auf `legacy_mode: true` (oder auf Exponential 6.0 allein) um, wenn das letzte Template fertig ist.

### 16.6.9 Hinweise je Quellversion für Weg B

| Quelle | Schema-Schritt | Feldtypen | Sonstiges |
|---|---|---|---|
| 1.x, 2.x bis 2.5 | das Legacy-Schema ist vollständig; `ezsqldiff.php` fügt die Tabellen und Spalten von 6.0.15 hinzu; Versionszeilen (die alte Zeile nennt eine Version des Symfony-Kernels, keine Legacy-Version) | `ezrichtext` ab 2.x (XmlText-Inhalte älterer Sites können noch XmlText sein) | alle Passwort-Hashes werden akzeptiert |
| 3.0 bis 3.3 | `ezsqldiff.php` legt die entfernten Tabellen neu an; Versionszeilen; Standardwert für die Schlüsselwort-Verknüpfung | `ezrichtext`, `ezimageasset`, `ezmatrix` (neues Format) | die Sortierfelder 6 und 7 funktionieren wieder |
| 4.0 bis 4.6 | wie 3.3; die `ibexa_*`-Nebentabellen können bleiben | wie 3.3 | REST-Clients auf `/api/ibexa/v2` brauchen die Legacy-REST-Schicht |
| 5.0 | die Umbenennungen rückgängig machen oder die Übersetzer-Extension verwenden, dann wie 4.6 | Bezeichner zurück auf `ez*`, falls sie geändert wurden | der Exponential-v5-Installer legt bei Neuinstallationen die Legacy-Tabellen bereits an |

## 16.7 Häufige Probleme

Die ersten fünf sind die [häufigen Migrationsprobleme](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/)
des Herstellers; der Hersteller führt die zugehörigen Bereinigungsbefehle aus dem `EzPublishMigrationBundle` aus, das in
der Umgebung `dev` aktiviert ist, einem Bundle, das mit 3.0 entfiel. Sichern Sie vor jedem davon.

| Problem | Ursache | Behebung auf dem Symfony-Stack (Exponential) | Behebung auf dem Legacy-Kernel |
|---|---|---|---|
| URL-Aliase fehlen nach der Migration oder sind falsch | Aliase, die ein älteres System oder eine andere Transformation angelegt hat | `php bin/console exponential:urls:regenerate-aliases` (v5; `ibexa:urls:regenerate-aliases` unter 3.x/4.6), das die Historie behält | `php bin/php/updateniceurls.php` (`--update-nodes`, `--import`) oder den Ort neu veröffentlichen |
| Bilder werden nicht angezeigt | Bilddateipfade mit nicht druckbaren UTF-8-Zeichen | `php bin/console exponential:images:normalize-paths` (der Hersteller schreibt `ezplatform:images:normalize-path`); prüfen Sie `var_dir` auf Sonderzeichen | der Legacy-Kernel liest dieselben Pfade; beheben Sie sie zuerst auf der Symfony-Seite oder benennen Sie die Dateien und die Zeilen in `ezimagefile` um |
| „Unknown relation type 0“ über REST nach dem Bearbeiten | Relationen mit Typ 0, die ältere Systeme hinterlassen haben | `ezpublish:update:legacy_storage_clean_up_relation_type_eq_zero` des Herstellers (auflisten, Probelauf, beheben) aus dem entfallenen Migrations-Bundle; führen Sie es unter 3.x und später zuerst auf einer 2.5-Installation aus oder löschen Sie die Zeilen: `DELETE FROM ezcontentobject_link WHERE relation_type = 0;` nach ihrer Prüfung | der Legacy-Kernel verwendet die REST-API des Symfony-Stacks nicht; die Zeilen sind harmlos |
| Sprachfilterung in der Legacy-Suchmaschine falsch | Inhalte, die immer in mehreren Übersetzungen verfügbar sind, mit dem Flag an jedem Feld statt an der Hauptsprache | `ezpublish:update:legacy_storage_fix_fields_always_available_flag` des Herstellers | den Legacy-Index neu aufbauen (`updatesearchindex.php --clean`); der Legacy-Kernel liest das Flag aus der Sprachmaske des Objekts |
| Unterelemente leer, Suchen schlagen fehl | leerer `sort_key_string` nach dem Upgrade | `ezpublish:update:legacy_storage_update_sort_keys` des Herstellers | neu veröffentlichen oder den Suchindex neu aufbauen |
| Fehler im Tab der Unterelemente bei manchen Orten | Sortierung nach Bezeichner der Inhaltsklasse (6) oder Name (7), vom Symfony-Stack nicht unterstützt | stellen Sie diese Orte vor der Migration auf Name, Veröffentlichung oder Priorität um (Abfrage in [16.4](#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen)) | keine: der Legacy-Kernel unterstützt beides (`SORT_FIELD_CLASS_IDENTIFIER = 6`, `SORT_FIELD_CLASS_NAME = 7`) |
| Exception bei der Migration der Landing Pages | interne Entwürfe von Landing Pages ohne Zeile in `ezcontentobject_name` | setzen Sie in `content.ini` `InternalDraftsCleanUpLimit=0` und jeden Eintrag `InternalDraftsDuration[...]` (`days`, `hours`, `minutes`, `seconds`) auf 0 und führen Sie dann vor dem Umzug auf der Legacy-Seite den Cronjob `internal_drafts_cleanup` aus | derselbe Cronjob (`cronjobs/internal_drafts_cleanup.php`) |
| Benutzer können sich nach dem Wechsel auf 3.x oder später nicht anmelden | MD5- oder Klartext-Hashes (Typen 1 bis 5) | `exponential:user:validate-password-hashes`, danach ein Zurücksetzen des Passworts | keine: 6.0 akzeptiert alle und erzeugt den Hash bei der Anmeldung neu (`UpdateHash=true`) |
| Anmeldeseite ohne Styles, anonyme Benutzer abgewiesen | der Richtlinie `user/login` von Anonymous fehlen die neuen Siteaccess-Namen | nehmen Sie jeden Siteaccess in die Einschränkung `user/login` der Rolle Anonymous auf; der v5-Kernel hat die Siteaccess-Einschränkung aus der Anmelde-Richtlinie von Anonymous in seinen Ausgangsdaten entfernt | dieselbe Richtlinie in der Legacy-Administration unter Rollen |
| `ibexa:reindex` (oder ein anderer Befehl) „is not defined“ auf Platform v5 | der v5-Kernel registriert nur die Namen mit `exponential:` | verwenden Sie `exponential:reindex` und die anderen primären Namen ([16.5.4](#1654-namen-der-konsolenbefehle)) | entfällt |
| Zwei Kopien einer Klasse, zufälliges Verhalten | ein Upstream-Paket ist neben seinem Fork installiert | `composer show \| grep -E "^(ibexa\|ezsystems\|netgen)/"`, die Upstream-Anforderung entfernen | entfällt |
| Schlüsselwort-Feld lässt sich in der Legacy-Administration nicht speichern (MySQL) | `ezkeyword_attribute_link.version` ist `NOT NULL` ohne Standardwert | entfällt | Schritt 4 von [16.6.4](#1664-die-datenbank-schritt-für-schritt) |
| Legacy-Kernel meldet auf der ersten Seite eine fehlende Tabelle | eine 5.0-Datenbank mit Namen `ibexa_*` | entfällt | die Umbenennungen rückgängig machen oder die Übersetzer-Extension aktivieren |
| Felder nach Weg B als rohes XML oder leer angezeigt | `ezrichtext`-Inhalte ohne Legacy-Datentyp | entfällt | konvertieren oder rendern ([16.6.3](#1663-feldtypen)) |
| „Failed to create closure from callable ... SilvercommonExtension“ beim 4.1-Update | Commerce-Überreste | ignorieren, wie der Hersteller sagt | entfällt |
| „non-existent parameter“ oder „non-existent service payum.storage.doctrine.orm“ nach 4.6 | Reihenfolge der Bundles in `config/bundles.php`, alte `payum.yaml` | nehmen Sie `config/bundles.php` und `payum.yaml` aus dem Skeleton der Version, die Sie installieren | entfällt |
| Alte Seiten nach der Umstellung | HTTP-Cache (Varnish, Symfony-Proxy) oder der Legacy-View-Cache | `fos:httpcache:invalidate:path / --all`, `cache:pool:clear` | `php bin/php/ezcache.php --clear-all` |

## 16.8 Die Migration prüfen

Auf dem Symfony-Stack (Weg A):

```bash
php bin/console list exponential                         # the exponential:* commands are registered
php bin/console debug:router | head                      # routes load
php bin/console exponential:debug:config-resolver languages --scope=site
composer why ibexa/core                                  # expect se7enxweb/exponential-platform-dxp-core on v5
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/adminui/
```

Auf dem Legacy-Kernel (Weg B):

```bash
php bin/php/ezsqldiff.php --type=mysql --host=HOST --user=USER --password=PASSWORD --check-only share/db_schema.dba DATABASE; echo $?
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
```

Inhaltsprüfungen für beide: Zählen Sie die veröffentlichten Objekte, die Orte und die URL-Aliase vorher und nachher und
vergleichen Sie die Zahlen.

```sql
SELECT COUNT(*) FROM ezcontentobject WHERE status = 1;
SELECT COUNT(*) FROM ezcontentobject_tree;
SELECT COUNT(*) FROM ezurlalias_ml WHERE is_original = 1 AND is_alias = 0;
```

Öffnen Sie dann eine Stichprobe von Seiten jeder Inhaltsklasse, suchen Sie nach einem Wort, von dem Sie wissen, dass es
auf der Site steht, melden Sie sich als Redakteur an, legen Sie ein Testelement an, bearbeiten und veröffentlichen Sie es
und löschen Sie es wieder, laden Sie ein Bild hoch und sehen Sie sich seine Variationen an, und prüfen Sie das
Cron-Protokoll nach dem ersten Lauf. Vergleichen Sie bei Weg B außerdem das HTML einer Seite im alten und im neuen
System nebeneinander; Unterschiede im gerenderten RichText zeigen sich dort zuerst.

## 16.9 Rollback

Eine Migration wird zurückgerollt, indem man das alte System zurückholt, nicht, indem man Anweisungen rückgängig macht.

- **Lassen Sie den alten Code und seinen Server unangetastet**, bis das neue System mindestens einen vollständigen
  redaktionellen Zyklus durchlaufen hat. Schalten Sie den Datenverkehr über DNS, den Proxy oder den virtuellen Host um,
  sodass das Zurückschalten derselbe Vorgang in umgekehrter Richtung ist.
- **Behalten Sie den Datenbank-Dump aus [16.4](#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen)** und das Archiv des
  Speichers. Sie auf dem alten Server wiederherzustellen ist der Rollback; Inhalte, die nach der Umstellung im neuen
  System bearbeitet wurden, gehen verloren, sofern Sie sie nicht neu eingeben; deshalb gibt es den redaktionellen Stopp
  und ein kurzes Entscheidungsfenster.
- **Weg B mit beibehaltenen Symfony-Tabellen** (die `DROP`-Zeilen in [16.6.4](#1664-die-datenbank-schritt-für-schritt)
  entfernt) kann ohne Wiederherstellung zum Symfony-Stack zurückkehren: Die gemeinsamen Tabellen sind dieselben. Bauen
  Sie dort den Suchindex neu auf und leeren Sie die Caches; Inhalte, die zwischenzeitlich im Legacy-Kernel angelegt
  wurden, erscheinen, außer dort, wo sie Datentypen verwenden, die es nur im Legacy-Kernel gibt.
- **Weg A** innerhalb einer Generation ist eine Änderung in Composer: Stellen Sie `composer.json` und `composer.lock`
  wieder her, installieren Sie und leeren Sie die Caches.

## 16.10 Ein Plan und seine Phasen

Die Dauer wird von drei Zahlen aus der Bestandsaufnahme bestimmt: der Anzahl eigener Bundles und Templates, der Anzahl
der Felder von Typen ohne Gegenstück im Ziel und der Größe der Inhaltstabellen (die über die Laufzeit von Datenbank und
Neuindizierung entscheidet). Messen Sie die letzte mit einem vollständigen Probelauf auf einer Kopie; planen Sie den
redaktionellen Stopp als diese Zeit zuzüglich eines Puffers.

| Phase | Weg A | Weg B |
|---|---|---|
| 1. Bestandsaufnahme und Entscheidung | [16.4](#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen), [16.2](#162-einen-weg-wählen) | ebenso |
| 2. Quelle auf die letzte Release ihrer Linie | Schritte des Herstellers, auf der Quelle | ebenso |
| 3. Code | Paketaustausch, Befehlsnamen, Umbenennungen beim Generationswechsel | Portierung von Templates und Konfiguration, eigener Code als Extensions |
| 4. Daten | innerhalb einer Generation keine; SQL des Herstellers über Generationen hinweg | Konvertierungen von Feldtypen, `ezsqldiff.php`, Versionszeilen |
| 5. Probelauf auf einer Kopie, mit Zeitmessung | vollständige Abfolge | vollständige Abfolge |
| 6. Stopp, Umstellung, Prüfung | [16.8](#168-die-migration-prüfen) | ebenso |
| 7. Entscheidungsfenster, dann Aufräumen | Entwicklungswerkzeuge entfernen (Rector, Kompatibilitätsschicht) | die Tabellen, die nur der Symfony-Stack hat, entfernen, wenn der Symfony-Stack stillgelegt wird |

## 16.11 Checkliste

- [ ] Bestandsaufnahme erstellt: Versionen, Feldtypen, Sortierfelder, Hash-Typen, Sprachen, Größen, Bundles, Siteaccesses, Suche, Cache, Cron, PHP.
- [ ] Ziel anhand der Entscheidungstabelle gewählt; die Generation des Ziels passt zur Quelle.
- [ ] Sicherungen erstellt und einmal auf einer Testmaschine wiederhergestellt.
- [ ] Quelle auf der letzten Release ihrer Linie, Datenbankskripte ohne `--force` angewendet.
- [ ] Weg A: Forks installiert, keine Upstream-Duplikate, Flex-Rezepte aus `se7enxweb/sevenx-recipes`.
- [ ] Weg A: Cron- und Deployment-Skripte verwenden die `exponential:`-Befehle (unter v5 zwingend).
- [ ] Weg A: REST-Clients auf dem Präfix der Generation, GraphQL-Schema neu erzeugt.
- [ ] Weg A: Landing Pages mit Netgen Layouts neu aufgebaut oder bewusst außen vor gelassen.
- [ ] Weg B: Jeder `data_type_string` hat einen Legacy-Datentyp; RichText konvertiert oder gerendert.
- [ ] Weg B: 5.0-Namen zurückbenannt oder Übersetzer aktiv; Ausgabe von `ezsqldiff.php` geprüft und ausgeführt; Standardwert für die Schlüsselwort-Verknüpfung; Versionszeilen.
- [ ] Weg B: INI-Einstellungen für Siteaccesses, Sprachen, Designs, Bild-Aliase und Overrides; Speicher mit demselben `VarDir` kopiert.
- [ ] Weg B: Templates portiert, jede Ausgabe maskiert.
- [ ] Suchindex neu aufgebaut, Caches geleert, HTTP-Cache geleert.
- [ ] Prüfung aus [16.8](#168-die-migration-prüfen) bestanden; Redakteure haben sich angemeldet und veröffentlicht.
- [ ] Weg für den Rollback getestet, Entscheidungsfenster festgelegt.

## Referenzen

In diesem Repository:

- [11. Upgrade](11-aktualisieren.md) (die Update-Dateien von 6.0), [9. Datenbanken](09-datenbanken.md), [8. Die Site ausliefern](08-die-website-ausliefern.md), [1. Einführung](01-einfuehrung.md).
- Kapitel dieses Teils: [14. Migration von der 4.x-Linie](14-migration-von-4x.md), [15. Migration vom 5.x-Legacy-Stack](15-migration-von-5x-legacy.md), [17. Migrationsreferenz](17-migrationsreferenz.md).
- Plattform: [Paket-Forks und Umbenennungen von Befehlen](../../en/us/bc/6.0/platform-package-forks-and-command-renames.md),
  [Paketübersicht der Plattform](../spezifikationen/6.0/platform-paketzuordnung.md),
  [Namen der Konsolenbefehle der Plattform](../spezifikationen/6.0/platform-konsolenbefehle.md),
  [SQLite-Installer der Plattform](../spezifikationen/6.0/platform-sqlite-installer.md),
  [Exponential Platform Nexus](../../en/us/features/6.0/platform-nexus-starter.md),
  [DXP-Skeleton](../../en/us/features/6.0/platform-dxp-skeleton.md),
  [Administrationsoberfläche der Plattform](../../en/us/features/6.0/platform-admin-ui-fork.md),
  [Layouts auf der Plattform](../../en/us/features/6.0/platform-layouts-core-fork.md),
  [Framework-Forks für PHP 8.4 / 8.5](../../en/us/features/6.0/platform-php85-framework-forks.md),
  [Site-Bundles](../../en/us/features/6.0/platform-site-bundles.md),
  [SQLite für Exponential Platform](../../en/us/features/6.0/platform-sqlite-install.md).
- Legacy Bridge: [Legacy Bridge](../../en/us/features/6.0/legacy-bridge.md), [Spezifikation des Bundles](../spezifikationen/6.0/legacy-bridge-bundle.md), [ngsymfonytools](../../en/us/features/6.0/extensions/ngsymfonytools.md).
- Layouts: [Exponential Layouts](../../en/us/bc/6.0/LAYOUTS.md), [API des Layout-Editors](../spezifikationen/6.0/explayouts-ui-api.md).
- [Anleitung zu Templates und Design](../anleitungen/templates-und-design.md).
- Changelogs und Geschichte: [Exponential Platform Nexus](../../changelogs/extensions/exponential-platform-nexus.md),
  [Exponential Platform Legacy](../../changelogs/extensions/exponential-platform-legacy.md),
  [XmlText-Feldtyp](../../changelogs/extensions/ezplatform-xmltext-fieldtype.md),
  [Geschichte der Legacy Bridge](../../en/us/history/ecosystem/legacyBridge.md).
- Code: `share/db_schema.dba`, `kernel/sql/*/`, `bin/php/ezsqldiff.php`, `update/database/*/6.0/`,
  `kernel/classes/datatypes/ezuser/ezuser.php` (Hash-Typen), `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`,
  `settings/site.ini`, `settings/content.ini`, `settings/image.ini`.

Exponential-Repositorys auf GitHub:

- [se7enxweb/exponential-platform-nexus](https://github.com/se7enxweb/exponential-platform-nexus) und seine [Releases](https://github.com/se7enxweb/exponential-platform-nexus/releases),
  [se7enxweb/exponential-platform-nexus-starter](https://github.com/se7enxweb/exponential-platform-nexus-starter),
  [se7enxweb/exponential-platform-legacy](https://github.com/se7enxweb/exponential-platform-legacy) und seine [Releases](https://github.com/se7enxweb/exponential-platform-legacy/releases),
  [se7enxweb/exponential-platform](https://github.com/se7enxweb/exponential-platform) (die Skripte in `upgrade/db/`),
  [se7enxweb/exponential-platform-dxp](https://github.com/se7enxweb/exponential-platform-dxp),
  [se7enxweb/oss](https://github.com/se7enxweb/oss), [se7enxweb/core](https://github.com/se7enxweb/core).
- [se7enxweb/LegacyBridge](https://github.com/se7enxweb/LegacyBridge) (Paket `se7enxweb/legacy-bridge`),
  [se7enxweb/ezplatform-xmltext-fieldtype](https://github.com/se7enxweb/ezplatform-xmltext-fieldtype),
  [se7enxweb/sevenx_exponential_platform_v5_database_translator](https://github.com/se7enxweb/sevenx_exponential_platform_v5_database_translator).
- [se7enxweb/explayouts](https://github.com/se7enxweb/explayouts), [se7enxweb/sevenx_themes_media](https://github.com/se7enxweb/sevenx_themes_media),
  [se7enxweb/exponential](https://github.com/se7enxweb/exponential).

Die Dokumentation des Herstellers (des Quellsystems):

- Migration: [Migration vom 4.x- und 5.x-Legacy-Stack (Seite des Herstellers)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/),
  [Migration vom 5.x-Platform-Stack (Seite des Herstellers)](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
  (auch als [latest](https://doc.ibexa.co/en/latest/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/)
  und als [2.5-Version](https://doc.ibexa.co/en/2.5/migrating/migrating_from_ez_publish_platform/)),
  [Häufige Migrationsprobleme](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/).
- Updates: [Ibexa DXP aktualisieren](https://doc.ibexa.co/en/5.0/update_and_migration/update_ibexa_dxp/),
  [von 1.13 und 2.x](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_from_1.x_2.x/),
  [Anwendung auf 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_app_to_2.5/),
  [Datenbank auf 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_1.x_2.x/update_db_to_2.5/),
  [von 2.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/update_from_2.5/),
  [auf 3.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.2/),
  [Code an v3 anpassen](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/adapt_code_to_v3/),
  [auf 3.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_2.5/to_3.3/),
  [3.3 auf 3.3.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/update_from_3.3/),
  [3.3 auf 4.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_3.3/to_4.0/),
  [4.0 auf 4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.0/to_4.1/),
  [von 4.1](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.1/update_from_4.1/),
  [von 4.2](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.2/update_from_4.2/),
  [von 4.3](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.3/update_from_4.3/),
  [von 4.4](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.4/update_from_4.4/),
  [von 4.5](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.5/update_from_4.5/),
  [4.6 auf 4.6.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_from_4.6/),
  [4.6 auf 5.0](https://doc.ibexa.co/en/5.0/update_and_migration/from_4.6/update_to_5.0/),
  [5.0 auf 5.0.latest](https://doc.ibexa.co/en/5.0/update_and_migration/from_5.0/update_from_5.0/),
  [Update-Übersicht für 3.3](https://doc.ibexa.co/en/3.3/update_and_migration/update_ibexa_dxp/).
- Inkompatible Änderungen: [Abkündigungen in eZ Platform v3.0](https://doc.ibexa.co/en/5.0/release_notes/ez_platform_v3.0_deprecations/),
  [Abkündigungen in Ibexa DXP v4.0](https://doc.ibexa.co/en/5.0/release_notes/ibexa_dxp_v4.0_deprecations/),
  die [Zuordnungen der Kompatibilitätsschicht](https://github.com/ibexa/compatibility-layer/tree/4.0/src/bundle/Resources/mappings),
  die [API-Änderungen von 6.0](https://github.com/ezsystems/ezpublish-kernel/blob/v6.7.0/doc/bc/changes-6.0.md) des Kernels und
  seine [Upgrade-Hinweise zu 7.2](https://github.com/ezsystems/ezpublish-kernel/blob/7.5/doc/upgrade/7.2.md).
- Upstream-Repositorys: [github.com/ezsystems](https://github.com/ezsystems), [github.com/ibexa](https://github.com/ibexa),
  [ezsystems/LegacyBridge](https://github.com/ezsystems/LegacyBridge), [ibexa/recipes](https://github.com/ibexa/recipes).

Frameworks und PHP:

- Upgrade-Hinweise von Symfony: [UPGRADE-4.0](https://github.com/symfony/symfony/blob/4.4/UPGRADE-4.0.md),
  [UPGRADE-5.0](https://github.com/symfony/symfony/blob/5.0/UPGRADE-5.0.md),
  [UPGRADE-6.0](https://github.com/symfony/symfony/blob/6.0/UPGRADE-6.0.md),
  [UPGRADE-7.0](https://github.com/symfony/symfony/blob/7.0/UPGRADE-7.0.md),
  [Upgrade auf eine neue Hauptversion](https://symfony.com/doc/current/setup/upgrade_major.html).
- PHP-Migrationsanleitungen: [8.0](https://www.php.net/manual/en/migration80.php),
  [8.1](https://www.php.net/manual/en/migration81.php), [8.2](https://www.php.net/manual/en/migration82.php),
  [8.3](https://www.php.net/manual/en/migration83.php), [8.4](https://www.php.net/manual/en/migration84.php),
  [8.5](https://www.php.net/manual/en/migration85.php).
- [Composer replace](https://getcomposer.org/doc/04-schema.md#replace), [MySQL RENAME TABLE](https://dev.mysql.com/doc/refman/8.4/en/rename-table.html),
  [PostgreSQL ALTER TABLE](https://www.postgresql.org/docs/current/sql-altertable.html).

[Inhalt](README.md) · Zurück: [15. Migration vom 5.x-Legacy-Stack](15-migration-von-5x-legacy.md) · Weiter: [17. Migrationsreferenz](17-migrationsreferenz.md)
