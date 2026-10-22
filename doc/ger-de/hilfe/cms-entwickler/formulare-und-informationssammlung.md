---
title: Formulare und Informationssammlung
audience: cms-developer
category: cms-development
order: 34
summary: Für CMS-Entwickler, die Kontaktformulare, Umfragen und Formulare zur Lead-Gewinnung ohne Code mit der Informationssammlung bauen. Die Seite behandelt Sammler-Attribute, die Templates für Formular und Ergebnis, die Einstellungen in collect.ini für E-Mail, Anzeige, anonyme und wiederholte Antworten, das Lesen und Exportieren von Sammlungen, Spam und Datenschutz und wann ein eigenes Modul das bessere Werkzeug ist.
keywords: Formulare, Kontaktformular, Feedback-Formular, Umfrage, Informationssammlung, Informationssammler, collect.ini, ActionCollectInformation, content/action, collectedinfo, collectedinfomail, infocollector, gesammelte Informationen, CSV-Export, bccie, Captcha, Datenschutz, forms, contact form, feedback form, poll, information collection, information collector, collected information, CSV export, privacy
type: help
---
# Formulare und Informationssammlung

Diese Seite richtet sich an Entwickler, die Formulare auf einer Website brauchen: Kontakt, Feedback, Umfragen,
Anmeldung zu Veranstaltungen, Lead-Gewinnung. Exponential baut die meisten davon aus dem Inhaltsmodell: Ein Formular
ist ein Objekt einer Klasse, deren Attribute als **Informationssammler** gekennzeichnet sind; Redakteure legen
Formulare an und ändern sie wie jeden Inhalt, und der Kernel speichert und versendet die Antworten.

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Eine Formularklasse](#eine-formularklasse)
3. [Das Formular-Template](#das-formular-template)
4. [Nach dem Absenden: Ergebnisseite und E-Mail](#nach-dem-absenden-ergebnisseite-und-e-mail)
5. [Einstellungen je Formulartyp](#einstellungen-je-formulartyp)
6. [Antworten lesen und exportieren](#antworten-lesen-und-exportieren)
7. [Spam und Datenschutz](#spam-und-datenschutz)
8. [Wann Sie stattdessen ein Modul schreiben](#wann-sie-stattdessen-ein-modul-schreiben)

## Wie es funktioniert

```text
form object (class with collector attributes), full view
  -> <form> posts to /content/action with ActionCollectInformation
     -> the kernel validates each collector attribute, stores one collection
        -> result page (content/collectedinfo/<type>.tpl) and, if enabled, a mail (content/collectedinfomail/<type>.tpl)
```

Antworten sind keine Inhaltsobjekte: Sie sind Sammlungen in eigenen Tabellen, die unter **Setup** > **Gesammelte
Informationen** gelesen werden ([Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md)).

## Eine Formularklasse

Legen Sie eine Klasse an (zum Beispiel `exp_contact_form`) mit:

- normalen Attributen für das, was Redakteure schreiben: einen Titel, eine Einleitung (`ezxmltext`) und, falls Sie sie
  verwenden, die Attribute, die `collect.ini` je Objekt liest (siehe unten);
- **Informationssammler**-Attributen für das, was Besucher ausfüllen: Textzeile für einen Namen, E-Mail für die
  Adresse, Textabschnitt für die Nachricht, Auswahlliste für eine Wahl, Kontrollkästchen für eine Einwilligung.

Kennzeichnen Sie die Sammler-Attribute als **Erforderlich**, wo das Formular sie braucht; der Kernel lehnt
unvollständige Antworten dann mit einer Prüfmeldung ab.

## Das Formular-Template

Die Full View des Formularobjekts gibt das Formular aus. Jedes Sammler-Attribut wird mit `attribute_view_gui`
gezeichnet, das das Collect-Template des Datentyps verwendet und ein Eingabefeld ausgibt; die Absende-Schaltfläche heißt
`ActionCollectInformation`, mit den verborgenen Feldern `ContentNodeID`, `ContentObjectID` und `ViewMode`. Ein
vollständiges Template mit Beschriftungen und Prüfmeldungen:
[Formulare in Templates](../designer/vorlagen/formulare-in-templates.md#das-formular-template). Gehen Sie vom
Formular-Template des Designs aus, auf dem Sie aufbauen, damit die verborgenen Felder und das Formular-Token stimmen,
und geben Sie Eingabefeldern IDs für ihre Beschriftungen ([Barrierefreie Formulare](../designer/theme-design/barrierefreie-formulare.md)).

## Nach dem Absenden: Ergebnisseite und E-Mail

Die Ergebnisseite ist `content/collectedinfo/<type>.tpl` und die E-Mail `content/collectedinfomail/<type>.tpl`, wobei
der Typ aus `collect.ini` kommt (mitgeliefert: `form`, `poll`, `feedback`). Überschreiben Sie beide in Ihrem Design: die
Ergebnisseite für die Dankesnachricht, die E-Mail für Betreff und Text (gesetzt mit
`{set-block scope=root variable=subject}`). Testen Sie E-Mails auf Entwicklungsrechnern mit einem Empfänger unter
`example.invalid` und einem Datei-Transport.

## Einstellungen je Formulartyp

`settings/collect.ini` bestimmt das Verhalten je Typ und lässt ein Formularobjekt es über ein eigenes Attribut
überschreiben:

| Gruppe | Einstellung | Mitgeliefert | Mit dem Attribut |
|---|---|---|---|
| `[InfoSettings]` | `Type`, `TypeList[]` | `form`; `poll`, `form`, `feedback` | `collection_type` |
| `[EmailSettings]` | `SendEmail`, `SendEmailList[poll]` | `enabled`; Umfragen `disabled` | `collection_email` |
| `[DisplaySettings]` | `Display`, `RedirectURL` | `result`, `/` | `collection_display`, `collection_redirect` |
| `[CollectionSettings]` | `CollectAnonymousData` | `enabled` | `collection_anonymous` |
| `[CollectionSettings]` | `CollectionUserData` | `multiple`; Umfragen `unique` | `collection_userdata` |
| `[CollectionSettings]` | `CollectSensitiveData` | `true`; Umfragen `false` | |

So kann eine Klasse vielen Formularen dienen, und ein Redakteur kann zum Beispiel je Formular wählen, wohin die
Antworten gehen. Legen Sie Ihre Änderungen in `collect.ini.append.php` Ihrer Erweiterung ab.

## Antworten lesen und exportieren

**Setup** > **Gesammelte Informationen** listet die Objekte, die gesammelt haben, mit den Sammlungen jedes Objekts,
einem CSV-Export und dem Entfernen nach Bestätigung; dafür ist die Richtlinie `infocollector/read` nötig. In Templates
lesen die Fetch-Funktionen für Inhalte `collected_info_count`, `collected_info_count_list`, `collected_info_collection`
und `collected_info_list` Antworten, etwa um Umfrageergebnisse zu zeigen. Die Erweiterung `bccie` der
Standarddistribution exportiert gesammelte Informationen ebenfalls
([Inhalte abbilden: Formulare](../../installation/migration/inhaltsmodell-zuordnen.md#9-kommentare-formulare-produkte)).

## Spam und Datenschutz

- Fügen Sie öffentlichen Formularen einen Captcha-Datentyp (`recaptcha` oder `hcaptcha`) als Sammler-Attribut hinzu
  ([Datentypen wählen](datentypen-waehlen.md)).
- Behalten Sie das Formular-Token (ezformtoken) im Template; entfernen Sie keine verborgenen Felder, die das Original
  ausgibt.
- Erheben Sie nur, was das Formular braucht, sagen Sie im Formular, was mit den Antworten geschieht, und entfernen Sie
  alte Sammlungen nach Plan; Antworten sind personenbezogene Daten ([Datenschutzstandards](../../standards/datenschutz.md)).
- Geben Sie Antworten nie unmaskiert wieder aus.

## Wann Sie stattdessen ein Modul schreiben

Die Informationssammlung passt zu Formularen, deren Antworten gespeichert und versendet werden. Wenn ein Formular
Inhalte anlegen (ein Mitglied reicht einen Artikel ein), ein anderes System aufrufen oder einem mehrstufigen Ablauf
folgen muss, schreiben Sie in Ihrer Erweiterung eine Modul-View mit einer Richtlinie, oder lassen Sie Benutzer Inhalte
mit `content/create`, nach Rolle eingeschränkt, anlegen
([Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md),
[Formulare und Aktionen](../trainer/entwickler/modul-06-module-und-views/formulare-und-aktionen.md)).

## Verwandte Seiten

- [Gesammelte Informationen](../../anleitungen/gesammelte-informationen.md)
- [Workflows und Auslöser](workflows-und-ausloeser.md)
- [Benutzer und Registrierung](benutzer-und-registrierung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
