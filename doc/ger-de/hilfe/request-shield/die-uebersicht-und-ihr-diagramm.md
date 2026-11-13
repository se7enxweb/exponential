---
title: "Request Shield: die Übersicht und ihr Diagramm"
audience: admin, hosting
category: administration
order: 10
summary: Die Übersicht unter Setup > Request Shield - die Hinweise, die Kennzahlen, das Diagramm der Entscheidungen pro Stunde, der Kasten Einrichtung mit Version der Bibliothek, Regeldatei und Speicher, die Regeln mit den meisten Entscheidungen und die neuesten Protokollzeilen - und wie Sie jeden Teil lesen.
keywords: Request Shield, Übersicht, Dashboard, requestshield/dashboard, Diagramm, Entscheidungen pro Stunde, abgewiesen, Browserprüfung, beobachtet, Monitor, Kennzahlen, Hinweise, pausiert, nicht gelaufen, Regeln können nicht gelesen werden, Speicher, Stufe, S0, S1, S2, APCu, Version der Bibliothek, Hauptregeldatei, DashboardHours, DashboardRecent, requestshield.ini, chart, decisions per hour, store, tier
type: help
---
# Request Shield: die Übersicht und ihr Diagramm

**Setup > Request Shield** öffnet die Übersicht (`/requestshield/dashboard`). Sie beantwortet drei Fragen auf einen
Blick: Läuft der Shield, in welchem Modus, und was hat er zuletzt entschieden. Sie brauchen die Richtlinie
`requestshield/read`.

## Inhalt

1. [Die Schalter](#die-schalter)
2. [Hinweise](#hinweise)
3. [Die Kennzahlen](#die-kennzahlen)
4. [Entscheidungen pro Stunde](#entscheidungen-pro-stunde)
5. [Einrichtung](#einrichtung)
6. [Regeln mit den meisten Entscheidungen](#regeln-mit-den-meisten-entscheidungen)
7. [Neueste Protokollzeilen](#neueste-protokollzeilen)
8. [Einstellungen der Übersicht](#einstellungen-der-übersicht)

## Die Schalter

Oben, für Benutzer mit `requestshield/manage`: **Pausieren** oder **Einschalten** und die Modi **Durchsetzen**,
**Beobachten** und **Aus**. [Die Schalter](die-schalter.md) erklärt jeden davon.

## Hinweise

Ein Hinweis über den Kennzahlen sagt Ihnen, wenn etwas Aufmerksamkeit braucht:

| Hinweis | Bedeutung | Was zu tun ist |
|---|---|---|
| **Die Regeln können nicht gelesen werden** | eine Regeldatei enthält einen Fehler; bis er behoben ist, lässt der Shield jede Anfrage durch | öffnen Sie **Regeln** oder führen Sie `bin/request-shield check` aus ([Die Regeln](die-regeln.md)) |
| **Der Shield ist pausiert** | die Datei `var/request-shield/PAUSED` existiert | **Einschalten** oder die Datei entfernen |
| **Der Shield lief für diese Seite nicht** | `config.php` lädt den Hook der Extension nicht, oder dieser Server führt ihn nicht aus (unter Velocity: seit dem Hinzufügen des Hooks nicht neu gestartet) | den Hook hinzufügen; Velocity neu starten ([Fehlersuche](fehlersuche.md)) |
| **Beobachtungsmodus** | jede Entscheidung wird so protokolliert, wie sie fiele, und niemand wird abgewiesen | zurück zu **Durchsetzen**, wenn das Protokoll stimmt ([Beobachten und durchsetzen](beobachten-und-durchsetzen.md)) |

Kein Hinweis bedeutet: Der Shield ist für die Seite gelaufen, die Sie gerade sehen, mit lesbaren Regeln, im Modus
enforce.

## Die Kennzahlen

Für den Zeitraum der Übersicht (standardmäßig 24 Stunden): der **Modus**, die **Protokollzeilen** des Zeitraums und
wie viele davon **Abgewiesen**, zur **Browserprüfung** geschickt, **Beobachtet (Monitor)** oder **Sonstige** waren.

Mit dem mitgelieferten `set log-level flag` schreibt der Shield nur, was er abweist, prüft oder abweisen würde; die
Protokollzeilen sind also die Anfragen, auf die er reagiert hat, nicht alle Anfragen der Website.

## Entscheidungen pro Stunde

Ein gestapelter Balken pro Stunde des Zeitraums, eine Farbe pro Entscheidungsgruppe (abgewiesen, Browserprüfung,
beobachtet, sonstige). Fahren Sie mit der Maus über einen Balken oder fokussieren Sie ihn, um Stunde und Summe zu
sehen; die Beschriftung nennt den Höchstwert einer Stunde. Das Diagramm liest das Protokoll des Shields und
funktioniert deshalb auch ohne die Statistik der Bibliothek (`set stats off`).

So lesen Sie es:

- **Ein gleichmäßig niedriges Band** von Abweisungen ist normal: Scanner und Bots probieren jede öffentliche Website.
- **Ein plötzlich hoher Balken** ist ein Scan oder eine Anfrageflut, die der Shield abgewiesen hat. Öffnen Sie das
  Protokoll für diesen Zeitraum, um Clients und Regeln zu sehen.
- **Viele Browserprüfungen** nach einer Änderung bedeuten, dass eine Regel zu oft fragt; sehen Sie sich die Regeln
  unter **Regeln mit den meisten Entscheidungen** an.
- **Ein leeres Diagramm** bedeutet keine Protokollzeilen: Die Regeln nennen keine Protokolldatei, oder `log-level`
  ist `off`.

## Einrichtung

Womit der Shield läuft, gelesen über die eigenen Klassen der Bibliothek, sodass Sie sehen, was er tatsächlich
entscheidet:

- Version und Build der **Bibliothek** (request-shield 0.4.0, Build `v0.4.0`) und die Version der **Erweiterung**;
- die **Hauptregeldatei** und woher sie stammt: die eigene Datei der Website in `settings/request-shield/`, eine in
  `config.php` genannte Datei (`EXP_REQUEST_SHIELD_RULES`) oder die Standarddatei der Erweiterung (noch keine Datei
  der Website);
- der **Speicher** und seine Stufe: S0 zustandslos, S1 Dateien, S2 APCu, mit dem, was abgeschaltet ist, und warum;
- die **Protokolldatei**, die **Hostnamen** und die **geladenen Teile** (der Kern, Statistik, API, Firewall-Seiten,
  HTTP-Cache).

## Regeln mit den meisten Entscheidungen

Die Regel-IDs, die im Zeitraum am häufigsten entschieden haben, mit der Zeit ihrer letzten Entscheidung. Regeln
`EXP-...` stammen aus den mitgelieferten Exponential-Regeln, Regeln `SITE-...` aus der eigenen Datei der Website.
Eine Regel, die Sie dort oben nicht erwarten, ist nach einer Beschwerde das Erste, was Sie sich ansehen.

## Neueste Protokollzeilen

Die neuesten Zeilen (standardmäßig 15), mit einem Link auf das ganze Protokoll. [Das Protokoll](das-log.md) erklärt die
Spalten.

## Einstellungen der Übersicht

In `settings/override/requestshield.ini.append.php`, `[AdminSettings]`:

| Einstellung | Vorgabe | Bedeutung |
|---|---|---|
| `DashboardHours` | 24 | Stunden, die die Übersicht zusammenfasst und zeichnet (1 bis 168) |
| `DashboardRecent` | 15 | neueste Protokollzeilen auf der Übersicht |

Diese Einstellungen ändern nur die Seiten der Administration. Der Shield selbst wird in seinen Regeldateien
konfiguriert.
