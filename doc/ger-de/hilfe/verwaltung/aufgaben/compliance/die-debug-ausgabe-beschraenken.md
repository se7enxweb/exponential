---
title: Die Debug-Ausgabe beschränken
audience: government, admin, developer
category: public-sector
order: 350
summary: Schritt für Schritt - sicherstellen, dass die Debug-Ausgabe von Exponential in der Produktion ausgeschaltet ist, und wenn ein Live-Problem sie braucht, sie nur für Ihre eigene Adresse einschalten, sie dann wieder entfernen und prüfen, dass Besuchern nichts angezeigt wird.
keywords: Debug-Ausgabe, DebugOutput, DebugByIP, DebugIPList, Template-Debug, Offenlegung von Informationen, Produktion, display_errors, Audit system.setting.write, öffentlicher Sektor, debug output, template debug, information disclosure, production, public sector
type: help
---
# Die Debug-Ausgabe beschränken

Für Administratoren und Entwickler. Die Debug-Ausgabe zeigt SQL, Dateipfade und Template-Namen: niemals für Besucher
einer öffentlichen Site ([Debug-Ausgabe in der Produktion](../../../../../eng-us/help/government/security/debug-output-in-production.md)).

## Inhalt

1. [Prüfen, ob sie ausgeschaltet ist](#prüfen-ob-sie-ausgeschaltet-ist)
2. [Debug nur für Ihre Adresse](#debug-nur-für-ihre-adresse)
3. [Sie wieder entfernen](#sie-wieder-entfernen)
4. [Verwandte Seiten](#verwandte-seiten)

## Prüfen, ob sie ausgeschaltet ist

```bash
./console exp:ini get site.ini/DebugSettings/DebugOutput --allow-root-user        # disabled
./console exp:ini get site.ini/TemplateSettings/Debug --allow-root-user           # disabled
./console exp:ini where site.ini/DebugSettings/DebugOutput --allow-root-user      # which files set it
```

Prüfen Sie auch PHP: `display_errors=Off` und `expose_php=Off` für PHP-FPM.

## Debug nur für Ihre Adresse

1. In `settings/override/site.ini.append.php`:

   ```ini
   [DebugSettings]
   DebugOutput=enabled
   DebugByIP=enabled
   DebugIPList[]
   DebugIPList[]=203.0.113.10
   ```

   Hinter einem Proxy verwendet `DebugByIP` die Adresse des Besuchers nur von vertrauenswürdigen Proxys; tragen Sie
   niemals die eigene Adresse des Proxys ein.
2. Leeren Sie den INI-Cache und laden Sie PHP neu.
3. Stellen Sie das Problem nach; erwägen Sie stattdessen, den Fehler über seine Referenz im Log-Viewer zu finden
   ([Einen Fehler anhand seiner Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Sie wieder entfernen

Setzen Sie `DebugOutput=disabled` (oder entfernen Sie die Zeilen), leeren Sie den INI-Cache, laden Sie neu. Dann aus
einem anderen Netzwerk:

```bash
curl -s https://www.example.gov/ | grep -c -i 'debug'
```

Das Audit zeichnet jedes Schreiben einer Einstellung auf; `/audit/console/(name)/system.setting.write` zeigt vergessene
Debug-Schalter.

## Verwandte Seiten

- [Debug-Ausgabe in der Produktion](../../../../../eng-us/help/government/security/debug-output-in-production.md)
- [Die Debug-Leiste von Exp](../../../../bc/6.0/debug-leiste.md)
- [13.4 Debug-Ausgabe und Anzeige von Fehlern](../../../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
