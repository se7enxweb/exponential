---
title: Schwerpunkte der Anforderungen für Kommunen
audience: government, project-manager, admin
category: public-sector
order: 50
summary: Die Anforderungen, die für die Website einer Kommune am meisten wiegen - Barrierefreiheit für alle Einwohnerinnen und Einwohner, einfache Sprache, die Sprachen der Einwohnerschaft, Sicherheit mit einem kleinen Team, Schriftgut der Ratsangelegenheiten, Datenschutz bei Formularen - und was Exponential für jede bietet.
keywords: Anforderungen, Barrierefreiheit, einfache Sprache, Sprachen, Sicherheit, kleines Team, Ratsunterlagen, Formulare, Datenschutz, Aufbewahrung, Kommune, requirements, accessibility, plain language, languages, security, small team, council records, forms, privacy, retention, municipal
type: help
---
# Schwerpunkte der Anforderungen für Kommunen

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Anforderungen an
die Website einer Stadt, Gemeinde oder eines Rats festlegen. Sie nennt, was auf kommunaler Ebene am meisten wiegt und
was Exponential bietet, mit der Seite, die jede Funktion dokumentiert.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Barrierefreiheit für alle Einwohnerinnen und Einwohner](#barrierefreiheit-für-alle-einwohnerinnen-und-einwohner)
2. [Einfache Sprache und die Sprachen der Einwohnerschaft](#einfache-sprache-und-die-sprachen-der-einwohnerschaft)
3. [Sicherheit mit einem kleinen Team](#sicherheit-mit-einem-kleinen-team)
4. [Schriftgut der Ratsangelegenheiten](#schriftgut-der-ratsangelegenheiten)
5. [Formulare und Datenschutz](#formulare-und-datenschutz)

## Barrierefreiheit für alle Einwohnerinnen und Einwohner

Kommunale Websites dienen allen, auch älteren Menschen und Menschen mit Behinderungen, die auf örtliche
Dienstleistungen angewiesen sind. In der EU sind Kommunen öffentliche Stellen im Sinne der Richtlinie über barrierefreie
Websites, [Richtlinie (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj); [WCAG 2.2](https://www.w3.org/TR/WCAG22/)
ist die Referenz, auf die die meisten Regeln verweisen.

Die Administration von Exponential (admin4, admin4l) wird mit **Teilweise erfüllt** gegenüber WCAG 2.2 AA bewertet;
EN 301 549 und Section 508 sind **nicht geprüft**; öffentliche Designs sind nicht geprüft
([Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md)). Für Ihre Website bedeutet das:

| Bereich | Ihre Arbeit |
|---|---|
| Design-Templates | mit Tastatur, Screenreader und Zoom testen ([Die Barrierefreiheit testen](../../../ersteller/website-ersteller/barrierefreiheit-testen.md)) |
| Bilder | **Alternativtext ist erforderlich** an Bildattributen einschalten ([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)) |
| Formulare | Beschriftungen, Fehlermeldungen, kein CAPTCHA ohne Alternative |
| Dokumente | eine Zusammenfassung in HTML auf der Seite; barrierefreie PDFs aus der Quelle |
| Erklärung zur Barrierefreiheit | schreiben und veröffentlichen ([Leitfaden des W3C](https://www.w3.org/WAI/planning/statements/)) |

## Einfache Sprache und die Sprachen der Einwohnerschaft

Dienstleistungsseiten funktionieren nur, wenn die Einwohnerschaft sie versteht. Leitlinien der Verwaltung für einfache
Sprache sind unter [plainlanguage.gov](https://www.plainlanguage.gov/) (US-Bundesverwaltung) gesammelt. Exponential kann
nicht beurteilen, ob Text einfach ist; eine Klasse mit einem kurzen Feld **Kurz gesagt** und ein Prüfprozess können es.

Viele Kommunen bieten wichtige Seiten in Sprachen an, die die Einwohnerschaft über die Amtssprache hinaus spricht.
Übersetzungen je Objekt und ein Siteaccess je Sprache unterstützen das
([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)); eine teilweise übersetzte Website sollte sagen, welche Seiten
es in welcher Sprache gibt. Sprachen von rechts nach links werden unterstützt
([Sprachen von rechts nach links](../../../../funktionen/6.0/rechts-nach-links.md)).

## Sicherheit mit einem kleinen Team

Ein kleines Team kann die Website nicht jede Stunde beobachten, daher zählen die Standardwerte:

| Maßnahme | Exponential | Lesen |
|---|---|---|
| Zweistufige Anmeldung für jedes Konto von Beschäftigten | sevenx_authentication_2fa, `Enforce2FA` | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |
| Kontosperre nach fehlgeschlagenen Anmeldungen | `MaxNumberOfFailedLogin`, standardmäßig aus | [Sicherheitshärtung](../../../sicherheitshaertung.md) |
| CSRF-Schutz jedes Formulars | `ezformtoken` | [Seite „Formular abgelaufen“](../../../../funktionen/6.0/seite-abgelaufenes-formular.md) |
| Audit von Änderungen | standardmäßig an | [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md) |
| Updates | Upgrade-Prüfung und Update-Leitfaden | [Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md), [Aktualisieren](../../../../anleitungen/aktualisieren.md) |

Halten Sie schriftlich fest, wer Sicherheitsupdates einspielt, besonders wenn ein Lieferant die Website hostet.

## Schriftgut der Ratsangelegenheiten

Tagesordnungen, Niederschriften und Beschlüsse sind Schriftgut. Exponential bewahrt Versionen und ein Audit-Protokoll;
das förmliche Archiv ist meist Ihr Schriftgutverwaltungssystem. Halten Sie Berichtigungen sichtbar (eine neue Version mit
einem Hinweis) und entscheiden Sie, wie lange alte Versionen aufbewahrt werden: `[VersionManagement]
DefaultVersionHistoryLimit` ist in der mitgelieferten `settings/content.ini` 45, und `VersionHistoryClass[<class id>]`
setzt eine Grenze je Klasse ([Inhaltsmodell und Bearbeitung](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md)).

## Formulare und Datenschutz

Melde- und Antragsformulare erheben Namen, Anschriften und E-Mail-Adressen. Exponential speichert Eingaben unter
**Setup > Gesammelte Informationen**, exportiert sie als CSV (jeder Export wird im Audit aufgezeichnet) und entfernt sie
auf Anfrage ([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)). Alte Eingaben löscht es nicht von
selbst: Planen Sie eine Überprüfung ein. CAPTCHA-Erweiterungen (`recaptcha`, `hcaptcha`) sind Dienste Dritter
([Spamschutz für Formulare](../../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)). Exponential liefert kein
Cookie-Banner mit ([Datenschutzstandards](../../../../standards/datenschutz.md)); siehe in der EU die
[DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj).

## Verwandte Seiten

- [Kommunen](README.md)
- [Die ersten 90 Tage für Kommunen](die-ersten-90-tage.md)
- [Barrierefreiheit für Website-Ersteller](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
