---
title: Barrierefreie Formulare
audience: designer
category: theme-design
order: 480
summary: Formulare, die ein Design zeichnet (Suche, Anmeldung, Feedback und Informationssammlung, Benutzerregistrierung, Bearbeitungsseiten): Beschriftungen, die mit Feldern verknüpft sind, die ids, die die Bearbeitungstemplates der Datentypen den Feldern geben, Pflichtfeldmarkierungen, Fehlerzusammenfassungen und -meldungen sowie autocomplete.
keywords: Formulare, barrierefreie Formulare, label, for, id, ezcoa, Pflichtfelder, Fehlermeldung, Validierung, aria-describedby, aria-invalid, autocomplete, Suchformular, Feedbackformular, Informationssammlung, forms, accessible forms, required fields, error message, validation, search form, feedback form, information collection
type: help
---
# Barrierefreie Formulare

Diese Seite ist für Designer, die Formulare gestalten oder überschreiben: das Suchfeld, Anmelde- und Registrierungsseiten,
Feedbackformulare (Informationssammlung) und Bearbeitungsseiten für Inhalte. Sie beantwortet: wie Felder ihre Beschriftungen
bekommen, wie Fehler angezeigt werden und was die Templates des Kernels bereits mitbringen.

## Inhalt

1. [Beschriftungen](#beschriftungen)
2. [Feld-ids aus den Datentyp-Templates](#feld-ids-aus-den-datentyp-templates)
3. [Pflichtfelder](#pflichtfelder)
4. [Fehler](#fehler)
5. [Autocomplete und Eingabetypen](#autocomplete-und-eingabetypen)
6. [Das Suchformular](#das-suchformular)

## Beschriftungen

Jedes Feld braucht ein sichtbares `<label>`, das mit `for`/`id` damit verknüpft ist oder es umschließt. Platzhaltertext ist
keine Beschriftung. Gruppen von Optionsfeldern oder Kontrollkästchen gehören in ein `<fieldset>` mit einer `<legend>`.

```tpl
<label for="search-text">{'Search'|i18n( 'design/mytheme' )}</label>
<input id="search-text" type="search" name="SearchText" autocomplete="off">
```

## Feld-ids aus den Datentyp-Templates

Felder von Inhaltsklassen werden von Datentyp-Templates in `design/standard/templates/content/datatype/` gezeichnet:

- **Bearbeitungsseiten** verwenden `content/datatype/edit/`. Das Template für die Textzeile gibt seinem Eingabefeld
  `id="ezcoa-<class attribute id>_<attribute identifier>"` (`edit/ezstring.tpl`), sodass eine Beschriftung darauf zeigen kann.
- **Feedbackformulare** (Informationssammlung) verwenden `content/datatype/collect/`. Das mitgelieferte `collect/ezstring.tpl`
  gibt sein Eingabefeld **ohne id** aus, sodass ein `<label for>` nichts hat, worauf es zeigen kann.

Überschreiben Sie für ein barrierefreies Feedbackformular in Ihrem Design die collect-Templates der Datentypen, die Ihr Formular
verwendet (`templates/content/datatype/collect/ezstring.tpl` und so weiter), und geben Sie jedem Eingabefeld eine id, zum Beispiel
nach demselben `ezcoa-`-Schema; geben Sie dann die Beschriftungen in Ihrem Formular-Template aus:

```tpl
{def $field = $node.data_map.email}
<label for="ezcoa-{$field.contentclassattribute_id}_{$field.contentclass_attribute_identifier}">
    {$field.contentclass_attribute.name|wash}
</label>
{attribute_view_gui attribute=$field}
{undef $field}
```

Wenn Sie ein Datentyp-Template überschreiben, behalten Sie das id-Schema bei, damit Beschriftungen in anderen Templates weiter passen.

## Pflichtfelder

- Kennzeichnen Sie Pflichtfelder sichtbar (ein Sternchen mit einer Legende, die es erklärt, oder das Wort „erforderlich“) und
  mit dem Attribut `required` oder `aria-required="true"`.
- admin4 zeigt eine Markierung neben der Beschriftung mit dem Wort in `.a4-sr-only` für Screenreader und einen Hinweis über dem
  Formular (`.a4-required-mark`, `.a4-required-hint` in `components.css`).

## Fehler

Der Kernel validiert beim Absenden und gibt dem Template ein Validierungsergebnis: `content/edit_validation.tpl` und
`content/collectedinfo_validation.tpl` geben die Liste der Probleme aus. In Ihrem Design:

- Zeigen Sie oben im Formular eine Zusammenfassung mit einer Überschrift, deren Einträge jeweils zu ihrem Feld verlinken; setzen
  Sie den Fokus auf die Zusammenfassung (`.a4-error-summary` von admin4 erhält beim Laden den Fokus, mit `role="alert"`).
- Zeigen Sie die Meldung auch neben dem Feld, verknüpft mit `aria-describedby`, und markieren Sie das Feld mit `aria-invalid="true"`.
- Behalten Sie, was der Besucher eingegeben hat.
- Verlassen Sie sich nicht allein auf Farbe: Fügen Sie ein Symbol oder das Wort „Fehler“ hinzu.

## Autocomplete und Eingabetypen

Verwenden Sie den richtigen `type` (`email`, `tel`, `url`, `number`, `search`) und die richtigen `autocomplete`-Token (`name`,
`email`, `username`, `current-password`, `new-password`), damit Telefone die passende Tastatur zeigen und Passwortmanager
funktionieren. Die Anmelde- und Passwort-Templates des Moduls user sind die Stellen, an denen `current-password` und
`new-password` am meisten zählen.

## Das Suchformular

Ein Suchfeld im Kopfbereich braucht eine Beschriftung (sichtbar oder `.sr-only`), einen Absendeknopf mit Namen und die Landmark
`role="search"` am `<form>`:

```tpl
<form role="search" action={'/content/search'|ezurl} method="get">
    <label class="sr-only" for="site-search">{'Search'|i18n( 'design/mytheme' )}</label>
    <input id="site-search" type="search" name="SearchText">
    <button type="submit">{'Search'|i18n( 'design/mytheme' )}</button>
</form>
```

## Verwandte Seiten

- [Formulare in Templates](../vorlagen/formulare-in-templates.md)
- [Tastatur und Fokus](tastatur-und-fokus.md)
- [Bearbeitungstemplates der Datentypen](../vorlagen/datentyp-bearbeitungstemplates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
