---
title: Formulare in Templates
audience: designer, developer
category: templates
order: 720
summary: Formulare auf einer öffentlichen Website, aus Inhalten gebaut - Informationssammlung (Feedback- und Kontaktformulare) mit ActionCollectInformation, die collect-Templates, Prüfmeldungen, die Ergebnisseite und Mail-Templates, das Formular-Token und barrierefreies Markup.
keywords: Formulare, Feedback-Formular, Kontaktformular, Informationssammlung, ActionCollectInformation, collect-Templates, collectedinfo, Prüfung, Formular-Token, ezformtoken, Formular-Templates, forms, feedback form, contact form, information collection, validation, form token, form templates
type: help
---
# Formulare in Templates

Diese Seite ist für Designer, die Kontakt- und Feedback-Formulare bauen. In Exponential ist ein Formular meist ein Inhalt:
eine Klasse mit Feldern, die als **Information Collector** gekennzeichnet sind. Redakteure legen das Formular als Seite an;
das Design zeichnet es.

## Inhalt

1. [Wie ein Feedback-Formular funktioniert](#wie-ein-feedback-formular-funktioniert)
2. [Das Formular-Template](#das-formular-template)
3. [Nach dem Absenden](#nach-dem-absenden)
4. [Sicherheit](#sicherheit)
5. [Barrierefreiheit](#barrierefreiheit)

## Wie ein Feedback-Formular funktioniert

1. Die Vollansicht des Formularobjekts gibt ein `<form>` aus, das an `/content/action` sendet.
2. Jedes Collector-Feld wird mit `attribute_view_gui` gezeichnet, das `content/datatype/collect/` verwendet und eine
   Eingabe ausgibt.
3. Die Schaltfläche zum Absenden heißt `ActionCollectInformation`.
4. Der Kernel prüft, speichert die Antworten und zeigt eine Ergebnisseite (`content/collectedinfo/<class>.tpl`) und kann sie
   per Mail versenden (`content/collectedinfomail/<class>.tpl`).

## Das Formular-Template

```tpl
{* override/templates/full/feedback_form.tpl *}
<h1>{$node.name|wash}</h1>
{include uri='design:content/collectedinfo_validation.tpl' class='message-warning' validation=$validation collection_attributes=$collection_attributes}
<form method="post" action={'content/action'|ezurl}>
    {foreach $node.data_map as $field}
        {if $field.contentclass_attribute.is_information_collector}
            <div class="form-field">
                <label for="ezcoa-{$field.contentclassattribute_id}_{$field.contentclass_attribute_identifier}">{$field.contentclass_attribute.name|wash}</label>
                {attribute_view_gui attribute=$field}
            </div>
        {/if}
    {/foreach}
    <input type="hidden" name="ContentNodeID" value="{$node.node_id}">
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}">
    <input type="hidden" name="ViewMode" value="full">
    <button type="submit" name="ActionCollectInformation">{'Send'|i18n( 'design/mytheme/form' )}</button>
</form>
```

Vergleichen Sie mit dem Template des Feedback-Formulars des Designs, auf dem Sie aufbauen (`full/feedback_form.tpl` von
ezwebin, die Formulare des Designs media), um die genauen versteckten Felder und das Include für die Prüfung zu sehen, die
Ihre Installation verwendet. Die mitgelieferten collect-Templates geben Eingaben ohne IDs aus; geben Sie Ihren Kopien IDs,
damit die Beschriftungen oben funktionieren ([Barrierefreie Formulare](../theme-design/barrierefreie-formulare.md)).

## Nach dem Absenden

- `content/collectedinfo/<class>.tpl` (zum Beispiel `feedback.tpl`, `form.tpl`, `poll.tpl` in `design/standard`) ist die
  Dankesseite; überschreiben Sie sie für Ihr Aussehen.
- `content/collectedinfomail/<class>.tpl` baut die Mail; ihr Betreff kommt aus `{set-block scope=root variable=subject}`.
- `collect.ini` enthält die Sammeleinstellungen pro Klasse (wohin Antworten gemailt werden, ob sie gespeichert werden).

## Sicherheit

- Die Erweiterung für Formular-Tokens (ezformtoken) fügt Formularen, die an die Website senden, ein Token hinzu; behalten
  Sie die versteckten Felder, die das Original-Template Ihres Designs ausgibt.
- Prüfen Sie auf dem Server (der Kernel tut das); verlassen Sie sich nicht allein auf die Prüfung im Browser.
- Geben Sie gesammelte Antworten nie unmaskiert wieder aus.

## Barrierefreiheit

Beschriftungen, Pflichtkennzeichnungen im Text, eine Fehlerzusammenfassung mit Links zu den Feldern, `autocomplete` bei
persönlichen Feldern ([Barrierefreie Formulare](../theme-design/barrierefreie-formulare.md)).

## Verwandte Seiten

- [attribute_view_gui](attribute-view-gui.md)
- [Bearbeitungstemplates von Datentypen](datentyp-bearbeitungstemplates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
