---
title: Override nach Elternknoten
audience: designer, developer
category: templates
order: 620
summary: Templates, die davon abhängen, wo Inhalte liegen - Abgleich auf die Klasse des Elternknotens (parent_class_identifier), die Remote-ID des Elternknotens, den Elternknoten, einen URL-Teilbaum (Präfix von url_alias) oder die Tiefe, mit Beispielen für Blogbeiträge, Nachrichten und Seiten der obersten Ebene.
keywords: Override nach Elternknoten, parent_class_identifier, parent_node, parent_node_remote_id, Teilbaum per url_alias, depth, ortsabhängiges Template, Template für Blogbeiträge, Template für den Nachrichtenordner, override parent, url_alias subtree, location based template, blog post template, news folder template
type: help
---
# Override nach Elternknoten

Diese Seite ist für Designer, die Inhalte je nach ihrem Platz anders aussehen lassen wollen: einen Artikel unter einem Blog,
Einträge im Nachrichtenordner, Seiten der obersten Ebene.

## Inhalt

1. [Nach der Klasse des Elternknotens](#nach-der-klasse-des-elternknotens)
2. [Nach einem bestimmten Elternknoten](#nach-einem-bestimmten-elternknoten)
3. [Nach einem Teilbaum](#nach-einem-teilbaum)
4. [Nach Tiefe](#nach-tiefe)

## Nach der Klasse des Elternknotens

```ini
[mytheme_line_post_in_blog]
Source=node/view/line.tpl
MatchFile=line/blog_post_in_blog.tpl
Subdir=templates
Match[class_identifier]=blog_post
Match[parent_class_identifier]=blog
Priority=10
```

Übertragbar: funktioniert, wo auch immer Blogs liegen.

## Nach einem bestimmten Elternknoten

`Match[parent_node_remote_id]=mytheme-news-folder` (übertragbar) oder `Match[parent_node]=61` (nur diese Installation). Trifft
nur auf die direkten Kinder zu.

## Nach einem Teilbaum

`Match[url_alias]=news` trifft auf jede Seite zu, deren URL-Alias mit `news` beginnt: den ganzen Nachrichtenbereich in jeder
Tiefe. Beachten Sie, dass es der URL folgt, sodass eine Umbenennung des Ordners die Regel zerstört; für einen großen Bereich ist
eine Sektion das robustere Werkzeug ([Override nach Sektion](override-nach-sektion.md)).

## Nach Tiefe

`Match[depth]=2` trifft auf Seiten direkt unter der Wurzel zu (der Wurzelknoten des Baums hat die Tiefe 1); praktisch für ein
anderes Template für Seiten der obersten Ebene.

## Verwandte Seiten

- [Override-Match-Schlüssel](override-match-schluessel.md)
- [node_view_gui](node-view-gui.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
