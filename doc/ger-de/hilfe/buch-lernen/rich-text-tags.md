---
title: "Rich-Text-Tags"
audience: editor, builder, developer
category: books
order: 270
summary: Die XML-Tags des Rich-Text-Datentyps (Überschriften, Listen, Hervorhebung, literal, Links, Anker, Tabellen, eingebettete Objekte, eigene Tags), der Parameter class und wie der Online-Editor und die Templates mit ihnen zusammenhängen.
keywords: XML-Text, Tags, Überschrift, Absatz, Link, Anker, Tabelle, Objekt, Einbettung, eigenes Tag, Parameter class, Online-Editor, Buch Learning, ezxmltext, XML text, tags, header, paragraph, link, anchor, table, object, embed, custom tag, class parameter, online editor, Learning book
type: help
---
# Rich-Text-Tags

Der Datentyp `ezxmltext` speichert strukturierte Textblöcke als XML. Redakteure sehen das XML selten, weil der
Online-Editor es schreibt, aber ein Builder braucht das Vokabular: Es ist das, was Templates in HTML verwandeln, und das,
was ein Import oder ein Skript schreibt. Die Tags, die der Kernel akzeptiert, sind in
`kernel/classes/datatypes/ezxmltext/ezxmlschema.php` aufgeführt; die Menge ist die, die das Buch beschreibt.

Jedes Tag kann einen Parameter `class` erhalten. Er wird normalerweise im Template des Tags verwendet, um eine CSS-Klasse
an das HTML zu setzen, aber das Template kann mehr damit tun (das Beispiel des Buchs: eine Überschrift der Klasse
`chapter`, die anders dargestellt wird).

## Die Tags

| Tag | Verwendung |
|---|---|
| `section` | Der Container des ganzen Texts; Sektionen können verschachtelt werden. |
| `header` | Eine Überschrift. Parameter `level` (1 bis 6) und `class`. |
| `paragraph` | Ein Absatz. |
| `ul`, `ol`, `li` | Ungeordnete und geordnete Listen. |
| `strong` | Fett. |
| `emphasize` | Hervorhebung, meist kursiv. |
| `literal` | Unformatierter Text: Quellcode, HTML oder XML, so gezeigt, wie es geschrieben ist, und ohne Interpretation dargestellt. |
| `link` | Ein Hyperlink. Nimmt `href` (oder die ID eines gespeicherten Links), `target` und mehr. |
| `anchor` | Eine benannte Stelle im Text, auf die ein Link zeigen kann. Nimmt `name`. |
| `table`, `tr`, `th`, `td` | Eine Tabelle. Parameter wie `border` und `width` an der Tabelle; `width`, `colspan`, `rowspan` an Zellen. |
| `embed`, `embed-inline` | Ein Objekt der Website (ein Bild, eine Datei, ein Artikel), in den Text gesetzt, als Block oder innerhalb einer Zeile. Parameter: `object_id` oder `node_id`, `view`, `align`, `class`. |
| `custom` | Ein Tag, das Sie selbst unter einem Namen definieren. Braucht ein Template. |
| `line` | Eine Zeile innerhalb einer Überschrift. |

Das Buch nennt außerdem alternative Schreibweisen (`b`, `bold`, `i`, `em`, `p`, `a`, `h` und weitere); der
Eingabeparser akzeptiert die vertrauten HTML-Namen und speichert die Tags aus der Tabelle oben. Das Tag `object` des
Buchs ist das heutige `embed`.

## Links werden einmal gespeichert

Jeder Hyperlink in einem XML-Text wird in einer eigenen Linktabelle mit der Adresse gespeichert. Das macht die Linkprüfung
möglich ([Links, URL-Aliase und Platzhalter](../../anleitungen/urls-und-aliase.md)): Dieselbe Adresse in vielen Texten ist
eine Zeile, und eine Änderung der Adresse lässt sich an einer Stelle vornehmen. Ein Link auf ein Objekt der Website wird
über die Objekt- oder Knoten-ID gespeichert und übersteht so ein Umbenennen oder Verschieben.

## Eingebettete Objekte

Ein eingebettetes Objekt zeigt ein anderes Objekt im Text, mit der **Ansicht**, die Sie nennen (`embed` für einen Block
oder `embed-inline` für eine Einbettung in der Zeile). Das Template für diese Ansicht und Klasse entscheidet, was
erscheint: ein Bild in einer Größe, eine Datei als Download-Link. Siehe [Ein anderes Objekt einbetten](../handbuch/wie-bette-ich-ein-anderes-objekt-ein.md).

## Eigene Tags

Eigene Tags tragen einen Namen (`<custom name="quote">`), und jedes hat ein Template unter `design/<design>/templates/content/datatype/view/ezxmltags/`
(zum Beispiel ein Tag „quote“ oder „factbox“). Ihre Namen stehen in `content.ini` (`[CustomTagSettings] AvailableCustomTags[]`)
und werden Redakteuren im Online-Editor zur Verfügung gestellt ([Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md#4-der-online-editor-und-tinymce-8)).

## Im Editor

Ein Redakteur tippt keine Tags. Der Online-Editor bietet Formatierungsschaltflächen, die die richtigen Tags schreiben,
und die Administration kann das Ergebnis zeigen. Um das XML selbst zu sehen, liest ein Entwickler
`$attribute.content.xml_data`; siehe [Zugriff auf Attribute](../template-sprache/zugriff-auf-attribute.md).

Weiter: [Objekte anlegen und bearbeiten](objekte-anlegen-und-bearbeiten.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
