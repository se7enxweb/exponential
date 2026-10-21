---
title: Layouts exportieren und importieren
audience: admin, developer, website-creator, designer, hosting
category: build-two-ways
order: 600
summary: Layouts zwischen Installationen übertragen, unter Versionsverwaltung halten und ein Layout zurücksetzen; exp:layouts list, export und import mit Probeläufen und Strategien, das Format der .zip und der nach einem Import auf der Kommandozeile zu leerende Cache. Echte Ausgaben.
keywords: exp:layouts, Layouts exportieren, Layouts importieren, Layouts-Zip, Probelauf, Strategie skip overwrite copy, publish, Zurücksetzen, Versionsverwaltung, Staging zu Produktion, Verweise, export layouts, import layouts, layouts zip, dry run, strategy, rollback, version control, staging to production, references
type: help
---
# Layouts exportieren und importieren

Layouts liegen in der Datenbank, müssen dort aber nicht bleiben: `./console exp:layouts` schreibt sie in eine `.zip` und
liest sie zurück, auf derselben oder einer anderen Installation. Die Administration bietet dasselbe unter
**Layouts > Exportieren** und **Layouts > Importieren**.

## Inhalt

1. [Auflisten](#auflisten)
2. [Exportieren](#exportieren)
3. [Was in der Datei steht](#was-in-der-datei-steht)
4. [Mit Probelauf importieren](#mit-probelauf-importieren)
5. [Strategien](#strategien)
6. [Nach einem Import auf der Kommandozeile](#nach-einem-import-auf-der-kommandozeile)

## Auflisten

```text
$ ./console exp:layouts list
ID     Identifier                               Name                                 Status     Shared    Rules  Links to
2      fd0aa7af77a85a03bf1080018f5db1dc         Header / Footer                      published  shared        0
7      d14c82715a0c5f1da1e6df4d0b77e638         Prefooter                            published  shared        0
...
```

`--drafts` fügt offene Entwürfe hinzu, `--json` gibt JSON aus.

## Exportieren

```text
$ ./console exp:layouts export --only=d14c82715a0c5f1da1e6df4d0b77e638 --output=prefooter.zip
Wrote prefooter.zip: 1 layouts (1 shared, 0 drafts), 4 blocks, 0 mappings, 3 content references.
```

| Option | Bedeutet |
|---|---|
| `--only=a,b` | diese Layouts (nach Bezeichner) |
| `--all` | jedes Layout, jedes gemeinsame Layout und jede Zuordnung |
| `--shared=all\|a,b` | hinzuzufügende gemeinsame Layouts (die, mit denen die gewählten Layouts verknüpft sind, sind immer dabei) |
| `--rules=none\|selected\|all` | die Layout-Zuordnungen: keine, die der gewählten Layouts (Standard) oder alle |
| `--drafts` | auch die offenen Entwürfe |
| `--output=file.zip` | die Datei (Standard `layouts-<date>.zip` im aktuellen Verzeichnis) |

## Was in der Datei steht

`layouts.json` (Layouts, Zonen, Blöcke, Parameter, Sammlungen und Einträge), `rules.json` (Zuordnungen),
`references.json` und `manifest.json`. Auf Inhalte wird über **Remote-ID und Pfad** verwiesen, nicht nur über die
Knoten-ID, sodass ein Import in eine andere Installation dieselben Inhalte finden kann:

```json
"node": {
    "93": { "remote_id": "media-n-725", "path": "fit_healthy/fitness/7_keystone_habits_to_maximize_your_mornings" },
    "98": { "remote_id": "media-n-731", "path": "fit_healthy/fitness/why_track_your_workouts" }
}
```

`manifest.json` nennt das Format (`exponential-layouts-transfer`, Version 1), die Quell-Site und die Version von
Exponential, die Datenbank, die Auswahl und die Anzahlen.

## Mit Probelauf importieren

Beginnen Sie immer mit `--dry-run`. Den Import der Datei in die Installation, aus der sie stammt:

```text
$ ./console exp:layouts import prefooter.zip --dry-run
File: format version 1, made 2026-10-19T12:37:15+00:00 on www.example.com.
State        Action      Kind       Key
identical    none        layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 1 identical, 0 changed, 0 conflicting.
Dry run: nothing was written.
```

Ein älterer Export desselben Layouts, nachdem das Layout geändert wurde:

```text
$ ./console exp:layouts import prefooter-before.zip --dry-run
State        Action      Kind       Key
changed      skip        layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Dry run: nothing was written.
```

## Strategien

| `--strategy=` | Ein Eintrag, den es bereits gibt |
|---|---|
| `skip` (Standard) | bleibt, wie er ist |
| `overwrite` | wird ersetzt; mit `--publish` wird das Ergebnis veröffentlicht, sonst bleibt es ein Entwurf |
| `copy` | wird als neue Kopie daneben importiert |

`--item=<key>=<strategy>,...` setzt eine Strategie pro Eintrag, mit den Schlüsseln, die der Probelauf ausgibt.
`--unmapped=drop|keep|fail` entscheidet, was mit Verweisen auf Inhalte geschieht, die die Zielinstallation nicht hat (Standard `drop`).

## Nach einem Import auf der Kommandozeile

Auf der Testkopie schrieb ein Import mit `--strategy=overwrite --publish` das Layout und gab
`1 items written; 0 resolver cache entries removed.` aus, aber die Artikelseiten kamen mit den alten Blöcken weiterhin aus
dem Seiten-Cache (`X-Exp-Cache: HIT`), bis `./console exp:cache clear --tag=content` lief. Leeren Sie nach einem Import auf
der Kommandozeile die Inhalts-Caches. (Das Veröffentlichen im Editor hat sie von selbst geleert.)

## Verwandte Seiten

- [Ein Layout zurücksetzen](layout-zuruecksetzen.md)
- [Gemeinsame Layouts und Zonenverknüpfung](gemeinsame-layouts-und-zonenverknuepfung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
