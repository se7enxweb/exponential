---
title: Switch und case
audience: designer, developer
category: templates
order: 90
summary: Mit switch und case zwischen mehreren Ausgaben wählen - einen Wert abgleichen, mit in einen von mehreren Werten abgleichen, der Standardfall, warum Bedingungen in if gehören und wann if oder ein Override das bessere Werkzeug ist.
keywords: switch, case, match, in, process, Standardfall, mehrere Auswahlmöglichkeiten, Template-Switch, Klassen-Switch, default case, multiple choices, template switch, class switch
type: help
---
# Switch und case

Diese Seite ist für Designer, die je nach einem Wert eine von mehreren Ausgaben erzeugen.

## Inhalt

1. [Syntax](#syntax)
2. [Mehrere Werte abgleichen](#mehrere-werte-abgleichen)
3. [Eine Bedingung](#eine-bedingung)
4. [Wann Sie es nicht verwenden](#wann-sie-es-nicht-verwenden)

## Syntax

Aus `lib/eztemplate/classes/eztemplateswitchfunction.php`:

```tpl
{switch match=$node.class_identifier}
{case match='article'}
    <span class="badge">{'Article'|i18n( 'design/mytheme' )}</span>
{/case}
{case match='event'}
    <span class="badge">{'Event'|i18n( 'design/mytheme' )}</span>
{/case}
{case}
    {* no match: the case without a match is the default *}
{/case}
{/switch}
```

Das Titel-Template des Designs media verwendet ein switch auf Fehlertyp und Fehlernummer, um Fehlerseiten zu benennen.

## Mehrere Werte abgleichen

```tpl
{case in=array( 'article', 'blog_post' )}
    ...
{/case}
```

## Eine Bedingung

`{case}` vergleicht nur Werte (`match` oder `in`). Ein Parameter `process=` erscheint im Kommentar der Klasse als TODO und ist
nicht umgesetzt. Für einen Bereich oder jede andere Bedingung verwenden Sie `{if}`:

```tpl
{if gt( $children|count, 5 )}
    ...
{else}
    ...
{/if}
```

## Wann Sie es nicht verwenden

- Markup pro Inhaltsklasse gehört in Override-Templates, nicht in ein großes switch ([Knoten-Templates nach Klasse](knoten-templates-nach-klasse.md)).
- Zwei Zweige sind mit `{if}` klarer.

## Verwandte Seiten

- [If und Bedingungen](if-und-bedingungen.md)
- [Override-Grundlagen](override-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
