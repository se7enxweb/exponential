---
title: Inhaltsmodell für die nationale Regierung
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Welche Inhaltsklassen und Datentypen von Exponential zur Website eines Ministeriums passen, welche mitgelieferten Klassen Sie wiederverwenden und drei Klassen, die Sie im Klasseneditor hinzufügen - Publikation, Konsultation und Pressemitteilung - mit echten Datentypen.
keywords: Inhaltsmodell, Inhaltsklasse, Datentyp, Ministerium, Klasse Publikation, Klasse Konsultation, Klasse Pressemitteilung, Klasseneditor, content model, content class, datatype, ministry, publication class, consultation class, press release class, ezbinaryfile, ezdatetime, eztags, ezobjectrelationlist, class editor
type: help
---
# Inhaltsmodell für die nationale Regierung

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Inhaltsklassen der Website eines
Ministeriums oder zentralen Ressorts entwerfen. Sie nennt die Klassen, die mit dem Standard-Websitepaket ausgeliefert
werden und so passen, wie sie sind, und gibt Rezepte für die Klassen, die die meisten nationalen Websites hinzufügen.
Jede hier genannte Klasse und jeder Datentyp existiert in Exponential: die Klassen im Paket `sevenx_classes` und dessen
Grunddaten ([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)), die Datentypen in
`kernel/classes/datatypes/` und in den genannten Erweiterungen.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Publikation](#hinzuzufügende-klasse-publikation)
3. [Hinzuzufügende Klasse: Konsultation](#hinzuzufügende-klasse-konsultation)
4. [Hinzuzufügende Klasse: Pressemitteilung](#hinzuzufügende-klasse-pressemitteilung)
5. [Taxonomie und Wiederverwendung](#taxonomie-und-wiederverwendung)
6. [Faustregeln](#faustregeln)

## Mitgelieferte Klassen, die passen

In einer neuen Installation des Standardpakets gibt es diese Klassen (Bezeichner wie unter **Setup > Klassen**):

| Klasse | Bezeichner | Verwendung auf der Website eines Ministeriums | Hinweise |
|---|---|---|---|
| Folder | `folder` | Themenseiten, Seiten von Einheiten, Container | hat Summary, Description, Display sub items, Tags (`ezkeyword`) und Publish date (`ezdatetime`) |
| Article | `article` | Politikseiten, Nachrichten, Reden | Title, Short title, Author (`ezauthor`), Intro und Body (`ezxmltext`), Image (`ezobjectrelation`) |
| File | `file` | jedes herunterladbare Dokument | Name, Description, File (`ezbinaryfile`) |
| Image | `image` | Fotos, Diagramme als Bilder | das Bildfeld kann einen Alternativtext verlangen |
| Link | `link` | ein Link auf eine Dienstleistung, die als Vorgang in einem anderen System läuft | `ezurl` |
| Event | `event` | Termine von Ministerinnen und Ministern, Starts | From Time und To Time (`ezdatetime`), Category (`ezkeyword`) |
| Feedback form | `feedback_form` | ein Kontaktformular | seine Felder Sender name, Subject, Message und Email sind Felder des Informationssammlers |
| Job position | `ng_job_position` | Stellenangebote | Location, Engagement type, Application deadline (`ezdatetime`) |
| Person | `ng_person` | öffentliche Profile von Amtsträgerinnen und Amtsträgern | Name, CV, Image; behalten Sie nur, was das Amt öffentlich macht |

Die Klassen `ng_*` kommen mit den Demo-Inhaltspaketen des Standard-Websitepakets, sowohl in der vollständigen als auch
in der leeren Variante ([Die Demo-Inhaltspakete](../../../seitenpakete/sevenx-multisite/die-demo-inhaltspakete.md)).

## Hinzuzufügende Klasse: Publikation

Ein Bericht, eine Strategie oder ein Leitfaden mit einer Einstiegsseite, die ihn zusammenfasst. Legen Sie sie unter
**Setup > Klassen**, Gruppe Content, **Neue Klasse** an
([Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)).

| Attribut | Bezeichner | Datentyp (Name im Klasseneditor) | Pflicht | Warum |
|---|---|---|---|---|
| Titel | `title` | Textzeile (`ezstring`) | ja | der Name der Publikation |
| Zusammenfassung | `summary` | XML-Block (`ezxmltext`) | ja | eine barrierefreie HTML-Zusammenfassung des Dokuments |
| Dokument | `document` | Datei (`ezbinaryfile`) | nein | das PDF oder eine andere Datei; oder verwenden Sie unten Zugehörige Dateien |
| Zugehörige Dateien | `files` | Objektbeziehungen (`ezobjectrelationlist`) | nein | mehrere Dateien (Sprachfassungen, Anlagen) als Objekte der Klasse File |
| Datum der Publikation | `publication_date` | Datum (`ezdate`) | ja | das amtliche Datum, unabhängig vom Zeitpunkt der Veröffentlichung |
| Geschäftszeichen | `reference` | Textzeile (`ezstring`) | nein | die in Zitaten verwendete Dokumentnummer |
| Art der Publikation | `publication_type` | Auswahl (`ezselection`) | ja | Bericht, Strategie, Leitfaden ... |
| Themen | `tags` | Tags (`eztags`) | nein | Themen über Einheiten hinweg ([eztags](../../../../../eng-us/features/6.0/extensions/eztags.md)) |
| Metadaten | `metadata` | Metadaten (`xrowmetadata`) | nein | Seitentitel und Beschreibung für Suchmaschinen |

Muster für den Objektnamen: `<title>`. Markieren Sie Zusammenfassung als durchsuchbar, damit die Suche die Publikation
auch dann findet, wenn die Datei nicht indiziert ist.

## Hinzuzufügende Klasse: Konsultation

| Attribut | Bezeichner | Datentyp | Sammler | Warum |
|---|---|---|---|---|
| Titel | `title` | `ezstring` | nein | |
| Vorschlag | `proposal` | `ezxmltext` | nein | was vorgeschlagen wird, in einfacher Sprache |
| Beginn | `opens` | `ezdatetime` | nein | auf der Seite angezeigt |
| Ende | `closes` | `ezdatetime` | nein | auf der Seite angezeigt; das Template des Formulars kann das Formular danach verbergen |
| Dokumente | `documents` | `ezobjectrelationlist` | nein | das Konsultationspapier und die Anlagen |
| Ihre Stellungnahme | `response` | `eztext` | ja | die Antwort des Besuchers |
| Stellungnahme als | `respondent_type` | `ezselection` | ja | Einzelperson, Organisation ... |
| Name der Organisation | `organization` | `ezstring` | ja | für Einzelpersonen optional |
| E-Mail | `email` | `ezemail` | ja | nur wenn Sie antworten werden; sonst weglassen |
| Ergebnis | `outcome` | `ezxmltext` | nein | das veröffentlichte Ergebnis nach dem Ende |

Wird **Informationssammler** an einem Attribut angekreuzt, füllen es die Besucher aus, nicht die Redaktion
([Formulare mit dem Informationssammler](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).
Stellungnahmen erscheinen unter **Setup > Gesammelte Informationen** und lassen sich als CSV exportieren
([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)). Erheben Sie nur, was die Konsultation braucht.

## Hinzuzufügende Klasse: Pressemitteilung

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | `ezstring` | |
| Datum und Uhrzeit der Veröffentlichung | `release_date` | `ezdatetime` | die Sperrfrist; kann ein Workflow-Ereignis *Bis Datum warten* steuern |
| Vorspann | `intro` | `ezxmltext` | der erste Absatz |
| Text | `body` | `ezxmltext` | |
| Pressekontakt | `press_contact` | `ezxmltext` | eine Funktionsadresse wie die der Pressestelle, keine Privatperson |
| Anlagen | `attachments` | `ezobjectrelationlist` | Fotos und Dokumente |
| Themen | `tags` | `eztags` | |

Ein Ereignis *Bis Datum warten*, eingestellt auf die Klasse und ihr Datumsattribut, hält eine Version bis zu diesem
Zeitpunkt zurück, und der Workflow-Cronjob veröffentlicht sie ([Workflows](../../../../anleitungen/workflows.md)). Testen Sie
das zuerst auf einer Kopie der Website.

## Taxonomie und Wiederverwendung

- **Baum für Verantwortung, Tags für Themen.** Der Inhaltsbaum sagt, welcher Einheit eine Seite gehört; Tags sagen,
  welche Themen sie betrifft. Eine Pressemitteilung liegt unter der Pressestelle und trägt die Tags aller Themen, die sie
  berührt.
- **Beziehungen, keine Kopien.** Eine Publikation, die von fünf Themenseiten verlinkt ist, ist ein Objekt mit fünf
  Beziehungen, sodass eine Aktualisierung einmal erfolgt
  ([Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)).
- **Mehrere Orte**, wenn eine Seite in zwei Menüs erscheinen muss
  ([Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md)).

## Faustregeln

| Regel | Grund |
|---|---|
| Fügen Sie eine Klasse nur hinzu, wenn sich ihre Felder unterscheiden | jede Klasse braucht Templates, Rollen und Schulung |
| Machen Sie Daten zu Attributen, nicht zu Text | Listen, Feeds und Ablauf verwenden sie |
| Halten Sie personenbezogene Daten aus Inhaltsklassen heraus, sofern das Amt sie nicht öffentlich macht | Datenminimierung |
| Legen Sie übersetzbare Attribute im Voraus fest | Daten und Codes werden meist nicht übersetzt ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)) |
| Übertragen Sie Klassen als Pakete zwischen Installationen | [Pakete](../../../../anleitungen/pakete.md) |

## Verwandte Seiten

- [Nationale Regierung](README.md)
- [Struktur, Sektionen und Rollen für die nationale Regierung](struktur-sektionen-und-rollen.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Ihr Inhaltsmodell entwerfen](../../../../../eng-us/help/creators/website-creators/designing-your-content-model.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
