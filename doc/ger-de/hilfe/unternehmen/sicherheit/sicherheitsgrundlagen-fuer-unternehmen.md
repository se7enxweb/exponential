---
title: Sicherheitsgrundlagen für Unternehmen
audience: businesses, admin, project-manager
category: business-solutions
order: 10
summary: Die zehn Sicherheitsgrundlagen, die eine Unternehmenswebsite auf Exponential braucht, was die Software für jede bereits tut, was Sie entscheiden müssen und wer sich in einer kleinen Organisation meist darum kümmert. Keine Rechtsberatung.
keywords: Sicherheitsgrundlagen, Updates, Passwörter, zweistufige Anmeldung, Rollen, minimale Rechte, Backups, Wiederherstellung, Administration, Überwachung, Prüfprotokoll, Vorfall, Verantwortung, kleines Unternehmen, security basics, HTTPS, updates, passwords, two-step sign-in, roles, least privilege, backups, restore, administration, monitoring, audit trail, incident, responsibility, small business
type: help
---
# Sicherheitsgrundlagen für Unternehmen

Diese Seite richtet sich an Inhaber und Führungskräfte, die für eine Webanwendung verantwortlich sind, aber keine
Sicherheitsfachleute sind. Sie listet zehn Grundlagen auf, sagt, was Exponential für jede bereits tut, was Sie noch
entscheiden müssen und wer sich in einer kleinen Organisation meist darum kümmert. Das technische „Wie“ steht auf den
verlinkten Seiten.

> **Warnung**
> Dies ist eine Zusammenfassung von Grundlagen, keine rechtliche oder fachliche Sicherheitsberatung. Gesetze und
> Verträge können mehr von Ihnen verlangen; lesen Sie sie und fragen Sie bei Bedarf einen Anwalt, Ihren
> Datenschutzbeauftragten oder einen Sicherheitsfachmann.

## Inhalt

1. [Wer wofür verantwortlich ist](#wer-wofür-verantwortlich-ist)
2. [Die zehn Grundlagen](#die-zehn-grundlagen)
3. [Was die Software standardmäßig tut](#was-die-software-standardmäßig-tut)
4. [Was Sie entscheiden müssen](#was-sie-entscheiden-müssen)
5. [Eine Checkliste auf einer Seite](#eine-checkliste-auf-einer-seite)

## Wer wofür verantwortlich ist

Sicherheit ist geteilt. Eine typische Aufteilung in einer kleinen Organisation:

| Partei | Meist verantwortlich für |
|---|---|
| Verantwortlicher der Website (Sie) | Entscheiden, wer Zugriff bekommt, welche Daten Sie erheben, Verträge, Menschen nach einem Vorfall informieren |
| Administrator (Mitarbeiter oder Partner) | Rollen, Konten, Updates der Anwendung, Einstellungen, das Prüfprotokoll |
| Hoster | Server, Betriebssystem, PHP, Netzwerk, Backups der Maschine, physische Sicherheit |
| Entwickler oder Partner | Eigene Templates und Erweiterungen, sie über Updates hinweg sicher halten |
| Das Exponential-Projekt | Schwachstellen in der Software beheben und Releases veröffentlichen (`SECURITY.md`) |

Schreiben Sie auf, wer welche Aufgabe für Ihre Website übernimmt. Die
[Vorlage für Projektbeschreibungen](../vorlagen/vorlage-fuer-projektbeschreibungen.md) und die RACI-Matrix in
[Projektvorlagen](../../fuehrung/projektvorlagen.md) haben Platz dafür.

## Die zehn Grundlagen

| Nr. | Grundlage | Was Exponential bietet | Lesen |
|---|---|---|---|
| 1 | Überall HTTPS | Velocity liefert HTTPS selbst aus, einschließlich Zertifikaten von Let's Encrypt; sichere Cookies bei HTTPS | [TLS und HTTPS](../../hosting/sicherheit/tls-und-https.md) |
| 2 | Aktuelle Software | Eine gepflegte Linie; Korrekturen kommen mit dem nächsten Release 6.0.x | [Sicherheitsupdates und Offenlegung](sicherheitsupdates-und-offenlegung.md) |
| 3 | Starke Anmeldung | bcrypt-Hashes, gängige Passwörter abgelehnt, zweistufige Anmeldung | [Benutzerkonten und Passwörter](benutzerkonten-und-passwoerter.md) |
| 4 | Minimale Rechte | Feingranulare Rollen und Richtlinien; niemand bekommt etwas, das keine Rolle gibt | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |
| 5 | Backups, die sich wiederherstellen lassen | Eine dokumentierte Liste dessen, was zu sichern ist, und ein Verfahren zum Testen der Wiederherstellung | [Backups, die sich wiederherstellen lassen](../zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md) |
| 6 | Eine geschützte Administration | Ein eigener Hostname, Netzwerkbeschränkungen, Request-Shield davor | [Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md) |
| 7 | Hinschauen | Anfrage-Logs, ein Log-Viewer und Audit-Alarme bei Brute Force und riskanten Änderungen | [Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md) |
| 8 | Ihre personenbezogenen Daten kennen | Mail-Einwilligung, Formularexporte, Kontenlisten, Aufbewahrungseinstellungen | [Personenbezogene Daten in Exponential](personenbezogene-daten-in-exponential.md) |
| 9 | Ein Weg, Probleme zu melden | Vertrauliche Meldungen an das Projekt, erste Antwort innerhalb eines Werktags | `SECURITY.md` |
| 10 | Ein Plan für Vorfälle | Runbooks für eine kompromittierte oder ausgefallene Website | [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md) |

## Was die Software standardmäßig tut

Aus [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md) und [Sicherheitshärtung](../../sicherheitshaertung.md):

- Jede Seite trägt Sicherheits-Header wie `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` und
  `Permissions-Policy`.
- Das Sitzungs-Cookie ist `HttpOnly`, `SameSite=Lax` und bei HTTPS `Secure`; das Abmelden beendet die Sitzung endgültig.
- Hochgeladene Bilder werden nach ihrem Inhalt geprüft, nicht nach ihrem Namen; SVG-Bilder werden nur bereinigt
  gespeichert.
- Kein Installer lässt das alte Standard-Administratorpasswort bestehen
  ([Das Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
- Die Anmeldung verrät nicht, welche Konten existieren; die Alarmregeln des Audits melden Brute-Force-Versuche.
- In der Administration wird eine Content Security Policy im Nur-Bericht-Modus gesendet
  ([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).
- Das Prüfprotokoll ist standardmäßig eingeschaltet, braucht keine Einrichtung und zeichnet Anmeldungen,
  Inhaltsänderungen, geschriebene Einstellungen und Bestellungen auf ([Das Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)).

## Was Sie entscheiden müssen

- [ ] Ob die zweistufige Anmeldung Pflicht ist, zumindest für Administratoren und Redakteure.
- [ ] Wer sich in der Administration anmelden darf, und von wo.
- [ ] Welche personenbezogenen Daten die Website erhebt und wie lange sie sie aufbewahrt
      ([Aufbewahrung und Löschung von Daten](aufbewahrung-und-loeschung-von-daten.md)).
- [ ] Wer die Alarmmails des Prüfprotokolls bekommt (Standard: die `AdminEmail` der Website).
- [ ] Wie schnell Sie ein Sicherheits-Release einspielen und wer das tut.
- [ ] Ob die Content Security Policy nach Tests auf Ihrer öffentlichen Website durchgesetzt werden soll.
- [ ] Was Sie Kunden sagen, wenn etwas schiefgeht, und wer das entscheidet.

## Eine Checkliste auf einer Seite

Drucken Sie sie aus und haken Sie sie einmal im Jahr oder nach jeder großen Änderung ab:

- [ ] Die Website antwortet nur über HTTPS; das Zertifikat erneuert sich selbst.
- [ ] Das neueste Release 6.0.x ist installiert; die Erweiterungen sind aktuell.
- [ ] Jede Person hat ein persönliches Konto; es gibt kein gemeinsam genutztes Administratorkonto.
- [ ] Die zweistufige Anmeldung ist für Administratoren eingeschaltet.
- [ ] Die Rollen wurden in diesem Jahr geprüft ([Zugriffsprüfungen](zugriffspruefungen.md)).
- [ ] In diesem Quartal wurde eine Wiederherstellung getestet.
- [ ] Audit-Alarme erreichen eine Person, die sie liest.
- [ ] Die Liste der personenbezogenen Daten und ihrer Aufbewahrung ist aufgeschrieben.
- [ ] Die Mitarbeiter wissen, dass sie vermutete Schwachstellen an security@se7enx.com melden, nicht öffentlich.
- [ ] Das Runbook für Vorfälle ist dem bekannt, der gerade Dienst hat.

Die vollständige technische Checkliste steht in
[Checklisten zu Barrierefreiheit, Sicherheit und Compliance](../../fuehrung/checklisten-barrierefreiheit-sicherheit-und-compliance.md).

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Leitfaden Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Härtung für Hoster](../../hosting/sicherheit/haertung-fuer-hoster.md)
- [Sicherheitscheckliste für Shops](../shops/sicherheitscheckliste-fuer-shops.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
