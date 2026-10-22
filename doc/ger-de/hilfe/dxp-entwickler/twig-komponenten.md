---
title: Twig-Komponenten
audience: dxp-developer
category: dxp-development
order: 22
summary: Was ibexa/twig-components einem DXP-Projekt hinzufügt - benannte Gruppen kleiner Komponenten (script, stylesheet, template, controller, html, menu), in YAML konfiguriert und mit ibexa_twig_component_group ausgegeben - und wie Sie sie ohne Inline-Code verwenden.
keywords: Twig-Komponenten, ibexa/twig-components, ibexa_twig_component_group, ibexa_twig_component, Komponentengruppe, script, stylesheet, template, controller, html, menu, Priorität, Twig components, component group, priority
type: help
---
# Twig-Komponenten

Diese Seite richtet sich an Entwickler, die kleine Ausgabestücke an Stellen ergänzen möchten, die ein Template oder ein
Bundle vorbereitet hat, ohne dieses Template zu ändern. Das Paket `ibexa/twig-components` ist Teil des Metapakets der
DXP. Die Angaben unten wurden aus seiner Konfigurationsklasse und dem kommentierten Beispiel gelesen, das das Rezept nach
`config/packages/ibexa_twig_components.yaml` schreibt.

## Inhalt

1. [Die Idee](#die-idee)
2. [Komponententypen](#komponententypen)
3. [Eine Gruppe konfigurieren](#eine-gruppe-konfigurieren)
4. [Ausgabe](#ausgabe)
5. [Guter Gebrauch](#guter-gebrauch)

## Die Idee

Ein Template gibt eine benannte **Gruppe** aus. Jedes Bundle und Ihr Projekt können dieser Gruppe **Komponenten**
hinzufügen. Die Gruppe gibt jede Komponente in der Reihenfolge von `priority` aus. Ein Layout kann so zum Beispiel eine
Gruppe "head" anbieten, in die mehrere Pakete ihre Stylesheet-Links eintragen.

## Komponententypen

| Typ | Gibt aus |
|---|---|
| `script` | ein `<script>`-Element für eine Datei |
| `stylesheet` | ein `<link rel="stylesheet">`-Element |
| `template` | ein Twig-Template mit Parametern |
| `controller` | die Ausgabe eines Controllers |
| `html` | ein festes Stück HTML |
| `menu` | ein Menü |

Jeder andere `type` wird beim Bauen des Containers mit "Invalid type ... Allowed types: ..." abgelehnt.

## Eine Gruppe konfigurieren

```yaml
# config/packages/ibexa_twig_components.yaml
ibexa_twig_components:
    exp_site_head:                       # the group name
        exp_site_print_css:              # the component name
            type: stylesheet
            priority: 10
            arguments:
                href: '/assets/app/print.css'
        exp_site_notice:
            type: template
            arguments:
                template: '@ibexadesign/parts/notice.html.twig'
```

Jede Komponente braucht `type` und `arguments`; `priority` ist standardmäßig `0`. Die Argumente, die jeder Typ annimmt,
sind die Konstruktorargumente seiner Komponentenklasse unter `vendor/ibexa/twig-components/src/lib/Component/`; lesen
Sie sie, bevor Sie eine konfigurieren.

## Ausgabe

```twig
<head>
    {{ ibexa_twig_component_group('exp_site_head') }}
</head>
```

`ibexa_twig_component(...)` gibt eine einzelne Komponente aus.

## Guter Gebrauch

- Bevorzugen Sie Komponenten vom Typ `stylesheet`, `script` und `template` gegenüber `html`: Inline-Markup in der
  Konfiguration ist schwer zu prüfen, und Inline-Skripte und -Styles brechen eine strenge Content Security Policy.
- Verwenden Sie für Ihre eigenen Gruppen und Komponenten Namen mit `exp_`; verwenden Sie nie einen Namen, den ein Bundle
  definiert hat, es sei denn, Sie möchten seiner Gruppe etwas hinzufügen.
- Leeren Sie nach einer Änderung den Cache: Die Konfiguration wird in den Container kompiliert.

## Verwandte Seiten

- [Twig-Templates und die Design-Engine](twig-templates-und-die-design-engine.md)
- [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md)
- [Kein Inline-JavaScript](../designer/theme-design/kein-inline-javascript.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
