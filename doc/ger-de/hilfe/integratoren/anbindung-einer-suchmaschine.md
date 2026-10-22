---
title: Anbindung einer Suchmaschine
audience: integrator, developer, admin
category: integration
order: 240
summary: Wie die Website-Suche in Exponential verdrahtet ist und wie Sie einen externen Suchdienst anbinden - die eingebaute Engine, die Plugin-Schnittstelle ezpSearchEngine, Solr über ezfind und exp:solr, verzögerte Indizierung, Neuindizierung nach Importen, einen externen Index aus Veröffentlichungs-Hooks speisen und Suche über die APIs.
keywords: Suche, Suchmaschine, Such-Plugin, verzögerte Indizierung, Neuindizierung, externe Suche, search, search engine, eZSearchEngine, ezpSearchEngine, SearchSettings, SearchEngine, search plugin, Solr, ezfind, exp:solr, solr.ini, updatesearchindex, DelayedIndexing, indexcontent, external search, Elasticsearch, OpenSearch, expsearch
type: help
---
# Anbindung einer Suchmaschine

Diese Seite richtet sich an Integratoren, die die Website-Suche verbessern oder ersetzen oder Inhalte in einen
Suchdienst einspeisen müssen, den der Kunde bereits betreibt. Die Suche in Exponential ist eine austauschbare Engine:
Der Kernel ruft `eZSearch` auf, das jedes Hinzufügen, Entfernen und jede Abfrage an die Engine weitergibt, die in
`site.ini [SearchSettings] SearchEngine` genannt ist.

## Inhalt

1. [Die Teile](#die-teile)
2. [Option A: die eingebaute Engine](#option-a-die-eingebaute-engine)
3. [Option B: Solr über ezfind](#option-b-solr-über-ezfind)
4. [Option C: ein eigenes Such-Plugin](#option-c-ein-eigenes-such-plugin)
5. [Option D: ein externer Index neben der Website-Suche](#option-d-ein-externer-index-neben-der-website-suche)
6. [Indizieren nach Importen](#indizieren-nach-importen)
7. [Suche über die APIs](#suche-über-die-apis)

## Die Teile

| Teil | Wo |
|---|---|
| Die Einstellung der Engine | `site.ini [SearchSettings] SearchEngine=eZSearchEngine` |
| Die Schnittstelle, die jede Engine umsetzt | `ezpSearchEngine` (`kernel/private/interfaces/ezpsearchengine.php`): `addObject()`, `removeObject()`, `removeObjectById()`, `search()`, `supportedSearchTypes()`, `needCommit()`, `needRemoveWithUpdate()`, `commit()` |
| Wo Engines gesucht werden | `kernel/search/plugins/<name>/<name>.php`, dann `extension/<ext>/search/plugins/<name>/<name>.php` für jede Erweiterung in `[SearchSettings] ExtensionDirectories[]` (`eZSearch::getEngine()`) |
| Verzögerte Indizierung | `[SearchSettings] DelayedIndexing` (`disabled`, `enabled` oder `classbased` mit `DelayedIndexingClassList[]`); das globale Cronjob-Skript `indexcontent.php` indiziert, was vorgemerkt wurde |
| Vollständige Neuindizierung | `php bin/php/updatesearchindex.php` (`--clean` leert zuerst den Index) |

## Option A: die eingebaute Engine

`eZSearchEngine` indiziert Wörter in Datenbanktabellen. Sie braucht nichts weiter und genügt für kleine und mittlere
Websites. Statistiken zu Suchbegriffen sind standardmäßig aus (`LogSearchStats=disabled`), weil sie personenbezogene
Daten enthalten können.

## Option B: Solr über ezfind

Die Erweiterung `ezfind` verbindet die Website-Suche mit Apache Solr (Facetten, Abstimmung der Relevanz, der Inhalt von
Dateien). Sie steht **nicht** in der `require`-Liste der `composer.json` im Wurzelverzeichnis; installieren Sie sie
separat und prüfen Sie ihren Zustand, bevor Sie sie zusagen. Der Kernel-Befehl `exp:solr` steuert einen Solr-Server,
sobald `solr.ini [SolrSettings] Enabled=true` gesetzt ist:

```bash
php bin/php/console exp:solr status --allow-root-user
php bin/php/console exp:solr ping --allow-root-user
```

Lesen Sie [Suche mit ezfind und Solr](../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md) und
[Befehle zur Serversteuerung](../../funktionen/6.0/befehle-fuer-webserver-und-solr.md#expsolr-ein-optionaler-suchserver).

## Option C: ein eigenes Such-Plugin

Damit die Website-Suche einen anderen Dienst verwendet (OpenSearch, Elasticsearch, eine SaaS-Suche), schreiben Sie in
Ihrer Erweiterung eine Klasse, die `ezpSearchEngine` umsetzt:

```text
extension/myintegration/search/plugins/myintegrationsearch/myintegrationsearch.php   class myIntegrationSearch
```

```ini
# settings/override/site.ini.append.php
[SearchSettings]
SearchEngine=myIntegrationSearch
ExtensionDirectories[]=myintegration
```

Verzeichnis- und Dateiname sind der Name der Engine in Kleinbuchstaben; der Klassenname ist der Wert der Einstellung.
Alles, was die Suche aufruft (die Suchseite, `fetch( 'content', 'search', ... )`, die `expsearch`-Dienste), verwendet
dann Ihre Engine, und jedes Veröffentlichen und Entfernen ruft `addObject()` und `removeObject()` auf. Das ist die
tiefste Integration und das größte Stück Arbeit: Die Templates der Suchseite erwarten die Ergebnisstruktur der
eingebauten Engine.

## Option D: ein externer Index neben der Website-Suche

Oft muss die Unternehmenssuche des Kunden Inhalte nur **empfangen**, während die Website ihre eigene Suche behält.
Ersetzen Sie dann nicht die Engine: Senden Sie Veröffentlichungen mit einer Outbox an den externen Index
([Workflows und Trigger als Hooks](workflows-und-trigger-als-hooks.md#das-outbox-muster-schnell-vermerken-später-senden)),
Entfernungen mit dem Audit-Webhook oder derselben Outbox, und lassen Sie ein Cronjob-Teil sie zustellen. Ein
vollständiges Neuladen ist ein serverseitiger Export ([Einen Export schreiben](einen-export-oder-ausgehenden-abgleich-schreiben.md)).

## Indizieren nach Importen

| Situation | Tun Sie |
|---|---|
| Ein Import hat Objekte über `eZContentFunctions` angelegt | Nichts: Das Veröffentlichen indiziert sie (oder merkt sie bei verzögerter Indizierung vor) |
| Die verzögerte Indizierung ist an | Sorgen Sie dafür, dass der globale Cronjob-Teil läuft (`indexcontent.php`) |
| Ein Paket wurde mit `ezpm.php` installiert | Der Installer verzögert die Indizierung: Führen Sie `php bin/php/updatesearchindex.php` aus |
| Daten wurden außerhalb der Veröffentlichungsoperation geändert | Führen Sie `php bin/php/updatesearchindex.php` aus; besser, ändern Sie Daten nicht auf diese Weise |

## Suche über die APIs

Die REST-API hat keine Suchroute. `expservices` hat `expsearch::search`, `count`, `facets`, `byClass`, `inSubtree`,
`bySection`, `byAttribute`, `suggest` und `engine` (welche Engine verwendet wird), jeweils gegen `content/read` geprüft
([Entfernte Dienste mit expservices](entfernte-dienste-mit-expservices.md)).

## Verwandte Seiten

- [Such-Backends für Hoster](../hosting/grundlagen/such-backends.md)
- [Suche mit ezfind und Solr](../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)
- [Suchen](../suchen.md)
- [Caching nach Importen](caching-nach-importen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
