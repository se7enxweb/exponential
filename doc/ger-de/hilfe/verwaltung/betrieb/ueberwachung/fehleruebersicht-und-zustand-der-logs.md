---
title: Fehlerübersicht und Zustand der Logs
audience: government, admin, developer
category: public-sector
order: 40
summary: Die beiden Übersichtsseiten des Log-Viewers - die Fehlerübersicht, die die unterschiedlichen Fehler des letzten Tages je Log gruppiert und zählt, und der Zustand der Logs mit Speicherbelegung, Wachstum, Schreibrechten und Rotationseinstellungen - und wie Sie sie in einer täglichen Prüfung nutzen.
keywords: Fehlerübersicht, Zustand der Logs, Setup Logdateien, unterschiedliche Fehler, Als bekannt markieren, Speicherbudget, Log-Wachstum, beschreibbare Logs, Rotationseinstellungen, tägliche Prüfung, logsdigest, logshealth, error digest, log health, Setup Log files, distinct errors, Mark as known, disk budget, log growth, writable logs, rotation settings, daily check
type: help
---
# Fehlerübersicht und Zustand der Logs

Diese Seite richtet sich an die Administration, die die Logs einer öffentlichen Website jeden Tag prüft, ohne sie Zeile
für Zeile zu lesen. Dafür hat der Log-Viewer zwei Übersichtsseiten: **Fehlerübersicht** (`/setup/logsdigest`) und
**Zustand der Logs** (`/setup/logshealth`), beide unter **Setup > Logdateien** und beide mit der Funktion `setup/logs`
([Log-Viewer](../../../../funktionen/6.0/log-betrachter.md#fehlerübersicht)).

## Inhalt
1. [Die Fehlerübersicht](#die-fehlerübersicht)
2. [Mit der Fehlerübersicht arbeiten](#mit-der-fehlerübersicht-arbeiten)
3. [Zustand der Logs](#zustand-der-logs)
4. [Täglich fünf Minuten](#täglich-fünf-minuten)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Fehlerübersicht

Die Fehlerübersicht listet die unterschiedlichen Fehler und Warnungen der letzten 24 Stunden (`[DigestSettings] Hours`)
jedes Kernel-Logs auf: Jede Meldung wird maskiert, dann normalisiert (Zahlen, IDs, Werte in Anführungszeichen, Pfade und
`ERR-`-Referenzen ausgeklammert), gruppiert und gezählt, die häufigste zuerst, höchstens 50 je Log, mit der höchsten
Stufe und dem Zeitpunkt des letzten Auftretens. Jede Gruppe verweist auf das danach gefilterte Log.

- `/setup/logsdigest/<key>` zeigt ein einzelnes Log; `/(hours)/N` ein anderes Zeitfenster (1 bis 168 Stunden).
- Die Anfrage-Logs werden auf eigene Weise gruppiert: `404.log` nach Grund, `security.log` nach Kategorie und Regel,
  `csp.log` nach Direktive und blockierter Quelle. Die Fehlerübersicht auf dem Demonstrationsserver listet unter
  anderem „Not found requests (404)“, „Security (request classifier)“ und „Content Security Policy reports“.

## Mit der Fehlerübersicht arbeiten

- **Als bekannt markieren** blendet eine Gruppe aus, die Sie angesehen und akzeptiert haben (eine persönliche
  Einstellung, höchstens 200 Gruppen); **Bekannte Meldungen zeigen** listet sie wieder auf. Nutzen Sie das, damit
  neue Probleme auffallen.
- Eine Gruppe, die heute neu ist, sehen Sie sich zuerst an; eine Gruppe, deren Zahl sprunghaft steigt, als Zweites.
- Öffnen Sie den Verweis der Gruppe und dann eine Zeile, um die ganze Anfrage und die Audit-Einträge derselben Minute
  zu sehen.
- Tragen Sie wiederkehrende Fehler mit dem Permalink in Ihr Ticketsystem ein; die Entwicklung behebt Ursachen, der
  Betrieb sollte einen echten Fehler nicht für immer „als bekannt markieren“.

## Zustand der Logs

**Zustand der Logs** zeigt auf einen Blick:

- den von allen Logs und Archiven belegten Speicher im Vergleich zu `[HealthSettings] DiskBudget` (standardmäßig
  256 MB; eine Warnung ab 80 %, ein Alarm darüber);
- die am schnellsten wachsenden Logs und die Logs über ihrer Größengrenze;
- Logs, in die der Benutzer des Webservers nicht schreiben kann, und eine Warnung, wenn Logs gemischte Eigentümer haben
  (zum Beispiel root von der Kommandozeile und der Benutzer des Webservers), was die Rotation und neue Zeilen stoppt;
- den neuesten Fehler jedes Logs, verlinkt;
- die je Log geltenden Rotationseinstellungen, jeweils mit der INI-Datei, dem Abschnitt und der Einstellung, aus der
  sie stammen.

## Täglich fünf Minuten

1. **Fehlerübersicht**, Standardzeitfenster: Ist etwas neu oder wächst?
2. **Zustand der Logs**: Ist etwas rot? Gibt es ein nicht beschreibbares Log?
3. Reiter **Audit**, **Alarme**: Wurde seit gestern etwas ausgelöst?
4. Halten Sie Befunde in Ihrem Betriebsprotokoll oder Ticketsystem fest.

Vorgehen: [Die Fehlerübersicht lesen](../../aufgaben/betrieb/die-fehleruebersicht-lesen.md) und
[Den Zustand der Logs prüfen](../../aufgaben/betrieb/den-zustand-der-logs-pruefen.md).

## Verwandte Seiten
- [Der Log-Viewer](der-log-viewer.md)
- [Aufbewahrung und Rotation von Logs](aufbewahrung-und-rotation-von-protokollen.md)
- [Anfrage-Logs: 404 und Sicherheit](anfrageprotokolle-404-und-sicherheit.md)
- [Festplatte voll](../vorfaelle/festplatte-voll.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
