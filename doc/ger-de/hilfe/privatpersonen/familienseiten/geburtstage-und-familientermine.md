---
title: Geburtstage und Familientermine
audience: individual, website-creator
category: personal-sites
order: 1704
summary: Was Exponential für das Festhalten von Geburtstagen, Jahrestagen und Familientreffen bietet - den Datentyp ezbirthday der Erweiterung birthday, der ein Geburtsdatum speichert und sonst nichts, die Klassen Event und Event calendar für Treffen, was nicht verfügbar ist (keine Erinnerungen, keine Glückwunsch-E-Mails) und die Privatsphäre von Geburtsdaten.
keywords: Geburtstag, ezbirthday, Erweiterung birthday, Geburtsdatum, Jahrestage, Familientermine, Veranstaltung, Veranstaltungskalender, Erinnerungen, Glückwünsche, CSV-Export, xrowextract, Privatsphäre, Familienseite, birthday, date of birth, anniversaries, reminders, privacy
type: help
---
# Geburtstage und Familientermine

Diese Seite ist für Privatpersonen, deren Familienseite Geburtstage, Jahrestage und Treffen enthalten soll. Sie sagt
klar, was es gibt, denn der Name einer Erweiterung verspricht mehr, als sie hält.

## Inhalt

1. [Die Erweiterung birthday](#die-erweiterung-birthday)
2. [Was sie nicht tut](#was-sie-nicht-tut)
3. [Geburtstage festhalten](#geburtstage-festhalten)
4. [Treffen und Jahrestage als Veranstaltungen](#treffen-und-jahrestage-als-veranstaltungen)
5. [Eine Terminseite ohne Programmierung](#eine-terminseite-ohne-programmierung)
6. [Geburtsdaten sind personenbezogene Daten](#geburtsdaten-sind-personenbezogene-daten)

## Die Erweiterung birthday

`birthday` („eZ Birthday", Composer-Paket `se7enxweb/birthday`, in der `composer.json` im Wurzelverzeichnis) bietet
genau eines: den Datentyp **`ezbirthday`**, ein Feld, das ein Geburtsdatum so speichert, dass es sortiert und
durchsucht werden kann, mit den üblichen Templates zum Bearbeiten und Anzeigen. Sie registriert außerdem einen
CSV-Export-Handler, den das Exportwerkzeug `xrowextract` nutzt
([birthday: ein Datentyp für Geburtstage](../../../funktionen/6.0/erweiterungen/birthday.md)). Die `composer.json` der
Erweiterung beschreibt sie als Datentyp, der „das Datum im Format YYYY-MM-DD speichert".

So nutzen Sie sie: Aktivieren Sie die Erweiterung, fügen Sie unter **Setup > Klassen** einer Klasse ein Attribut vom
Datentyp birthday hinzu und tragen Sie in Objekten dieser Klasse Daten ein.

## Was sie nicht tut

Im Code der Erweiterung geprüft (`extension/birthday`: nur `datatypes/ezbirthday`, `classes/parsers`, Templates und
Einstellungen):

- **Keine Erinnerungen** an Sie oder andere vor einem Geburtstag.
- **Keine Glückwunsch-E-Mails** am Tag selbst.
- **Keine Kalenderansicht** kommender Geburtstage.
- **Standardmäßig keine Anzeige des Alters.**

Jedes davon wäre Template- oder Cronjob-Arbeit für jemanden, der Exponential-Code schreibt
([Wann Sie sich Hilfe holen sollten](../im-betrieb/wann-sie-sich-hilfe-holen-sollten.md)). Der Kalender Ihres Smartphones ist ein
völlig ausreichendes Erinnerungssystem.

## Geburtstage festhalten

Zwei Orte können ein Geburtsdatum aufnehmen:

| Wo | Wie | Passt zu |
|---|---|---|
| In den Benutzerkonten der Verwandten | Der Klasse *User* ein Geburtstagsfeld hinzufügen | Verwandten mit Konto |
| In einer eigenen Klasse „Verwandte" | Eine Klasse mit Name, Foto, Feld `ezbirthday`, einer kurzen Notiz | Auch Verwandten ohne Konto und Vorfahren |

Ein Feld in der Klasse *User* ändert jedes Kontoformular, auch die Registrierung; überlegen Sie, bevor Sie es tun
([Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md)).

## Treffen und Jahrestage als Veranstaltungen

Für Familientreffen, Feste und Jahrestage ist die mitgelieferte Klasse *Event* das richtige Werkzeug. Ihre Felder
sind Titel, Kurztitel, Text, Kategorie, Beginn und Ende (`class-event.xml` im Paket `sevenx_classes`).
Veranstaltungen liegen in einem *Event calendar*, der sie nach Monat anzeigt
([Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md)).

| Verwendung | Wie |
|---|---|
| Ein Familientreffen | Ein *Event* mit Datum, Ort und was mitzubringen ist |
| Ein Hochzeitstag | Jedes Jahr ein *Event* oder ein Artikel, der sie auflistet |
| Anmeldung zu einem Treffen | Ein Formular neben der Veranstaltung ([Formulare, Umfragen und Anmeldungen](../vereinsseiten/formulare-umfragen-und-anmeldungen.md)) |

Einen Export von Veranstaltungen in Smartphone-Kalender gibt es von Haus aus nicht.

## Eine Terminseite ohne Programmierung

Das Einfachste, was funktioniert: ein *Article* namens „Familientermine" mit einer Tabelle aus Namen und Daten (nur
Tag und Monat), nach Monat sortiert, im privaten Bereich. Sie aktualisieren ihn einmal im Jahr von Hand. Das ist
weniger raffiniert als eine Datenbank und viel leichter privat zu halten.

## Geburtsdaten sind personenbezogene Daten

Ein vollständiges Geburtsdatum ist eine der Angaben, mit denen man sich als jemand anderes ausgibt und
Sicherheitsfragen beantwortet.

- Speichern Sie **nur Tag und Monat**, wo das Jahr nicht gebraucht wird.
- Halten Sie Geburtstage **im privaten Bereich**, nie auf öffentlichen Seiten.
- Halten Sie die vollständigen Geburtsdaten von Kindern nur fest, wenn deren Eltern zustimmen.
- Denken Sie daran, dass ein Export (CSV) einer Klasse mit Geburtstagen eine Liste personenbezogener Daten ist;
  halten Sie ihn von gemeinsam genutzten Laufwerken fern.
- Löschen Sie die Daten von Personen, die darum bitten.

Siehe [Privatsphäre für Ihre Familie](privatsphaere-fuer-ihre-familie.md) und
[Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md).

## Verwandte Seiten

- [Familienseiten](README.md)
- [birthday: ein Datentyp für Geburtstage](../../../funktionen/6.0/erweiterungen/birthday.md)
- [Veranstaltungen und Kalender für Vereine](../vereinsseiten/veranstaltungen-und-kalender-fuer-vereine.md)
- [Was es gibt und was nicht](../was-es-gibt-und-was-nicht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
