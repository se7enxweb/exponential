---
title: Template-Operator csp_nonce
audience: developer, admin
category: examples
summary: "Der Template-Operator csp_nonce: keine benannten Parameter, registriert von expContentSecurityPolicyOperator."
keywords: Template-Operator, Operator, csp_nonce, template operator, operator, tpl, expContentSecurityPolicyOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator csp_nonce

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expContentSecurityPolicyOperator` (`kernel/common/expcontentsecuritypolicyoperator.php`).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|csp_nonce}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/expcontentsecuritypolicyoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
