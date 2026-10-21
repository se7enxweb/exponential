---
title: eZTemplate aus PHP
audience: developer
category: extension-development
order: 120
summary: Templates mit eZTemplate aus PHP rendern - factory(), setVariable(), fetch('design:...'), hasErrors() - in Modul-Views, Cronjobs und E-Mails, und wie Template-Operatoren und -Funktionen registriert werden.
keywords: Template, rendern, Template aus PHP, Design-Kette, Template-Variablen, Fehler, eZTemplate, factory, setVariable, hasVariable, variable, fetch, design resource, file resource, hasErrors, errorLog, render, template from PHP, eztemplateautoload
type: help
---
# eZTemplate aus PHP

Teil 8, Referenz 12. Modul-Views, E-Mails und manche Cronjobs rendern Templates aus PHP. `eZTemplate`
(`lib/eztemplate/classes/eztemplate.php`) ist die Template-Engine. Diese Seite behandelt ihre Verwendung; wie Sie
Operatoren und Funktionen für sie schreiben, steht in [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md)
und [Template-Funktionen](../teil-3-erweiterungsentwickler/template-funktionen.md).

## Die üblichen drei Zeilen

```php
<?php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'items', $items );
$html = $tpl->fetch( 'design:myext/list.tpl' );
```

| Methode | Verwendung |
|---|---|
| `eZTemplate::factory()` (statisch) | die Template-Engine, eingerichtet mit den Einstellungen, Operatoren und Funktionen der Website |
| `setVariable( $var, $val, $namespace = '', $scopeSafe = false )` | einen Wert im Template als `$var` verfügbar machen |
| `hasVariable( $var, $namespace = '', $attrs = null )`, `variable( $var, ... )` | Variablen zurücklesen |
| `fetch( $template = false, $extraParameters = false, $returnResourceData = false )` | rendern und die Ausgabe zurückgeben |
| `hasErrors()`, `errorLog()` | ob beim Rendern Fehler gemeldet wurden, und welche |
| `resetVariables()` | die Variablen leeren, bevor Sie mit derselben Engine ein anderes Template rendern |

## Ressourcen

Der Template-Name hat ein Ressourcen-Präfix:

| Präfix | Findet das Template |
|---|---|
| `design:` | über die Design-Kette, mit Override-Regeln: `design:myext/list.tpl` |
| `file:` | unter einem Dateipfad relativ zum Wurzelverzeichnis der Installation |

Verwenden Sie für die Templates Ihrer Erweiterung immer `design:`, damit Designs und Overrides sie ersetzen können
([Die Design-Kette](../teil-2-builder/die-design-kette.md)).

## In einer Modul-View

```php
<?php
$Module = $Params['Module'];
$tpl = eZTemplate::factory();
$tpl->setVariable( 'items', myExtItem::fetchByParent( 2 ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:myext/list.tpl' );
$Result['path'] = array( array( 'url' => false, 'text' => ezpI18n::tr( 'extension/myext', 'Items' ) ) );
```

Der Kernel zeichnet `$Result['content']` dann innerhalb des Pagelayouts
([View-Ergebnisse, der Pfad und das Pagelayout](../teil-3-erweiterungsentwickler/view-ergebnisse-und-das-pagelayout.md)).

## Variablen in Templates sind Werte

Objekte, die mit `setVariable()` übergeben werden, werden in Templates über `attribute()`, `hasAttribute()` und
`attributes()` gelesen. Einfache Arrays funktionieren direkt. Ein PHP-Objekt ohne diese Methoden zeigt in einem
Template nichts an.

## Fehler

Template-Fehler (eine unbekannte Variable, ein Operator, den es nicht gibt) werden mit dem Namen und der Zeile des
Templates in die Debug-Ausgabe geschrieben. Prüfen Sie in Skripten und Tests `hasErrors()`:

```php
<?php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'name', 'World' );
$out = $tpl->fetch( 'design:myext/hello.tpl' );
if ( $tpl->hasErrors() )
{
    eZDebug::writeError( $tpl->errorLog(), __METHOD__ );
}
```

## Caching

Kompilierte Templates werden im Cache-Verzeichnis der Website gecacht. Für eine neue Template-Datei muss der
template-override-Cache geleert werden, bevor eine Override-Regel sie findet, und gerenderte Ausgabe in View-Caches oder
`cache-block`s bleibt erhalten, bis diese Caches geleert werden
([Cache-Tags, Cache-Blöcke und das Leeren von Caches](cache-tags-und-leeren.md)).

## Verwandte Seiten

- Zurück: [eZSys und eZExtension](ezsys-und-ezextension.md)
- Weiter: [eZModule und Modulergebnisse](ezmodule.md)
- [Eingebaute Template-Operatoren](referenz-der-template-operatoren.md), [Eingebaute Template-Funktionen](referenz-der-template-funktionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
