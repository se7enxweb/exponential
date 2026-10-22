---
title: Sicherheitstechnik im Überblick
audience: software-engineer, developer, admin
category: software-engineering
order: 700
summary: Das Sicherheitsmodell von Exponential für Ingenieure - Zugriffskontrolle über Richtlinien, Maskierung von Ausgaben, sicheres SQL, CSRF-Tokens, die sicheren Standards von 2026, die HTTP-Sicherheitsschicht, Authentifizierung und Zwei-Faktor-Anmeldung, der Audit-Trail und die Klassifizierung von Anfragen, die dokumentierten Härtungsdurchgänge, die OWASP-Einstufung, die sich das Projekt selbst gibt, und wie Schwachstellen gemeldet und behoben werden.
keywords: Sicherheit, Sicherheitstechnik, OWASP, ASVS, Zugriffskontrolle, Maskierung, wash, SQL-Injection, CSRF, ezformtoken, sichere Standards, Härtung, SECURITY.md, Offenlegung von Schwachstellen, Audit, Softwareingenieur, security, security engineering, access control, escaping, secure defaults, hardening, vulnerability disclosure
type: help
---
# Sicherheitstechnik im Überblick

Diese Seite richtet sich an Ingenieure, die die Sicherheit eines Exponential-Systems bewerten, entwerfen oder prüfen. Sie
nennt die Mechanismen, wo sie liegen, was standardmäßig an ist und wo das Projekt selbst sagt, dass es zurückbleibt. Der
Stand der Standards ist [Sicherheitsstandards](../../standards/sicherheit.md); die an Angreifern orientierte Anleitung für
Betreiber ist [Sicherheitshärtung](../sicherheitshaertung.md).

## Inhalt

1. [Die Mechanismen](#die-mechanismen)
2. [Sichere Standards](#sichere-standards)
3. [Festgehaltene Härtungsdurchgänge](#festgehaltene-härtungsdurchgänge)
4. [Die eigene Einschätzung des Projekts](#die-eigene-einschätzung-des-projekts)
5. [Schwachstellen melden und beheben](#schwachstellen-melden-und-beheben)
6. [Wie es weitergeht](#wie-es-weitergeht)

## Die Mechanismen

| Bedrohung | Mechanismus | Wo |
|---|---|---|
| Fehlerhafte Zugriffskontrolle | Rollen, Richtlinien und Einschränkungen, für jede Modul-View geprüft; Lesen von Inhalten nach Berechtigung gefiltert | [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md) |
| Injektion (SQL) | `escapeString()` und Ganzzahl-Casts; Persistenzobjekte maskieren für Sie | [Portables SQL schreiben](portables-sql-schreiben.md) |
| Injektion (Shell) | Argumente über `escapeshellarg()` | [Sicherheitshärtung von 6.0.13](../../bc/6.0/haertung.md) |
| Cross-Site-Scripting | `\|wash` in Templates; Content Security Policy mit Nonces | [HTTP-Sicherheit](http-sicherheit-csp-und-proxys.md) |
| Cross-Site-Request-Forgery | Formular-Tokens in jedem Formular (`ezformtoken`) | [ezformtoken und CSRF](../erweiterungsentwicklung/teil-8-referenz/ezformtoken-und-csrf.md) |
| Schwache Authentifizierung | `password_hash()`, Mindestlänge 10, optionale Sperre, Zwei-Faktor-Anmeldung | [Authentifizierung, Sessions und Zwei-Faktor-Anmeldung](authentifizierung-sessions-und-2fa.md) |
| Gefälschte Proxy-Header | weitergeleitete Header nur von `TrustedProxies[]` vertraut | [Vertrauenswürdige Proxys](../../bc/6.0/vertrauenswuerdige-proxys.md) |
| Offene Weiterleitungen | erlaubte Weiterleitungs-Hosts | [Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md) |
| Bösartige Uploads | Upload-Prüfungen, Bereinigung von SVG | [SVG-Bilder](../../bc/6.0/svg-bilder.md) |
| Konfigurationsdrift in der Produktion | `ManagedByDeployment` | [Konfiguration, die die Bereitstellung verwaltet](../../funktionen/6.0/durch-deployment-verwaltet.md) |
| Unentdeckter Missbrauch | manipulationssicherer Audit-Trail, Anfrageklassifizierer und `security.log` | [Beobachtbarkeit](beobachtbarkeit-logs-debug-und-audit.md) |

## Sichere Standards

Im September und Oktober 2026 wurden Sicherheits-Header, Session-Cookies mit `HttpOnly` und `SameSite`, eine strengere
Upload-Prüfung und neue Zugriffsanforderungen für einige entfernte Dienste standardmäßig eingeschaltet.
Sicherheitskorrekturen werden wie Fehlerkorrekturen behandelt: standardmäßig an, dokumentiert mit dem Weg, sie bewusst
rückgängig zu machen ([Sicherheitsstandards](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md),
[Verhaltensänderungen vom 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)).

Mitgelieferte Werte, die Sie kennen sollten (`settings/site.ini`): Sicherheits-Header wie
`X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN` und `Referrer-Policy: strict-origin-when-cross-origin`;
`MinPasswordLength=10`; `HashType=php_default`; `MaxNumberOfFailedLogin=0` (die Sperre ist aus, bis Sie sie setzen); die
Content Security Policy in `Mode=auto` (nur berichtend in der Administration, aus auf Websites).

## Festgehaltene Härtungsdurchgänge

| Durchgang | Inhalt |
|---|---|
| 6.0.13 | vier SQL-Injektionen, zwei Betriebssystem-Befehlsinjektionen, ein reflektiertes Cross-Site-Scripting ([Sicherheitshärtung von 6.0.13](../../bc/6.0/haertung.md)) |
| August 2026 | [Sicherheitshärtung, August 2026](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md) |
| September 2026 | [Sicherheitsstandards](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md) |

Die Seite zu 6.0.13 listet auch die Muster, die Entwickler von Erweiterungen in ihrem eigenen Code prüfen müssen: Eine
Kernel-Aktualisierung behebt sie in Erweiterungen nicht.

## Die eigene Einschätzung des Projekts

Der Sicherheitsstandard stuft OWASP Top 10 (2021) als **teilweise erfüllt** ein, mit Gegenmaßnahmen für alle zehn
Kategorien und benannten Lücken (Kontosperre standardmäßig aus, kein Abhängigkeits-Scan in der CI, CSP auf öffentlichen
Websites aus), OWASP ASVS 4.0.3 als **nicht verifiziert** (keine Bewertung durchgeführt) und Content Security Policy
Level 3 als **teilweise erfüllt**. "Erfüllt" bräuchte eine externe Bewertung, die nicht durchgeführt wurde. Verwenden Sie
diese Liste als Ausgangspunkt Ihres eigenen Bedrohungsmodells.

## Schwachstellen melden und beheben

- Melden Sie vertraulich per E-Mail an die Adresse in `SECURITY.md`, nicht im öffentlichen Issue-Tracker oder in
  Diskussionen. Eine erste Antwort wird innerhalb eines Werktags versprochen.
- Geben Sie Version, PHP-Version, Engine und Datenbank an, was ein Angreifer tun kann, und die Schritte zum Reproduzieren;
  lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg.
- Korrekturen entstehen auf `main` und werden mit der nächsten Version 6.0.x veröffentlicht. Ein veröffentlichter Tag
  wird nie geändert: Eine Korrektur für ein Problem in einem älteren Tag erreicht Sie als nächstes Release. Nur die Linie
  6.0 wird unterstützt.

## Wie es weitergeht

- [HTTP-Sicherheit: Header, CSP, Anfrageregeln und Proxys](http-sicherheit-csp-und-proxys.md)
- [Authentifizierung, Sessions und Zwei-Faktor-Anmeldung](authentifizierung-sessions-und-2fa.md)
- [Checkliste für Sicherheitsprüfungen](checkliste-fuer-sicherheitspruefungen.md)
- [Kapitel 13 des Installationsbuchs: Sicherheitshärtung](../../installation/13-sicherheitshaertung.md)

## Verwandte Seiten

- [Sicherheit für Anwendungen](../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Sicherheit in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)
- [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
