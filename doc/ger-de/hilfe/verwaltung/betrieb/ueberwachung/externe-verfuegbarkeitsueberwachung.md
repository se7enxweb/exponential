---
title: Externe Verfügbarkeitsüberwachung
audience: government, admin, hosting, project-manager
category: public-sector
order: 120
summary: Eine öffentliche Website von außen prüfen, so wie Bürgerinnen und Bürger sie erreichen - welche Adressen geprüft werden, was als gesund gilt, wie Wartungsfenster für eine Überwachung aussehen, Ablauf von Zertifikaten und eine ehrliche Berichterstattung über die Verfügbarkeit.
keywords: Verfügbarkeitsüberwachung, Verfügbarkeit, externe Prüfungen, synthetische Überwachung, Statuscode, Antwortzeit, Wartung 503, Ablauf von Zertifikaten, Statusseite, Verfügbarkeitsbericht, SLA, uptime monitoring, availability, external checks, synthetic monitoring, status code, response time, maintenance 503, certificate expiry, status page, availability report
type: help
---
# Externe Verfügbarkeitsüberwachung

Diese Seite richtet sich an IT-Teams und die Verantwortlichen für den Dienst, die wissen und oft auch berichten müssen,
ob eine öffentliche Website verfügbar ist. Exponential enthält keine Überwachung von außen; jeder Überwachungsdienst
und jedes Werkzeug, das eine URL abrufen kann, funktioniert. Diese Seite beschreibt, was zu prüfen ist, auf Grundlage
von [Zustandsprüfungen und Verfügbarkeit (Hosting)](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md).

## Inhalt
1. [Was Sie prüfen sollten](#was-sie-prüfen-sollten)
2. [Wartungsfenster](#wartungsfenster)
3. [Zertifikate](#zertifikate)
4. [Wo die Prüfungen laufen](#wo-die-prüfungen-laufen)
5. [Über die Verfügbarkeit berichten](#über-die-verfügbarkeit-berichten)
6. [Verwandte Seiten](#verwandte-seiten)

## Was Sie prüfen sollten

| Prüfung | Beispiel | Gesund |
|---|---|---|
| Öffentliche Startseite über HTTPS | `curl -s -o /dev/null -w '%{http_code} %{time_total}\n' https://www.example.gov/` | `200`, Zeit in Ihrem üblichen Bereich |
| Anmeldeseite der Administration | `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.gov/admin/user/login` | `200` |
| Ein Wort, das nur die gerenderte Seite enthält | der Inhaltsabgleich Ihrer Überwachung | gefunden |
| Eine wichtige Dienstseite (ein Formular, eine Suche) | ihre Adresse | `200` |

Die Startseite wird oft aus einem Cache ausgeliefert (dem Antwortcache von Velocity, dem HTTP-Cache, einem CDN) und
kann daher antworten, während die Datenbank ausgefallen ist; die Anmeldeseite und ein Inhaltsabgleich erkennen das.

## Wartungsfenster

Im Wartungsmodus antwortet jede Seite mit `503 Service Unavailable` mit `Retry-After` und `noindex`. Pausieren Sie
entweder die Überwachung für das angekündigte Fenster, oder akzeptieren Sie ein 503 mit `Retry-After`, solange
`php bin/php/maintenance.php status` meldet, dass die Wartung eingeschaltet ist, und lösen Sie eine Warnung aus, wenn das
Fenster länger dauert als angekündigt: Die Wartung endet nie von selbst
([Wartungsfenster](../aktualisierungen-und-aenderungen/wartungsfenster.md)). Statische Dateien werden weiterhin ausgeliefert,
eine Prüfung auf ein Bild bleibt während der Wartung also grün.

## Zertifikate

Richten Sie die Überwachung auf die Adresse, die Besucher verwenden, mit HTTPS, damit ein ablaufendes Zertifikat oder
eine fehlerhafte Weiterleitung eine Warnung auslöst. Unter Velocity gibt `qbixconsole ssl:show` das verwendete
Zertifikat aus; Zertifikate von Let's Encrypt erneuert der Server selbst
([Velocity: HTTPS und Zertifikate](../../../../funktionen/6.0/velocity-https-zertifikate.md)). Siehe
[Ablauf von Zertifikaten](../vorfaelle/ablauf-von-zertifikaten.md).

## Wo die Prüfungen laufen

Von außerhalb Ihres eigenen Netzes (Bürgerinnen und Bürger sind draußen) und von mindestens zwei Orten aus, damit ein
einzelnes Netzwerkproblem nicht wie ein Ausfall aussieht. Bei mehreren Webservern oder Engines ergänzen Sie eine
Prüfung je Knoten oder Port, wie es die Hosting-Seite empfiehlt.

## Über die Verfügbarkeit berichten

Wenn Ihre Organisation über die Verfügbarkeit berichtet (an die Leitung, in einer Service-Level-Vereinbarung oder
öffentlich), legen Sie schriftlich fest, was als „verfügbar“ gilt (welche Prüfungen, von wo, wie oft), ob angekündigte
Wartung mitzählt und wie Vorfälle eingestuft werden. Die Kennzahlen sind eine organisatorische Entscheidung;
Exponential liefert keinen eigenen Verfügbarkeitsprozentsatz
([Verfügbarkeitsziele und Service-Level](../verfuegbarkeit/verfuegbarkeitsziele-und-service-level.md)).

## Verwandte Seiten
- [Website nicht erreichbar](../vorfaelle/website-nicht-erreichbar.md)
- [Überwachung im Überblick](ueberwachung-im-ueberblick.md)
- [Sicherheits-Header prüfen](../../aufgaben/betrieb/die-sicherheitsheader-pruefen.md)
- [Mehrere Webserver hinter einem Load Balancer](../verfuegbarkeit/mehrere-webserver-hinter-einem-load-balancer.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
