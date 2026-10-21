---
title: Template-Operator implode
audience: developer, admin
category: examples
summary: "Der Template-Operator implode: 1 Parameter, registriert von eZTemplateArrayOperator."
keywords: Template-Operator, Operator, implode, template operator, operator, tpl, eZTemplateArrayOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator implode

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateArrayOperator` (`lib/eztemplate/classes/eztemplatearrayoperator.php`).
Dieselbe Klasse registriert auch: [array](array.md), [hash](hash.md), [array_prepend](array_prepend.md), [prepend](prepend.md), [array_append](array_append.md), [append](append.md), [array_merge](array_merge.md), [merge](merge.md), [contains](contains.md), [compare](compare.md), [extract](extract.md), [extract_left](extract_left.md), [extract_right](extract_right.md), [begins_with](begins_with.md), [ends_with](ends_with.md), [explode](explode.md), [repeat](repeat.md), [reverse](reverse.md), [insert](insert.md), [remove](remove.md), [replace](replace.md), [unique](unique.md), [array_sum](array_sum.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `separator` | string | ja | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|implode( 'separator' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/audit/archives.tpl` Zeile 61:

```tpl
{if $c.manifests|count}<p class="a4-audit-note">{'Latest manifests'|i18n( 'design/admin/audit' )}: {$c.manifests|implode( ', ' )|wash}</p>{/if}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatearrayoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
