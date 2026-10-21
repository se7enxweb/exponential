---
title: NIS2 und eine Exponential-Website
audience: government, project-manager, admin
category: public-sector
order: 280
summary: Für öffentliche Stellen in der EU, die unter die NIS2-Richtlinie (EU) 2022/2555 oder daraus abgeleitete nationale Cybersicherheitsvorschriften fallen - eine allgemeine Einordnung, wie Funktionen von Exponential übliche Risikomanagementmaßnahmen unterstützen (Protokollierung, Zugangssteuerung, MFA, Umgang mit Schwachstellen, Sicherungen, Lieferkette), und was organisatorisch bleibt.
keywords: NIS2, NIS-2-Umsetzungsgesetz, Cybersicherheit, Risikomanagementmaßnahmen, Meldung von Sicherheitsvorfällen, Einrichtungen der öffentlichen Verwaltung, Mehr-Faktor-Authentifizierung, Protokollierung, Umgang mit Schwachstellen, Lieferkette, Sicherungen, EU, öffentlicher Sektor, 2022/2555, cybersecurity, risk management measures, incident reporting, public administration entities, multi-factor authentication, logging, vulnerability handling, supply chain, backups, public sector
type: help
---
# NIS2 und eine Exponential-Website

Diese Seite richtet sich an Informationssicherheitsbeauftragte und Projektverantwortliche öffentlicher Stellen in der
EU. Die NIS2-Richtlinie, Richtlinie (EU) 2022/2555 (<https://eur-lex.europa.eu/eli/dir/2022/2555/oj>), legt Pflichten
zum Cybersicherheits-Risikomanagement und zur Meldung für Einrichtungen in ihrem Anwendungsbereich fest, den die
Mitgliedstaaten in ihrem nationalen Recht bestimmen, in vielen Fällen einschließlich Einrichtungen der öffentlichen
Verwaltung. Ob und wie sie für Ihre Organisation gilt, ist eine Rechtsfrage.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wie Sie diese Seite verwenden](#wie-sie-diese-seite-verwenden)
2. [Übliche Maßnahmen und was Exponential bietet](#übliche-maßnahmen-und-was-exponential-bietet)
3. [Was organisatorisch bleibt](#was-organisatorisch-bleibt)
4. [Verwandte Seiten](#verwandte-seiten)

## Wie Sie diese Seite verwenden

Die Richtlinie und die nationalen Gesetze beschreiben Maßnahmen allgemein (Risikoanalyse, Bewältigung von
Sicherheitsvorfällen, Aufrechterhaltung des Betriebs, Sicherheit der Lieferkette, sichere Entwicklung und Wartung,
Umgang mit Schwachstellen, Zugangssteuerung, Kryptografie, Mehr-Faktor-Authentifizierung und weitere). Die Leitlinien
Ihrer nationalen Behörde machen daraus konkrete Anforderungen. Die folgende Tabelle hilft Ihnen, für eine mit
Exponential betriebene Website die Funktionen zu finden, die Nachweise beitragen. Sie ist keine Compliance-Zuordnung,
und das Projekt erhebt keinen NIS2-Anspruch.

## Übliche Maßnahmen und was Exponential bietet

| Maßnahmenbereich (allgemein) | Beitragende Funktionen von Exponential | Seite in diesem Bereich |
|---|---|---|
| Zugangssteuerung und minimale Rechte | Rollen, Richtlinien mit Einschränkungen nach Sektion und Teilbaum; Kontenprüfung; Deaktivieren und verschieben | Rollen und minimale Rechte |
| Mehr-Faktor-Authentifizierung | zweistufige Anmeldung (`sevenx_authentication_2fa`), kann verpflichtend gemacht werden | Zweistufige Anmeldung für Beschäftigte öffentlicher Stellen |
| Protokollierung und Überwachung | manipulationssicheres Audit-Protokoll standardmäßig an, Alarmregeln, `security.log`, CSP-Berichte, Weiterleitung an ein SIEM | Das Audit-Protokoll; Weiterleitung an ein SIEM |
| Bewältigung von Sicherheitsvorfällen | Filter der Audit-Konsole nach Anfrage, Benutzer, Zeit; Prüfung; Exporte | Reaktion auf Sicherheitsvorfälle |
| Umgang mit und Offenlegung von Schwachstellen | `SECURITY.md`, unveränderliche Releases; Ihre eigene `security.txt` | Offenlegung von Schwachstellen; security.txt hinzufügen |
| Sichere Konfiguration | sichere Standardwerte, Sicherheits-Header, CSP, `ManagedByDeployment` | Härtung; Von der Bereitstellung verwaltete Konfiguration |
| Integrität | Dateikonsistenzprüfung gegen die Prüfsummen des Release | Dateikonsistenzprüfung |
| Kryptografie | bcrypt-Passwort-Hashes, HTTPS und HSTS, `Secure`-Cookies | Passwortrichtlinie; HTTPS und HSTS |
| Aufrechterhaltung des Betriebs | Sicherungen von Datenbank, Speicher und `settings/override`; Wiederherstellungstests | Sicherung und Wiederherstellung |
| Lieferkette | Open Source unter GPL, Composer-Lock-Datei, `composer audit`, Changelogs der Releases | Patch-Rhythmus |

## Was organisatorisch bleibt

- Festlegung des Geltungsbereichs, Risikoanalyse und Billigung durch die Leitung.
- Meldung von Sicherheitsvorfällen an Behörden innerhalb der Fristen, die Ihr nationales Recht setzt: Exponential
  liefert Aufzeichnungen, nicht den Meldeprozess.
- Verträge mit Hosting- und Implementierungslieferanten und deren Sicherheitspflichten.
- Schulung der Beschäftigten.
- Erprobung der Maßnahmen (Penetrationstests, Übungen).

## Verwandte Seiten

- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Die Sicherheitslage von Exponential auf einen Blick](sicherheitslage-auf-einen-blick.md)
- [Compliance und DSGVO für Hoster](../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
