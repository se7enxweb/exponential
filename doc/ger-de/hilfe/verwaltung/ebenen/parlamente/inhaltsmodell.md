---
title: Inhaltsmodell für Parlamente
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website eines Parlaments passen, und Rezepte für die Klassen Mitglied, Sitzung, Gesetzentwurf und Abstimmungsergebnis mit echten Datentypen, durch Beziehungen verbunden, sodass jede Tatsache einmal eingegeben wird.
keywords: Inhaltsmodell, Parlament, Klasse Mitglied, Klasse Sitzung, Klasse Gesetzentwurf, Abstimmungsergebnis, Stadium, Klasseneditor, content model, parliament, member class, sitting class, bill class, vote record, division, ezobjectrelationlist, ezmatrix, ezselection, stage, class editor
type: help
---
# Inhaltsmodell für Parlamente

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Klassen der Website eines Parlaments
oder einer gesetzgebenden Versammlung entwerfen. Die genannten Klassen und Datentypen gibt es in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md), `kernel/classes/datatypes/`, die genannten
Erweiterungen); der Klasseneditor ist unter
[Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Mitglied](#hinzuzufügende-klasse-mitglied)
3. [Hinzuzufügende Klasse: Sitzung](#hinzuzufügende-klasse-sitzung)
4. [Hinzuzufügende Klasse: Gesetzentwurf](#hinzuzufügende-klasse-gesetzentwurf)
5. [Hinzuzufügende Klasse: Abstimmungsergebnis](#hinzuzufügende-klasse-abstimmungsergebnis)
6. [Wie die Klassen zusammenhängen](#wie-die-klassen-zusammenhängen)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Kammern, Ausschüsse, Jahre |
| Article | `article` | Nachrichten, Erklärungen des Verfahrens |
| File | `file` | Texte von Gesetzentwürfen, Protokolle, Berichte, Abstimmungsergebnisse |
| Person | `ng_person` | kann als Mitglied dienen, wenn Sie die unten genannten Attribute hinzufügen |
| Event, Event calendar | `event`, `event_calendar` | Besuche, öffentliche Veranstaltungen |
| Documentation page | `documentation_page` | Ratgeber „Wie das Parlament funktioniert“ |

## Hinzuzufügende Klasse: Mitglied

Kopieren Sie `ng_person` oder legen Sie eine Klasse an:

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Name | `name` | Textzeile (`ezstring`) | |
| Partei oder Fraktion | `group` | Tags (`eztags`) oder Auswahl (`ezselection`) | Tags erlauben, einen Verlauf der Fraktionen zu führen |
| Wahlkreis | `constituency` | Tags | ein Tag-Baum der Wahlkreise |
| Rolle | `role` | Textabschnitt (`eztext`) | Präsidentin oder Präsident, Ausschussvorsitz ... |
| Im Amt von, bis | `from`, `to` | Datum (`ezdate`) | ehemalige Mitglieder bleiben mit einem Enddatum erhalten |
| Lebenslauf | `cv` | XML-Block (`ezxmltext`) | Informationen zur öffentlichen Rolle |
| Foto | `image` | Bild (`ezimage`) | Alternativtext verpflichtend |
| Amtlicher Kontakt | `email` | E-Mail (`ezemail`) | die amtliche Adresse |

Ehemalige Mitglieder bleiben als Objekte mit einem Enddatum: Abstimmungen und Protokolle bleiben dauerhaft mit ihnen
verknüpft.

## Hinzuzufügende Klasse: Sitzung

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Kammer oder Ausschuss | `body` | Auswahl |
| Datum und Uhrzeit | `sitting_date` | Datum und Zeit (`ezdatetime`) |
| Tagesordnung | `agenda` | XML-Block (Punkte mit Links) |
| Dokument der Tagesordnung | `agenda_file` | Objektbeziehung (`ezobjectrelation`) zu einer Datei |
| Gesetzentwürfe auf der Tagesordnung | `bills` | Objektbeziehungen (`ezobjectrelationlist`) |
| Abstimmungen | `votes` | Objektbeziehungen zu Abstimmungsergebnissen |
| Protokoll | `transcript` | Objektbeziehung zu einer Datei |
| Video | `video_url` | URL (`ezurl`) |
| Status | `status` | Auswahl: geplant, Tagesordnung veröffentlicht, Sitzung, vertagt, Aufzeichnung vollständig |

## Hinzuzufügende Klasse: Gesetzentwurf

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | |
| Drucksachennummer | `number` | Textzeile | |
| Stadium | `stage` | Auswahl | eingebracht, erste Lesung, Ausschuss, ... , angenommen, erledigt |
| Eingebracht am | `introduced_on` | Datum | |
| Einbringende | `sponsor` | Objektbeziehungen (Mitglieder) | |
| Zusammenfassung | `summary` | XML-Block | was der Gesetzentwurf bewirkt, in einfacher Sprache |
| Fassungen des Textes | `texts` | Objektbeziehungen (Objekte der Klasse File, eines je gedruckter Fassung) | |
| Änderungsanträge | `amendments` | Objektbeziehungen (Dateien) | |
| Ausschuss | `committee` | Objektbeziehung (ein Ausschussordner) | |
| Themen | `tags` | Tags | |
| Datensatz im System für Gesetzentwürfe | `source_url` | URL | die maßgebliche Aufzeichnung |

Jede gedruckte Fassung eines Gesetzentwurfs ist ein eigenes Objekt der Klasse File; die Versionen des Objekts
Gesetzentwurf zeichnen Änderungen an der Seite selbst auf.

## Hinzuzufügende Klasse: Abstimmungsergebnis

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | „Abstimmung über <Gesetzentwurf>, <Stadium>“ |
| Datum und Uhrzeit | `vote_date` | Datum und Zeit | |
| Gesetzentwurf | `bill` | Objektbeziehung | |
| Frage | `question` | Textabschnitt | was zur Abstimmung gestellt wurde |
| Ergebnis | `result` | Auswahl | angenommen, abgelehnt, Stimmengleichheit |
| Ja, Nein, Enthaltungen | `for`, `against`, `abstain` | Ganzzahl (`ezinteger`) | |
| Stimmen der Mitglieder | `member_votes` | Matrix (`ezmatrix`) | Spalten Mitglied und Stimme, wo aufgezeichnet |
| Amtliche Aufzeichnung | `record_file` | Datei (`ezbinaryfile`) | die Aufzeichnung, wie sie die Parlamentsverwaltung veröffentlicht |

Eine Matrix enthält eine Tabelle mit benannten Spalten; die Liste je Mitglied einer großen Kammer ist als Datendatei
(CSV) im Feld Datei, plus die Matrix für die Anzeige, durch Import erzeugt, vielleicht besser aufgehoben.

## Wie die Klassen zusammenhängen

```text
Sitting ──< Bill (on the agenda) ──< Vote record >── Member (sponsor, votes)
   │                 └── texts, amendments (File)
   └── transcript, agenda_file (File)
```

Umgekehrte Beziehungen beantworten „Wo erscheint das?“ (der Reiter **Verknüpfungen** eines Objekts). Prüfen Sie sie,
bevor Sie ein Mitglied oder einen Gesetzentwurf entfernen
([Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md#reverse-relations)).

## Verwandte Seiten

- [Parlamente](README.md)
- [Tagesordnungen, Gesetzentwürfe und Abstimmungsergebnisse](tagesordnungen-gesetzentwuerfe-und-abstimmungsergebnisse.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
