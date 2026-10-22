---
title: Rich Text
audience: cms-developer
category: cms-development
order: 15
summary: Für CMS-Entwickler, wie Rich Text in Exponential funktioniert. Text wird als XML (ezxmltext) gespeichert, im Online Editor bearbeitet, mit einem Template je Tag als HTML gezeichnet und mit eigenen Tags und Klassen erweitert. Die Seite behandelt auch den Import von HTML.
keywords: Rich Text, ezxmltext, XML Block, XmlText, Online Editor, ezoe, TinyMCE, eigene Tags, Custom Tags, Factbox, Einbettung, Link, Klassen, ezxmltags, Ausgabe-Handler, HTML-Import, vereinfachter Eingabeparser, rich text, custom tags, embed, output handler, HTML import, simplified input parser
type: help
---
# Rich Text

Diese Seite richtet sich an Entwickler, die entscheiden, wie Redakteure lange Texte schreiben und wie diese auf die
Seite gelangen. Rich Text wird in Exponential nicht als HTML gespeichert, sondern als XML mit einem kleinen Satz von
Tags, und jedes Tag wird von einem Template, das Sie steuern, in HTML umgewandelt. Das hält die Texte der Redakteure
über ein Redesign hinweg einheitlich.

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Was Redakteure schreiben können](#was-redakteure-schreiben-können)
3. [Der Online Editor](#der-online-editor)
4. [Von XML zu HTML](#von-xml-zu-html)
5. [Eigene Tags: Ihre eigene Struktur](#eigene-tags-ihre-eigene-struktur)
6. [Klassen, die Redakteure wählen dürfen](#klassen-die-redakteure-wählen-dürfen)
7. [HTML importieren](#html-importieren)
8. [Praktische Regeln](#praktische-regeln)

## Wie es funktioniert

```text
Editor (Online Editor, HTML in the browser)
   -> input handler: HTML is reduced to the allowed XML tags (eZSimplifiedXMLInputParser)
      -> stored XML in the attribute (data_text)
         -> output handler (eZXHTMLXMLOutput): one template per tag
            -> HTML on the page
```

Der Datentyp ist `ezxmltext` (XML Block im Klasseneditor). Seine Handler liegen in
`kernel/classes/datatypes/ezxmltext/handlers/`: der Eingabeparser und Ausgabe-Handler für XHTML, reinen Text und PDF.
Weil die gespeicherte Form Struktur statt Markup ist, kann derselbe Text von jedem Design anders gezeichnet werden, als
reiner Text in einem Feed oder als PDF.

## Was Redakteure schreiben können

Die Tags sind Absätze, Überschriften, `strong` und `emphasize`, Links und Anker, nummerierte und unnummerierte Listen,
Tabellen, `literal`-Blöcke, Zeilenumbrüche, Einbettungen (Block und inline) und eigene Tags; die mitgelieferten
Templates dafür liegen in `design/standard/templates/content/datatype/view/ezxmltags/`. Die Tags mit Einstellungen
(Klassen, Ansichtsmodi, eigene Attribute) haben jeweils einen Block in `settings/content.ini`, etwa `[paragraph]`,
`[header]`, `[table]`, `[link]`, `[embed]` und `[embed-inline]`. Einbettungen und Links auf Inhalte erzeugen
Relationen ([Relationen und Einbettungen](relationen-und-einbettungen.md)).
Skripte, Styles und Inline-Event-Handler haben kein Tag und können deshalb nicht gespeichert werden: Eingefügtes HTML
verliert sie absichtlich.

## Der Online Editor

Redakteure schreiben Rich Text im Online Editor der Erweiterung `ezoe`. Zwei Engines schreiben dasselbe XML: TinyMCE 3,
der Standard, und TinyMCE 8, das ein Siteaccess oder ein einzelner Benutzer einschalten kann (`ezoe.ini
[EditorSettings] EditorEngine=tinymce8`). Einbettungen, Links, eigene Tags, Tabellen und Uploads finden sich in seiner
Werkzeugleiste; Uploads nehmen nur die Dateitypen an, die die Einstellungen erlauben. Details und die Umschaltung für
einen Benutzer: [Der Online Editor und TinyMCE 8](../../anleitungen/inhaltsmodell-und-bearbeitung.md#4-der-online-editor-und-tinymce-8)
und [Online Editor TinyMCE 8](../../funktionen/6.0/online-editor-tinymce8.md).

## Von XML zu HTML

`attribute_view_gui` auf einem `ezxmltext`-Attribut führt den XHTML-Ausgabe-Handler aus, der jedes Tag mit dem
Template `content/datatype/view/ezxmltags/<tag>.tpl` Ihrer Design-Kette zeichnet. Um das Aussehen eines Tags auf einer
Website zu ändern, überschreiben Sie dieses Template in Ihrer Design-Erweiterung, zum Beispiel eine Tabelle in einem
scrollbaren Behälter oder Überschriften mit Ankerlinks. Jedes Tag-Template erhält Inhalt und Attribute des Tags; die
Liste steht unter [Rich-Text-Templates](../designer/vorlagen/rich-text-templates.md).

```smarty
{* in a full view template *}
{if $node.data_map.body.has_content}
    <div class="exp-article__body">{attribute_view_gui attribute=$node.data_map.body}</div>
{/if}
```

Für einen Teaser ist oft der reine Text des XML das Richtige; geben Sie ihn über den Inhalt des Attributs aus,
maskiert, und kürzen Sie ihn mit einem String-Operator ([String-Operatoren](../designer/vorlagen/zeichenketten-operatoren.md)).

## Eigene Tags: Ihre eigene Struktur

Ein eigenes Tag (Custom Tag) ist ein Tag, das Redakteure wie jedes andere einfügen: ein Infokasten, ein Zitat mit
Quelle, ein Handlungsaufruf, ein Video eines Dienstes. Die mitgelieferte Liste ist
`content.ini [CustomTagSettings] AvailableCustomTags[]` (`factbox`, `quote`, `strike`, `sub`, `sup`), mit
`IsInline[<tag>]=true` für Tags, die innerhalb eines Absatzes stehen. So fügen Sie eines in Ihrer Erweiterung hinzu:

```ini
# extension/exp_mysite/settings/content.ini.append.php
<?php /* #?ini charset="utf-8"?

[CustomTagSettings]
AvailableCustomTags[]=exp_callout

[exp_callout]
CustomAttributes[]=title
*/ ?>
```

Dann zeichnet es das Template `content/datatype/view/ezxmltags/exp_callout.tpl` in Ihrem Design, und der Online Editor
bietet es in seinem Dialog für eigene Tags an. Das vollständige Rezept mit Attributen und Regeln zur Barrierefreiheit:
[Templates für eigene Tags](../designer/vorlagen/templates-fuer-custom-tags.md) und das Tutorial
[Ein eigenes Tag von Hand](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/von-hand-custom-tag.md).

Verwenden Sie eigene Tags, statt Redakteure Markup einfügen zu lassen: So bleiben HTML, Styles und Content Security
Policy im Design
([Content Security Policy für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md)).

## Klassen, die Redakteure wählen dürfen

Jeder Tag-Block in `content.ini` hat `AvailableClasses[]`: die CSS-Klassen, die Redakteure für dieses Tag wählen
dürfen (ein Absatz "lead", eine Tabelle "striped"). Deklarieren Sie sie in `content.ini.append.php` Ihrer Erweiterung
und gestalten Sie sie im Stylesheet Ihres Designs. Halten Sie die Liste kurz; jede Klasse ist ein Versprechen, das Ihr
Design einhält.

## HTML importieren

Ein Import muss das gespeicherte XML schreiben, nicht HTML: `fromString()` von `ezxmltext` speichert die Zeichenkette,
wie sie ist. Wandeln Sie HTML zuerst mit dem Eingabeparser des Kernels um, wie das Import-Gerüst zeigt
([Import- und Exportwerkzeuge, Abschnitt 4](../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)):
Reduzieren Sie das HTML auf die vereinfachte Eingabe (Absätze, fett, kursiv, Links, Überschriften, Listen, Tabellen),
parsen Sie es und speichern Sie das Ergebnis. Bilder und Links im HTML werden in einem zweiten Durchgang zu
Einbettungen und Links auf Inhalte, sobald die Ziele existieren ([Inhalte importieren](inhalte-importieren.md)).

Wenn Sie von einer Plattform kommen, die Rich Text als DocBook speichert: siehe
[Umstieg von eZ Platform oder Ibexa](umstieg-von-ez-platform-oder-ibexa.md#feldtypen-und-datentypen).

## Praktische Regeln

- Verwenden Sie `ezxmltext` für jeden Text, der länger als ein Satz ist und einen Link oder eine Hervorhebung brauchen
  könnte; verwenden Sie `eztext` für reinen Text.
- Überschreiben Sie Tag-Templates in Ihrem Design; ändern Sie nie die mitgelieferten Templates in `design/standard`.
- Fügen Sie eigene Tags für wiederkehrende Strukturen hinzu, statt Redakteure um Markup zu bitten.
- Maskieren Sie nichts, was aus `attribute_view_gui` kommt: Der Ausgabe-Handler maskiert Text bereits; maskieren Sie
  alles, was Sie selbst ausgeben, mit `|wash()`.

## Verwandte Seiten

- [Datentypen wählen](datentypen-waehlen.md)
- [Rich Text: zusätzliche Unicode-Zeichen und TeX-Quelltext](../../funktionen/6.0/rich-text-zusatzzeichen-und-tex-quelltext.md)
- [Attribute view gui](../designer/vorlagen/attribute-view-gui.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
