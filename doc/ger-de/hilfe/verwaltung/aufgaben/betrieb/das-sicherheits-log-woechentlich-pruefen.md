---
title: Das Sicherheits-Log wöchentlich prüfen
audience: government, admin, project-manager
category: public-sector
order: 160
summary: Schritt für Schritt - eine wöchentliche Prüfung von security.log über die Fehlerübersicht und den Log-Viewer - Kategorien und Regeln, Zeilen mit hoher Konfidenz, die eine 200 erhielten, Fluten, neue Ziele - mit einer kurzen Notiz für das Sicherheitsteam.
keywords: Sicherheits-Log, wöchentliche Prüfung, security.log, Anfrageklassifizierung, Sondierungen, Injection, Traversal, Scanner, Schwellenwert, Fehlerübersicht, Sicherheitsüberwachung, Notiz, security log, weekly review, request classifier, probes, injection, traversal, scanner, threshold, error digest, security monitoring, record
type: help
---
# Das Sicherheits-Log wöchentlich prüfen

Diese Aufgabe ist für Administratoren oder Sicherheitsbeauftragte. Am Ende einer 15-minütigen Prüfung wissen Sie, was
Angreifer diese Woche versucht haben und ob etwas Handeln erfordert. `security.log` enthält Anfragen, die der
Klassifizierer als Sondierung, Traversal, Injection, Angriffswerkzeug oder 404-Häufung einstuft, mit jedem Status; er
blockiert nichts ([Anfrage-Logs: 404 und Sicherheit](../../betrieb/ueberwachung/anfrageprotokolle-404-und-sicherheit.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was Handeln erfordert](#was-handeln-erfordert)
4. [Die Notiz](#die-notiz)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `setup/logs` oder Shell-Zugang.

## Schritte

1. **Setup > Logdateien > Fehlerübersicht** für die Woche: `/setup/logsdigest/security/(hours)/168`. Die Gruppen sind
   `<category>/<rule>` mit Zahlen.
2. Vergleichen Sie mit der Notiz der Vorwoche: neu auslösende Regeln, große Sprünge.
3. Suchen Sie nach Zeilen mit hoher Konfidenz und Status 200:

   ```bash
   ./console exp:logs --search='confidence=high 200 ' --file=var/log/security.log --rotations
   ```

4. Fluten: Zeilen `threshold/404-rate` (mehr als 30 Antworten „nicht gefunden“ je Minute von einem Client).
5. Prüfen Sie die Seite des Audits für dieselbe Woche: `./console exp:audit alerts list --allow-root-user` und
   ausgelöste Warnungen `brute_force` auf dem Reiter **Audit**, **Alarme**.

## Was Handeln erfordert

| Befund | Maßnahme |
|---|---|
| Sondierungen nach Software, die Sie nicht betreiben (andere CMS) | keine; normaler Hintergrund |
| Injection oder Traversal mit Status 200 | einen Entwickler bestätigen lassen, dass die Antwort keine Daten preisgegeben hat |
| Viele Anfragen aus wenigen Netzen | das Muster an die Stelle geben, die die vorgeschaltete Schicht betreibt ([DDoS und Fluten von Sondierungen](../../betrieb/vorfaelle/ddos-und-flut-von-sondierungsanfragen.md)) |
| Ein neuer Zielpfad auf Ihrer eigenen Site | die Verarbeitung der Eingaben dieser Seite prüfen |
| Warnungen `brute_force` | [Vermutete Kompromittierung eines Kontos](../../betrieb/vorfaelle/verdacht-auf-kompromittiertes-konto.md), wenn ein Konto danach erfolgreich war |

## Die Notiz

Woche, die fünf häufigsten Regeln mit Zahlen, alles Ungewöhnliche, Maßnahmen. Bewahren Sie sie bei Ihren
Sicherheitsunterlagen auf; sie zeigt Prüfern eine fortlaufende Überwachungspraxis.

## Verwandte Seiten
- [Die Fehlerübersicht lesen](die-fehleruebersicht-lesen.md)
- [Audit-Warnungen](../../betrieb/ueberwachung/audit-warnungen.md)
- [Sicherheits-Log (Hosting)](../../../hosting/betrieb/sicherheitslog.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
