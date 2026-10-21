---
title: Schwerpunkte der Anforderungen für Länder und Regionen
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website eines Landes oder einer Region am meisten wiegen - Barrierefreiheit, Regional- und Minderheitensprachen, Sicherheit, Schriftgut der Beschlüsse der Volksvertretung, Datenschutz bei Karten Dritter - und was Exponential für jede bietet.
keywords: Anforderungen, Barrierefreiheit, Regionalsprachen, Minderheitensprachen, Übersetzungen, Sicherheit, Schriftgut, Beschlüsse der Volksvertretung, Datenschutz, Karten, Dritte, requirements, accessibility, regional languages, minority languages, translations, security, records, assembly decisions, privacy, maps, third party
type: help
---
# Schwerpunkte der Anforderungen für Länder und Regionen

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Anforderungen an
die Website eines Landes oder einer Region festlegen. Sie nennt die Anforderungen, die auf dieser Ebene meist am meisten
wiegen, und was Exponential für jede bietet, mit der Seite, die es dokumentiert.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Barrierefreiheit](#barrierefreiheit)
2. [Regional- und Minderheitensprachen](#regional--und-minderheitensprachen)
3. [Sicherheit](#sicherheit)
4. [Schriftgut der Beschlüsse](#schriftgut-der-beschlüsse)
5. [Datenschutz und Dritte](#datenschutz-und-dritte)
6. [Übersichtstabelle](#übersichtstabelle)

## Barrierefreiheit

Regionale und Landesstellen sind in der EU öffentliche Stellen im Sinne der EU-Richtlinie über barrierefreie Websites,
[Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj). In den Vereinigten Staaten bindet
[Section 508](https://www.section508.gov/) Bundesbehörden; die Regeln der Bundesstaaten unterscheiden sich, und viele
verweisen auf [WCAG](https://www.w3.org/TR/WCAG22/).

Der eigene Status von Exponential ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)): WCAG 2.2
AA **Teilweise erfüllt** für die Administration admin4 und admin4l; EN 301 549 und Section 508 **Nicht geprüft**; öffentliche
Designs nicht geprüft; kein VPAT und keine Vorlage für eine Erklärung zur Barrierefreiheit. Ihr öffentliches Design und
Ihre Dokumente müssen Sie selbst testen.

Regionale Websites veröffentlichen viele Pläne und Karten als PDF. Die Einstiegsseite eines Plans sollte eine
Zusammenfassung in HTML tragen; eine Karte sollte eine Textbeschreibung dessen haben, was sie zeigt
([Barrierefreiheit für Website-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).

## Regional- und Minderheitensprachen

| Bedarf | Exponential | Lesen |
|---|---|---|
| Seiten in der Landessprache und der Regionalsprache | Übersetzungen je Objekt, ein Siteaccess je Sprache | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| Eine Oberfläche in der Regionalsprache für die Redaktion | Übersetzungen der Oberfläche; fehlende Texte fallen auf eine andere Sprache zurück | [Übersetzungen und Sprachen](../../../../../eng-us/features/6.0/translations-and-languages.md) |
| Eine Seite nur in einer Sprache | das Objekt existiert nur in dieser Sprache; „Immer verfügbar“ entscheidet, ob andere es zeigen | [Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) |
| Sprachangaben im HTML | das Locale des Siteaccess setzt `lang` | [Standards zur Internationalisierung](../../../../../eng-us/standards/internationalization.md) |

Verlangt das Gesetz die Gleichbehandlung der Sprachen, stellen Sie die **Betroffenen Sprachen** der Freigabe so ein, dass
jede Sprachfassung freigegeben wird, und verfolgen Sie fehlende Übersetzungen im Reiter **Übersetzungen** des Objekts.

## Sicherheit

| Thema | Was mitgeliefert wird | Lesen |
|---|---|---|
| Anmeldung der Beschäftigten | zweistufige Anmeldung, verpflichtend einstellbar | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Konten kommunaler Beschäftigter (außerhalb Ihrer Organisation) | Konten in einer Gruppe, regelmäßige Überprüfung | [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md) |
| Härtung | Einstellungen und Maßnahmen am Server | [Sicherheitshärtung](../../../sicherheitshaertung.md) |
| Status gegenüber OWASP | Teilweise erfüllt | [Sicherheitsstandards](../../../../../eng-us/standards/security.md) |

Viele regionale Einrichtungen fallen in der EU je nach nationaler Umsetzung unter NIS2,
[Richtlinie (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj).

## Schriftgut der Beschlüsse

Beschlüsse der Volksvertretung sind Schriftgut. Exponential bewahrt Versionen und ein Audit-Protokoll; das förmliche
Archiv bleibt meist Ihr Schriftgutverwaltungssystem.

| Bedarf | Exponential | Lesen |
|---|---|---|
| Der veröffentlichte Text ändert sich nicht stillschweigend | Versionen; Vergleich | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| Wer wann veröffentlicht hat | das Audit-Protokoll, standardmäßig an, manipulationssicher | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Alte Versionen bleiben erhalten | `[VersionManagement] DefaultVersionHistoryLimit` (45 in der mitgelieferten `settings/content.ini`) und `VersionHistoryClass[<class id>]` je Klasse | [Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| Export in ein Archiv | PDF-Export eines Teilbaums; die Dateien selbst | [PDF-Exporte](../../../../anleitungen/pdf-exporte.md) |

## Datenschutz und Dritte

Karten von Google (`ezgmaplocation`), CAPTCHA-Dienste (`recaptcha`, `hcaptcha`) und eingebettete Videos senden Daten der
Besucher an Dritte. Exponential liefert kein Cookie-Banner mit ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)).
Entscheiden Sie nach der [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) und der
[ePrivacy-Richtlinie](https://eur-lex.europa.eu/eli/dir/2002/58/oj) mit Ihrer oder Ihrem Datenschutzbeauftragten,
welche davon Sie verwenden und wie die Besucher informiert werden.

## Übersichtstabelle

| Anforderung | Unterstützung durch das Produkt | Ihre Arbeit |
|---|---|---|
| Barrierefreie öffentliche Website | Administration getestet; öffentliches Design ist Ihres | Prüfung, Erklärung, Schulung der Redaktion |
| Regionalsprache | Übersetzungen, Siteaccesses | Übersetzungsprozess |
| Sicherer Zugang der Beschäftigten | 2FA, Rollen, Audit | verpflichtend machen, Konten überprüfen |
| Beschlüsse als Schriftgut | Versionen, Audit | Aufbewahrungsregeln, Übergabe an das Archiv |
| Datenschutz | Einwilligungsprotokoll für E-Mails, Maskierung in Protokollen | Entscheidungen über Dritte, Datenschutzerklärung |

## Verwandte Seiten

- [Länder und Regionen](README.md)
- [Die ersten 90 Tage für Länder und Regionen](die-ersten-90-tage.md)
- [Sprachen](../../../sprachen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
