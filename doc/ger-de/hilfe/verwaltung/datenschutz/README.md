---
title: Datenschutz für öffentliche Stellen
audience: government, project-manager, admin, developer, editor
category: public-sector
order: 10
summary: Der Datenschutzbereich der Hilfe für Verwaltungen - wie die Funktionen von Exponential der DSGVO und anderen Regeln zugeordnet sind, wo personenbezogene Daten liegen, Datenminimierung, Aufbewahrung, Einwilligungsnachweise für E-Mails, Auskunfts- und Löschanträge, Cookies, Erweiterungen von Drittanbietern, Formulare und die Dokumente, die der Betreiber schreibt (Verzeichnis von Verarbeitungstätigkeiten, DSFA, Datenschutzerklärung).
keywords: Datenschutz, DSGVO, Datenschutz-Grundverordnung, ePrivacy, Cookies, Einwilligung, Aufbewahrung, Auskunftsantrag, Löschung, Datenminimierung, Drittanbieter, Webanalyse, CAPTCHA, Formulare, Verzeichnis von Verarbeitungstätigkeiten, DSFA, Datenschutzerklärung, öffentlicher Sektor, Verwaltung, privacy, data protection, GDPR, consent, DPIA
type: help
---
# Datenschutz für öffentliche Stellen

Dieser Bereich richtet sich an Datenschutzbeauftragte, Projektleitungen, Administratoren, Entwickler und Redakteure
öffentlicher Stellen, die eine Website mit Exponential betreiben. Jede Aussage über das Produkt ist durch das
Repository belegt, vor allem durch [Datenschutzstandards](../../../../eng-us/standards/privacy.md): Exponential ist Software,
kein Verantwortlicher im Sinne des Datenschutzrechts; es stellt Funktionen bereit (Einwilligungsprotokoll, E-Mail nur
nach Opt-in, Löschung beim Entfernen eines Kontos, Pseudonymisierung im Audit, Maskierung und Aufbewahrungsfristen in
Protokollen, keine Ressourcen von Drittanbietern in der Administration) und benennt seine Lücken (keine einheitliche
Export- oder „Lösch mich“-Funktion, manuelles Entfernen von Konten, Protokolle standardmäßig ohne Altersgrenze,
Formulare ohne Aufbewahrungsfrist, kein Cookie-Banner).

> **Hinweis** Dieser Bereich beschreibt Funktionen und gängige Praxis; er ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Lesereihenfolge](#lesereihenfolge)
2. [Seiten in diesem Bereich](#seiten-in-diesem-bereich)
3. [Verwandte Seiten](#verwandte-seiten)

## Lesereihenfolge

- **Datenschutzbeauftragte**: DSGVO-Zuordnung, Verantwortlicher und Auftragsverarbeiter, Bestandsaufnahme
  personenbezogener Daten, Verzeichnis von Verarbeitungstätigkeiten, DSFA, Gliederung der Datenschutzerklärung.
- **Administratoren**: Aufbewahrung im Überblick mit den Seiten zu Audit und Protokollen, Einwilligungsnachweise,
  Sperrliste, Auskunft und Löschung, Einschränkung.
- **Projektleitungen und Entwickler**: Datenminimierung, Cookies und Banner, Erweiterungen von Drittanbietern,
  Webanalyse, CAPTCHAs, Karten, Social Login, Formulare, die Checkliste für Datenschutz durch Technikgestaltung.

## Seiten in diesem Bereich

| Seite | Beantwortet |
|---|---|
| [Die DSGVO und Exponential](dsgvo-zuordnung.md) | Thema für Thema: Funktionen und Lücken |
| [Verantwortlicher, Auftragsverarbeiter und die Software](verantwortlicher-auftragsverarbeiter-und-exponential.md) | wer rund um eine Website welche Rolle hat |
| [Wo personenbezogene Daten in Exponential liegen](bestandsaufnahme-personenbezogener-daten.md) | die Bestandsaufnahme mit Werkzeugen und Aufbewahrung |
| [Datenminimierung](datenminimierung.md) | standardmäßig minimiert; was Sie selbst minimieren |
| [Datenschutz im Audit-Trail](datenschutz-im-audit-trail.md) | Voreinstellungen, Pseudonymisierung, strenges Profil |
| [Maskierung in Protokollen und im Protokollbetrachter](maskierung-in-protokollen.md) | Maskieren beim Schreiben und beim Anzeigen |
| [Aufbewahrung personenbezogener Daten im Überblick](aufbewahrung-im-ueberblick.md) | jeder Mechanismus und seine Voreinstellung |
| [Aufbewahrung von Audit-Einträgen](aufbewahrung-von-audit-eintraegen.md) | 90 / 730 Tage und wie Sie sie ändern |
| [Aufbewahrung von Protokollen](aufbewahrung-von-protokollen.md) | Altersgrenzen und Bereinigung |
| [Einwilligungsnachweise für E-Mail](einwilligungsnachweise-fuer-e-mail.md) | das Einwilligungsprotokoll |
| [E-Mail-Kategorien und Opt-in](e-mail-kategorien-und-opt-in.md) | notwendig, optional, Double-Opt-in |
| [Abmelden mit einem Klick](abmelden-mit-einem-klick.md) | Fußzeile und List-Unsubscribe |
| [Die Sperrliste](sperrliste.md) | gehashte Einträge „keine E-Mail senden“ |
| [Das Auskunftsrecht](auskunftsrecht.md) | Exporte und Zusammenstellung |
| [Was nicht exportiert werden kann](was-nicht-exportiert-werden-kann.md) | die Grenzen und die Möglichkeiten |
| [Das Recht auf Löschung](recht-auf-loeschung.md) | automatisch, manuell, aufbewahrt |
| [Einschränkung und „Deaktivieren und verschieben“](einschraenkung-deaktivieren-und-verschieben.md) | Konten stilllegen, ohne sie zu löschen |
| [Von Exponential gesetzte Cookies](cookies.md) | die funktionalen Cookies des Kernels |
| [Cookie-Banner und Einwilligung](cookie-banner-und-einwilligung.md) | keines mitgeliefert; wann Sie eines brauchen |
| [Erweiterungen von Drittanbietern und Datenschutz](erweiterungen-von-drittanbietern.md) | Dienste, die nach dem Aktivieren Daten nach außen senden |
| [Webanalyse und Besucherstatistiken](webanalyse.md) | eigene Statistiken und Google Analytics |
| [CAPTCHAs und Datenschutz](captchas.md) | reCAPTCHA, hCaptcha und Alternativen |
| [Karten und eingebettete Inhalte](karten-und-einbettungen.md) | Datentyp für Google-Maps-Standorte und Einbettungen |
| [Social Login und Datenschutz](social-login.md) | Anmelden über Anbieter |
| [Formulare zur Informationssammlung und Datenschutz](formulare-des-information-collectors.md) | was Formulare speichern; manuelle Aufbewahrung |
| [Suchbegriffe](suchbegriffe.md) | Suchstatistiken |
| [Verzeichnis von Verarbeitungstätigkeiten für eine Website](verzeichnis-von-verarbeitungstaetigkeiten.md) | Fakten für jede Tätigkeit |
| [Datenschutz-Folgenabschätzung für eine Website](datenschutz-folgenabschaetzung.md) | wann und mit welchen Fakten |
| [Gliederung einer Datenschutzerklärung für eine Exponential-Website](gliederung-einer-datenschutzerklaerung.md) | Abschnitte und Fakten |
| [Datenschutzregeln außerhalb der EU](andere-rechtsordnungen.md) | Gesetze, die das Projekt zuordnet; ein Ansatz über Funktionen |
| [Checkliste Datenschutz durch Technikgestaltung](checkliste-datenschutz-durch-technikgestaltung.md) | von der Planung bis zum Betrieb |

Schritt-für-Schritt-Aufgaben (einen Auskunftsantrag zu E-Mail-Einstellungen beantworten, ein Benutzerkonto löschen,
Audit-Einträge über eine Person exportieren, Formulareinsendungen exportieren und entfernen) finden Sie in den
Compliance-Aufgaben dieser Hilfe ([Compliance-Aufgaben](../../../../eng-us/help/government/tasks/compliance/README.md)).

## Verwandte Seiten

- Nach oben: [Verwaltungen: hier beginnen](../verwaltungen-hier-beginnen.md)
- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Compliance und DSGVO für Hoster](../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
