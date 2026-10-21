---
title: Checkliste Datenschutz durch Technikgestaltung
audience: government, project-manager, admin, developer
category: public-sector
order: 320
summary: Eine Checkliste für öffentliche Stellen, die eine Website mit Exponential bauen oder überprüfen - Datenschutz durch Technikgestaltung und durch datenschutzfreundliche Voreinstellungen von der Planung über den Start bis zum Betrieb, mit der Einstellung oder dem Werkzeug für jeden Punkt.
keywords: Datenschutz durch Technikgestaltung, datenschutzfreundliche Voreinstellungen, DSGVO Artikel 25, Checkliste, Start, Überprüfung, Inhaltsmodell, Formulare, Erweiterungen, Aufbewahrung, Protokolle, Audit, E-Mail, Cookies, Betroffenenrechte, öffentlicher Sektor, privacy by design, privacy by default, checklist, GDPR
type: help
---
# Checkliste Datenschutz durch Technikgestaltung

Diese Seite richtet sich an Projektleitungen, Datenschutzbeauftragte, Administratoren und Entwickler. Nutzen Sie sie
bei der Planung einer neuen Website, vor dem Start und bei jeder jährlichen Überprüfung. Jeder Punkt verweist auf die
Stelle in Exponential, an der er erledigt wird; Einzelheiten stehen auf den Seiten dieses Bereichs.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Planung](#planung)
2. [Aufbau](#aufbau)
3. [Vor dem Start](#vor-dem-start)
4. [Betrieb](#betrieb)
5. [Verwandte Seiten](#verwandte-seiten)

## Planung

- [ ] Zwecke und Rechtsgrundlagen für jede Verarbeitungstätigkeit schriftlich festgehalten.
- [ ] Bestandsaufnahme der Daten entworfen („Wo personenbezogene Daten in Exponential liegen“).
- [ ] Entscheidung über eine DSFA getroffen und dokumentiert.
- [ ] Auftragsverarbeiter benannt; Verträge geplant (Hosting, Umsetzung, Support).
- [ ] Über Dienste von Drittanbietern einzeln entschieden; keiner „für alle Fälle“ aktiviert.

## Aufbau

- [ ] Benutzerklasse nur mit den nötigen Feldern.
- [ ] Formulare nur mit den nötigen Feldern, je einem Hinweis daneben, einer zuständigen Person und einer
      Aufbewahrungsregel.
- [ ] Kein Skript und keine Einbettung eines Drittanbieters vor der Einwilligung geladen, wo eine Einwilligung nötig
      ist; die CSP erlaubt nur die genutzten Hosts.
- [ ] E-Mail: Jede optionale E-Mail hat eine Kategorie; Absenderangaben in der Fußzeile gesetzt
      (`[FooterSettings] OrganisationName`, `OrganisationAddress`); das Website-Geheimnis gesichert.
- [ ] Suchstatistiken aus, sofern nicht benötigt.
- [ ] Datenschutzeinstellungen des Audits überprüft (`[AuditPrivacySettings]`); über das Aufzeichnen des
      Veröffentlichens entschieden.
- [ ] Maskierung in Protokollen eingeschaltet gelassen (`MaskIP=enabled`, Maskierung im Protokollbetrachter).

## Vor dem Start

- [ ] Aufbewahrungseinstellungen angewandt: Audit-Kanäle, `MaxAgeDays` pro Protokoll, Einwilligungsprotokoll,
      Sperrliste.
- [ ] Cronjobs laufen: `frequent` (Audit), `infrequent` (E-Mail-Einstellungen), `logs`, wenn Sie sich auf
      Altersgrenzen verlassen.
- [ ] Cookies in einem privaten Fenster geprüft; Cookie-Hinweis geschrieben.
- [ ] Datenschutzerklärung veröffentlicht und von jeder Seite, jedem Formular und jeder E-Mail aus verlinkt.
- [ ] Richtlinien für sensible Daten nur an wenige vergeben: `infocollector/read`, `mailpreferences/export`,
      `audit/read`, `setup/unmasklogs`, `role/assign` (zeigt E-Mail-Adressen in Kontolisten).
- [ ] Verfahren für Auskunft und Löschung schriftlich festgelegt und an einem Testkonto erprobt.

## Betrieb

- [ ] Formulareinsendungen nach Plan entfernt.
- [ ] Suchstatistiken nach Plan zurückgesetzt.
- [ ] Überprüfung der Konten zweimal im Jahr (Benutzer > Benutzerkonten prüfen).
- [ ] Exporte nach der Nutzung gelöscht.
- [ ] Datenschutzerklärung überprüft, wenn sich Erweiterungen, Formulare oder Kategorien ändern.
- [ ] Jährliche Probeläufe der Aufbewahrung: `./console exp:logs --prune --dry-run`,
      `./console exp:mail:consent cleanup --dry-run`, `./console exp:audit purge --dry-run`.

## Verwandte Seiten

- [Datenminimierung](datenminimierung.md)
- [Aufbewahrung personenbezogener Daten im Überblick](aufbewahrung-im-ueberblick.md)
- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
