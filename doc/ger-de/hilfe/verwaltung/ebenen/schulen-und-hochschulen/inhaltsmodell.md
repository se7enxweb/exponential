---
title: Inhaltsmodell für Schulen und Hochschulen
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website einer Bildungseinrichtung passen, und Rezepte für die Klassen Studiengang, Kurs, Fachbereich und Richtliniendokument mit echten Datentypen, einschließlich der Beziehungen zwischen Studiengängen, Kursen, Fachbereichen und Personen.
keywords: Inhaltsmodell, Schule, Hochschule, Universität, Klasse Studiengang, Klasse Kurs, Klasse Fachbereich, Richtliniendokument, Person, Klasseneditor, content model, school, university, program class, course class, department class, policy document, person, documentation page, ezobjectrelationlist, eztags, class editor
type: help
---
# Inhaltsmodell für Schulen und Hochschulen

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Klassen der Website einer Schule,
Fachhochschule oder Universität entwerfen. Die genannten Klassen und Datentypen gibt es in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md),
[Die Demo-Inhaltspakete](../../../seitenpakete/sevenx-multisite/die-demo-inhaltspakete.md),
`kernel/classes/datatypes/`); der Klasseneditor ist unter
[Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Studiengang](#hinzuzufügende-klasse-studiengang)
3. [Hinzuzufügende Klasse: Kurs](#hinzuzufügende-klasse-kurs)
4. [Hinzuzufügende Klasse: Fachbereich](#hinzuzufügende-klasse-fachbereich)
5. [Hinzuzufügende Klasse: Richtliniendokument](#hinzuzufügende-klasse-richtliniendokument)
6. [Wie die Klassen zusammenhängen](#wie-die-klassen-zusammenhängen)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Bereiche der Website, Bereiche der Fachbereiche |
| Article | `article` | Nachrichten, Erklärungen |
| Documentation page | `documentation_page` | Handbücher mit Kapiteln (ein Container) |
| Event, Event calendar | `event`, `event_calendar` | Tage der offenen Tür, Halbjahrestermine, Veranstaltungen |
| File | `file` | Formulare, Berichte, Richtlinien als Dokumente |
| Person | `ng_person` | Profile der öffentlichen Rolle: Name, CV (XML-Block), Image, Links zu sozialen Netzwerken |
| Job position | `ng_job_position` | Stellenangebote mit Bewerbungsfrist |
| Feedback form | `feedback_form` | Kontaktformulare |
| Gallery, Image | `gallery`, `image` | Fotos, mit Einwilligung, wo Personen gezeigt werden |

## Hinzuzufügende Klasse: Studiengang

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Name | `name` | Textzeile (`ezstring`) | |
| Stufe | `level` | Auswahl (`ezselection`) | für Schulen: Klassenstufe oder Schulstufe; für Hochschulen: Bachelor, Master ... |
| Kurz gesagt | `summary` | Textabschnitt (`eztext`) | |
| Was Sie lernen werden | `content` | XML-Block (`ezxmltext`) | |
| Zugangsvoraussetzungen | `requirements` | XML-Block | |
| Dauer | `duration` | Textzeile | |
| Bewerbungsfrist | `deadline` | Datum und Zeit (`ezdatetime`) | Listen der noch offenen Studiengänge |
| Bewerben | `apply_url` | URL (`ezurl`) | das Zulassungssystem |
| Kurse | `courses` | Objektbeziehungen (`ezobjectrelationlist`) | Objekte Kurs |
| Fachbereich | `department` | Objektbeziehung (`ezobjectrelation`) | |
| Themen | `tags` | Tags (`eztags`) | Fachgebiete |

## Hinzuzufügende Klasse: Kurs

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Titel | `title` | Textzeile |
| Kennung | `code` | Textzeile |
| Beschreibung | `description` | XML-Block |
| Leistungspunkte oder Stunden | `credits` | Ganzzahl (`ezinteger`) |
| Lehrende | `teachers` | Objektbeziehungen (Objekte Person) |
| Unterrichtssprache | `language` | Auswahl |

Für einen großen Kurskatalog, der in einem System der Studierendenverwaltung gepflegt wird, verlinken Sie darauf, statt
Hunderte Kurse von Hand zu kopieren; ein Import über die REST-API ist eine Entwicklungsaufgabe
([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)).

## Hinzuzufügende Klasse: Fachbereich

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Name | `name` | Textzeile |
| Über uns | `about` | XML-Block |
| Leitung (öffentliche Rolle) | `head` | Objektbeziehung (Person) |
| Kontakt | `contact` | XML-Block (ein gemeinsames Postfach und Telefon) |
| Bild | `image` | Bild (`ezimage`), Alternativtext verpflichtend |

Ein Fachbereich ist ein Container, wenn seine Seiten unterhalb von ihm liegen; setzen Sie in der Klasse **Container**.

## Hinzuzufügende Klasse: Richtliniendokument

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | |
| Zusammenfassung | `summary` | XML-Block | die Richtlinie in Kürze, auf der Seite lesbar |
| Dokument | `document` | Datei (`ezbinaryfile`) | der genehmigte Text |
| Genehmigt am | `approved_on` | Datum (`ezdate`) | |
| Nächste Überprüfung | `review_date` | Datum | Richtlinien werden meist in einem Zyklus überprüft |
| Verantwortlich | `owner_role` | Textzeile | eine Funktion („Leitung Zulassung“), kein Name |

## Wie die Klassen zusammenhängen

```text
Department ──< Program ──< Course >── Person (teachers)
     │                         
     └── head ── Person
```

Beziehungen halten jede Tatsache an einer Stelle: Das Profil einer Lehrperson ist ein Objekt Person, das aus jedem Kurs
verknüpft ist ([Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)). Verlässt
eine Person die Einrichtung, prüfen Sie **Verknüpfungen** an ihrem Objekt (die umgekehrten Beziehungen), bevor Sie es
entfernen.

## Verwandte Seiten

- [Schulen und Hochschulen](README.md)
- [Struktur, Sektionen und Rollen für Schulen und Hochschulen](struktur-sektionen-und-rollen.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
