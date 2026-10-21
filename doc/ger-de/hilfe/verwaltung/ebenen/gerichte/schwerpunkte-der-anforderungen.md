---
title: Schwerpunkte der Anforderungen für Gerichte
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website eines Gerichts oder Spruchkörpers am meisten wiegen - personenbezogene Daten in Entscheidungen, Integrität des veröffentlichten Textes, Barrierefreiheit für Rechtsuchende, Sicherheit, Schriftgut - und was Exponential bietet und dem Gericht überlässt.
keywords: Anforderungen, Gericht, personenbezogene Daten, Anonymisierung, Pseudonymisierung, Integrität, Audit-Protokoll, Barrierefreiheit, einfache Sprache, Sicherheit, Schriftgut, requirements, court, personal data, anonymization, pseudonymization, integrity, audit trail, accessibility, plain language, security, records
type: help
---
# Schwerpunkte der Anforderungen für Gerichte

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren der Website eines
Gerichts oder Spruchkörpers.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Personenbezogene Daten](#personenbezogene-daten)
2. [Integrität des veröffentlichten Textes](#integrität-des-veröffentlichten-textes)
3. [Barrierefreiheit und einfache Sprache](#barrierefreiheit-und-einfache-sprache)
4. [Sicherheit](#sicherheit)
5. [Schriftgut](#schriftgut)

## Personenbezogene Daten

Entscheidungen, Terminrollen und Schriftverkehr enthalten personenbezogene Daten, oft besonderer Kategorien
(Gesundheit, Strafsachen, Kinder). In der EU gilt die [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) für
Gerichte, mit besonderen Regeln für Gerichte im Rahmen ihrer justiziellen Tätigkeit; das nationale Recht legt fest, was
veröffentlicht wird.

| Anforderung | Exponential | Ihre Arbeit |
|---|---|---|
| Entscheidungen vor der Veröffentlichung anonymisiert | nichts: Exponential speichert, was Sie hochladen | durch Menschen oder ein Werkzeug anonymisieren, vor dem Hochladen |
| Eine zweite Prüfung vor der Veröffentlichung | Ereignis *Freigeben*; eine Prüfsektion | Prüfende, die geschult sind, worauf sie achten müssen |
| Schnelles Zurückziehen | verbergen, entfernen, Versionen entfernen ([Entscheidungen mit Sorgfalt veröffentlichen](entscheidungen-mit-sorgfalt-fuer-personenbezogene-daten-veroeffentlichen.md)) | ein schriftliches Verfahren für das Zurückziehen |
| Begrenzter Veröffentlichungszeitraum, wo das Gesetz einen festlegt | `hide.php` mit `[HideSettings]` ([Cronjobs](../../../../anleitungen/cronjobs.md)) | der Zeitraum je Art der Entscheidung |
| Sparsame Protokolle | Pseudonymisierung im Audit, Maskierung von IP-Adressen ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)) | Aufbewahrungseinstellungen |
| Kein Tracking Dritter auf Seiten mit Entscheidungen | die Administration lädt nichts von Dritten; das öffentliche Design ist Ihres | keine Skripte Dritter auf der Website |

## Integrität des veröffentlichten Textes

| Anforderung | Exponential | Lesen |
|---|---|---|
| Jede Änderung an einer veröffentlichten Entscheidung aufgezeichnet | Versionen; das Audit-Protokoll, manipulationssicher (hash-verkettet mit signierten Manifesten) | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Begrenzt, wer veröffentlichte Entscheidungen ändern darf | Rollen mit Einschränkungen nach Sektion und Zustand | [Struktur, Sektionen und Rollen für Gerichte](struktur-sektionen-und-rollen.md) |
| Dateien auf dem Server nicht verändert | die Upgrade-Prüfung vergleicht Kernel-Dateien mit `share/filelist.md5`; hochgeladene Dateien sind Aufgabe Ihrer Sicherungen | [Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) |
| Konfiguration im Produktivbetrieb gesperrt | `ManagedByDeployment` | [Von der Bereitstellung verwaltet](../../../../../eng-us/features/6.0/managed-by-deployment.md) |

Viele Gerichte veröffentlichen zusätzlich eine Prüfsumme oder ein signiertes PDF jeder Entscheidung; das wird außerhalb
von Exponential erzeugt und kann mit der Entscheidung hochgeladen werden.

## Barrierefreiheit und einfache Sprache

Zu den Rechtsuchenden gehören Menschen ohne Anwalt, Menschen unter Druck, Menschen mit Behinderungen und Menschen mit
eingeschränkter Kenntnis der Sprache. In der EU fallen die Websites von Gerichten als öffentliche Stellen, mit
begrenzten Ausnahmen, unter die [Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj). Status von
Exponential: Administration admin4 erfüllt WCAG 2.2 AA **teilweise**; EN 301 549 und Section 508 **Nicht geprüft**;
öffentliche Designs nicht geprüft ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)). Hinweisseiten
in HTML mit einer kurzen Zusammenfassung helfen mehr als PDFs; Leitlinien für einfache Sprache finden Sie unter
[plainlanguage.gov](https://www.plainlanguage.gov/).

## Sicherheit

| Maßnahme | Lesen |
|---|---|
| Zweistufige Anmeldung, verpflichtend für alle mit einer Rolle über das Lesen hinaus | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Kontosperre eingeschaltet (`MaxNumberOfFailedLogin`), standardmäßig aus | [Sicherheitshärtung](../../../sicherheitshaertung.md) |
| Ein Anfrageschutz vor der Website | [Sicherheitshärtung: request-shield](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield) |
| Content Security Policy auf der Website (auf Websites standardmäßig aus) | [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md) |
| Alarmregeln des Audits (Brute-Force und andere) beobachtet | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |

Status gegenüber OWASP Top 10: **Teilweise erfüllt** ([Sicherheitsstandards](../../../../../eng-us/standards/security.md)). Die
Justiz ist oft kritische Infrastruktur; siehe [NIST CSF 2.0](https://www.nist.gov/cyberframework) oder Ihren nationalen
Rahmen.

## Schriftgut

Die maßgebliche Aufzeichnung einer Entscheidung ist die Verfahrensakte des Gerichts, nicht die Website. Die Kopie auf der
Website sollte sich auf sie zurückführen lassen (Aktenzeichen, Datum), und ihr Veröffentlichungsverlauf sollte erhalten
bleiben (Versionen, Audit). Stellen Sie die Aufbewahrung der Audit-Einträge auf Ihren Aktenplan ein
([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).

## Verwandte Seiten

- [Gerichte](README.md)
- [Die ersten 90 Tage für Gerichte](die-ersten-90-tage.md)
- [Sicherheitsstandards](../../../../../eng-us/standards/security.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
