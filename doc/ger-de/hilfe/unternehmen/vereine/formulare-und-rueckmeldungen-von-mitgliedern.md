---
title: Formulare und Rückmeldungen von Mitgliedern
audience: businesses, editor, website-creator
category: business-solutions
order: 40
summary: Wie eine kleine Gruppe mit Exponential Kontaktanfragen, Rückmeldungen, Umfrageantworten und Anmeldungen sammelt - das mitgelieferte Rückmeldeformular, die Typen form, feedback und poll, Sternebewertungen, wer Einsendungen erhält und liest, Spamschutz und das Löschen dessen, was Sie nicht mehr brauchen.
keywords: Rückmeldeformular, Kontaktformular, Umfrage, Befragung, Anmeldeformular, Informationssammler, gesammelte Informationen, Sternebewertung, CAPTCHA, Rückmeldungen von Mitgliedern, kleine Gruppe, feedback form, contact form, poll, survey, registration form, information collector, collected information, star rating, ezstarrating, CAPTCHA, member feedback, small group
type: help
---
# Formulare und Rückmeldungen von Mitgliedern

Diese Seite ist für den Schriftführer, den Web-Koordinator und die Ehrenamtlichen eines Vereins, einer Gesellschaft
oder einer kleinen Gruppe, die von Mitgliedern und Besuchern hören möchten. Sie zeigt, welche Arten von Formularen
Exponential mitbringt, wie sie die richtige Person erreichen und wie Sie die personenbezogenen Daten darin in Ordnung
halten. Die technische Einrichtung steht in
[Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md).

## Inhalt

1. [Arten von Formularen](#arten-von-formularen)
2. [Das mitgelieferte Rückmeldeformular](#das-mitgelieferte-rückmeldeformular)
3. [Umfragen unter Mitgliedern](#umfragen-unter-mitgliedern)
4. [Anmeldungen zu Veranstaltungen](#anmeldungen-zu-veranstaltungen)
5. [Sternebewertungen](#sternebewertungen)
6. [Wer benachrichtigt wird, wer lesen darf](#wer-benachrichtigt-wird-wer-lesen-darf)
7. [Spam](#spam)
8. [Aufräumen](#aufräumen)
9. [Checkliste für Formulare](#checkliste-für-formulare)

## Arten von Formularen

`settings/collect.ini` kennt drei Formulartypen, `form`, `feedback` und `poll`, und legt je Typ fest, ob eine Mail
versendet wird, was der Besucher nach dem Absenden sieht und ob anonyme Besucher absenden dürfen
([Formulare mit dem Informationssammler, Formulartypen](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md#form-types-form-feedback-poll)):

| Typ | Mail beim Absenden | Antworten pro Person | Geeignet für |
|---|---|---|---|
| `form` | Ja | Mehrere | Kontakt, Aufnahmeanträge, Anmeldungen |
| `feedback` | Ja | Mehrere | Rückmeldungen zu einer Veranstaltung oder zur Website |
| `poll` | Nein (`SendEmailList[poll]=disabled`) | Einmal pro Benutzer (`CollectionUserDataList[poll]=unique`) | Schnelle Abstimmungen unter Mitgliedern |

Anonyme Besucher dürfen Formulare standardmäßig absenden (`CollectAnonymousData=enabled`).

## Das mitgelieferte Rückmeldeformular

Die Klasse `feedback_form` hat Attribute, die als **Informationssammler** markiert sind: Name des Absenders, E-Mail,
Betreff und Nachricht. Ein Redakteur legt ein Objekt dieser Klasse an, und seine Seite zeigt das Formular
([Wie es funktioniert](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md#how-it-works)). Das
Paket `feedback_form` wird mit `ezwebin` ausgeliefert (`extension/ezwebin/packages/`). Für eine Gruppe ist das
anfangs meist das einzige nötige Formular: eines auf der Kontaktseite und eines auf der Beitrittsseite mit eigenem
Empfänger.

## Umfragen unter Mitgliedern

Eine Umfrage ist ein Formular des Typs `poll`: Mitglieder wählen eine Antwort, jeder Benutzer kann einmal antworten,
und das Ergebnis-Template der Umfrage (`content/collectedinfo/poll.tpl`) zeigt die Ergebnisse
([Formulareinsendungen lesen und exportieren, Ergebnisse auf der Website zeigen](../../../../eng-us/help/creators/website-creators/form-submissions.md#showing-results-on-the-site)).
„Einmal pro Benutzer“ wird pro Benutzerkonto gezählt, und die Dokumentation beschreibt nicht, wie Besucher behandelt
werden, die nicht angemeldet sind; lassen Sie Mitglieder sich daher bei einer Umfrage unter Mitgliedern anmelden (und
testen Sie es). Verwenden Sie Umfragen für Meinungen, nicht für verbindliche Abstimmungen, die Ihre Satzung regelt.

## Anmeldungen zu Veranstaltungen

Stellen Sie ein Formularobjekt mit den benötigten Feldern (Name, E-Mail, Anzahl der Personen, Ernährungswünsche) auf
die Veranstaltungsseite. Erheben Sie nur, was Sie für diese Veranstaltung brauchen, und entfernen Sie die Einsendungen
nach der Veranstaltung
([Formulareinsendungen lesen und exportieren](../../../../eng-us/help/creators/website-creators/form-submissions.md#removing-submissions)).
Zu den Veranstaltungsseiten selbst siehe [Veranstaltungen und Neuigkeiten für Gruppen](veranstaltungen-und-neuigkeiten-fuer-gruppen.md).

## Sternebewertungen

Die Erweiterung `ezstarrating` fügt einen Datentyp **„Star Rating“** hinzu: Besucher bewerten Objekte einer Klasse „mit
1 bis 5 Sternen“, jeder Benutzer darf einmal pro Sitzung abstimmen, und der Durchschnitt wird pro Objekt gespeichert
([ezstarrating](../../../../eng-us/features/6.0/extensions/ezstarrating.md)). Sie eignet sich auf einer Gruppen-Website zum
Bewerten von Vorträgen oder Rezepten; ein Werkzeug für Befragungen ist sie nicht.

## Wer benachrichtigt wird, wer lesen darf

- **Mail**: Der Standardempfänger ist `site.ini [InformationCollectionSettings] EmailReceiver`; die ezwebin-Mail-Templates
  verwenden ein Attribut `recipient` des Formularobjekts, wenn es eines hat, „sodass jedes Formular sein eigenes Team
  anschreiben kann“
  ([Mail an einen Empfänger](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md#mail-to-a-receiver)).
  Verwenden Sie eine Funktionsadresse, über die die Gruppe verfügt (schriftfuehrer@...), nicht die private Adresse
  eines Ehrenamtlichen, damit sie eine Übergabe übersteht.
- **Lesen**: Jede Einsendung wird unter **Setup > Gesammelte Informationen** aufbewahrt. „Für alles ist
  `infocollector/read` nötig. Geben Sie es den Personen, die die Formulare bearbeiten, nicht jedem Redakteur“
  ([Wer sie sehen darf](../../../../eng-us/help/creators/website-creators/form-submissions.md#who-may-see-them)).
- **Nach dem Absenden**: eine Weiterleitung auf eine Dankeseite (`Display=redirect`), damit ein Neuladen nicht erneut
  absendet.

## Spam

Formulare auf einer öffentlichen Website ziehen Spam an:

- das Formular-Token von `ezformtoken` weist gefälschte Übermittlungen ab und ist standardmäßig eingeschaltet;
- ein CAPTCHA (`recaptcha` oder `hcaptcha`) an der Formularklasse stoppt den meisten automatisierten Spam;
- Spam, der durchkommt, lässt sich unter **Setup > Gesammelte Informationen** gesammelt entfernen.

Quelle: [Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md). Nennen Sie den
CAPTCHA-Anbieter in Ihrer Datenschutzerklärung.

## Aufräumen

- **CSV exportieren** liefert eine Tabelle mit den Einsendungen eines Formulars; formelähnliche Werte werden
  entschärft, und jeder Export wird ohne die Werte im Audit-Trail festgehalten.
- **Ausgewählte entfernen** löscht Einsendungen nach einer Bestätigung; die Formulare bleiben erhalten.
- Entfernen Sie alte Einsendungen nach einem Zeitplan, den Sie benennen können, und löschen Sie Exporte, wenn Sie
  fertig sind.

Quelle: [Formulareinsendungen lesen und exportieren](../../../../eng-us/help/creators/website-creators/form-submissions.md). Die Regeln
für personenbezogene Daten in Formularen sind Grundlagen, keine Rechtsberatung; siehe
[Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md).

## Checkliste für Formulare

- [ ] Kontakt- und Beitrittsformular aus der mitgelieferten Klasse `feedback_form`, jedes mit seinem Empfänger.
- [ ] Empfänger sind Funktionsadressen, über die die Gruppe verfügt.
- [ ] Weiterleitung auf eine Dankeseite nach dem Absenden.
- [ ] Token eingeschaltet, CAPTCHA hinzugefügt, abgemeldet getestet.
- [ ] `infocollector/read` nur für die Personen, die antworten.
- [ ] Umfragen für Meinungen verwendet, mit angemeldeten Mitgliedern.
- [ ] Anmeldungen zu Veranstaltungen nach der Veranstaltung entfernt; ein Löschplan für den Rest.

## Verwandte Seiten

- [Vereine und kleine Gruppen](README.md)
- [Ehrenamtliche und Anmeldeformulare](../gemeinnuetzige/ehrenamtliche-und-anmeldeformulare.md)
- [Ein Kontaktformular Schritt für Schritt erstellen](../../../../eng-us/help/creators/website-creators/building-a-contact-form.md)
- [Gesammelte Informationen (Anleitung)](../../../anleitungen/gesammelte-informationen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
