---
title: Die Suchstatistiken zurücksetzen
audience: government, admin
category: public-sector
order: 320
summary: Schritt für Schritt - prüfen, welche Siteaccesses Suchbegriffe aufzeichnen, die Zahlen nutzen und alle aufgezeichneten Begriffe mit Statistik zurücksetzen unter Setup > Suchstatistiken entfernen, weil Begriffe Namen und Aktenzeichen enthalten können.
keywords: Suchstatistiken, Suchbegriffe, Statistik zurücksetzen, LogSearchStats, personenbezogene Daten, Minimierung, Setup Suchstatistiken, öffentlicher Sektor, search statistics, search phrases, reset statistics, personal data, minimization, public sector
type: help
---
# Die Suchstatistiken zurücksetzen

Für Administratoren. Suchstatistiken sind standardmäßig ausgeschaltet (`[SearchSettings] LogSearchStats=disabled`); wo
sie eingeschaltet sind, behalten sie, was Besucher eingetippt haben, und das kann Namen und Aktenzeichen einschließen
([Suchbegriffe](../../datenschutz/suchbegriffe.md)).

## Inhalt

1. [Schritte](#schritte)
2. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
3. [Verwandte Seiten](#verwandte-seiten)

## Schritte

1. Öffnen Sie **Setup > Suchstatistiken** (`/search/stats`). Die Seite sagt, welche Siteaccesses Suchen aufzeichnen,
   oder warnt, dass keiner es tut.
2. Nutzen Sie zuerst die Zahlen: **Anzeigen** der Begriffe, die nichts gefunden haben; notieren Sie fehlende Inhalte
   oder Wörter für die Redakteure.
3. **Statistik zurücksetzen**: Eine Bestätigung an Ort und Stelle sagt, wie viele Begriffe wegfallen; bestätigen Sie.
   Sie werden endgültig entfernt.
4. Wenn Sie die Statistiken nicht mehr brauchen, schalten Sie sie in `site.ini.append.php` des Siteaccess aus:

   ```ini
   [SearchSettings]
   LogSearchStats=disabled
   ```

   und leeren Sie den INI-Cache.
5. Tragen Sie das nächste Zurücksetzen in Ihren Kalender ein (zum Beispiel vierteljährlich).

## Prüfen, ob es funktioniert hat

Die Seite zeigt keine Begriffe; nach neuen Suchen auf einem aufzeichnenden Siteaccess erscheinen nur neue Begriffe.

## Verwandte Seiten

- [Suchbegriffe](../../datenschutz/suchbegriffe.md)
- [URLs und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)
- [Datenminimierung](../../datenschutz/datenminimierung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
