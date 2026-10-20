---
title: Template-Operator siteaccess_url
audience: developer, admin
category: examples
summary: "Der Template-Operator siteaccess_url: 1 Parameter, registriert von ezpSiteAccessURLOperator."
keywords: Template-Operator, Operator, siteaccess_url, template operator, operator, tpl, ezpSiteAccessURLOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator siteaccess_url

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `ezpSiteAccessURLOperator` (`kernel/common/ezpsiteaccessurloperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `siteaccess` | string | nein | `''` |

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{siteaccess_url( 'siteaccess' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin3/templates/page_topmenu.tpl` Zeile 13:

```tpl
<a class="site-preview" href="{siteaccess_url()|wash}" title="{'Open the site'|i18n( 'design/admin/pagelayout' )|wash}">
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezpsiteaccessurloperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
