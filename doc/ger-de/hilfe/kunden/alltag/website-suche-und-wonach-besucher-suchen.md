---
title: Website-Suche und wonach Besucher suchen
audience: customer, editor, admin
category: customer-success
order: 22
summary: Wie ein Website-Betreiber die eigene Suche der Website als Ideenquelle und Qualitätsprüfung nutzt - Suchstatistiken einschalten, Begriffe lesen, die nichts gefunden haben, die Inhalte verbessern statt der Suche, was die eingebaute Suchmaschine und Solr leisten, und eine monatliche Routine.
keywords: Website-Suche, Suchstatistiken, Suchbegriffe, keine Treffer, Auffindbarkeit, eingebaute Suche, Solr, ezfind, durchsuchbare Attribute, Inhaltslücken, site search, search statistics, search phrases, no results, findability, built-in search, searchable attributes, content gaps
type: help
---
# Website-Suche und wonach Besucher suchen

Diese Seite ist für den Betreiber und die Chefredaktion. Die Begriffe, die Besucher in das Suchfeld Ihrer Website
eingeben, sind die direkteste Rückmeldung, die Sie bekommen: Sie sagen in den eigenen Worten der Besucher, was diese zu
finden erwarten. Exponential kann sie aufzeichnen und anzeigen, welche davon nichts gefunden haben
([URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)). Wie Redakteure
innerhalb der Administration suchen, ist ein anderes Thema: [Suchen](../../suchen.md).

## Inhalt

1. [Wie die Suche der Website funktioniert](#wie-die-suche-der-website-funktioniert)
2. [Suchstatistiken einschalten](#suchstatistiken-einschalten)
3. [Die Begriffe lesen](#die-begriffe-lesen)
4. [Die Inhalte verbessern, nicht die Suche](#die-inhalte-verbessern-nicht-die-suche)
5. [Wann Sie mehr anfordern sollten](#wann-sie-mehr-anfordern-sollten)
6. [Eine monatliche Routine](#eine-monatliche-routine)

## Wie die Suche der Website funktioniert

- Die eingebaute Suchmaschine hält ihren Index in der Datenbank und indexiert beim Veröffentlichen die Felder, die in
  jeder Klasse als **Durchsuchbar** markiert sind. Besucher finden nur, was sie lesen dürfen
  ([Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md)).
- Große Websites oder Websites, die Facetten und eine Feinabstimmung der Relevanz brauchen, verwenden Solr über
  `ezfind` ([Suche mit ezfind und Solr](../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)).
- Verborgene Inhalte und Inhalte in einer Sektion, die Besucher nicht lesen dürfen, werden von Besuchern nicht gefunden.

## Suchstatistiken einschalten

Suchstatistiken sind **standardmäßig ausgeschaltet** (`site.ini [SearchSettings] LogSearchStats=disabled`), weil
Suchbegriffe personenbezogene Daten sein können ([Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md)). Um
sie zu nutzen:

1. Entscheiden Sie mit der Person, die für den Datenschutz verantwortlich ist, ob und wie lange Sie Suchbegriffe
   aufbewahren. Dies ist keine Rechtsberatung.
2. Bitten Sie Ihren Administrator, `LogSearchStats=enabled` im **öffentlichen** Siteaccess zu setzen.
3. Vermerken Sie in Ihren Datenschutzhinweisen, dass Suchbegriffe aufgezeichnet werden.

**Setup > Suchstatistiken** (`/search/stats`) zeigt dann jeden Begriff, wie oft danach gesucht wurde und die
durchschnittliche Zahl der Treffer. Die Seite filtert die Begriffe, die nichts gefunden haben, sortiert nach
Häufigkeit, Begriff oder den wenigsten Treffern und führt einen Begriff mit einem Klick erneut aus. Wenn kein
Siteaccess Suchen aufzeichnet, sagt die Seite das ([URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)).

## Die Begriffe lesen

| Was Sie sehen | Was es meist bedeutet | Was zu tun ist |
|---|---|---|
| Ein häufiger Begriff ohne Treffer | der Inhalt fehlt | ihn schreiben, oder auf die Stelle verlinken, an der die Frage beantwortet wird |
| Ein Begriff ohne Treffer, obwohl Sie die Seite haben | Besucher verwenden andere Wörter | ihre Wörter in Titel oder Einleitung verwenden |
| Ein Begriff mit vielen Treffern, oft gesucht | Besucher finden es nicht im Menü | einen Link ins Menü oder auf die Startseite setzen |
| Produkt- oder Personennamen | man erwartet ein Verzeichnis oder eine Katalogseite | eine solche erwägen |
| Tippfehler | normal | die gängige Schreibweise im Text ergänzen, wo es natürlich wirkt |

## Die Inhalte verbessern, nicht die Suche

Die meisten Suchprobleme sind Inhaltsprobleme: Titel mit internen Begriffen, leer gelassene Einleitungen, Informationen
nur in einem PDF. Bevor Sie um geänderte Sucheinstellungen bitten, ändern Sie die Wörter. Redakteure bestimmen Titel
und Einleitungen; der Website-Ersteller entscheidet, welche Felder durchsuchbar sind ([Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md)).

> **Tipp**
> Die Suchstatistiken sind auch eine gute Themenliste für den [Redaktionskalender](redaktionskalender.md).

## Wann Sie mehr anfordern sollten

Fragen Sie Ihren Partner, wenn:

- wichtige Seiten zwar gefunden werden, aber weit unten in der Liste stehen (eine Feinabstimmung der Relevanz braucht Solr);
- Besucher Filter wie Typ, Datum oder Kategorie brauchen (Facetten);
- die Suche auf einer großen Website langsam ist;
- eine neu veröffentlichte Seite überhaupt nicht gefunden wird: Der Index muss möglicherweise neu aufgebaut werden,
  oder die Indexierung wird durch Cron verzögert (`DelayedIndexing`) ([Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md)).

## Eine monatliche Routine

- [ ] Öffnen Sie **Setup > Suchstatistiken**, filtern Sie Begriffe ohne Treffer, sortieren Sie nach Häufigkeit.
- [ ] Nehmen Sie die ersten zehn; entscheiden Sie für jeden: schreiben, umbenennen, verlinken oder ignorieren.
- [ ] Führen Sie jeden behobenen Begriff mit einem Klick erneut aus, um zu bestätigen, dass er die Seite jetzt findet.
- [ ] Notieren Sie die Maßnahmen im Protokoll der [Monatlichen SEO-Checkliste](monatliche-seo-checkliste.md).

## Verwandte Seiten

- [Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md)
- [Suchen (für Redakteure)](../../suchen.md)
- [Suche im Intranet](../../unternehmen/intranets/suche-im-intranet.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
