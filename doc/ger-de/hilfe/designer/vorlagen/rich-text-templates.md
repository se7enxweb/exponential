---
title: Rich-Text-Templates
audience: designer, developer
category: templates
order: 630
summary: Wie Rich Text (der Datentyp XML-Text) über ein Template pro Tag in content/datatype/view/ezxmltags zu HTML wird - paragraph, header, link, table, Listen, embed, custom; die Variablen, die Tag-Templates erhalten; Klassen, die Redakteure wählen können; und das Überschreiben eines Tags für ein Design.
keywords: Rich Text, ezxmltext, ezxmltags, paragraph.tpl, header.tpl, link.tpl, table.tpl, Tag-Templates, AvailableClasses, Rich-Text-Markup, Klassen für Redakteure, output_text, rich text, tag templates, rich text markup, editor classes
type: help
---
# Rich-Text-Templates

Diese Seite ist für Designer, die das Markup von Rich Text steuern: Fließtexte, Einleitungen, Beschreibungen. Rich Text wird
als XML gespeichert; beim Anzeigen wird jedes Tag von einem eigenen Template in `content/datatype/view/ezxmltags/` dargestellt
(`TemplatesPath` in `kernel/classes/datatypes/ezxmltext/ezxmloutputhandler.php`).

## Inhalt

1. [Die Tag-Templates](#die-tag-templates)
2. [Was ein Tag-Template erhält](#was-ein-tag-template-erhält)
3. [Ein Tag für Ihr Design ändern](#ein-tag-für-ihr-design-ändern)
4. [Klassen, die Redakteure wählen können](#klassen-die-redakteure-wählen-können)
5. [Überschriften](#überschriften)

## Die Tag-Templates

`design/standard/templates/content/datatype/view/ezxmltags/` hat unter anderem: `paragraph.tpl`, `header.tpl`, `strong.tpl`,
`emphasize.tpl`, `link.tpl`, `anchor.tpl`, `ul.tpl`, `ol.tpl`, `li.tpl`, `table.tpl`, `tr.tpl`, `td.tpl`, `th.tpl`,
`literal.tpl`, `line.tpl`, `embed.tpl`, `embed-inline.tpl` und die Custom Tags `factbox.tpl`, `quote.tpl`, `strike.tpl`,
`sub.tpl`, `sup.tpl`.

## Was ein Tag-Template erhält

- `$content`: das schon gerenderte innere HTML (ohne `wash` ausgeben).
- `$classification`: die Klasse, die der Redakteur gewählt hat.
- `$align`, wo das Tag eine Ausrichtung unterstützt.
- Tag-spezifische Werte: `$href`, `$title`, `$target` bei Links; `$level` bei Überschriften; eigene Attribute bei Custom Tags.

Das mitgelieferte `paragraph.tpl`:

```tpl
{set $classification = cond( and( is_set( $align ), $align ), concat( $classification, ' text-', $align ), $classification )}
<p{if $classification|trim} class="{$classification|wash}"{/if}>
{if eq( $content|trim(), '' )}&nbsp;{else}{$content}{/if}
</p>
```

## Ein Tag für Ihr Design ändern

Kopieren Sie das Tag-Template an denselben Pfad in Ihr Design oder verwenden Sie einen Override auf
`Source=content/datatype/view/ezxmltags/<tag>.tpl` (mit `Match[class_identifier]`, um ihn auf eine Klasse zu beschränken). Zum
Beispiel eine Tabelle in einem Scroll-Container:

```tpl
{* design/mytheme/templates/content/datatype/view/ezxmltags/table.tpl *}
<div class="table-scroll">
<table{if $classification|trim} class="{$classification|wash}"{/if}>
{$rows}
</table>
</div>
```

(Lesen Sie zuerst das Original: Jedes Tag erhält seine eigenen Variablen; das Tabellen-Template erhält die gerenderten Zeilen.)

Das `factbox.tpl` des Kernels verwendet noch das alte Attribut `bgcolor`, und `table.tpl` gibt ein `style="width: ..."` aus, wenn
ein Redakteur eine Tabellenbreite gesetzt hat: Ein Design für eine Website mit strikter Content Security Policy
(`style-src 'self'`) überschreibt beide mit Elementen mit Klassen.

## Klassen, die Redakteure wählen können

`content.ini [<tag>] AvailableClasses[]` listet die Klassen, die Redakteure im Online-Editor für ein Tag wählen dürfen, zum
Beispiel:

```ini
[paragraph]
AvailableClasses[]=lead
AvailableClasses[]=note
```

Gestalten Sie jede Klasse in Ihrem Stylesheet; das Absatz-Template setzt sie in `class=""`.

## Überschriften

`header.tpl` ordnet Ebenen des Rich Text HTML zu: Ebene 1 wird zu `h2`, Ebene 2 zu `h3` ... (die eigene `h1` der Vollansicht ist
der Seitentitel). Es gibt auch Anker von `eztoc` für Inhaltsverzeichnisse aus. Behalten Sie diese Zuordnung, sonst bekommt die
Seite zwei `h1`.

## Verwandte Seiten

- [Templates für Custom Tags](templates-fuer-custom-tags.md)
- [Embed-Ansicht](embed-ansicht.md)
- [Ansichtstemplates von Datentypen](datentyp-ansichtstemplates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
