---
title: Die persistent variable
audience: designer, developer
category: templates
order: 650
summary: Werte mit persistent_variable aus einem View-Template an das Pagelayout übergeben - sie als Hash setzen, sie aus $module_result.content_info.persistent_variable lesen, warum sie den View-Cache übersteht, ezpagedata_set als Helfer und Beispiele (eine Spalte ausblenden, Klasse am body, Seitentitel).
keywords: persistent_variable, von der View zum Pagelayout, content_info, ezpagedata_set, Variable an das Pagelayout übergeben, linkes Menü ausblenden, Klasse am body aus der View, sicher für den View-Cache, view to pagelayout, pass variable to pagelayout, hide left menu, body class from view, view cache safe
type: help
---
# Die persistent variable

Diese Seite ist für Designer, deren Inhalts-Template den Rahmen beeinflussen muss: eine Seitenspalte auf einer Landingpage
ausblenden, für eine Klasse eine Klasse am body hinzufügen, den Seitentitel ändern. Das View-Template läuft vor dem Pagelayout
und kann es nicht direkt erreichen; die **persistent variable** überbringt die Nachricht.

## Inhalt

1. [Sie setzen](#sie-setzen)
2. [Sie lesen](#sie-lesen)
3. [Warum „persistent“](#warum-persistent)
4. [Helfer](#helfer)
5. [Beispiele in den mitgelieferten Designs](#beispiele-in-den-mitgelieferten-designs)

## Sie setzen

Setzen Sie in einer Vollansicht (oder einem Override davon) die Template-Variable `persistent_variable` im Wurzel-Gültigkeitsbereich
auf einen Hash:

```tpl
{set scope=global persistent_variable=hash( 'body_class', 'landing', 'left_menu', false() )}
```

Die Inhaltsansicht liest sie nach dem Rendern (`kernel/classes/eznodeviewfunctions.php`: Der Kernel setzt sie vor dem Lauf der
View auf `false` und kopiert jeden anderen Wert in `content_info`).

## Sie lesen

Im Pagelayout:

```tpl
{def $pv = cond( and( is_array( $module_result.content_info ), is_set( $module_result.content_info.persistent_variable ) ),
                 $module_result.content_info.persistent_variable, false() )}
<body class="{if and( $pv, is_set( $pv.body_class ) )}{$pv.body_class|wash}{/if}">
```

## Warum „persistent“

Der Wert wird mit dem Eintrag im View-Cache gespeichert (der Kernel fügt ihn den Cache-Schlüsseln hinzu), sodass eine Seite,
die aus dem View-Cache kommt, denselben Wert liefert wie beim Rendern. Eine auf andere Weise gesetzte Variable ginge bei einem
Cache-Treffer verloren.

## Helfer

- `ezcss_require` und `ezscript_require` verwenden die persistent variable, um Listen von Stylesheets und Skripten zu übergeben.
- `ezpagedata_set( 'key', value )` und `ezpagedata_append` der Erweiterung ezwebin schreiben hinein, und `ezpagedata()` liest
  sie zurück ([Das Design ezwebin als Grundlage](../../../../eng-us/help/designers/theme-design/ezwebin-as-a-base.md)).

## Beispiele in den mitgelieferten Designs

Das Pagelayout von admin4 liest `persistent_variable.left_menu` und `extra_menu`, sodass das Template einer
Bearbeitungsseite eine eigene linke Spalte verlangen kann (`design/admin4/templates/pagelayout.tpl`).

## Verwandte Seiten

- [Module result und content info](module-result-und-content-info.md)
- [Vollansicht](vollansicht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
