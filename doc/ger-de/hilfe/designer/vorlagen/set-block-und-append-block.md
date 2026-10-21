---
title: Set-block und append-block
audience: designer, developer
category: templates
order: 120
summary: Gerenderte Ausgabe mit set-block in einer Variablen auffangen, mit append-block mehrere Stücke in einer Liste sammeln, der Parameter scope und typische Verwendungen (einen Teil einmal rendern und zweimal ausgeben, Mail-Betreffs, cache_ttl).
keywords: set-block, append-block, Ausgabe auffangen, Template-Variable aus Ausgabe, scope global, scope root, Mail-Betreff, einmal rendern, capture output, template variable from output, mail subject, render once
type: help
---
# Set-block und append-block

Diese Seite ist für Designer, die gerendertes Markup in einer Variablen statt in der Ausgabe brauchen.

## Inhalt

1. [set-block](#set-block)
2. [append-block](#append-block)
3. [Scope](#scope)
4. [Verwendungen in den mitgelieferten Templates](#verwendungen-in-den-mitgelieferten-templates)

## set-block

Rendert seinen Inhalt und speichert den Text in einer Variablen (`eZTemplateBlockFunction`):

```tpl
{set-block variable=$share_bar}
    {include uri='design:parts/share.tpl' node=$node}
{/set-block}
<header>{$share_bar}</header>
...
<footer>{$share_bar}</footer>
```

Der Teil wird einmal gerendert und zweimal ausgegeben. Das Pagelayout von admin4l verwendet das, um Zonen des Admin-Layouts in
Variablen zu rendern (`{set-block variable=$header_html}...{/set-block}`) und sie später zu platzieren.

## append-block

Fügt jeden Inhalt einer Liste hinzu:

```tpl
{append-block scope=global variable=page_notes}
    <li>{'Updated today'|i18n( 'design/mytheme' )}</li>
{/append-block}
```

## Scope

`scope=global` und `scope=root` schreiben die Variable außerhalb des aktuellen Templates, sodass ein View-Template dem Code, der
es aufgerufen hat, einen Wert übergeben kann. Mail-Templates verwenden das für den Betreff:

```tpl
{set-block scope=root variable=subject}{'Your order'|i18n( 'design/mytheme/mail' )}{/set-block}
```

und Vollansichten für die Lebensdauer ihres View-Cache:

```tpl
{set-block scope=root variable=cache_ttl}400{/set-block}
```

(der Veranstaltungskalender von ezwebin). Für Werte, die das Pagelayout braucht, ziehen Sie die persistent variable vor
([Die persistent variable](die-persistent-variable.md)).

## Verwendungen in den mitgelieferten Templates

- `cache_ttl` in Vollansichten (`full/event_calendar.tpl`, `full/multicalendar.tpl` von ezwebin).
- Teile einmal rendern im Pagelayout von admin4l.
- Mail-Betreffs und -Kopfzeilen in Mail-Templates.

## Verwandte Seiten

- [Variablen und def](variablen-und-def.md)
- [Caching für Designer](../theme-design/caching-fuer-designer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
