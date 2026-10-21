---
title: Content Security Policy für öffentliche Websites
audience: government, admin, developer
category: public-sector
order: 120
summary: Für öffentliche Stellen - was die Content Security Policy von Exponential leistet, wie sie ausgeliefert wird (Report-only in der Administration, aus auf öffentlichen Websites), wohin Verstoßberichte gehen und warum sich das Durchsetzen für eine öffentliche Website lohnt.
keywords: Content Security Policy, CSP, Cross-Site-Scripting, XSS, Report-only, durchsetzen, Nonce, Verstoßberichte, öffentlicher Sektor, cross-site scripting, report-only, enforce, nonce, csp_nonce, csp/report, csp.log, ReportEndpoint, Directives, AdditionalSources, ReportOnlyDesigns, CSP Level 3, public sector
type: help
---
# Content Security Policy für öffentliche Websites

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Entwicklerinnen und Entwickler öffentlicher
Websites. Eine Content Security Policy teilt dem Browser mit, welche Skripte, Stile, Bilder und Verbindungen eine
Seite verwenden darf; mit einer strengen Richtlinie läuft ein Skript nicht, das über einen gespeicherten oder
reflektierten Wert eingeschleust wurde, selbst wenn ein Template vergisst, ihn zu maskieren. Öffentliche Websites sind
attraktive Ziele für Defacement und für das Einschleusen von Skripten in vertrauenswürdige Seiten, daher ist die
Richtlinie eine starke zweite Verteidigungslinie
([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)).

## Inhalt

1. [Wie sie ausgeliefert wird](#wie-sie-ausgeliefert-wird)
2. [Die Einstellungen](#die-einstellungen)
3. [Berichte](#berichte)
4. [Was unter einer durchgesetzten Richtlinie nicht mehr funktioniert](#was-unter-einer-durchgesetzten-richtlinie-nicht-mehr-funktioniert)
5. [Verwandte Seiten](#verwandte-seiten)

## Wie sie ausgeliefert wird

| Siteaccess | `Mode=auto` ergibt |
|---|---|
| Administration (Design beginnt mit `admin`, oder ein Editor-Siteaccess auf admin4l) | **Report-only**: Der Browser meldet, was er blockieren würde, und blockiert nichts |
| öffentliche Websites | **keine Richtlinie** |

Das Projekt bewertet seine CSP-Unterstützung mit **Partly complies** gegenüber CSP Level 3
(<https://www.w3.org/TR/CSP3/>); das Durchsetzen ist eine Entscheidung des Betreibers je Siteaccess
([Sicherheitsstandards: Content Security Policy Level 3](../../../../eng-us/standards/security.md#content-security-policy-level-3)).
Unabhängig davon begrenzt `SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self'` das Einbetten in Frames auf
jeder Seite.

## Die Einstellungen

`settings/site.ini [ContentSecurityPolicy]`:

| Einstellung | Ab Werk | Bedeutung |
|---|---|---|
| `Mode` | `auto` | `disabled`, `report-only`, `enforce` oder `auto` |
| `Directives[]` | `default-src 'self'`, `script-src 'self'`, `style-src 'self'`, `object-src 'none'`, `base-uri 'self'`, `form-action 'self'`, `frame-ancestors 'self'` und weitere | die Richtlinie |
| `AdditionalSources[]` | leer | Hosts zu einer Direktive hinzufügen, ohne sie neu anzugeben |
| `Nonce`, `NonceDirectives[]` | `enabled`, `script-src` | eine Nonce je Anfrage für `<script nonce="{csp_nonce()}">` |
| `ReportOnlyDesigns[]` | `admin`, `admin2`, `admin_classic` | klassische Designs bleiben Report-only, auch wenn durchgesetzt wird |
| `ReportEndpoint` | `disabled` | `enabled` schickt Berichte an die eigene Ansicht `csp/report` der Website |
| `ReportOnlyDirectives[]` | leer | bei enforce: eine strengere begleitende Report-only-Richtlinie |

## Berichte

Mit `ReportEndpoint=enabled` gehen die Berichte des Browsers an `csp/report`, das je Verstoß eine Zeile in
`var/log/csp.log` schreibt, angezeigt unter **Setup > Logdateien** als „Berichte der Content Security Policy“. Die
Ansicht ist für jeden Besucher offen (Browser senden Berichte ohne Anmeldung), je Client und insgesamt in der Rate
begrenzt, antwortet ohne Inhalt und schreibt eine Flut als eine Zeile `abuse/csp-report-flood` pro Minute in
`security.log`. Mit `ReportURI` lässt sich stattdessen ein externer Sammeldienst angeben.

## Was unter einer durchgesetzten Richtlinie nicht mehr funktioniert

- Inline-Skripte und Event-Handler-Attribute in Templates Ihres Designs oder von Erweiterungen.
- Kontextmenüeinträge einiger Erweiterungen, die Inline-Skripte verwenden (die Funktionsseite nennt xrowextract,
  ezodf und ezmultiupload); sie werden angezeigt, tun aber nichts.
- TinyMCE 3, das Handler-Attribute schreibt; der Editor weicht auf TinyMCE 8 aus, sofern der Siteaccess nicht
  `'unsafe-inline'` erlaubt.
- Einbettungen Dritter (Karten, Video, Analyse, CAPTCHAs), bis ihre Hosts mit `AdditionalSources[]` ergänzt sind.

Die Administrationen admin4, admin4l und AdminUI sind so geschrieben, dass sie unter einer durchgesetzten Richtlinie
laufen; ein Test schlägt bei Inline-Skripten, Handlern, `javascript:`-URLs und `style=""` in Templates von admin4 und
admin4l fehl.

## Verwandte Seiten

- [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)
- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Content Security Policy für Hoster](../../hosting/sicherheit/content-security-policy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
