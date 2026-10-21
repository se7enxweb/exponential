---
title: Übergabe von Schlüsseln und Geheimnissen beim Ausstieg
audience: government, project-manager, admin, hosting
category: public-sector
order: 120
summary: Von welchen Schlüsseln, Passwörtern und Geheimnissen eine Exponential-Website abhängt, welche davon sich nicht neu erzeugen lassen (Signaturschlüssel des Audits, das Geheimnis der E-Mail-Einstellungen, Zertifikatsschlüssel), wie Sie sie vertraulich übertragen und welche Sie nach einem Lieferantenwechsel erneuern.
keywords: Geheimnisse, Schlüssel, Zugangsdaten, Passwörter, Übergabe, Erneuerung, Rotation, Audit-Schlüssel, TokenSecret, API-Schlüssel, TLS-Zertifikate, Settings-Override, Ausstieg, Offboarding, secrets, keys, credentials, passwords, handover, rotation, audit keys, API keys, TLS certificates, settings override, exit, offboarding
type: help
---
# Übergabe von Schlüsseln und Geheimnissen beim Ausstieg

Diese Seite richtet sich an Projektleitungen, die sicherstellen müssen, dass nichts Geheimes verloren geht oder
zurückbleibt, wenn ein Lieferant geht, und an die Administratoren, die die Übergabe durchführen. Einige Geheimnisse
einer Exponential-Website erzeugt die Software selbst, und sie lassen sich nicht neu erzeugen; ihr Verlust beschädigt
Dinge, die keine Wiederherstellung von Inhalten reparieren kann. Andere müssen geändert werden, nachdem der bisherige
Lieferant gegangen ist. Die Fakten stammen aus [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)
und [Eine Website zwischen Hostern umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md).

## Inhalt
1. [Geheimnisse, die sich nicht neu erzeugen lassen](#geheimnisse-die-sich-nicht-neu-erzeugen-lassen)
2. [Geheimnisse, die nach dem Ausstieg zu erneuern sind](#geheimnisse-die-nach-dem-ausstieg-zu-erneuern-sind)
3. [Wie Sie Geheimnisse übertragen](#wie-sie-geheimnisse-übertragen)
4. [Zu entfernende Konten](#zu-entfernende-konten)
5. [Checkliste](#checkliste)

## Geheimnisse, die sich nicht neu erzeugen lassen

| Geheimnis | Wo | Bei Verlust |
|---|---|---|
| Signatur- und Pseudonymschlüssel des Audits | `settings/override/audit.ini.append.php` | bestehende Audit-Einträge lassen sich nicht mehr als unverändert verifizieren ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |
| Website-Geheimnis der E-Mail-Einstellungen | `settings/override/mailpreferences.ini.append.php`, `[SecretSettings] TokenSecret` | jeder Abmeldelink in jeder bereits versandten E-Mail funktioniert nicht mehr, und die Sperrliste passt nicht mehr ([E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)) |
| Private Zertifikatsschlüssel von Velocity | der Konfigurationsbaum von Velocity, `ssl/` (`/etc/vc` oder `var/vc/qbix/etc`) | HTTPS auf Velocity muss neu eingerichtet werden |

Übergeben Sie diese mit der Sicherung und behalten Sie sie: Erzeugen Sie sie auf dem neuen Host nicht neu.

## Geheimnisse, die nach dem Ausstieg zu erneuern sind

Diese lassen sich ändern und sollten geändert werden, weil der bisherige Lieferant sie kannte:

| Geheimnis | Wo | Wie |
|---|---|---|
| Datenbankpasswort | `settings/override/site.ini.append.php`, `[DatabaseSettings]` | auf dem Datenbankserver und in der Einstellung ändern; die Offboarding-Checkliste des Hostings fordert den Kunden auf, es beim neuen Host zu ändern ([Kunden-Offboarding](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)) |
| Zugangsdaten des Mailservers | `[MailSettings]` | beim Mail-Anbieter ändern |
| Passwörter der Administratorkonten | die Benutzerkonten | ändern oder die Konten entfernen ([Das Admin-Passwort ändern](../../../administratorpasswort-aendern.md)) |
| Persönliche API-Schlüssel und OAuth-Clients | **Setup > oAuth-Verwaltung** | Schlüssel der Beschäftigten des bisherigen Lieferanten widerrufen ([Persönliche API-Schlüssel](../../../../anleitungen/api-schluessel.md)) |
| Zweistufige Anmeldung der Beschäftigten des bisherigen Lieferanten | ihre Konten | mit den Konten entfernt ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)) |
| Schlüssel für Dienste Dritter (Karten, Captcha, KI-Anbieter, Analyse) | Einstellungen der Erweiterungen | bei jedem Anbieter neu erzeugen, die Einstellungen aktualisieren |
| Zugang zu Server, SFTP, Panel und Git | außerhalb von Exponential | entfernen oder ändern |

## Wie Sie Geheimnisse übertragen

- Übertragen Sie `settings/override/` und andere geheime Dateien über einen verschlüsselten Kanal, nie in einem Ticket,
  Chat, einer E-Mail oder einem öffentlichen Repository ([Eine Website zwischen Hostern umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md#einstellungen-die-die-alte-maschine-nennen)).
- Bewahren Sie sie verschlüsselt auf, lesbar nur für benannte Rollen.
- Schreiben Sie in die Dokumentation, wo ein Geheimnis aufbewahrt wird, nie das Geheimnis selbst ([Übergabe der Dokumentation](uebergabe-der-dokumentation.md)).
- Der Systembericht unter **Setup > Systeminformationen** lässt Passwörter und Schlüssel weg und kann daher geteilt
  werden; Einstellungsdateien nicht ([Systeminformationen](../../../../anleitungen/systeminformationen.md)).

## Zu entfernende Konten

- Administrationskonten der Beschäftigten des bisherigen Lieferanten: Entfernen Sie sie oder deaktivieren Sie sie, wenn
  Ihre Regeln zum Schriftgut verlangen, sie aufzubewahren. **Benutzerkonten prüfen** (`/user/hygiene`) hilft, Konten zu
  finden, die eine Zeit lang nicht benutzt wurden ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)).
- Ihre Rollenzuweisungen, API-Schlüssel und Sitzungen (**Setup > Sitzungen**, [Sitzungen](../../../../anleitungen/sitzungen.md)).
- Ihren Zugang zu Servern, Panels, Überwachung und Repositorys.

## Checkliste

- [ ] Audit-Schlüssel, Geheimnis der E-Mail-Einstellungen und Zertifikatsschlüssel erhalten, verschlüsselt aufbewahrt,
  nicht neu erzeugt.
- [ ] Zugangsdaten für Datenbank, Mail und Dienste nach dem Umzug erneuert.
- [ ] Konten, API-Schlüssel und Sitzungen des bisherigen Lieferanten in der Administration entfernt.
- [ ] Zugang des bisherigen Lieferanten zu Servern, Panels und Repositorys entfernt.
- [ ] `./console exp:audit verify` meldet mit den übertragenen Schlüsseln eine intakte Kette.
- [ ] Erneuerung mit Datum im Ausstiegsplan festgehalten.

## Verwandte Seiten
- [Ausstiegspläne: Übersicht](README.md)
- [Alles sichern](alles-sichern.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
