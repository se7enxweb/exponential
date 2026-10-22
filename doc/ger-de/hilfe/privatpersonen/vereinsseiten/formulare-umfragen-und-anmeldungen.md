---
title: Formulare, Umfragen und Anmeldungen
audience: individual, website-creator, customer
category: personal-sites
order: 1715
summary: Wie eine ehrenamtliche Person auf einer Vereins- oder Familienseite mit Exponential über den Informationssammler Anmeldungen, Antworten und Stimmen sammelt - die mitgelieferten Klassen Feedback form und Poll, Formulartypen in collect.ini, eine Stimme pro Benutzer bei Umfragen, wo Einsendungen gespeichert werden, E-Mail an einen Empfänger, Spamschutz und Datenschutz.
keywords: Formulare, Anmeldung, Anmeldeformular, Umfrage, Abstimmung, Befragung, Feedback form, Klasse Poll, Informationssammler, collect.ini, Formulartypen, Einsendungen, gesammelte Informationen, E-Mail-Empfänger, CAPTCHA, Datenschutz, Verein, forms, sign-up, poll, vote, information collector, submissions, privacy
type: help
---
# Formulare, Umfragen und Anmeldungen

Diese Seite ist für Privatpersonen, die auf ihrer Website etwas abfragen müssen: „Wer kommt zum Sommerausflug?",
„Welcher Termin passt Ihnen?", „Schicken Sie uns Ihre Frage". Das erledigt der **Informationssammler** von
Exponential: Felder einer Klasse lassen sich so markieren, dass Besucher sie ausfüllen, und die Antworten werden
gespeichert und können per E-Mail verschickt werden
([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).

## Inhalt

1. [Was mitgeliefert wird](#was-mitgeliefert-wird)
2. [Formulartypen](#formulartypen)
3. [Ein Anmeldeformular für eine Veranstaltung](#ein-anmeldeformular-für-eine-veranstaltung)
4. [Eine Umfrage](#eine-umfrage)
5. [Die Antworten lesen](#die-antworten-lesen)
6. [Spam](#spam)
7. [Datenschutz](#datenschutz)
8. [Grenzen](#grenzen)

## Was mitgeliefert wird

| Klasse | Felder für Besucher | Verwendung |
|---|---|---|
| *Feedback form* | Name des Absenders, Betreff, Nachricht, E-Mail (Informationssammler); Empfänger legen Sie fest | Kontakt, Fragen, einfache Anmeldungen |
| *Poll* | eine Frage mit Antwortmöglichkeiten (`ezoption`, Informationssammler) | Eine Frage, mehrere Antworten |

Quellen: `class-feedback_form.xml` und `class-poll.xml` im Paket `sevenx_classes`.

## Formulartypen

`settings/collect.ini` kennt drei Typen, `form`, `feedback` und `poll`, und legt pro Typ fest, ob eine E-Mail
verschickt wird und ob ein Benutzer einmal oder mehrfach antworten darf. Ausgeliefert ist: E-Mail beim Absenden
aktiviert, bei Umfragen aber deaktiviert; eine Umfrage kann pro Benutzer einmal beantwortet werden
([Formulartypen](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md#formulartypen-form-feedback-poll)).

## Ein Anmeldeformular für eine Veranstaltung

Der schnellste Weg, ohne eine Klasse zu ändern:

1. Legen Sie neben der Veranstaltung (oder darin) ein *Feedback form* namens „Anmeldung: Sommerausflug" an.
2. Schreiben Sie in die Beschreibung, was in die Nachricht gehört: Personenzahl, Essenswünsche.
3. Setzen Sie als Empfänger die Adresse der organisierenden Person.
4. Veröffentlichen Sie es und verlinken Sie es von der Veranstaltung aus.

Wenn Sie richtige Felder brauchen (Anzahl der Gäste, eine Menüauswahl), ist eine eigene Klasse mit Feldern für den
Informationssammler die saubere Lösung; das ist Arbeit für Website-Ersteller
([Ein Kontaktformular bauen](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md),
[Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)).

Für Anmeldungen nur für Mitglieder legen Sie das Formular in den Mitgliederbereich: Nur angemeldete Mitglieder können
es lesen und absenden.

## Eine Umfrage

1. Legen Sie eine *Poll* dort an, wo sie erscheinen soll.
2. Schreiben Sie die Frage und die Antwortmöglichkeiten.
3. Veröffentlichen. Besucher wählen eine Möglichkeit und sehen die Ergebnisseite.

Weil Umfragen standardmäßig einmal pro Benutzer beantwortet werden, zählt eine Umfrage im Mitgliederbereich eine
Stimme pro Mitglied. Eine öffentliche Umfrage beantworten anonyme Besucher; behandeln Sie ihr Ergebnis also als
Stimmungsbild, nicht als Entscheidung.

## Die Antworten lesen

| Wo | Was |
|---|---|
| Das Postfach des Empfängers | Eine E-Mail pro Einsendung (Formulare und Feedback, wenn E-Mail funktioniert) |
| Die Administration | Jede Einsendung, gespeichert ([Formulareinsendungen](../../ersteller/website-ersteller/formulareinsendungen.md), [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) |

Exportieren oder kopieren Sie die Antworten, die Sie brauchen, und löschen Sie alte Einsendungen nach einem festen
Plan.

## Spam

Öffentliche Formulare ziehen Bots an. Nutzen Sie den Formular-Token (`ezformtoken`, in der Standardinstallation),
fügen Sie der Klasse ein CAPTCHA-Feld (`recaptcha` oder `hcaptcha`) hinzu oder behalten Sie das Formular im
Mitgliederbereich ([Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)).

## Datenschutz

- Fragen Sie nur nach dem, was Sie brauchen.
- Schreiben Sie neben das Formular, was gespeichert wird, warum und wie lange ([Einwilligung und Formulare](../../unternehmen/sicherheit/einwilligung-und-formulare.md)).
- Löschen Sie Einsendungen nach der Veranstaltung.
- Anmeldelisten mit Namen sind personenbezogene Daten; veröffentlichen Sie sie nicht ohne Einwilligung.

## Grenzen

- Keine Bezahlung in Formularen.
- Keine Begrenzung der Platzzahl (wer zuerst kommt) ohne eigene Entwicklung.
- Das mitgelieferte Feedbackformular schickt der angemeldeten Person keine automatische Bestätigung; die E-Mail geht
  an den Empfänger.
- Keine bedingten Fragen oder mehrseitigen Formulare von Haus aus.

## Verwandte Seiten

- [Vereinsseiten](README.md)
- [Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)
- [Formulare und Rückmeldungen von Mitgliedern](../../unternehmen/vereine/formulare-und-rueckmeldungen-von-mitgliedern.md)
- [Über-mich-Seite und Kontaktformular](../persoenliche-seiten/ueber-mich-seite-und-kontaktformular.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
