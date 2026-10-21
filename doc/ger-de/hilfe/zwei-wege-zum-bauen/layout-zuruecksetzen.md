---
title: Ein Layout zurücksetzen
audience: admin, website-creator, editor, developer
category: build-two-ways
order: 610
summary: Vor einer Änderung eine Sicherheitskopie eines Layouts machen und sie danach mit exp:layouts zurückholen; exportieren, ändern, mit overwrite und publish importieren, den Inhalts-Cache leeren. Durchgespielt am gemeinsamen Layout Prefooter.
keywords: Zurücksetzen, Layout rückgängig machen, Layout wiederherstellen, Layout sichern, exp:layouts export, Import overwrite publish, Sicherheitskopie, Layout-Historie, rollback, undo layout, restore layout, backup layout, safety copy, layout history
type: help
---
# Ein Layout zurücksetzen

Im Editor wirft **Verwerfen** einen Entwurf weg; eine vorher veröffentlichte Version holt es nicht zurück. Ein vor der
Änderung gemachter Export ist ein Weg zurück, der auf der Kommandozeile funktioniert, auf jeder Installation.

## Inhalt

1. [Vor der Änderung: exportieren](#1-vor-der-änderung-exportieren)
2. [Die Änderung vornehmen](#2-die-änderung-vornehmen)
3. [Zurücksetzen: Vorschau](#3-zurücksetzen-vorschau)
4. [Zurücksetzen: schreiben](#4-zurücksetzen-schreiben)
5. [Den Inhalts-Cache leeren](#5-den-inhalts-cache-leeren)

## 1. Vor der Änderung: exportieren

```text
$ ./console exp:layouts export --only=d14c82715a0c5f1da1e6df4d0b77e638 --output=prefooter-before.zip
Wrote prefooter-before.zip: 1 layouts (1 shared, 0 drafts), 4 blocks, 0 mappings, 3 content references.
```

Die Administration macht dasselbe unter **Layouts > Exportieren**. Bewahren Sie die Datei mit einem Datum im Namen oder
im Repository Ihres Projekts auf.

## 2. Die Änderung vornehmen

Auf der Testkopie bekam der Prefooter einen vierten Artikel, dann den Ansichtstyp `list_compact`, dann einen
Notice-Block ([Den Prefooter im Layout-Editor ändern](prefooter-im-layout-editor.md)).

## 3. Zurücksetzen: Vorschau

```text
$ ./console exp:layouts import prefooter-before.zip --dry-run
State        Action      Kind       Key
changed      skip        layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Dry run: nothing was written.
```

`changed` mit der Standardstrategie `skip` würde das Layout unberührt lassen. Sehen Sie sich das Überschreiben in der Vorschau an:

```text
$ ./console exp:layouts import prefooter-before.zip --dry-run --strategy=overwrite --publish
State        Action      Kind       Key
changed      overwrite   layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Dry run: nothing was written.
```

## 4. Zurücksetzen: schreiben

```text
$ ./console exp:layouts import prefooter-before.zip --strategy=overwrite --publish
State        Action      Kind       Key
changed      overwrite   layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Written: layout:d14c82715a0c5f1da1e6df4d0b77e638:published overwritten and published (ID 7)
1 items written; 0 resolver cache entries removed.
```

Das Layout behält seine ID (7) und seinen Bezeichner, sodass jedes Layout, das damit verknüpft ist, weiter funktioniert.
Nach dem Import hatte es wieder seine drei ursprünglichen Artikel und keinen der hinzugefügten Blöcke.

Ohne `--publish` wird die importierte Version ein Entwurf, den Sie vor dem Veröffentlichen im Editor prüfen können.

## 5. Den Inhalts-Cache leeren

Der Import leert den Seiten-Cache nicht. Auf der Testkopie zeigten die Artikelseiten den geänderten Prefooter weiterhin
(`X-Exp-Cache: HIT`), bis:

```text
./console exp:cache clear --tag=content
```

## Verwandte Seiten

- [Layouts exportieren und importieren](layouts-exportieren-und-importieren.md)
- [Den Prefooter im Layout-Editor ändern](prefooter-im-layout-editor.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
