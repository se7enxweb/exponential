---
title: Template-Operator has_any_role
audience: developer, admin
category: examples
summary: "Der Template-Operator has_any_role: 3 Parameter, registriert von eZTemplateRoleOperator."
keywords: Template-Operator, Operator, has_any_role, template operator, operator, tpl, eZTemplateRoleOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator has_any_role

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateRoleOperator` (`lib/eztemplate/classes/eztemplateroleoperator.php`).
Dieselbe Klasse registriert auch: [has_role](has_role.md), [has_role_id](has_role_id.md), [has_role_by_user](has_role_by_user.md), [has_role_by_user_id](has_role_by_user_id.md), [has_policy](has_policy.md), [has_policy_by_user](has_policy_by_user.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `roles` | array | ja | `array()` |
| `user_id` | integer | nein | `0` |
| `match_all` | boolean | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|has_any_role( array(), 0, true() )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateroleoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
