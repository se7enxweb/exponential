---
title: Anforderungen an den Datenschutz
audience: government, project-manager, admin, partner
category: public-sector
order: 60
summary: Formulierungen für den Datenschutzteil einer Ausschreibung für ein CMS - Datenminimierung, Einwilligungsnachweise, Anfragen betroffener Personen, Aufbewahrung, Cookies, Dritte und Auftragsverarbeitungsverträge - mit dem, was Exponential bietet, und dem, was bei Ihrer Organisation bleibt.
keywords: Datenschutz, DSGVO, Anforderungen, Einwilligung, Löschung, Auskunftsrecht, Aufbewahrung, Cookies, ePrivacy, Auftragsverarbeiter, Auftragsverarbeitungsvertrag, AVV, Pseudonymisierung, Protokolle, Ausschreibung, privacy, data protection, GDPR, requirements, consent, erasure, right of access, retention, processor, data processing agreement, DPA, pseudonymization, logs, tender
type: help
---
# Anforderungen an den Datenschutz

Diese Seite richtet sich an Datenschutzbeauftragte, Beschaffungsteams und Projektleitungen, die den Datenschutzteil
einer Ausschreibung für ein CMS schreiben. Sie gibt Formulierungen für Anforderungen und ordnet jede Anforderung dem zu,
was die [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) für Exponential dokumentieren. Software stellt Funktionen
bereit; Ihre Organisation bleibt Verantwortliche und ist weiterhin für Zwecke, Rechtsgrundlagen, Hinweise und Anfragen
zuständig.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Hintergrund](#hintergrund)
2. [Formulierung der Anforderungen](#formulierung-der-anforderungen)
3. [Was Exponential bietet](#was-exponential-bietet)
4. [Was bei Ihrer Organisation oder dem Lieferanten bleibt](#was-bei-ihrer-organisation-oder-dem-lieferanten-bleibt)
5. [Fallstricke](#fallstricke)

## Hintergrund

- **In der EU** regelt die [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) personenbezogene Daten, einschließlich
  Datenschutz durch Technikgestaltung und durch datenschutzfreundliche Voreinstellungen (Art. 25) und Sicherheit der
  Verarbeitung (Art. 32). Die [ePrivacy-Richtlinie](https://eur-lex.europa.eu/eli/dir/2002/58/oj) regelt Cookies und
  Werbe-E-Mails.
- **Anderswo** gelten nationale Datenschutzgesetze; die Anforderungsbereiche unten sind den meisten gemeinsam.
- Ein Lieferant, der die Website hostet oder betreut, handelt meist als **Auftragsverarbeiter** und braucht einen
  Auftragsverarbeitungsvertrag.

## Formulierung der Anforderungen

| Id | Anforderung |
|---|---|
| PRI-01 | Das System minimiert personenbezogene Daten in Protokollen: IP-Adressen gekürzt oder maskiert, keine Passwörter, Tokens oder Formularinhalte geschrieben. Beschreiben Sie die Standardwerte. |
| PRI-02 | Protokolle und Audit-Einträge haben einstellbare Aufbewahrungsfristen und werden automatisch entfernt. Geben Sie die Standardwerte an. |
| PRI-03 | Die Einwilligung in optionale E-Mails erfolgt je Zweck per Opt-in, wird mit Zeitpunkt und angezeigtem Text festgehalten und lässt sich so leicht widerrufen, wie sie erteilt wurde. |
| PRI-04 | Die Organisation kann eine Auskunftsanfrage beantworten: die über eine Person gespeicherten Daten in einem maschinenlesbaren Format exportieren. Beschreiben Sie, welche Daten jedes Werkzeug abdeckt. |
| PRI-05 | Die Organisation kann das Konto einer Person und die damit verknüpften Daten löschen; beschreiben Sie, was gelöscht, anonymisiert oder aufbewahrt wird. |
| PRI-06 | Die Administration lädt keine Skripte, Schriften oder anderen Ressourcen von Hosts Dritter. |
| PRI-07 | Die öffentliche Website setzt nur unbedingt erforderliche Cookies, sofern die Organisation keinen Dienst mit Einwilligung hinzufügt. Führen Sie jedes Cookie auf. |
| PRI-08 | Jeder Dienst Dritter, den die gelieferte Website nutzt, wird mit seinem Zweck und den Daten, die er erhält, aufgeführt. |
| PRI-09 | Der Lieferant unterzeichnet einen Auftragsverarbeitungsvertrag und führt seine Unterauftragsverarbeiter und Datenstandorte auf. |

## Was Exponential bietet

| Id | Dokumentierte Funktion | Lücke |
|---|---|---|
| PRI-01 | Audit: Adressen gekürzt (/24, IPv6 /48), Sitzungs-IDs gehasht, keine Passwörter oder POST-Inhalte; Anfrageprotokolle `MaskIP=enabled`; der Log-Betrachter maskiert IPs, E-Mail-Adressen und Geheimnisse ([Log-Betrachter](../../../../../eng-us/features/6.0/log-viewer.md)) | Zugriffsprotokolle des Webservers liegen außerhalb von Exponential |
| PRI-02 | Audit je Kanal 90 Tage live und 2 Jahre archiviert, nach einer festgelegten Zeit pseudonymisiert; 404-, Sicherheits- und CSP-Protokolle haben `MaxAgeDays` ([Rotation der Logdateien](../../../../../eng-us/features/6.0/log-rotation.md)) | Kernel-Protokolle wie `error.log` rotieren nach Größe, standardmäßig ohne Altersgrenze |
| PRI-03 | E-Mail-Kategorien aus, bis sie eingeschaltet werden, Einwilligungsprotokoll in der Tabelle `expmail_consent_log`, Abmeldung mit einem Klick ([E-Mail-Einstellungen](../../../../../eng-us/features/6.0/mail-preferences.md)) | Ohne Kategorie versandte Werbe-E-Mails erhalten keine Fußzeile |
| PRI-04 | E-Mail-Einstellungen und Einwilligungsverlauf als JSON oder CSV; Audit-Einträge über einen Benutzer mit `./console exp:audit export --subject-user=<id>` | **Keine einzelne Funktion „alle meine Daten exportieren“** |
| PRI-05 | Das Entfernen eines Benutzers löscht die E-Mail-Einstellungen und anonymisiert das Einwilligungsprotokoll; „Deaktivieren und verschieben“ zur Einschränkung; **Benutzerkonten prüfen** findet unbenutzte Konten ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)) | Das Entfernen erfolgt manuell, ein Konto nach dem anderen; Formulareingaben werden gesondert erreicht |
| PRI-06 | Skripte der Administration werden lokal ausgeliefert; admin4 lädt keine externen Schriften oder Skripte | - |
| PRI-07 | Der Kernel setzt nur funktionale Cookies (Sitzung, `is_logged_in`, Zustand der Oberfläche in der Administration) | **Kein Cookie-Einwilligungsbanner** im Kernel oder in mitgelieferten Designs |
| PRI-08 | Erweiterungen, die Dritte kontaktieren (Analyse, reCAPTCHA, hCaptcha, Social Login), sind installiert, aber standardmäßig nicht aktiv | Ob ein Website-Paket eine davon aktiviert: `ActiveExtensions[]` prüfen |
| PRI-09 | - | Eine Leistungsfrage für den Lieferanten; siehe [Hosting: Compliance und DSGVO](../../../hosting/sicherheit/compliance-und-dsgvo.md) |

## Was bei Ihrer Organisation oder dem Lieferanten bleibt

- Das Verzeichnis der Verarbeitungstätigkeiten, der Datenschutzhinweis und die Rechtsgrundlagen.
- Eine Lösung zur Cookie-Einwilligung, wenn die Website Analyse, eingebettete Medien oder andere Dienste Dritter
  hinzufügt.
- Aufbewahrungsregeln für Formulare des Information Collectors; die Website muss selbst einen Einwilligungstext und eine
  Aufbewahrungsregel ergänzen ([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)).
- Suchbegriffe werden gespeichert und können personenbezogene Daten enthalten ([URLs und Aliase](../../../../anleitungen/urls-und-aliase.md)).
- Sicherungen enthalten personenbezogene Daten und Zugangsdaten; ihre Aufbewahrung und Verschlüsselung gehören in den
  Hosting-Vertrag ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).

## Fallstricke

- Von der Software verlangen, „DSGVO-konform“ zu sein. Ein Produkt kann das nicht sein; fragen Sie nach den Funktionen
  oben und bewerten Sie sie.
- Die Erweiterungen Dritter vergessen, die ein Lieferant beim Bau hinzufügt. Verlangen Sie die endgültige Liste
  `ActiveExtensions[]` als Lieferung.
- Newsletter-Statistiken für harmlos halten: Das Zählen von Öffnungen und Klicks je Person braucht eine vorherige
  Einwilligung ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)).

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Ausschreibungsvorlage: Klausel zu Datenschutz und Auftragsverarbeitung](../ausschreibungsvorlagen/klausel-datenschutz-und-auftragsverarbeitung.md)
- [Anforderungen an Schriftgut und Audit](anforderungen-an-schriftgut-und-audit.md)
- [E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
