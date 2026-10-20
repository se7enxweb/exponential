---
title: Bearbeitungstemplates von Datentypen
audience: designer, developer
category: templates
order: 490
summary: Die Templates der Bearbeitungsformulare jedes Datentyps unter content/datatype/edit - wann ein öffentliches Design sie braucht (Bearbeiten im Frontend, Registrierung, Benutzerprofile), Feldnamen und IDs, die bleiben müssen, Barrierefreiheit von Beschriftungen und Fehlern und keine Inline-Styles.
keywords: Bearbeitungstemplates von Datentypen, content/datatype/edit, Bearbeitungsformular, attribute_edit_gui, Feldnamen, ezcoa-ID, Bearbeiten im Frontend, Registrierungsformular, Formular-Markup, barrierefreies Bearbeitungsformular, datatype edit templates, edit form, field names, front-end editing, user registration form, form markup, accessible edit form
type: help
---
# Bearbeitungstemplates von Datentypen

Diese Seite ist für Designer, deren öffentliches Design Bearbeitungsformulare zeigt: Bearbeiten im Frontend,
Benutzerregistrierung, Profilseiten. Jeder Datentyp hat ein Bearbeitungstemplate in `content/datatype/edit/<datatype>.tpl`,
gezeichnet von `{attribute_edit_gui}`.

## Inhalt

1. [Wann Designs sie berühren](#wann-designs-sie-berühren)
2. [Was bleiben muss](#was-bleiben-muss)
3. [Beschriftungen und Fehler](#beschriftungen-und-fehler)
4. [Gestaltung](#gestaltung)

## Wann Designs sie berühren

Die Bearbeitungstemplates von `design/standard` dienen auch der Administration: Die Designs der Administration haben für die
meisten Datentypen keine eigenen Bearbeitungstemplates. Eine Änderung in `design/standard` änderte also die Administration;
bearbeiten Sie es nie. Ein öffentliches Design trifft auf sie, wenn Besucher sich registrieren (`user/register`), ihr Profil
bearbeiten oder Redakteure das Bearbeiten im Frontend auf der öffentlichen Website nutzen; die Kopien Ihres Designs gelten
dann nur für Ihren Siteaccess.

## Was bleiben muss

- **Feldnamen.** Der Kernel liest die gesendeten Werte nach Namen, zum Beispiel
  `{$attribute_base}_ezstring_data_text_{$attribute.id}` in `edit/ezstring.tpl`. Einen Namen zu ändern, verhindert das Speichern.
- **IDs.** Die IDs `ezcoa-<class attribute id>_<identifier>` sind das, worauf Beschriftungen zeigen.
- **Versteckte Eingaben**, die das Original ausgibt.

Kopieren Sie das Original in Ihr Design und ändern Sie Markup und Klassen um diese herum.

## Beschriftungen und Fehler

- Das Template der Bearbeitungsseite (`content/edit.tpl` und `content/edit_attribute.tpl`) gibt die Beschriftungen der
  Felder aus; halten Sie jede Beschriftung an ihre Eingabe gebunden ([Barrierefreie Formulare](../../../../eng-us/help/designers/theme-design/accessible-forms.md)).
- Prüfmeldungen kommen aus `content/edit_validation.tpl`; platzieren Sie sie dort, wo Nutzer sie sehen, und setzen Sie den
  Fokus auf die Zusammenfassung.

## Gestaltung

`tests/tests/design/StandardDatatypeEditNoStyleAttributeTest.php` prüft, dass die Standard-Bearbeitungstemplates der
Datentypen keine `style=""`-Attribute tragen. Halten Sie auch Ihre Kopien frei von Inline-Styles; gestalten Sie sie aus
Ihrem Stylesheet über Klassen.

## Verwandte Seiten

- [Ansichtstemplates von Datentypen](datentyp-ansichtstemplates.md)
- [Formulare in Templates](formulare-in-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
