---
title: Umstieg von Exponential 6
audience: dxp-developer
category: dxp-development
order: 42
summary: Eine Übersetzungstabelle für Entwickler von Exponential 6, die auf der DXP beginnen - Klassen zu Inhaltstypen, Knoten zu Platzierungen, INI zu YAML, TPL zu Twig, Erweiterungen zu Bundles, Module zu Controllern, fetch zum Repository - und die ehrliche Antwort auf die Frage, ob eine Website überhaupt umziehen sollte.
keywords: Exponential 6, Legacy, Migration, INI zu YAML, TPL zu Twig, Erweiterungen zu Bundles, Inhaltsklasse, Knoten, Platzierung, fetch, Siteaccess, Legacy Bridge, Vergleich, legacy, extensions to bundles, content class, node, location, legacy bridge, comparison
type: help
---
# Umstieg von Exponential 6

Diese Seite richtet sich an Entwickler, die Exponential 6 kennen (INI-Einstellungen, `.tpl`-Templates, Erweiterungen) und
ein Projekt auf der DXP beginnen. Sie ordnet, was Sie kennen, dem zu, was Ihnen begegnen wird, und hilft Ihnen zu
entscheiden, ob eine vorhandene Website umziehen sollte.

## Inhalt

1. [Sollte die Website umziehen?](#sollte-die-website-umziehen)
2. [Die Übersetzungstabelle](#die-übersetzungstabelle)
3. [Gewohnheiten, die bleiben, und Gewohnheiten, die sich ändern](#gewohnheiten-die-bleiben-und-gewohnheiten-die-sich-ändern)
4. [Beides betreiben](#beides-betreiben)

## Sollte die Website umziehen?

Exponential 6 ist die gepflegte Linie, keine Sackgasse ("Legacy" bedeutet nur den Kernel neben einer Symfony-Plattform,
[Die Produktfamilie kennen](../fuersprecher/die-produktfamilie-kennen.md#häufige-verwechslungen)). Ziehen Sie eine Website auf die
DXP um, wenn das Team Symfony, Twig und Doctrine möchte oder braucht, was die APIs der DXP in diesem Stack bieten.
Bleiben Sie bei Exponential 6, wenn die Website auf Kernel-Funktionen angewiesen ist, die die DXP nicht mitliefert: den
Shop, Workflows wie Freigabe oder Warten bis zu einem Datum, Exponential Layouts, Exponential Velocity
([Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md#was-sie-nicht-mitliefert)).

## Die Übersetzungstabelle

| Exponential 6 | DXP | Lesen |
|---|---|---|
| Inhaltsklasse, Klassenattribut | Inhaltstyp, Felddefinition | [Inhalte, Inhaltstypen und Platzierungen](inhaltstypen-und-platzierungen.md) |
| Inhaltsobjekt, Objektattribut | Inhaltselement, Feld | ebenda |
| Knoten | Platzierung (Location) | ebenda |
| Datentyp | Feldtyp | [Die mitgelieferten Feldtypen](die-mitgelieferten-feldtypen.md) |
| `settings/*.ini`, `.ini.append.php` | YAML unter `config/packages/` | [Der Verzeichnisaufbau des Projekts](der-verzeichnisaufbau-des-projekts.md) |
| Siteaccess in `site.ini` | `ibexa.siteaccess` und `ibexa.system.<scope>` | [Siteaccesses](siteaccesses-site-und-admin.md) |
| Design-Kette (`[DesignSettings]`) | Design-Engine: Designs aus geordneten Themes | [Twig und die Design-Engine](twig-templates-und-die-design-engine.md) |
| `.tpl`-Templates, `override.ini` | Twig-Templates, Konfiguration der Inhaltsansichten | [Twig-Funktionen](twig-funktionen-fuer-den-alltag.md) |
| `attribute_view_gui` | `ibexa_render_field` | ebenda |
| `fetch( 'content', 'node', ... )` | Repository-Services in PHP oder ein Feld vom Typ Inhaltsabfrage | [Inhalte, Inhaltstypen und Platzierungen](inhaltstypen-und-platzierungen.md#mit-dem-repository-in-php-arbeiten) |
| Bildvarianten in `image.ini` | Bildvariationen in YAML | [Bildvariationen](bildvariationen.md) |
| Erweiterung | Symfony-Bundle oder Code in `src/` | |
| Modul und View | Controller und Route | |
| Cronjob-Teile | `ibexa:cron:run`, Konsolenbefehle | [Cron](cron.md) |
| `bin/php/ezcache.php` | `bin/console cache:clear` und die Pool-Befehle | [Anwendungs-Cache](anwendungs-cache-und-redis.md) |
| Rollen und Richtlinien | Rollen und Richtlinien (dieselbe Idee) | |
| Die Administrationsdesigns | `/adminui/` | [Die Admin-Oberfläche](die-admin-oberflaeche.md) |

Kapitel 16 der Installationsanleitung zeigt die Gegenrichtung im Detail (YAML zu INI, Twig zu TPL), die sich genauso gut
als Wörterbuch liest
([16.6.5](../../installation/16-migration-von-ez-platform-und-ibexa.md#1665-konfiguration-siteaccess-yaml-zu-ini),
[16.6.6](../../installation/16-migration-von-ez-platform-und-ibexa.md#1666-templates-twig-zu-tpl)).

## Gewohnheiten, die bleiben, und Gewohnheiten, die sich ändern

**Behalten Sie**: zuerst in Inhaltstypen denken; Rollen statt Code für den Zugriff verwenden; Templates überschreiben
statt kopieren; nur den Cache leeren, der geleert werden muss.

**Ändern Sie**: Die Konfiguration wird in den Symfony-Container kompiliert, jede YAML-Änderung braucht also
`cache:clear`; Services werden injiziert, nicht aus einem globalen Objekt geholt; Assets werden mit Encore gebaut, nicht
zur Laufzeit gepackt.

## Beides betreiben

Die Legacy Bridge betreibt den Kernel von Exponential 6 innerhalb einer Symfony-Plattform auf einer Datenbank, sodass
Redakteure die Legacy-Administration behalten können, während ein Twig-Frontend wächst. Sie ist nicht Teil der DXP:
Verwenden Sie Exponential Platform Legacy oder eine Nexus-Linie, die den Legacy-Kernel enthält
([Legacy Bridge](../../funktionen/6.0/legacy-bridge.md), [Wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md)).

## Verwandte Seiten

- [Entwickler: das Buch zur Erweiterungsentwicklung](../erweiterungsentwicklung/README.md)
- [DXP, Legacy, Nexus oder Exponential 6 wählen](dxp-legacy-nexus-oder-exponential-6-waehlen.md)
- [Legacy Bridge](../../funktionen/6.0/legacy-bridge.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
