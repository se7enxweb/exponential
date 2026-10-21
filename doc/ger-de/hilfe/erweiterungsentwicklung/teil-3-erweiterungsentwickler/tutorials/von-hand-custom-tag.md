---
title: "Von Hand: ein Custom Tag für Rich Text"
audience: developer, app-creator, designer
category: extension-development
order: 200
summary: Ein Custom Tag für Rich Text - AvailableCustomTags, ein eigenes Attribut mit einem Bedienelement im Online-Editor und das ezxmltags-Template, das es mit $content rendert, geprüft.
keywords: Tutorial, von Hand, Custom Tag, eigenes Tag, Rich Text, Online-Editor, Hinweisbox, tutorial, by hand, custom tag, rich text, ezxmltext, AvailableCustomTags, CustomAttributes, IsInline, ezxmltags, online editor, ezoe_attributes.ini, factbox
type: help
---
# Von Hand: ein Custom Tag für Rich Text

Tutorial 19. Redakteure schreiben Rich Text (der Datentyp `ezxmltext`) mit dem Online-Editor. Ein **Custom Tag** gibt
ihnen einen eigenen Block, hier eine Hinweisbox mit der Variante „info“ oder „warn“. Die Einstellungen folgen
`settings/content.ini`, deren eigene Custom Tags (`factbox`, `quote`, `strike`, `sub`, `sup`) auf dieselbe Weise
eingerichtet sind; das Template wurde von der Template-Engine gerendert.

## 1. Das Tag deklarieren

`extension/mysite_hello/settings/content.ini.append.php` (ergänzen Sie die Datentyp-Einstellungen aus Tutorial 14):

```ini
[CustomTagSettings]
AvailableCustomTags[]=mysite_note

[mysite_note]
CustomAttributes[]
CustomAttributes[]=variant
```

Ein Tag, das innerhalb einer Textzeile verwendet wird, bekäme zusätzlich `IsInline[mysite_note]=true` in
`[CustomTagSettings]`; dieses hier ist ein Block.

## 2. Ein Bedienelement für das Attribut im Online-Editor

`extension/mysite_hello/settings/ezoe_attributes.ini.append.php`:

```ini
[CustomAttribute_mysite_note_variant]
Name=Variant
Type=select
Selection[]
Selection[info]=Information
Selection[warn]=Warning
Default=info
```

Die Schlüssel (`Name`, `Type`, `Default`, `Selection[]`) sind oben in `extension/ezoe/settings/ezoe_attributes.ini`
dokumentiert.

## 3. Das Template

`extension/mysite_hello/design/standard/templates/content/datatype/view/ezxmltags/mysite_note.tpl`:

```text
{* The custom tag mysite_note: $content is the rendered body, $variant a custom attribute (info, warn). *}
{def $kind = cond( and( is_set( $variant ), eq( $variant, 'warn' ) ), 'warn', 'info' )}
<aside class="mysite-note is-{$kind}" role="note">{$content}</aside>
{undef $kind}
```

Der Ausgabe-Handler rendert das Tag mit dem Template, das nach dem Tag benannt ist (er akzeptiert nur einfache Namen,
siehe `initHandlerCustom()` in `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`); `$content`
ist der bereits gerenderte Rumpf, und jedes eigene Attribut ist eine Variable. Mit `variant="warn"` ergab es
`<aside class="mysite-note is-warn" role="note"><p>Mind the gap.</p></aside>`, ohne das Attribut die Variante `info`.
Die Variante wird nie direkt ausgegeben: Sie wird auf eine feste Liste abgebildet, sodass ein Redakteur keine Klasse
einschleusen kann.

Gestalten Sie `.mysite-note` im Stylesheet Ihres Designs, nicht im Template.

## 4. Verwenden

```bash
php bin/php/ezcache.php --clear-all
```

Bearbeiten Sie einen Artikel: Der Online-Editor bietet das Custom Tag „mysite_note“ mit einem Bedienelement „Variant“
an. Veröffentlichen Sie den Artikel und sehen Sie ihn sich an.

## Verwandte Seiten

- Zurück: [Von Hand: eine ezjscore-Serverfunktion](von-hand-ajax-serverfunktion.md)
- Weiter: [Von Hand: eine Erweiterung übersetzen](von-hand-uebersetzung.md)
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
