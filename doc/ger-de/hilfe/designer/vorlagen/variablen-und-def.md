---
title: Variablen und def
audience: designer, developer
category: templates
order: 30
summary: Template-Variablen mit def, set und undef anlegen, ändern und entfernen; Gültigkeitsbereiche (current, root, global), um Werte nach oben zu reichen; default für optionale Parameter; und die Benennungsgewohnheiten, die Kollisionen zwischen eingebundenen Templates vermeiden.
keywords: def, set, undef, let, default, Template-Variablen, Gültigkeitsbereich, scope global, scope root, Variablenkollision, is_set, first_set, Benennung von Variablen, template variables, scope, variable clash, variable naming
type: help
---
# Variablen und def

Diese Seite ist für Designer, die Variablen in Templates verwenden. Sie beantwortet: wie Sie sie anlegen und ändern, wie lange
sie leben und wie Sie vermeiden, dass zwei Templates denselben Namen für Verschiedenes verwenden.

## Inhalt

1. [def, set, undef](#def-set-undef)
2. [Optionale Parameter: default und first_set](#optionale-parameter-default-und-first_set)
3. [Gültigkeitsbereich](#gültigkeitsbereich)
4. [Benennung](#benennung)
5. [Ältere Formen](#ältere-formen)

## def, set, undef

Aus dem Syntaxkommentar von `lib/eztemplate/classes/eztemplatedeffunction.php`:

```tpl
{def $i=10 $j=20}
{def $s1='hello' $s2='world'}
{set $i=$i|inc}
{undef $i}
{undef $s1 $s2}
```

- `{def}` legt Variablen an. Eine schon vorhandene Variable zu definieren, weist den neuen Wert zu und schreibt die Warnung
  „Variable ... is already defined.“ in die Debug-Ausgabe.
- `{set}` ändert eine vorhandene Variable.
- `{undef}` entfernt Variablen; `{undef}` allein entfernt jede Variable, die dieses Template definiert hat.

## Optionale Parameter: default und first_set

Ein Template, das mit oder ohne einen Parameter eingebunden werden kann:

```tpl
{default image_class='medium'}
```

setzt `$image_class` nur, wenn der Aufrufer es nicht übergeben hat (das `ezimage.tpl` des Kernels tut das). In einem Ausdruck
gibt `first_set( $a, $b, 'fallback' )` den ersten gesetzten Wert zurück, und `is_set( $x )` / `is_unset( $x )` prüfen ihn.

## Gültigkeitsbereich

Eine in einem Template definierte Variable ist in den Templates sichtbar, die es einbindet, nicht in seinem Aufrufer. Um einen
Wert nach oben zurückzugeben (zum Beispiel von einer Vollansicht an das Pagelayout), verwendet der Kernel `scope`:

```tpl
{set-block scope=root variable=cache_ttl}400{/set-block}
```

schreibt `cache_ttl` in den Wurzel-Namensraum, wo die Inhaltsansicht es liest. Für Werte auf Seitenebene ziehen Sie die
[persistent variable](die-persistent-variable.md) vor.

## Benennung

Eingebundene Templates teilen den Namensraum mit ihrem Einbindenden: Ein `{def $item ...}` in einem Teil überschreibt (mit einer
Warnung), wenn das einbindende Template schon `$item` hat. Gewohnheiten, die das verhindern:

- Stellen Sie den Variablen eines Teils ein kurzes Kürzel voran (`$tz_` in einem Teaser-Teil, `pl_` wie das Pagelayout von
  starter);
- entfernen Sie am Ende des Teils mit `{undef}`, was er definiert hat;
- übergeben Sie Teilen Werte als Include-Parameter, statt sich auf die Namen des Einbindenden zu verlassen.

## Ältere Formen

`{let}` ... `{/let}` mit `{set}` darin und Variablen mit dem Gültigkeitsbereich von `{section}` stammen aus älteren Templates;
neue Templates verwenden `{def}`/`{undef}` ([Section (veraltet)](section-legacy.md)).

## Verwandte Seiten

- [TPL-Syntax: Grundlagen](tpl-syntax-grundlagen.md)
- [Include](include.md)
- [Set-block und append-block](set-block-und-append-block.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
