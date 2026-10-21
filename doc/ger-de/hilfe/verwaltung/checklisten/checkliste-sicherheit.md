---
title: Checkliste Sicherheit
audience: government, admin, hosting, project-manager
category: public-sector
order: 40
summary: Eine Sicherheitscheckliste für eine öffentliche Stelle, die Exponential betreibt - Konten und Anmeldung, Härtung, Header und CSP, Protokollierung und Audit, Updates, der Server und Meldewege - aufgebaut auf der Seite zu den Sicherheitsstandards und den Anleitungen zur Härtung, mit den Voreinstellungen, die Sie ändern müssen.
keywords: Sicherheit, Checkliste, Härtung, zweistufige Anmeldung, Kontosperre, Passwortlänge, CSP, HSTS, Audit-Trail, request-shield, Updates, SECURITY.md, security.txt, öffentlicher Sektor, security, checklist, hardening, two-step sign-in, lockout, password length, audit trail, updates, public sector
type: help
---
# Checkliste Sicherheit

Diese Checkliste richtet sich an die Informationssicherheitsbeauftragten und die Website-Administration einer
öffentlichen Stelle. Sie folgt den [Sicherheitsstandards](../../../../eng-us/standards/security.md), der
[Sicherheitshärtung](../../sicherheitshaertung.md) und [13. Sicherheitshärtung für die Produktion](../../../installation/13-sicherheitshaertung.md).
Mit „Voreinstellung“ markierte Punkte nennen eine Einstellung, die ausgeschaltet oder offen ausgeliefert wird und über
die Sie entscheiden.

## Inhalt

1. [Konten und Anmeldung](#konten-und-anmeldung)
2. [Konfiguration](#konfiguration)
3. [Header und Content Security Policy](#header-und-content-security-policy)
4. [Protokollierung und Audit](#protokollierung-und-audit)
5. [Updates](#updates)
6. [Der Server und was davor steht](#der-server-und-was-davor-steht)
7. [Meldewege](#meldewege)

## Konten und Anmeldung

- [ ] Keine geteilten Konten; jeder Administrator ist eine namentlich bekannte Person.
- [ ] Zweistufige Anmeldung für Administratoren: optional über `sevenx_authentication_2fa` ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Voreinstellung: Die Kontosperre ist ausgeschaltet (`[UserSettings] MaxNumberOfFailedLogin=0`); entscheiden Sie, sie zu setzen, oder begrenzen Sie die Anfragerate vor der Website ([Sicherheitsstandards](../../../../eng-us/standards/security.md#known-gaps)).
- [ ] Voreinstellung: Die Mindestlänge für Passwörter ist 10; OWASP ASVS Level 1 verlangt 12 (`MinPasswordLength`) ([Sicherheitsstandards](../../../../eng-us/standards/security.md#status-1)).
- [ ] Das anfängliche Administratorpasswort ist geändert ([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)).
- [ ] Rollen folgen dem Prinzip der geringsten Rechte ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)); die [Vierteljährliche Zugriffsprüfung](vierteljaehrliche-zugriffspruefung.md) ist eingeplant.

## Konfiguration

- [ ] Debug-Ausgabe auf aufgeführte Adressen beschränkt ([13. Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md)).
- [ ] Einstellungen, Templates und Pakete in der Produktion mit `ManagedByDeployment` gesperrt, wo Ihr Ablauf es erlaubt ([Von der Bereitstellung verwaltete Konfiguration](../../../../eng-us/features/6.0/managed-by-deployment.md)).
- [ ] **Setup > Upgrade-Prüfung** zeigt keine unerwarteten geänderten Dateien ([Die Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).
- [ ] Erweiterungen von Drittanbietern nur aktiv, wenn sie gebraucht werden, und in der Datenschutzerklärung genannt ([Datenschutzstandards](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)).
- [ ] Erlaubte Hosts für Weiterleitungen gesetzt ([Sichere Weiterleitungen](../../../../eng-us/features/6.0/safe-redirects.md)).

## Header und Content Security Policy

- [ ] Sicherheits-Header eingeschaltet (standardmäßig eingeschaltet): `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` ([Sicherheitsstandards](../../../../eng-us/standards/security.md#owasp-cheat-sheets-reflected-in-the-code)).
- [ ] Voreinstellung: HSTS leer; setzen Sie es, wenn die ganze Website HTTPS nutzt.
- [ ] Voreinstellung: CSP in der Administration nur im Berichtsmodus und auf Websites ausgeschaltet; prüfen Sie `var/log/csp.log` und entscheiden Sie pro Siteaccess über das Erzwingen ([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).

## Protokollierung und Audit

- [ ] Audit-Trail eingeschaltet (standardmäßig eingeschaltet) und Ketten unversehrt: `./console exp:audit status` und `./console exp:audit verify` ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).
- [ ] Empfänger von Alarmen gesetzt: `./console exp:audit alerts recipients`.
- [ ] Die Signaturschlüssel des Audits in `settings/override/audit.ini.append.php` sind in den Datensicherungen enthalten ([Datensicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md#schlüssel-und-geheimnisse-die-sich-nicht-neu-erzeugen-lassen)).
- [ ] `var/log/security.log` geprüft ([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)).

## Updates

- [ ] Eine benannte Person beobachtet die Releases; Korrekturen kommen nur als nächstes Release (`SECURITY.md`; [Sicherheitsupdates und Patch-Rhythmus](../../../../eng-us/help/government/procurement/lifecycle/security-updates-and-patching-cadence.md)).
- [ ] Composer-Abhängigkeiten mit `composer audit` geprüft, das das Projekt nicht in der CI ausführt ([Sicherheitsstandards](../../../../eng-us/standards/security.md#known-gaps)).
- [ ] PHP und das Betriebssystem erhalten Sicherheitsupdates ([Unterstützung von PHP und Plattform](../../../../eng-us/help/government/procurement/lifecycle/php-and-platform-support.md)).

## Der Server und was davor steht

- [ ] HTTPS mit einem gültigen Zertifikat ([TLS und HTTPS](../../hosting/sicherheit/tls-und-https.md)).
- [ ] Die Administration, wo möglich, auf Netzebene beschränkt ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md)).
- [ ] Ein Anfragefilter wie request-shield davor ([Sicherheitshärtung](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).
- [ ] Dateirechte wie dokumentiert ([Beschreibbare Verzeichnisse und Rechte](../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).

## Meldewege

- [ ] Ihre Website sagt Besuchern, wo sie ein Sicherheitsproblem melden können; Exponential liefert keine `security.txt` mit ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)).
- [ ] Probleme der Software gehen vertraulich an das Projekt, wie `SECURITY.md` es beschreibt, ohne personenbezogene Daten.
- [ ] Verfahren für Vorfälle bereit ([Bereitschaft für Vorfälle](bereitschaft-fuer-vorfaelle.md)).

## Verwandte Seiten

- [Checklisten für Verwaltungen](README.md)
- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Sicherheitsprüfung (für Hoster)](../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
