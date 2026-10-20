---
title: Template-Operator currentdate
audience: developer, admin
category: examples
summary: "Der Template-Operator currentdate: keine benannten Parameter, registriert von eZTemplateLocaleOperator."
keywords: Template-Operator, Operator, currentdate, template operator, operator, tpl, eZTemplateLocaleOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator currentdate

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateLocaleOperator` (`lib/eztemplate/classes/eztemplatelocaleoperator.php`).
Dieselbe Klasse registriert auch: [l10n](l10n.md), [locale](locale.md), [datetime](datetime.md), [maketime](maketime.md), [makedate](makedate.md), [gettime](gettime.md).

## Parameter

Keine.

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{currentdate}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/collectedinfo/poll.tpl` Zeile 7:

```tpl
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Anonymous users are not allowed to vote in this poll. Please log in.'|i18n('design/admin/content/collectedinfo/poll')}</h2>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatelocaleoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
