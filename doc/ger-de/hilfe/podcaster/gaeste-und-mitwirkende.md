---
title: Gäste und Mitwirkende
audience: podcaster
category: podcasting-streaming
order: 32
summary: Wie Sie Gäste, Moderatoren und Team einer Sendung auf einer Exponential-Website vorstellen - Personen-Objekte und Beziehungsfelder, der Datentyp Autoren, Nennungen in den Shownotes, was der Feed über Autoren sagt - und wie Sie mit personenbezogenen Daten und der Einwilligung von Gästen umgehen.
keywords: Gäste, Moderatoren, Mitwirkende, Team, Person, ng_person, Verknüpfungen, ezobjectrelationlist, ezauthor, Autoren, itunes:author, Einwilligung, personenbezogene Daten, Gastseite, guests, hosts, credits, crew, relations, authors, consent, personal data, guest page, contributors
type: help
---
# Gäste und Mitwirkende

Diese Seite ist für Produzenten, die die Menschen vor und hinter einer Sendung nennen. Ein Gast kann eine Zeile in den
Shownotes sein oder eine eigene Seite, auf die jede Episode mit ihm verweist. Beides funktioniert; das Zweite macht
"alle Episoden mit diesem Gast" möglich.

## Inhalt

1. [Drei Wege, Mitwirkende zu nennen](#drei-wege-mitwirkende-zu-nennen)
2. [Gastseiten](#gastseiten)
3. [Gäste mit Episoden verknüpfen](#gäste-mit-episoden-verknüpfen)
4. [Moderatoren und Team](#moderatoren-und-team)
5. [Was der Feed sagt](#was-der-feed-sagt)
6. [Einwilligung und personenbezogene Daten](#einwilligung-und-personenbezogene-daten)

## Drei Wege, Mitwirkende zu nennen

| Weg | Wie | Geeignet für |
|---|---|---|
| In den Shownotes | eine Zeile mit Name, Rolle und Link ([Shownotes schreiben](shownotes-schreiben.md)) | gelegentliche Gäste |
| Ein Personen-Objekt pro Gast, mit Episoden verknüpft | ein Beziehungsfeld in der Episodenklasse | wiederkehrende Gäste, ein Gästearchiv |
| Ein Feld **Autoren** (`ezauthor`) in der Episode | Name und E-Mail pro Mitwirkendem | interne Aufzeichnungen, wer daran gearbeitet hat |

## Gastseiten

Auf Installationen mit dem Paket der Media Site hat die Klasse **Person** (`ng_person`) die Felder **Name**, **CV**
(formatierter Text), **Image**, **Copyright** (für den Fotonachweis), **Facebook URL**, **Twitter URL**, **Instagram
URL** und **Meta Data**. Auf anderen Websites kann Ihr Administrator eine ähnliche Klasse anlegen, oder Sie verwenden
einen Artikel.

1. Legen Sie einen Ordner "Gäste" an ([Die Sendungsstruktur einrichten](die-sendungsstruktur-einrichten.md)).
2. Legen Sie pro Gast ein Personen-Objekt an: Name, eine kurze Biografie in den eigenen Worten des Gastes, ein Foto,
   das er geliefert hat, dessen Nachweis und nur die Links, die er gezeigt haben möchte.

## Gäste mit Episoden verknüpfen

Geben Sie der Episodenklasse ein Feld vom Typ **Objektbeziehungen** (`ezobjectrelationlist`), zum Beispiel mit dem
Namen **Guests** ([Eine Episodenklasse aufbauen](eine-episodenklasse-aufbauen.md)). Im Bearbeitungsformular der Episode
wählt **Vorhandenes hinzufügen** die Personen-Objekte aus ([Verknüpfungen und Bilder](../inhalte-bearbeiten.md#verknüpfungen-und-bilder)).

Was die Website dann zeigt, hängt vom Design ab: Die Episodenseite kann ihre Gäste auflisten, und die Seite eines
Gastes kann die Episoden auflisten, die mit ihm verknüpft sind (umgekehrte Verknüpfungen). Bitten Sie Ihren Designer um
beide Ansichten; die Techniken stehen unter [Verknüpfungen und Wiederverwendung](../ersteller/website-ersteller/relationen-und-wiederverwendung.md).

## Moderatoren und Team

- **Auf der Website**: Moderatoren sind Personen wie Gäste; verknüpfen Sie sie über ein eigenes Feld (zum Beispiel
  **Hosts**), damit Listen Moderatoren von Gästen unterscheiden können.
- **Im Audio**: Nennen Sie die Mitwirkenden auch am Ende der Episode.
- **Der Datentyp Autoren** speichert Namen und E-Mail-Adressen. Seine Standardansicht zeigt jeden Namen **mit seiner
  E-Mail-Adresse** (`design/standard/templates/content/datatype/view/ezauthor.tpl`); verwenden Sie ihn nicht für
  Adressen, die privat bleiben müssen, oder lassen Sie ihn vom Design auf der öffentlichen Seite weglassen.

## Was der Feed sagt

Der Podcast-Feed hat zwei Autorenelemente, und keines stammt von Gästen:

| Element | Quelle |
|---|---|
| `itunes:author` des Kanals | das Feld **Autor** des Exports: der Autor der Sendung, wie Hörer ihn sehen sollen |
| `itunes:author` des Elements | der Name des **Benutzers, der das Episodenobjekt angelegt hat** (`podcastItemList()` in `kernel/classes/ezrssexport.php`) |

Das Benutzerkonto, das eine Episode anlegt, wird also im Feed genannt. Wenn das der Moderator sein soll, lassen Sie die
Moderatoren ihre Episoden mit ihren eigenen Konten anlegen; ein gemeinsames technisches Konto setzt seinen Namen in
jedes Element. Gäste werden in der Beschreibung genannt, wenn diese den Shownotes zugeordnet ist.

## Einwilligung und personenbezogene Daten

Gäste sind Menschen mit Rechten an ihren personenbezogenen Daten, und eine veröffentlichte Episode ist öffentlich und
langlebig.

- Fragen Sie, bevor Sie Foto, Biografie, Links oder E-Mail-Adresse eines Gastes veröffentlichen, und veröffentlichen
  Sie nur, was er erlaubt.
- Veröffentlichen Sie nie die private Telefonnummer, Adresse oder E-Mail-Adresse eines Gastes. Die **E-Mail des
  Eigentümers** im Feed ist öffentlich; sie ist die Adresse der Sendung, nicht die eines Gastes.
- Halten Sie Einwilligungen außerhalb der öffentlichen Inhalte fest (in den Unterlagen Ihres Teams); ein verborgenes
  Feld steht trotzdem in der Datenbank und in Exporten.
- Wenn ein Gast seine Einwilligung widerruft, aktualisieren oder verbergen Sie sein Personen-Objekt und prüfen die
  Notizen der Episoden ([Episoden verbergen und zurückziehen](episoden-verbergen-und-zurueckziehen.md)).

Der Pressebereich behandelt die Seite des Interviews: [Gäste in Podcast und Radio](../presse/gaeste-in-podcast-und-radio.md)
und [Privatsphäre vor der Kamera](../presse/privatsphaere-vor-der-kamera.md).

## Verwandte Seiten

- [Shownotes schreiben](shownotes-schreiben.md)
- [Rechte und Lizenzen dessen, was Sie veröffentlichen](rechte-und-lizenzen.md)
- [Compliance und DSGVO](../hosting/sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
