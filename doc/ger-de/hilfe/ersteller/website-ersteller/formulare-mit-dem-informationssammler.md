---
title: Formulare mit dem Informationssammler
audience: website-creator, developer
category: website-creation
order: 450
summary: Wie Besucherformulare in Exponential funktionieren - Sammelattribute, das Absenden an content/action, Validierung, die Ergebnisseite, Mail an einen Empfänger, Formulartypen (form, feedback, poll) in collect.ini und wo Einsendungen gespeichert werden.
keywords: Informationssammler, Formulare, Feedbackformular, Kontaktformular, Umfrage, Formulartypen, Website-Ersteller, information collector, forms, feedback form, contact form, poll, collect.ini, ActionCollectInformation, collectedinfo, EmailReceiver, form types, website creator
type: help
---
# Formulare mit dem Informationssammler

Diese Seite richtet sich an Website-Ersteller, die Formulare bauen, die Besucher ausfüllen: Kontaktformulare,
Feedbackformulare, Umfragen, Formulare zur Lead-Gewinnung. Exponential macht das mit dem **Informationssammler**: eine
Klasse, deren Attribute als **Informationssammler** markiert sind, ein Objekt davon, das als Formular angezeigt wird, und
eine gespeicherte **Sammlung** für jede Einsendung ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Das Formular-Template](#das-formular-template)
3. [Formulartypen: form, feedback, poll](#formulartypen-form-feedback-poll)
4. [Mail an einen Empfänger](#mail-an-einen-empfänger)
5. [Nach dem Absenden](#nach-dem-absenden)
6. [Datenschutz](#datenschutz)

## Wie es funktioniert

1. Eine Klasse (das mitgelieferte **Feedback form**, `feedback_form`) hat Attribute, die als **Informationssammler**
   markiert sind: Name des Absenders, E-Mail, Betreff, Nachricht. Andere Attribute (eine Beschreibung) schreibt die
   Redaktion wie gewohnt.
2. Ein Redakteur legt ein Objekt der Klasse an; seine Full-Ansicht zeigt das Formular.
3. Der Besucher sendet das Formular mit der Schaltfläche `ActionCollectInformation` an `content/action`; die Ansicht
   `content/collectinformation` des Kernels validiert jedes Sammelattribut mit seinem Datentyp.
4. Bei Erfolg wird eine Sammlung gespeichert, eventuell eine Mail gesendet, und der Besucher sieht die Ergebnisseite oder
   wird weitergeleitet.
5. Die Redaktion liest und exportiert die Sammlungen unter **Setup > Gesammelte Informationen**
   ([Formulareinsendungen](formulareinsendungen.md)).

## Das Formular-Template

Das mitgelieferte ezwebin-Template (`extension/ezwebin/design/ezwebin/override/templates/full/feedback_form.tpl`) zeigt
das Muster:

```text
{include name=Validation uri='design:content/collectedinfo_validation.tpl'
         class='message-warning' validation=$validation collection_attributes=$collection_attributes}
<form method="post" action={'content/action'|ezurl}>
  <label for="sender">{$node.data_map.sender_name.contentclass_attribute.name|wash}</label>
  {attribute_view_gui attribute=$node.data_map.sender_name}
  ...
  <input type="submit" name="ActionCollectInformation" value="{'Send form'|i18n( 'design/mysite' )}">
  <input type="hidden" name="ContentNodeID" value="{$node.node_id}">
  <input type="hidden" name="ContentObjectID" value="{$node.object.id}">
  <input type="hidden" name="ViewMode" value="full">
</form>
```

Das View-Template eines Sammelattributs rendert dessen Eingabefeld. Das Formular-Token, das POST-Formulare schützt, fügt
die Erweiterung `ezformtoken` hinzu; lassen Sie sie aktiv ([Spamschutz für Formulare](spamschutz-fuer-formulare.md)).

## Formulartypen: form, feedback, poll

`settings/collect.ini` entscheidet das Verhalten pro Typ. Der Typ eines Objekts kommt aus seinem Attribut
`collection_type`, sonst aus `[InfoSettings] Type=form`:

| Block | Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|---|
| `InfoSettings` | `TypeList[]` | `poll`, `form`, `feedback` | bekannte Typen |
| `EmailSettings` | `SendEmail`, `SendEmailList[poll]` | `enabled`, `disabled` für Umfragen | Mail bei Einsendung |
| `DisplaySettings` | `Display`, `RedirectURL` | `result`, `/` | die Ergebnisseite anzeigen oder weiterleiten |
| `CollectionSettings` | `CollectAnonymousData` | `enabled` | nicht angemeldete Besucher dürfen senden |
| `CollectionSettings` | `CollectionUserData`, `CollectionUserDataList[poll]` | `multiple`, `unique` | eine Umfrage kann einmal pro Benutzer beantwortet werden |

Die Ergebnis-Templates sind `content/collectedinfo/<type>.tpl` (form, feedback, poll in `design/standard`), die
Mail-Templates `content/collectedinfomail/<type>.tpl`; überschreiben Sie sie in Ihrem Design.

## Mail an einen Empfänger

`site.ini [InformationCollectionSettings] EmailReceiver` ist der Standardempfänger. Das Mail-Template kann den Empfänger
pro Formular mit `{set-block scope=root variable=email_receiver}...{/set-block}` setzen; die Mail-Templates von ezwebin
(`extension/ezwebin/design/ezwebin/templates/content/collectedinfomail/feedback.tpl`) nehmen ihn aus einem Attribut
`recipient` des Formularobjekts, wenn es eines hat, sodass jedes Formular sein eigenes Team anschreiben kann. Dieselben
Templates können `redirect_to_node_id` setzen, um den Besucher danach zu einem Knoten zu schicken. Mails laufen über die
Mail-Einstellungen der Website
([10.4 Mail](../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)); testen Sie zuerst
mit dem Datei-Transport.

## Nach dem Absenden

`Display=result` zeigt `content/collectedinfo/<type>.tpl` mit den Werten; `Display=redirect` schickt den Besucher zu
`RedirectURL` (eine Dankeseite). Verwenden Sie die Weiterleitung, damit ein Neuladen nicht erneut sendet.

## Datenschutz

Formulare sammeln personenbezogene Daten. Sammeln Sie nur, was Sie brauchen, sagen Sie neben dem Formular, was damit
geschieht, beschränken Sie `infocollector/read` auf die Personen, die sie bearbeiten, und entfernen Sie Sammlungen, wenn
sie nicht mehr gebraucht werden.

## Verwandte Seiten

- [Ein Kontaktformular bauen](ein-kontaktformular-bauen.md)
- [Formulareinsendungen](formulareinsendungen.md)
- [Spamschutz für Formulare](spamschutz-fuer-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
