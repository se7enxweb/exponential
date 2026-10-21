---
title: Leistungen modellieren
audience: government, website-creator, project-manager
category: public-sector
order: 20
summary: Wie Sie im Klasseneditor von Exponential eine Inhaltsklasse Service anlegen, mit den Attributen, Datentypen, Pflichtkennzeichen und Übersetzungseinstellungen, die jede Leistungsseite einheitlich machen, und wie Sie sie testen.
keywords: Klasse für Leistungen, Inhaltsklasse, Klasseneditor, Attribute, Datentypen, erforderlich, übersetzbar, Muster für den Objektnamen, service class, content class, class editor, attributes, datatypes, required, translatable, object name pattern, ezstring, eztext, ezxmltext, ezurl, ezobjectrelation, eztags, ezdate
type: help
---
# Leistungen modellieren

Diese Seite richtet sich an Site-Ersteller und Projektleiter, die die Inhaltsklasse für Leistungsseiten anlegen. Eine
Klasse für alle Leistungen hält die Seiten einheitlich, macht Listen und Suche vorhersehbar und lässt Redakteure ein
Formular ausfüllen, statt eine Seite zu gestalten. Der Klasseneditor ist in
[Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) beschrieben; die
genannten Datentypen sind die aus `kernel/classes/datatypes/` und der Erweiterung eztags.

## Inhalt

1. [Die Attribute](#die-attribute)
2. [Die Klasse anlegen](#die-klasse-anlegen)
3. [Zu entscheidende Einstellungen](#zu-entscheidende-einstellungen)
4. [Die Klasse testen](#die-klasse-testen)
5. [Varianten](#varianten)

## Die Attribute

| Attribut | Bezeichner | Datentyp (Name im Klasseneditor) | Erforderlich | Übersetzbar |
|---|---|---|---|---|
| Name der Leistung | `title` | Textzeile (`ezstring`) | ja | ja |
| In Kürze | `summary` | Textabschnitt (`eztext`) | ja | ja |
| Wer sie nutzen kann | `eligibility` | XML Block (`ezxmltext`) | nein | ja |
| Was Sie brauchen | `requirements` | XML Block | nein | ja |
| Was sie kostet | `cost` | XML Block | nein | ja |
| Wie Sie beantragen | `how_to_apply` | XML Block | ja | ja |
| Online beantragen | `online_link` | URL (`ezurl`) | nein | ja |
| Wie lange es dauert | `processing_time` | Textzeile | nein | ja |
| Wie es weitergeht | `next_steps` | XML Block | nein | ja |
| Kontakt | `contact` | Objekt Verknüpfung (`ezobjectrelation`) | nein | nein |
| Zugehörige Dokumente | `documents` | Objektbeziehungen (`ezobjectrelationlist`) | nein | nein |
| Themen und Lebenslagen | `tags` | Tags (`eztags`) | nein | nein (Schlagworte haben eigene Übersetzungen) |
| Nächste Überprüfung | `review_date` | Datum (`ezdate`) | ja | nein |
| Metadaten | `metadata` | Metadaten (`xrowmetadata`) | nein | ja |

Muster für den Objektnamen `<title>`. Die Datentypen `xrowmetadata` und `eztags` stammen aus Erweiterungen, die die
Standardinstallation aktiviert
([Erweiterungen, die es aktiviert](../../../seitenpakete/sevenx-multisite/erweiterungen-die-sevenx-multisite-aktiviert.md)).

## Die Klasse anlegen

1. **Setup > Klassen**, Gruppe **Content**, **New class**.
2. Name `Service`, Bezeichner `service`, Muster für den Objektnamen `<title>`.
3. Für jedes Attribut: Wählen Sie den Datentyp unter **Attribut hinzufügen**, drücken Sie **Hinzufügen**, füllen Sie
   Name und Bezeichner aus, kreuzen Sie **Erforderlich** und **Übersetzbar** wie in der Tabelle an, **Durchsuchbar**
   für Textattribute.
4. Setzen Sie für **Kontakt** die erlaubte Klasse der Beziehung auf Ihre Klasse für Ansprechpartner (siehe
   [Ansprechpartner und Dienststellen](ansprechpartner-und-dienststellen.md)); erlauben Sie für **Zugehörige Dokumente**
   File.
5. Für **Nächste Überprüfung** kann der Datentyp Datum leer vorbelegt sein; lassen Sie ihn erforderlich, damit
   Redakteure ihn setzen müssen.
6. Drücken Sie **OK**. Prüfen Sie: Die Klasse erscheint in der Liste; legen Sie eine Test-Leistung an.

## Zu entscheidende Einstellungen

| Einstellung | Empfehlung | Warum |
|---|---|---|
| Container | nein | Leistungen enthalten keine anderen Inhalte; Dokumente werden verknüpft, nicht untergeordnet |
| Voreingestellte Verfügbarkeit der Objekte | wie Ihre Sprachrichtlinie es sagt | „immer verfügbar“ zeigt die Hauptsprache, wo eine Übersetzung fehlt |
| Reihenfolge der Attribute | die Lesereihenfolge der Seite | das Bearbeitungsformular folgt ihr |
| Kategorie (Reiter) der Attribute | „Content“ und „Management“ (Kontakt, Schlagworte, Überprüfung) | ein kürzerer erster Reiter für Schreibende |
| Textzeile **Trim the stored value** | an für Name der Leistung | überzählige Leerzeichen stören die Sortierung ([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)) |

## Die Klasse testen

- [ ] Legen Sie eine Leistung nur mit den Pflichtfeldern an: Sie wird veröffentlicht.
- [ ] Lassen Sie **Wie Sie beantragen** leer: Das Formular lehnt mit einer Meldung am Feld ab.
- [ ] Übersetzen Sie sie: Daten, Schlagworte und Beziehungen werden geteilt; Texte gelten je Sprache.
- [ ] Die vollständige Ansicht zeigt die Felder in der richtigen Reihenfolge (siehe
  [Die Leistungsseite und einfache Sprache](die-leistungsseite-und-einfache-sprache.md)).
- [ ] Die Suche findet die Leistung über ein Wort aus **In Kürze**.

## Varianten

| Bedarf | Änderung |
|---|---|
| Gebühren, die sich jedes Jahr an einer Stelle ändern | ein eigenes Gebührenobjekt, von Leistungen verknüpft, einmal bearbeitet |
| Leistungen in mehreren Sprachen mit unterschiedlichen Rechtstexten | halten Sie Rechtstexte in verknüpften File-Objekten je Sprache |
| Leistungen für Unternehmen und für Einwohner | eine Klasse; eine Auswahlliste Zielgruppe (`ezselection`) oder Schlagworte |
| Sehr einfache Leistungen | dieselbe Klasse; optionale Felder bleiben leer, Templates verstecken leere Felder |

Übertragen Sie die Klasse als Paket in andere Installationen, nie durch Nachbauen von Hand
([Pakete](../../../../anleitungen/pakete.md)).

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Die Leistungsseite und einfache Sprache](die-leistungsseite-und-einfache-sprache.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
