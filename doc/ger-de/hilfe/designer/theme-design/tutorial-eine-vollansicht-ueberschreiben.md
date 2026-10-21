---
title: "Tutorial: eine Vollansicht überschreiben"
audience: designer
category: theme-design
order: 780
summary: Schritt für Schritt einer Inhaltsklasse ein eigenes Template für die volle Seite in Ihrem Design geben: Bezeichner und Felder der Klasse finden, die Override-Regel schreiben, das Template mit den Feldern schreiben, prüfen, welches Template verwendet wird, und die richtigen Caches leeren.
keywords: Tutorial, Vollansicht überschreiben, Template der Vollansicht, Klassen-Template, override.ini, node/view/full.tpl, MatchFile, class_identifier, Artikel-Template, eigenes Seiten-Template, tutorial, override full view, full view template, class template, article template, custom page template
type: help
---
# Tutorial: eine Vollansicht überschreiben

Dieses Tutorial ist für Designer, die möchten, dass eine Art von Seite, etwa Veranstaltungen, anders aussieht als der Standard. Sie fügen in
Ihrem Design eine Override-Regel und ein Template hinzu. Planen Sie 30 Minuten ein. Es setzt eine eigene Design-Erweiterung voraus
([Tutorial: eine Theme-Erweiterung anlegen](tutorial-eine-theme-erweiterung-anlegen.md)).

## Inhalt

1. [Schritt 1: die Klasse und ihre Felder finden](#schritt-1-die-klasse-und-ihre-felder-finden)
2. [Schritt 2: die Override-Regel](#schritt-2-die-override-regel)
3. [Schritt 3: das Template](#schritt-3-das-template)
4. [Schritt 4: Caches leeren und ansehen](#schritt-4-caches-leeren-und-ansehen)
5. [Schritt 5: verfeinern](#schritt-5-verfeinern)

## Schritt 1: die Klasse und ihre Felder finden

Öffnen Sie in der Administration unter **Setup > Klassen** die Klasse (zum Beispiel „Event“). Notieren Sie ihren **Bezeichner** (`event`) und
die Bezeichner der Felder, die Sie zeigen möchten (`title`, `short_description`, `text`, `from_time`, `image` ...). Templates verwenden
Bezeichner, nicht Namen.

## Schritt 2: die Override-Regel

In `settings/siteaccess/<your siteaccess>/override.ini.append.php` Ihrer Erweiterung (oder `settings/override.ini.append.php` für jeden
Siteaccess mit der Erweiterung):

```ini
<?php /* #?ini charset="utf-8"?
[mytheme_full_event]
Source=node/view/full.tpl
MatchFile=full/event.tpl
Subdir=templates
Match[class_identifier]=event
*/ ?>
```

- `Source`: das Template, das ersetzt wird: die Vollansicht jedes Knotens.
- `MatchFile`: Ihr Template, relativ zu `override/templates/` eines Designs.
- `Match[...]`: die Bedingung; hier die Klasse.
- Der Abschnittsname muss über alle Override-Dateien hinweg eindeutig sein: Stellen Sie ihm Ihr Theme voran.

## Schritt 3: das Template

`design/mytheme/override/templates/full/event.tpl`:

```tpl
<article class="event">
    <header class="event-header">
        <h1>{$node.name|wash}</h1>
        {if $node.data_map.from_time.has_content}
            <p class="event-date">
                <time datetime="{$node.data_map.from_time.content.timestamp|datetime( 'custom', '%Y-%m-%dT%H:%i' )}">
                    {$node.data_map.from_time.content.timestamp|l10n( 'shortdatetime' )}
                </time>
            </p>
        {/if}
    </header>
    {if $node.data_map.image.has_content}
        {attribute_view_gui attribute=$node.data_map.image image_class='large'}
    {/if}
    {if $node.data_map.text.has_content}
        <div class="event-text">{attribute_view_gui attribute=$node.data_map.text}</div>
    {/if}
</article>
```

Das Vollansichts-Template erhält `$node` (den gezeigten Knoten), `$view_parameters` und `$language_code` ([Vollansicht](../vorlagen/vollansicht.md)).
Prüfen Sie `has_content`, bevor Sie optionale Felder ausgeben.

## Schritt 4: Caches leeren und ansehen

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-id=template-override,content
```

Öffnen Sie eine Veranstaltung. Mit `[TemplateSettings] ShowTemplatePathComments=enabled` auf Ihrem Test-Siteaccess zeigt der
Seitenquelltext `override/templates/full/event.tpl` um den Inhalt. Wird noch das Standard-Template verwendet, siehe
[Ein Design debuggen](ein-design-debuggen.md), „Mein neues Template oder mein Override wird ignoriert“.

## Schritt 5: verfeinern

- Fügen Sie mit `{ezcss_require( array( 'event.css' ) )}` oben im Template ein Stylesheet nur für Veranstaltungen hinzu.
- Fügen Sie auf dieselbe Weise die line-Ansicht für Veranstaltungslisten hinzu (`Source=node/view/line.tpl`, `MatchFile=line/event.tpl`).
- Prüfen Sie die Seite bei 375 px, im dunklen Modus und mit der Tastatur.

## Verwandte Seiten

- [Grundlagen der Overrides](../vorlagen/override-grundlagen.md)
- [Knoten-Templates nach Klasse](../vorlagen/knoten-templates-nach-klasse.md)
- [View-Modi](../vorlagen/ansichtsmodi.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
