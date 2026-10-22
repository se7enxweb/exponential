---
title: Eigene Bearbeitungstemplates
audience: designer, developer
category: templates
order: 727
summary: Einer Inhaltsklasse auf der öffentlichen Website mit einem Override von content/edit.tpl ein eigenes Bearbeitungsformular geben - die Override-Regel, die Variablen, die das Bearbeitungstemplate erhält, die Formularfelder und Schaltflächen, die es behalten muss, und ein kurzes Beispiel für ein Kommentarformular.
keywords: Bearbeitungstemplate, Bearbeitungsformular, Override, Bearbeiten auf der Website, Kommentarformular, edit template, content/edit.tpl, edit form, override, class_identifier, attribute_edit_gui, content_attributes_data_map, PublishButton, DiscardButton, StoreButton, front-end editing, RedirectURIAfterPublish
type: help
---
# Eigene Bearbeitungstemplates

Diese Seite richtet sich an Designer, die für eine Klasse auf der öffentlichen Website ein einfacheres
Bearbeitungsformular möchten, zum Beispiel ein Kommentarformular mit drei Feldern statt des allgemeinen Formulars mit seiner
Seitenleiste. Die Administration behält ihr eigenes Formular; die Änderung gilt für den Siteaccess, dessen
Override-Einstellungen Sie ändern.

## Inhalt

- [Die Override-Regel](#die-override-regel)
- [Was das Bearbeitungstemplate erhält](#was-das-bearbeitungstemplate-erhält)
- [Was das Formular behalten muss](#was-das-formular-behalten-muss)
- [Ein Beispiel](#ein-beispiel)
- [Was Sie beachten sollten](#was-sie-beachten-sollten)

## Die Override-Regel

Die Bearbeitungsansicht stellt `content/edit.tpl` dar. Eine Override-Regel im öffentlichen Siteaccess ersetzt es für eine
Klasse:

```ini
# settings/siteaccess/<public siteaccess>/override.ini.append.php
[edit_comment]
Source=content/edit.tpl
MatchFile=edit/comment.tpl
Subdir=templates
Match[class_identifier]=comment
```

Die Datei liegt dann in Ihrem Design unter `override/templates/edit/comment.tpl`. Neben `class_identifier` bietet die
Bearbeitungsansicht die Schlüssel `object`, `class`, `remote_id`, `class_group`, `section`, `section_identifier` und
`parent_class` / `parent_class_identifier` (die Klasse des übergeordneten Elements des Hauptorts), ein Formular lässt sich
also auch nach dem Ort wählen.

## Was das Bearbeitungstemplate erhält

| Variable | Enthält |
|---|---|
| `$object` | das bearbeitete Objekt |
| `$class` | seine Inhaltsklasse |
| `$edit_version` | die Nummer des Entwurfs |
| `$edit_language` | den Sprachcode des Entwurfs |
| `$content_attributes` | die Attribute des Entwurfs in der Reihenfolge der Klasse |
| `$content_attributes_data_map` | dieselben, nach Attributbezeichner geordnet |
| `$attribute_base` | das Präfix der Feldnamen der Attribute, zur Weitergabe an `attribute_edit_gui` |

`{attribute_edit_gui attribute=...}` gibt die Eingabe eines Attributs mit dem Bearbeitungstemplate seines Datentyps aus,
das Formular bleibt also für Text, Bilder, Relationen und jeden anderen Typ korrekt.

## Was das Formular behalten muss

- **Die Zieladresse.** Das Formular sendet zurück an die Bearbeitungsansicht dieses Entwurfs:
  `content/edit/<Objekt-ID>/<Version>/<Sprache>`, aufgebaut wie im Beispiel unten (der Sprachteil entfällt, wenn
  `$edit_language` leer ist).
- **`enctype="multipart/form-data"`**, wenn ein Attribut eine Datei annimmt.
- **Die Prüfmeldungen.** Binden Sie `design:content/edit_validation.tpl` ein, damit ein abgewiesenes Feld erklärt wird.
- **Die Schaltflächen** mit ihren Namen:

| Name der Schaltfläche | Bewirkt |
|---|---|
| `PublishButton` | veröffentlicht den Entwurf |
| `StoreButton` | speichert den Entwurf und lässt das Formular offen |
| `DiscardButton` | verwirft den Entwurf |

- **Die Weiterleitungsfelder.** Binden Sie `design:content/edit_redirection.tpl` ein, um `RedirectURIAfterPublish` und
  `RedirectIfDiscarded` aus der Sitzung in das Formular zu übernehmen, oder setzen Sie sie selbst als versteckte Felder.

## Ein Beispiel

```
{* override/templates/edit/comment.tpl *}
<form class="comment-edit" enctype="multipart/form-data" method="post"
      action={concat( 'content/edit/', $object.id, '/', $edit_version, '/', $edit_language|not|choose( concat( $edit_language, '/' ), '' ) )|ezurl}>

    <h2>{'Write a comment'|i18n( 'design/mysite/comment' )}</h2>

    {include uri='design:content/edit_validation.tpl'}

    {foreach array( 'subject', 'author', 'message' ) as $identifier}
        {def $attribute = $content_attributes_data_map[$identifier]}
        <div class="form-field">
            <label>{$attribute.contentclass_attribute.name|wash}</label>
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute}
        </div>
        {undef $attribute}
    {/foreach}

    <div class="form-actions">
        <button type="submit" name="PublishButton" value="1">{'Publish comment'|i18n( 'design/mysite/comment' )}</button>
        <button type="submit" name="DiscardButton" value="1">{'Cancel'|i18n( 'design/mysite/comment' )}</button>
    </div>

    {include uri='design:content/edit_redirection.tpl'}
</form>
```

Um den Benutzer nach dem Veröffentlichen zum Artikel zurückzuschicken, setzen Sie `RedirectURIAfterPublish` in das Formular,
das den Kommentar anlegt ([Schaltflächen zum Bearbeiten, Entfernen und Anlegen auf der Website](schaltflaechen-zum-bearbeiten-entfernen-und-anlegen.md)):
Der Wert reist mit dem Entwurf.

## Was Sie beachten sollten

- **Lassen Sie nur weg, was so bleiben darf, wie es ist.** Die mitgelieferten Datentypen lesen nur die Felder, die gesendet
  werden; ein Attribut, das Sie nicht ausgeben, behält also seinen Wert oder bleibt bei einem neuen Objekt leer. Ein
  erforderliches Attribut, das weggelassen wird, wird auch nicht geprüft; geben Sie jedes erforderliche Attribut aus.
- **Übersetzungen.** Ein Attribut, dessen Übersetzung abgeschaltet ist, zeigt seinen Wert statt einer Eingabe, wenn der
  Benutzer eine Übersetzung bearbeitet; das allgemeine `content/edit_attribute.tpl` behandelt diesen Fall, und Sie können es
  statt einer eigenen Schleife einbinden.
- **Prüfen Sie das Ergebnis in jeder Sprache, in der die Klasse bearbeitet wird**, und mit einem Benutzer, der nur die
  Rechte eines Besuchers hat, der Kommentare schreibt.
- **Leeren Sie den Template-Override-Cache**, nachdem Sie die Regel und die neue Datei hinzugefügt haben, sonst erscheint
  weiter das allgemeine Formular ([Template-Kompilierung und Cache](template-kompilierung-und-cache.md)).

## Verwandte Seiten

- [Schaltflächen zum Bearbeiten, Entfernen und Anlegen auf der Website](schaltflaechen-zum-bearbeiten-entfernen-und-anlegen.md)
- [Bearbeitungstemplates von Datentypen](datentyp-bearbeitungstemplates.md)
- [Override-Grundlagen](override-grundlagen.md)
- [Override-Match-Schlüssel](override-match-schluessel.md)
- [Formulare in Templates](formulare-in-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
