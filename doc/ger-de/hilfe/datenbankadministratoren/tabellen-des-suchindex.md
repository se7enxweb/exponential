---
title: Die Tabellen des eingebauten Suchindex
audience: dba
category: database-administration
order: 36
summary: Wie die eingebaute Suchmaschine von Exponential ihren Index in der Datenbank hält - die Tabellen ezsearch_word, ezsearch_object_word_link und ezsearch_search_phrase, verzögerte Indizierung über ezpending_actions und indexcontent.php, den Index mit updatesearchindex.php neu aufbauen, Zahlen, die nicht in eine Ganzzahlspalte passen, Suchstatistik als personenbezogene Daten und wo Solr ins Bild passt.
keywords: Suche, Suchindex, eZSearchEngine, ezsearch_word, ezsearch_object_word_link, ezsearch_search_phrase, DelayedIndexing, ezpending_actions, index_object, index_moved_node, indexcontent.php, updatesearchindex.php, --clean, LogSearchStats, MAX_INT, clampInteger, Solr, ezfind, search, search index, delayed indexing
type: help
---
# Die Tabellen des eingebauten Suchindex

Diese Seite richtet sich an Datenbankadministratoren einer Website, die die eingebaute Suchmaschine von Exponential verwendet.
Diese Maschine hält ihren Index in der eigenen Datenbank der Website, ihre Tabellen gehören also zu den größten und
meistgenutzten. Diese Seite beantwortet: welche Tabellen den Index enthalten, wie Inhalte indiziert werden, wie Sie den Index
neu aufbauen und worauf Sie achten. Quellen: [10.8 Suche](../../installation/10-nach-der-installation.md#suche) und
[Eine Website betreiben, Zahlen im eingebauten Suchindex](../../anleitungen/betrieb-einer-website.md#zahlen-im-eingebauten-suchindex).

## Inhalt

1. [Die Maschine](#die-maschine)
2. [Die Tabellen](#die-tabellen)
3. [Wann Inhalte indiziert werden](#wann-inhalte-indiziert-werden)
4. [Den Index neu aufbauen](#den-index-neu-aufbauen)
5. [Große Zahlen](#große-zahlen)
6. [Suchstatistik ist personenbezogen](#suchstatistik-ist-personenbezogen)
7. [Stattdessen Solr](#stattdessen-solr)
8. [Checkliste](#checkliste)

## Die Maschine

Die eingebaute Maschine ist `eZSearchEngine` (`site.ini [SearchSettings] SearchEngine`). Sie funktioniert auf jeder
unterstützten Datenbank. Verwandte Objekte werden mit dem Objekt indiziert, das auf sie verweist, eine Ebene tief.

## Die Tabellen

| Tabelle | Enthält | Hinweise |
|---|---|---|
| `ezsearch_word` | eine Zeile je verschiedenem Wort, mit `object_count` | indiziert auf `word` und `object_count` |
| `ezsearch_object_word_link` | eine Zeile je Vorkommen eines Wortes in einem Objekt: Objekt, Klasse, Attribut, Wort, vorheriges und nächstes Wort, Position, Häufigkeit, `integer_value`, Sektion, veröffentlicht | die größte Suchtabelle; indiziert auf Objekt, Wort, Bezeichner, Häufigkeit, `integer_value` |
| `ezsearch_search_phrase` | Phrasen, nach denen Besucher gesucht haben, mit Zählern | nur gefüllt, wenn `LogSearchStats` eingeschaltet ist |

## Wann Inhalte indiziert werden

Beim Veröffentlichen, sofern `[SearchSettings] DelayedIndexing` nicht `enabled` ist (oder `classbased` mit
`DelayedIndexingClassList[]`). Mit verzögerter Indizierung reiht das Veröffentlichen die Arbeit nur in `ezpending_actions` ein
(`index_object`), und `indexcontent.php` aus dem Standard-Cronjob-Teil indiziert sie später, die ältesten zuerst, und entfernt
jede Zeile, wenn sie erledigt ist. Das macht das Veröffentlichen großer Objekte schneller.

```sql
SELECT action, COUNT(*), MIN(created) FROM ezpending_actions GROUP BY action;
```

Eine wachsende Zahl `index_object` mit einem alten `MIN(created)` bedeutet, dass die Cronjobs nicht laufen
([Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md)).

## Den Index neu aufbauen

Nach einem Import, einem Datenbankumzug, einer Wiederherstellung ohne die Suchtabellen oder wenn Ergebnisse falsch sind:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user
```

`--clean` entfernt vor dem Indizieren alle Suchdaten. Weitere Optionen aus seiner Hilfe: `--db-host`, `--db-user`,
`--db-password`, `--db-database`, `--db-driver` (`--db-type`), um in eine andere Datenbank zu indizieren, und `--sql`, um die
Abfragen anzuzeigen. Der Konsolenname ist `exp:updatesearchindex`. Ein Neuaufbau liest jedes Objekt und schreibt die beiden
großen Tabellen; führen Sie ihn außerhalb der Spitzenzeiten aus und rechnen Sie damit, dass die Datenbank währenddessen ihre
Logdateien vergrößert. Auf MongoDB führen Sie nach der Installation die Cronjobs einmal aus, um den Index aufzubauen
([MongoDB einrichten](mongodb-einrichten.md#den-suchindex-aufbauen)).

## Große Zahlen

Die Maschine speichert jedes Wort mit seinem Zahlenwert in `integer_value`, sodass eine Suche nach Zahlen filtern kann. Eine
sehr große Zahl in einem Text (eine 20-stellige Bestellnummer) bricht die Indizierung nicht mehr: Jeder Treiber kennt den
Bereich seiner Ganzzahlspalten (`$db::MAX_INT`, `$db::MIN_INT`: vorzeichenbehaftet 32 Bit für MySQL, PostgreSQL und Oracle,
vorzeichenbehaftet 64 Bit für SQLite und MongoDB), und `$db->clampInteger()` speichert den größten Wert, den die Spalte fasst.
Bei einem Upgrade ist nichts zu tun.

## Suchstatistik ist personenbezogen

`LogSearchStats` ist standardmäßig ausgeschaltet, weil es jede Phrase speichert, die Besucher eingeben, und das können
personenbezogene Daten sein. Schalten Sie es ein, nehmen Sie `ezsearch_search_phrase` in Ihre Aufbewahrungsregeln auf.

## Stattdessen Solr

Große Websites verlagern die Suche oft über die Erweiterung ezfind nach Solr, wenn sie installiert ist; `php bin/php/console
exp:solr` steuert einen Solr-Server, sobald er installiert und konfiguriert ist. Die eingebauten Tabellen wachsen dann nicht
mehr; der Index von Solr liegt außerhalb der Datenbank und braucht einen eigenen Plan für Sicherung oder Neuaufbau. Mit ezfind
reihen Verschiebungen und Änderungen der Sichtbarkeit außerdem Aktionen `index_moved_node` ein.

## Checkliste

- [ ] Die Cronjobs laufen, sodass `ezpending_actions` nicht wächst.
- [ ] Ein vollständiger Neuaufbau ist nach Importen, Wiederherstellungen und Engine-Wechseln eingeplant.
- [ ] `ezsearch_object_word_link` ist in der Kapazitätsplanung berücksichtigt.
- [ ] `LogSearchStats` bleibt ausgeschaltet, sofern keine Statistik gebraucht wird und Aufbewahrungsregeln sie abdecken.

## Verwandte Seiten

- [Suchen](../suchen.md)
- [Die Tabellen, die ein Datenbankadministrator kennen sollte](tabellen-die-sie-kennen-sollten.md)
- [Such-Backends](../hosting/grundlagen/such-backends.md) (für Hosting-Unternehmen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
