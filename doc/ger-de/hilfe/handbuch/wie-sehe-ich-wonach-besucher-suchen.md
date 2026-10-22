---
title: Wie sehe ich, wonach Besucher suchen?
audience: editor, admin
category: manual
order: 565
summary: Setup > Suchstatistiken listet jeden aufgezeichneten Suchbegriff auf, mit der Häufigkeit der Suche und der durchschnittlichen Trefferzahl; Ohne Treffer zeigt die Lücken, Jetzt suchen führt einen Suchbegriff erneut aus, und Statistik zurücksetzen leert das Protokoll. Die Aufzeichnung ist aus, bis ein Administrator sie einschaltet.
keywords: Suchstatistiken, Suchbegriffe, ohne Treffer, am häufigsten gesucht, Statistik zurücksetzen, Datenschutz, LogSearchStats, search/stats, search statistics, search phrases, found nothing, most searched, reset statistics, privacy
context: search/stats
type: help
---
# Wie sehe ich, wonach Besucher suchen?

Die Suchstatistiken sind eine Liste der Begriffe, die Menschen in die Suche Ihrer Websites eingegeben haben. Sie
zeigen, wonach Leser suchen, und, was nützlicher ist, wonach sie suchen und es nicht finden.

## Wer das tun kann

Benutzer, deren Rolle Zugriff auf das Modul **search** gibt, meist Administratoren. Jemand mit dem Recht, Einstellungen
zu ändern, muss die Aufzeichnung zuerst einschalten.

## Schritte

1. Bitten Sie einen Administrator, die Aufzeichnung für den öffentlichen Siteaccess einzuschalten (`site.ini [SearchSettings]
   LogSearchStats=enabled`). Sie ist standardmäßig aus, weil Suchbegriffe personenbezogene Daten enthalten können.
2. Klicken Sie auf **Setup** und dann auf **Suchstatistiken**.
   Die Seite zeigt eine **Übersicht** (Anzahl der Suchen, verschiedene Suchbegriffe, der Anteil ohne Treffer) und
   welche Siteaccesses Suchen aufzeichnen.
3. Wählen Sie, was angezeigt wird: **Alle Suchbegriffe**, **Am häufigsten gesucht**, **Ohne Treffer** oder **Wenigste
   Treffer**, und die Reihenfolge.
4. Geben Sie unter **Einen Suchbegriff finden** einen Teil eines Begriffs ein, um danach zu suchen.
5. Klicken Sie in einer Zeile auf **Jetzt suchen**, um den Begriff in der Administration auszuführen und zu sehen, was
   er heute findet.
6. Beheben Sie, was Sie erfahren: Schreiben Sie die fehlende Seite, ergänzen Sie einen vorhandenen Titel oder Text um
   die Wörter, die Menschen verwenden, oder verschlagworten Sie ihn.
7. Um neu zu zählen, klicken Sie auf **Statistik zurücksetzen** und bestätigen Sie.

## Was schiefgehen kann

- **„Es wurde noch keine Suche aufgezeichnet.“** Kein Siteaccess zeichnet Suchen auf, oder seit dem Einschalten hat
  niemand gesucht.
- **Achtung: Statistik zurücksetzen entfernt jeden aufgezeichneten Suchbegriff endgültig,** nicht nur die auf dem
  Bildschirm.
- **Die Suchbegriffe enthalten Namen oder E-Mail-Adressen.** Menschen geben alles Mögliche in ein Suchfeld ein.
  Behandeln Sie die Liste als personenbezogene Daten, halten Sie sie kurz und setzen Sie sie regelmäßig zurück.
- **Der Menüeintrag fehlt.** Ihre Rolle gibt keinen Zugriff auf das Modul search.

## Verwandte Seiten

- [Die Suche der Website und wonach Besucher suchen](../kunden/alltag/website-suche-und-wonach-besucher-suchen.md)
- [Suchstatistiken und Datenschutz](../verwaltung/aufbauen/suche/suchstatistik-und-datenschutz.md)
- [Warum findet die Suche ein Element nicht, von dem ich weiß, dass es existiert?](warum-findet-die-suche-ein-element-nicht.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
