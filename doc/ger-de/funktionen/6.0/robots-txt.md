# Eine brauchbare robots.txt ab Werk

Diese Seite ist für Website-Betreiber, denen wichtig ist, wie Suchmaschinen ihre Website durchsuchen. Eine neue
Installation bringt im Web-Root eine `robots.txt` mit. Vor September 2025 mussten Sie selbst eine schreiben; jetzt
erhalten Suchmaschinen ab der ersten Anfrage sinnvolle Regeln, und Crawler werden von Seiten ferngehalten, die ihre
Zeit und Ihre Bandbreite verschwenden.

## In drei Schritten anpassen

1. Öffnen Sie `robots.txt` im Document Root und entfernen oder ergänzen Sie `Disallow:`-Zeilen für die Bereiche Ihres
   Inhaltsbaums. Verwenden Sie den URL-Alias, wie Besucher ihn sehen.
2. Ersetzen Sie die auskommentierten Sitemap-Zeilen durch Ihre eigene Sitemap, zum Beispiel:

   ```
   Sitemap: https://www.example.com/sitemap.xml
   ```

   Die Sitemap kann aus der Extension `bcgooglesitemaps` kommen, die Teil der
   [Auslieferung](standard-extension-auslieferung.md) ist.
3. Testen Sie sie:

   ```bash
   curl -s https://www.example.com/robots.txt
   ```

   Die Datei, die Sie bearbeitet haben, wird ausgegeben.

## Was sie enthält

Die Datei beginnt mit einem langen Block erklärender Kommentare (ASCII-Art und Links zu den Dokumentationsseiten des
Produkts; entfernen Sie ihn, wenn Sie ihn nicht veröffentlichen wollen) und enthält dann diese Direktiven:

```
User-agent: *
Allow: /

Disallow: /content/search/
Disallow: /content/advancedsearch/
Disallow: /content/tipafriend/
Disallow: /layout/set/print/
Disallow: /media/
Disallow: /test-area/

Crawl-delay: 1
```

- Der Disallow-Block nennt jeden Pfad in Kleinschreibung und in seiner großgeschriebenen Form (`/Content/Search/`),
  weil ein Crawler beiden Schreibweisen einer URL begegnet sein kann.
- Suchergebnisse, das Formular der erweiterten Suche, das Formular „Einem Freund empfehlen“ und das Drucklayout bringen
  einem Suchindex nichts.
- `/media/` und `/test-area/` sind Beispiele für Bereiche, die Sie vielleicht nicht indiziert haben möchten; passen
  Sie sie an Ihren eigenen Inhaltsbaum an.
- Die Sitemap-Zeilen sind als Kommentare vorhanden und zeigen auf die eigenen Websites des Produkts.

## Wie sie ausgeliefert wird

Die Beispielkonfiguration für das Umschreiben (`.htaccess_root`) enthält die Regel `RewriteRule ^robots\.txt - [L]`,
mit der der Webserver die Datei direkt an den Crawler gibt, statt die Anfrage an `index.php` weiterzureichen. Wird Ihre
Website von einem anderen Webserver ausgeliefert, sorgen Sie dafür, dass `/robots.txt` die Datei erreicht.

## Grenzen

Eine Installation mit mehreren Websites und mehreren Domains auf einem Document Root liefert allen dieselbe Datei aus.
Geben Sie jeder Domain ein eigenes Document Root oder erzeugen Sie die Datei pro Host, wenn die Regeln sich
unterscheiden müssen.

## Verwandte Seiten

- [Eine saubere Installation, die zu Shared Hosting passt](standardwerte-einer-sauberen-installation.md)
- [bcgooglesitemaps](erweiterungen/bcgooglesitemaps.md), [xrowmetadata](erweiterungen/xrowmetadata.md)
- [Changelog 6.0.11](../../../changelogs/6.0/6.0.11.md)
- [Chronik: September 2025](../../geschichte/2025/2025-09.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
