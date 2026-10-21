---
title: Checkliste Datenschutz
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Eine Checkliste zu Datenschutz für eine öffentliche Stelle, die Exponential betreibt - Bestandsaufnahme, Hinweise, Cookies, Formulare, Einwilligung für E-Mails, Protokolle und Aufbewahrung, Anfragen betroffener Personen und Auftragsverarbeiter - mit der Trennung zwischen den Funktionen von Exponential und den Entscheidungen, die bei der Organisation bleiben.
keywords: Datenschutz, DSGVO, Checkliste, Datenschutzerklärung, Cookies, Einwilligung, Formulare, Aufbewahrung, Protokolle, Löschung, Auskunftsersuchen, Auftragsverarbeiter, Auftragsverarbeitungsvertrag, öffentlicher Sektor, privacy, data protection, GDPR, checklist, privacy notice, consent, forms, retention, logs, erasure, access request, processor, processing agreement, public sector
type: help
---
# Checkliste Datenschutz

Diese Checkliste richtet sich an die Datenschutzbeauftragten und die Website-Administration einer öffentlichen Stelle.
Sie folgt den [Datenschutzstandards](../../../../eng-us/standards/privacy.md), die festhalten: Exponential ist Software, kein
Verantwortlicher; die Einhaltung liegt in der Verantwortung der Organisation, die die Website betreibt.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung. In der EU: [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj),
> [ePrivacy-Richtlinie](https://eur-lex.europa.eu/eli/dir/2002/58/oj).

## Inhalt

1. [Bestandsaufnahme](#bestandsaufnahme)
2. [Hinweise und Cookies](#hinweise-und-cookies)
3. [Formulare und E-Mail](#formulare-und-e-mail)
4. [Protokolle und Aufbewahrung](#protokolle-und-aufbewahrung)
5. [Anfragen betroffener Personen](#anfragen-betroffener-personen)
6. [Dienstleister](#dienstleister)

## Bestandsaufnahme

- [ ] Halten Sie fest, wo personenbezogene Daten liegen: Benutzerkonten, Formulareinsendungen ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)), Abonnenten von Newslettern, Bestellungen, Protokolle, Suchbegriffe ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#known-gaps)).
- [ ] Halten Sie jede aktive Erweiterung fest, die Daten an einen Drittanbieter sendet: `ActiveExtensions[]` in `settings/override/site.ini.append.php` ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)).
- [ ] Halten Sie Zwecke und Rechtsgrundlagen in Ihrem Verzeichnis von Verarbeitungstätigkeiten fest.

## Hinweise und Cookies

- [ ] Datenschutzerklärung veröffentlicht und von jeder Seite aus verlinkt.
- [ ] Der Kernel setzt nur funktionale Cookies; führen Sie sie in der Erklärung auf ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#eprivacy-directive-cookies)).
- [ ] Wenn die Website Webanalyse, Einbettungen oder Werbung hinzufügt, fügen Sie ein Banner zur Einwilligung hinzu: Exponential hat keines ([Statistiken und Webanalyse](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)).
- [ ] CAPTCHA-Dienste (reCAPTCHA, hCaptcha) in der Erklärung genannt, wenn sie verwendet werden ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).

## Formulare und E-Mail

- [ ] Jedes Formular fragt nur nach dem, was es braucht, und zeigt einen Einwilligungs- oder Informationstext ([Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).
- [ ] Jedes Formular hat eine Aufbewahrungsregel; alte Einsendungen werden entfernt ([Formulareinsendungen](../../../../eng-us/help/creators/website-creators/form-submissions.md)).
- [ ] E-Mail-Einstellungen eingerichtet: Kategorien standardmäßig ausgeschaltet, Einwilligungsprotokoll wird geführt ([E-Mail-Einstellungen einrichten](../../../anleitungen/mail-einstellungen-administrator.md)).
- [ ] Absenderangaben (Postanschrift) für die Fußzeile der E-Mails gesetzt ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#can-spam-act-and-one-click-unsubscribe)).
- [ ] Newsletter-Statistiken nur mit vorheriger Einwilligung ([Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)).

## Protokolle und Aufbewahrung

- [ ] Datenschutzeinstellungen des Audits geprüft: gekürzte Adressen, Pseudonymisierung nach `PseudonymiseAfterDays` ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).
- [ ] Anfrageprotokolle maskieren IP-Adressen (`MaskIP=enabled`) ([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)).
- [ ] Andere Protokolle des Kernels als 404, Sicherheit und CSP haben standardmäßig keine Altersgrenze; setzen Sie `MaxAgeDays` oder rotieren Sie sie selbst ([Protokollrotation](../../../../eng-us/features/6.0/log-rotation.md)).
- [ ] Zugriffsprotokolle des Webservers werden nach der Aufbewahrungsregel des Hosters behandelt.
- [ ] Auch Datensicherungen haben eine Aufbewahrungsfrist ([Sicherungspläne und Aufbewahrung](../../hosting/betrieb/backup-zeitplaene-und-aufbewahrung.md)).

## Anfragen betroffener Personen

- [ ] Auskunft und Datenübertragbarkeit: Export der E-Mail-Einstellungen und der Einwilligungsgeschichte (`mailpreferences/export`, `./console exp:mail:preferences export`); Audit-Einträge mit `./console exp:audit export --subject-user=<id>` ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#gdpr)).
- [ ] Andere Daten (Formulareinsendungen, Inhalte, die die Person geschrieben hat, Bestellungen) werden über die einzelnen Werkzeuge der Administration gefunden; es gibt kein einzelnes „alle meine Daten exportieren“ ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#known-gaps)).
- [ ] Löschung: Das Entfernen des Kontos löscht die E-Mail-Einstellungen und anonymisiert das Einwilligungsprotokoll; Formulareinsendungen entfernen Sie getrennt.
- [ ] Einschränkung: **Deaktivieren und verschieben**, wo eingeschaltet ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
- [ ] Jede Anfrage wird mit Datum und Antwort in Ihrem eigenen Register festgehalten.

## Dienstleister

- [ ] Auftragsverarbeitungsvertrag mit dem Hoster und dem Supportdienstleister.
- [ ] Dienstleister verwenden personenbezogene Daten aus der Produktion nie in Schulungs- oder Testkopien.
- [ ] Der Datenstandort ist im Hostingvertrag angegeben ([Ein Hostingmodell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).
- [ ] Support-Tickets und Fehlerberichte enthalten keine personenbezogenen Daten ([Support erhalten](../../projekt/unterstuetzung-erhalten.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Compliance und DSGVO (für Hoster)](../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
