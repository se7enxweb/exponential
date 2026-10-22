---
title: Neu indexieren
audience: dxp-developer
category: dxp-development
order: 32
summary: Wann und wie Sie den Suchindex der DXP mit exponential:reindex neu aufbauen - der vollständige Neuaufbau, Stapel, einzelne Inhaltselemente, ein Inhaltstyp oder ein Teilbaum - und wie Sie ihn auf einer großen Website sicher ausführen.
keywords: Neuindexierung, neu indexieren, exponential:reindex, ibexa:reindex, Suchindex, iteration-count, content-type, subtree, content-ids, Solr-Commit, Speicher, Cron, reindex, search index, memory
type: help
---
# Neu indexieren

Diese Seite richtet sich an Entwickler und Administratoren, die den Suchindex mit den Inhalten in Einklang halten. Das
Veröffentlichen aktualisiert den Index von selbst; Sie indexieren nach Ereignissen neu, die am Veröffentlichen
vorbeigehen.

## Inhalt

1. [Wann Sie neu indexieren](#wann-sie-neu-indexieren)
2. [Die Befehle](#die-befehle)
3. [Große Websites](#große-websites)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)

## Wann Sie neu indexieren

- Nach der Installation (der Installationsbefehl tut es, sofern Sie nicht `--skip-indexing` übergeben).
- Nach dem Import von Inhalten oder der Wiederherstellung einer Datenbank.
- Nach dem Wechsel der Suchmaschine.
- Nach einer Änderung eines Inhaltstyps, die durchsuchbare Felder betrifft.
- Nach `composer update`, wenn sich das Schema des Inhaltsmodells geändert haben kann (die Aktualisierungsschritte der
  Anleitung enthalten es).

## Die Befehle

`exponential:reindex` ist der primäre Name; `ibexa:reindex` ist sein Alias.

```bash
php bin/console exponential:reindex                              # everything
php bin/console exponential:reindex --iteration-count=100        # batch size
php bin/console exponential:reindex --content-ids=2,34,68        # only these content items
php bin/console exponential:reindex --content-type=article       # one content type
php bin/console exponential:reindex --subtree=45                 # one subtree
```

Mit Solr erzwingt die Anleitung nach dem Indexieren einen Commit:

```bash
curl "http://localhost:8983/solr/<your core>/update?commit=true"
```

## Große Websites

- Führen Sie ihn mit `--env=prod` und außerhalb der Stoßzeiten aus.
- Senken Sie `--iteration-count`, wenn der Speicher knapp ist; erhöhen Sie ihn, wenn die Datenbank schnell und Speicher
  reichlich vorhanden ist.
- Die Anleitung führt ihn bei großen Indizes "without memory limit in production" aus; bevorzugen Sie zuerst eine
  vernünftige Stapelgröße.
- Bauen Sie jeweils einen Inhaltstyp oder Teilbaum neu auf, wenn sich nur ein Teil der Website geändert hat.

## Das Ergebnis prüfen

- Suchen Sie in `/adminui/` nach einem bekannten neuen Element.
- Vergleichen Sie bei Solr die Zahl der Dokumente mit der Zahl der Inhaltselemente.
- "Search results outdated" unter [Fehlersuche](fehlersuche-in-der-dxp.md) nennt die üblichen Ursachen.

## Verwandte Seiten

- [Suche: die Datenbank-Engine und Solr](suche-legacy-und-solr.md)
- [Konsolenbefehle und Umbenennungen](konsolenbefehle-und-umbenennungen.md)
- [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
