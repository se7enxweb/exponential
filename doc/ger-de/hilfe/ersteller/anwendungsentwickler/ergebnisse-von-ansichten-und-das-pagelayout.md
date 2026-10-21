---
title: Ergebnisse von Ansichten und das Pagelayout
audience: app-creator, developer
category: app-creation
order: 120
summary: Was eine Ansicht zurückgibt und wie es in den Browser gelangt - das Array $Result, Templates im Design Ihrer Erweiterung, das Pagelayout darum herum, alternative Pagelayouts, JSON- und Dateiantworten sowie Übersetzungen von Ansichtstexten.
keywords: Ergebnis, Pagelayout, Template, Design, JSON-Antwort, Download, Übersetzung, Anwendungsentwickler, Result, content, path, pagelayout, eZTemplate, template, design, JSON response, download, i18n, ezpI18n, ui_context, app creator
type: help
---
# Ergebnisse von Ansichten und das Pagelayout

Diese Seite richtet sich an Anwendungsentwickler, die Seiten aus Ansichten rendern. Eine Ansicht gibt ein `$Result`
zurück; der Kernel setzt dessen `content` in das Pagelayout des Siteaccess ein. HTML gehört in Templates, nicht in PHP.

## Inhalt

1. [Das Ergebnis-Array](#das-ergebnis-array)
2. [Templates Ihrer Erweiterung](#templates-ihrer-erweiterung)
3. [Das Pagelayout darum herum](#das-pagelayout-darum-herum)
4. [Antworten, die keine Seiten sind](#antworten-die-keine-seiten-sind)
5. [Übersetzte Texte](#übersetzte-texte)

## Das Ergebnis-Array

| Schlüssel | Bedeutung |
|---|---|
| `content` | das HTML der Ansicht, meist `$tpl->fetch( 'design:<path>.tpl' )` |
| `path` | Einträge der Brotkrümelnavigation: `array( 'text' => ..., 'url' => ... )`; der letzte mit `'url' => false` |
| `pagelayout` | ein anderes Pagelayout-Template für diese Ansicht oder `false` für keines |

## Templates Ihrer Erweiterung

Legen Sie Templates in `extension/<ext>/design/standard/templates/<ext>/...` ab (das Design `standard` ist in jeder
Kette enthalten) und registrieren Sie die Design-Erweiterung:

```ini
# extension/expdemo/settings/design.ini.append.php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=expdemo
*/ ?>
```

```php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'items', $items );
$Result['content'] = $tpl->fetch( 'design:expdemo/list.tpl' );
```

Websites überschreiben Ihre Templates in ihren eigenen Designs, und genau das macht eine Erweiterung wiederverwendbar
([Template-Overrides](../website-ersteller/template-overrides.md)). Für die Administration gehören Templates in die
Admin-Designs und folgen den Gestaltungsregeln der Administration: kein Inline-CSS, gemeinsame Komponenten und Tokens
([Gestaltung von admin4](../../../bc/6.0/admin4-gestaltung.md)).

## Das Pagelayout darum herum

Die `pagelayout.tpl` des Siteaccess erhält `$module_result` mit Ihrem `content` und `path`
([Das Pagelayout](../website-ersteller/das-pagelayout.md)). Übergeben Sie `'pagelayout' => false`, um nur die Ausgabe der
Ansicht zu senden, oder nennen Sie ein Template für ein minimales Layout. Die Admin-Designs wählen ihren Rahmen anhand
von `default_navigation_part` und `ui_context` der Ansicht.

## Antworten, die keine Seiten sind

Für JSON senden Sie die Antwort und beenden die Anfrage ohne das Pagelayout:

```php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( array( 'ok' => true, 'data' => $data ) );
eZExecution::cleanExit();
```

Verwenden Sie für APIs bevorzugt die vorhandenen JSON-Schichten ([JSON-Endpunkte mit ezjscore](json-endpunkte-mit-ezjscore.md),
[Die REST-API](rest-api.md)). Unter Velocity beendet `cleanExit()` die Anfrage durch eine Exception, die der Server
behandelt; schließen Sie den Aufruf nicht in ein `catch ( Exception $e )` ein, sonst wird die Seite an Ihre Ausgabe
angehängt.

## Übersetzte Texte

In Templates: `{'Latest items'|i18n( 'extension/expdemo' )}`. In PHP: `ezpI18n::tr( 'extension/expdemo', 'Latest items' )`.
Übersetzungen liegen in `extension/<ext>/translations/<locale>/translation.ts` und werden mit
`site.ini [RegionalSettings] TranslationExtensions[]=<ext>` registriert
([Internationalisierung](../../../../eng-us/standards/internationalization.md)).

## Verwandte Seiten

- [Eigene Module und Ansichten](eigene-module-und-ansichten.md)
- [Template-Funktionen und Includes](template-funktionen-und-includes.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
