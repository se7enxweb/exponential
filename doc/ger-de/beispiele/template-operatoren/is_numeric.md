---
title: Template-Operator is_numeric
audience: developer, admin
category: examples
summary: "Der Template-Operator is_numeric: keine benannten Parameter, registriert von eZTemplateTypeOperator."
keywords: Template-Operator, Operator, is_numeric, template operator, operator, tpl, eZTemplateTypeOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator is_numeric

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateTypeOperator` (`lib/eztemplate/classes/eztemplatetypeoperator.php`).
Dieselbe Klasse registriert auch: [is_array](is_array.md), [is_boolean](is_boolean.md), [is_integer](is_integer.md), [is_float](is_float.md), [is_string](is_string.md), [is_object](is_object.md), [is_class](is_class.md), [is_null](is_null.md), [is_set](is_set.md), [is_unset](is_unset.md), [get_type](get_type.md), [get_class](get_class.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|is_numeric}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/setup/languages.tpl` Zeile 90:

```tpl
<td>{if $language_extension.coverage|is_numeric}<span class="a4-meter a4-setup-languages-meter{if $language_extension.pct|ge( 100 )} is-ok{/if}" aria-hidden="true"><span class="a4-pct-{$language_extension.pct}"></span></span> {$language_extension.pct} %{else}<span class="a4-muted">{'English source texts'|i18n( 'design/admin/setup/languages' )}</span>{/if}</td></tr>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatetypeoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
