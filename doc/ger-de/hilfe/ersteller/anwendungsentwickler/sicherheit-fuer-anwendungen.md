---
title: Sicherheit für Anwendungen
audience: app-creator, developer
category: app-creation
order: 560
summary: Die Sicherheitsregeln, denen eine Exponential-Anwendung folgt - Rechte an jedem Einstiegspunkt, Eingabeprüfung, Maskierung der Ausgabe, Formular-Tokens, sichere Weiterleitungen, Uploads, Geheimnisse, CSP-taugliches Markup, Protokollierung und Offenlegung - jeweils mit der Kernel-Funktion, die dahintersteht.
keywords: Sicherheit, Rechte, Eingabeprüfung, SQL-Injection, Uploads, Weiterleitungen, Geheimnisse, Audit, Offenlegung von Schwachstellen, Anwendungsentwickler, security, OWASP, permissions, input validation, XSS, CSRF, SQL injection, uploads, SVG, redirects, secrets, CSP, audit, vulnerability disclosure, app creator
type: help
---
# Sicherheit für Anwendungen

Diese Seite richtet sich an Anwendungsentwickler, deren Code so sicher sein soll wie der Kernel um ihn herum. Jede Regel
nennt die Kernel-Funktion, die dabei hilft; die Standardseite ordnet sie den OWASP Top 10 zu
([Sicherheitsstandards](../../../../eng-us/standards/security.md)).

## Inhalt

1. [Die Regeln](#die-regeln)
2. [Einstiegspunkte, die zu sichern sind](#einstiegspunkte-die-zu-sichern-sind)
3. [Vor der Auslieferung](#vor-der-auslieferung)
4. [Schwachstellen melden](#schwachstellen-melden)

## Die Regeln

| Regel | Hilfe des Kernels | Lesen |
|---|---|---|
| Rechte in jeder Ansicht, Serverfunktion, jedem Fetch und Dienst prüfen | `functions` in `module.php`, `hasAccessTo()`, `canRead()` | [Zugriffskontrolle im Code](zugriffskontrolle-im-code.md) |
| Alle Eingaben prüfen und typumwandeln | `eZHTTPTool`, Modulaktionen | [Eingabeprüfung und Escaping](eingabepruefung-und-escaping.md) |
| Alle Ausgaben maskieren | `|wash` in Templates | ebenda |
| POST-Formulare schützen | `ezformtoken` | [Formulare, CSRF und HTTP-Eingaben](formulare-csrf-und-http-eingaben.md) |
| SQL nur mit Maskierung und Typumwandlung oder `eZPersistentObject` | `escapeString()` | [Datenbankzugriff](datenbankzugriff.md) |
| Nur innerhalb der Website weiterleiten | Prüfungen für sichere Weiterleitungen | [Sichere Weiterleitungen](../../../../eng-us/features/6.0/safe-redirects.md) |
| Uploads über den Kernel speichern | Bild- und Datei-Datentypen; SVG-Bereinigung | [SVG-Bilder](../../../bc/6.0/svg-bilder.md) |
| Keine Inline-Skripte oder -Styles | die Content Security Policy | [Content Security Policy für Anwendungen](content-security-policy.md) |
| Keine Geheimnisse in Code, Nutzdaten oder Logs | `settings/override/` | [Umgebungseinstellungen und Geheimnisse](umgebungseinstellungen-und-geheimnisse.md) |
| Sicherheitsrelevante Aktionen aufzeichnen | das Audit-Protokoll | [Audit-Protokoll](../../../../eng-us/features/6.0/audit-trail.md) |
| Ausgehende Anfragen nur an erlaubte Hosts | die Regel der Linkprüfung für private Netze als Vorbild | [URLs und Links](../../../anleitungen/urls-und-aliase.md) |

## Einstiegspunkte, die zu sichern sind

Jeder Weg in Ihren Code braucht seine eigene Prüfung:

- Modulansichten (die `functions` in `module.php` plus Ihre eigenen Prüfungen für Einschränkungen);
- ezjscore-Serverfunktionen (ezjscore prüft keine Richtlinie für Sie);
- REST-Controller (das Framework authentifiziert; Sie autorisieren);
- Fetch-Funktionen und Template-Operatoren (Templates können von überall aufgerufen werden);
- Cronjobs, Befehle und Warteschlangen-Handler (laufen als der richtige Benutzer; gespeicherte Eingaben erneut prüfen).

## Vor der Auslieferung

- [ ] Jeder Einstiegspunkt mit einem Benutzer geprüft, dem das Recht fehlt.
- [ ] Jeder ausgegebene Wert maskiert; kein rohes HTML von Benutzern.
- [ ] Formulare senden mit Tokens; keine Zustandsänderung per GET.
- [ ] Uploads nach Typ und Größe geprüft, über den Kernel gespeichert.
- [ ] Keine Geheimnisse im Repository (`git log -p` durchsucht).
- [ ] Seiten erzeugen keine Meldungen in `var/log/csp.log`.
- [ ] Fehler zeigen Besuchern keine Stacktraces; Debug-Ausgabe in der Produktion aus.
- [ ] Abhängigkeiten aktuell; Composer-Pakete auf gepflegte Versionen festgelegt.

## Schwachstellen melden

Schwachstellen in Exponential werden vertraulich gemeldet, wie es `SECURITY.md` im Repository beschreibt, nicht in
öffentlichen Issues ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)). Geben Sie auch Ihrer eigenen
Erweiterung eine `SECURITY.md` mit einem vertraulichen Kontakt.

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [13. Sicherheitshärtung (Installation)](../../../installation/13-sicherheitshaertung.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
