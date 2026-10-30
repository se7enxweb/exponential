---
title: Wie füge ich einer Inhaltsklasse ein Attribut hinzu?
audience: admin
category: manual
order: 635
summary: Öffnen Sie die Klasse unter Setup > Klassen, klicken Sie auf Bearbeiten, wählen Sie den Attributtyp und Attribut hinzufügen, füllen Sie Name, Bezeichner und die Optionen aus und klicken Sie auf OK; jedes Objekt der Klasse erhält das neue, leere Feld.
keywords: Inhaltsklasse, Klassenattribut, Attribut hinzufügen, Feld, Datentyp, Bezeichner, Pflichtfeld, durchsuchbar, Informationssammler, Übersetzung deaktivieren, class/edit, Anwenden, OK, content class, class attribute, add attribute, field, datatype, identifier, required, searchable, information collector, disable translation, Apply
context: class/edit
type: help
---
# Wie füge ich einer Inhaltsklasse ein Attribut hinzu?

Eine Inhaltsklasse legt die Felder (Attribute) fest, die jedes Objekt der Klasse hat. Wenn Sie ein Attribut
hinzufügen, erhalten alle vorhandenen Objekte der Klasse beim Speichern der Klasse ein leeres Feld.

## Wer das tun kann

Benutzer, deren Rolle das Modul `class` hat (alle Funktionen), meist Administratoren und Website-Entwickler.

## Schritte

1. Klicken Sie im oberen Menü auf **Setup** und wählen Sie **Klassen**.
2. Öffnen Sie die Klassengruppe, zum Beispiel „Content“, und klicken Sie auf die Klasse.
   Die Seite der Klasse listet ihre Attribute mit Typen und Optionen auf.
3. Klicken Sie auf **Bearbeiten**.
4. Wählen Sie den Typ des neuen Attributs, zum Beispiel **Textzeile**, und klicken Sie auf **Attribut hinzufügen**.
   Das neue Attribut erscheint am Ende der Liste.
5. Füllen Sie **Name** aus, die Beschriftung, die Redakteure sehen, und **Bezeichner**, den Namen, den Templates und
   Einstellungen verwenden.
   Der Bezeichner besteht aus Kleinbuchstaben, Ziffern und `_` und lässt sich nicht mehr gefahrlos ändern, sobald
   Templates ihn verwenden.
6. Setzen Sie die Optionen, die Sie brauchen:
   - **Erforderlich**: Ein Objekt lässt sich ohne Wert nicht veröffentlichen.
   - **Durchsuchbar**: Der Wert kommt in den Suchindex.
   - **Informationssammler**: Besucher füllen das Feld in einem Formular aus, nicht Redakteure (für
     Feedbackformulare).
   - **Übersetzung deaktivieren**: Der Wert ist in jeder Sprache eines Objekts derselbe.
7. Füllen Sie die Einstellungen des Typs aus, zum Beispiel den Standardwert oder die maximale Länge einer Textzeile.
8. Bringen Sie das Attribut mit den Pfeilen oder der Positionsnummer an seinen Platz.
9. Klicken Sie auf **OK**.
   Die Klasse wird gespeichert, und ihre Objekte erhalten das neue Attribut.

**Anwenden** speichert einen Entwurf der Klasse und lässt das Formular offen; für die Objekte ändert sich nichts bis
**OK**. **Abbrechen** verwirft den Entwurf.

## Was schiefgehen kann

- **„Die Klassendefinition konnte nicht gespeichert werden.“** Ein Name oder Bezeichner fehlt, oder ein Bezeichner wird
  doppelt verwendet. Die Meldungen oben im Formular nennen das Attribut.
- **Die Klasse ist gesperrt.** Jemand anderes bearbeitet sie. Die Sperre endet, wenn diese Person speichert oder
  abbricht, oder nach `content.ini [ClassSettings] DraftTimeout` Sekunden (standardmäßig 7200).
- **Ein neues Pflichtattribut und alte Objekte.** Vorhandene Objekte erhalten das Feld leer. Sie bleiben
  veröffentlicht, aber wenn ein Redakteur eines davon das nächste Mal bearbeitet, lässt es sich erst veröffentlichen,
  wenn das Feld ausgefüllt ist.
- **Das Speichern dauert lange.** Eine Klasse mit sehr vielen Objekten erhält das neue Feld in allen Objekten auf
  einmal. Tun Sie es zu einer ruhigen Zeit.
- **Die Suche findet die neuen Werte nicht.** Eine Änderung von **Durchsuchbar** wirkt sich nur darauf aus, was ab
  dann indiziert wird. Objekte, die nicht erneut bearbeitet werden, brauchen einen neu aufgebauten Suchindex
  (`php bin/php/updatesearchindex.php`).
- **Achtung:** Wenn Sie ein Attribut mit **Ausgewählte Attribute löschen** entfernen und auf **OK** klicken, wird sein
  Inhalt in jedem Objekt der Klasse gelöscht. Das lässt sich nicht rückgängig machen.

## Verwandte Seiten

- [Klassengruppen](../../anleitungen/klassengruppen.md): die Klassenseiten im Detail
- [Eine Inhaltsklasse anlegen](../cms-entwickler/eine-inhaltsklasse-anlegen.md)
- [Datentypen auswählen](../cms-entwickler/datentypen-waehlen.md)
- [Inhaltsklassen und Attribute](../cms-entwickler/inhaltsklassen-und-attribute.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
