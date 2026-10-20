---
title: Templates für Custom Tags
audience: designer, developer
category: templates
order: 640
summary: Custom Tags in Rich Text (factbox, quote, strike, sub, sup und eigene) - sie in content.ini deklarieren, inline oder als Block, eigene Attribute, das nach dem Tag benannte Template und ihr Markup barrierefrei und CSP-sicher halten.
keywords: Custom Tags, Custom Tag, CustomTagSettings, AvailableCustomTags, IsInline, CustomAttributes, factbox, quote, eigene Tags in Rich Text, ezxmltags custom template, custom tags, custom tag, rich text custom
type: help
---
# Templates für Custom Tags

Diese Seite ist für Designer, die Redakteuren zusätzliche Bausteine in Rich Text geben: einen Infokasten, ein Zitat, einen
Hinweiskasten, eine Formel. Ein **Custom Tag** ist ein benanntes Tag im XML; es wird von einem Template dargestellt, das
nach ihm benannt ist.

## Inhalt

1. [Einen Custom Tag deklarieren](#einen-custom-tag-deklarieren)
2. [Sein Template](#sein-template)
3. [Eigene Attribute](#eigene-attribute)
4. [Regeln](#regeln)

## Einen Custom Tag deklarieren

`settings/content.ini [CustomTagSettings]` listet die Tags; der Kernel liefert `factbox`, `quote`, `strike`, `sub`, `sup` mit
(`strike`, `sub` und `sup` mit `IsInline[...]=true`). Ein Theme fügt in seiner `content.ini.append.php` eigene hinzu:

```ini
[CustomTagSettings]
AvailableCustomTags[]=callout
IsInline[callout]=false

[callout]
CustomAttributes[]
CustomAttributes[]=kind
```

Deklarieren Sie Custom Tags in Einstellungen, die **jeder** Siteaccess liest (den globalen Einstellungen der Erweiterung),
nicht nur der öffentliche Siteaccess: Sonst verwirft der Editor der Administration beim Speichern von Inhalten Tags, die er
nicht kennt. Das Theme starter hält seine Custom Tags genau aus diesem Grund global (sein README).

## Sein Template

Der Ausgabe-Handler verwendet den Namen des Tags als Template-Namen (`initHandlerCustom` in
`kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`; nur einfache Namen sind erlaubt):

```tpl
{* design/mytheme/templates/content/datatype/view/ezxmltags/callout.tpl *}
<aside class="callout callout-{first_set( $kind, 'note' )|wash}">
    {$content}
</aside>
```

Liefern Sie das Template in einem Design, das jeder Siteaccess erreicht (zum Beispiel im `design/standard` Ihrer
Erweiterung), damit auch die Vorschau der Administration es zeichnen kann; das Theme starter liefert schlichte Darstellungen
seiner Tags in `design/standard/` mit.

## Eigene Attribute

Jeder Name in `CustomAttributes[]` wird zu einer Variablen des Templates (`$kind` oben). Maskieren Sie Attributwerte; sie
sind Eingaben von Redakteuren. Geben Sie jedem Attribut mit `first_set` einen sinnvollen Standardwert.

## Regeln

- Inline-Tags (`IsInline`) erzeugen nur Inline-Markup (`<span>`, `<sub>`, `<s>`).
- Kein `style=""`, das aus Attributen gebaut wird: Ordnen Sie ein Attribut stattdessen einer Klasse zu.
- Semantische Elemente: `<aside>` für einen Infokasten, `<blockquote>` für ein Zitat, `<s>` für Durchgestrichenes.

## Verwandte Seiten

- [Rich-Text-Templates](../../../../eng-us/help/designers/templates/rich-text-templates.md)
- [Designs und Siteaccesses](../../../../eng-us/help/designers/theme-design/designs-and-siteaccesses.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
