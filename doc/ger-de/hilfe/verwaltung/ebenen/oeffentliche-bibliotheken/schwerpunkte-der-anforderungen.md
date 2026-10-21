---
title: Schwerpunkte der Anforderungen für öffentliche Bibliotheken
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website einer öffentlichen Bibliothek am meisten wiegen - Barrierefreiheit und Inklusion, viele Sprachen, Datenschutz der Lesenden und Dienste Dritter, Sicherheit an gemeinsam genutzten Computern der Beschäftigten, Schriftgut - und was Exponential bietet.
keywords: Anforderungen, Bibliothek, Barrierefreiheit, Inklusion, Leichte Sprache, Sprachen, Datenschutz der Lesenden, Dritte, Analyse, gemeinsam genutzte Computer, zweistufige Anmeldung, requirements, library, accessibility, inclusion, easy read, languages, reader privacy, third-party, analytics, shared computers, two-step sign-in
type: help
---
# Schwerpunkte der Anforderungen für öffentliche Bibliotheken

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren der Website einer
öffentlichen Bibliothek.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Barrierefreiheit und Inklusion](#barrierefreiheit-und-inklusion)
2. [Sprachen](#sprachen)
3. [Datenschutz der Lesenden](#datenschutz-der-lesenden)
4. [Sicherheit](#sicherheit)
5. [Schriftgut](#schriftgut)

## Barrierefreiheit und Inklusion

Bibliotheken dienen allen, auch Menschen, die mit Mühe lesen, Screenreader oder Vergrößerung nutzen oder neu am Computer
sind. Von öffentlichen Stellen betriebene öffentliche Bibliotheken fallen in vielen Rechtsordnungen unter die Regeln zur
Barrierefreiheit im öffentlichen Sektor (in der EU die
[Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)).

Status von Exponential: Die Administration admin4 ist mit **Teilweise erfüllt** gegenüber WCAG 2.2 AA bewertet;
EN 301 549 und Section 508 **Nicht geprüft**; öffentliche Designs nicht geprüft
([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)). Also:

| Bereich | Ihre Arbeit |
|---|---|
| Der Kalender | das Monatsraster muss mit Tastatur und Screenreader bedienbar sein; die Ansicht als Programmliste ist für beide oft einfacher |
| Beschreibungen von Veranstaltungen | Datum, Uhrzeit, Ort und Barrierefreiheit des Veranstaltungsorts als Text, nicht nur in einem Plakatbild |
| Bilder | Alternativtext an Bildattributen verpflichtend ([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)) |
| Leichte Lektüre | kurze Sätze auf Dienstleistungsseiten; Leitlinien für einfache Sprache unter [plainlanguage.gov](https://www.plainlanguage.gov/) |
| Erklärung | eine Erklärung zur Barrierefreiheit ([W3C](https://www.w3.org/WAI/planning/statements/)) |

## Sprachen

Die Gemeinschaften einer Bibliothek sprechen oft viele Sprachen. Übersetzungen je Objekt und ein Siteaccess je Sprache
lassen wichtige Seiten in mehreren Sprachen existieren ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md));
Schriften von rechts nach links werden unterstützt
([Sprachen von rechts nach links](../../../../funktionen/6.0/rechts-nach-links.md)). Beginnen Sie mit Anmeldung,
Öffnungszeiten und den Veranstaltungen für Sprachlernende.

## Datenschutz der Lesenden

Bibliotheken schützen traditionell, was Menschen lesen und ausleihen. Auf der Website:

| Risiko | Maßnahme |
|---|---|
| Analysedienste Dritter oder eingebettete soziale Medien, die Profile von Besuchern bilden | datenschutzfreundliche Statistik bevorzugen; Exponential lädt in der Administration keine Skripte Dritter ([Datenschutzstandards](../../../../standards/datenschutz.md)) |
| Anmeldeformulare für Veranstaltungen, die Namen und Kontaktdaten speichern | das Minimum erheben; Eingaben nach der Veranstaltung entfernen ([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)) |
| Newsletter | Einwilligung und Abmeldung mit einem Klick über die E-Mail-Einstellungen ([E-Mail-Einstellungen](../../../../funktionen/6.0/e-mail-einstellungen.md)) |
| Anmeldung zum Katalog auf der Website | nicht bauen; auf die eigene Anmeldung des Katalogs verlinken |
| CAPTCHA in Formularen | `recaptcha` und `hcaptcha` sind Dritte ([Spamschutz für Formulare](../../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)) |

Exponential hat kein Cookie-Banner; entscheiden Sie mit Ihrer oder Ihrem Datenschutzbeauftragten, was Sie in der EU nach
der [ePrivacy-Richtlinie](https://eur-lex.europa.eu/eli/dir/2002/58/oj) und der
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) brauchen.

## Sicherheit

Beschäftigte veröffentlichen von gemeinsam genutzten Computern an der Theke. Verwenden Sie die zweistufige Anmeldung
([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)), melden Sie sich nach der Nutzung ab und prüfen Sie die
Konten ausgeschiedener Beschäftigter (**Benutzer > Benutzerkonten prüfen**,
[Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)). Die Einstellung für die Kontosperre
(`MaxNumberOfFailedLogin`) ist standardmäßig aus ([Sicherheitshärtung](../../../sicherheitshaertung.md)).

## Schriftgut

Websites von Bibliotheken enthalten wenig förmliches Schriftgut, aber Dokumente der Trägerschaft (Protokolle des
Vorstands, Jahresberichte, Pläne) sind Schriftgut des Leitungsgremiums der Bibliothek; bewahren Sie sie als Objekte der
Klasse File und in Ihrem Schriftgutverwaltungssystem auf. Veranstaltungen brauchen auf der Website keine langfristige
Aufzeichnung; vergangene Veranstaltungen lassen sich entfernen oder in einem Archivordner je Jahr aufbewahren.

## Verwandte Seiten

- [Öffentliche Bibliotheken](README.md)
- [Die ersten 90 Tage für öffentliche Bibliotheken](die-ersten-90-tage.md)
- [Barrierefreiheit für Website-Ersteller](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
