---
title: Logs rotieren und bereinigen
audience: government, admin, hosting
category: public-sector
order: 150
summary: Schritt für Schritt - die Archive und Grenzen jedes Logs sehen, Logs jetzt rotieren, Archive jenseits der Grenzen bereinigen, sie komprimieren und das mit dem Teil logs der Cronjobs planen, immer zuerst mit einem Probelauf.
keywords: Rotation der Logs, Logs bereinigen, Logs komprimieren, exp:logs, --archives, --rotate-all, --prune, --dry-run, Cronjob logs, logrotate, Aufbewahrung, log rotation, prune logs, compress logs, logs cronjob, retention
type: help
---
# Logs rotieren und bereinigen

Diese Aufgabe ist für Administratoren, die Speicherplatz und Aufbewahrung verwalten. Jeder Vorgang wird zuerst geplant
(`--dry-run`) und dann Schritt für Schritt ausgeführt. Referenz:
[Rotation und Bereinigung der Logs](../../../../funktionen/6.0/log-rotation.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Es planen](#es-planen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang als der Benutzer, dem die Logs gehören (oder root; neue Logs behalten ihren Eigentümer).
- Kennen Sie Ihre Aufbewahrungsregeln
  ([Aufbewahrung und Rotation der Logs](../../betrieb/ueberwachung/aufbewahrung-und-rotation-von-protokollen.md)). Audit-Einträge
  werden hiervon nicht berührt; das Audit-Protokoll hat seine eigene Aufbewahrung.

## Schritte

```bash
./console exp:logs --archives                    # each log, its archives, sizes and limits
./console exp:logs --rotate-all --dry-run        # what would be rotated
./console exp:logs --apply --rotate-all                  # rotate the logs over their MaxSize
./console exp:logs --prune --dry-run             # archives beyond Keep and MaxAgeDays
./console exp:logs --apply --prune                       # remove them
./console exp:logs --apply --compress                    # compress plain archives from .2 on
```

Nennen Sie Logs über ihren Schlüssel (`error`, `debug`, `<site>-storage`), um nur auf einige zu wirken:
`--rotate=error,debug`, `--prune=error`. Exit-Codes: 0 erledigt, 1 nichts zu tun, 2 ein unbekannter Name oder ein
Fehler. Im Browser erscheint **Jetzt rotieren** an einem Log nur, wenn `logviewer.ini [ActionSettings] Rotate=enabled`
gesetzt ist und Sie `setup/managelogs` haben.

## Es planen

Der Teil `logs` der Cronjobs steht in keinem Standardteil; führen Sie ihn nachts als Benutzer der Site aus:

```
15 3 * * * cd /path/to/installation && php runcronjobs.php -q logs
```

Wenn stattdessen das `logrotate` des Systems `var/log` verwaltet, setzen Sie `site.ini [FileSettings]
LogFileRotateCount=0`, damit nicht beide rotieren.

## Prüfen, ob es funktioniert hat

- `./console exp:logs --archives` zeigt die erwarteten Archive.
- **Zustand der Logs** zeigt eine Speicherbelegung im Budget.
- Das Audit-Protokoll zeichnet `system.log.rotate` und `system.log.delete` auf.

## Verwandte Seiten
- [Den Zustand der Logs prüfen](den-zustand-der-logs-pruefen.md)
- [Datenträger voll](../../betrieb/vorfaelle/festplatte-voll.md)
- [Aufbewahrung von Logs und Daten (Hosting)](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)
- [Log-Viewer und Rotation (Hosting)](../../../hosting/betrieb/log-viewer-und-log-rotation.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
