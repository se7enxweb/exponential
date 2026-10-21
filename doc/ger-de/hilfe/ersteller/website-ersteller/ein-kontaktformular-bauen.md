---
title: Ein Kontaktformular bauen
audience: website-creator, editor
category: website-creation
order: 460
summary: Ein vollständiges Kontaktformular in zehn Schritten - Klasse mit Sammelfeldern, das Formularobjekt, sein Template, Mail an Ihr Team, eine Weiterleitung auf eine Dankeseite, Spamschutz, Rechte für Besucher und ein abschließender Test.
keywords: Kontaktformular, Feedbackformular, Anleitung, Informationssammler, Dankeseite, Weiterleitung, Website-Ersteller, contact form, feedback form, tutorial, information collector, collect.ini, thank you page, redirect, EmailReceiver, recaptcha, hcaptcha, website creator
type: help
---
# Ein Kontaktformular bauen

Diese Seite richtet sich an Website-Ersteller, die ein funktionierendes Kontaktformular brauchen. Sie verwendet den
Informationssammler ([Formulare mit dem Informationssammler](formulare-mit-dem-informationssammler.md)). Wenn Ihr
Seitenpaket die Klasse **Feedback form** (`feedback_form`) mitliefert, sind die Schritte 1 und 2 bereits erledigt;
beginnen Sie bei Schritt 3.

## Schritte

**1. Die Klasse.** Unter **Setup > Klassen > Content > Neue Klasse**: Name `Contact form`, Bezeichner
`contact_form`, Container aus. Attribute:

| Name | Bezeichner | Datentyp | Pflichtfeld | Informationssammler |
|---|---|---|---|---|
| Title | `title` | Textzeile | ja | nein |
| Introduction | `description` | Formatierter Text | nein | nein |
| Your name | `sender_name` | Textzeile | ja | ja |
| Your e-mail | `email` | E-Mail | ja | ja |
| Message | `message` | Textblock | ja | ja |

Muster für Objektnamen: `<title>`.

**2. Spamschutz.** Fügen Sie ein reCAPTCHA- oder hCaptcha-Attribut hinzu, wenn eine dieser Erweiterungen aktiv ist
([Spamschutz für Formulare](spamschutz-fuer-formulare.md)).

**3. Das Formularobjekt.** Legen Sie unter dem Wurzelordner ein Contact form namens „Kontakt“ an, schreiben Sie eine
Einleitung, veröffentlichen Sie es.

**4. Das Template.** Kopieren Sie das mitgelieferte Template des Feedbackformulars als
`override/templates/full/contact_form.tpl` in Ihr Design, benennen Sie die Felder nach Ihren Bezeichnern um und fügen Sie
die Override-Regel hinzu:

```ini
# settings/siteaccess/<site>/override.ini.append.php
[full_contact_form]
Source=node/view/full.tpl
MatchFile=full/contact_form.tpl
Subdir=templates
Match[class_identifier]=contact_form
```

Geben Sie jedem Eingabefeld ein sichtbares `<label>`; kennzeichnen Sie Pflichtfelder im Text, nicht nur durch Farbe.

**5. Mail.** Legen Sie den Empfänger fest:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[InformationCollectionSettings]
EmailReceiver=team@example.com
```

und überschreiben Sie `content/collectedinfomail/form.tpl`, wenn die Mail anders aussehen soll.

**6. Dankeseite.** Legen Sie eine Seite „Danke“ an und leiten Sie darauf weiter:

```ini
# settings/siteaccess/<site>/collect.ini.append.php
[DisplaySettings]
Display=redirect
RedirectURL=/Thank-you
```

**7. Besucher dürfen senden.** Anonymous braucht `content/read` auf dem Formular (die Sektion Standard genügt), und die
Ansicht `content/collectinformation` verwendet die Funktion read, also ist nichts weiter nötig. Lassen Sie
`CollectAnonymousData=enabled` stehen.

**8. Caches.** `php bin/php/ezcache.php --clear-id=template-override --allow-root-user`, dann `--clear-tag=template`
und `--clear-tag=content`.

**9. Abgemeldet testen.** Senden Sie das Formular mit einem leeren Pflichtfeld (daneben erscheint eine Fehlermeldung),
dann korrekt (die Dankeseite erscheint). Mit dem Datei-Mailtransport prüfen Sie die Mail im Mailverzeichnis.

**10. Die Einsendung prüfen** unter **Setup > Gesammelte Informationen**: Das Objekt Kontakt hat eine Sammlung.

## Checkliste

- [ ] Jedes Eingabefeld hat eine Beschriftung; Fehler werden neben dem Feld angesagt.
- [ ] Der Spamschutz ist eingeschaltet.
- [ ] Der Datenschutzhinweis sagt, was mit den Daten geschieht.
- [ ] Die Mail kommt an der Team-Adresse an (bei eingeschaltetem echtem Mailversand).
- [ ] Ein Neuladen der Dankeseite sendet nicht erneut.

## Verwandte Seiten

- [Formulare mit dem Informationssammler](formulare-mit-dem-informationssammler.md)
- [Formulareinsendungen](formulareinsendungen.md)
- [Template-Overrides](template-overrides.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
