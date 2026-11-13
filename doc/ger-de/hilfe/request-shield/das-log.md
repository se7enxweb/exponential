---
title: "Request Shield: das Protokoll"
audience: admin, hosting
category: administration
order: 30
summary: Die Seite Protokoll unter Setup > Request Shield - die Zeiträume, die Entscheidungsgruppen, die Spalten, das Netz des Clients statt der Adresse einer Person, Ansichtsparameter für einen Link auf eine Ansicht und wo die Protokolldatei liegt.
keywords: Request Shield, Log, Protokoll, requestshield/log, shield.log, var/request-shield/log, Entscheidung, abgewiesen, Browserprüfung, beobachtet, Monitor, sonstige, Zeitraum, hours, group, offset, Client, Netz, maskiert, log-ip, Regel-ID, Status, LogHours, LogPageSize, decision, refused, watched, client, network, masked
type: help
---
# Request Shield: das Protokoll

**Setup > Request Shield > Protokoll** (`/requestshield/log`) listet, was der Shield in sein Protokoll geschrieben
hat: jede Anfrage, die er abgewiesen, zur Browserprüfung geschickt oder im Beobachtungsmodus abgewiesen hätte.
Neueste zuerst. Sie brauchen die Richtlinie `requestshield/read`.

## Inhalt

1. [Auswählen, was Sie sehen](#auswählen-was-sie-sehen)
2. [Die Spalten](#die-spalten)
3. [Ein Link auf eine Ansicht](#ein-link-auf-eine-ansicht)
4. [Die Protokolldatei](#die-protokolldatei)
5. [Einstellungen der Seite Protokoll](#einstellungen-der-seite-protokoll)

## Auswählen, was Sie sehen

- **Zeitraum**: die letzten 1, 6, 24, 72 oder 168 Stunden.
- **Entscheidung**: **Alle**, **Abgewiesen**, **Browserprüfung**, **Beobachtet (Monitor)** oder **Sonstige**, jeweils
  mit ihrer Anzahl.
- **Neuere** und **Ältere** blättern durch die Zeilen, 50 auf einmal.

## Die Spalten

| Spalte | Was sie zeigt |
|---|---|
| **Zeit** | wann der Shield entschieden hat |
| **Client** | das Netz des Clients, wie das Protokoll es geschrieben hat |
| **Anfrage** | Methode, Host und Pfad |
| **Entscheidung** | was der Shield getan hat (abgewiesen, Browserprüfung, im Beobachtungsmodus eine Entscheidung `monitor-...` ...) |
| **Status** | der HTTP-Status, den der Client erhalten hat |
| **Regel und Grund** | die Regel-ID, die entschieden hat (`EXP-SYSVIEW`, `EXP-LOGIN`, `SITE-DASH` ...), und warum |

Die Spalte **Client** zeigt die Adresse so, wie das Protokoll sie geschrieben hat: standardmäßig ihr Netz (IPv4 /24,
IPv6 /48, `set log-ip masked` der Bibliothek), nie die vollständige Adresse einer Person.

Die Regel-ID ist der Faden, an dem Sie ziehen. Sie sagt der Person, die die Regeln betreut, welche Zeile entschieden
hat; `trace` auf dem Server zeigt dieselbe Entscheidung Schritt für Schritt ([Die Regeln](die-regeln.md)).

## Ein Link auf eine Ansicht

Jede Auswahl ist ein Link mit Ansichtsparametern, sodass Sie einer Kollegin oder einem Kollegen genau das schicken
können, was Sie sehen:

```
/requestshield/log/(hours)/72/(group)/refused/(offset)/50
```

## Die Protokolldatei

Der Shield schreibt `var/request-shield/log/shield.log` (mit den mitgelieferten Regeln), eine Zeile pro Entscheidung,
rotiert bei `log-max-size` der Bibliothek. Es ist eine einfache Datei auf dem Server; `tail -f` verfolgt sie. Meldet
die Seite, dass das Protokoll nicht existiert oder nicht gelesen werden kann, nennen die Regeln keine
Protokolldatei, oder der Webserver kann sie nicht lesen ([Fehlersuche](fehlersuche.md)).

Die Aufzeichnung, wer die Schalter benutzt hat, ist eine andere Datei, `var/log/requestshield.log`, zu sehen unter
**Setup > Logdateien**.

## Einstellungen der Seite Protokoll

In `settings/override/requestshield.ini.append.php`, `[AdminSettings]`:

| Einstellung | Vorgabe | Bedeutung |
|---|---|---|
| `LogHours[]` | 1, 6, 24, 72, 168 | die wählbaren Zeiträume |
| `LogPageSize` | 50 | Zeilen pro Seite |
