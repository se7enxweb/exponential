---
title: Das Sicherheits-Log prüfen
audience: government, admin, hosting
category: public-sector
order: 340
summary: Schritt für Schritt - das wöchentliche Lesen von security.log in Exponential unter Setup > Logdateien - was der Anfrageklassifizierer aufzeichnet, welche Zeilen genauer betrachtet werden müssen (Injection oder Traversal mit Status 200 oder 500) und was dagegen zu tun ist.
keywords: security.log, Anfrageklassifizierung, Log-Viewer, Sondierungen, Injection, Traversal, Scanner, Schwellenwert, wöchentliche Prüfung, exp:logs, request-shield, öffentlicher Sektor, request classifier, log viewer, probes, injection, traversal, scanner, threshold, weekly review, public sector
type: help
---
# Das Sicherheits-Log prüfen

Für Administratoren und Hosting-Teams, wöchentlich. Der Anfrageklassifizierer schreibt jede Anfrage, die er für eine
Sondierung oder einen Angriff hält, maskiert in `var/log/security.log`; er blockiert nichts
([Anfrage-Logs und das Sicherheits-Log](../../../../../eng-us/help/government/security/request-logs-and-the-security-log.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was Handeln erfordert](#was-handeln-erfordert)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Richtlinie `setup/logs`; die Maskierung bleibt eingeschaltet (das Aufheben der Maskierung braucht
  `setup/unmasklogs` und wird aufgezeichnet).
- Die Notiz der Vorwoche, um die Mengen zu vergleichen.

## Schritte

1. **Setup > Logdateien** > „Sicherheit (Anfrageklassifizierung)“ (`/setup/logs/view/security`).
2. Sehen Sie sich die Zahlen je Regel an (`<category>/<rule>`, zum Beispiel `probe/wp-login`, `injection/sql-union`,
   `traversal/...`, `threshold/404-rate`) und vergleichen Sie mit der Vorwoche.
3. Filtern oder suchen Sie nach Zeilen, deren Status nicht 404 ist: `200` und `500` bei Zeilen `injection/*` und
   `traversal/*`.
4. Notieren Sie für jede solche Zeile URL, Siteaccess und Zeit; öffnen Sie die passenden Audit-Einträge oder Zeilen des
   Fehler-Logs um diese Zeit.
5. Schreiben Sie die wöchentliche Notiz: Mengen, alles Untersuchte, Maßnahmen.

## Was Handeln erfordert

| Sie sehen | Tun |
|---|---|
| einen gleichmäßigen Strom von `probe/*` mit 404 | normales Rauschen aus dem Internet; nichts |
| einen plötzlichen Anstieg einer Regel | prüfen, ob ein einzelnes Netz ihn verursacht; eine Regel in request-shield oder im Webserver erwägen |
| `injection/*` mit 200 oder 500 | das Modul oder die Erweiterung prüfen, die geantwortet hat; bei eigenem Code ihn überprüfen; als Vorfall behandeln, wenn Daten offengelegt sein könnten |
| `traversal/*` mit 200 | prüfen, dass keine Datei außerhalb des Document Root ausgeliefert wurde; die Prüfung der Dateikonsistenz ausführen |
| `abuse/csp-report-flood` | jemand flutet den CSP-Sammler; die Begrenzung greift, sonst ist nichts nötig |

## Verwandte Seiten

- [Anfrage-Logs](../../../../../eng-us/features/6.0/request-logs.md)
- [Routine zur Prüfung der Logs](../../../../../eng-us/help/government/security/log-review-routine.md)
- [Sicherheits-Log für Hoster](../../../hosting/betrieb/sicherheitslog.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
