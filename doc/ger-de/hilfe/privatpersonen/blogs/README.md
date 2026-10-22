---
title: Blogs
audience: individual, website-creator, customer
category: personal-sites
order: 1620
summary: Der Bereich für Menschen, die allein oder mit einem Freund ein Blog mit Exponential betreiben - die mitgelieferten Klassen Blog und Blog post, Schreiben und Planen, Tags, Kommentare, RSS-Feeds und der Umzug von einer anderen Plattform, jeweils mit ihren ehrlichen Grenzen.
keywords: Blog, Bloggen, Klasse Blog, Klasse Blog post, Beiträge, Planen, Tags, eztags, Kommentare, Klasse Comment, RSS, Atom, Feeds, WordPress, Migration, Privatperson, blogging, posts, scheduling, comments, individual
type: help
---
# Blogs

Dieser Bereich ist für Privatpersonen, die ein Blog schreiben: ein persönliches Tagebuch, Notizen zu einem Hobby,
ein berufliches Blog, ein Reisetagebuch. Er behandelt das Blog, das Sie selbst betreiben. Wenn jemand anderes Ihr
Blog gebaut hat und hostet, finden Sie die Sicht des Inhabers unter
[Blogs: hier beginnen](../../kunden/typen/blogs-hier-beginnen.md) im Bereich für Kunden.

## Inhalt

1. [Was für ein Blog mitgeliefert wird](#was-für-ein-blog-mitgeliefert-wird)
2. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
3. [Was nicht mitgeliefert wird](#was-nicht-mitgeliefert-wird)
4. [Ein Blog in drei Schritten](#ein-blog-in-drei-schritten)

## Was für ein Blog mitgeliefert wird

| Bedarf | Was es trägt | Quelle |
|---|---|---|
| Das Blog selbst | Klasse *Blog*: Name, Beschreibung, Tags, Metadaten | `class-blog.xml` im Paket `sevenx_classes` |
| Beiträge | Klasse *Blog post*: Titel, Text, Veröffentlichungsdatum, Datum für das Zurückziehen, Tags, Kommentare aktivieren, Metadaten | `class-blog_post.xml` |
| Kommentare | Klasse *Comment* (Betreff, Autor, Nachricht), unter dem Beitrag gespeichert | `class-comment.xml`; Templates `full/blog_post.tpl` in `ezwebin` und `sevenx_themes_simple` |
| Tags | Das Schlüsselwortfeld `tags` (`ezkeyword`); `eztags` für eine verwaltete Liste | `class-blog_post.xml`; `composer.json` im Wurzelverzeichnis |
| Feeds | RSS- und Atom-Exporte, **Setup > RSS** | Kernel |
| Entwürfe, Versionen, Vorschau | Kernel | [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md) |
| Felder für Suchmaschinen | Feld `metadata` (`xrowmetadata`) | [Metadaten und Teilen in sozialen Netzwerken](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md) |
| Hilfe bei Entwürfen | Exponential Intelligence | [Der Schreibassistent](../im-betrieb/schreibassistent-expai.md) |

## Die Seiten dieses Bereichs

| Seite | Lesen Sie sie, wenn |
|---|---|
| [Ein Blog beginnen](ein-blog-beginnen.md) | Sie das Blog einrichten |
| [Beiträge schreiben und planen](beitraege-schreiben-und-planen.md) | Sie die ersten Beiträge schreiben und manche später erscheinen sollen |
| [Tags und Kategorien](tags-und-kategorien.md) | Leser ältere Beiträge nach Thema finden sollen |
| [Kommentare in Ihrem Blog](kommentare-in-ihrem-blog.md) | Sie entscheiden, ob Leser kommentieren dürfen |
| [RSS-Feeds für Ihr Blog](rss-feeds-fuer-ihr-blog.md) | Leser Ihnen in einem Feedreader folgen sollen |
| [Umzug von einer anderen Blogplattform](umzug-von-einer-anderen-blogplattform.md) | Ihr Blog heute woanders liegt |

## Was nicht mitgeliefert wird

- **Keine Erweiterung `ezcomments`.** Kommentare nutzen die mitgelieferte Klasse *Comment*; keine Threads, Likes oder
  Benachrichtigungen über Antworten.
- **Kein Ein-Klick-Importer** aus WordPress oder anderen Plattformen.
- **Standardmäßig kein Newsletter mit neuen Beiträgen.** Leser folgen dem Feed; eine Zusammenfassung per E-Mail
  braucht `cjw_newsletter` und Arbeit ([Vereinsnewsletter](../vereinsseiten/vereinsnewsletter.md)).
- **Kein eingebautes Statistik-Dashboard** zu Lesern; siehe
  [Statistik und Analyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md).

## Ein Blog in drei Schritten

1. Erstellen Sie ein *Blog*-Objekt an der Stelle, an der das Blog liegen soll, zum Beispiel „Notizen" unter der
   Startseite ([Ein Blog beginnen](ein-blog-beginnen.md)).
2. Schreiben Sie darunter einen ersten *Blog post*, veröffentlichen Sie ihn und sehen Sie ihn sich an.
3. Legen Sie einen RSS-Export für das Blog an und verlinken Sie ihn ([RSS-Feeds für Ihr Blog](rss-feeds-fuer-ihr-blog.md)).

Halten Sie dann ein Tempo, das Sie durchhalten können ([Beiträge schreiben und planen](beitraege-schreiben-und-planen.md)).

## Verwandte Seiten

- [Privatpersonen: hier beginnen](../privatpersonen-hier-beginnen.md)
- [Blogs: hier beginnen (Kunden)](../../kunden/typen/blogs-hier-beginnen.md)
- [Lesepfad für Blogs (Kunden)](../../kunden/typen/lesepfad-blogs.md)
- [Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
