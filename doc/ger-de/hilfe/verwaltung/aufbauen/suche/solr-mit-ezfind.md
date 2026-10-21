---
title: Solr mit ezfind
audience: government, admin, website-creator
category: public-sector
order: 60
summary: Wann eine Behörde ihre Site-Suche mit der Erweiterung ezfind auf Solr umstellen sollte, was Solr bringt (Relevanz, Facetten, Cores je Sprache, Autovervollständigung, Rechtschreibprüfung), was der Betrieb kostet und die Schritte zum Einschalten mit exp:solr.
keywords: Solr, Facetten, Relevanz, Autovervollständigung, Rechtschreibprüfung, Suchserver, ezfind, eZ Find, facets, relevance, exp:solr, solr.ini, SearchEngine ezsolr, autocomplete, spell check, MoreLikeThis, search server
type: help
---
# Solr mit ezfind

Diese Seite richtet sich an Administratoren und Site-Ersteller, deren Site der eingebauten Suche entwächst. Die
Erweiterung `ezfind` verbindet Exponential mit einem Apache-Solr-Server; Solr ist optional, und nichts ändert sich,
bis Sie es einschalten
([Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)).

## Inhalt

1. [Was Solr bringt](#was-solr-bringt)
2. [Was es kostet](#was-es-kostet)
3. [Einschalten](#einschalten)
4. [Wissenswerte Einstellungen](#wissenswerte-einstellungen)

## Was Solr bringt

| Funktion | Einstellung oder Quelle in ezfind |
|---|---|
| Relevanzsortierung und Gewichtung | `ezfind.ini [IndexBoost]`, `[QueryBoost]` |
| Facetten (Ergebnisse nach Klasse, Schlagwort, Datum filtern) | `[FacetSettings]`, die Fetch-Funktionen der Erweiterung |
| Verhalten je Sprache | `[LanguageSearch] SearchMainLanguageOnly`, `MultiCore`, `LanguagesCoresMap` |
| Autovervollständigung | `[AutoCompleteSettings]` (aus, sofern nicht eingeschaltet; zusätzliche Last) |
| Rechtschreibprüfung | `[SpellCheck] SpellCheck=enabled` |
| „Ähnliche Inhalte“ | `[MoreLikeThis]` |
| Hervorhebung von Treffern im Text | `[HighLighting]` |
| Hervorhebung gewählter Ergebnisse | das Modul `ezfind/elevate` (siehe „Hervorhebung und Synonyme“) |

## Was es kostet

Solr ist ein eigener Java-Server, der installiert, betrieben, überwacht, abgesichert und gesichert werden muss; planen
Sie Arbeitsspeicher dafür ein und einen Betreuer, der ihn kennt. Für eine kleine Site genügt die eingebaute
Suchmaschine.

## Einschalten

1. Installieren Sie einen Solr-Server (lokal mit `BinaryPath` oder entfernt mit `Url`).
2. Setzen Sie `[SolrSettings] Enabled=true` und die Adresse in `settings/override/solr.ini.append.php`.
3. Prüfen Sie: `./console exp:solr status`, dann `./console exp:solr ping`.
4. Aktivieren Sie `ezfind`; seine `site.ini.append.php` schaltet `[SearchSettings] SearchEngine` auf `ezsolr` um.
5. Leeren Sie den INI-Cache und bauen Sie den Index auf: `php bin/php/updatesearchindex.php --clean`.

`exp:solr` hat die Aktionen `status`, `start`, `stop`, `restart` und `ping`
([Befehle zur Serversteuerung](../../../../../eng-us/features/6.0/web-server-and-solr-commands.md)).

## Wissenswerte Einstellungen

- `SearchMainLanguageOnly=enabled` (mitgeliefert) liefert Ergebnisse nur in der ersten Sprache des Siteaccess.
- `AutoComplete` wird am besten je Siteaccess eingeschaltet, wo es verwendet wird.
- Mit verzögerter Indizierung reihen Verschiebungen und Änderungen der Sichtbarkeit Aktionen für Solr ein; halten Sie
  den Indizierungs-Cronjob am Laufen.

## Verwandte Seiten

- [Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)
- [Such-Backends beim Hosting](../../../hosting/grundlagen/such-backends.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
