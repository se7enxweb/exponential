---
title: Sicherheitsgrundlagen
audience: customer, businesses, admin
category: customer-success
order: 8
summary: Die Sicht des Betreibers auf die Sicherheit einer Exponential-Website - was die Software bereits standardmäßig tut, die wenigen Dinge, die nur Ihre Organisation entscheiden kann, wer was tun sollte und wo die ausführlichen Leitfäden stehen.
keywords: Sicherheit, Betreiber, Standardeinstellungen, Updates, Konten, zweistufige Anmeldung, Prüfprotokoll, Backups, Sicherheits-Header, Verantwortung, security, owner, default settings, updates, accounts, two-step sign-in, audit trail, backups, security headers, responsibility
type: help
---
# Sicherheitsgrundlagen

Diese Seite ist für die Person, die für die Sicherheit der Website einsteht, ohne ihre Technikerin zu sein. Sie legt
dar, was Exponential bereits tut, was Ihre Entscheidung bleibt und wer in Ihrem Team oder bei Ihrem Anbieter welchen
Teil erledigen sollte. Die technische Tiefe steht unter [Sicherheit und Prüfprotokoll](../../../../en/us/guides/security-and-audit.md),
[Sicherheitshärtung](../../../../en/us/help/security-hardening.md) und im [Kapitel 13](../../../../en/us/install/13-security-hardening.md) des
Installationshandbuchs.

## Was bereits eingeschaltet ist

„Seit September 2026 ist eine Standardinstallation ohne zusätzliche Arbeit sicher. Prüfen Sie das, statt darauf zu
vertrauen“ ([Sicherheit und Prüfprotokoll](../../../../en/us/guides/security-and-audit.md)). Zu den Standardeinstellungen gehören:

| Schutz | Was er für Sie tut |
|---|---|
| Sicherheits-Header auf jeder Seite | Helfen Browsern, Einbetten in Frames und das Erraten von Inhaltstypen abzulehnen |
| Sitzungs-Cookies mit `HttpOnly` und `SameSite` | Erschweren gestohlene Sitzungen |
| Eine echte Abmeldung | Die alte Sitzungs-ID meldet niemanden mehr an |
| Upload-Prüfungen nach Inhalt | Ein Bild wird anhand dessen angenommen, was es enthält, nicht anhand seines Namens |
| Ein zufälliges erstes Administratorpasswort | Wenn der Installer keines oder ein allgemein bekanntes erhalten hat |
| Das Prüfprotokoll, standardmäßig eingeschaltet | Ein manipulationssicherer Nachweis von Anmeldungen, Inhaltsänderungen, Einstellungen und Deployments ([Das Prüfprotokoll](../../../../en/us/features/6.0/audit-trail.md)) |

Bitten Sie Ihren Anbieter, die Prüfungen aus [Sicherheit und Prüfprotokoll](../../../../en/us/guides/security-and-audit.md)
einmal durchzuführen und Ihnen das Ergebnis zu schicken.

## Was nur Sie entscheiden können

| Entscheidung | Warum sie Ihre ist | Seite |
|---|---|---|
| Wer welchen Zugriff bekommt | Sie wissen, wer was braucht | [Administratorkonten und Zugriff](administratorkonten-und-zugriff.md) |
| Ob die zweistufige Anmeldung verpflichtend ist | Sie kostet Ihr Personal bei jeder Anmeldung ein paar Sekunden | [Zweistufige Anmeldung](../../../../en/us/guides/two-factor-login.md) |
| Wie schnell Sicherheits-Releases eingespielt werden | Es ist eine Vertragsbedingung mit Ihrem Anbieter | [Release-Linie und Support](release-linie-und-support.md) |
| Wie viel Datenverlust hinnehmbar ist | Es bestimmt den Backup-Plan | [Backups](backups.md) |
| Wer Sicherheitsalarme erhält | Jemand muss sie lesen | [Überwachung](ueberwachung.md) |
| Welche personenbezogenen Daten Sie erheben, und wie lange | Es ist eine rechtliche und geschäftliche Frage | [Personenbezogene Daten in Exponential](../../../../en/us/help/businesses/security/personal-data-in-exponential.md) |

## Die drei wichtigsten Gewohnheiten

1. **Aktuell bleiben.** Sicherheitskorrekturen erreichen Sie nur als nächstes Release (`SECURITY.md`). Eine veraltete
   Website ist der häufigste Weg, den Schutz zu verlieren, den das Projekt bereits gebaut hat
   ([Sicherheitshärtung](../../../../en/us/help/security-hardening.md)).
2. **Zugänge knapp halten.** Ein Konto pro Person, wenige Administratoren, zweistufige Anmeldung für sie, und Konten
   werden geschlossen, wenn Menschen gehen ([Das Administratorpasswort ändern, Kontenpflege](../../../../en/us/help/change-the-admin-password.md)).
3. **Wissen, wen Sie anrufen.** Vereinbaren Sie einen Kontakt für einen vermuteten Vorfall, und wissen Sie, dass ein
   Sicherheitsproblem in der Software vertraulich gemeldet wird, nie öffentlich ([Vertrauliche Sicherheitsmeldungen](vertrauliche-sicherheitsmeldungen.md)).

## Wer was tut

| Aufgabe | Üblicherweise |
|---|---|
| Server, PHP, TLS-Zertifikate, Firewall | Hoster |
| Updates von Exponential einspielen | Hoster oder Partner, wie vereinbart |
| Einstellungen der Website (Header, Sitzungen, Adresse der Administration) | Partner oder Ihr Entwickler |
| Rollen, Konten, zweistufige Anmeldung für das Personal | Der Administrator Ihrer Website |
| Alarme des Prüfprotokolls und Logs lesen | Hoster oder Betreiber, mit einer Zusammenfassung an Sie |
| Nach einem Vorfall entscheiden | Betreiber |

Schreiben Sie Ihre eigene Fassung dieser Tabelle in Ihre Vereinbarungen; siehe [Wer was macht](../partner-und-hoster/wer-was-macht.md).

## Wenn Sie einen Einbruch vermuten

Behandeln Sie es als dringendste Art von Vorfall: Der Support-Leitfaden für Hoster sagt: „Ein vermuteter
Sicherheitsvorfall hat immer den Schweregrad 1“ ([Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md)). Rufen Sie
sofort Ihren Hoster an, löschen Sie nichts, und lassen Sie ihn dem Runbook [Kompromittierte Website](../../../../en/us/help/hosting/runbooks/compromised-site.md)
folgen, das damit beginnt, den Schaden einzudämmen, ohne Beweise zu vernichten. Notieren Sie, was Sie wann gesehen
haben, in der [Vorlage für Vorfallnotizen](vorlage-vorfallnotizen.md).

> **Warnung**
> Schicken Sie nie Passwörter, Schlüssel oder personenbezogene Daten von Kunden in einem Ticket, Chat oder einer E-Mail,
> um ein Problem zu melden ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)).

## Verwandte Seiten

- [Sicherheits-Checkliste für Betreiber](sicherheits-checkliste-fuer-betreiber.md)
- [Sicherheitsgrundlagen für Unternehmen](../../../../en/us/help/businesses/security/security-basics-for-businesses.md)
- [DSGVO-Grundlagen](../../../../en/us/help/businesses/security/gdpr-basics.md) (keine Rechtsberatung)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
