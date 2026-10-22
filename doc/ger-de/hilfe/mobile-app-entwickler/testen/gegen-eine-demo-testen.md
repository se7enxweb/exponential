---
title: Gegen eine Demo testen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 810
summary: Jede Exponential-Installation, auch eine öffentliche Demo, mit drei anonymen Anfragen prüfen, bevor Sie dagegen entwickeln - was die Antworten bedeuten, was eine Demo im Demomodus erlaubt, warum auf einer öffentlichen Demo nicht jede API aktiv sein muss und was Sie auf einer gemeinsam genutzten Demo nie tun sollten.
keywords: Demo, öffentliche Demo, prüfen, Demomodus, EXP_DEMO_MODE, REST 401, 404, expsession::whoami, ezjsc::time, demo.exponential.earth, gemeinsam genutzte Demo, Testen, Mobile App, public demo, probe, demo mode, shared demo, testing, mobile app
type: help
---
# Gegen eine Demo testen

Diese Seite richtet sich an App-Entwickler, die die APIs ausprobieren möchten, bevor sie eine eigene Installation
haben, und an alle, die eine App zum ersten Mal auf einen Server richten. Sie beginnt mit einer Prüfung, die auf jeder
Installation funktioniert.

## Inhalt

1. [Erst prüfen, dann bauen](#erst-prüfen-dann-bauen)
2. [Was die Antworten bedeuten](#was-die-antworten-bedeuten)
3. [Was eine öffentliche Demo erlaubt](#was-eine-öffentliche-demo-erlaubt)
4. [Regeln für gemeinsam genutzte Demos](#regeln-für-gemeinsam-genutzte-demos)

## Erst prüfen, dann bauen

Drei anonyme Anfragen, ohne Zugangsdaten:

```bash
# 1. the REST content API
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/api/ezp/v1/content/node/2
# 2. ezjscore's call interface
curl -s 'https://example.com/ezjscore/call/ezjsc::time?ContentType=json'
# 3. the remote services
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

## Was die Antworten bedeuten

| Prüfung | Antwort | Bedeutung |
|---|---|---|
| 1 | `401` mit `WWW-Authenticate: OAuth realm='Exponential REST'` | REST ist aktiv und verlangt Zugangsdaten (die Voreinstellung) |
| 1 | `404` | `ezprestapi` ist nicht aktiv, oder der Pfad weicht ab |
| 1 | `200` | REST ist aktiv, die Authentifizierung ist ausgeschaltet |
| 2 | `{"error_text":"","content":...}` | ezjscore antwortet |
| 3 | ein Umschlag mit `"anonymous":true` | `expservices` ist aktiv |
| 3 | `error_text` „Not a valid ezjscServerRouter argument“ | `expservices` ist dort nicht aktiv |

Zwei Beispiele, wie das am 20. Oktober 2026 aussah: Der in den Leitfäden durchgehend verwendete
Demonstrationsserver beantwortete Prüfung 1 mit `401` und Prüfung 3 mit dem anonymen Umschlag; die öffentliche Demo
unter `https://demo.exponential.earth/` beantwortete Prüfung 1 mit `404` und Prüfung 3 mit „Not a valid
ezjscServerRouter argument“. Installationen ändern sich; führen Sie die Prüfung deshalb selbst aus, statt sich auf
diese Zeilen zu verlassen.

## Was eine öffentliche Demo erlaubt

Eine öffentliche Demonstrations-Website veröffentlicht eine Administratoranmeldung, damit jeder die
Administrationsoberfläche ausprobieren kann. Eine solche Demo soll im **Demomodus** laufen (`EXP_DEMO_MODE=1`):
Niemand kann das Passwort eines Administrators ändern oder zurücksetzen, und auch die Webdienste lehnen das mit `403`
ab; alles andere funktioniert wie auf jeder Website ([Demomodus](../../demomodus.md),
[Demomodus-FAQ](../../demomodus-faq.md)). Demos werden nach Zeitplan von Grund auf neu aufgebaut, daher verschwindet
alles, was Sie dort anlegen ([Eine Demo-Website betreiben](../../designer/veroeffentlichung/demo-websites.md)).

Was das für die Arbeit an einer App bedeutet:

- Gut geeignet, um Inhalte zu lesen und die Administrationsoberfläche zu erkunden (Klassen, Rollen, die Seiten zur
  Verwaltung von API-Schlüsseln und oAuth).
- Kein Ort für Schlüssel oder OAuth-Clients, auf die Sie sich verlassen: Sie verschwinden beim nächsten Neuaufbau, und
  jeder mit der veröffentlichten Anmeldung kann die oAuth-Verwaltungsseiten sehen.
- Knoten-IDs können nach einem Neuaufbau abweichen; übernehmen Sie sie nie fest aus einer Demo in den Code.

## Regeln für gemeinsam genutzte Demos

- Verwenden Sie Wegwerfkonten und Wegwerfschlüssel; widerrufen Sie, was Sie angelegt haben, wenn Sie fertig sind.
- Führen Sie keine Lasttests gegen eine öffentliche Demo durch.
- Speichern Sie dort keine personenbezogenen Daten.
- Richten Sie keine veröffentlichte App auf eine Demo.

Für alles, was über einen ersten Blick hinausgeht, installieren Sie eine eigene: [Eine lokale Testinstallation](eine-lokale-testinstallation.md).

## Verwandte Seiten

- [Demomodus-FAQ](../../demomodus-faq.md)
- [Ihre erste Anfrage](../rest-api/ihre-erste-anfrage.md)
- [Apps gegen Exponential testen](README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
