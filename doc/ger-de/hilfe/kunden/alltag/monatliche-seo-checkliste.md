---
title: Monatliche SEO-Checkliste
audience: customer, editor, businesses
category: customer-success
order: 17
summary: Eine einstündige monatliche Checkliste für den Betreiber oder die Marketingleitung einer Exponential-Website - Suchbegriffe ohne Treffer, kaputte Links, kaputte Beziehungen, entfernte Seiten, die Qualität neuer Inhalte, Sitemap und Webmaster-Tools - mit einer Protokollzeile zum Aufbewahren.
keywords: SEO-Checkliste, monatlich, Suchstatistiken, kaputte Links, URL-Verwaltung, Weiterleitungen, Sitemap, Titel, Beschreibungen, Webmaster-Tools, SEO checklist, monthly, search statistics, broken links, link management, redirects, titles, descriptions, webmaster tools
type: help
---
# Monatliche SEO-Checkliste

Diese Seite ist eine **Checkliste** für eine Stunde im Monat. Sie verwendet die Werkzeuge, die mit Exponential kommen,
und setzt voraus, dass die Antworten aus [SEO-Prüfungen](seo-pruefungen.md) bekannt sind (ob es eine Sitemap gibt, welches
Feld die Beschreibung ist). Einige Schritte brauchen Seiten unter Setup; erledigen Sie diese mit Ihrem Administrator,
wenn Sie keinen Zugriff haben.

## Inhalt

1. [Besucher und Suche](#besucher-und-suche)
2. [Links und Adressen](#links-und-adressen)
3. [Neue Inhalte des Monats](#neue-inhalte-des-monats)
4. [Technische Signale](#technische-signale)
5. [Protokoll](#protokoll)

## Besucher und Suche

- [ ] Öffnen Sie **Setup > Suchstatistiken** (`/search/stats`). Filtern Sie die Begriffe, die nichts gefunden haben,
      und sortieren Sie nach Häufigkeit ([URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)).
- [ ] Für jeden der häufigsten Begriffe ohne Treffer: Fehlt der Inhalt (schreiben Sie ihn), heißt er anders (nehmen
      Sie das Wort in einen Titel oder eine Einleitung auf), oder ist er verborgen (prüfen Sie seine Sichtbarkeit)?
- [ ] Wenn die Seite meldet, dass kein Siteaccess Suchen aufzeichnet, entscheiden Sie, ob Sie das anfordern
      ([Website-Suche und wonach Besucher suchen](website-suche-und-wonach-besucher-suchen.md)).

## Links und Adressen

- [ ] **Setup > URL-Verwaltung** (`/url/list`): Zeigen Sie **ungültige** Links an; beheben oder entfernen Sie jeden
      in dem Inhalt, der ihn verwendet. Die Linkprüfung läuft im Cron-Teil `infrequent`
      ([URL-Aliase, Wildcards und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)).
- [ ] **Kaputte Beziehungen** ab der Inhaltswurzel: Beheben Sie jede Quelle
      ([Die Inhaltsprüfungen](../../../funktionen/6.0/inhaltspruefungen.md)).
- [ ] In diesem Monat entfernte Seiten: Jede hat eine Ersatzadresse (einen Alias auf der neuen Seite oder eine
      Wildcard), oder ist bewusst weg ([URLs, Weiterleitungen und verschobene Seiten](urls-weiterleitungen-und-verschobene-seiten.md)).
- [ ] Testen Sie zwei oder drei alte Adressen, von denen Sie wissen, dass sie verschoben wurden: Sie sollten auf der
      neuen Seite landen.

## Neue Inhalte des Monats

Wählen Sie fünf Elemente, die in diesem Monat veröffentlicht wurden (das Dashboard listet neue Inhalte):

- [ ] Titel kurz und klar; die Adresse liest sich gut.
- [ ] Einleitung oder Beschreibung ausgefüllt.
- [ ] Eine Hauptüberschrift; Überschriften in der richtigen Reihenfolge.
- [ ] Bilder haben Alternativtexte.
- [ ] Linktexte sagen, wohin sie führen.
- [ ] Jede Übersetzung hat ihren eigenen Titel, nicht den der Ausgangssprache
      ([Übersetzungsroutine](uebersetzungsroutine.md)).

Sagen Sie den Autoren, was Sie gefunden haben, freundlich und konkret. Die Regeln für die Redaktion stehen in der
[Checkliste zur Barrierefreiheit für Redakteure](checkliste-barrierefreiheit-fuer-redakteure.md), die sich mit gutem SEO überschneidet.

## Technische Signale

- [ ] Die Webmaster-Tools der Suchmaschinen: neue Crawl-Fehler, aus dem Index ausgeschlossene Seiten.
- [ ] Die Sitemap, wenn Ihre Website eine hat: Sie öffnet sich, listet die neuen Seiten dieses Monats und listet keine
      verborgenen Seiten oder Seiten nur für Mitglieder ([XML-Sitemaps](../../ersteller/website-ersteller/sitemaps.md)).
- [ ] `robots.txt` nennt weiterhin die Sitemap und wurde nicht versehentlich geändert
      ([robots.txt für Ihre Website](../../ersteller/website-ersteller/robots-txt.md)).
- [ ] Die Website öffnet sich unter einem einzigen Hostnamen mit HTTPS; die anderen Schreibweisen leiten weiter.
- [ ] Alles, was langsam oder kaputt ist, geht mit Adresse und Uhrzeit an den Hoster oder Partner.

## Protokoll

```text
SEO MONTH: __________   Checked by: ______________   Time spent: ______
Top search phrases without results: ______________________________________
Content written or renamed because of them: ______________________________
Invalid links fixed: ____   Broken relations fixed: ____   Redirects added: ____
Crawl errors in webmaster tools: ____   Sent to partner/host: ______________
```

## Verwandte Seiten

- [SEO-Prüfungen](seo-pruefungen.md)
- [Wöchentliche Routine des Betreibers](woechentliche-routine-des-betreibers.md)
- [Monatliche Checkliste für den Betrieb](../../unternehmen/vorlagen/monatliche-betriebscheckliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
