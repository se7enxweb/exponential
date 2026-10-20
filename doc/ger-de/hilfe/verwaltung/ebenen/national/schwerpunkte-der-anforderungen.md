---
title: Schwerpunkte der Anforderungen für die nationale Regierung
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Welche Anforderungen für die Website eines Ministeriums oder zentralen Ressorts am meisten wiegen - Barrierefreiheit, Amtssprachen, Sicherheit und Verfügbarkeit, Schriftgut - und was Exponential für jede bietet und Ihnen überlässt.
keywords: Anforderungen, Barrierefreiheit, WCAG, EN 301 549, Section 508, Amtssprachen, Sicherheit, NIS2, Verfügbarkeit, Schriftgut, Aufbewahrung, Audit, nationale Regierung, requirements, accessibility, official languages, security, availability, records, retention, audit, national government
type: help
---
# Schwerpunkte der Anforderungen für die nationale Regierung

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Anforderungen an
die Website eines Ministeriums oder zentralen Ressorts schreiben oder prüfen. Sie sagt, welche Anforderungen auf
nationaler Ebene meist am meisten wiegen, und für jede, was Exponential bietet (mit der Seite, die es belegt) und was die
Arbeit Ihrer Organisation bleibt.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Barrierefreiheit](#barrierefreiheit)
2. [Amtssprachen](#amtssprachen)
3. [Sicherheit und Verfügbarkeit](#sicherheit-und-verfügbarkeit)
4. [Schriftgut und Verlauf](#schriftgut-und-verlauf)
5. [Datenschutz](#datenschutz)
6. [Eine Anforderungstabelle als Ausgangspunkt](#eine-anforderungstabelle-als-ausgangspunkt)

## Barrierefreiheit

Websites des öffentlichen Sektors unterliegen in vielen Rechtsordnungen dem Recht der Barrierefreiheit. In der EU gilt
die Richtlinie über barrierefreie Websites, [Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj),
für öffentliche Stellen, mit der harmonisierten Norm
[EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf); in den
Vereinigten Staaten folgen Bundesbehörden [Section 508](https://www.section508.gov/) und den
[Revised 508 Standards](https://www.access-board.gov/ict/). Beide bauen auf [WCAG](https://www.w3.org/TR/WCAG22/) auf.

Was Exponential über sich selbst erklärt ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)):

| Standard | Status | Bedeutung für Sie |
|---|---|---|
| WCAG 2.2 AA | Partly complies | die Administration admin4 und admin4l ist gegen AA-Kriterien gebaut und getestet; öffentliche Designs sind nicht geprüft |
| EN 301 549 | Unverified | es gibt keinen Konformitätsbericht |
| Section 508 | Unverified | es gibt kein VPAT und keinen Accessibility Conformance Report |

Die Barrierefreiheit Ihrer **öffentlichen Website** hängt also von Ihrem Design, Ihren Templates und Inhalten ab.
Planen Sie Mittel für eine Prüfung Ihrer eigenen Templates ein, für die Schulung der Redaktion (Alternativtexte,
Überschriften, Linktexte, barrierefreie Dokumente) und für eine Erklärung zur Barrierefreiheit, die Exponential nicht
erzeugt ([Leitfaden des W3C für Erklärungen](https://www.w3.org/WAI/planning/statements/)). Bildfelder können einen
Alternativtext verlangen
([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)).

## Amtssprachen

Wo mehrere Sprachen Amtssprachen sind, muss jede Seite meist vor oder bei der Veröffentlichung in jeder verfügbar sein.

| Bedarf | Exponential | Lesen |
|---|---|---|
| Ein Objekt, mehrere Sprachen | Übersetzungen je Objekt, ein Siteaccess je Sprache | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| Sprachumschalter und Sprach-URLs | Siteaccess je Sprache, Templates für den Umschalter | [Sprachumschalter und Sprach-URLs](../../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md) |
| Sprachen von rechts nach links | Unterstützung für `dir="rtl"` | [Sprachen von rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md) |
| Freigabe je Sprache | **Betroffene Sprachen** des Ereignisses *Freigeben* | [Workflows](../../../../anleitungen/workflows.md) |

Es gibt keine eingebaute Regel „nur veröffentlichen, wenn alle Sprachen vorliegen“. Setzen Sie sie durch einen Prozess
durch: Übersetzerinnen und Übersetzer arbeiten in Entwürfen, und eine freigebende Person prüft die Übersetzungen vor der
Freigabe.

## Sicherheit und Verfügbarkeit

Nationale Websites sind häufige Ziele und müssen bei Eilmeldungen erreichbar bleiben. In der EU fallen viele
Einrichtungen der Zentralregierung unter die NIS2-Richtlinie,
[Richtlinie (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj); das
[NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) ist anderswo eine verbreitete Referenz.

| Thema | Was mitgeliefert wird | Lesen |
|---|---|---|
| OWASP Top 10 | Gegenmaßnahmen für alle zehn Kategorien, Status „Partly complies“ | [Sicherheitsstandards](../../../../../eng-us/standards/security.md) |
| Zweistufige Anmeldung für Beschäftigte | sevenx_authentication_2fa, kann verpflichtend gemacht werden | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Kontosperre | verfügbar, standardmäßig aus (`MaxNumberOfFailedLogin`) | [Sicherheitshärtung](../../../sicherheitshaertung.md) |
| Content Security Policy | standardmäßig Report-only in der Administration, auf Websites aus | [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md) |
| Lastspitzen | Ansichts-Cache, statischer Cache, Antwort-Cache von Velocity | [Leistung und Caching](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md) |
| Geplante Ausfallzeit | Wartungsmodus mit 503 und `Retry-After` | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |

Ihre Arbeit: ein Bedrohungsmodell, ein Hosting mit Redundanz, durch Wiederherstellen getestete Sicherungen, Überwachung,
ein Verfahren für Sicherheitsvorfälle und ein Meldeweg für Schwachstellen. Exponential liefert `SECURITY.md`, aber keine
`security.txt` ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)) mit; veröffentlichen Sie selbst eine.

## Schriftgut und Verlauf

| Bedarf | Exponential | Lücke |
|---|---|---|
| Wer was wann geändert hat | Versionen je Objekt, das Audit-Protokoll (standardmäßig an, hash-verkettet) | Ihr Aufbewahrungsplan entscheidet, wie lange |
| Frühere Fassung einer Seite | archivierte Versionen, Vergleich, neuer Entwurf aus einer alten Version | alte Versionen werden jenseits der Grenze des Versionsverlaufs entfernt (`DefaultVersionHistoryLimit`, 45 in der mitgelieferten `settings/content.ini`; `VersionHistoryClass[<class id>]` je Klasse) |
| Zurückgezogene Inhalte weiterhin auffindbar | verbergen, Objektzustände, eine Sektion für Zurückgezogenes | keine eingebaute Beziehung „ersetzt durch“; fügen Sie ein Attribut `ezobjectrelation` hinzu |

Verlangen Ihre Regeln für das Schriftgut, dass jede veröffentlichte Version erhalten bleibt, heben Sie die Grenze des
Versionsverlaufs für diese Klassen an oder exportieren Sie Publikationen in Ihr Schriftgutverwaltungssystem. Normen für
die Schriftgutverwaltung wie [ISO 15489-1](https://www.iso.org/standard/62542.html) beschreiben die Grundsätze.

## Datenschutz

Formulare, Konten und Protokolle enthalten personenbezogene Daten. Exponential bietet Einwilligungsnachweise für E-Mails,
Löschung beim Entfernen eines Kontos, Pseudonymisierung im Audit und Maskierung in Protokollen, aber kein Cookie-Banner
([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)); in der EU gilt die
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj).

## Eine Anforderungstabelle als Ausgangspunkt

| ID | Anforderung | Prüfung |
|---|---|---|
| R1 | Öffentliche Templates erfüllen WCAG 2.2 AA | externe Prüfung Ihrer Templates |
| R2 | Jede Seite in jeder Amtssprache verfügbar | Checkliste der Freigabe |
| R3 | Beschäftigte melden sich zweistufig an | `Enforce2FA=enabled`, getestet |
| R4 | Jede Änderung ist zuordenbar | Audit-Protokoll an, Aufbewahrung eingestellt |
| R5 | Die Website übersteht den geschäftigsten Tag | Lasttest mit eingeschalteten Caches |
| R6 | Geplante Arbeiten zeigen eine Wartungsseite | Wartungsmodus geprobt |

Die Bereiche zu den Anforderungen in dieser Hilfe für Verwaltungen gehen tiefer:
[Barrierefreiheit](../../barrierefreiheit/README.md), [Sicherheit](../../sicherheit/README.md),
[Datenschutz](../../../../../eng-us/help/government/privacy/README.md) und [Schriftgut und Archivierung](../../../../../eng-us/help/government/records-and-archiving/README.md).

## Verwandte Seiten

- [Nationale Regierung](README.md)
- [Die ersten 90 Tage für die nationale Regierung](die-ersten-90-tage.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
