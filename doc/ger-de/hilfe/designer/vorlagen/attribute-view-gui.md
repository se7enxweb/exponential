---
title: attribute_view_gui
audience: designer, developer
category: templates
order: 450
summary: Ein Feld eines Inhalts mit attribute_view_gui zeichnen - wie es das Datentyp-Template wählt, zusätzliche Parameter wie image_class, Override-Schlüssel (attribute_identifier, class_identifier), um ein Feld einer Klasse anders zu gestalten, und Felder des Information Collectors.
keywords: attribute_view_gui, Datentyp-Template, Feldausgabe, image_class, content/datatype/view, Override eines Attributs, attribute_identifier, collect, Information Collector, Feld-Template, datatype template, field output, override attribute, information collector, field template
type: help
---
# attribute_view_gui

Diese Seite ist für Designer, die Felder von Inhalten ausgeben. `{attribute_view_gui}` übergibt ein Feld an das Template
seines Datentyps, das weiß, wie es gezeichnet wird: Rich Text als HTML, ein Bild in einem Alias, ein Datum im Format der
Locale.

## Inhalt

1. [Grundlegende Verwendung](#grundlegende-verwendung)
2. [Welches Template es verwendet](#welches-template-es-verwendet)
3. [Zusätzliche Parameter](#zusätzliche-parameter)
4. [Ein Feld mit einem Override anders gestalten](#ein-feld-mit-einem-override-anders-gestalten)
5. [Felder des Information Collectors](#felder-des-information-collectors)

## Grundlegende Verwendung

```tpl
{attribute_view_gui attribute=$node.data_map.body}
{attribute_view_gui attribute=$node.data_map.image image_class='medium'}
```

Prüfen Sie optionale Felder zuerst: `{if $node.data_map.image.has_content}...{/if}`.

## Welches Template es verwendet

Die Funktion ist in `kernel/common/eztemplateautoload.php` definiert (`eZObjectForwardInit`). Sie nimmt den
Template-Ordner `content/datatype/view/` und den Namen des View-Templates des Felds, also seinen Datentyp: Eine Textzeile
verwendet `content/datatype/view/ezstring.tpl`, Rich Text `ezxmltext.tpl`, ein Bild `ezimage.tpl` (der Ordner listet alle).
Wie jedes Template werden sie über die Design-Kaskade gefunden, sodass Ihr Design das Template für einen Datentyp ersetzen
kann.

## Zusätzliche Parameter

Jeder andere Parameter wird an das Datentyp-Template übergeben. Das `ezimage.tpl` des Kernels dokumentiert oben seine
eigenen: `image_class` (der Alias, Standard `large`), `css_class`, `alignment`, `link_to_image`, `href`, `alt_text`, `title`
und mehr. Lesen Sie den Kopf eines Datentyp-Templates, um zu sehen, was es annimmt.

## Ein Feld mit einem Override anders gestalten

Die Funktion setzt Override-Schlüssel, auf die Sie abgleichen können: `attribute_identifier`, `attribute` (die ID des
Klassenattributs), `class_identifier`, `class`. Um das Feld `image` von Artikeln anders zu zeichnen:

```ini
[mytheme_article_image]
Source=content/datatype/view/ezimage.tpl
MatchFile=datatype/article_image.tpl
Subdir=templates
Match[class_identifier]=article
Match[attribute_identifier]=image
```

Das Override-Template erhält `$attribute` wie das Original ([Override-Grundlagen](override-grundlagen.md)).

## Felder des Information Collectors

Ein Feld, das als Information Collector gekennzeichnet ist (ein Feedback-Formular), wird aus `content/datatype/collect/`
statt aus `view/` gezeichnet, sodass es als Eingabe dargestellt wird. Siehe [Formulare in Templates](formulare-in-templates.md).

## Verwandte Seiten

- [Datentyp-Ansichtstemplates](datentyp-ansichtstemplates.md)
- [Knoten, Objekte und Attribute](knoten-objekte-und-attribute.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
