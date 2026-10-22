---
title: Ehrenamtliche
audience: non-profit, businesses, editor, website-creator
category: non-profits
order: 10
summary: Alles, was eine Non-Profit-Organisation auf ihrer Exponential-Website mit Ehrenamtlichen tut - sie mit einem Formular des Informationssammlers gewinnen, geschützt durch ein Formular-Token und hCaptcha oder reCAPTCHA, Bewerbungen bearbeiten, ein Mitgliederbereich für aktive Ehrenamtliche und Ehrenamtliche, die die Website mit Rollen, Bereichen, Freigabe, Versionen und Zwei-Faktor-Anmeldung sicher bearbeiten.
keywords: Ehrenamtliche, Ehrenamt, Gewinnung von Ehrenamtlichen, Anmeldeformular, Informationssammler, hcaptcha, recaptcha, enhancedezbinaryfile, Bewerbungen, Mitgliederbereich, ehrenamtliche Redakteure, Rollen, Schulung, volunteers, volunteer recruitment, sign-up form, information collector, applications, members area, volunteer editors, roles, training
type: help
---
# Ehrenamtliche

Dieser Ordner richtet sich an Koordinatoren für Ehrenamtliche, Mitarbeitende und die Website-Ersteller. Ehrenamtliche
begegnen Ihrer Website zweimal: zuerst als Bewerber, die ein Formular ausfüllen, später vielleicht als Mitglieder
eines Bereichs für Ehrenamtliche oder als Redakteure der Website selbst. Jede Etappe hat eigene Seiten.

## Inhalt

1. [Was Exponential bietet](#was-exponential-bietet)
2. [Die Seiten dieses Ordners](#die-seiten-dieses-ordners)
3. [Der Weg der Ehrenamtlichen auf der Website](#der-weg-der-ehrenamtlichen-auf-der-website)
4. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)

## Was Exponential bietet

| Bedarf | Was es gibt | Quelle |
|---|---|---|
| Ein Anmeldeformular | Der Informationssammler: Klassenattribute, die als Informationssammler markiert sind, werden zu einem Formular; Einsendungen werden gespeichert | [Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md) |
| Spamschutz | `ezformtoken` (standardmäßig aktiv), die Datentypen `hcaptcha` und `recaptcha` | [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md) |
| Uploads mit dem Formular | `enhancedezbinaryfile` („Enhanced File“) für das Sammeln von Informationen | [enhancedezbinaryfile](../../../funktionen/6.0/erweiterungen/enhancedezbinaryfile.md) |
| Lesen und Exportieren | **Setup > Gesammelte Informationen**, CSV-Export | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |
| Konten für Ehrenamtliche | Benutzerregistrierung mit E-Mail-Bestätigung, eine Gruppe und Rolle für Mitglieder | [Mitgliederregistrierung und Mitgliederbereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md) |
| Ehrenamtliche, die bearbeiten | Auf Bereiche beschränkte Rollen, Freigabe-Workflow, Versionen, Audit-Protokoll, Zwei-Faktor-Anmeldung | [Gemeinsames Bearbeiten mit Ehrenamtlichen](../../unternehmen/vereine/gemeinsam-bearbeiten-mit-ehrenamtlichen.md) |
| Konten stilllegen | **Benutzerkonten prüfen** und die optionale Aktion **Deaktivieren und verschieben** | [Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md) |

Alle hier genannten Erweiterungen werden über `composer.json` installiert.

## Die Seiten dieses Ordners

| Seite | Was sie beantwortet |
|---|---|
| [Anmeldeformular für Ehrenamtliche, Schritt für Schritt](anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md) | Das Formular bauen, von der Klasse bis zur Dankesseite |
| [Spamschutz für Ehrenamtsformulare](spamschutz-fuer-ehrenamtsformulare.md) | Formular-Token, hCaptcha oder reCAPTCHA, Datenschutz und Barrierefreiheit eines CAPTCHA |
| [Bewerbungen von Ehrenamtlichen bearbeiten](bewerbungen-von-ehrenamtlichen-bearbeiten.md) | Einsendungen lesen, beantworten, exportieren und löschen |
| [Ein Mitgliederbereich für Ehrenamtliche](mitgliederbereich-fuer-ehrenamtliche.md) | Dienstpläne, Anleitungen und Neuigkeiten, die nur Ehrenamtliche sehen |
| [Ehrenamtliche als Redakteure](ehrenamtliche-als-redakteure.md) | Ehrenamtliche veröffentlichen lassen, ohne die Website zu gefährden |
| [Ehrenamtliche Redakteure schulen](ehrenamtliche-redakteure-schulen.md) | Eine erste Schulung, Hausregeln und ein Spickzettel |

Arbeitsblätter: [Arbeitsblatt: Felder des Ehrenamtsformulars](../vorlagen/arbeitsblatt-felder-des-ehrenamtsformulars.md) und
[Arbeitsblatt: Rollen und Berechtigungen](../vorlagen/arbeitsblatt-rollen-und-berechtigungen.md).

## Der Weg der Ehrenamtlichen auf der Website

| Etappe | Auf der Website | Seite |
|---|---|---|
| Findet Sie | Eine Seite „Engagieren Sie sich bei uns“, verlinkt aus dem Menü, aus Kampagnen und dem Newsletter | [Kampagnen und Spendenaufrufe](../kommunikation/kampagnen-und-spendenaufrufe.md) |
| Bewirbt sich | Das Anmeldeformular | [Anmeldeformular](anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md) |
| Wird angenommen | Eine Antwort des Koordinators; vielleicht ein Konto | [Bewerbungen bearbeiten](bewerbungen-von-ehrenamtlichen-bearbeiten.md) |
| Macht mit | Mitgliederbereich, Veranstaltungsseiten, Newsletter | [Mitgliederbereich](mitgliederbereich-fuer-ehrenamtliche.md) |
| Hilft bei der Website | Ein Redakteurskonto mit einer eingeschränkten Rolle | [Ehrenamtliche als Redakteure](ehrenamtliche-als-redakteure.md) |
| Geht | Konto deaktiviert, Daten fristgerecht gelöscht | [Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md) |

## Was nicht eingebaut ist

Eine Durchsuchung des Repositorys hat über Formulare und Konten hinaus keine Funktionen zur Verwaltung von
Ehrenamtlichen gefunden:

| Nicht eingebaut | Stattdessen planen |
|---|---|
| Schichtplanung und Dienstpläne mit Anmeldeplätzen | Ein Dienstplan als Seite oder Datei im Mitgliederbereich oder ein externes Planungswerkzeug |
| Erfassung von Stunden | Eine Tabellenkalkulation oder ein externes Werkzeug |
| Workflows für Führungszeugnisse und Hintergrundprüfungen | Ihr eigener Prozess, außerhalb der Website |
| Automatische Erinnerungen an Ehrenamtliche | Eine Newsletter-Liste für Ehrenamtliche ([Unterstützer-Newsletter](../kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md)) |

Ein Partner kann den Aufwand für eine Erweiterung schätzen, wenn Sie eine brauchen ([Mit einem Partner arbeiten](../betrieb/mit-einem-partner-arbeiten.md)).

## Verwandte Seiten

- [Non-Profit-Organisationen: hier beginnen](../non-profit-organisationen-hier-beginnen.md)
- [Ehrenamtliche und Anmeldeformulare](../../unternehmen/gemeinnuetzige/ehrenamtliche-und-anmeldeformulare.md)
- [Kleine Gruppen und Vereine](../../unternehmen/vereine/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
