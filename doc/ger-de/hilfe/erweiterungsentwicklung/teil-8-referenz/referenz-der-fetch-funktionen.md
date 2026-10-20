---
title: Eingebaute Fetch-Funktionen nach Modul
audience: developer, designer
category: extension-development
order: 290
summary: Jede Fetch-Funktion, die die Module des Kernels deklarieren, aus deren function_definition.php aufgelistet, mit den meistverwendeten und dem Verhältnis von fetch( module, function, hash() ) zu PHP.
keywords: Fetch-Funktionen, Module, Inhaltsliste, Knoten, Teilbaum, Benutzer, Zugriff, Fetch-Alias, fetch functions, fetch, function_definition.php, content list, content node, content tree, user has_access_to, current_user, section, class list, shop basket, setup version, fetch_alias
type: help
---
# Eingebaute Fetch-Funktionen nach Modul

Teil 8, Referenz 25. Eine Fetch-Funktion ist eine Leseoperation, die ein Modul Templates anbietet:
`fetch( 'content', 'list', hash( 'parent_node_id', 2 ) )`. Jedes Modul deklariert seine Fetch-Funktionen in
`function_definition.php`; diese Seite führt die des Kernels auf, im Oktober 2026 aus diesen Dateien gelesen. Wie Sie
eigene schreiben, steht in [Fetch-Funktionen](../teil-3-erweiterungsentwickler/fetch-funktionen.md); wie Sie sie in
Templates verwenden, steht in [Fetch für Builder](../teil-2-builder/fetch-fuer-builder.md) und in der Hilfe für
Designer ([Grundlagen von fetch](../../../../eng-us/help/designers/templates/fetch-basics.md)).

## Die meistverwendeten

| Aufruf | Gibt zurück |
|---|---|
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | einen Knoten |
| `fetch( 'content', 'object', hash( 'object_id', 57 ) )` | ein Objekt |
| `fetch( 'content', 'list', hash( 'parent_node_id', 2, 'limit', 10, 'sort_by', array( 'published', false() ) ) )` | die Kinder eines Knotens |
| `fetch( 'content', 'list_count', hash( 'parent_node_id', 2 ) )` | wie viele |
| `fetch( 'content', 'tree', hash( 'parent_node_id', 2, 'class_filter_type', 'include', 'class_filter_array', array( 'article' ) ) )` | einen Teilbaum |
| `fetch( 'user', 'current_user' )` | den aktuellen Benutzer |
| `fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'edit' ) )` | ob der Benutzer es tun darf |
| `fetch( 'content', 'class', hash( 'class_id', 'article' ) )` | eine Klasse |

Die Listenparameter sind die kleingeschriebenen Formen der Parameter von `eZContentObjectTreeNode::subTreeByNodeID()`
([eZContentObjectTreeNode](ezcontentobjecttreenode.md)).

## Alle Module

| Modul | Fetch-Funktionen |
|---|---|
| content | object, version, node, locale_list, locale, prioritized_languages, prioritized_language_codes, translation_list, language_states, non_translation_list, class, class_attribute_list, class_attribute, calendar, list, list_count, tree, tree_count, search, trash_count, trash_object_list, draft_count, draft_version_list, pending_count, pending_list, version_count, version_list, can_instantiate_class_list, class_list, can_instantiate_classes, contentobject_attributes, bookmarks, bookmark_rows, bookmark_folders, recent, section_list, tipafriend_top_list, view_top_list, collected_info_count, collected_info_count_list, collected_info_collection, collected_info_list, object_by_attribute, object_count_by_user_id, same_classattribute_node |
| user | current_user, is_logged_in, logged_in_count, anonymous_count, logged_in_list, logged_in_users, user_role, member_of, has_access_to, can_open, account_status, deactivation_offers |
| section | object, list, object_list, object_list_count, roles, user_roles |
| class | list, list_by_groups, latest_list, attribute_list, override_template_list |
| role | role, policies, policy_count |
| search | list, list_count |
| url | list, list_count |
| infocollector | collected_info_count, collected_info_count_list, collected_info_collection, collected_info_list |
| shop | basket, best_sell_list, related_purchase, wish_list, wish_list_count, current_wish_list, order, order_status_history_count, order_status_history, currency_list, currency, preferred_currency_code, user_country, product_category_list, product_category |
| package | list, list_count, maintainer_role_list, can_create, can_edit, can_import, can_install, can_export, content_export_access, can_read, can_list, can_remove, item, dependent_list, repository_list |
| notification | handler_list, digest_handlers, digest_items, event_content, subscribed_nodes, subscribed_nodes_count |
| collaboration | participant, participant_list, participant_map, message_list, item_list, inbox, inbox_row, item_count, group_tree, tree_count |
| setup | version, alias, major_version, minor_version, release, state, is_development, database_version, database_release, edition, extension_counts, cronjob_pauses, log_files |
| audit | events, count, event, chain_status, can_read, hidden_menu_links |
| apikey | can_create, counts |
| rss | has_export_by_node, export_by_node |
| workflow | workflow_statuses, workflow_type_statuses |
| layout | sitedesign_list |
| uiswitch | toolbar |
| exphelp | context, license, context_menu |

Erweiterungen fügen die Fetch-Funktionen ihrer eigenen Module hinzu; die `function_definition.php` jedes Moduls ist
für dessen Parameter maßgeblich.

## In PHP

Eine Fetch-Funktion ist eine Methode der Funktionssammlungsklasse des Moduls, die in `function_definition.php`
genannt ist. Rufen Sie aus PHP direkt die zugrunde liegende Kernel-Klasse auf (für Inhalte
`eZContentObjectTreeNode::subTreeByNodeID()` und verwandte Methoden), statt den Weg über die Template-Schicht zu gehen.

## Fetch-Aliase

`fetch_alias( 'name', hash( ... ) )` ruft eine in `fetchalias.ini` konfigurierte Fetch-Funktion auf, sodass eine
Website ändern kann, was ein benannter Fetch tut, ohne Templates zu bearbeiten.

## Verwandte Seiten

- Zurück: [Eingebaute Template-Funktionen](referenz-der-template-funktionen.md)
- Weiter: [INI-Dateien, denen ein Erweiterungsautor begegnet](ini-dateien-fuer-erweiterungsautoren.md)
- [Fetch-Funktionen](../teil-3-erweiterungsentwickler/fetch-funktionen.md)
- [Leistung von fetch](../../../../eng-us/help/designers/templates/fetch-performance.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
