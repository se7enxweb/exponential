---
title: "Die View-Schicht: die Template-Sprache neben Twig und Blade"
audience: web-framework-developer, developer, designer
category: web-frameworks
order: 100
summary: Die Template-Sprache von Exponential für Entwickler, die Twig, Blade, Jinja oder ERB kennen - Syntax im direkten Vergleich, Operatoren statt Infix-Symbolen, kein automatisches Escaping, fetch im Template, das Pagelayout statt extends, Includes und View-Funktionen, Kompilierung und Caches - mit den Stolperfallen und den Seiten, die tiefer gehen.
keywords: Template-Sprache, template language, tpl, Twig, Blade, Jinja, ERB, Smarty, Syntax, syntax, wash, Escaping, escaping, Operatoren, operators, def, foreach, include, Pagelayout, pagelayout, Layout, layout, extends, Kompilierung, compile, cache-block, Web-Framework-Entwickler, web framework developer
type: help
---
# Die View-Schicht: die Template-Sprache neben Twig und Blade

Diese Seite richtet sich an Entwickler, die Templates in Twig, Blade, Jinja oder ERB schreiben. Exponential rendert
HTML mit einer eigenen Template-Sprache: `.tpl`-Dateien, die `eZTemplate` (`lib/eztemplate/classes/eztemplate.php`)
zu PHP kompiliert. Auf den ersten Blick ähnelt sie Smarty; [Ein Template von Smarty umziehen](../template-sprache/ein-template-von-smarty-umziehen.md)
und [Was die Template-Sprache ist](../template-sprache/was-die-template-sprache-ist.md) behandeln sie ausführlich.
Hier finden Sie die Unterschiede, die am meisten zählen, wenn Sie von einem anderen Framework kommen.

## Inhalt

1. [Syntax im direkten Vergleich](#syntax-im-direkten-vergleich)
2. [Fünf Unterschiede, die beißen](#fünf-unterschiede-die-beißen)
3. [Layouts](#layouts)
4. [Includes, Partials und Komponenten](#includes-partials-und-komponenten)
5. [Die Sprache erweitern](#die-sprache-erweitern)
6. [Kompilierung und Caches](#kompilierung-und-caches)
7. [Kann ich stattdessen Twig verwenden?](#kann-ich-stattdessen-twig-verwenden)

## Syntax im direkten Vergleich

| Aufgabe | Twig | Blade | Template-Sprache von Exponential |
|---|---|---|---|
| Ausgeben, escapt | `{{ title }}` | `{{ $title }}` | `{$title\|wash}` |
| Roh ausgeben | `{{ html\|raw }}` | `{!! $html !!}` | `{$html}` |
| Variable | `{% set n = 3 %}` | `@php($n = 3)` | `{def $n = 3}` (neu), `{set $n = 4}` (ändern) |
| Bedingung | `{% if a > 1 %}` | `@if($a > 1)` | `{if $a\|gt( 1 )}` ... `{/if}` |
| Schleife | `{% for p in pages %}` | `@foreach($pages as $p)` | `{foreach $pages as $p}` ... `{/foreach}` |
| Attribut | `page.parent.name` | `$page->parent->name` | `$page.parent.name` |
| Filter oder Helfer | `name\|upper` | `strtoupper($name)` | `$name\|upcase` |
| Arithmetik | `a + b` | `$a + $b` | `sum( $a, $b )` oder `$a\|sum( $b )` |
| Übersetzung | `'Save'\|trans` | `__('Save')` | `'Save'\|i18n( 'extension/expdemo' )` |
| URL | `path( 'item', {id: 42} )` | `route( 'item', 42 )` | `'expdemo/item/42'\|ezurl` |
| Kommentar | `{# ... #}` | `{{-- ... --}}` | `{* ... *}` |

## Fünf Unterschiede, die beißen

Aus [Smarty und die Template-Sprache: die Unterschiede im Detail](../template-sprache/smarty-unterschiede-im-detail.md):

1. **Kein automatisches Escaping.** Die Engine escapt Ausgaben nie; schreiben Sie `|wash` an alles, was aus Inhalten,
   URLs oder Request-Parametern stammt. Gerenderter Rich Text wird über sein View-Template (`attribute_view_gui`)
   ausgegeben und darf nicht erneut gewaschen werden.
2. **Keine Infix-Arithmetik und keine Infix-Vergleiche.** `{if $a > 1}` tut nicht, was es zu sagen scheint; verwenden
   Sie `gt`, `eq`, `and`, `sum` und die anderen Operatoren.
3. **Unbekannte Operatoren reichen ihre Eingabe durch** und protokollieren einen Fehler, statt die Seite scheitern zu
   lassen, sodass ein Tippfehler Rohdaten ausgeben kann. Beobachten Sie während der Entwicklung die Debug-Ausgabe.
4. **`def` und `set` sind strikt**: `def` legt an, `set` ändert; falscher Gebrauch schreibt eine Warnung.
5. **Kein PHP in Templates.** Logik gehört in Operatoren, Fetch-Funktionen und die View.

## Layouts

Es gibt kein `{% extends %}` und kein `@extends`. Das **Pagelayout** des Siteaccess (`pagelayout.tpl`) ist das
Grundlayout jeder Seite: Die View rendert ihr eigenes Template, und das Pagelayout gibt es mit
`{$module_result.content}` aus ([Responses](responses-weiterleitungen-und-fehler.md#das-pagelayout)). Wo Twig einen Block
eines Eltern-Templates überschreiben würde, ersetzt Exponential ein ganzes Template durch einen **Override**, der nach
Bedingungen wie der Inhaltsklasse, dem Knoten oder dem View-Modus ausgewählt wird ([Template-Auflösung](template-aufloesung-designs-und-overrides.md)).

## Includes, Partials und Komponenten

| Bedarf | Verwenden Sie |
|---|---|
| Ein Partial | `{include uri='design:parts/menu.tpl' title=$node.name}`; zusätzliche Parameter werden im eingebundenen Template zu Variablen |
| Ein Inhaltselement in einem View-Modus rendern (eine „Komponente“ für Inhalte) | `{node_view_gui content_node=$child view='line'}` wählt `node/view/line.tpl` und dessen Overrides |
| Ein einzelnes Feld rendern | `{attribute_view_gui attribute=$node.data_map.title}` verwendet das View-Template des Datentyps |
| Variablen getrennt halten | Namespaces, gesetzt durch die View-Funktionen ([Namespaces](../template-sprache/namensraeume.md)) |

## Die Sprache erweitern

| Twig oder Blade | Exponential |
|---|---|
| Twig-Filter oder -Funktion, Blade-Direktive | ein Template-Operator: eine PHP-Klasse, registriert in `extension/<ext>/autoloads/eztemplateautoload.php` ([Template-Operatoren](../ersteller/anwendungsentwickler/template-operatoren-schreiben.md)) |
| Twig-Tag | eine Template-Funktion ([Eigene Template-Funktionen](../template-sprache/eigene-template-funktionen.md)) |
| View-Composer oder ein Twig-Global aus einem Dienst | eine Fetch-Funktion (`fetch( 'expdemo', 'stats', hash() )`) ([Daten in Templates bringen](fetch-funktionen-und-template-daten.md)) |

## Kompilierung und Caches

Templates werden zu PHP-Dateien im Cache-Verzeichnis kompiliert und neu erzeugt, wenn sich die Quelle ändert
([Template-Kompilierung](../template-sprache/der-template-compiler.md)). Inhaltsansichten werden zusätzlich im
View-Cache gehalten, und `{cache-block}` speichert fertige HTML-Fragmente ([Template-Caches](../template-sprache/template-caches.md),
[Caching](cache-ebenen.md)). Wenn sich ein Template „nicht ändert“, liegt die Ursache fast immer in einem dieser Caches.

## Kann ich stattdessen Twig verwenden?

Nicht im Legacy-Kernel allein. Twig steht zur Verfügung, wenn der Kernel über die Legacy Bridge innerhalb der
Symfony-Plattform läuft: Die öffentliche Site kann mit Twig rendern, während die Redakteure die Legacy-Administration
behalten, und die Erweiterung `ngsymfonytools` ermöglicht es einem `.tpl`-Template, ein Twig-Template einzubinden oder
einen Symfony-Controller zu rendern ([Symfony Legacy Bridge](symfony-und-die-legacy-bridge.md), [Site-Bundles der Plattform](../../funktionen/6.0/plattform-site-bundles.md)).

## Verwandte Seiten

- [Die Template-Sprache: Übersicht](../template-sprache/README.md)
- [Smarty und die Template-Sprache im direkten Vergleich](../template-sprache/smarty-nebeneinander.md)
- [Wash und Escaping](../designer/vorlagen/wash-und-maskierung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
