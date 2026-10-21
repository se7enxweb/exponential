---
title: Templates im Überblick
audience: designer, developer
category: templates
order: 10
summary: Eine Übersicht über die Template-Sprache (TPL) von Exponential für Designer - was Templates sind, die Arten von Templates in einem Design, die Bausteine der Sprache (Variablen, Funktionen, Operatoren, Fetches), wie Templates zu ihren Daten kommen und wo Sie weiterlesen.
keywords: Templates, tpl, Template-Sprache, eZ-Template, Überblick über Templates, Smarty-ähnlich, Template-Funktionen, Template-Operatoren, fetch, View-Templates, Override-Templates, Pagelayout, templates, template language, template overview, smarty like, template functions, template operators, view templates, override templates, pagelayout
featured: true
type: help
---
# Templates im Überblick

Diese Seite ist für Designer, die mit dem Schreiben von Templates beginnen. Die Templates von Exponential sind Textdateien mit
der Endung `.tpl`, geschrieben in seiner eigenen Template-Sprache (TPL), die HTML mit Tags in geschweiften Klammern mischt:
`{$node.name|wash}`, `{foreach ...}`, `{include ...}`. Diese Seite ist die Übersicht; jeder Abschnitt verlinkt die Einzelheiten.

## Inhalt

1. [Was ein Template tut](#was-ein-template-tut)
2. [Die Arten von Templates in einem Design](#die-arten-von-templates-in-einem-design)
3. [Die Sprache auf einem Bildschirm](#die-sprache-auf-einem-bildschirm)
4. [Woher Templates ihre Daten bekommen](#woher-templates-ihre-daten-bekommen)
5. [Lesepfad](#lesepfad)

## Was ein Template tut

Ein Template macht aus Daten (einem Knoten, einer Liste, Einstellungen) HTML. Es hat keinen Zugriff auf die Datenbank oder das
Dateisystem außer über **Fetch-Funktionen** und **Operatoren**, die Kernel und Erweiterungen bereitstellen, was Templates sicher
in die Hände von Designern legen lässt. Templates werden bei ihrem ersten Lauf in PHP kompiliert und gecacht
([Template-Kompilierung und Cache](template-kompilierung-und-cache.md)).

## Die Arten von Templates in einem Design

| Art | Beispielpfad | Aufgerufen von |
|---|---|---|
| Pagelayout | `pagelayout.tpl` | dem Kernel, um jede Seite ([Das Pagelayout](../../../../eng-us/help/designers/theme-design/the-pagelayout.md)) |
| View-Templates für Knoten | `node/view/full.tpl`, `node/view/line.tpl` | Inhaltsansichten, `node_view_gui` ([Ansichtsmodi](ansichtsmodi.md)) |
| Override-Templates | `override/templates/full/article.tpl` | Regeln in `override.ini` ([Override-Grundlagen](override-grundlagen.md)) |
| Datentyp-Templates | `content/datatype/view/ezstring.tpl` | `attribute_view_gui` ([Ansichtstemplates von Datentypen](datentyp-ansichtstemplates.md)) |
| Modul-Templates | `user/login.tpl`, `content/search.tpl` | Views von Modulen |
| Teile | `pagelayout/header.tpl`, `parts/teaser.tpl` | `{include}` aus anderen Templates |
| Templates für Layout-Blöcke | `explayouts/block/list.tpl` | der Layout-Engine ([Layouts für Designer](../../../../eng-us/help/designers/theme-design/layouts-for-designers.md)) |

## Die Sprache auf einem Bildschirm

```tpl
{* a comment *}
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                 'sort_by', $node.sort_array,
                                                 'limit', 10 ) )}
{if $children|count}
<ul class="children">
    {foreach $children as $child}
        <li><a href={$child.url_alias|ezurl}>{$child.name|wash}</a></li>
    {/foreach}
</ul>
{else}
<p>{'Nothing here yet.'|i18n( 'design/mytheme' )}</p>
{/if}
{undef $children}
```

- `{def}`, `{if}`, `{foreach}`, `{include}` sind **Funktionen** ([Funktionsreferenz](funktionsreferenz.md)).
- `|wash`, `|ezurl`, `|count`, `|i18n` sind **Operatoren**; `fetch()`, `hash()` ebenfalls, mit Klammern aufgerufen
  ([Operatoren im Überblick](operatoren-ueberblick.md)).
- `$node.name` liest ein **Attribut** eines Objekts ([Attribute und Punktnotation](attribute-und-punktnotation.md)).

## Woher Templates ihre Daten bekommen

- **Variablen, die der Aufrufer setzt**: Eine Vollansicht bekommt `$node`; ein Datentyp-Template bekommt `$attribute`; das
  Pagelayout bekommt `$module_result` und `$site` ([Pagelayout-Variablen](../../../../eng-us/help/designers/theme-design/pagelayout-variables.md)).
- **Fetch-Funktionen**: `fetch( 'content', 'list', ... )`, `fetch( 'user', 'current_user' )`, `fetch( 'explayouts', ... )`
  ([Fetch-Grundlagen](fetch-grundlagen.md)).
- **Einstellungen**: `ezini( 'Section', 'Setting', 'file.ini' )` ([Der Operator ezini](der-ezini-operator.md)).
- **Die Anfrage**: `ezhttp()` für GET- und POST-Werte, mit Vorsicht ([Der Operator ezhttp](der-ezhttp-operator.md)).

## Lesepfad

1. [TPL-Syntax: Grundlagen](tpl-syntax-grundlagen.md), [Variablen und def](variablen-und-def.md), [If und Bedingungen](if-und-bedingungen.md),
   [Foreach-Schleifen](foreach-schleifen.md), [Include](include.md).
2. [Wash und Maskierung](wash-und-maskierung.md), [URL-Operatoren](url-operatoren.md), [Der Operator i18n](der-i18n-operator.md).
3. [Fetch-Grundlagen](fetch-grundlagen.md), [Knoten, Objekte und Attribute](knoten-objekte-und-attribute.md), [Ansichtsmodi](ansichtsmodi.md).
4. [Override-Grundlagen](override-grundlagen.md), [Override-Match-Schlüssel](override-match-schluessel.md).
5. [Template-Styleguide](template-styleguide.md), [Häufige Template-Fehler](haeufige-template-fehler.md).

## Verwandte Seiten

- [Operatorreferenz](operatorreferenz.md)
- [Funktionsreferenz](funktionsreferenz.md)
- [Designer: hier beginnen](../designer-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
