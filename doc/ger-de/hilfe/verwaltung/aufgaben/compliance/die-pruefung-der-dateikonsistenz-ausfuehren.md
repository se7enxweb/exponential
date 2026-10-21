---
title: Die Prüfung der Dateikonsistenz ausführen
audience: government, admin, hosting
category: public-sector
order: 370
summary: Schritt für Schritt - jede Datei der Installation mit der Prüfsummenliste des Release vergleichen, unter Setup > Upgrade-Prüfung oder auf der Kommandozeile, die Befunde lesen und das Ergebnis nach Updates und Vorfällen ablegen.
keywords: Dateikonsistenz, Upgrade-Prüfung, filelist.md5, verifyfiles.sh, checkmanifest.php, Integrität, geänderte Dateien, fehlende Dateien, nach dem Update, Vorfall, Nachweis, öffentlicher Sektor, file consistency, Upgrade check, integrity, modified files, missing files, after update, incident, evidence, public sector
type: help
---
# Die Prüfung der Dateikonsistenz ausführen

Für Administratoren und Hosting-Teams, nach jedem Update, nach einem Vorfall und als regelmäßige Prüfung
([Prüfung der Dateikonsistenz](../../../../../eng-us/help/government/security/file-consistency-check.md)).

## Inhalt

1. [Schritte in der Administration](#schritte-in-der-administration)
2. [Schritte auf der Kommandozeile](#schritte-auf-der-kommandozeile)
3. [Das Ergebnis lesen](#das-ergebnis-lesen)
4. [Verwandte Seiten](#verwandte-seiten)

## Schritte in der Administration

1. **Setup > Upgrade-Prüfung** (`/setup/systemupgrade`).
2. **Überprüfe Dateikonsistenz**: Die Seite vergleicht die Dateien mit `share/filelist.md5` und den Manifesten aktiver
   Erweiterungen, die eines mitbringen, und gruppiert die Befunde (geändert, fehlend, nicht lesbar, Hinweise).
3. Laden Sie das Ergebnis als CSV oder Text herunter und legen Sie es ab.
4. **Überprüfe Datenbankkonsistenz** vergleicht das Schema mit dem des Release.

## Schritte auf der Kommandozeile

Aus dem Document Root, das der Webserver verwendet:

```bash
bash bin/shell/verifyfiles.sh                 # summary and differing files; exit 0 when all match
php bin/php/checkmanifest.php --all --extensions --csv > var/tmp/consistency-$(date +%F).csv
```

Für das Monitoring: `bash bin/shell/verifyfiles.sh --quiet || <your alert command>`.

## Das Ergebnis lesen

| Befund | Maßnahme |
|---|---|
| nichts weicht ab | das Ergebnis ablegen |
| eine Datei des Kernels geändert, die niemand geändert hat | als mögliche Kompromittierung behandeln; mit dem Release-Tag vergleichen; das Audit um die Zeit der Datei prüfen |
| eine Datei durch einen lokalen Patch geändert | die Änderung in eine Erweiterung oder ein Override verlegen |
| fehlende Dateien | das Update oder Deployment abschließen |

## Verwandte Seiten

- [Prüfung der Dateikonsistenz](../../../../../eng-us/help/government/security/file-consistency-check.md)
- [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md)
- [Reaktion auf Vorfälle](../../../../../eng-us/help/government/security/incident-response.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
