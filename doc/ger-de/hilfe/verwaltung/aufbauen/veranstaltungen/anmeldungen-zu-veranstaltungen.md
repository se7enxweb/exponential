---
title: Anmeldungen zu Veranstaltungen
audience: government, website-creator, project-manager
category: public-sector
order: 60
summary: Wie Sie mit dem Information Collector von Exponential Anmeldungen zu öffentlichen Veranstaltungen annehmen - sammelnde Attribute an der Veranstaltung, die Bestätigung und die Mail, was er nicht kann (Kapazität, Wartelisten, Zahlungen), die betroffenen personenbezogenen Daten und wann ein Buchungssystem verlinkt werden sollte.
keywords: Anmeldung zu Veranstaltungen, Buchung, Information Collector, sammelnde Attribute, Kapazität, Warteliste, Bestätigungsmail, personenbezogene Daten, Aufbewahrung, Buchungssystem, event registration, sign-up, booking, information collector, collector attributes, capacity, waiting list, confirmation mail, personal data, retention, booking system
type: help
---
# Anmeldungen zu Veranstaltungen

Diese Seite richtet sich an Site-Ersteller und Projektleiter, die Anmeldungen zu einer Veranstaltung brauchen.
Exponential hat kein Buchungssystem; der **Information Collector** kann einfache Anmeldungen annehmen. Diese Seite zeigt
wie, und wo seine Grenzen liegen. Quellen:
[Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)
und [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Ein Anmeldeformular an der Veranstaltung](#ein-anmeldeformular-an-der-veranstaltung)
2. [Was er nicht kann](#was-er-nicht-kann)
3. [Personenbezogene Daten](#personenbezogene-daten)
4. [Wann ein Buchungssystem sinnvoll ist](#wann-ein-buchungssystem-sinnvoll-ist)

## Ein Anmeldeformular an der Veranstaltung

1. Fügen Sie der Klasse Event (oder einer eigenen Klasse für Veranstaltungen mit Anmeldung) sammelnde Attribute hinzu,
   jedes mit angekreuztem **Informationssammler**:

   | Attribut | Datentyp |
   |---|---|
   | Name | Textzeile |
   | E-Mail | E-Mail |
   | Zahl der Plätze | Ganzzahl |
   | Bedarf an Barrierefreiheit (optional) | Textabschnitt |

2. Zeigen Sie das Formular im Template der vollständigen Ansicht der Veranstaltung, mit dem Ziel `content/action` und
   `ActionCollectInformation` ([Grundlagen des Information Collectors](../formulare/grundlagen-des-information-collectors.md)).
3. Setzen Sie `collect.ini [DisplaySettings] Display=redirect` und eine Dankesseite, die sagt, was als Nächstes
   geschieht.
4. Senden Sie jede Anmeldung per Mail an das Postfach des Veranstalters ([Mail an Empfänger](../formulare/mail-an-empfaenger.md)).
5. Lesen Sie die Liste unter **Setup > Gesammelte Informationen**; **CSV exportieren** für die Teilnehmerliste.

Nur Veranstaltungen mit Anmeldung brauchen die sammelnden Attribute; bei anderen Veranstaltungen zeigt das Template
kein Formular, wenn das Feld Booking „freier Eintritt“ sagt.

## Was er nicht kann

| Fehlt | Abhilfe |
|---|---|
| Kapazität: er stoppt nicht bei N Plätzen | beobachten Sie die Zahl unter **Setup > Gesammelte Informationen**; setzen Sie das Feld Booking auf „ausgebucht“, was das Formular versteckt |
| Eine Warteliste | Anmeldungen nach „ausgebucht“ gehen an ein zweites Formular, oder per E-Mail |
| Bestätigungsmail an die Person | die Standardmail geht an den Empfänger; einer eingegebenen Adresse zu schreiben, kann missbraucht werden, um anderen Spam zu senden; bestätigen Sie daher mit einer Antwort von Hand |
| Absage durch die Person | per E-Mail an den Veranstalter |
| Zahlungen | ein Zahlungssystem; der Shop von Exponential ist für Produkte, in den meisten Behörden nicht für Veranstaltungsgebühren |

## Personenbezogene Daten

| Praxis | Warum |
|---|---|
| Fragen Sie nur, was die Veranstaltung braucht | Datenminimierung |
| „Bedarf an Barrierefreiheit“ ist optional und so formuliert, dass niemand Gesundheitsangaben machen muss | besondere Kategorien von Daten |
| Sagen Sie im Formular, wer die Daten sieht und wann sie gelöscht werden | Transparenz |
| Entfernen Sie die Anmeldungen nach der Veranstaltung ([Aufbewahrung gesammelter Informationen](../formulare/aufbewahrung-gesammelter-informationen.md)) | Speicherbegrenzung |
| Beschränken Sie `infocollector/read` | wer es hat, sieht die Anmeldungen aller Veranstaltungen |

In der EU gilt die [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj)
([Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md)).

## Wann ein Buchungssystem sinnvoll ist

| Situation | Verwenden Sie |
|---|---|
| Begrenzte Plätze, die schnell voll sind | ein Buchungssystem mit Kapazität und Wartelisten |
| Kostenpflichtige Veranstaltungen | ein Buchungs- oder Zahlungssystem |
| Wiederkehrende Kurse mit vielen Terminen | ein System zur Kursbuchung |
| Kleine kostenlose Veranstaltungen, Informationsabende | der Information Collector genügt |

Verlinken Sie das Buchungssystem über das Attribut **Booking link** der Veranstaltung
([Eine Klasse für Veranstaltungen modellieren](eine-klasse-fuer-veranstaltungen-modellieren.md)).

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Formulare aufbauen](../formulare/README.md)
- [Barrierefreie Formulare](../formulare/barrierefreie-formulare.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
