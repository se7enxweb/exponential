---
title: Umstieg von WordPress
audience: cms-developer
category: cms-development
order: 4
summary: Für WordPress-Entwickler, wie sich Ihr Wissen auf Exponential überträgt. Die Seite behandelt Post Types und Custom Fields, Themes und die Template-Hierarchie, Plugins und Hooks, WP_Query, Shortcodes und Blöcke, wp-config.php, WP-CLI und wp-cron sowie das, was Sie zuerst verlernen sollten.
keywords: WordPress, Umstieg von WordPress, Post Type, Custom Fields, ACF, Theme, Template-Hierarchie, Plugin, Hooks, Actions, Filter, WP_Query, Shortcode, Gutenberg, Block-Editor, wp-config, WP-CLI, wp-cron, Migration, coming from WordPress, template hierarchy, block editor
type: help
---
# Umstieg von WordPress

Diese Seite richtet sich an Entwickler, die WordPress kennen und ein Exponential-Projekt beginnen. Sie übersetzt die
WordPress-Begriffe, die Sie täglich verwenden, in ihre Entsprechungen in Exponential und verweist auf die Seite, die
jeden erklärt. Sie vergleicht nur Begriffe; sie bewertet die beiden Systeme nicht, die für unterschiedliche
Ausgangslagen gebaut sind.

## Inhalt

1. [Der größte Unterschied](#der-größte-unterschied)
2. [Begriff für Begriff](#begriff-für-begriff)
3. [Templates: von der Template-Hierarchie zu Overrides](#templates-von-der-template-hierarchie-zu-overrides)
4. [Abfragen: von WP_Query zu fetch](#abfragen-von-wp_query-zu-fetch)
5. [Plugins und Hooks: Erweiterungen, Events und Operatoren](#plugins-und-hooks-erweiterungen-events-und-operatoren)
6. [Konfiguration und Kommandozeile](#konfiguration-und-kommandozeile)
7. [Was Sie verlernen sollten](#was-sie-verlernen-sollten)
8. [Eine WordPress-Website umziehen](#eine-wordpress-website-umziehen)

## Der größte Unterschied

WordPress geht von Beiträgen und Seiten aus und lässt Sie darauf eigene Post Types und Custom Fields ergänzen, oft über
Plugins. Exponential geht von einem **Inhaltsmodell aus, das Sie selbst festlegen**: Jede Art von Inhalt ist eine
**Inhaltsklasse** mit typisierten **Attributen**, und alles, von einer Seite über ein Bild bis zu einem Benutzer, ist
ein Objekt einer Klasse, platziert in einem **Inhaltsbaum** ([Das Inhaltsmodell](das-inhaltsmodell.md)). Rechnen Sie
damit, den ersten Projekttag mit Klassen statt mit einem Theme zu verbringen.

## Begriff für Begriff

| In WordPress | In Exponential | Lesen |
|---|---|---|
| Post Type, Custom Post Type | Inhaltsklasse | [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md) |
| Custom Field, Meta-Box, ACF-Feld | Klassenattribut eines Datentyps | [Datentypen wählen](datentypen-waehlen.md) |
| Beitrag, Seite | Inhaltsobjekt mit Versionen | [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md) |
| Übergeordnete Seite, Menüreihenfolge | Knoten im Inhaltsbaum, Priorität | [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md) |
| Kategorie, Schlagwort | Platzierungen, `ezkeyword` oder `eztags` | [Relationen und Einbettungen](relationen-und-einbettungen.md) |
| Anhang in der Mediathek | Bild- oder Dateiobjekt in der Medienbibliothek | [Bilder und Bildvarianten](bilder-und-bildvarianten.md) |
| Revisionen | Versionen, mit Vergleichen und Wiederherstellen | [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md) |
| Rollen und Berechtigungen | Rollen mit Richtlinien, eingeschränkt nach Sektion, Teilbaum, Klasse | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md) |
| Permalinks | URL-Aliase, aus dem Baum gebildet | [URL-Aliase und Routing](url-aliase-und-routing.md) |
| Theme | Design in einer Design-Erweiterung | [Templates und Overrides](templates-und-overrides-fuer-cms-entwickler.md) |
| Plugin | Erweiterung | [Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md) |
| Shortcode | eigenes Tag (Custom Tag) in Rich Text | [Rich Text](rich-text.md) |
| Layouts im Block-Editor | Exponential Layouts (Zonen und Blöcke) | [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md) |
| Multisite-Netzwerk | Siteaccesses in einer Installation | [Siteaccesses und Multisite](siteaccesses-und-multisite.md) |
| REST API | die REST-Schnittstelle von `ezprestapi` und `expservices` | [Die REST-API](die-rest-api.md) |

## Templates: von der Template-Hierarchie zu Overrides

WordPress wählt eine PHP-Template-Datei über eine Namenshierarchie (`single-{post_type}.php`, `page-{slug}.php` und so
weiter). Die Templates von Exponential sind in einer eigenen Template-Sprache (`.tpl`) geschrieben, nicht in PHP, und
ein Template wird über **Override-Regeln** in `override.ini` gewählt: "für `node/view/full.tpl`, wenn die Klasse
`exp_event` ist, verwende `full/exp_event.tpl`". Regeln können auf Klasse, Knoten, übergeordneten Knoten, Sektion,
Tiefe, Ansichtsmodus und mehr passen. Die Template-Sprache maskiert Ausgaben mit `|wash()` und hat keinen direkten
Zugriff auf PHP, sodass Templates gefahrlos an Designer übergeben werden können. Beginnen Sie mit
[Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md); die Sprache selbst steht in
[Die Template-Sprache](../template-sprache/README.md).

## Abfragen: von WP_Query zu fetch

Wo ein WordPress-Template `WP_Query` oder `get_posts()` aufruft, ruft ein Exponential-Template eine **Fetch-Funktion**
auf:

```smarty
{def $events = fetch( 'content', 'list', hash(
        'parent_node_id', $node.node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'exp_event' ),
        'sort_by', array( 'attribute', true(), 'exp_event/start_date' ),
        'limit', 10 ) )}
```

Fetches prüfen Berechtigungen und versteckte Knoten für Sie. Details:
[Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md); in PHP: [Inhalte aus PHP](inhalte-aus-php.md).

## Plugins und Hooks: Erweiterungen, Events und Operatoren

Ein WordPress-Plugin hängt sich in Actions und Filter ein. Eine **Erweiterung** in Exponential ist ein Ordner unter
`extension/`, der Einstellungen, Templates, Module (Seiten mit eigener URL), Template-Operatoren, Datentypen,
Workflow-Events, Cronjobs und Konsolenbefehle hinzufügen kann. Statt eines einzigen Hook-Systems gibt es mehrere
genaue Erweiterungspunkte:

| WordPress-Gewohnheit | Exponential |
|---|---|
| `add_action` beim Speichern oder Veröffentlichen | ein Workflow-Event am Auslöser für das Veröffentlichen oder ein Event-Listener ([Workflows und Auslöser](workflows-und-ausloeser.md), [Events, Listener und Filter](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ereignisse-und-filter.md)) |
| `add_filter` auf die Ausgabe | ein Template-Operator oder ein Override des Templates ([Template-Operatoren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/template-operatoren.md)) |
| Eine eigene Administrationsseite | ein Modul mit einer View und einer Richtlinie ([Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md)) |
| Hilfsfunktionen in `functions.php` | Template-Operatoren und PHP-Klassen in Ihrer Erweiterung |

Die vollständige Liste: [Erweiterungspunkte auf einen Blick](../erweiterungsentwicklung/teil-8-referenz/erweiterungspunkte-auf-einen-blick.md).

## Konfiguration und Kommandozeile

| WordPress | Exponential |
|---|---|
| `wp-config.php` | INI-Dateien: mitgelieferte Standardwerte in `settings/`, Ihre Werte in `settings/override/` und `settings/siteaccess/<name>/` ([Einstellungen und INI](einstellungen-und-ini-fuer-cms-entwickler.md)) |
| Options-Tabelle | INI-Einstellungen, gelesen und geändert mit `./console exp:ini` |
| WP-CLI | `./console` (`bin/php/console`) und die Skripte in `bin/php/` |
| wp-cron | Cronjobs, die die Crontab des Systems ausführt ([Cronjobs](cronjobs.md)) |
| Seiten-Cache-Plugins | View-Cache, HTTP-Cache und der Antwort-Cache von Velocity, eingebaut ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md)) |

## Was Sie verlernen sollten

- **Fragen Sie die Datenbank nicht aus Templates oder Plugins ab.** Verwenden Sie Fetch-Funktionen und die
  Kernel-API; sie halten Berechtigungen, Versionen, Caches und Suche korrekt.
- **Legen Sie kein Markup in Inhalte.** Rich Text wird als XML gespeichert und von Templates gezeichnet; Redakteure
  wählen die Struktur, nicht das HTML ([Rich Text](rich-text.md)).
- **Ändern Sie keine mitgelieferten Dateien.** Kernel-Dateien und `settings/*.ini` werden bei einer Aktualisierung
  ersetzt; Ihre Arbeit liegt in Ihrer Erweiterung und in Override-Einstellungen.
- **Leeren Sie den richtigen Cache.** Templates, Einstellungen und Inhaltsansichten werden zwischengespeichert;
  [Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md) sagt, welcher Cache zu welcher Änderung gehört.

## Eine WordPress-Website umziehen

Die Migrationsanleitung behandelt Schritt für Schritt den WXR-Export, die Zuordnung, HTML zu Rich Text, Permalinks und
Weiterleitungen: [WordPress und Exponential](../../installation/migration/wordpress.md). Die Werkzeuge, die das Ergebnis
importieren, finden Sie unter [Inhalte importieren](inhalte-importieren.md); die Lektion für Trainer ist
[Von WordPress](../trainer/migration/modul-07-von-anderen-systemen/von-wordpress.md).

## Verwandte Seiten

- [Exponential für CMS-Entwickler](exponential-fuer-cms-entwickler.md)
- [Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md)
- [Umstieg von Smarty](../template-sprache/ein-template-von-smarty-umziehen.md): Wenn Sie Smarty kennen, wird Ihnen die Template-Sprache vertraut vorkommen
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
