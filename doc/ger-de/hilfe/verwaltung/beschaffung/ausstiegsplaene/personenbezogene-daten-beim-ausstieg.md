---
title: Personenbezogene Daten beim Ausstieg
audience: government, project-manager, admin
category: public-sector
order: 100
summary: Wie Sie mit personenbezogenen Daten umgehen, wenn eine Exponential-Website zu einem neuen Lieferanten oder System wechselt - herausfinden, wo personenbezogene Daten liegen, nur exportieren, was der Empfänger braucht, Passwörter, Einwilligungs- und Audit-Einträge, sichere Übertragung und bestätigte Löschung durch den bisherigen Lieferanten.
keywords: personenbezogene Daten, Datenschutz, DSGVO, Ausstieg, Datenübertragung, Löschung, Benutzerkonten, Passwörter, Einwilligungsnachweise, Audit-Protokoll, Aufbewahrung, Auftragsverarbeitungsvertrag, personal data, data protection, GDPR, exit, data transfer, deletion, user accounts, passwords, consent records, audit trail, retention, data processing agreement
type: help
---
# Personenbezogene Daten beim Ausstieg

Diese Seite richtet sich an Projektleitungen und Datenschutzbeauftragte, die den Teil eines Ausstiegs planen, der
personenbezogene Daten betrifft. Ein Ausstieg bewegt Daten zwischen Parteien und lässt Kopien zurück; beides sind
Momente, in denen personenbezogene Daten verloren gehen oder offengelegt werden können. Die Fakten dazu, wo Exponential
personenbezogene Daten speichert und was es exportiert, stammen aus [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) und
[Exponential verlassen, Abschnitt 5](../../../../installation/migration/exponential-verlassen.md#5-benutzer-und-personenbezogene-daten).

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Wo personenbezogene Daten in einer Website liegen](#wo-personenbezogene-daten-in-einer-website-liegen)
2. [Nur exportieren, was der Empfänger braucht](#nur-exportieren-was-der-empfänger-braucht)
3. [Passwörter](#passwörter)
4. [Einwilligungs- und Audit-Einträge](#einwilligungs--und-audit-einträge)
5. [Übertragung und Löschung](#übertragung-und-löschung)
6. [Checkliste](#checkliste)

## Wo personenbezogene Daten in einer Website liegen

| Daten | Wo | Export |
|---|---|---|
| Benutzerkonten (Name, E-Mail, Login) | Benutzerobjekte und die Tabelle `ezuser` | Website-Archiv (Benutzerklasse), Datenbank-Dump |
| Formulareingaben | Informationssammlungen | `ext:bccie:export` ([Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md)) |
| E-Mail-Einstellungen und Einwilligungsprotokoll | `expmail_consent_log` und zugehörige Tabellen | `exp:mailconsent`, `exp:mail:preferences export` |
| Newsletter-Abonnenten (wenn cjw_newsletter verwendet wird) | die Tabellen der Newsletter-Erweiterung | Datenbank-Dump |
| Audit-Protokoll | `var/<site>/log/audit/` | `exp:audit export` |
| Logdateien (Fehler, Anfragen, Sicherheit) | `var/log/` | Dateien; Adressen in einigen Logdateien maskiert |
| Suchbegriffe | Suchstatistiken | Datenbank-Dump; kann personenbezogene Daten enthalten |
| Bestellungen (wenn ein Shop verwendet wird) | Shop-Tabellen | Datenbank-Dump |
| Sitzungen | Sitzungsspeicher | nicht nötig; nie übertragen |

Die Datenschutzseite weist darauf hin, dass es keine einzelne Funktion „alle meine Daten exportieren“ gibt: Es sind
getrennte Werkzeuge.

## Nur exportieren, was der Empfänger braucht

- Vereinbaren Sie schriftlich, welche personenbezogenen Daten die übernehmende Partei für ihren Zweck braucht, und auf
  welcher Rechtsgrundlage.
- Exportieren Sie diese Teilmenge; bewahren Sie den Rest mit Zugriffskontrollen in Ihrem eigenen Archiv auf oder löschen
  Sie ihn nach Ihren Aufbewahrungsregeln, wie es die Seite zum Verlassen empfiehlt.
- Entfernen Sie inaktive Konten vor dem Umzug; **Benutzer > Benutzerkonten prüfen** (`/user/hygiene`) findet Konten,
  die seit einer Anzahl von Tagen nicht benutzt wurden ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)).
- Eine Test- oder Staging-Kopie beim neuen Lieferanten sollte erfundene oder minimierte Daten enthalten.

## Passwörter

Das Website-Archiv enthält nie Passwort-Hashes. Benutzer setzen auf dem Zielsystem neue Passwörter (Sie versenden
E-Mails zum Zurücksetzen), es sei denn, das Zielsystem kann Standardwerte von PHP `password_hash()` prüfen; dann kann
ein Datenbankexport der `ezuser`-Hashes vom Typ 7 verwendet werden. Die Seite zum Verlassen bittet Sie, das mit Ihrer
Datenschutzberatung zu entscheiden. Erwägen Sie auch, auf dem Zielsystem erneut die zweistufige Anmeldung zu verlangen.

## Einwilligungs- und Audit-Einträge

- **Einwilligungsnachweise** belegen, dass Personen optionalen E-Mails zugestimmt haben. Exportieren Sie sie
  (`exp:mailconsent`) und bewahren Sie sie so lange auf, wie Sie die Einwilligung womöglich nachweisen müssen; das
  empfangende System muss Widerrufe beachten.
- **Audit-Einträge** werden für ihre Aufbewahrungsdauer aufbewahrt und nach einer festgelegten Zeit pseudonymisiert
  ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)). Exportieren Sie sie mit ihrem signierten Manifest, damit
  sie verifizierbar bleiben, und bewahren Sie die Schlüssel in `settings/override/audit.ini.append.php` mit ihnen auf
  ([Übergabe von Schlüsseln und Geheimnissen](uebergabe-von-schluesseln-und-geheimnissen.md)).

## Übertragung und Löschung

- Übertragen Sie Exporte über einen verschlüsselten Kanal, nie als E-Mail-Anhang, wie es die Offboarding-Checkliste des
  Hosters vorsieht ([Kunden-Offboarding](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).
- Verarbeitet die übernehmende Partei personenbezogene Daten in Ihrem Auftrag, schließen Sie vor der Übertragung einen
  Auftragsverarbeitungsvertrag ab, wo die [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) gilt.
- Der bisherige Lieferant löscht Produktionsdaten, Staging-Kopien, Sicherungen und Logdateien zu den vereinbarten Terminen
  und bestätigt das schriftlich. Sicherungen dürfen bis zum Ende einer vereinbarten Aufbewahrungsfrist aufbewahrt werden,
  nicht länger.

## Checkliste

- [ ] Bestandsaufnahme der personenbezogenen Daten in der Website, nach der Tabelle oben.
- [ ] Teilmenge für den Empfänger mit Rechtsgrundlage vereinbart; der Rest archiviert oder gelöscht.
- [ ] Inaktive Konten vor dem Export bereinigt.
- [ ] Vorgehen bei Passwörtern mit der Datenschutzberatung entschieden; Benutzer informiert.
- [ ] Einwilligungs- und Audit-Einträge mit Manifesten exportiert; Schlüssel aufbewahrt.
- [ ] Verschlüsselte Übertragung; Auftragsverarbeitungsvertrag vorhanden.
- [ ] Löschung durch den bisherigen Lieferanten schriftlich mit Datum bestätigt.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Datenschutzstandards](../../../../../eng-us/standards/privacy.md)
- [Partner: Kundendaten schützen](../../../partner/kundendaten-schuetzen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
