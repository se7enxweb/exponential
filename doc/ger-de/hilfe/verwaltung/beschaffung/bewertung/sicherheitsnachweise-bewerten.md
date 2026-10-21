---
title: Sicherheitsnachweise in einem Angebot für ein CMS bewerten
audience: government, project-manager, admin, hosting
category: public-sector
order: 100
summary: Wie eine Fachkraft für Sicherheit in einem Bewertungsgremium ein auf Exponential beruhendes Angebot beurteilt - die dokumentierte Zuordnung zu OWASP, die Standardwerte, die geändert werden müssen, der Prozess für Offenlegung und Releases, was nicht belegt ist (ASVS, Penetrationstest, Abhängigkeitsscan) und was Bieter und Hoster ergänzen müssen.
keywords: Sicherheitsbewertung, OWASP Top 10, ASVS, Penetrationstest, Sicherheitsfragebogen, Zwei-Faktor, Audit-Protokoll, CSP, Härtung, Offenlegung von Schwachstellen, security evaluation, penetration test, security questionnaire, two-factor, audit trail, hardening, vulnerability disclosure, SECURITY.md, security.txt
type: help
---
# Sicherheitsnachweise in einem Angebot für ein CMS bewerten

Diese Seite richtet sich an die Informationssicherheitsbeauftragten in einem Bewertungsgremium. Sie zeigt, was die
Dokumentation von Exponential belegt, welche Standardwerte eine öffentliche Website ändern sollte, was fehlt und was Sie
vom Bieter und vom Hoster verlangen. Die Fakten stammen aus [Sicherheitsstandards](../../../../../eng-us/standards/security.md),
[Sicherheitshärtung](../../../sicherheitshaertung.md) und `SECURITY.md` im Wurzelverzeichnis des Repositorys.

## Inhalt
1. [Drei zu bewertende Schichten](#drei-zu-bewertende-schichten)
2. [Was die Produktdokumentation belegt](#was-die-produktdokumentation-belegt)
3. [Im Angebot zu prüfende Standardwerte](#im-angebot-zu-prüfende-standardwerte)
4. [Was nicht belegt ist](#was-nicht-belegt-ist)
5. [Was Sie vom Bieter und vom Hoster verlangen](#was-sie-vom-bieter-und-vom-hoster-verlangen)
6. [Tests im Proof of Concept](#tests-im-proof-of-concept)

## Drei zu bewertende Schichten

| Schicht | Verantwortlich | Nachweis |
|---|---|---|
| Die Software | das Projekt, über Releases | [Sicherheitsstandards](../../../../../eng-us/standards/security.md) |
| Die Konfiguration und eigener Code | der Bieter | sein Härtungsplan und Code-Review |
| Das Hosting | der Hoster (intern oder extern) | seine Betriebsverfahren ([Sicherheit im Hosting](../../../hosting/sicherheit/README.md)) |

## Was die Produktdokumentation belegt

| Bereich | Status | Kurz gesagt |
|---|---|---|
| OWASP Top 10 (2021) | **Partly complies** | Gegenmaßnahmen je Kategorie genannt, drei dokumentierte Härtungsdurchgänge |
| OWASP ASVS 4.0.3 | **Unverified** | keine Bewertung; viele Kontrollen der Stufe 1 vorhanden |
| Content Security Policy Level 3 | **Partly complies** | Nonces je Anfrage und Berichte; standardmäßig Report-only in der Administration, keine auf Websites |
| Protokollierung und Überwachung (A09) | **Complies** | manipulationssicheres Audit-Protokoll standardmäßig an, Alarmregeln, Sicherheitsprotokoll ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) |
| Koordinierte Offenlegung | **Partly complies** | vertrauliche Meldung in `SECURITY.md`; keine mitgelieferte `security.txt` |

Weitere Fakten, auf die sich Bewertende verlassen können: Passwörter werden mit `password_hash()` von PHP gespeichert,
jedes Formular trägt ein CSRF-Token, Sicherheits-Header werden auf jeder Seite gesendet, und veröffentlichte
Release-Tags werden nie geändert
([Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md#tags-are-permanent)).

## Im Angebot zu prüfende Standardwerte

| Standardwert | Ab Werk | Was eine öffentliche Website meist braucht |
|---|---|---|
| Kontosperre | aus (`MaxNumberOfFailedLogin=0`) | eine Entscheidung: Kontosperre oder vorgeschaltete Ratenbegrenzung ([request-shield](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)) |
| Zweistufige Anmeldung | Erweiterung, auf Wunsch | für jede Administratorin, jeden Administrator und jede Redaktion an ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)) |
| Mindestlänge des Passworts | 10 | 12 oder mehr, wenn Sie ASVS Stufe 1 folgen (V2.1.1) |
| HSTS | leer | setzen, sobald die ganze Website nur noch über HTTPS läuft |
| Content Security Policy | Report-only in der Administration, keine auf Websites | durchsetzen, wo das Design es erlaubt ([Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)) |
| `security.txt` | nicht mitgeliefert | eine nach [RFC 9116](https://www.rfc-editor.org/rfc/rfc9116) veröffentlichen |

Bitten Sie den Bieter, jede dieser Einstellungen für Ihre Website im Angebot anzugeben.

## Was nicht belegt ist

- Für das Produkt ist kein externer Penetrationstest und keine Sicherheitsprüfung veröffentlicht.
- Keine Bewertung nach OWASP ASVS
  ([OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/)).
- Kein Schwachstellenscan der Abhängigkeiten in der CI; der Härtungsleitfaden empfiehlt `composer audit`.
- Keine Prepared Statements in der Datenbankschicht; es wird maskiert, und der Code von Erweiterungen muss derselben
  Regel folgen.
- Die Maskierung der Ausgabe in Templates geschieht nicht automatisch.
- Commits und Tags sind nicht signiert.
- Das Projekt veröffentlicht keine Service-Level-Vereinbarung; die einzige Zusage zur Reaktion ist eine erste Antwort
  auf eine vertrauliche Sicherheitsmeldung innerhalb eines Werktags (`SECURITY.md`).

## Was Sie vom Bieter und vom Hoster verlangen

1. Einen Härtungsplan auf Grundlage von
   [Kapitel 13 des Installationsbuchs](../../../../installation/13-sicherheitshaertung.md) und der
   [Härtungs-Checkliste](../../../sicherheitshaertung.md#checkliste), mit jedem Punkt als erledigt oder nicht markiert.
2. Ein Code-Review oder einen Test der eigenen Templates und Erweiterungen des Bieters (Maskierung mit `wash`,
   SQL-Maskierung).
3. Einen Penetrationstest der gelieferten Website vor dem Go-live, durch eine Stelle, die Sie akzeptieren.
4. Eine Zusage für Patches: veröffentlichte Sicherheitskorrekturen von Exponential innerhalb einer von Ihnen festgelegten
   Zeit einspielen, Updates von PHP und Betriebssystem ebenfalls innerhalb einer festgelegten Zeit
   ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).
5. Umgang mit Sicherheitsvorfällen: wer gerufen wird, wie schnell und wie das Audit-Protokoll gesichert wird
   ([Runbook: kompromittierte Website](../../../hosting/runbooks/kompromittierte-website.md)).
6. Eine Zuordnung zu Ihrem eigenen Rahmen, zum Beispiel dem
   [NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) oder
   [ISO/IEC 27001](https://www.iso.org/standard/27001), wenn Ihre Richtlinie eine verlangt. Das Projekt liefert keine.

## Tests im Proof of Concept

- Melden Sie sich mehrmals mit einem falschen Passwort an und suchen Sie die Einträge im Audit.
- Führen Sie `./console exp:audit verify` aus, um die Audit-Kette zu prüfen.
- Richten Sie die zweistufige Anmeldung für ein Testkonto ein.
- Lesen Sie die Antwort-Header einer Seite (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`).
- Führen Sie **Setup > Upgrade-Prüfung** aus, um die Dateikonsistenzprüfung zu sehen.

## Verwandte Seiten
- [Bewertung: Übersicht](README.md)
- [Beschaffung: Übersicht](../README.md)
- [Sicherheitsstandards](../../../../../eng-us/standards/security.md)
- [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
