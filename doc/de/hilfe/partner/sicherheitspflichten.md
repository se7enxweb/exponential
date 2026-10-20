---
title: Sicherheitspflichten von Partnern
audience: partner
category: partnership
order: 690
summary: Wofür Partner bei der Sicherheit der Websites verantwortlich sind, die sie bauen und hosten - sichere Standardwerte, Härtung, Updates, Zugangsdaten, vertrauliche Meldung, Vorfälle und eine Sicherheits-Checkliste pro Website.
keywords: Sicherheit, Pflichten, Härtung, Updates, Zugangsdaten, Passwörter, Offenlegung, Vorfall, Checkliste, CSP, TLS, security, responsibilities, hardening, credentials, passwords, disclosure, incident, checklist
type: help
---
# Sicherheitspflichten von Partnern

Diese Seite ist für Partner, die Exponential-Websites bauen, hosten oder pflegen. Laut Partnervertrag gilt: „7x übernimmt keine Gewähr für die von Partnern gelieferten Produkte und Dienstleistungen“ (<https://exponential.earth/partner/program>). Die Sicherheit der Websites Ihrer Kunden liegt in Ihrer Verantwortung.

## Geteilte Verantwortung

| Wer | Verantwortlich für |
|---|---|
| Das Projekt | Sicheren Code im Kernel und seinen Erweiterungen; in Releases veröffentlichte Korrekturen (SECURITY.md) |
| Der Hosting-Partner | Server, PHP, Datenbank, TLS, Netzwerk, Backups ([Technische Checkliste für Hosting-Partner](technische-checkliste-fuer-hosting-partner.md)) |
| Der Implementierungspartner | Eigenen Code, Konfiguration, gewählte Erweiterungen, eingespielte Updates |
| Der Kunde | Konten und Passwörter seiner Redakteure, Inhalte |

Schreiben Sie die Aufteilung in jeden Vertrag.

## Checkliste pro Website

- [ ] Gehärtet wie in [Sicherheitshärtung](../../../en/us/help/security-hardening.md) und `doc/en/us/install/13-security-hardening.md`
- [ ] Standard- und gemeinsam genutzte Passwörter geändert ([Standardpasswort des Administrators](../../../en/us/help/default-administrator-password.md),
      [Das Administratorpasswort ändern](../../../en/us/help/change-the-admin-password.md))
- [ ] Setup-Assistent und Installer-Dateien nicht erreichbar
- [ ] Überall TLS; Sicherheits-Header; die Content Security Policy der Administration nicht abgeschwächt
- [ ] Debug-Ausgabe im Produktivbetrieb aus
- [ ] Zwei-Faktor-Authentifizierung für Administratoren angeboten, wo verfügbar ([Konten und Anmeldung](../../../en/us/help/accounts-and-sign-in.md))
- [ ] Eigener Code auf SQL-Injection, XSS und CSRF geprüft ([Sicherheitshärtung](../../../en/us/help/security-hardening.md))
- [ ] Updates nach Zeitplan eingespielt ([Kunden-Websites aktuell halten](kunden-websites-aktuell-halten.md))
- [ ] Backups getestet

## Zugangsdaten

- Nie in Tickets, Chats, E-Mails oder Git-Repositorys.
- Übergeben Sie Zugangsdaten über einen sicheren Kanal an Kunden; lassen Sie Passwörter bei der ersten Anmeldung ändern.
- Entfernen Sie die Konten ausscheidender Mitarbeitender, auf Ihrer Seite und beim Kunden.

## Sicherheitslücken

Melden Sie vertraulich an security@se7enx.com; nie öffentlich ([Eskalationen und Sicherheitsprobleme](eskalationen-und-sicherheitsprobleme.md)).

## Vorfälle

Halten Sie für jeden Kunden einen kurzen Plan bereit:

1. Eindämmen: den betroffenen Teil vom Netz nehmen oder den Angriffsweg sperren.
2. Sichern: Logs und Kopien für die Analyse aufbewahren.
3. Informieren: sofort den Kunden; Behörden, wenn das Gesetz es verlangt.
4. Beheben: die Korrektur einspielen, Zugangsdaten wechseln, saubere Backups wiederherstellen.
5. Lernen: aufschreiben, was geschehen ist, und den Ablauf ändern.

## Verwandte Seiten

- [Eskalationen und Sicherheitsprobleme](eskalationen-und-sicherheitsprobleme.md)
- [Kundendaten schützen](kundendaten-schuetzen.md)
- [Sicherheitshärtung](../../../en/us/help/security-hardening.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
