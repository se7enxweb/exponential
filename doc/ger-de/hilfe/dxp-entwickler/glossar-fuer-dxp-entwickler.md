---
title: Glossar für DXP-Entwickler
audience: dxp-developer
category: dxp-development
order: 50
summary: Die Begriffe von Exponential Platform DXP in einer Liste - von Admin-Oberfläche und Inhaltstyp bis Siteaccess, Theme, Variation und replace - jeweils mit einer Bedeutung in einer Zeile, der Entsprechung in Exponential 6, wo es eine gibt, und der Seite, die ihn erklärt.
keywords: Glossar, Begriffe, Definitionen, Inhaltstyp, Platzierung, Siteaccess, Design, Theme, Feldtyp, Variation, Metapaket, Skeleton, Fork, replace, Rezept, JWT, glossary, terms, definitions, content type, location, field type, metapackage, recipe
type: help
---
# Glossar für DXP-Entwickler

Diese Seite richtet sich an Entwickler, die auf diesen Seiten einem Begriff begegnen und seine Bedeutung in einer Zeile
suchen. Die Begriffe stehen in der alphabetischen Reihenfolge der englischen Originalbegriffe.

## Inhalt

1. [A bis F](#a-bis-f)
2. [G bis M](#g-bis-m)
3. [N bis S](#n-bis-s)
4. [T bis Z](#t-bis-z)

## A bis F

| Begriff | Bedeutung | Exponential 6 | Seite |
|---|---|---|---|
| Admin-Oberfläche (Admin UI) | die Administrationsoberfläche unter `/adminui/` | die Administrationsdesigns | [Die Admin-Oberfläche](die-admin-oberflaeche.md) |
| Alias (Befehl) | ein alter Befehlsname, der weiter funktioniert, etwa `ibexa:reindex` | | [Konsolenbefehle](konsolenbefehle-und-umbenennungen.md) |
| Inhaltselement (content item) | ein Inhalt eines Inhaltstyps | Inhaltsobjekt | [Inhalte](inhaltstypen-und-platzierungen.md) |
| Inhaltstyp (content type) | die Definition einer Art von Inhalt mit ihren Feldern | Inhaltsklasse | ebenda |
| Design | eine benannte, geordnete Liste von Themes | Design-Kette | [Twig und die Design-Engine](twig-templates-und-die-design-engine.md) |
| DXP | Digital Experience Platform; hier die Distribution `se7enxweb/exponential-platform-dxp` | | [Was sie ist](was-exponential-platform-dxp-ist.md) |
| Encore | Webpack Encore, das Stylesheets und Skripte baut | der Packer | [Assets bauen](assets-mit-yarn-und-encore-bauen.md) |
| Feldtyp (field type) | die Art eines Feldes (Textzeile, Bild, Relation) | Datentyp | [Feldtypen](die-mitgelieferten-feldtypen.md) |
| Flex | das Composer-Plugin von Symfony, das Rezepte anwendet | | [Projektaufbau](der-verzeichnisaufbau-des-projekts.md#was-flex-schreibt) |
| Fork | eine se7enxweb-Kopie eines Upstream-Pakets, für diese Linie geändert | | [Die Forks](die-se7enxweb-forks-und-replace.md) |

## G bis M

| Begriff | Bedeutung | Exponential 6 | Seite |
|---|---|---|---|
| GraphiQL | der GraphQL-Explorer im Browser, nur in der Entwicklung | | [GraphQL](graphql-und-graphiql.md#der-explorer) |
| HTTP-Cache | ganze Antworten, zwischengespeichert und über Tags geleert | statischer Cache und View-Caches | [HTTP-Cache](http-cache-und-varnish.md) |
| JWT | ein signiertes Token, das eine App statt einer Session sendet | | [JWT](jwt-authentifizierung.md) |
| Platzierung (location) | ein Platz eines Inhaltselements im Baum | Knoten | [Inhalte](inhaltstypen-und-platzierungen.md#der-baum) |
| Metapaket (metapackage) | ein Composer-Paket ohne Code, das andere Pakete listet | | [Das Metapaket lesen](das-metapaket-lesen.md) |
| Messenger | die Nachrichten-Warteschlangen-Komponente von Symfony | | [Messenger](messenger-und-benachrichtigungen.md) |

## N bis S

| Begriff | Bedeutung | Exponential 6 | Seite |
|---|---|---|---|
| Nexus | die Distribution von Exponential Platform mit Netgen Layouts und einer Demo-Website | | [Wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md) |
| Rezept (recipe) | Konfiguration, die ein Flex-Paket in Ihr Projekt schreibt | | [Projektaufbau](der-verzeichnisaufbau-des-projekts.md#was-flex-schreibt) |
| Replace | eine Composer-Deklaration, dass ein Paket ein anderes liefert | | [Die Forks](die-se7enxweb-forks-und-replace.md#wie-replace-funktioniert) |
| Repository | der Inhaltsspeicher und seine PHP-Services | die Inhaltsklassen des Kernels | [Inhalte](inhaltstypen-und-platzierungen.md) |
| Geltungsbereich (scope) | wo eine Einstellung gilt: `default`, eine Gruppe, ein Siteaccess, `global` | Reihenfolge der INI-Overrides | [Siteaccesses](siteaccesses-site-und-admin.md#wie-einstellungen-aufgelöst-werden) |
| Siteaccess | eine benannte Konfiguration, die je Anfrage gewählt wird | Siteaccess | [Siteaccesses](siteaccesses-site-und-admin.md) |
| Skeleton | die Projektvorlage, aus der Sie ein DXP-Projekt anlegen | | [Installation](installation-mit-dem-skeleton.md) |

## T bis Z

| Begriff | Bedeutung | Exponential 6 | Seite |
|---|---|---|---|
| Theme | ein Verzeichnis mit Templates und Assets innerhalb eines Designs | ein Design-Verzeichnis | [Das Standarddesign und Ihr Theme](das-standarddesign-und-ihr-theme.md) |
| Twig-Komponente | ein kleines Ausgabestück, das einer benannten Gruppe hinzugefügt wird | | [Twig-Komponenten](twig-komponenten.md) |
| Variation | eine erzeugte Größe eines Bildes | Bildvariante | [Bildvariationen](bildvariationen.md) |
| Version | ein Zustand eines Inhaltselements: Entwurf, veröffentlicht, archiviert | Version | [Inhalte](inhaltstypen-und-platzierungen.md#versionen-und-übersetzungen) |

## Verwandte Seiten

- [Glossar für Fürsprecher](../fuersprecher/glossar-fuer-fuersprecher.md)
- [Glossar für Erweiterungsentwickler](../erweiterungsentwicklung/teil-8-referenz/glossar-fuer-erweiterungsentwickler.md)
- [FAQ für DXP-Entwickler](faq-fuer-dxp-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
