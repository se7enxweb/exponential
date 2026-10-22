---
title: Was die DXP mitliefert und was nicht
audience: dxp-developer
category: dxp-development
order: 3
summary: Die maßgebliche Liste dessen, was Exponential Platform DXP installiert, aus den Anforderungen des Metapakets gelesen, und was sie nicht enthält - keinen Layout-Editor, keinen Freigabe-Workflow, keinen Scheduler, keinen Kernel von Exponential 6 - mit dem, was Sie stattdessen wählen.
keywords: DXP-Funktionen, was enthalten ist, Metapaket, composer.json, Layout-Editor, Workflow, Scheduler, Legacy-Kernel, Admin-Oberfläche, REST, GraphQL, Solr, HTTP-Cache, Feldtypen, nicht enthalten, DXP features, what is included, metapackage, layouts editor, legacy kernel, admin UI, field types, not included
type: help
---
# Was die DXP mitliefert und was nicht

Diese Seite richtet sich an Entwickler, Architekten und alle, die ein Angebot für ein DXP-Projekt schreiben. Sie listet,
was Exponential Platform DXP installiert, Paket für Paket aus den Anforderungen von `se7enxweb/exponential-platform-dxp`
gelesen, und was sie nicht installiert. Steht eine Funktion nicht auf dieser Seite, gehen Sie davon aus, dass sie nicht
Teil der DXP ist, bis Sie das Paket gefunden haben, das sie liefert.

## Inhalt

1. [Wie diese Liste entstanden ist](#wie-diese-liste-entstanden-ist)
2. [Was sie mitliefert](#was-sie-mitliefert)
3. [Was sie nicht mitliefert](#was-sie-nicht-mitliefert)
4. [Wenn Sie brauchen, was sie nicht mitliefert](#wenn-sie-brauchen-was-sie-nicht-mitliefert)
5. [Prüfen Sie es selbst](#prüfen-sie-es-selbst)

## Wie diese Liste entstanden ist

Die `composer.json` des Metapakets (`"type": "metapackage"`, Beschreibung "A meta package for installing 7x Exponential
Platform DXP (Stable; Open Source)") listet jedes Paket, das ein DXP-Projekt erhält. Diese Seite gruppiert sie. Dieselbe
Gruppierung steht unter
[Wer ist die Exponential Foundation?, Exponential Platform DXP](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp).

## Was sie mitliefert

| Bereich | Pakete im Metapaket | Was Sie erhalten |
|---|---|---|
| Inhaltskernel | `se7enxweb/exponential-platform-dxp-core`, `ibexa/core-persistence`, `ibexa/doctrine-schema` | Inhalts-Repository: Inhaltstypen, Inhaltselemente, Platzierungen, Versionen, Übersetzungen, Sektionen, Benutzer, Rollen und Richtlinien |
| Administration | `se7enxweb/admin-ui`, `se7enxweb/admin-ui-assets`, `se7enxweb/system-info` | die Administrationsoberfläche unter `/adminui/`, mit einer Ansicht der Systeminformationen |
| Bearbeitungsformulare | `ibexa/content-forms` | Symfony-Formulare zum Anlegen und Bearbeiten von Inhalten und Benutzern |
| Benutzer | `ibexa/user` | Anmeldung, Registrierung, vergessene und geänderte Passwörter, Benutzereinstellungen |
| Feldtypen | im Core, plus `se7enxweb/fieldtype-richtext`, `ibexa/fieldtype-matrix`, `ibexa/fieldtype-query` | Text, Zahl, Datum, Bild, Datei, Relation und mehr; Rich Text; Matrix; Inhaltsabfrage ([Liste](die-mitgelieferten-feldtypen.md)) |
| Ausgabe | `ibexa/design-engine`, `ibexa/standard-design`, `ibexa/twig-components`, `symfony/twig-bundle`, `twig/extra-bundle` | Twig-Templates mit Themes und Rückfall, das Design `standard`, Gruppen von Twig-Komponenten |
| Assets | `symfony/webpack-encore-bundle`, `symfony/asset` | Builds mit Webpack Encore für die Website und die Administration |
| APIs | `ibexa/rest`, `ibexa/graphql`, `overblog/graphiql-bundle`, `lexik/jwt-authentication-bundle`, `friendsofsymfony/jsrouting-bundle` | REST-API v2, GraphQL mit dem Explorer GraphiQL, JWT-Tokens |
| Suche | `ibexa/core-search`, `ibexa/search`, `ibexa/solr` | die Standard-Suchmaschine auf der Datenbank und eine optionale Solr-Engine |
| Cache | `ibexa/http-cache`, `symfony/cache` | HTTP-Cache mit Purging, tagfähiger Anwendungs-Cache |
| Hintergrundarbeit | `ibexa/cron`, `ibexa/messenger`, `ibexa/notifications`, `symfony/mailer` | ein Cron-Runner, Nachrichten-Warteschlangen, Systembenachrichtigungen, E-Mail |
| Sprachen | `ibexa/i18n`, `symfony/translation` | Übersetzungen der Oberfläche |
| Installation | `ibexa/post-install`, `doctrine/doctrine-migrations-bundle` | Schritte nach der Installation und Datenbankmigrationen |
| Kern für den Seitenbau | `se7enxweb/layouts-core` | das Modell und der Ausgabekern von Layouts, ohne Editor ([Layouts Core in der DXP](layouts-core-in-der-dxp.md)) |
| Framework | Komponenten und Bundles von Symfony 7.4, `doctrine/doctrine-bundle`, `monolog/monolog`, `knplabs/knp-menu-bundle` | das Anwendungs-Framework |

Anforderungen des Metapakets selbst: `"php": ">=8.3"`, `ext-ctype`, `ext-iconv`; das Skeleton ergänzt `ext-gd`,
`ext-curl`, `ext-json`, `ext-xsl`, `ext-xml`, `ext-intl` und `ext-mbstring`.

## Was sie nicht mitliefert

| Nicht enthalten | Warum es wichtig ist | Quelle |
|---|---|---|
| Ein Layout-Editor | Verlangt wird nur `se7enxweb/layouts-core`: keine Layouts-Oberfläche, keine Standardblöcke, kein Ibexa-Integrationspaket von Netgen Layouts | Anforderungen des Metapakets |
| Ein Freigabe-Workflow | Kein Workflow-Paket wird verlangt | Anforderungen des Metapakets |
| Ein Scheduler für das Veröffentlichen | Kein Scheduler-Paket wird verlangt | Anforderungen des Metapakets |
| Der Kernel von Exponential 6 | Keine Legacy Bridge, kein `se7enxweb/exponential` | README des Skeletons: "No LegacyBridge, no classic legacy kernel" |
| Inhalte und Design einer Demo-Website | Das Skeleton ist "a clean Symfony project for Exponential Platform with no demo design" | [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md#was-es-ist) |
| Ein Shop | Kein Commerce-Paket wird verlangt | Anforderungen des Metapakets |

Das README des Skeletons enthält außerdem eine allgemeine Liste "Main Exponential Platform DXP Features", die
Inhaltsfreigabe, zeitgesteuerte Veröffentlichung und ein Workflow-System nennt. Diese Zeilen beschreiben das Erbe der
Familie, nicht die Pakete, die das Metapaket installiert. Richten Sie sich nach der Paketliste
([Aussagen, die Sie vermeiden sollten](aussagen-die-sie-vermeiden-sollten.md)).

## Wenn Sie brauchen, was sie nicht mitliefert

| Sie brauchen | Wählen Sie |
|---|---|
| Redakteure, die Seiten mit Blöcken anordnen, auf Symfony | Exponential Platform Nexus ([Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md)) |
| Den Kernel von Exponential 6 neben Symfony | Exponential Platform Legacy ([Legacy Bridge](../../funktionen/6.0/legacy-bridge.md)) |
| Workflows, zeitgesteuertes Veröffentlichen, einen Shop und einen Layout-Editor ohne Symfony | Exponential 6 ([Erste Schritte](../erste-schritte.md)) |
| Etwas nur für Ihr Projekt | Schreiben Sie ein Symfony-Bundle in `src/` oder verlangen Sie ein Paket; prüfen Sie es zuerst gegen PHP 8.3 und Symfony 7.4 |

## Prüfen Sie es selbst

```bash
composer show se7enxweb/exponential-platform-dxp --tree | head -60   # what the metapackage pulls in
composer show | grep -E "^(ibexa|se7enxweb|netgen)/"                  # the content packages in your project
composer why netgen/layouts-ui                                        # prints nothing when it is not installed
```

## Verwandte Seiten

- [Das Metapaket lesen](das-metapaket-lesen.md)
- [DXP, Legacy, Nexus oder Exponential 6 wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md)
- [Die Produktfamilie kennen, häufige Verwechslungen](../fuersprecher/die-produktfamilie-kennen.md#häufige-verwechslungen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
