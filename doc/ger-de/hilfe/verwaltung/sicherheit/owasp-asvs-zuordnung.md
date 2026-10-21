---
title: OWASP ASVS auf Exponential abgebildet
audience: government, developer, admin
category: public-sector
order: 40
summary: Für öffentliche Stellen, deren Sicherheitsanforderungen auf den OWASP Application Security Verification Standard verweisen - die Kapitel von ASVS 4.0.3, denen das Projekt Maßnahmen zuordnet, der Status Nicht geprüft, die eine bekannte Lücke auf Stufe 1 (Passwortlänge) und wie Sie Ihre eigene Prüfung planen.
keywords: OWASP ASVS, Prüfstandard für Anwendungssicherheit, Stufe 1, Stufe 2, Authentifizierung, Sitzungsverwaltung, Zugangssteuerung, Validierung, Protokollierung, Dateien, Konfiguration, Prüfung, Sicherheitsanforderungen, application security verification standard, 4.0.3, level 1, level 2, V2 authentication, V3 session management, V4 access control, V5 validation, V7 logging, V12 files, V13 API, V14 configuration, verification, security requirements
type: reference
---
# OWASP ASVS auf Exponential abgebildet

Diese Seite richtet sich an Sicherheitsarchitektinnen und -architekten sowie Entwicklerinnen und Entwickler
öffentlicher Stellen und an Lieferanten, die Anforderungen auf Basis des ASVS beantworten. Der OWASP Application Security
Verification Standard (<https://owasp.org/www-project-application-security-verification-standard/>) ist ein Katalog
prüfbarer Anforderungen in drei Stufen. Das Projekt hat **keine** Bewertung Anforderung für Anforderung vorgenommen: Sein
Status ist **Nicht geprüft** ([Sicherheitsstandards: OWASP ASVS 4.0.3](../../../../eng-us/standards/security.md#owasp-asvs-403)).
Das Projekt verwendet die Ausgabe 4.0.3, wie die meisten aktuellen Fragebögen; OWASP hat inzwischen 5.0 veröffentlicht.

## Inhalt

1. [Vorhandene Maßnahmen nach Kapitel](#vorhandene-maßnahmen-nach-kapitel)
2. [Die Lücke auf Stufe 1, die Sie schließen können](#die-lücke-auf-stufe-1-die-sie-schließen-können)
3. [Eine Prüfung planen](#eine-prüfung-planen)
4. [Verwandte Seiten](#verwandte-seiten)

## Vorhandene Maßnahmen nach Kapitel

| ASVS-Kapitel | In Exponential vorhanden | Hinweis für den Betreiber |
|---|---|---|
| V2 Authentifizierung | Speicherung mit `password_hash()` (V2.4); Mindestlänge 10 (V2.1.1 verlangt 12); Prüfung auf schwache Passwörter; optionale zweistufige Anmeldung mit TOTP (V2.8) | `MinPasswordLength` anheben; zweistufige Anmeldung verlangen |
| V3 Sitzungsverwaltung | `HttpOnly`, `SameSite=Lax`, `Secure` über HTTPS (V3.4); neue Sitzungs-ID bei der Anmeldung (V3.2.1); Sitzungen bei Passwortänderung beendet (V3.3.3) | `CookieSecure=true`; kürzeres `SessionTimeout`, wenn Ihre Richtlinie es verlangt |
| V4 Zugangssteuerung | standardmäßig verweigert durch Richtlinien; Modulansichten brauchen eine Richtlinie, sofern sie nicht in `[RoleSettings] PolicyOmitList[]` stehen | Ergänzungen von `PolicyOmitList[]` durch Erweiterungen prüfen |
| V5 Validierung, Bereinigung und Kodierung | Operator `wash`; Bild-Uploads anhand des Inhalts gegen `AllowedMIMETypes[]` geprüft; SVG-Bereiniger | Templates Ihres Designs auf `|wash` prüfen |
| V7 Fehlerbehandlung und Protokollierung | Audit ohne Passwörter oder Tokens; Protokollansicht maskiert E-Mail- und IP-Adressen und Geheimnisse | `DebugOutput=disabled` beibehalten |
| V12 Dateien und Ressourcen | keine PHP-Ausführung unterhalb des Wurzelverzeichnisses durch die mitgelieferten Umschreiberegeln; Downloads über `content/download` | Umschreiberegeln nach Upgrades aktuell halten |
| V13 API | REST-Tokens aus `random_bytes()` | OAuth-Clients und API-Schlüssel begrenzen |
| V14 Konfiguration | Sicherheits-Header, CSP, eingeschränktes Debug, Dateimanifest | CSP in der Administration durchsetzen |

## Die Lücke auf Stufe 1, die Sie schließen können

ASVS 4.0.3 V2.1.1 verlangt Passwörter mit mindestens 12 Zeichen; Exponential liefert `MinPasswordLength=10` aus.
Setzen Sie den Wert in `settings/override/site.ini.append.php`:

```ini
[UserSettings]
MinPasswordLength=12
```

Das Anheben sperrt niemanden aus: Vorhandene Passwörter funktionieren weiter, und die Regel gilt, wenn ein Passwort das
nächste Mal gesetzt wird ([13.6 Passwörter und Anmeldung](../../../installation/13-sicherheitshaertung.md#136-passwörter-und-anmeldung)).
Beachten Sie, dass die Einstellung Bytes zählt. Viele Organisationen wählen 15, nach NIST SP 800-63B-4, wie die
Installationsanleitung erklärt.

Damit ist eine bekannte Lücke geschlossen; die Installation erfüllt dadurch nicht ASVS Stufe 1, weil niemand die
übrigen Anforderungen geprüft hat.

## Eine Prüfung planen

1. Wählen Sie die Stufe: Stufe 1 für alle öffentlichen Websites, Stufe 2 für Websites mit persönlichen Konten oder
   sensiblen Daten, wie es Ihre Sicherheitsrichtlinie vorsieht.
2. Legen Sie den Umfang fest: Ihre Version von Exponential, aktivierte Erweiterungen, Ihre eigenen Erweiterungen und
   Templates, den Hosting-Stack.
3. Verwenden Sie die Nachweise des Projekts (Einstellungen, Tests in `tests/tests/kernel/classes/security/`, die
   Härtungshinweise) als Eingabe und testen Sie, was Ihnen gehört.
4. Beauftragen Sie für das, was eine Prüferin oder einen Prüfer braucht, eine solche Person (siehe
   „Penetrationstests“ in diesem Bereich).
5. Halten Sie das Ergebnis je Anforderung fest; bewahren Sie den Bericht bei Ihrer Sicherheitsdokumentation auf.

## Verwandte Seiten

- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Sicherheitshärtung von 6.0.13](../../../bc/6.0/haertung.md)
- [Sicherheitsvoreinstellungen vom September 2026](../../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
