---
title: Die Suchstatistik einschalten
audience: government, admin
category: public-sector
order: 330
summary: Aufgabe für Administratoren - festhalten, wonach Besucher auf einem öffentlichen Siteaccess suchen (LogSearchStats), Setup > Suchstatistiken lesen und die Suchbegriffe regelmäßig zurücksetzen, weil sie personenbezogene Daten enthalten können.
keywords: Suchstatistik, Suchstatistiken, keine Treffer, Statistik zurücksetzen, Datenschutz, search statistics, LogSearchStats, Setup Search statistics, search/stats, zero results, reset statistics, privacy
type: help
---
# Die Suchstatistik einschalten

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Suchstatistik und Datenschutz](../../aufbauen/suche/suchstatistik-und-datenschutz.md).

## Schritte

1. In jedem öffentlichen Siteaccess, der Suchen aufzeichnen soll:

   ```ini
   # settings/siteaccess/<public siteaccess>/site.ini.append.php
   [SearchSettings]
   LogSearchStats=enabled
   ```

2. Leeren Sie den INI-Cache.
3. Erwähnen Sie die Aufzeichnung in der Datenschutzerklärung.
4. Öffnen Sie nach einigen Tagen **Setup > Suchstatistiken** (`/search/stats`); verwenden Sie **Anzeigen** für
   Suchbegriffe, die nichts gefunden haben, und **Reihenfolge** nach **Am häufigsten gesucht**.
5. Handeln Sie nach den Ergebnissen (Inhalte ergänzen, Wörter ergänzen, die Menschen verwenden), und legen Sie einen
   Termin fest, um die **Statistik zurücksetzen**.

## Prüfen

Die Seite nennt die Siteaccesses, die Suchen aufzeichnen; neue Suchbegriffe erscheinen, nachdem Besucher gesucht haben.

## Probleme

| Problem | Lösung |
|---|---|
| Die Seite warnt, dass kein Siteaccess Suchen aufzeichnet | die Einstellung steht im falschen Siteaccess, oder INI-Cache |
| Suchbegriffe enthalten Namen | setzen Sie die Statistik zurück und behalten Sie nur zusammengefasste Werte |

## Verwandte Seiten

- [Links, URL-Aliase, Platzhalter und Suchstatistik](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
