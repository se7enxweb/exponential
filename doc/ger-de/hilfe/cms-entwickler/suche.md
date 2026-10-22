---
title: Suche
audience: cms-developer
category: cms-development
order: 28
summary: Für CMS-Entwickler, wie die Suche auf einer Exponential-Website funktioniert. Die Seite behandelt die eingebaute Engine und Solr über eZ Find, was indexiert wird, verzögertes Indexieren und Cron, die Suchseite und ihr Template, die Suche aus Templates und PHP, den Neuaufbau des Index und die Entscheidungen je Projekt.
keywords: Suche, Website-Suche, eZSearchEngine, eingebaute Suche, Solr, eZ Find, ezfind, solr.ini, exp:solr, durchsuchbares Attribut, DelayedIndexing, indexcontent, updatesearchindex, Such-Template, content/search, Such-Fetch, Suchstatistik, search, site search, built-in search, searchable attribute, search template, search statistics
type: help
---
# Suche

Diese Seite richtet sich an Entwickler, die die Suche einer Website bauen. Exponential liefert eine Suchmaschine mit,
die ihren Index in der Datenbank der Website hält und nichts weiter braucht; Solr über die Erweiterung `ezfind` ist die
Wahl für Websites, die Relevanzabstimmung, Facetten oder große Mengen brauchen. In beiden Fällen bestimmt das
Inhaltsmodell, was durchsuchbar ist.

## Inhalt

1. [Zwei Engines](#zwei-engines)
2. [Was indexiert wird](#was-indexiert-wird)
3. [Wann Inhalte indexiert werden](#wann-inhalte-indexiert-werden)
4. [Die Suchseite](#die-suchseite)
5. [Suchen aus Templates](#suchen-aus-templates)
6. [Suchen aus PHP](#suchen-aus-php)
7. [Den Index neu aufbauen](#den-index-neu-aufbauen)
8. [Die Wahl für ein Projekt](#die-wahl-für-ein-projekt)

## Zwei Engines

| | Eingebaut | Solr über eZ Find |
|---|---|---|
| Einstellung | `site.ini [SearchSettings] SearchEngine=eZSearchEngine` (mitgeliefert) | die Erweiterung `ezfind` aktiv, mit einem Solr-Server |
| Der Index liegt in | der Datenbank | dem Solr-Server |
| Zusätzlicher Dienst | keiner | Solr, lokal oder entfernt |
| In einer neuen Installation | ja | nein; `composer.json` schlägt `se7enxweb/ezfind` vor |

`settings/solr.ini [SolrSettings] Enabled=false` wird mitgeliefert; `./console exp:solr status` sagt, ob ein Solr
konfiguriert ist und antwortet ([Such-Backends](../hosting/grundlagen/such-backends.md)). Dieses Repository gibt nicht an,
welche Solr-Versionen eZ Find braucht; entnehmen Sie das dem eigenen Repository der Erweiterung.

## Was indexiert wird

Jedes Attribut, dessen Klassenattribut **durchsuchbar** ist (`is_searchable`), wird über seinen Datentyp indexiert;
entfernen Sie das Häkchen bei Feldern, die nie einen Treffer liefern sollen (interne Notizen, Codes). Objektname,
Klasse und Sektion werden ebenfalls indexiert, sodass sich Ergebnisse danach einschränken lassen. Objekte werden nur
von Benutzern gefunden, die sie lesen dürfen: Die Suche prüft `content/read` wie ein Fetch
([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md)).

## Wann Inhalte indexiert werden

Beim Veröffentlichen, es sei denn, `[SearchSettings] DelayedIndexing` ist `enabled` (oder `classbased` mit
`DelayedIndexingClassList[]`). Dann reiht das Veröffentlichen das Objekt nur ein, und der Cronjob `indexcontent.php`
des Standardteils indexiert es später. Das Veröffentlichen großer Objekte geht so schneller, aber **eine Website mit
verzögertem Indexieren hängt von Cron ab**: Ohne Cron erscheinen neue Inhalte nie in der Suche
([Verzögertes Indexieren und Cron](../hosting/grundlagen/such-backends.md#verzögerte-indizierung-und-cron), [Cronjobs](cronjobs.md)).

## Die Suchseite

`/content/search` ist die Such-View; `/content/advancedsearch` das erweiterte Formular. Das Ergebnis-Template ist
`content/search.tpl` Ihrer Design-Kette: Überschreiben Sie es passend zur Website, behalten Sie den Feldnamen
`SearchText` und blättern Sie mit dem mitgelieferten Navigator
([Template für Suchergebnisse](../designer/vorlagen/suchergebnis-template.md)). Die Grenzen der eingebauten Engine
sind Einstellungen in `[SearchSettings]`:

| Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|
| `MaximumSearchLimit` | `30` | die meisten Ergebnisse, die eine Anfrage verlangen darf |
| `AllowEmptySearch` | `disabled` | leere Suchen werden abgelehnt |
| `EnableWildcard` | `false` | Suche mit Platzhaltern, die langsam sein kann |
| `LogSearchStats` | `disabled` | Statistik jeder eingegebenen Phrase; sie kann personenbezogene Daten enthalten |

## Suchen aus Templates

```smarty
{def $result = fetch( 'content', 'search', hash(
        'text', $search_text,
        'subtree_array', array( $node.node_id ),
        'limit', 10,
        'offset', $view_parameters.offset ) )}
{foreach $result.SearchResult as $hit}
    {node_view_gui content_node=$hit view='line'}
{/foreach}
<p>{$result.SearchCount} results</p>
{undef $result}
```

Die Parameter des Fetch `search` (`kernel/content/function_definition.php`): `text`, `subtree_array`, `offset`,
`limit`, `publish_timestamp`, `publish_date`, `section_id`, `class_id`, `class_attribute_id`, `ignore_visibility`,
`limitation` und `sort_by`; `class_id` nimmt numerische Klassen-IDs. Verwenden Sie ihn für ein Suchfeld, das auf einen
Teil der Website beschränkt ist; für Listen ohne Suchtext ist ein Fetch `list` oder `tree` günstiger
([Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)).

## Suchen aus PHP

`eZSearch::search( $text, $params )` führt die konfigurierte Engine aus; derselbe Code funktioniert also mit der
eingebauten Engine und mit eZ Find. Prüfen Sie die Parameter, die Ihre Engine annimmt, in ihrer Klasse, bevor Sie sich
darauf verlassen (`kernel/classes/ezsearch.php`, die von `SearchEngine` genannte Engine-Klasse).

## Den Index neu aufbauen

Nach einem Import, einem Datenbankumzug oder wenn Ergebnisse falsch sind:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user
```

`--clean` leert zuerst den Index. Führen Sie es als Benutzer der Website aus, auf einer großen Website in einer ruhigen
Stunde. Mit eZ Find indexieren Sie mit den eigenen Werkzeugen der Erweiterung neu.

## Die Wahl für ein Projekt

| Bedarf | Wahl |
|---|---|
| Ein Suchfeld auf einer kleinen oder mittleren Website | die eingebaute Engine |
| Relevanzabstimmung, Facetten, Rechtschreibvorschläge, viele Sprachen, große Mengen | Solr über eZ Find, nachdem Sie die benötigten Versionen geprüft haben |
| Nach Feldern gefilterte Listen ohne Suchtext | Fetches, nicht die Suche |
| Ergebnisse nach bester Übereinstimmung zuerst in den Dialogen der Redakteure | mit eZ Find, `ezfind.ini [SearchSettings] SortBy[]` ([Die Reihenfolge der Suchergebnisse](../../anleitungen/inhaltsmodell-und-bearbeitung.md#die-reihenfolge-der-suchergebnisse-in-den-dialogen-des-editors)) |

## Verwandte Seiten

- [Suchen](../suchen.md): die Sicht der Redakteure
- [Suchstatistik](../../anleitungen/urls-und-aliase.md#5-suchstatistiken)
- [Kurs für Trainer zur Suche](../trainer/suche/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
