---
title: Template-Pfadkommentare
audience: designer, developer
category: templates
order: 750
summary: Im Quelltext der Seite sehen, welches Template welches Markup geschrieben hat - ShowTemplatePathComments einschalten, die Kommentare START und STOP und die Override-Quelle in Klammern lesen, der Schalter in der Exp-Debugleiste und warum es in Produktion aus bleiben muss.
keywords: Template-Pfadkommentare, ShowTemplatePathComments, Kommentare START STOP, welches Template, Override-Quelle, Templates debuggen, Quelltext der Seite, Debugleiste, template path comments, START STOP comments, which template, override source, debug templates, page source, debug bar
type: help
---
# Template-Pfadkommentare

Diese Seite ist für Designer, die eine Seite ansehen und das Template hinter einem Teil davon brauchen. Mit eingeschalteten
Template-Pfadkommentaren wird jedes gerenderte Template von zwei HTML-Kommentaren umschlossen, die seine Datei nennen. Die
Funktionsseite ist [Template-Pfadkommentare](../../../funktionen/6.0/template-pfad-kommentare.md).

## Inhalt

1. [Einschalten](#einschalten)
2. [Lesen](#lesen)
3. [Vorsicht](#vorsicht)

## Einschalten

Auf einem Test-Siteaccess:

```ini
# settings/siteaccess/<test siteaccess>/site.ini.append.php
[TemplateSettings]
ShowTemplatePathComments=enabled
```

und dann den Template-Cache leeren (`php bin/php/ezcache.php --clear-tag=template`), weil kompilierte Templates die Prüfung
tragen. Die [Exp-Debugleiste](../../../funktionen/6.0/exp-debug-leiste.md) kann dieselbe Einstellung umschalten.

## Lesen

```html
<!-- START design/media/templates/pagelayout/footer.tpl -->
...the footer markup...
<!-- STOP design/media/templates/pagelayout/footer.tpl -->
```

Hat ein Override geantwortet, wird das angeforderte Template in Klammern genannt:

```html
<!-- START design/media/templates/node/view/full/article.tpl (node/view/full.tpl) -->
```

„Die Datei `article.tpl` hat die Anfrage `node/view/full.tpl` beantwortet.“ Im Elementinspektor des Browsers erscheinen die
Kommentare um die Elemente, sodass Sie von jedem Element zu seinem Template hinaufsteigen können.

## Vorsicht

- Nur für die Entwicklung: Die Kommentare fügen jedem Template Markup hinzu, zerstören JSON-, XML- und RSS-Ausgaben und verraten
  Dateipfade. Lassen Sie die Einstellung auf Live-Siteaccesses auf `disabled` (ihr Kommentar in `settings/site.ini` sagt
  dasselbe).
- Gecachte Ausgabe behält die Kommentare, mit denen sie gerendert wurde: Leeren Sie nach dem Umschalten Inhalts- und
  Template-Caches.

## Verwandte Seiten

- [Template-Debugging](template-debugging.md)
- [Ein Design debuggen](../theme-design/ein-design-debuggen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
