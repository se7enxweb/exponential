---
title: Inhaltsmodell für Behörden
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website einer Behörde passen, und Rezepte für die Klassen Leitfaden, Durchsetzungsbescheid und Programm, mit Geltungsdaten, Änderungshinweisen und einem als Objektzustand geführten Status.
keywords: Inhaltsmodell, Behörde, Regulierungsbehörde, Klasse Leitfaden, Durchsetzungsbescheid, Klasse Programm, Geltungsdatum, Änderungshinweis, Objektzustand, ersetzt, Klasseneditor, content model, agency, regulator, guidance class, enforcement notice, program class, effective date, change note, object state, superseded, class editor
type: help
---
# Inhaltsmodell für Behörden

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Klassen der Website einer
Regulierungsbehörde, Aufsichtsbehörde oder nachgeordneten Behörde entwerfen. Behörden brauchen Leitfäden, die datiert,
versioniert und mit ihrem Status gekennzeichnet sind. Die genannten Klassen und Datentypen gibt es in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md), `kernel/classes/datatypes/`, genannte
Erweiterungen); der Klasseneditor ist unter
[Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Leitfaden](#hinzuzufügende-klasse-leitfaden)
3. [Der Status als Objektzustand](#der-status-als-objektzustand)
4. [Hinzuzufügende Klasse: Durchsetzungsbescheid](#hinzuzufügende-klasse-durchsetzungsbescheid)
5. [Hinzuzufügende Klasse: Programm](#hinzuzufügende-klasse-programm)
6. [Hinweise](#hinweise)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Einstiege nach Zielgruppe, Themen |
| Article | `article` | Nachrichten, Warnungen, Erklärungen |
| Documentation page | `documentation_page` | Handbücher über mehrere Ebenen (Container) |
| File | `file` | Berichte, Datenexporte, Formulare |
| Event | `event` | Webinare, Informationsveranstaltungen |
| Feedback form | `feedback_form` | Kontakt, Einstieg für Beschwerden |
| Link | `link` | die Registersuche, das Onlineportal |

## Hinzuzufügende Klasse: Leitfaden

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile (`ezstring`) | |
| Fundstelle | `reference` | Textzeile | das zitierfähige Kürzel des Leitfadens |
| Kurz gesagt | `summary` | Textabschnitt (`eztext`) | für wen er gilt und was er verlangt |
| Text | `body` | XML-Block (`ezxmltext`) | |
| Gilt ab | `effective_from` | Datum (`ezdate`) | ab wann er gilt, was nicht der Zeitpunkt der Veröffentlichung ist |
| Änderungshinweis | `change_note` | Textabschnitt | was sich in dieser Version geändert hat, auf der Seite angezeigt |
| Gilt für | `applies_to` | Tags (`eztags`) | Branchen oder Arten regulierter Akteure |
| Ersetzt | `replaces` | Objektbeziehung (`ezobjectrelation`) | der frühere Leitfaden, den er ablöst |
| Dokumente | `documents` | Objektbeziehungen (`ezobjectrelationlist`) | Anlagen, Formulare |

Der **Änderungshinweis** wird für jede neue Version geschrieben. Zusammen mit dem Versionsverlauf ergibt er eine klare
Spur: Die Seite sagt, was sich geändert hat, und die Versionsseite zeigt genau wie
([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)).

## Der Status als Objektzustand

Ein Status wie *In Kraft*, *Ersetzt*, *Zurückgezogen* passt besser in eine Gruppe von Objektzuständen als in ein
Attribut:

- Legen Sie unter **Setup > Zustände** eine Gruppe `guidance_status` mit den Zuständen an
  ([Objektzustände](../../../../anleitungen/objektzustaende.md));
- der erste Zustand ist der Standard für neue Objekte;
- Templates zeigen den Zustand auf der Seite und in Listen an;
- Rollen können mit der Einschränkung `NewState` von `state/assign` begrenzen, wer *Zurückgezogen* setzen darf;
- das Ändern eines Zustands erzeugt keine neue Version, sodass der veröffentlichte Text genau so bleibt, wie er war.

## Hinzuzufügende Klasse: Durchsetzungsbescheid

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | |
| Betroffener | `party` | Textzeile | die Organisation, soweit Ihre Veröffentlichungsrichtlinie es erlaubt |
| Datum der Entscheidung | `decision_date` | Datum | |
| Art | `type` | Auswahl (`ezselection`) | Verwarnung, Bußgeld, Auflage zur Zulassung ... |
| Zusammenfassung | `summary` | XML-Block | |
| Entscheidungsdokument | `document` | Datei (`ezbinaryfile`) | wie erforderlich geschwärzt |
| Veröffentlicht bis | `hide_date` | Datum und Zeit (`ezdatetime`) | von `hide.php` verwendet, wenn Bescheide für einen begrenzten Zeitraum angezeigt werden |

Für einen begrenzten Zeitraum veröffentlichte Bescheide lassen sich automatisch verbergen: Nennen Sie Klasse und
Attribut in `content.ini [HideSettings] HideDateAttributeList[]` und den Ordner in `RootNodeList[]`; der Cronjob
`hide.php` erledigt den Rest ([Cronjobs](../../../../anleitungen/cronjobs.md)).

## Hinzuzufügende Klasse: Programm

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Titel | `title` | Textzeile |
| Ziel | `objective` | XML-Block |
| Wer sich bewerben kann | `eligibility` | XML-Block |
| Beginn, Ende | `opens`, `closes` | Datum und Zeit |
| Bewerben | `application_url` | URL (`ezurl`) |
| Dokumente | `documents` | Objektbeziehungen |
| Ergebnisse | `results` | XML-Block |

## Hinweise

| Regel | Grund |
|---|---|
| Geltungsdatum getrennt vom Veröffentlichungsdatum | Leitfäden können veröffentlicht werden, bevor sie gelten |
| Leitfäden nie stillschweigend überschreiben | Änderungshinweis und Versionen halten sie nachvollziehbar |
| Die Grenze des Versionsverlaufs für Leitfäden anheben, wenn jede Version erhalten bleiben muss | `DefaultVersionHistoryLimit` ist in der mitgelieferten `settings/content.ini` 45; `VersionHistoryClass[<class id>]` setzt eine Grenze für eine Klasse ([Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md)) |
| Namen von Personen in Durchsetzungsbescheiden nur, wo das Gesetz es verlangt | Datenminimierung |

## Verwandte Seiten

- [Behörden](README.md)
- [Leitfäden für regulierte Akteure](leitfaeden-fuer-regulierte-akteure.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
