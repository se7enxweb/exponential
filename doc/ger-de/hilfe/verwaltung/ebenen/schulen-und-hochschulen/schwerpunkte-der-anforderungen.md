---
title: Schwerpunkte der Anforderungen für Schulen und Hochschulen
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website einer Bildungseinrichtung am meisten wiegen - Barrierefreiheit, Daten und Bilder Minderjähriger, Konten von Beschäftigten und Lernenden, die sich jedes Jahr ändern, Sprachen, Schriftgut - und was Exponential für jede bietet.
keywords: Anforderungen, Schule, Hochschule, Barrierefreiheit, Minderjährige, Daten von Kindern, Einwilligung, Fotos, Konten, Deaktivierung, zweistufige Anmeldung, Sprachen, Schriftgut, requirements, school, university, accessibility, minors, children's data, consent, photos, accounts, deactivation, two-step sign-in, languages, records
type: help
---
# Schwerpunkte der Anforderungen für Schulen und Hochschulen

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren der Website einer Schule,
Fachhochschule oder Universität.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Barrierefreiheit](#barrierefreiheit)
2. [Daten und Bilder Minderjähriger](#daten-und-bilder-minderjähriger)
3. [Konten, die sich jedes Jahr ändern](#konten-die-sich-jedes-jahr-ändern)
4. [Sprachen](#sprachen)
5. [Schriftgut](#schriftgut)

## Barrierefreiheit

Öffentliche Bildungseinrichtungen sind in vielen Rechtsordnungen öffentliche Stellen im Sinne der Regeln zur
Barrierefreiheit (in der EU die [Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj); in den
Vereinigten Staaten gelten für Einrichtungen, die Bundesmittel erhalten, eigene Regeln, und
[Section 508](https://www.section508.gov/) gilt für Bundesbehörden). Lernende mit Behinderungen sind auf das Intranet
ebenso angewiesen wie auf die öffentliche Website.

Status von Exponential: Administration admin4 **Partly complies** mit WCAG 2.2 AA; EN 301 549 und Section 508
**Unverified**; öffentliche Designs nicht geprüft; kein VPAT
([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)). Fachbereiche, die ihre eigenen Dokumente
veröffentlichen, brauchen Schulung: barrierefreie Dokumente und Alternativtexte
([Barrierefreiheit für Website-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).

## Daten und Bilder Minderjähriger

Schulen veröffentlichen für und über Kinder. Praxis in vielen Rechtsordnungen:

| Thema | Maßnahme |
|---|---|
| Fotos von Schülerinnen und Schülern | nur mit festgehaltener Einwilligung; keine Namen neben Fotos von Kindern, sofern die Einwilligung das nicht abdeckt |
| Arbeiten von Schülerinnen und Schülern | Einwilligung; keine persönlichen Angaben |
| Formulare für Eltern | das Minimum erheben; nach der Verwendung entfernen ([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)) |
| Profile der Beschäftigten | nur Informationen zur öffentlichen Rolle |
| Dienste Dritter (eingebettete Videos, CAPTCHA, Karten) | vor der Verwendung prüfen, was sie erheben |

Exponential speichert, was Sie hochladen; es hat kein Einwilligungsregister für Fotos. Führen Sie die
Einwilligungsnachweise in Ihrer Schulverwaltung und vermerken Sie im Beschreibungsfeld eines Bildes, dass eine
Einwilligung vorliegt. Die [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) schützt in der EU die Daten von Kindern
besonders ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)).

## Konten, die sich jedes Jahr ändern

| Bedarf | Exponential | Lesen |
|---|---|---|
| Konten finden, die niemand nutzt | **Benutzer > Benutzerkonten prüfen**, Filter *Nie angemeldet*, *Seit N Tagen nicht gesehen* | [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md) |
| Ausscheidende in großer Zahl deaktivieren | **Deaktivieren** an angekreuzten Konten; **Deaktivieren und verschieben** (auf Wunsch einzuschalten) in eine verborgene Gruppe ohne Rollen | dieselbe Seite |
| Starke Anmeldung für Beschäftigte | zweistufige Anmeldung, verpflichtend einstellbar | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Passwortregeln | `MinPasswordLength` (10), Ablauf von Passwörtern und Regeln mit ezmbpaex | [ezmbpaex](../../../../../eng-us/features/6.0/extensions/ezmbpaex.md) |
| Entfernen eines Kontos | endgültig (Benutzerobjekte kommen nie in den Papierkorb); E-Mail-Einstellungen gelöscht | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) |

## Sprachen

Hochschulen lehren oft in mehreren Sprachen, und Schulen betreuen Familien mit vielen Herkunftssprachen. Übersetzungen
je Objekt und ein Siteaccess je Sprache ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)); die Klasse Kurs kann
ihre Unterrichtssprache als Attribut tragen.

## Schriftgut

Richtlinien, Protokolle der Gremien, Inspektionsberichte und Zulassungsregeln sind Schriftgut. Bewahren Sie die
genehmigten Fassungen als Objekte der Klasse File mit Genehmigungsdatum auf; Versionen und das Audit-Protokoll zeichnen
Änderungen auf der Website auf ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md),
[Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)). Das förmliche Archiv ist Ihr Schriftgutverwaltungssystem.

## Verwandte Seiten

- [Schulen und Hochschulen](README.md)
- [Die ersten 90 Tage für Schulen und Hochschulen](die-ersten-90-tage.md)
- [Registrierung von Mitgliedern und Mitgliederbereiche](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
