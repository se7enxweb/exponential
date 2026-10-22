---
title: "Template-Auflösung: Designs und Overrides"
audience: web-framework-developer, developer, designer
category: web-frameworks
order: 110
summary: Wie Exponential entscheidet, welche Template-Datei eine Ansicht rendert - die Design-Kette eines Siteaccess, Designs von Erweiterungen, die Ressource design:, Regeln in override.ini, die nach Klasse, Knoten, Bereich oder View-Modus greifen - im Vergleich mit Template-Pfaden, Theme-Vererbung und Twig-Namespaces in anderen Frameworks.
keywords: Design, design, Design-Kette, design chain, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, override.ini, Match, MatchFile, Priority, eZTemplateDesignResource, Template-Override, template override, Theme, Fallback, fallback, Web-Framework-Entwickler, web framework developer
type: help
---
# Template-Auflösung: Designs und Overrides

Diese Seite richtet sich an Entwickler, die die Template-Pfade und Namespaces von Twig, die View-Pfade und
Vendor-Overrides von Laravel oder die Template-Loader von Django kennen. Exponential löst einen Template-Namen wie
`design:node/view/full.tpl` in zwei Schritten auf: zuerst über eine **Design-Kette** (einen Stapel von Verzeichnissen),
dann über **Override-Regeln**, die die Datei je nach dem, was gerendert wird, austauschen können.

## Inhalt

1. [Die Design-Kette](#die-design-kette)
2. [Override-Regeln](#override-regeln)
3. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)
4. [Wohin Ihre Templates gehören](#wohin-ihre-templates-gehören)
5. [Herausfinden, welche Datei gerendert hat](#herausfinden-welche-datei-gerendert-hat)

## Die Design-Kette

Ein Design ist ein Verzeichnis mit Templates, Stylesheets und Bildern. Ein Siteaccess benennt seine Designs in
`site.ini [DesignSettings]`: `SiteDesign`, `AdditionalSiteDesignList[]` und `StandardDesign` (`standard`).
Erweiterungen fügen ihre Design-Verzeichnisse mit `design.ini [ExtensionSettings] DesignExtensions[]` hinzu.
`eZTemplateDesignResource` (`kernel/common/eztemplatedesignresource.php`) baut die Kette auf, zuerst die
Design-Verzeichnisse der Erweiterungen, dann `design/<design>/`; das erste Verzeichnis, das die Datei enthält, gewinnt
([Design- und Override-System](../../standards/architektur/kernel-architektur.md#design--und-überschreibungssystem),
[Designs und die Fallback-Kette](../ersteller/website-ersteller/designs-und-die-fallback-kette.md)).

## Override-Regeln

`override.ini` (die des Siteaccess und die jeder Erweiterung) enthält eine Gruppe pro Regel:

```ini
[node/view/full#ng_article]
Source=node/view/full.tpl
MatchFile=node/view/full/full_ng_article.tpl
Subdir=templates
Match[class_identifier]=ng_article
Priority=1
```

Wenn `node/view/full.tpl` für ein Objekt der Klasse `ng_article` geholt wird, wird stattdessen die Datei
`override/templates/node/view/full/full_ng_article.tpl` aus der Design-Kette verwendet. Die erste Regel, deren
Bedingungen alle zutreffen, gewinnt; ist `Priority` angegeben, werden die Regeln danach sortiert (niedrigere zuerst)
([Ressourcen und Overrides](../template-sprache/template-ressourcen-und-overrides.md#overrides),
[Reihenfolge von Template-Overrides](../../funktionen/6.0/reihenfolge-der-template-ueberschreibungen.md)).

Die Match-Schlüssel setzt derjenige, der rendert: `content/view` setzt die Schlüssel des Knotens (`class_identifier`,
`node`, `parent_node`, `section`, `url_alias`, `depth`, `viewmode`, `state` und weitere); `node_view_gui` setzt sie pro
Element, sodass eine Liste jedes Kind mit dem Zeilen-Template seiner Klasse rendern kann. Eine eigene View kann mit
`eZTemplateDesignResource::instance()->setKeys()` Schlüssel hinzufügen.

## Im Vergleich mit anderen Frameworks

| Idee im Framework | Exponential |
|---|---|
| Template-Pfade, die der Reihe nach durchsucht werden | die Design-Kette |
| Bundle-Templates, überschrieben in `templates/bundles/<Bundle>/` (Symfony), `resources/views/vendor/` (Laravel) | derselbe Dateiname in einem Design weiter vorn in der Kette |
| Theme-Vererbung | `AdditionalSiteDesignList[]` mit Fallback auf `standard` |
| Ein Template pro Inhaltstyp im Controller wählen | eine Override-Regel, die auf `class_identifier` greift |
| Template-Auswahl durch einen View-Resolver (Spring) | die Design-Ressource mit Override-Regeln |
| `{% extends %}` und Blöcke | keine Entsprechung; ganze Templates werden überschrieben, gemeinsame Teile eingebunden |

## Wohin Ihre Templates gehören

| Templates | Ort |
|---|---|
| Die Views Ihres Moduls | `extension/<ext>/design/standard/templates/<ext>/...`, mit `DesignExtensions[]=<ext>` |
| Overrides für das Design einer Site | `extension/<site>/design/<design>/override/templates/...` plus Regeln in `override.ini.append.php` |
| Administrationsseiten | die Administrationsdesigns, nach den Gestaltungsregeln für die Administration ([admin4-Gestaltung](../../bc/6.0/admin4-gestaltung.md)) |

Bearbeiten Sie niemals `design/standard/` oder die Administrationsdesigns des Kernels: Überschreiben Sie stattdessen,
damit Updates Ihre Änderungen nicht überschreiben.

## Herausfinden, welche Datei gerendert hat

- Template-Pfadkommentare geben den Dateinamen rund um die Ausgabe jedes Templates aus
  ([Template-Pfadkommentare](../../funktionen/6.0/template-pfad-kommentare.md)).
- Die Debug-Leiste listet die auf einer Seite verwendeten Templates auf ([Exp-Debug-Leiste](../../funktionen/6.0/exp-debug-leiste.md)).
- Leeren Sie nach dem Hinzufügen einer neuen Template-Datei den Cache `template-override`; sonst bleibt die alte
  Auflösung zwischengespeichert ([Template-Caches](../template-sprache/template-caches.md)).

## Verwandte Seiten

- [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md)
- [Template-Overrides](../ersteller/website-ersteller/template-overrides.md)
- [Leitfaden zu Templates und Design](../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
