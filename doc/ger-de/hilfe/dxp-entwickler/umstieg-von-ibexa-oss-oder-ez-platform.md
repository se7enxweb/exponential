---
title: Umstieg von Ibexa OSS oder eZ Platform
audience: dxp-developer
category: dxp-development
order: 41
summary: Für Teams mit einem Projekt auf eZ Platform oder Ibexa OSS - welche Linie von Exponential Platform zu Ihrer Generation passt, was gleich bleibt (Namensräume, Twig-Funktionen, APIs), was sich ändert (Paketnamen, Befehlsnamen, Branding, Tabellennamen ab 5.0) und wo das vollständige Migrationskapitel steht.
keywords: Migration, Ibexa OSS, eZ Platform, Aktualisierung, Generation, Paketaustausch, Tabellenumbenennungen, ibexa_-Tabellen, Twig-Funktionen, Namensräume, Kapitel 16, Skeleton, Nexus, upgrade, generation, package swap, table renames, namespaces
type: help
---
# Umstieg von Ibexa OSS oder eZ Platform

Diese Seite richtet sich an Entwickler, deren Projekt auf eZ Platform oder Ibexa OSS läuft und die einen Umzug auf die
Linie Exponential Platform erwägen. Sie gibt die Orientierung; die Arbeit Schritt für Schritt steht in Kapitel
[16. Migration von eZ Platform und Ibexa](../../installation/16-migration-von-ez-platform-und-ibexa.md) der
Installationsanleitung.

## Inhalt

1. [Zu Ihrer Generation passen](#zu-ihrer-generation-passen)
2. [Was gleich bleibt](#was-gleich-bleibt)
3. [Was sich ändert](#was-sich-ändert)
4. [Die Arbeit in Phasen](#die-arbeit-in-phasen)
5. [Was keine direkte Entsprechung hat](#was-keine-direkte-entsprechung-hat)

## Zu Ihrer Generation passen

Die Regel von Kapitel 16: "zuerst dieselbe Generation". Ziehen Sie auf die passende Linie von Exponential Platform um und
aktualisieren Sie dann.

| Ihre Quelle | Ziel in Exponential Platform |
|---|---|
| Ibexa 5.0 | diese DXP: `se7enxweb/exponential-platform-dxp-skeleton` (oder Nexus 1.3 und der Starter) |
| Ibexa 4.0 bis 4.6 | zuerst auf 4.6, dann das [Skeleton 4.6.x](das-skeleton-v4x.md) oder Nexus 1.2 |
| eZ Platform 3.0 bis 3.2, Ibexa 3.3 | zuerst auf 3.3, dann die Linie 3.3 (Nexus 1.1, `se7enxweb/oss` 3.3) |
| eZ Platform 2.5 | Nexus 1.0 oder Exponential Platform Legacy 2.5 |

Quelle: [16.5.1 Die Generation wählen](../../installation/16-migration-von-ez-platform-und-ibexa.md#1651-die-generation-wählen).

## Was gleich bleibt

- **PHP-Namensräume** (`Ibexa\Contracts\Core\...`), Service-IDs und die öffentliche API: Die Forks behalten sie
  ([Die se7enxweb-Forks](die-se7enxweb-forks-und-replace.md#die-namensräume-bleiben-gleich)).
- **Twig-Funktionen** der Generation: auf v5 `ibexa_render_field`, `ibexa_content_name` und die übrigen; es gibt keine
  Twig-Funktionen `exponential_*`
  ([16.5.7](../../installation/16-migration-von-ez-platform-und-ibexa.md#1657-administration-twig-feldtypen-rest-und-graphql)).
- **REST und GraphQL**: dieselben Pakete (`ibexa/rest`, `ibexa/graphql`).
- **Ihre Bundles** in `src/`, Ihre Templates und Konfiguration ziehen innerhalb derselben Generation mit um.

## Was sich ändert

| Bereich | Änderung | Seite |
|---|---|---|
| Paketnamen | se7enxweb-Forks ersetzen mehrere Upstream-Pakete | [Die se7enxweb-Forks](die-se7enxweb-forks-und-replace.md) |
| Konsolenbefehle | primäre Namen `exponential:`, die alten Namen als Aliase | [Konsolenbefehle](konsolenbefehle-und-umbenennungen.md) |
| Branding | Administration und Ausgangsdaten sagen Exponential Platform DXP | [Forks der Plattformpakete, Schritt 5](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md#schritt-5-markennamen-in-installierten-daten-und-templates) |
| Lizenzfeld | `(GPL-2.0-or-later or proprietary)` | [Lizenzierung](lizenzierung.md) |
| Datenbanken | SQLite wird zusätzlich unterstützt | [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md) |
| Von 4.x nach 5.0 | jede Core-Tabelle in `ibexa_*` umbenannt; Bezeichner von Feldtypen beginnen mit `ibexa_` | [16.5.6](../../installation/16-migration-von-ez-platform-und-ibexa.md#1656-die-datenbank-generation-für-generation), [Feldtypen](die-mitgelieferten-feldtypen.md#in-v5-geänderte-bezeichner) |

## Die Arbeit in Phasen

1. **Bestandsaufnahme**: `composer.json` und `composer.lock`, eigene Bundles und Templates, verwendete Feldtypen, die
   Größe der Inhaltstabellen, die Suchmaschine.
2. **Bringen Sie die Quelle auf das letzte Release ihrer Linie.**
3. **Code**: Legen Sie ein Projekt aus dem passenden Skeleton an und übernehmen Sie `src/`, `templates/`,
   `translations/`, `assets/`, Konfiguration und Speicher.
4. **Daten**: Richten Sie es auf eine Kopie Ihrer Datenbank; wenden Sie für 4.x nach 5.0 die Tabellenumbenennungen an.
5. **Probelauf mit Zeitmessung**, dann einfrieren, umschalten, prüfen.

Führen Sie `exponential:install` nie gegen eine Datenbank mit Inhalten aus; es legt ein neues Repository an
([16.5.1](../../installation/16-migration-von-ez-platform-und-ibexa.md#1651-die-generation-wählen)). Die Hosting-Sicht auf
dieselbe Arbeit ist [Kunden von eZ Platform und Ibexa migrieren](../hosting/migrationen/kunden-von-ez-platform-und-ibexa-migrieren.md).

## Was keine direkte Entsprechung hat

- **Landingpages des Page Builder**: Mit der DXP wird keine Entsprechung ausgeliefert; Kapitel 16 baut sie mit Netgen
  Layouts auf Nexus neu
  ([16.5.9](../../installation/16-migration-von-ez-platform-und-ibexa.md#1659-seitenaufbau-page-builder-gegenüber-netgen-layouts)).
- **Funktionen kommerzieller Editionen** der Quelle: Die DXP ist nur aus Open-Source-Paketen gebaut; listen Sie auf, was
  Sie verwenden, und prüfen Sie jedes gegen [Was die DXP mitliefert](was-die-dxp-mitliefert-und-was-nicht.md).

## Verwandte Seiten

- [16. Migration von eZ Platform und Ibexa](../../installation/16-migration-von-ez-platform-und-ibexa.md)
- [Forks der Plattformpakete, Umbenennungen von Befehlen und Versionsregeln](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- [DXP, Legacy, Nexus oder Exponential 6 wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
