---
title: Template-Funktion for
audience: developer, admin
category: examples
summary: Die Template-Funktion for, registriert von eZTemplateForFunction.
keywords: Template-Funktion, for, template function, tpl, eZTemplateForFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion for

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateForFunction` (`lib/eztemplate/classes/eztemplateforfunction.php`).

## Innere Funktionen

Innerhalb des Blocks verwendet: `{delimiter}`, `{break}`, `{continue}`, `{skip}`.

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/children_detailed.tpl` Zeile 300:

```tpl
<div class="a4-subitems-reserve" aria-hidden="true"><span class="a4-subitems-reserve-head"></span>{if gt( $subitems_reserve_rows, 0 )}{for 1 to $subitems_reserve_rows as $subitems_reserve_row}<span></span>{/for}<span class="a4-subitems-reserve-pager"></span>{/if}</div>
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateforfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
