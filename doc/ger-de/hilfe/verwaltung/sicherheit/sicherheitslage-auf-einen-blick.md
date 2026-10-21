---
title: Die Sicherheitslage von Exponential auf einen Blick
audience: government, project-manager, admin
category: public-sector
order: 20
summary: Für öffentliche Stellen, die Exponential bewerten - der eigene Status des Projekts gegenüber OWASP Top 10, OWASP ASVS, Content Security Policy, den OWASP Cheat Sheets und der koordinierten Offenlegung, was standardmäßig an ist, was standardmäßig aus ist und was der Betreiber entscheiden muss.
keywords: Sicherheitslage, sicher ab Werk, Standardwerte, Audit-Protokoll, zweistufige Anmeldung, Kontosperre, Offenlegung, Sicherheitsbewertung, öffentlicher Sektor, Verwaltung, security posture, OWASP Top 10, ASVS, CSP, secure by default, defaults, audit trail, two-step sign-in, lockout, HSTS, disclosure, security assessment, public sector, government
type: reference
---
# Die Sicherheitslage von Exponential auf einen Blick

Diese Seite richtet sich an Projektverantwortliche, Informationssicherheitsbeauftragte und Administratorinnen und
Administratoren öffentlicher Stellen, die einen Überblick über die Sicherheit von Exponential auf einer Seite brauchen,
bevor sie die Einzelheiten lesen. Jeder Status stammt aus der eigenen Erklärung des Projekts,
[Sicherheitsstandards](../../../standards/sicherheit.md); die Bezeichnungen „Teilweise erfüllt“, „Nicht geprüft“ und
„Erfüllt“ werden so übernommen, wie sie dort stehen. Das Projekt beansprucht keine externe Bewertung.

## Inhalt

1. [Status je Standard](#status-je-standard)
2. [Standardmäßig an](#standardmäßig-an)
3. [Standardmäßig aus: Ihre Entscheidungen](#standardmäßig-aus-ihre-entscheidungen)
4. [Bekannte Lücken](#bekannte-lücken)
5. [Verwandte Seiten](#verwandte-seiten)

## Status je Standard

| Standard | Status | In einer Zeile |
|---|---|---|
| OWASP Top 10 (2021) | **Teilweise erfüllt** | Gegenmaßnahmen in allen zehn Kategorien; A09 Protokollierung und Überwachung ist mit **Erfüllt** bewertet |
| OWASP ASVS 4.0.3 | **Nicht geprüft** | keine Bewertung; Stufe 1 erfordert, `MinPasswordLength` auf 12 anzuheben |
| Content Security Policy Level 3 | **Teilweise erfüllt** | standardmäßig Report-only in der Administration, aus auf öffentlichen Websites |
| OWASP Cheat Sheet Series | **Teilweise erfüllt** | CSRF-Tokens, Passwortspeicherung, Sitzungs-Cookies, Header, Maskierung, Uploads |
| Koordinierte Offenlegung von Schwachstellen | **Teilweise erfüllt** | `SECURITY.md` mit einem vertraulichen Kanal; keine mitgelieferte `security.txt` |

„Erfüllt“ würde eine externe Bewertung erfordern, die nicht vorgenommen wurde
([Sicherheitsstandards: Status](../../../standards/sicherheit.md#status)).

## Standardmäßig an

| Schutz | Quelle |
|---|---|
| Sicherheits-Header auf jeder Seite (`nosniff`, `SAMEORIGIN`, `frame-ancestors 'self'`, `Referrer-Policy`, `Permissions-Policy`) | `site.ini [HTTPHeaderSettings] SecurityHeaders[]` |
| Sitzungs-Cookie `HttpOnly`, `SameSite=Lax`, `Secure` über HTTPS | `site.ini [Session]` |
| Passwörter mit `password_hash()` (bcrypt) gehasht, alte Hashes bei der Anmeldung aktualisiert | `[UserSettings] HashType=php_default`, `UpdateHash=true` |
| Bekannte und häufige Passwörter abgewiesen | `WeakPasswordCheck=enabled` |
| Andere Sitzungen nach einer Passwortänderung abgemeldet | `[PasswordSettings] EndOtherSessions=enabled` |
| CSRF-Formular-Tokens auf jedem Formular | Erweiterung `ezformtoken`, vom Einrichtungsassistenten aktiviert |
| Manipulationssicheres Audit-Protokoll mit Alarmregeln | `audit.ini [AuditSettings] Audit=enabled` |
| Anfrageklassifizierung, die Sondierungen und Angriffe in `security.log` schreibt | `requestclassifier.ini` |
| Einrichtungsassistent auf einer installierten Website geschlossen | `expSetupWizardGuard` |
| Unterhalb des Document-Root wird kein PHP ausgeführt; Punktdateien abgewiesen | `.htaccess_root` |
| Debug-Ausgabe aus | `[DebugSettings] DebugOutput=disabled` |

## Standardmäßig aus: Ihre Entscheidungen

| Einstellung | Ab Werk | Warum sie aus ist | Seite |
|---|---|---|---|
| Kontosperre | `MaxNumberOfFailedLogin=0` | eine Sperre erlaubt auch jedem, ein bekanntes Konto zu sperren | Kontosperre |
| Zweistufige Anmeldung verpflichtend | `Enforce2FA=disabled` | erfordert die Registrierung jedes Benutzers | Zweistufige Anmeldung |
| CSP durchgesetzt | `Mode=auto` (Report-only in der Administration) | Erweiterungen und Designs müssen zuerst geprüft werden | Content Security Policy |
| HSTS | leer | darf erst gesetzt werden, wenn jeder Name ausschließlich über HTTPS erreichbar ist | HTTPS und HSTS |
| Mindestlänge des Passworts 12 oder mehr | 10 | eine Richtlinienentscheidung | Passwortrichtlinie |
| Konfiguration gesperrt | `ManagedByDeployment=disabled` | nur wo Einstellungen aus der Versionsverwaltung kommen | Von der Bereitstellung verwaltete Konfiguration |
| Deaktivieren und verschieben für ausscheidende Beschäftigte | `DeactivateAndMove=disabled` | Funktion auf Wunsch | Rollen und minimale Rechte |

## Bekannte Lücken

Aus [Sicherheitsstandards: bekannte Lücken](../../../standards/sicherheit.md#bekannte-lücken):

- Kontosperre standardmäßig aus; Brute-Force wird von Alarmregeln erkannt, nicht vom Kernel gebremst;
- die zweistufige Anmeldung muss eigens eingeschaltet werden;
- keine Prepared Statements in der Datenbankschicht (Werte werden maskiert);
- die Maskierung der Ausgabe in Templates geschieht nicht automatisch (`|wash` muss angewendet werden);
- ältere Passwort-Hash-Typen bleiben für migrierte Konten lesbar, bis sie sich anmelden;
- kein Schwachstellenscan der Abhängigkeiten in der CI;
- HSTS standardmäßig leer.

## Verwandte Seiten

- [Sicherheitsstandards](../../../standards/sicherheit.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Kapitel 13: Sicherheitshärtung für den Produktivbetrieb](../../../installation/13-sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
