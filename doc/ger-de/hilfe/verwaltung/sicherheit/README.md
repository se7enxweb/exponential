---
title: Sicherheit für öffentliche Stellen
audience: government, admin, project-manager, developer, hosting
category: public-sector
order: 10
summary: Der Sicherheitsbereich der Hilfe für Verwaltungen - die dokumentierte Sicherheitslage von Exponential (OWASP Top 10, ASVS, CSP), Härtung, das Audit-Protokoll, die zweistufige Anmeldung, die Durchsetzung der CSP, request-shield, Passwörter und Sitzungen, Offenlegung und security.txt, Patches, die Einordnung nach NIS2, NIST CSF und ISO 27001, Penetrationstests und die Reaktion auf Sicherheitsvorfälle.
keywords: Sicherheit, öffentlicher Sektor, Verwaltung, Behörde, Härtung, Audit-Protokoll, zweistufige Anmeldung, Mehr-Faktor-Authentifizierung, Passwortrichtlinie, Kontosperre, Offenlegung von Schwachstellen, Patches, Sicherheitsvorfall, security, public sector, government, OWASP, ASVS, hardening, audit trail, two-step sign-in, MFA, CSP, request-shield, HSTS, password policy, lockout, security.txt, vulnerability disclosure, patching, NIS2, NIST CSF, ISO 27001, incident response
type: help
---
# Sicherheit für öffentliche Stellen

Dieser Bereich richtet sich an Informationssicherheitsbeauftragte, Administratorinnen und Administratoren,
Entwicklerinnen und Entwickler, Hosting-Teams und Projektverantwortliche öffentlicher Stellen, die eine Website mit
Exponential betreiben. Jede Aussage über das Produkt stammt aus dem Repository: aus den
[Sicherheitsstandards](../../../../eng-us/standards/security.md) des Projekts (deren Statusangaben „Teilweise erfüllt“,
„Nicht geprüft“ und, für OWASP A09, „Erfüllt“ unverändert übernommen werden), aus der
[Sicherheitshärtung](../../sicherheitshaertung.md), aus
[Kapitel 13 der Installationsanleitung](../../../installation/13-sicherheitshaertung.md), dem Audit-Leitfaden und den
Funktionsseiten. Lücken werden benannt, wo es sie gibt: Kontosperre standardmäßig aus, zweistufige Anmeldung nur auf
Wunsch, CSP in der Administration nur im Report-only-Modus und auf Websites aus, HSTS standardmäßig leer, keine
mitgelieferte `security.txt`, kein Abhängigkeitsscan in der CI des Projekts.

## Inhalt

1. [Lesereihenfolge](#lesereihenfolge)
2. [Seiten in diesem Bereich](#seiten-in-diesem-bereich)
3. [Verwandte Seiten](#verwandte-seiten)

## Lesereihenfolge

- **Sicherheitsbeauftragte und Projektverantwortliche**: Sicherheitslage auf einen Blick, die OWASP-Zuordnungen, die
  Seiten zur Einordnung (NIS2, NIST CSF, ISO 27001), die Go-live-Checkliste, Penetrationstests, die Reaktion auf
  Sicherheitsvorfälle.
- **Administratorinnen und Administratoren**: Härtung im Überblick, dann die Seiten zu Anmeldung, Sitzungen,
  Headern, HTTPS, CSP, Audit-Protokoll, Protokollen und Sicherungen.
- **Entwicklungs- und Hosting-Teams**: CSP-Plan, Uploads, Formular-Tokens, Proxys, Dateirechte, von der Bereitstellung
  verwaltete Konfiguration, request-shield.

## Seiten in diesem Bereich

| Seite | Beantwortet |
|---|---|
| [Die Sicherheitslage von Exponential auf einen Blick](sicherheitslage-auf-einen-blick.md) | Status je Standard; was standardmäßig an und aus ist; bekannte Lücken |
| [Die OWASP Top 10 auf Exponential abgebildet](owasp-top-10-zuordnung.md) | Gegenmaßnahmen, Ihr Anteil und Nachweise je Kategorie |
| [OWASP ASVS auf Exponential abgebildet](owasp-asvs-zuordnung.md) | Kontrollen nach Kapitel; die Lücke bei der Passwortlänge |
| [Eine Exponential-Website für eine öffentliche Stelle härten](haertung-im-ueberblick.md) | die Schichten in ihrer Reihenfolge |
| [Sicherheits-Checkliste vor dem Go-live öffentlicher Websites](sicherheits-checkliste-vor-dem-go-live.md) | Checkliste mit abzulegenden Nachweisen |
| [Das Audit-Protokoll für öffentliche Stellen](audit-protokoll-fuer-oeffentliche-stellen.md) | was aufgezeichnet wird (und was nicht), Aufbewahrung, wer es liest |
| [Audit-Alarme und ihre Empfänger](audit-alarme-und-empfaenger.md) | die sieben mitgelieferten Regeln; Zustellung per E-Mail |
| [Integrität und Prüfung des Audit-Protokolls](audit-integritaet-und-pruefung.md) | Hash-Ketten, Prüfpunkte, Prüfung, unterbrochene Ketten |
| [Das Audit-Protokoll an ein SIEM weiterleiten](audit-an-ein-siem-weiterleiten.md) | Syslog- und Webhook-Ziele |
| [Zweistufige Anmeldung für Beschäftigte öffentlicher Stellen](zweistufige-anmeldung.md) | einschalten, verlangen, einführen, verlorene Telefone |
| [Content Security Policy für öffentliche Websites](content-security-policy.md) | wie sie ausgeliefert wird, Einstellungen, Berichte |
| [Von Report-only zu einer durchgesetzten CSP](csp-von-report-only-zur-durchsetzung.md) | ein gestufter Plan mit Rückfallmöglichkeit |
| [request-shield vor einer öffentlichen Website](request-shield.md) | eine eigene Firewall; zuerst beobachten |
| [Anfrageprotokolle und das Sicherheitsprotokoll](anfrageprotokolle-und-das-sicherheitsprotokoll.md) | `404.log`, `security.log`, Maskierung, Aufbewahrung |
| [Sicherheits-Header](sicherheits-header.md) | die fünf Standardwerte; prüfen und ändern |
| [HTTPS und HSTS](https-und-hsts.md) | Schritte, Einstellungen, HSTS sicher anheben |
| [Passwortrichtlinie](passwortrichtlinie.md) | Einstellungen und eine längenbasierte Richtlinie |
| [Kontosperre](kontosperre.md) | standardmäßig aus; einschalten, entsperren |
| [Sitzungen und Cookies](sitzungen-und-cookies.md) | Cookie-Attribute, Zeitlimits, Benutzer abmelden |
| [Rollen und minimale Rechte](rollen-und-minimale-rechte.md) | sensible Richtlinien, ein Rollenkonzept, Zugriffsüberprüfungen |
| [Datei-Uploads und SVG](datei-uploads-und-svg.md) | Schutz bei Uploads und der SVG-Bereiniger |
| [Von der Bereitstellung verwaltete Konfiguration](von-der-bereitstellung-verwaltet.md) | Konfigurationsänderungen im Produktivbetrieb sperren |
| [Dateikonsistenzprüfung](dateikonsistenzpruefung.md) | geänderte Dateien erkennen |
| [Offenlegung von Schwachstellen](offenlegung-von-schwachstellen.md) | die Richtlinie des Projekts und Ihre eigene |
| [security.txt hinzufügen](security-txt-hinzufuegen.md) | Datei nach RFC 9116 und die Rewrite-Regel |
| [Patch-Rhythmus](patch-rhythmus.md) | was zu beobachten ist und eine Aktualisierungsroutine |
| [NIS2 und eine Exponential-Website](nis2-einordnung.md) | allgemeine Einordnung üblicher Maßnahmen |
| [NIST Cybersecurity Framework und eine Exponential-Website](nist-csf-einordnung.md) | die sechs Funktionen |
| [ISO/IEC 27001 und eine Exponential-Website](iso-27001-einordnung.md) | ISMS-Geltungsbereich und Nachweise |
| [Penetrationstests einer Exponential-Website](penetrationstests.md) | Umfang, Vorbereitung, Befunde |
| [Geheimnisse in settings/override](geheimnisse-in-settings-override.md) | welche Dateien Geheimnisse enthalten und wie Sie sie schützen |
| [Die Administration abtrennen](die-administration-abtrennen.md) | eigener Host, Netzwerkbeschränkung, Anfrageregeln |
| [Routine zur Protokollprüfung](routine-zur-protokollpruefung.md) | wöchentliche und monatliche Prüfungen |
| [Debug-Ausgabe im Produktivbetrieb](debug-ausgabe-im-produktivbetrieb.md) | sichere Standardwerte; Debugging für eine Adresse |
| [CSRF und Formular-Tokens](csrf-und-formular-tokens.md) | `ezformtoken` und eigener Code |
| [Proxys und vertrauenswürdige Header](proxys-und-vertrauenswuerdige-header.md) | `TrustedProxies[]`, von rechts lesen |
| [Dateirechte und Eigentümer](dateirechte-und-eigentuemer.md) | ein Schreiber, schreibgeschützter Code, gehärtete Modi |
| [Sicherung und Wiederherstellung](sicherung-und-wiederherstellung.md) | konsistente Sicherungen, Wiederherstellungstests, kleinere Wiederherstellungen |
| [Reaktion auf Sicherheitsvorfälle bei einer Exponential-Website](reaktion-auf-sicherheitsvorfaelle.md) | eindämmen, untersuchen, neu aufbauen, melden |

Aufgaben, die diese Seiten Schritt für Schritt anwenden, finden Sie in den Compliance-Aufgaben dieser Hilfe
([Compliance-Aufgaben](../../../../eng-us/help/government/tasks/compliance/README.md)).

## Verwandte Seiten

- Nach oben: [Verwaltungen: hier beginnen](../verwaltungen-hier-beginnen.md)
- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Sicherheit für Hoster](../../hosting/sicherheit/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
