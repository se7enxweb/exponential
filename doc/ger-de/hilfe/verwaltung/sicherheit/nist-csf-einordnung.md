---
title: NIST Cybersecurity Framework und eine Exponential-Website
audience: government, project-manager, admin
category: public-sector
order: 290
summary: Für öffentliche Stellen, die ihre Sicherheit mit dem NIST Cybersecurity Framework 2.0 organisieren - eine allgemeine Einordnung, wo sich die Funktionen von Exponential und die Praxis des Betreibers unter den sechs Funktionen Govern, Identify, Protect, Detect, Respond und Recover einfügen.
keywords: NIST CSF, Cybersecurity Framework 2.0, Steuern, Identifizieren, Schützen, Erkennen, Reagieren, Wiederherstellen, Profil, Risikomanagement, Vereinigte Staaten, öffentlicher Sektor, Maßnahmen, Website-Sicherheit, govern, identify, protect, detect, respond, recover, profile, risk management, United States, public sector, controls, website security
type: help
---
# NIST Cybersecurity Framework und eine Exponential-Website

Diese Seite richtet sich an Sicherheits- und Projektverantwortliche öffentlicher Stellen, die das NIST Cybersecurity
Framework (CSF) 2.0 (<https://www.nist.gov/cyberframework>) zur Organisation ihrer Arbeit verwenden, in den Vereinigten
Staaten und anderswo. Das Framework gruppiert Ergebnisse unter sechs Funktionen. Unten finden Sie für eine mit
Exponential betriebene Website die Funktionen und Praktiken, die zu jeder Funktion beitragen. Das ist eine
Einordnungshilfe, kein Profil und keine Bewertung; das Projekt erhebt keinen CSF-Anspruch.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Govern (Steuern)](#govern-steuern)
2. [Identify (Identifizieren)](#identify-identifizieren)
3. [Protect (Schützen)](#protect-schützen)
4. [Detect (Erkennen)](#detect-erkennen)
5. [Respond (Reagieren)](#respond-reagieren)
6. [Recover (Wiederherstellen)](#recover-wiederherstellen)
7. [Verwandte Seiten](#verwandte-seiten)

## Govern (Steuern)

- Rollen und Verantwortlichkeiten für die Website (Eigentümer, Administration, Lieferanten); siehe „Rollen und minimale
  Rechte“.
- Richtlinien für Passwörter, MFA, Patches und Offenlegung; siehe „Passwortrichtlinie“, „Patch-Rhythmus“,
  „Offenlegung von Schwachstellen“.
- Lieferantenmanagement: Exponential ist Open Source (GPL v2 oder neuer); Hosting- und Implementierungspartner werden
  vertraglich gebunden.

## Identify (Identifizieren)

- Bestandsaufnahme: Version von Exponential (`composer show se7enxweb/exponential`), aktivierte Erweiterungen
  (Setup > Extensions), Composer-Lock-Datei, Versionen von PHP und Datenbank (Setup > Systeminformationen).
- Bekannte Lücken aus der Erklärung des Projekts ([Sicherheitsstandards](../../../../eng-us/standards/security.md#known-gaps))
  gehören in das Risikoregister.

## Protect (Schützen)

| Ergebnisbereich | Exponential |
|---|---|
| Identität und Zugang | Rollen und Richtlinien, zweistufige Anmeldung, Passwortrichtlinie, Kontosperre, Kontenprüfung |
| Datensicherheit | HTTPS und HSTS, `Secure`/`HttpOnly`-Cookies, Geheimnisse in `settings/override` |
| Plattformsicherheit | Sicherheits-Header, CSP, Upload-Kontrollen, `ManagedByDeployment`, Dateirechte, Updates |

## Detect (Erkennen)

- Audit-Protokoll mit Alarmregeln (Brute-Force, Vergabe einer Administratorrolle, Massenlöschung, Audit abgeschaltet,
  unterbrochene Kette).
- `security.log` aus der Anfrageklassifizierung; CSP-Berichte in `csp.log`.
- Dateikonsistenzprüfung gegen die Prüfsummen des Release.
- Weiterleitung an Ihr SIEM.

## Respond (Reagieren)

- Audit-Konsole, um zu rekonstruieren, was geschehen ist, nach Benutzer, Anfrage oder Zeit; Exporte in signierten
  Bündeln.
- Konten deaktivieren, Sitzungen beenden, Wartungsmodus.
- Das Hosting-Runbook für eine kompromittierte Website
  ([Runbook: kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md)).

## Recover (Wiederherstellen)

- Sicherungen der Datenbank, von `var/storage` und `settings/override`, Wiederherstellung getestet
  ([10.9 Sicherung und Wiederherstellung](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)).
- Inhaltsversionen und der Papierkorb, um Inhalte wiederherzustellen; unveränderliche Releases, um Code aus einem
  bekannten Zustand neu aufzubauen.

## Verwandte Seiten

- [Die Sicherheitslage von Exponential auf einen Blick](sicherheitslage-auf-einen-blick.md)
- [Reaktion auf Sicherheitsvorfälle](reaktion-auf-sicherheitsvorfaelle.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
