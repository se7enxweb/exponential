---
title: Template-Operator ezdesign
audience: developer, admin
category: examples
summary: "Der Template-Operator ezdesign: 2 Parameter, registriert von eZURLOperator."
keywords: Template-Operator, Operator, ezdesign, template operator, operator, tpl, eZURLOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezdesign

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZURLOperator` (`kernel/common/ezurloperator.php`).
Dieselbe Klasse registriert auch: [ezurl](ezurl.md), [ezroot](ezroot.md), [ezimage](ezimage.md), [exturl](exturl.md), [ezsys](ezsys.md), [ezhttp](ezhttp.md), [ezhttp_hasvariable](ezhttp_hasvariable.md), [ezini](ezini.md), [ezini_hasvariable](ezini_hasvariable.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `quote_val` | string | nein | `'double'` |
| `server_url` | string | nein | `'relative'` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezdesign( 'double', 'relative' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/view/versionview.tpl` Zeile 183:

```tpl
<a href={concat("content/versionview/",$object.id,"/",$view_version.version,"/",$language, "/site_access/", $siteaccess, $view_mode_uri)|ezurl} target="_blank"><img src={"images/view-fullscreen.png"|ezdesign} /></a>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezurloperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
