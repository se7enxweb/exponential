---
title: Formulareinsendungen
audience: website-creator, editor
category: website-creation
order: 480
summary: Wohin Formulareinsendungen gehen und wie Sie damit arbeiten - Setup > Gesammelte Informationen, die Sammlungen eines Formulars, CSV-Export, Entfernen, wer sie lesen darf und wie Sie personenbezogene Daten im Griff behalten.
keywords: Formulareinsendungen, gesammelte Informationen, CSV-Export, Sammlungen, Umfrageergebnisse, Einsendungen entfernen, personenbezogene Daten, Website-Ersteller, form submissions, collected information, infocollector, CSV export, collections, poll results, remove submissions, infocollector/read, personal data, website creator
type: help
---
# Formulareinsendungen

Diese Seite richtet sich an Website-Ersteller, die Formulare an die Personen übergeben, die sie bearbeiten. Jede
Einsendung eines Formulars des Informationssammlers wird als **Sammlung** gespeichert und unter
**Setup > Gesammelte Informationen** (`/infocollector/overview`) aufgelistet. Vollständige Referenz:
[Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md).

![Gesammelte Informationen: die Formulare der Website und was Besucher darüber gesendet haben](../../../../eng-us/help/images/creators/collected-information.webp)

## Inhalt

1. [Die Übersicht](#die-übersicht)
2. [Die Einsendungen eines Formulars](#die-einsendungen-eines-formulars)
3. [CSV-Export](#csv-export)
4. [Einsendungen entfernen](#einsendungen-entfernen)
5. [Wer sie sehen darf](#wer-sie-sehen-darf)
6. [Ergebnisse auf der Website anzeigen](#ergebnisse-auf-der-website-anzeigen)

## Die Übersicht

Die Seite zeigt Kennzahlen (Objekte mit Sammlungen, Sammlungen insgesamt, in den letzten 30 und 7 Tagen, die neueste),
eine Suche nach Namen, Sortierung und eine Karte pro Formular mit **Sammlungen**, **CSV exportieren** und **Objekt**.
Wenn noch nichts gesammelt wurde, listet sie die veröffentlichten Objekte auf, die sammeln könnten, sodass Sie wissen,
dass das Formular erkannt wird.

## Die Einsendungen eines Formulars

**Sammlungen** öffnet `infocollector/collectionlist/<object id>`: eine Zeile pro Einsendung; jede öffnet
`infocollector/view/<collection id>` mit allen Werten.

## CSV-Export

**CSV exportieren** lädt `<name>-collected.csv` herunter (UTF-8 mit Byte Order Mark): Sammlungs-ID, Sende- und
Änderungszeit (UTC), Benutzer-ID, dann eine Spalte pro Sammelattribut. Werte, die eine Tabellenkalkulation als Formel
lesen würde, beginnen mit einem Apostroph. Höchstens 50 000 Zeilen, die neuesten zuerst. Jeder Export wird im
Prüfprotokoll festgehalten, mit der Zeilenanzahl und niemals den Werten ([Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).

## Einsendungen entfernen

Markieren Sie Formulare in der Übersicht (oder Einsendungen in der Liste eines Formulars) und klicken Sie auf
**Ausgewählte entfernen**; die Bestätigung nennt, wie viele es sind. Die Formulare selbst bleiben. Entfernungen werden im
Prüfprotokoll festgehalten. Exportieren Sie zuerst, wenn die Daten noch gebraucht werden.

## Wer sie sehen darf

Für alles ist `infocollector/read` nötig. Geben Sie es den Personen, die die Formulare bearbeiten, nicht jedem
Redakteur ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Ergebnisse auf der Website anzeigen

Umfrageergebnisse werden vom Ergebnis-Template der Umfrage angezeigt (`content/collectedinfo/poll.tpl`). Die
Einsendungen anderer Formulare sind ihrem Wesen nach privat; veröffentlichen Sie sie nicht.

## Checkliste für personenbezogene Daten

- [ ] Nur die Felder, die Sie brauchen, werden gesammelt.
- [ ] Nur die Personen, die sie bearbeiten, haben `infocollector/read`.
- [ ] Exporte werden dort gespeichert, wo personenbezogene Daten hingehören, und gelöscht, wenn sie erledigt sind.
- [ ] Alte Einsendungen werden nach einem Zeitplan entfernt, den Sie benennen können.

## Verwandte Seiten

- [Formulare mit dem Informationssammler](formulare-mit-dem-informationssammler.md)
- [Ein Kontaktformular bauen](ein-kontaktformular-bauen.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
