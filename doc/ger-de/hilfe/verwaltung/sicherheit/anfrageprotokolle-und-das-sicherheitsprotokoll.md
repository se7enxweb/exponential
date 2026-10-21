---
title: Anfrageprotokolle und das Sicherheitsprotokoll
audience: government, admin, hosting
category: public-sector
order: 150
summary: Für Administratorinnen und Administratoren öffentlicher Websites - was Exponential in 404.log und security.log schreibt (Sondierungen, Traversal, Injection, Scanner, 404-Häufungen), wie Adressen und Abfragewerte maskiert werden, wie lange die Protokolle aufbewahrt werden und wie Sie sie unter Setup > Logdateien lesen.
keywords: Sicherheitsprotokoll, Anfrageklassifizierung, Sondierungen, Traversal, Injection, Scanner, Schwellenwert, Maskierung, Protokollansicht, Protokollrotation, Überwachung, öffentlicher Sektor, security.log, 404.log, request classifier, probes, traversal, injection, scanner, threshold, masking, MaskIP, log viewer, log rotation, MaxAgeDays, requestclassifier.ini, monitoring, public sector
type: help
---
# Anfrageprotokolle und das Sicherheitsprotokoll

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. Exponential trennt echte
Fehler vom Rauschen: Antworten „nicht gefunden“ gehen nach `404.log`, Anfragen, die die Klassifizierung für eine
Sondierung oder einen Angriff hält, gehen nach `security.log`, und `error.log` behält die echten Fehler
([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)). Für eine öffentliche Website erhalten Sie so ein ruhiges
Fehlerprotokoll und eine eigene, lesbare Aufzeichnung der Angriffe.

## Inhalt

1. [Die Protokolle](#die-protokolle)
2. [Eine Zeile von security.log lesen](#eine-zeile-von-securitylog-lesen)
3. [Eingebauter Datenschutz](#eingebauter-datenschutz)
4. [Aufbewahrung](#aufbewahrung)
5. [Auf das Gesehene reagieren](#auf-das-gesehene-reagieren)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Protokolle

| Protokoll | Enthält | Bezeichnung in der Protokollansicht |
|---|---|---|
| `error.log` | nur echte Fehler | Fehler |
| `404.log` | Antworten 404 und 410 mit dem Grund, den der Kernel gefunden hat | „Nicht gefundene Anfragen (404)“ |
| `notice.log` | Antworten 401 und 403 | Hinweis |
| `security.log` | Sondierungen, Traversal, Injection, Angriffswerkzeuge, 404-Häufungen, mit jedem Status | „Sicherheit (Anfrageklassifizierung)“ |

Jede Anfrage wird einmal klassifiziert und landet nur in einem Protokoll. **Nichts wird blockiert**: Die
Klassifizierung klassifiziert nur. Blockieren ist Aufgabe von request-shield, des Webservers oder eines Listeners in
`requestclassifier.ini [ClassifierSettings] Listeners[]`.

## Eine Zeile von security.log lesen

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] probe/wp-login confidence=high 404 GET /wp-login.php siteaccess=site namespace=/wp-login.php ua=3f2a9c1b "curl/8.5.0"
```

| Teil | Bedeutet |
|---|---|
| `probe/wp-login` | Kategorie und Regel: `probe`, `traversal`, `injection`, `scanner`, `threshold` |
| `confidence=high` | low, medium oder high |
| `404` | der endgültige Status; ein `200` bei einem Injection-Versuch verdient einen genaueren Blick |
| `GET /wp-login.php` | Methode und URI; Abfragewerte als Beweis behalten, Geheimnisse und Adressen darin maskiert |
| `siteaccess=`, `namespace=` | wo die Anfrage ankam |
| `ua=` | 8 Hexadezimalstellen des Hashes des User-Agents und seine ersten 60 Zeichen |

## Eingebauter Datenschutz

- Client-Adressen maskiert (`203.0.x.x`) mit `site.ini [RequestLogSettings] MaskIP=enabled` (der Standard); die
  unveränderte Adresse wird nie in eine Datei geschrieben.
- In `404.log` wird jeder Abfragewert durch `***` ersetzt.
- Die Protokollansicht maskiert E-Mail-Adressen, IP-Adressen und Geheimnisse standardmäßig; **Unmaskiert zeigen**
  benötigt `setup/unmasklogs` und wird selbst aufgezeichnet
  ([Protokollansicht: Datenschutz](../../../../eng-us/features/6.0/log-viewer.md#privacy-what-is-masked)).

## Aufbewahrung

`404.log` und `security.log` behalten 5 komprimierte Archive von bis zu 10 MB, gelöscht nach 30 Tagen (404) und 90
Tagen (security) ([Protokollrotation](../../../../eng-us/features/6.0/log-rotation.md)). Ändern Sie das je Protokoll in
`settings/override/logviewer.ini.append.php`, `[RotationSettings] MaxAgeDays[security]=<days>`. Das Löschen nach Alter
läuft mit `./console exp:logs --prune` oder dem Cronjob-Teil `logs` (`php runcronjobs.php logs`).

## Auf das Gesehene reagieren

- Ein stetiger Strom von `probe/*` mit 404 ist normales Rauschen im Internet; achten Sie auf Änderungen der Menge.
- `injection/*` oder `traversal/*` mit Status 200 oder 500: Prüfen Sie die URL, das Modul und jede eigene Erweiterung,
  die sie verarbeitet.
- Viele `threshold/404-rate` aus einem Netz: Erwägen Sie eine Regel in request-shield oder im Webserver.
- Lesen Sie die wöchentliche Routine unter „Routine zur Protokollprüfung“ in diesem Bereich.

## Verwandte Seiten

- [Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)
- [Die Protokollansicht](../../../../eng-us/features/6.0/log-viewer.md)
- [request-shield vor einer öffentlichen Website](request-shield.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
