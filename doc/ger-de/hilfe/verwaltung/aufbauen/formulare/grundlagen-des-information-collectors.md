---
title: Grundlagen des Information Collectors
audience: government, website-creator, admin
category: public-sector
order: 20
summary: Wie der Information Collector von Exponential für Formulare des öffentlichen Sektors funktioniert - sammelnde Attribute in einer Klasse, das Formular-Template mit Ziel content/action, die Typen form, feedback und poll in collect.ini, die Ergebnisseite oder Weiterleitung und wo Einsendungen gespeichert werden.
keywords: Information Collector, sammelndes Attribut, Formulartyp, Rückmeldung, Umfrage, Weiterleitung, Setup Gesammelte Informationen, information collector, collector attribute, content/action, ActionCollectInformation, collect.ini, form type, feedback, poll, Display, RedirectURL, CollectAnonymousData, Setup Collected information
type: help
---
# Grundlagen des Information Collectors

Diese Seite richtet sich an Site-Ersteller und Administratoren, die ihr erstes Formular für eine Site des öffentlichen
Sektors bauen. Sie fasst zusammen, wie der Information Collector funktioniert, mit den wichtigen Einstellungen, und
verweist auf die vollständigen Anleitungen:
[Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)
und [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md).

## Inhalt

1. [Wie er funktioniert](#wie-er-funktioniert)
2. [Die mitgelieferten Formularklassen](#die-mitgelieferten-formularklassen)
3. [Formulartypen und ihre Einstellungen](#formulartypen-und-ihre-einstellungen)
4. [Nach dem Senden](#nach-dem-senden)
5. [Ein erstes Formular, Schritt für Schritt](#ein-erstes-formular-schritt-für-schritt)

## Wie er funktioniert

1. Eine Inhaltsklasse hat Attribute, die im Klasseneditor als **Informationssammler** markiert sind. Besucher füllen
   diese aus; Redakteure füllen die anderen Attribute (einen Titel, eine Einleitung).
2. Ein Redakteur legt ein Objekt der Klasse an; seine vollständige Ansicht zeigt das Formular.
3. Das Formular sendet an `content/action` mit der Schaltfläche `ActionCollectInformation`; jedes sammelnde Attribut
   wird von seinem Datentyp geprüft (erforderlich, gültige E-Mail ...).
4. Bei Erfolg wird eine **Sammlung** gespeichert, eine Mail kann versendet werden, und der Besucher sieht eine
   Ergebnisseite oder wird weitergeleitet.
5. Mitarbeiter mit der Richtlinie `infocollector/read` lesen und exportieren die Sammlungen unter **Setup > Gesammelte
   Informationen** (`/infocollector/overview`).

## Die mitgelieferten Formularklassen

| Klasse | Bezeichner | Sammelnde Attribute |
|---|---|---|
| Feedback form | `feedback_form` | Sender name, Subject, Message (Textabschnitt), Email; dazu Name, Description und ein Recipient (Email), die Redakteure ausfüllen |
| Poll | `poll` | eine Optionsliste (`ezoption`) |
| Contact form | `ng_contact_form` | in den Demo-Inhaltsklassen des Standardpakets |
| Advanced feedback form | `cjw_feedback_form` | wo die CJW-Erweiterungen installiert sind |

Fügen Sie eine eigene Klasse hinzu, wenn Sie andere Felder brauchen
([Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)).

## Formulartypen und ihre Einstellungen

`settings/collect.ini` legt das Verhalten je Typ fest; der Typ eines Objekts kommt aus einem Attribut
`collection_type`, sonst aus `[InfoSettings] Type=form`:

| Block | Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|---|
| `InfoSettings` | `TypeList[]` | poll, form, feedback | bekannte Typen |
| `EmailSettings` | `SendEmail`, `SendEmailList[poll]` | eingeschaltet; für Umfragen ausgeschaltet | Mail bei Einsendung |
| `DisplaySettings` | `Display`, `RedirectURL` | `result`, `/` | Ergebnisseite oder Weiterleitung |
| `CollectionSettings` | `CollectAnonymousData` | eingeschaltet | nicht angemeldete Besucher dürfen senden |
| `CollectionSettings` | `CollectionUserData`, `CollectionUserDataList[poll]` | multiple; unique für Umfragen | eine Antwort je Benutzer bei Umfragen |

Ändern Sie sie in `settings/override/collect.ini.append.php` oder je Siteaccess und leeren Sie dann den INI-Cache
(`php bin/php/ezcache.php --clear-tag=ini`).

## Nach dem Senden

| Einstellung | Wirkung | Verwenden für |
|---|---|---|
| `Display=result` | das Ergebnis-Template des Typs zeigt die gesendeten Werte | Umfragen (Ergebnisse) |
| `Display=redirect` mit `RedirectURL` | der Besucher kommt auf eine Seite | Formulare: eine Dankesseite, die sagt, was als Nächstes geschieht und wann |

Eine Weiterleitung verhindert auch, dass ein Neuladen das Formular erneut sendet.

## Ein erstes Formular, Schritt für Schritt

1. Legen Sie im Baum einen Ordner „Kontakt“ an.
2. **Neu erstellen** > **Feedback form**: Name „Kontakt“, eine Beschreibung, die sagt, was mit der Nachricht geschieht
   und wie lange sie aufbewahrt wird, und das Empfängerpostfach des Teams.
3. **Zur Veröffentlichung schicken**; öffnen Sie es abgemeldet; senden Sie eine Testnachricht.
4. Öffnen Sie **Setup > Gesammelte Informationen**: Das Formular ist mit einer Sammlung aufgeführt.
5. Prüfen Sie, dass die Mail angekommen ist (testen Sie zuerst mit dem Datei-Transport:
   [Mail an Empfänger](mail-an-empfaenger.md)).

Für jedes Detail: [Ein Kontaktformular bauen, Schritt für Schritt](../../../../../eng-us/help/creators/website-creators/building-a-contact-form.md).

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [Barrierefreie Formulare](barrierefreie-formulare.md)
- [Formulareinsendungen lesen und exportieren](../../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
