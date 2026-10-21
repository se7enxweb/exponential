---
title: "FAQ: Datenschutz"
audience: government, project-manager, admin
category: public-sector
order: 40
summary: Antworten auf die Datenschutzfragen öffentlicher Stellen zu Exponential - DSGVO, Cookies, Einwilligung, Daten an Drittanbieter, Auskunfts- und Löschanträge, Protokolle und Aufbewahrung, KI-Funktionen -, belegt durch die Seite Datenschutzstandards, mit den Entscheidungen, die bei der Organisation bleiben.
keywords: FAQ Datenschutz, DSGVO, Cookies, Cookie-Banner, Einwilligung, Drittanbieter, Webanalyse, Löschung, Auskunftsersuchen, Aufbewahrung, Protokolle, Pseudonymisierung, öffentlicher Sektor, privacy FAQ, GDPR, data protection, cookie banner, consent, third parties, analytics, erasure, access request, retention, logs, pseudonymization, public sector
type: help
---
# FAQ: Datenschutz

Diese Seite beantwortet die Fragen, die Datenschutzbeauftragte öffentlicher Stellen zu Exponential stellen. Die Antworten
stammen aus den [Datenschutzstandards](../../../../eng-us/standards/privacy.md), die festhalten, dass Exponential Software ist und
kein Verantwortlicher: Die Einhaltung liegt in der Verantwortung der Organisation, die die Website betreibt.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Ist Exponential DSGVO-konform?](#ist-exponential-dsgvo-konform)
2. [Welche Cookies setzt es? Brauchen wir ein Banner?](#welche-cookies-setzt-es-brauchen-wir-ein-banner)
3. [Sendet es Daten an Drittanbieter?](#sendet-es-daten-an-drittanbieter)
4. [Wie beantworten wir einen Auskunfts- oder Löschantrag?](#wie-beantworten-wir-einen-auskunfts--oder-löschantrag)
5. [Wie lange werden Protokolle aufbewahrt, und was enthalten sie?](#wie-lange-werden-protokolle-aufbewahrt-und-was-enthalten-sie)
6. [Wie wird die Einwilligung für E-Mails gehandhabt?](#wie-wird-die-einwilligung-für-e-mails-gehandhabt)
7. [Wie steht es um den KI-Assistenten?](#wie-steht-es-um-den-ki-assistenten)

## Ist Exponential DSGVO-konform?

Software kann für sich allein nicht konform sein; die Organisation ist es. Exponential bietet Funktionen, die helfen:
ein Einwilligungsprotokoll für E-Mails, E-Mail-Kategorien mit Opt-in, Löschung beim Entfernen eines Kontos,
Pseudonymisierung im Audit, Maskierung von IP-Adressen in Protokollen und keine Ressourcen von Drittanbietern in der
Administration. Der Status ist **Partly complies (features provided)** (teilweise erfüllt, Funktionen vorhanden)
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#gdpr)). Zwecke, Rechtsgrundlagen, die Datenschutzerklärung, das
Verzeichnis von Verarbeitungstätigkeiten und Auftragsverarbeitungsverträge bleiben bei Ihnen.

## Welche Cookies setzt es? Brauchen wir ein Banner?

Der Kernel setzt nur funktionale Cookies: das Sitzungscookie, `is_logged_in`, das eigene Cookie des
Einrichtungsassistenten während der Installation, ein optionales Cookie zur Erkennung mobiler Geräte und Cookies für den
Zustand der Oberfläche in der Administration
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#eprivacy-directive-cookies)). Es gibt **kein Cookie-Banner zur
Einwilligung**. Wenn Ihre Website Webanalyse, Einbettungen oder Werbung hinzufügt, müssen Sie eines hinzufügen.

## Sendet es Daten an Drittanbieter?

Nicht aus der Administration: Ihre Skripte und Schriften werden lokal ausgeliefert. Einige mitgelieferte Erweiterungen
tun es, wenn sie aktiviert sind: Google Analytics (`bcwebsitestatistics`), reCAPTCHA und hCaptcha sowie Anbieter für
Social Login. Sie sind installiert, aber **nicht aktiv**, bis eine Website sie aktiviert
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)). Prüfen Sie
`ActiveExtensions[]` auf Ihrer Website.

## Wie beantworten wir einen Auskunfts- oder Löschantrag?

- E-Mail-Einstellungen und Einwilligungsverlauf: von der Person oder einem Administrator als JSON oder CSV exportierbar.
- Audit-Einträge über einen Benutzer: `./console exp:audit export --subject-user=<id>`.
- Löschung: Das Entfernen des Kontos löscht die E-Mail-Einstellungen und anonymisiert das Einwilligungsprotokoll;
  Audit-Einträge werden für ihre Aufbewahrungsfrist behalten und pseudonymisiert.

Es gibt **keine einzelne Funktion „alle meine Daten exportieren“ oder „mich löschen“**: Inhalte, Formulareinsendungen
und Bestellungen erreichen Sie über getrennte Werkzeuge der Administration, und Konten werden einzeln entfernt
([Datenschutzstandards](../../../../eng-us/standards/privacy.md#known-gaps)).

## Wie lange werden Protokolle aufbewahrt, und was enthalten sie?

Der Audit-Trail behält pro Kanal 90 Tage aktiv und zwei Jahre archiviert, mit gekürzten Adressen und ohne Passwörter
oder Token. Die Protokolle für 404, Sicherheit und CSP haben Altersgrenzen; andere Protokolle des Kernels werden nach
Größe rotiert, ohne Altersgrenze, sofern Sie keine festlegen ([Protokollrotation](../../../../eng-us/features/6.0/log-rotation.md)).
Der Protokollbetrachter maskiert IP- und E-Mail-Adressen ([Protokollbetrachter](../../../../eng-us/features/6.0/log-viewer.md)).
Die Zugriffsprotokolle des Webservers liegen außerhalb von Exponential.

## Wie wird die Einwilligung für E-Mails gehandhabt?

Optionale E-Mail-Kategorien sind ausgeschaltet, bis die Person sie einschaltet; jede Änderung wird mit dem angezeigten
Text protokolliert; jede optionale E-Mail hat eine Abmeldung mit einem Klick
([E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md)).

## Wie steht es um den KI-Assistenten?

Exponential Intelligence ist eine Erweiterung, die bis zur Aktivierung inaktiv ist. Bei der Nutzung geht die Anfrage der
Redaktion an den gewählten Anbieter: Anthropic, einen OpenAI-kompatiblen Dienst (die Funktionsseite nennt darunter einen
Dienst in der EU) oder ein lokales Modell über Ollama, das die Daten auf Ihrem eigenen Server hält
([Exponential Intelligence](../../../../eng-us/features/6.0/extensions/expai.md)). Wählen Sie den Anbieter mit Ihren
Datenschutzbeauftragten und nennen Sie ihn in Ihren Aufzeichnungen.

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Checkliste Datenschutz](../checklisten/checkliste-datenschutz.md)
- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
