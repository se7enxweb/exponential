---
title: Beteiligungsverfahren und Umfragen
audience: government, website-creator, project-manager, editor
category: public-sector
order: 90
summary: Wie Sie in Exponential ein öffentliches Beteiligungsverfahren mit einer Klasse Consultation und einem Antwortformular durchführen, es pünktlich schließen, das Ergebnis veröffentlichen, und wann stattdessen die mitgelieferte Klasse Poll (eine Antwort je Benutzer) das richtige Werkzeug ist.
keywords: Beteiligungsverfahren, öffentliche Beteiligung, Antwortformular, Information Collector, Schlussdatum, Ergebnis, Umfrage, Ergebnisse, consultation, public consultation, response form, information collector, closing date, outcome, poll, ezoption, CollectionUserData, unique, results
type: help
---
# Beteiligungsverfahren und Umfragen

Diese Seite richtet sich an Site-Ersteller, Projektleiter und Redakteure, die die Öffentlichkeit nach ihrer Meinung
fragen. Ein **Beteiligungsverfahren** bittet über Wochen um überlegte Antworten zu einem Vorschlag; eine **Umfrage**
stellt eine schnelle Frage. Beide verwenden den Information Collector, mit unterschiedlichen Einstellungen. Quellen:
[Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)
und [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Eine Klasse für Beteiligungsverfahren](#eine-klasse-für-beteiligungsverfahren)
2. [Öffnen und schließen](#öffnen-und-schließen)
3. [Das Ergebnis lesen und veröffentlichen](#das-ergebnis-lesen-und-veröffentlichen)
4. [Umfragen](#umfragen)
5. [Beteiligungsverfahren oder Umfrage?](#beteiligungsverfahren-oder-umfrage)

## Eine Klasse für Beteiligungsverfahren

Legen Sie sie unter **Setup > Klassen** an
([Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)):

| Attribut | Bezeichner | Datentyp | Sammelnd |
|---|---|---|---|
| Titel | `title` | Textzeile | nein |
| Der Vorschlag in Kürze | `summary` | Textabschnitt | nein |
| Der Vorschlag | `proposal` | XML Block | nein |
| Dokumente | `documents` | Objektbeziehungen (File) | nein |
| Öffnet | `opens` | Datum und Zeit | nein |
| Schließt | `closes` | Datum und Zeit | nein |
| Wie wir Ihre Antwort verwenden | `privacy_note` | XML Block | nein |
| Ihre Antwort | `response` | Textabschnitt | ja |
| Sie antworten als | `respondent_type` | Auswahlliste: eine Einzelperson, eine Organisation | ja |
| Organisation (falls zutreffend) | `organization` | Textzeile | ja |
| E-Mail (optional, nur für eine Bestätigung oder Rückfragen) | `email` | E-Mail | ja |
| Ergebnis | `outcome` | XML Block | nein |

Mehrere Fragen können mehrere sammelnde Attribute vom Typ Textabschnitt sein. Halten Sie die Zahl der persönlichen
Felder gering.

## Öffnen und schließen

| Schritt | Wie |
|---|---|
| Vor dem Öffnungsdatum vorbereiten | anlegen und in einer Staging-Sektion oder als Entwurf halten ([Sektionen, 13.3](../../../../anleitungen/sektionen.md#133-inhalte-vorbereiten-bevor-sie-öffentlich-werden)) |
| Zu einem festen Zeitpunkt öffnen | dann veröffentlichen; oder ein Workflow-Ereignis **Bis Datum warten** an der Klasse mit dem Attribut **Opens** hält die Veröffentlichung bis zu diesem Datum zurück ([Workflows](../../../../anleitungen/workflows.md)) |
| Pünktlich schließen | das Template vergleicht **Closes** mit der aktuellen Zeit (`currentdate()`) und zeigt statt des Formulars „Dieses Beteiligungsverfahren wurde am ... geschlossen“ |

Der Information Collector selbst kennt kein Schlussdatum: Ein Besucher, der das Formular offen gehalten hat, oder ein
Skript, das an `content/action` sendet, könnte noch senden, nachdem das Template das Formular nicht mehr zeigt. Müssen
verspätete Antworten strikt abgelehnt werden, entfernen Sie nach dem Schließen das Sammel-Kennzeichen von den
Antwortattributen oder verschieben Sie das Objekt in eine Sektion, die anonyme Besucher nicht lesen können; vermerken
Sie verspätete Antworten bei der Auswertung.

## Das Ergebnis lesen und veröffentlichen

1. **Setup > Gesammelte Informationen**: Die Karte des Beteiligungsverfahrens zeigt die Zahl der Antworten;
   **CSV exportieren** für die Auswertung (jeder Export wird im Audit festgehalten).
2. Werten Sie außerhalb des CMS aus; veröffentlichen Sie keine einzelnen Antworten, sofern die Menschen nicht
   zugestimmt haben.
3. Bearbeiten Sie das Beteiligungsverfahren: Füllen Sie **Outcome** mit einer Zusammenfassung dessen, was gehört wurde
   und was sich ändern wird, und veröffentlichen Sie.
4. Entfernen Sie die Antworten, wenn Ihre Aufbewahrungsfrist endet
   ([Aufbewahrung gesammelter Informationen](aufbewahrung-gesammelter-informationen.md)).

## Umfragen

Die mitgelieferte Klasse **Poll** (`poll`) hat eine Optionsliste (`ezoption`) als sammelndes Attribut. `collect.ini`
behandelt Umfragen besonders:

| Einstellung | Wert für Umfragen | Wirkung |
|---|---|---|
| `CollectionUserDataList[poll]` | `unique` | eine Antwort je Benutzer |
| `SendEmailList[poll]` | `disabled` | keine Mail je Stimme |
| `Display` | `result` | das Ergebnis-Template zeigt die Zahlen |

„Eine Antwort je Benutzer“ bedeutet je Konto für angemeldete Benutzer; anonyme Besucher teilen sich ein Benutzerkonto,
sodass ihre Antworten keiner Person zugeordnet werden können, und eine Umfrage ist keine verlässliche Abstimmung.
Stellen Sie Ergebnisse als Anhaltspunkt dar.

## Beteiligungsverfahren oder Umfrage?

| Bedarf | Verwenden Sie |
|---|---|
| Überlegte Meinungen zu einem Vorschlag, mit Begründungen | ein Beteiligungsverfahren |
| Ein schneller Hinweis auf eine Vorliebe | eine Umfrage |
| Eine förmliche Abstimmung oder ein Bürgerentscheid | keines von beiden; ein eigenes System mit Identitätsprüfung |
| Strukturierte Befragungen mit vielen Fragetypen und Logik | ein Umfragewerkzeug, von der Seite aus verlinkt |

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [Grundlagen des Information Collectors](grundlagen-des-information-collectors.md)
- [Barrierefreie Formulare](barrierefreie-formulare.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
