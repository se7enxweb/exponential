---
title: Template-Operator flag_icon
audience: developer, admin
category: examples
summary: "Der Template-Operator flag_icon: keine benannten Parameter, registriert von eZWordToImageOperator."
keywords: Template-Operator, Operator, flag_icon, template operator, operator, tpl, eZWordToImageOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator flag_icon

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZWordToImageOperator` (`kernel/common/ezwordtoimageoperator.php`).
Dieselbe Klasse registriert auch: [wordtoimage](wordtoimage.md), [mimetype_icon](mimetype_icon.md), [class_icon](class_icon.md), [classgroup_icon](classgroup_icon.md), [action_icon](action_icon.md), [icon](icon.md), [icon_info](icon_info.md).

## Parameter

Die Klasse beschreibt ihre Parameter nicht (kein `namedParameterList()`); was sie liest, steht in ihrem Quelltext.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|flag_icon}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/template_look_edit.tpl` Zeile 57:

```tpl
{$translation_list[$language_index].locale.intl_language_name}&nbsp;<img src="{$translation_list[$language_index].language_code|flag_icon}" width="18" height="12" class="a4-flag" alt="{$translation_list[$language_index].language_code}" />
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezwordtoimageoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
