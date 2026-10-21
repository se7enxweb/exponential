---
title: Die Design-Kaskade
audience: designer, developer
category: theme-design
order: 30
summary: Die genaue Reihenfolge, in der Exponential Designs und Erweiterungen nach einem Template, Stylesheet oder Bild durchsucht, mit einem ausgearbeiteten Beispiel aus dieser Installation und den Caches, die das Ergebnis halten.
keywords: Design-Kaskade, Rückfall, Design-Rückfall, Design-Basen, Suchreihenfolge, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, DesignLocationCache, Cache design_base, welches Template gewinnt, design cascade, fallback, design fallback, design bases, search order, which template wins
featured: true
type: help
---
# Die Design-Kaskade

Diese Seite ist für Designer, die genau wissen möchten, welche Datei gewinnt, wenn dasselbe Template oder Stylesheet an mehreren Stellen
existiert. Sie beantwortet: in welcher Reihenfolge der Kernel sucht, wie Erweiterungen hineinpassen, wie Sie den Gewinner vorhersagen und
welche Caches sich das Ergebnis merken.

## Inhalt

1. [Die Regel in einem Satz](#die-regel-in-einem-satz)
2. [Schritt 1: die Liste der Designs](#schritt-1-die-liste-der-designs)
3. [Schritt 2: jedes Design in jeder Erweiterung](#schritt-2-jedes-design-in-jeder-erweiterung)
4. [Ein ausgearbeitetes Beispiel](#ein-ausgearbeitetes-beispiel)
5. [Wofür die Kaskade verwendet wird](#wofür-die-kaskade-verwendet-wird)
6. [Overrides kommen vor der Kaskade](#overrides-kommen-vor-der-kaskade)
7. [Die Caches der Kaskade](#die-caches-der-kaskade)
8. [Faustregeln](#faustregeln)

## Die Regel in einem Satz

Der Kernel baut eine geordnete Liste von **Design-Basen** (Ordnern) und nimmt eine Datei aus der **ersten** Basis, die sie hat. Der Code ist
`eZTemplateDesignResource::findDesignBase()` in `kernel/common/eztemplatedesignresource.php`.

## Schritt 1: die Liste der Designs

Die Designnamen kommen aus `site.ini [DesignSettings]` des aktuellen Siteaccess:

1. `SiteDesign`,
2. jeder Eintrag von `AdditionalSiteDesignList[]`, der Reihe nach,
3. `StandardDesign` (Standard `standard`).

Doppelte werden entfernt (`array_unique`), daher schadet es nicht, `standard` auch in `AdditionalSiteDesignList[]` aufzuführen; seine erste
Position zählt.

## Schritt 2: jedes Design in jeder Erweiterung

Für **jeden Designnamen** in dieser Liste fügt der Kernel hinzu:

1. `extension/<extension>/design/<design>` für jede Erweiterung in `design.ini [ExtensionSettings] DesignExtensions[]`, in der Reihenfolge
   dieser Liste, falls der Ordner existiert;
2. dann das eigene `design/<design>` des Kernels, falls es existiert.

Erweiterungen werden also **innerhalb jedes Designnamens** vor dem Kernel durchsucht, und ein weiter oben stehender Designname schlägt immer
einen tieferen, wo auch immer er liegt.

`DesignExtensions[]` füllen die Erweiterungen selbst: Jede Theme-Erweiterung hat eine `settings/design.ini.append.php` mit
`DesignExtensions[]=<its own name>`. Ihre Position in der Liste folgt der Reihenfolge, in der Erweiterungen geladen werden (siehe
[Ladereihenfolge der Erweiterungen](../../../../eng-us/features/6.0/extension-loading-order.md)).

## Ein ausgearbeitetes Beispiel

Der Siteaccess `site` dieser Installation setzt `SiteDesign=media` und `AdditionalSiteDesignList[]` `ezwebin`, `standard`, `base`. Mit den
aktiven Theme-Erweiterungen sehen die Basen für eine Suche so aus (nur existierende Ordner bleiben):

| # | Basis | Warum |
|---|---|---|
| 1 | `extension/sevenx_themes_media/design/media` | Design `media`, Kopie in der Erweiterung |
| 2 | `extension/ezwebin/design/ezwebin` | Design `ezwebin`, Kopie in der Erweiterung |
| 3 | `extension/<ext>/design/standard` für jede Erweiterung, die eines hat (explayouts, ezjscore, ezoe ...) | Design `standard`, Kopien in Erweiterungen |
| 4 | `design/standard` | Design `standard`, Kopie im Kernel |
| 5 | `design/base` | Design `base`, Kopie im Kernel |

Nun das Template `node/view/line.tpl`:

- hat `media` ein `templates/node/view/line.tpl`, gewinnt Basis 1;
- wenn nicht, aber ezwebin es hat, gewinnt Basis 2;
- sonst wird das `design/standard/templates/node/view/line.tpl` des Kernels verwendet.

Dieselbe Suche läuft für `stylesheets/index.css` (über `ezcss_load`) und für `images/logo.png` (über `ezimage`).

## Wofür die Kaskade verwendet wird

| Art von Datei | Ordner in einer Basis | Gesucht von |
|---|---|---|
| Templates | `templates/` | `{include uri='design:...'}`, View-Templates, das Pagelayout |
| Override-Templates | `override/templates/` | `override.ini` `MatchFile=` |
| Stylesheets | `stylesheets/` | `ezcss`, `ezcss_load`, `ezcss_require`, `ezcssfiles` |
| Skripte | `javascript/` | `ezscript`, `ezscript_load`, `ezscript_require`, `ezscriptfiles` |
| Design-Bilder | `images/` | `ezimage` |
| Jede Design-Datei | der Pfad, den Sie angeben | `ezdesign` (zum Beispiel `'stylesheets/print.css'\|ezdesign`) |

## Overrides kommen vor der Kaskade

Wenn ein Template angefordert wird, prüft der Kernel zuerst die Override-Regeln von `override.ini` für dieses Template. Passt eine Regel auf
die aktuelle Seite (Klasse, Knoten, View-Modus ...), wird ihre `MatchFile` verwendet, gesucht unter `override/templates/` über dieselben
Basen. Nur wenn keine Regel passt, wird die einfache Kopie der Kaskade verwendet. Siehe
[Grundlagen der Overrides](../vorlagen/override-grundlagen.md).

## Die Caches der Kaskade

| Cache | Wann er zählt | Wie Sie ihn leeren |
|---|---|---|
| Liste im Speicher pro Anfrage | immer | nichts zu tun |
| Liste der Design-Basen auf der Festplatte | nur mit `site.ini [DesignSettings] DesignLocationCache=enabled` (Standard `disabled`) | `php bin/php/ezcache.php --clear-id=design_base` |
| Template-Override-Cache | nach dem Hinzufügen eines Templates in einem Design oder einer Regel in `override.ini` | `php bin/php/ezcache.php --clear-id=template-override` |
| Kompilierte Templates | nach dem Bearbeiten von Templates, wenn Caches der Template-Kompilierung behalten werden | `--clear-id=template` |
| INI-Cache | nach einer Änderung an `site.ini` oder `design.ini` | `--clear-tag=ini` |

> **Tipp**
> Eine **neue** Template-Datei, die ignoriert zu werden scheint, ist fast immer der Template-Override-Cache. Eine Seite, die direkt nachdem
> Sie Templates hinzugefügt haben mit leerem Inhaltsbereich rendert, hat dieselbe Ursache. Leeren Sie zuerst `template-override`.

Auf dieser Installation leeren Deploys diese Caches über `./console exp:velocity deploy` (Template-, Template-Override-, Übersetzungs- und
design_base-Caches gehören zu seinen Schritten).

## Faustregeln

- **Legen Sie eine Datei nur dann in Ihr Design, wenn sie sich unterscheidet.** Alles andere fällt auf die Designs darunter durch.
- **Bearbeiten Sie nicht `design/standard` oder ein anderes mitgeliefertes Design.** Kopieren Sie das Template an denselben Pfad in Ihr
  eigenes Design und ändern Sie die Kopie. Ein Upgrade überschreibt Ihre Arbeit dann nie.
- **Halten Sie die Rückfallliste kurz und beabsichtigt.** Jedes zusätzliche Design ist ein weiterer durchsuchter Ordner und eine weitere
  Stelle, von der ein überraschendes Template kommen kann.
- **Nennen Sie in Templates den Pfad, nicht das Design.** Schreiben Sie `{include uri='design:parts/menu.tpl'}`, nie einen Pfad mit einem
  Designnamen darin; die Kaskade wählt die richtige Kopie.

## Verwandte Seiten

- [Designs und Siteaccesses](designs-und-siteaccesses.md)
- [Design-Erweiterungen](design-erweiterungen.md)
- [Der Aufbau des Design-Verzeichnisses](aufbau-des-design-verzeichnisses.md)
- [Ein Design debuggen](ein-design-debuggen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
