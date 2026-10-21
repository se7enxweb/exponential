---
title: Ein Antragsformular mit dem Informationssammler
audience: government, website-creator, admin
category: public-sector
order: 60
summary: Ein Online-Formular für Anträge auf Informationszugang in Exponential mit dem Informationssammler bauen - die Klasse, die Felder, nach denen Sie fragen und nicht fragen, E-Mail an das zuständige Team, die Bestätigungsseite, Spamschutz und Datenschutztext.
keywords: Antragsformular, Antrag auf Informationszugang, Informationssammler, collect.ini, Feedback-Formular, Formular, EmailReceiver, Bestätigung, Datenschutzhinweis, Spamschutz, Formular-Token, request form, FOI request, information collector, feedback form, confirmation, privacy notice, spam protection, form token
type: help
---
# Ein Antragsformular mit dem Informationssammler

Diese Seite richtet sich an Websiteersteller und Administratoren, die der Öffentlichkeit ein Online-Formular für
Anträge auf Informationszugang anbieten. Exponential baut Formulare mit dem **Informationssammler**: eine Klasse, deren
Attribute als *Informationssammler* markiert sind, ein Objekt davon, das als Formular angezeigt wird, und eine
gespeicherte *Sammlung* für jede Einsendung
([Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie bauen](#bevor-sie-bauen)
2. [Die Formularklasse](#die-formularklasse)
3. [E-Mail an das Team](#e-mail-an-das-team)
4. [Nach dem Absenden](#nach-dem-absenden)
5. [Spam und Missbrauch](#spam-und-missbrauch)
6. [Datenschutz](#datenschutz)
7. [Testen](#testen)

## Bevor Sie bauen

- Prüfen Sie, ob Ihr Gesetz Anträge auf jedem Weg erlaubt (Post, E-Mail, Papier). Ein Webformular ist meist ein
  zusätzlicher Weg; sagen Sie das auf der Seite.
- Legen Sie fest, welche Angaben mindestens nötig sind. Viele Gesetze verlangen nur eine Möglichkeit zur Antwort und
  eine Beschreibung der Informationen.
- Legen Sie fest, wer Anträge erhält und wo der Vorgang verfolgt wird: Exponential speichert Einsendungen, hat aber
  keine Vorgangsverfolgung mit Fristen (siehe „Anträge aus gesammelten Informationen bearbeiten“).

## Die Formularklasse

Legen Sie unter **Setup > Klassen** eine Klasse `information_request` an oder passen Sie das mitgelieferte
**Feedback form** (`feedback_form`) an:

| Attribut | Datentyp | Informationssammler | Erforderlich |
|---|---|---|---|
| Einleitung | Formatierter Text | nein (von der Redaktion geschrieben) | |
| Empfänger | E-Mail | nein (das Teampostfach, vom E-Mail-Template verwendet) | |
| Ihr Name | Textzeile | ja | wie Ihr Gesetz es verlangt |
| E-Mail-Adresse für die Antwort | E-Mail | ja | ja |
| Postanschrift (optional) | Textabschnitt | ja | nein |
| Die Informationen, die Sie erbitten | Textabschnitt | ja | ja |
| Gewünschtes Format | Auswahl | ja | nein |

Fragen Sie nicht nach Gründen, Ausweisdokumenten oder Telefonnummern, es sei denn, das Gesetz verlangt sie.

Legen Sie im Bereich „Informationen anfordern“ ein Objekt der Klasse an und schreiben Sie die Einleitung: was
angefordert werden kann, die Antwortfrist, die Ihr Gesetz vorgibt, andere Wege, was mit den Daten geschieht.

## E-Mail an das Team

`site.ini [InformationCollectionSettings] EmailReceiver` ist der Standardempfänger. Die E-Mail-Templates von ezwebin
nehmen den Empfänger aus einem Attribut `recipient` des Formularobjekts, wenn es eines hat, sodass dieses Formular an
das Postfach des Teams für Informationsfreiheit senden kann. E-Mail-Einstellungen und der Dateitransport zum Testen:
[10.4 E-Mail](../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand).

`settings/collect.ini` entscheidet pro Formulartyp, ob E-Mail versendet wird (`[EmailSettings] SendEmail`) und was der
Besucher danach sieht (`[DisplaySettings] Display`, `RedirectURL`).

## Nach dem Absenden

Verwenden Sie `Display=redirect` auf eine Dankesseite, damit ein Neuladen nicht doppelt sendet. Die Dankesseite sagt,
wann die antragstellende Person eine Antwort erwarten kann und wie sie nachfragen kann. Wenn Sie ein Aktenzeichen
vergeben, stammt es aus Ihrem Vorgangssystem; die ID der Sammlung ist intern.

## Spam und Missbrauch

- Lassen Sie die Erweiterung `ezformtoken` aktiv; sie schützt POST-Formulare.
- CAPTCHA-Erweiterungen (`recaptcha`, `hcaptcha`) senden Besucherdaten an einen Drittanbieter und brauchen einen
  Datenschutzhinweis ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md),
  [Datenschutzstandards](../../../../eng-us/standards/privacy.md)).
- `[CollectionSettings] CollectAnonymousData=enabled` lässt nicht angemeldete Besucher senden; lassen Sie es für ein
  öffentliches Antragsformular eingeschaltet.

## Datenschutz

Sagen Sie neben dem Formular, was erhoben wird, warum, wer es erhält, wie lange es aufbewahrt wird und wie man seine
Löschung verlangen kann. Beschränken Sie die Richtlinie `infocollector/read` auf das Team, das Anträge bearbeitet. Der
Datenschutzstandard führt auf, dass Formulare speichern, was Besucher eingeben, und dass Einwilligungstexte und
Aufbewahrungsregeln der Website überlassen bleiben ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#known-gaps)).

## Testen

1. Senden Sie abgemeldet einen Antrag mit einer Testadresse auf einer Domain, die Sie kontrollieren.
2. Prüfen Sie, ob die E-Mail angekommen ist (oder beim Dateitransport die Datei im E-Mail-Verzeichnis).
3. Prüfen Sie die Einsendung unter **Setup > Gesammelte Informationen**.
4. Entfernen Sie die Testeinsendung dort.

## Verwandte Seiten

- [Ein Kontaktformular bauen](../../../../eng-us/help/creators/website-creators/building-a-contact-form.md)
- [Formulareinsendungen lesen und exportieren](../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
