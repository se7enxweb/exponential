---
title: Umstieg von Drupal
audience: cms-developer
category: cms-development
order: 5
summary: Für Drupal-Entwickler, wie sich Inhaltstypen und Felder, Nodes, Taxonomie, Views, Blöcke, Module und Hooks, Twig-Themes, Konfigurationsverwaltung und Drush auf Exponential übertragen. Die Seite behandelt auch die falschen Freunde, auf die Sie achten sollten, allen voran das Wort "node".
keywords: Drupal, Umstieg von Drupal, Inhaltstyp, Feld, Entity, Node, Knoten, Taxonomie, Vokabular, Views, Blöcke, Paragraphs, Module, Hooks, Twig, Konfigurationsverwaltung, Drush, settings.php, Migration, coming from Drupal, content type, configuration management
type: help
---
# Umstieg von Drupal

Diese Seite richtet sich an Entwickler, die Drupal kennen und ein Exponential-Projekt beginnen. Drupal-Entwickler
denken bereits in Inhaltstypen und Feldern, daher wird sich das Inhaltsmodell vertraut anfühlen; die Unterschiede
liegen in den Begriffen, in der Art, wie Templates gewählt werden, und darin, wo die Konfiguration liegt. Diese Seite
vergleicht nur Begriffe und bewertet die beiden Systeme nicht.

## Inhalt

1. [Falsche Freunde](#falsche-freunde)
2. [Begriff für Begriff](#begriff-für-begriff)
3. [Views und Blöcke](#views-und-blöcke)
4. [Module und Hooks](#module-und-hooks)
5. [Themes und Twig](#themes-und-twig)
6. [Konfiguration und Drush](#konfiguration-und-drush)
7. [Eine Drupal-Website umziehen](#eine-drupal-website-umziehen)

## Falsche Freunde

| Wort | In Drupal | In Exponential |
|---|---|---|
| **node** | eine Inhaltsentität: ein Artikel, eine Seite | eine **Platzierung** eines Inhaltsobjekts im Baum (Knoten); der Inhalt selbst ist das **Objekt** |
| **module** | ein Zusatzpaket | eine Gruppe von Seiten (Views) unter einem URL-Präfix, etwa `content` oder `user`; Zusätze sind **Erweiterungen** |
| **view** | eine konfigurierte Abfrage mit Darstellung (Views) | eine Seite eines Moduls (`content/view`) und ein **Ansichtsmodus** wie `full` oder `line` |
| **block** | ein platzierbares Ausgabestück in einer Region | ein Block von Exponential Layouts, platziert in einer Zone eines Layouts |
| **section** | (kein Kernbegriff) | eine Kennzeichnung an Inhalten, die Berechtigungen und Template-Regeln verwenden (Sektion) |

Behalten Sie die erste Zeile im Kopf: In Exponential ist `$node` in einem Template der Platz im Baum, und
`$node.object` ist der Inhalt ([Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md)).

## Begriff für Begriff

| In Drupal | In Exponential | Lesen |
|---|---|---|
| Inhaltstyp (Node-Bundle) | Inhaltsklasse | [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md) |
| Feld und Feldtyp | Klassenattribut und Datentyp | [Datentypen wählen](datentypen-waehlen.md) |
| Entity-Reference-Feld | `ezobjectrelationlist` | [Relationen und Einbettungen](relationen-und-einbettungen.md) |
| Paragraphs | Unterobjekte kleiner Klassen oder Blöcke von Layouts | [Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md) |
| Taxonomie-Vokabular und Begriffe | `eztags` oder Ordner im Baum | [Datentypen wählen](datentypen-waehlen.md) |
| Menülinks, Buchgliederung | der Inhaltsbaum und seine Sortierung | [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md) |
| Revisionen und Content Moderation | Versionen, Objektzustände, Freigabe-Workflows | [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md) |
| Inhaltsübersetzung | Übersetzungen desselben Objekts | [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md) |
| Rollen und Berechtigungen | Rollen mit Richtlinien und Einschränkungen | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md) |
| Pfad-Aliase, Weiterleitungen | URL-Aliase, URL-Platzhalter (Wildcards) | [URL-Aliase und Routing](url-aliase-und-routing.md) |
| Webform | Informationssammlung | [Formulare und Informationssammlung](formulare-und-informationssammlung.md) |
| Multisite | Siteaccesses in einer Installation und einer Datenbank | [Siteaccesses und Multisite](siteaccesses-und-multisite.md) |
| JSON:API, REST | die REST-Schnittstelle von `ezprestapi`, `expservices` | [Die REST-API](die-rest-api.md) |

## Views und Blöcke

Ein Drupal-View ist Konfiguration, die eine Abfrage und eine Darstellung aufbaut. In Exponential ist die Abfrage eine
**Fetch-Funktion**, die in einem Template aufgerufen wird (`fetch( 'content', 'list', hash( ... ) )`), und die
Darstellung ist dieses Template ([Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md)). Redakteure, die
Listen selbst konfigurieren müssen, verwenden **Exponential Layouts**: einen Block mit einer Sammlung, die von Hand
oder durch eine Abfrage gefüllt wird, platziert in einer Zone eines Layouts, ohne Code
([Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md)). Beide Wege treffen sich in Template-Blöcken
([Zwei Wege zum Bauen](../zwei-wege-zum-bauen/zwei-wege-zum-bauen.md)).

## Module und Hooks

Ein Drupal-Modul implementiert Hooks oder abonniert Events. Eine Exponential-Erweiterung verwendet genaue
Erweiterungspunkte:

| Drupal-Gewohnheit | Exponential |
|---|---|
| `hook_entity_presave`, `hook_node_insert` | ein Workflow-Event am Auslöser für das Veröffentlichen oder ein Event-Listener ([Workflows und Auslöser](workflows-und-ausloeser.md)) |
| Eine Route und ein Controller | ein Modul mit einer View (`module.php`, ein View-Skript) ([Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md)) |
| Eine Berechtigung in `*.permissions.yml` | eine Richtlinienfunktion Ihres Moduls ([Richtlinienfunktionen und Zugriff](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)) |
| Ein Twig-Filter oder eine Twig-Funktion | ein Template-Operator oder eine Template-Funktion ([Template-Operatoren](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/template-operatoren.md)) |
| `hook_cron` | ein Cronjob-Skript in einem Cronjob-Teil ([Cronjobs](cronjobs.md)) |
| Ein Feldtyp-Plugin | ein Datentyp ([Datentypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datentypen.md)) |

## Themes und Twig

Drupal-Themes verwenden Twig mit Template-Vorschlägen wie `node--article--full.html.twig`. Exponential-Designs
verwenden eine eigene Template-Sprache (`.tpl`), und die "Vorschläge" sind ausdrückliche **Override-Regeln** in
`override.ini`, die auf Klasse, Knoten, übergeordneten Knoten, Sektion, Ansichtsmodus und mehr passen
([Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md)). Ansichtsmodi gibt es in
beiden Systemen mit derselben Idee: `full`, `line` und Ihre eigenen ([Ansichtsmodi](ansichtsmodi.md)). Designs fallen
durch eine Kette von Designs zurück, ganz ähnlich wie ein Basis-Theme
([Die Design-Kette](../erweiterungsentwicklung/teil-2-builder/die-design-kette.md)).

## Konfiguration und Drush

| Drupal | Exponential |
|---|---|
| `settings.php` | `settings/override/site.ini.append.php` und die Siteaccess-Ordner |
| Konfigurationsexport (YAML) | INI-Dateien in Ihrer Erweiterung unter Versionsverwaltung; Klassen und Inhalte in Paketen |
| `drush cr` | `php bin/php/ezcache.php --clear-all` oder gezielt `--clear-id` / `--clear-tag` |
| `drush`-Befehle | `./console` und `bin/php/*.php` |
| `drush config:get` / `config:set` | `./console exp:ini get` / `exp:ini set` |

Klassen liegen in der Datenbank, so wie die Inhaltstypen von Drupal in der aktiven Konfiguration liegen; um sie zu
versionieren, exportieren Sie sie in Paketen
([Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md#klassen-unter-versionsverwaltung)).

## Eine Drupal-Website umziehen

Die Migrationsanleitung behandelt JSON:API und die Migrate API, die Zuordnung, Paragraphs, Pfad-Aliase und
Weiterleitungen: [Drupal und Exponential](../../installation/migration/drupal.md). Die Lektion für Trainer ist
[Von Drupal und Joomla](../trainer/migration/modul-07-von-anderen-systemen/von-drupal-und-joomla.md), und die
Werkzeuge, die das Ergebnis importieren, stehen unter [Inhalte importieren](inhalte-importieren.md).

## Verwandte Seiten

- [Exponential für CMS-Entwickler](exponential-fuer-cms-entwickler.md)
- [Umstieg von WordPress](umstieg-von-wordpress.md), [Umstieg von TYPO3](umstieg-von-typo3.md)
- [Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
