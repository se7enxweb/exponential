---
title: Penetrationstests einer Exponential-Website
audience: government, project-manager, admin
category: public-sector
order: 310
summary: Für öffentliche Stellen, die einen Sicherheitstest ihrer Website beauftragen - Umfang, Einsatzregeln, was Sie den Testenden geben (Versionen, Siteaccesses, Testkonten je Rolle, bekannte Lücken), wie Sie die Installation vorbereiten und wie Sie mit Befunden in Exponential gegenüber Ihrem eigenen Code umgehen.
keywords: Penetrationstest, Pentest, Sicherheitstest, Umfang, Einsatzregeln, Testkonten, Staging, Befunde, Behebung, Nachtest, Audit-Protokoll, Offenlegung, öffentlicher Sektor, Beschaffung, penetration test, security test, scope, rules of engagement, test accounts, staging, findings, remediation, retest, request-shield, audit trail, disclosure, public sector, procurement
type: help
---
# Penetrationstests einer Exponential-Website

Diese Seite richtet sich an Projektverantwortliche und Sicherheitsbeauftragte, die einen Penetrationstest
beauftragen, und an Administratorinnen und Administratoren, die die Installation vorbereiten. Ein guter Test einer
Exponential-Website konzentriert sich auf das, was Ihnen gehört (Design, Erweiterungen, Konfiguration, Hosting), und
meldet Befunde im Produkt an das Projekt.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Umfang](#umfang)
2. [Was Sie den Testenden geben](#was-sie-den-testenden-geben)
3. [Die Installation vorbereiten](#die-installation-vorbereiten)
4. [Während des Tests](#während-des-tests)
5. [Umgang mit Befunden](#umgang-mit-befunden)
6. [Verwandte Seiten](#verwandte-seiten)

## Umfang

- **Umgebung**: Eine Staging-Kopie mit derselben Version, denselben Erweiterungen, Designs und derselben
  Webserver-Konfiguration ist das sicherste Ziel; ein Test der Produktion braucht eine schriftliche Genehmigung und ein
  Zeitfenster.
- **Ziele**: die öffentlichen Siteaccesses, der Administrations-Siteaccess, die REST-API, falls aktiviert
  ([REST API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)), Formulare, Datei-Uploads, Anmeldung und zweistufige
  Anmeldung.
- **Außerhalb des Umfangs**: Dienste Dritter, die Ihre Erweiterungen aufrufen (Analyse, CAPTCHA, Karten, KI-Anbieter),
  sofern deren Betreiber nicht zustimmen.

## Was Sie den Testenden geben

| Punkt | Quelle |
|---|---|
| Versionen | `composer show se7enxweb/exponential`, Setup > Systeminformationen |
| Aktivierte Erweiterungen | Setup > Extensions |
| Siteaccesses und Hostnamen | Ihre Konfiguration |
| Testkonten, eines je Rolle (anonym, Mitglied, Redaktion, Veröffentlichende, Administration) | für den Test angelegt, danach deaktiviert |
| Bekannte Lücken und akzeptierte Risiken | [Sicherheitsstandards: bekannte Lücken](../../../standards/sicherheit.md#bekannte-lücken) und Ihr Risikoregister |
| Die geltenden Sicherheitseinstellungen | Passwortrichtlinie, Kontosperre, CSP-Modus, HSTS |

Wenn Sie den Testenden die bekannten Lücken mitteilen (zum Beispiel „Kontosperre bewusst aus, Entscheidung
festgehalten“), sparen Sie Zeit und richten den Bericht auf das Neue aus.

## Die Installation vorbereiten

1. Führen Sie die Dateikonsistenzprüfung aus, damit der Test von einem bekannten Zustand ausgeht.
2. Entscheiden Sie, was mit request-shield geschieht: vorgeschaltet lassen, um den realen Aufbau zu testen, oder in den
   Beobachtungsmodus setzen, damit die Testenden die Anwendung erreichen; halten Sie die Wahl fest.
3. Informieren Sie die Empfänger der Alarme: Audit-Alarme (Brute-Force, Rollenvergabe) werden auslösen.
4. Sichern Sie die Staging-Datenbank und `var/storage`.
5. Ist die Kontosperre eingeschaltet, nehmen Sie die Adressen der Testenden nur dann in `TrustedIPList[]` auf, wenn Sie
   die Kontosperre vom Test ausnehmen wollen.

## Während des Tests

- Das Audit-Protokoll zeichnet Anmeldungen, Abweisungen und Änderungen auf; `security.log` zeichnet Sondierungen auf.
  Beides hilft Ihnen, die Aktivität der Testenden mit dem abzugleichen, was Ihre Überwachung gesehen hat: an sich schon
  eine nützliche Übung zur Erkennung.
- Jede Antwort der Administration trägt `X-Exp-Request-Id`; die Testenden können ihn angeben, damit Sie die genauen
  Einträge finden (`/audit/console/(request)/<id>`).

## Umgang mit Befunden

| Befund in | Tun |
|---|---|
| Ihrem Design, Ihren Templates oder Erweiterungen | beheben, erneut testen |
| Konfiguration oder Hosting | nach den Härtungsseiten beheben, erneut testen |
| Exponential selbst oder einer mitgelieferten Erweiterung | vertraulich an security@se7enx.com melden (siehe „Offenlegung von Schwachstellen“); nicht veröffentlichen, bevor eine Korrektur erschienen ist |

Nach dem Test: Deaktivieren Sie die Testkonten, entfernen Sie Testinhalte, stellen Sie request-shield wieder auf
Durchsetzen und bewahren Sie den Bericht bei Ihrer Sicherheitsdokumentation auf.

## Verwandte Seiten

- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Offenlegung von Schwachstellen](offenlegung-von-schwachstellen.md)
- [Das Audit-Protokoll für öffentliche Stellen](audit-protokoll-fuer-oeffentliche-stellen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
