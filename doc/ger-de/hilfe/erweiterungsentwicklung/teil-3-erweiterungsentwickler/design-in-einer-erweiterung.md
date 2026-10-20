---
title: Ein Design in einer Erweiterung
audience: developer, app-creator, designer
category: extension-development
order: 220
summary: Templates, Overrides, Stylesheets, Skripte und Bilder, die eine Erweiterung mitbringt - welchen Designordner Sie verwenden, wie Sie sie überschreibbar machen und wie sie auf die Seite gelangen.
keywords: Design, Design-Erweiterung, Templates, Override-Templates, Modul-Templates, Datentyp-Templates, überschreibbar, Bilder, design, design extension, DesignExtensions, standard design, admin design, admin4, templates, override templates, module templates, datatype templates, overridable, images
type: help
---
# Ein Design in einer Erweiterung

Teil 3, Kapitel 22. Fast jede Erweiterung hat Templates: die Seiten ihres Moduls, die Ansichten ihres Datentyps, die
Bildschirme der Administration. Sie liegen in einem Ordner `design/` der Erweiterung und werden über die Designkette
gefunden. Dieses Kapitel richtet sich an Autoren von Erweiterungen; ein ganzes **Theme** zu bauen beschreibt
[Teil 6](../../../../eng-us/help/extension-development/part-6-theme-developers/README.md).

## Den Designordner registrieren

`extension/mysite_hello/settings/design.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

Jedes `extension/mysite_hello/design/<design>/` ist dann Teil der Designkette für Siteaccesses, die `<design>`
verwenden ([Die Designkette](../../../../eng-us/help/extension-development/part-2-builder/the-design-chain.md)).

## Welcher Designordner

| Templates für | Legen Sie sie ab in | Warum |
|---|---|---|
| die öffentlichen Seiten Ihres Moduls | `design/standard/templates/<module>/` | `standard` steht am Ende jeder Kette: Sie funktionieren unter jedem Website-Design, und ein Website-Design kann sie überschreiben |
| Ihren Datentyp | `design/standard/templates/content/datatype/{edit,view}/` und `class/datatype/{edit,view}/` | dasselbe |
| die Admin-Seiten Ihres Moduls | `design/admin/templates/<module>/` und `design/admin4/templates/<module>/` | die Designs der Administration; admin4 ist die moderne Administration |
| Overrides, die Sie als Vorgabe mitliefern | `design/standard/override/templates/` | von Override-Regeln in Ihren Einstellungen verwendet |

Legen Sie Templates für Module oder Datentypen nicht in ein Website-Design (`design/mysite/`): Sie würden für jedes
andere Website-Design verschwinden.

## Überschreibbar machen

Eine Website, die Ihre Erweiterung installiert, wird Ihr HTML ändern wollen, ohne Ihre Dateien zu bearbeiten. Helfen
Sie ihr dabei:

- Halten Sie Templates klein und teilen Sie sie in Includes auf
  (`{include uri='design:hello/parts/item.tpl' item=$item}`); eine Website kann dann einen Teil überschreiben statt der
  ganzen Seite.
- Benennen Sie jedes Template nach dem, was es ist, in einem Ordner, der nach Ihrem Modul benannt ist.
- Legen Sie keine Logik in Templates, die eine Website beim Überschreiben kopieren müsste; verlagern Sie sie in
  Operatoren oder in die View.
- Dokumentieren Sie die Templates und ihre Variablen in Ihrer README.

## Stylesheets, Skripte, Bilder

Sie gehören in `design/<design>/stylesheets/`, `javascript/` und `images/` und werden über die Einstellungen des
Designs und den ezjscore-Packer geladen; siehe
[CSS und JavaScript in einer Erweiterung](css-und-javascript-in-einer-erweiterung.md). Templates enthalten nie
`<style>`, `style=""`, Inline-`<script>` oder `on...=`-Attribute: Die Administration setzt eine Content Security Policy
durch und lehnt sie ab ([Content Security Policy für Autoren von Erweiterungen](content-security-policy-fuer-erweiterungen.md)).

## Neue Template-Dateien und Caches

Eine **neue** Template-Datei wird erst gefunden, nachdem die Caches für Template-Overrides und Designs geleert wurden:

```bash
php bin/php/ezcache.php --clear-id=template-override,design_base
```

Ohne das kann eine Seite mit leerem Inhaltsbereich gerendert werden. `./console exp:velocity deploy` enthält diese
Leerungen.

## Prüfen

Schalten Sie `ShowTemplatePathComments` ein und öffnen Sie Ihre Seite: Die Kommentare müssen die Dateien Ihrer
Erweiterung nennen. Probieren Sie dann ein Override einer davon in der Website aus, um zu sehen, dass sie
überschreibbar ist.

## RAD

**Design extension** (`/setup/designextension`) schreibt ein Design mit Templates, Stylesheets und einem Satz
Overrides; die Modul-Assistenten schreiben ihre Templates in `standard` und in die Designs der Administration.

## Verwandte Seiten

- Zurück: [CLI-Skripte und Console-Befehle](cli-skripte-und-console-befehle.md)
- Weiter: [CSS und JavaScript in einer Erweiterung](css-und-javascript-in-einer-erweiterung.md)
- [Admin-Seiten im admin4-Stil](admin-seiten-im-admin4-stil.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
