---
title: Such-Backends
audience: hosting, admin
category: hosting
order: 190
summary: Die Suchoptionen, die ein Hoster für Exponential-Websites anbieten kann - die eingebaute Datenbanksuche mit ihren Grenzen und der verzögerten Indizierung, der Neuaufbau des Index, die optionale Solr-Suche über die Erweiterung eZ Find, der Befehl exp:solr und settings/solr.ini, und was der Betrieb jeweils kostet.
keywords: Suche, Such-Backend, eZSearchEngine, eingebaute Suche, DelayedIndexing, indexcontent, ezpending_actions, updatesearchindex, Solr, eZ Find, ezfind, solr.ini, exp:solr, SolrSettings, Enabled, Url, BinaryPath, LogSearchStats, personenbezogene Daten, Hosting, Zusatzoption, search, search backend, built-in search, personal data, hosting, add-on
type: help
---
# Such-Backends

Diese Seite ist für Produktmanager und Systemadministratoren bei einem Hosting-Anbieter, die entscheiden, ob
Exponential-Websites nur die eingebaute Suche oder einen optionalen Solr-Suchserver bekommen, und die das eine oder das
andere betreiben. Sie beantwortet: wie die eingebaute Suche funktioniert und was sie braucht, wann indiziert wird, wie
Sie den Index neu aufbauen und was Solr über die Erweiterung eZ Find umfasst. Quellen:
[Kapitel 10.8](../../../../en/us/install/10-after-installing.md#108-search-indexing-and-image-handling),
`settings/site.ini [SearchSettings]`, `settings/solr.ini` und
[Befehle zur Serversteuerung](../../../../en/us/features/6.0/web-server-and-solr-commands.md).

## Inhalt

1. [Die zwei Optionen](#die-zwei-optionen)
2. [Die eingebaute Suche](#die-eingebaute-suche)
3. [Verzögerte Indizierung und Cron](#verzögerte-indizierung-und-cron)
4. [Den Index neu aufbauen](#den-index-neu-aufbauen)
5. [Solr über eZ Find](#solr-über-ez-find)
6. [Der Befehl exp:solr](#der-befehl-expsolr)
7. [Suche in einem Tarif anbieten](#suche-in-einem-tarif-anbieten)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Die zwei Optionen

| | Eingebaute Suche | Solr über eZ Find |
|---|---|---|
| Engine | `eZSearchEngine` (`site.ini [SearchSettings] SearchEngine`) | die Erweiterung `ezfind` mit einem Solr-Server |
| Der Index liegt in | der Datenbank der Website | dem Solr-Server |
| Zusätzlich zu betreibender Dienst | keiner | ein Solr-Server, lokal oder entfernt |
| Standardmäßig installiert | ja | nein: `composer.json` schlägt `se7enxweb/ezfind` vor |
| Auf MongoDB | führen Sie die Cronjobs nach der Installation einmal aus, um den Index aufzubauen | in diesem Repository nicht angegeben |

## Die eingebaute Suche

Die eingebaute Engine hält ihren Index in der Datenbank und braucht daher nichts über die Website selbst hinaus. Ihre
Grenzen, aus `settings/site.ini [SearchSettings]`:

| Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|
| `MaximumSearchLimit` | `30` | die meisten Treffer, die eine Anfrage verlangen darf |
| `MaximumSearchTextWords`, `MaximumSearchTextLength` | `16`, `1024` | längere Suchtexte werden abgeschnitten |
| `AllowEmptySearch` | `disabled` | leere Suchen kosten viele Ressourcen |
| `EnableWildcard` | `false` | die Suche mit Platzhaltern kann die Leistung stark beeinträchtigen |
| `LogSearchStats` | `disabled` | die Statistik speichert jede Phrase, die ein Besucher eingegeben hat, was personenbezogene Daten enthalten kann |

> **Hinweis**
> Lassen Sie `LogSearchStats` aus, es sei denn, ein Kunde braucht die Statistik und hat die personenbezogenen Daten
> bedacht, die sie enthält. Dies ist keine Rechtsberatung; Kunden müssen selbst klären, was sie speichern dürfen.

## Verzögerte Indizierung und Cron

Inhalte werden beim Veröffentlichen indiziert, es sei denn, `[SearchSettings] DelayedIndexing` ist `enabled` (oder
`classbased` mit `DelayedIndexingClassList[]`): Dann stellt das Veröffentlichen das Objekt nur in die Tabelle
`ezpending_actions`, und `indexcontent.php` aus dem Standard-Cron-Teil indiziert es später. Das Veröffentlichen großer
Objekte geht so schneller.

Für einen Hoster bedeutet das: **Eine Website mit verzögerter Indizierung hängt von Cron ab.** Ohne die Cronjobs
erscheinen neue Inhalte nie in der Suche. Stellen Sie sicher, dass der Standard-Cron-Teil für jede solche Website läuft
([Cronjobs](../betrieb/cronjobs.md)).

## Den Index neu aufbauen

Nach einem Import, einem Datenbankumzug oder wenn Suchergebnisse falsch sind:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user      # --clean removes the old index first
```

Führen Sie es als Benutzer der Website aus, und auf einer großen Website in einer ruhigen Stunde. Mit eZ Find indizieren
Sie mit den eigenen Werkzeugen der Erweiterung neu ([Kapitel 14](../../../../en/us/install/14-migrating-from-4x.md)).

## Solr über eZ Find

eZ Find ist laut seinem Vorschlag in `composer.json` eine auf Solr basierende erweiterte Suchmaschine für Exponential mit
viel mehr Funktionen als die eingebaute Suche. Sie fügen es einer Website so hinzu:

```bash
composer require se7enxweb/ezfind:~3.0.7
php bin/php/ezpgenerateautoloads.php -e
```

dann `ActiveExtensions[]=ezfind` in `settings/override/site.ini.append.php`, leeren Sie den INI-Cache und starten Sie
Velocity neu, wenn es die Website ausliefert ([Kapitel 3.5](../../../../en/us/install/03-getting-the-code.md#35-extensions-are-composer-packages)).
Dieses Repository gibt nicht an, welche Solr-Versionen eZ Find braucht; entnehmen Sie das dem eigenen Repository der
Erweiterung, bevor Sie Solr anbieten, statt es anzunehmen.

`settings/solr.ini` sagt der Installation, wo Solr ist. Solr ist aus, bis es eingeschaltet wird:

```ini
# settings/override/solr.ini.append.php
[SolrSettings]
Enabled=true
# a running Solr, local or remote, base URL with scheme and port
Url=http://127.0.0.1:8983
# or, for a Solr installed on this machine and managed by exp:solr:
#BinaryPath=/opt/solr/bin/solr
#Host=127.0.0.1
#Port=8983
#DataDir=
```

| Schlüssel | Mitgeliefert | Bedeutung |
|---|---|---|
| `Enabled` | `false` | Hauptschalter |
| `Url` | leer | ein laufendes Solr; wenn gesetzt, gilt Solr als anderswo verwaltet |
| `BinaryPath` | leer | die Steuerbinärdatei von Solr einer lokalen Instanz (genutzt, wenn `Url` leer ist) |
| `Host`, `Port` | `127.0.0.1`, `8983` | wo ein lokales Solr lauscht |
| `DataDir` | leer | der eigene Standard von Solr gilt |
| `RunDir`, `LogDir` | `var/vc/solr/run`, `var/vc/solr/log` | PID- und Logdateien |
| `StartTimeout` | `60` | Sekunden, die `start` auf eine Antwort von Solr wartet |

## Der Befehl exp:solr

```bash
php bin/php/console exp:solr status --allow-root-user
php bin/php/console exp:solr ping --allow-root-user
php bin/php/console exp:solr start --allow-root-user      # local instance only
```

| Verb | Was es tut |
|---|---|
| `status` | ob Solr konfiguriert ist und, falls ja, ob es antwortet |
| `start`, `stop`, `restart` | führen das eigene Starten und Stoppen von Solr für eine lokale Instanz aus (`BinaryPath`); mit einer entfernten `Url` melden sie, dass es anderswo verwaltet wird |
| `ping` | Solr fragen, ob es läuft |

Solange Solr nicht konfiguriert ist, sagt jedes Verb das, und `start` endet mit einem Wert ungleich null, sodass eine
Überwachungsprüfung „nicht konfiguriert“ nie für „läuft“ hält ([Befehle zur Serversteuerung](../../../../en/us/features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)).

## Suche in einem Tarif anbieten

Optionen, aus denen ein Hoster wählen kann:

- **Nur die eingebaute Suche.** Nichts zusätzlich zu betreiben; passt zu den meisten Websites.
- **Solr als Zusatzoption**, auf einem Solr-Server, den der Hoster für mehrere Kunden betreibt (`Url` gesetzt), oder auf
  dem dedizierten Server des Kunden (`BinaryPath`). Planen Sie seinen Speicher und seine Festplatte getrennt von PHP, und
  sichern Sie seinen Index oder können Sie ihn neu aufbauen.
- **Vom Kunden bereitgestelltes Solr.** `Url` zeigt darauf; `exp:solr status` und `ping` prüfen es, Starten und Stoppen
  sind Sache des Kunden.

> **Tipp**
> Was immer Sie anbieten: Nehmen Sie `exp:solr status` für Websites mit Solr in die Überwachung auf, und eine Prüfung,
> dass die Cronjobs laufen, für Websites mit verzögerter Indizierung.

## Checkliste

- [ ] Jede Website kennt ihre Engine: `SearchEngine` und, für eZ Find, `[SolrSettings] Enabled`.
- [ ] Cron führt den Standard-Teil aus, wo `DelayedIndexing` an ist.
- [ ] `LogSearchStats` aus, es sei denn, ein Kunde hat darum gebeten.
- [ ] Speicher, Festplatte und Backups von Solr geplant, wenn es angeboten wird.
- [ ] `exp:solr status` in der Überwachung für Solr-Websites.

## Verwandte Seiten

- [Kapitel 10.8: Suchindizierung und Bildverarbeitung](../../../../en/us/install/10-after-installing.md#108-search-indexing-and-image-handling)
- [Suchen](../../../../en/us/help/searching.md) (für Redakteure)
- [Cronjobs](../betrieb/cronjobs.md) und [Zustandsprüfungen und Verfügbarkeit](../betrieb/zustandspruefungen-und-verfuegbarkeit.md)
- [Befehle zur Serversteuerung](../../../../en/us/features/6.0/web-server-and-solr-commands.md)
- [Composer und Git auf Servern](composer-und-git-auf-servern.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
