---
title: Template-Funktion run-once
audience: developer, admin
category: examples
summary: Die Template-Funktion run-once, registriert von eZTemplateBlockFunction.
keywords: Template-Funktion, run-once, template function, tpl, eZTemplateBlockFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion run-once

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateBlockFunction` (`lib/eztemplate/classes/eztemplateblockfunction.php`).
Dieselbe Klasse verarbeitet auch: [set-block](set-block.md), [append-block](append-block.md).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `benchmarks/eztemplate/mark.tpl` Zeile 11:

```tpl
{run-once}i: {/run-once}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateblockfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
