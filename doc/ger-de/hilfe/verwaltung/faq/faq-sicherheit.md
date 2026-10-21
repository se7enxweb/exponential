---
title: "FAQ: Sicherheit"
audience: government, project-manager, admin, hosting
category: public-sector
order: 30
summary: Antworten auf die Sicherheitsfragen öffentlicher Stellen zu Exponential - OWASP Top 10, ASVS, Penetrationstests, zweistufige Anmeldung, Kontosperre, Audit-Trail, Content Security Policy, Meldung und Behebung von Schwachstellen -, belegt durch die Seite Sicherheitsstandards.
keywords: FAQ Sicherheit, OWASP Top 10, ASVS, Penetrationstest, zweistufige Anmeldung, 2FA, Kontosperre, Audit-Trail, CSP, Schwachstelle, SECURITY.md, Patches, Härtung, BSI, öffentlicher Sektor, security FAQ, penetration test, two-factor, lockout, audit trail, vulnerability, hardening, public sector
type: help
---
# FAQ: Sicherheit

Diese Seite beantwortet die Sicherheitsfragen, die Informationssicherheitsbeauftragte und Bewertungsgremien
öffentlicher Stellen zu Exponential stellen. Die Antworten stammen aus den
[Sicherheitsstandards](../../../../eng-us/standards/security.md), `SECURITY.md` und den Anleitungen zur Härtung.

## Inhalt

1. [Wie behandelt Exponential die OWASP Top 10?](#wie-behandelt-exponential-die-owasp-top-10)
2. [Wurde es gegen OWASP ASVS bewertet oder einem Penetrationstest unterzogen?](#wurde-es-gegen-owasp-asvs-bewertet-oder-einem-penetrationstest-unterzogen)
3. [Unterstützt es die zweistufige Anmeldung?](#unterstützt-es-die-zweistufige-anmeldung)
4. [Werden Konten nach fehlgeschlagenen Anmeldungen gesperrt?](#werden-konten-nach-fehlgeschlagenen-anmeldungen-gesperrt)
5. [Können wir sehen, wer was getan hat?](#können-wir-sehen-wer-was-getan-hat)
6. [Sendet es eine Content Security Policy?](#sendet-es-eine-content-security-policy)
7. [Wie werden Schwachstellen gemeldet und behoben?](#wie-werden-schwachstellen-gemeldet-und-behoben)
8. [Was müssen wir selbst härten?](#was-müssen-wir-selbst-härten)

## Wie behandelt Exponential die OWASP Top 10?

Jede Kategorie der OWASP Top 10 (2021) hat eingebaute Gegenmaßnahmen, einzeln zugeordnet in den
[Sicherheitsstandards](../../../../eng-us/standards/security.md#owasp-top-10-2021): rollenbasierte Zugriffsprüfungen,
Passwort-Hashing mit `password_hash()`, Maskierung der Ausgabe, CSRF-Token in Formularen, Sicherheits-Header, ein
manipulationssicherer Audit-Trail, SSRF-Prüfungen und mehr. Der Status ist **Partly complies** (teilweise erfüllt):
„Complies“ (erfüllt) würde eine externe Bewertung erfordern, die nicht durchgeführt wurde.

## Wurde es gegen OWASP ASVS bewertet oder einem Penetrationstest unterzogen?

Es wurde keine Bewertung nach ASVS durchgeführt; Status **Unverified** (nicht überprüft). Die voreingestellte
Mindestlänge für Passwörter (10) liegt unter den 12 von ASVS Level 1, sodass Level 1 eine Änderung der Einstellungen
erfordert ([Sicherheitsstandards](../../../../eng-us/standards/security.md#owasp-asvs-403)). Im Repository ist kein Bericht eines
Penetrationstests veröffentlicht. Der Kernel hat drei dokumentierte Härtungsdurchgänge hinter sich. Wenn Ihre Regeln
einen Test verlangen, geben Sie einen für Ihre Website in Auftrag.

## Unterstützt es die zweistufige Anmeldung?

Ja, wahlweise: Die Erweiterung `sevenx_authentication_2fa` ergänzt nach dem Passwort einen Code aus einer
Authenticator-App oder per E-Mail ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).

## Werden Konten nach fehlgeschlagenen Anmeldungen gesperrt?

Nicht standardmäßig: `MaxNumberOfFailedLogin=0`. Sie können eine Grenze setzen, mit einer Liste vertrauenswürdiger
Adressen. Brute-Force-Versuche werden von den Alarmregeln des Audits erkannt, und die Ratenbegrenzung bleibt dem
Webserver oder einem Anfragefilter wie request-shield überlassen
([Sicherheitsstandards](../../../../eng-us/standards/security.md#known-gaps),
[Sicherheitshärtung](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Können wir sehen, wer was getan hat?

Ja. Der Audit-Trail ist standardmäßig eingeschaltet und mit einer Hash-Kette gesichert, zeichnet Anmeldungen,
Inhaltsänderungen, das Schreiben von Einstellungen und mehr auf und hat Alarmregeln
([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)). Er lässt sich mit `./console exp:audit verify` prüfen
([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).

## Sendet es eine Content Security Policy?

Ja, aufgebaut aus `site.ini [ContentSecurityPolicy]` mit Nonces pro Anfrage und einem Sammler für Berichte. In der
Auslieferung ist sie **in der Administration nur im Berichtsmodus und auf öffentlichen Websites ausgeschaltet**; das
Erzwingen entscheidet der Betreiber pro Siteaccess
([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).

## Wie werden Schwachstellen gemeldet und behoben?

Vertraulich per E-Mail an die Adresse in `SECURITY.md`, mit „a first answer within one business day“ (einer ersten
Antwort innerhalb eines Werktags). Angenommene Probleme werden auf `main` behoben und mit dem nächsten Release 6.0.x
veröffentlicht; veröffentlichte Versionen werden nie geändert. Eine Frist für die Behebung wird nicht zugesagt. Eine
`security.txt` wird nicht mitgeliefert ([Sicherheitsstandards](../../../../eng-us/standards/security.md#vulnerability-disclosure)).
Wie Sie das einplanen:
[Sicherheitsupdates und Patch-Rhythmus](../../../../eng-us/help/government/procurement/lifecycle/security-updates-and-patching-cadence.md).

## Was müssen wir selbst härten?

HTTPS und HSTS, die Entscheidung zur Kontosperre, die Passwortlänge, die zweistufige Anmeldung, das Erzwingen der CSP,
die Einschränkung der Administration, einen Anfragefilter, Prüfungen der Abhängigkeiten mit `composer audit` (in der
CI des Projekts nicht ausgeführt) und die Härtung des Servers. Die Anleitungen:
[Sicherheitshärtung](../../sicherheitshaertung.md),
[13. Sicherheitshärtung für den Produktivbetrieb](../../../installation/13-sicherheitshaertung.md),
[Checkliste Sicherheit](../checklisten/checkliste-sicherheit.md).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Checkliste Sicherheit](../checklisten/checkliste-sicherheit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
