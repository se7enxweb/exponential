---
title: "Suche: die Datenbank-Engine und Solr"
audience: dxp-developer
category: dxp-development
order: 31
summary: Die zwei Suchmaschinen der DXP - die Standard-Engine auf der Datenbank und Solr 8 - wie Sie wählen, wie Sie mit SEARCH_ENGINE umstellen, wie Sie den Solr-Core ohne Konsolenbefehl anlegen und was die Volltextsuche auf SQLite kann und nicht kann.
keywords: Suche, Suchmaschine, SEARCH_ENGINE, legacy, Solr, ibexa/solr, ibexa/core-search, ibexa/search, Solr-Core, SOLR_DSN, Volltext, Neuindexierung, search, search engine, Solr core, full text, reindex
type: help
---
# Suche: die Datenbank-Engine und Solr

Diese Seite richtet sich an Entwickler, die die Suchmaschine eines DXP-Projekts wählen und betreiben. Die DXP liefert die
Suchmaschine auf der Datenbank (in der Konfiguration `legacy` genannt) und die Solr-Engine (`ibexa/solr`) mit. Ihr Code
verwendet mit beiden dieselbe Such-API.

## Inhalt

1. [Eine Engine wählen](#eine-engine-wählen)
2. [Auf Solr umstellen](#auf-solr-umstellen)
3. [Zurückstellen](#zurückstellen)
4. [Suchen aus Code und Templates](#suchen-aus-code-und-templates)
5. [Grenzen, die Sie kennen sollten](#grenzen-die-sie-kennen-sollten)

## Eine Engine wählen

| Engine | Einstellung | Gut für |
|---|---|---|
| Datenbank (Standard) | `SEARCH_ENGINE=legacy` | kleine und mittlere Websites, Entwicklung, kein zusätzlicher Dienst |
| Solr 8 (8.11.x empfohlen) | `SEARCH_ENGINE=solr` | große Inhaltsmengen, Relevanz-Ranking, Facetten, starke Volltextnutzung |

Der Name `legacy` bedeutet "die Suchmaschine, die die Datenbank abfragt"; er hat nichts mit dem Kernel von
Exponential 6 zu tun.

## Auf Solr umstellen

Aus der Anleitung des Skeletons (Abschnitt 18):

1. Setzen Sie `SEARCH_ENGINE=solr`, `SOLR_DSN` und `SOLR_CORE` in `.env.local`.
2. `php bin/console cache:clear`.
3. Legen Sie den Core mit der eigenen HTTP-API von Solr an ("no console command exists in v5"):

   ```bash
   curl "http://localhost:8983/solr/admin/cores?action=CREATE&name=default&configSet=exponential"
   ```

   Das in der Anfrage genannte Config Set muss auf Ihrem Solr-Server existieren.
4. Indexieren Sie alles: `php bin/console exponential:reindex` ([Neu indexieren](neu-indexieren.md)).

## Zurückstellen

```bash
SEARCH_ENGINE=legacy
php bin/console cache:clear
```

## Suchen aus Code und Templates

Injizieren Sie in PHP `Ibexa\Contracts\Core\Repository\SearchService` und führen Sie eine Abfrage mit Kriterien und
Sortierklauseln aus; dieselbe Abfrage läuft auf beiden Engines. Für Listen in Templates führt der Feldtyp
Inhaltsabfrage eine Abfrage als Feld aus ([Die mitgelieferten Feldtypen](die-mitgelieferten-feldtypen.md#strukturierte-felder)).

## Grenzen, die Sie kennen sollten

- Auf SQLite gibt es keinen FULLTEXT-Index; die Volltextsuche fällt auf `LIKE`-Abfragen zurück
  ([SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md#grenzen)).
- Eine Änderung der durchsuchbaren Felder eines Inhaltstyps braucht eine Neuindexierung.
- Planen Sie mit Solr den Solr-Server wie jeden anderen Dienst ein: Überwachung, Sicherungen seiner Konfiguration und
  eine Neuindexierung nach seiner Wiederherstellung.

## Verwandte Seiten

- [Neu indexieren](neu-indexieren.md)
- [Umgebungsvariablen](umgebungsvariablen.md#suche-cache-e-mail-und-der-rest)
- [Suchen in Exponential 6](../suchen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
