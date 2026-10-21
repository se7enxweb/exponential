---
title: Anmeldung und Sitzungen
audience: government, admin, website-creator
category: public-sector
order: 30
summary: Wie Anmeldung und Sitzungen für öffentliche Kontoinhaber und Mitarbeiter auf einer Exponential-Site funktionieren - die Anmeldeseiten, Lebensdauer von Sitzungen und Cookies, das Abmelden anderer Sitzungen nach einer Passwortänderung, die Seite Setup > Sitzungen, fehlgeschlagene Anmeldungen und Sperre.
keywords: Anmeldung, Abmeldung, Sitzungen, Sitzungs-Handler, Sperre, Brute Force, sign-in, login, logout, sessions, SessionTimeout, ActivityTimeout, CookieSecure, session handler, ezpSessionHandlerDB, EndOtherSessions, lockout, MaxNumberOfFailedLogin, brute force
type: help
---
# Anmeldung und Sitzungen

Diese Seite richtet sich an Administratoren und Site-Ersteller, die Konten auf einer Site für den öffentlichen Sektor
betreiben. Sie erklärt, wie aus einer Anmeldung eine Sitzung wird, wie lange sie dauert, wie Sitzungen beendet werden
und wie fehlgeschlagene Anmeldungen behandelt werden. Quellen: [Sitzungen](../../../../anleitungen/sitzungen.md),
[Sicherheitshärtung](../../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung) und
[Standards zur Sicherheit](../../../../../eng-us/standards/security.md).

## Inhalt

1. [Die Anmeldung](#die-anmeldung)
2. [Sitzungen und ihre Lebensdauer](#sitzungen-und-ihre-lebensdauer)
3. [Sitzungen beenden](#sitzungen-beenden)
4. [Fehlgeschlagene Anmeldungen und Sperre](#fehlgeschlagene-anmeldungen-und-sperre)
5. [Anmeldung von Öffentlichkeit und Mitarbeitern getrennt](#anmeldung-von-öffentlichkeit-und-mitarbeitern-getrennt)

## Die Anmeldung

| Eigenschaft | Exponential |
|---|---|
| Speicherung der Passwörter | bcrypt über `password_hash()` von PHP (`HashType=php_default`), bei der Anmeldung von älteren Hashes aktualisiert |
| Unbekannter Benutzername oder falsches Passwort | derselbe Aufwand und dieselbe Antwort, sodass Konten nicht ermittelt werden können |
| Sitzungs-ID | bei der Anmeldung erneuert |
| Cookie | `Secure` über HTTPS (`[Session] CookieSecure=auto`) |
| Zweiter Schritt | freiwillige oder verpflichtende zweistufige Anmeldung ([Zweistufige Anmeldung](zweistufige-anmeldung.md)) |
| Nachweis | Anmeldungen und Fehlschläge im Audit-Protokoll ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |

## Sitzungen und ihre Lebensdauer

| Einstellung (`site.ini [Session]`) | Voreinstellung | Bedeutung |
|---|---|---|
| `SessionTimeout` | 259200 (drei Tage) | eine Sitzung endet so lange nach ihrer letzten Nutzung |
| `ActivityTimeout` | 3600 (eine Stunde) | verwendet für „letzter Besuch“ und die Zahlen der Seite **Sitzungen** |
| `Handler` | leer (der eigene Speicher von PHP) | `ezpSessionHandlerDB` hält Sitzungen in einer Tabelle, sodass die Seite **Sitzungen** sie auflisten und entfernen kann |

Für öffentliche Konten auf gemeinsam genutzten oder öffentlichen Computern (Bibliotheken, Ämter) ist ein kürzerer
`SessionTimeout` freundlicher für Benutzer, die das Abmelden vergessen. Wägen Sie das gegen häufigeres Anmelden ab.

## Sitzungen beenden

| Ereignis | Wirkung |
|---|---|
| Der Benutzer meldet sich ab | die Sitzung endet |
| Das Passwort ändert sich (durch den Benutzer, einen Administrator, **Passwort vergessen?**, die Konsole) | die anderen Sitzungen des Benutzers werden bei ihrer nächsten Anfrage abgemeldet (`[PasswordSettings] EndOtherSessions=enabled`) ([Das eigene Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md)) |
| Ein Administrator entfernt Sitzungen | **Setup > Sitzungen** mit einer Sitzungstabelle: markieren und entfernen, mit einer Bestätigung; die eigene Sitzung des Administrators wird nie entfernt |
| Ein Konto wird deaktiviert | es kann sich nicht mehr anmelden ([Kontenhygiene](kontenhygiene.md)) |

## Fehlgeschlagene Anmeldungen und Sperre

| Maßnahme | Voreinstellung | Lesen |
|---|---|---|
| Audit-Alarmregeln `brute_force` (20 Fehlschläge aus einem Netz in 5 Minuten) und `brute_force_user` (10 für ein Konto in 15 Minuten) | ein, mit dem Audit-Protokoll | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md) |
| Sperre nach N Fehlschlägen, `[UserSettings] MaxNumberOfFailedLogin` | aus (0) | [Sicherheitshärtung](../../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung) |
| Vertrauenswürdige Netze von der Sperre ausgenommen, `TrustedIPList[]` | leer | dasselbe |
| Entsperren | **Benutzer > Benutzerkonten prüfen**, Filter **Gesperrt**, **Entsperren** | [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md) |

Eine Sperre erlaubt es auch jedem, ein bekanntes Konto durch falsche Passwörter zu sperren. Für öffentliche Konten
sind die zweistufige Anmeldung und die Alarmregeln oft besser als eine niedrige Sperrschwelle.

## Anmeldung von Öffentlichkeit und Mitarbeitern getrennt

| Praxis | Warum |
|---|---|
| Mitarbeiter melden sich auf dem Siteaccess der Administration an, die Öffentlichkeit auf der Site | unterschiedliche Rollen, unterschiedliche Risiken |
| Die Adresse der Administration auf Ihr Netz beschränkt oder hinter request-shield | weniger Angriffe auf Mitarbeiterkonten ([Sicherheitshärtung](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)) |
| Öffentliche Konten in einer eigenen Benutzergruppe mit einer minimalen Rolle | ein kompromittiertes öffentliches Konto kann wenig tun |
| Die Richtlinie `user/login` nach Siteaccess eingeschränkt | das Standardpaket lässt Anonymous sich auf jedem ausgelieferten Siteaccess außer denen der Administration anmelden ([Rollen und Richtlinien von sevenx_multisite](../../../seitenpakete/sevenx-multisite/rollen-und-richtlinien-von-sevenx-multisite.md#anonymous)) |

## Verwandte Seiten

- [Bürgerkonten](README.md)
- [Zweistufige Anmeldung](zweistufige-anmeldung.md)
- [Sitzungen](../../../../anleitungen/sitzungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
