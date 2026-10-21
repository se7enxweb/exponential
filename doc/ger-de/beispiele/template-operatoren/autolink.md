---
title: Template-Operator autolink
audience: developer, admin
category: examples
summary: "Der Template-Operator autolink: 1 Parameter, registriert von eZAutoLinkOperator."
keywords: Template-Operator, Operator, autolink, template operator, operator, tpl, eZAutoLinkOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator autolink

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZAutoLinkOperator` (`kernel/common/ezautolinkoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `max_chars` | integer | nein | `null` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|autolink( 1 )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/comment.tpl` Zeile 14:

```tpl
<p>{$node.data_map.message.content|wash(xhtml)|break|wordtoimage|autolink}</p>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezautolinkoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
