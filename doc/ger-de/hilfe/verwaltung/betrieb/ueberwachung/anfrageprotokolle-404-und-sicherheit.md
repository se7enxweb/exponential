---
title: "Anfrage-Logs: 404 und Sicherheit"
audience: government, admin, hosting
category: public-sector
order: 50
summary: Wie Exponential Antworten „nicht gefunden“ (404.log) und Anfragen, die wie Sondierungen oder Angriffe aussehen (security.log), von echten Fehlern trennt, was jede Zeile enthält, wie personenbezogene Daten maskiert werden und wie eine öffentliche Stelle sie für die Arbeit an defekten Links und für das Lagebild zu Bedrohungen nutzt.
keywords: 404.log, security.log, Anfrageklassifizierer, Sondierungen, Angriffe, Injektion, Pfadmanipulation, Scanner, nicht gefunden, defekte Links, Maskierung, MaskIP, Bedrohungsüberwachung, request classifier, probes, attacks, injection, traversal, scanner, not found, broken links, masking, threat monitoring
type: help
---
# Anfrage-Logs: 404 und Sicherheit

Diese Seite richtet sich an die Administration und das Sicherheitspersonal öffentlicher Websites, die einen stetigen
Strom automatisierter Sondierungen anziehen. Exponential schreibt Antworten „nicht gefunden“ und vermutete Angriffe in
eigene Logs, damit `error.log` die echten Fehler enthält. Referenz: [Anfrage-Logs](../../../../funktionen/6.0/anfrageprotokolle.md);
Sicht des Hostings: [Sicherheitslog (Hosting)](../../../hosting/betrieb/sicherheitslog.md).

## Inhalt
1. [Drei Logs, eine Zeile je Anfrage](#drei-logs-eine-zeile-je-anfrage)
2. [Was security.log Ihnen sagt](#was-securitylog-ihnen-sagt)
3. [Was 404.log Ihnen sagt](#was-404log-ihnen-sagt)
4. [Datenschutz](#datenschutz)
5. [Einstellungen](#einstellungen)
6. [Verwandte Seiten](#verwandte-seiten)

## Drei Logs, eine Zeile je Anfrage

| Log | Was | Stufe |
|---|---|---|
| `error.log` | echte Fehler (Datenbankfehler, ein Template, das sich nicht kompilieren lässt, PHP-Fehler) | error |
| `404.log` | Antworten 404 und 410, mit dem Grund, den der Kernel gefunden hat | notice |
| `security.log` | Anfragen, die der Klassifizierer als Sondierung, Pfadmanipulation, Injektion, Angriffswerkzeug oder Häufung von Antworten „nicht gefunden“ einstuft, mit beliebigem Status | warning |

Eine Anfrage steht in höchstens einem davon. **Es wird nichts blockiert**: Der Klassifizierer stuft nur ein; die Anfrage
erhält die Antwort, die sie ohnehin bekommen würde. Das Blockieren gehört vor die Website
([Sicherheitshärtung, request-shield](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Was security.log Ihnen sagt

Jede Zeile nennt `<category>/<rule>` (`probe/wp-login`, `injection/sql-union`, `threshold/404-rate` ...), eine
Konfidenz (low, medium, high), den Status, die Anfrage mit den Abfragewerten als Beweis (Geheimnisse und Adressen
maskiert), den Siteaccess und einen gekürzten User-Agent. Lesen Sie es auf:

- **Trends**: eine Regel, die plötzlich viel häufiger anschlägt, oder Injektionen mit hoher Konfidenz, die ein `200`
  erhalten haben.
- **Fluten**: `threshold/404-rate` markiert einen Client mit mehr als `Max404PerMinute` (30) Antworten „nicht gefunden“
  in einer Minute ([DDoS und Fluten von Sondierungsanfragen](../vorfaelle/ddos-und-flut-von-sondierungsanfragen.md)).
- **Ziele**: welche Pfade Angreifer ausprobieren; ein erfolgreich aussehender Status in einer Injektionszeile verdient
  einen Blick der Entwicklung.

Die Fehlerübersicht gruppiert es nach Kategorie und Regel; sehen Sie es wöchentlich durch
([Das Sicherheitslog wöchentlich durchsehen](../../aufgaben/betrieb/das-sicherheits-log-woechentlich-pruefen.md)).

## Was 404.log Ihnen sagt

Jede Zeile enthält den Status (404 oder 410), die Anfrage, den Siteaccess, den Grund (`module:`, `view:`, `node:<id>`,
`object:<id>`, `rule:`, `file:` ...), die Knoten-ID, wenn bekannt, und den Referrer. Nutzen Sie es für:

- **Defekte Links** auf Ihrer eigenen Website (der Referrer ist Ihre Website): Korrigieren Sie den Link oder ergänzen
  Sie einen URL-Alias oder eine Wildcard.
- **Verschobene Inhalte**, nach denen Bürgerinnen und Bürger noch suchen (der Referrer ist eine Suchmaschine oder die
  Website einer anderen öffentlichen Stelle).
- **Entfernte Inhalte**, die mit 410 Gone statt mit 404 antworten sollten.

## Datenschutz

Client-Adressen werden maskiert (`203.0.x.x`, `2001:db8:x:x`); jeder Abfragewert wird in `404.log` durch `***` ersetzt;
User-Agents sind ein Hash und ein kurzes Präfix. Die unveränderte Adresse wird nie in eine Datei geschrieben.
`MaskIP=disabled` schreibt Adressen vollständig, „nur für eine Website, die einen Grund und eine Rechtsgrundlage dafür
hat“. Rotation: standardmäßig 5 Archive, komprimiert, 10 MB, bereinigt nach 30 Tagen (404) und 90 Tagen (Sicherheit).

## Einstellungen

| Einstellung | Standard | Wirkung |
|---|---|---|
| `site.ini [RequestLogSettings] NotFoundLog` | `enabled` | `disabled`: Antworten „nicht gefunden“ gehen wie früher in `error.log` |
| `site.ini [RequestLogSettings] MaskIP` | `enabled` | Maskierung der Adressen |
| `requestclassifier.ini [ClassifierSettings] Classification` | `enabled` | `disabled`: nichts wird eingestuft |
| `requestclassifier.ini [ThresholdSettings] Max404PerMinute` | `30` | die Regel für 404-Häufungen; `0` schaltet sie ab |

## Verwandte Seiten
- [Fehlerübersicht und Zustand der Logs](fehleruebersicht-und-zustand-der-logs.md)
- [CSP-Berichte](csp-berichte.md)
- [Audit-Warnungen](audit-warnungen.md)
- [Anfrageregeln](../../../../funktionen/6.0/anfrageregeln.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
