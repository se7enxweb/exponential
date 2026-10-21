---
title: Suchstatistik und Datenschutz
audience: government, admin, editor
category: public-sector
order: 80
summary: Wie Sie mit Setup > Suchstatistiken lernen, wonach die Öffentlichkeit sucht und was sie nicht findet, wie Sie die Aufzeichnung je Siteaccess einschalten und wie Sie mit den personenbezogenen Daten umgehen, die Suchbegriffe enthalten können.
keywords: Suchstatistik, Suchbegriffe, keine Treffer, Setup Suchstatistiken, Datenschutz, personenbezogene Daten, Statistik zurücksetzen, search statistics, LogSearchStats, search phrases, zero results, Setup Search statistics, search/stats, privacy, personal data, reset statistics
type: help
---
# Suchstatistik und Datenschutz

Diese Seite richtet sich an Administratoren und Redakteure, die eine öffentliche Site anhand dessen verbessern möchten,
wonach Menschen suchen. Exponential zeichnet Suchbegriffe nur dort auf, wo es eingeschaltet ist, und zeigt sie unter
**Setup > Suchstatistiken** (`/search/stats`)
([Suchstatistik](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)).

## Inhalt

1. [Einschalten](#einschalten)
2. [Die Seite lesen](#die-seite-lesen)
3. [Danach handeln](#danach-handeln)
4. [Datenschutz](#datenschutz)

## Einschalten

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

Voreingestellt ist es `disabled`. Schalten Sie es je öffentlichem Siteaccess (je Sprache) ein. Die Seite sagt, welche
Siteaccesses Suchen aufzeichnen, oder warnt, dass keiner es tut.

## Die Seite lesen

- Kennzahlen: verschiedene Suchbegriffe, alle Suchen, Suchbegriffe ohne Treffer und ihr Anteil.
- **Einen Suchbegriff finden**, **Anzeigen** (alle oder nur die ohne Treffer), **Reihenfolge** (**Am häufigsten
  gesucht**, **Suchbegriff A bis Z**, **Wenigste Treffer**).
- **Jetzt suchen** führt einen Suchbegriff in der Suche der Administration aus, um zu sehen, was er heute findet.

## Danach handeln

| Befund | Aktion |
|---|---|
| Ein häufiger Suchbegriff findet nichts | ergänzen Sie den Inhalt oder die Wörter, die Menschen verwenden, auf einer bestehenden Seite |
| Ein Suchbegriff findet zuerst die falsche Seite | verbessern Sie Titel und Zusammenfassungen; mit Solr heben Sie die richtige Seite hervor (siehe „Hervorhebung und Synonyme“) |
| Viele Suchen nach einer Leistung | verlinken Sie sie von der Startseite oder einer Liste der „häufigsten Anliegen“ |

## Datenschutz

Suchbegriffe sind das, was Besucher eingegeben haben. Sie können Namen, Adressen oder Aktenzeichen enthalten. Der
Datenschutzstandard führt gespeicherte Suchbegriffe unter den bekannten Lücken, mit denen die Site umgehen muss
([Standards zum Datenschutz](../../../../standards/datenschutz.md#bekannte-lücken)):

- geben Sie die Seite nur Mitarbeitern, die sie brauchen (sie braucht Zugang zu Setup);
- erwähnen Sie die Aufzeichnung in der Datenschutzerklärung;
- **Statistik zurücksetzen** entfernt nach einer Bestätigung alle Suchbegriffe endgültig; tun Sie es nach einem Plan,
  der zu Ihrer Aufbewahrungsregel passt;
- exportieren oder kopieren Sie Suchbegriffe nur als zusammengefasste Werte ohne personenbezogene Daten.

## Verwandte Seiten

- [Links, URL-Aliase, Platzhalter und Suchstatistik](../../../../anleitungen/urls-und-aliase.md)
- [Hervorhebung und Synonyme](hervorhebung-und-synonyme.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
