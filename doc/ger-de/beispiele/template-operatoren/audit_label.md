---
title: Template-Operator audit_label
audience: developer, admin
category: examples
summary: "Der Template-Operator audit_label: keine benannten Parameter, registriert von expAuditTemplateOperator."
keywords: Template-Operator, Operator, audit_label, template operator, operator, tpl, expAuditTemplateOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator audit_label

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expAuditTemplateOperator` (`kernel/classes/audit/console/expaudittemplateoperator.php`).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|audit_label}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/classes/audit/console/expaudittemplateoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
