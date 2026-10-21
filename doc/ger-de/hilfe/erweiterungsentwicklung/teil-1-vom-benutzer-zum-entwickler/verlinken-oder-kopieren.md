---
title: "Verlinken oder kopieren: Quellen fair zitieren"
audience: user, developer, designer, app-creator
category: extension-development
order: 160
summary: Wann Sie auf eine Quelle verlinken, wann Sie sie zitieren, wann Sie sie kopieren und bearbeiten dürfen und wie Sie die Urheber in Codekommentaren, READMEs, Dokumentation und Commit-Nachrichten nennen - mit den Mustern, die dieses Projekt verwendet.
keywords: verlinken, kopieren, zitieren, Zitat, Namensnennung, Quellenangabe, Quelle, README, Commit-Nachricht, Dokumentation, fair, Plagiat, link, copy, quote, cite, citation, attribution, credit, source, commit message, documentation, plagiarism
type: help
---
# Verlinken oder kopieren: Quellen fair zitieren

Teil 1, Kapitel 16. Sie werden ständig aus der Arbeit anderer lernen. Dieses Kapitel gibt Ihnen eine einfache Regel,
um sie fair zu nutzen, und die Muster, mit denen dieses Projekt die Urheber in Code, Dokumentation und Historie nennt.

## Die Regel

1. **Verlinken** Sie standardmäßig. Ein Link schickt den Leser zur Quelle, in ihrem Zusammenhang, von ihrem Autor auf
   dem neuesten Stand gehalten.
2. **Zitieren** Sie eine kurze Passage, wenn es auf den genauen Wortlaut ankommt, mit einem Link und dem Datum, an dem
   Sie sie gelesen haben.
3. **Kopieren und bearbeiten** Sie nur, wenn die Lizenz es erlaubt, und dann **behalten Sie die Hinweise bei und sagen
   Sie, woher es stammt**.
4. Geben Sie die Arbeit anderer **nie** als Ihre eigene aus.

| Quelle | Verlinken | Kurz zitieren | Kopieren und bearbeiten |
|---|---|---|---|
| Eine Seite auf einer Website mit „All rights reserved“ | ja | ja, kurz, mit Quellenangabe | nein |
| eZpedia, diese Hilfe, die freien Bücher unter der GNU FDL | ja | ja | ja, unter der GNU FDL, mit Quellenangabe |
| Code unter der GPL oder MIT | ja | ja | ja, mit Lizenz- und Copyright-Hinweisen |
| Ein Forenbeitrag ohne Lizenz | ja | ja, kurz | nur die Idee, außer der Autor ist einverstanden |
| Code ohne jede Lizenz | ja | nein | nein |

## Urheber im Code nennen

Wenn Sie Code unter einer freien Lizenz kopieren oder bearbeiten, behalten Sie seinen Kopf bei und fügen Sie Ihre
eigene Zeile hinzu:

```php
<?php
/**
 * Adapted from <project> (<link to the original file or repository>), <license>.
 *
 * @copyright Copyright (C) <years> <original holder>
 * @copyright Copyright (C) <year> <you or your organization> (changes)
 * @license GNU General Public License v2.0 (or any later version)
 */
```

Viele Erweiterungen in diesem Ökosystem tragen aus demselben Grund mehrere Copyright-Zeilen in `ezinfo.php`: Jede Zeile
nennt jemanden, der daran gearbeitet hat. Die `ezinfo.php` von `owsimpleoperator` zum Beispiel nennt 7x und Brookins
Consulting zusammen mit OPEN WIDE, dem Unternehmen, das die Erweiterung ursprünglich geschrieben hat. Behalten Sie
solche Zeilen bei; fügen Sie Ihre hinzu.

## Urheber in einer README oder Dokumentation nennen

Ein kurzer Abschnitt „Credits“ oder „Origin“ genügt:

```text
## Origin

This extension is a port of <upstream package> (<link>), published under the <license>.
The structure of its services follows the upstream package; the code was rewritten for Exponential 6.
```

Die Layouts-Pakete tun genau das: Die README von `explayouts_core` sagt, es sei „an Exponential Legacy port inspired by
the `netgen/layouts-core` package“, und die README von `explayouts` dasselbe für `netgen/layouts-ibexa`.

## Urheber in Commit-Nachrichten nennen

Eine Commit-Nachricht ist dauerhaft und öffentlich. Wenn ein Commit die Arbeit eines anderen einbringt, sagen Sie das in
seinem Text:

```text
Added: Initial import of the class conversion extension

Forked from <original extension> and hardened for PHP 8, with a transactional
rollback so that a failed conversion leaves the content untouched.
```

Das kommt dem echten ersten Commit von `se7enxweb/expchangeclass` (Commit `7c3f164`) nahe, der sagt, die Erweiterung
sei „forked from eZChangeclass and hardened for PHP 8“. Wenn Sie den Pull Request eines Mitwirkenden übernehmen,
übernehmen Sie ihn als Merge-Commit, damit dessen eigene Commits mit seinem Namen in der Historie bleiben (siehe
[Umgangsformen bei Pull Requests](../teil-5-erweitern-forken-zurueckgeben/pull-request-etikette.md)).

## Urheber in einer Hilfeseite nennen

Diese Hilfe verlinkt ihre Quellen und nennt Dateien außerhalb von `doc/` als Pfade. Wenn Sie Text aus eZpedia oder einem
freien Buch für eine Hilfeseite bearbeiten:

- nennen Sie die Quelle und verlinken Sie sie im Text oder unter „Verwandte Seiten“,
- sagen Sie, dass Sie sie bearbeitet haben („bearbeitet nach ... und für Exponential 6 aktualisiert“),
- halten Sie die Seite unter der GNU FDL (jede Hilfeseite endet mit der Zeile zur freien Dokumentation).

## Wie die faire Nutzung einer Idee aussieht

Sie lesen einen Forenbeitrag, der Ihr Problem mit einer cleveren SQL-Abfrage löst. Sie dürfen:

- mit derselben Idee eine eigene Lösung bauen, auf Ihre eigene Weise geschrieben,
- den Beitrag in einem Kommentar über Ihrem Code verlinken (`// Approach from <link>`),
- dem Autor im Thread danken.

Das ist fair, ehrlich und hilft dem nächsten Leser, die ursprüngliche Diskussion zu finden.

## Verwandte Seiten

- Zurück: [Lizenzen lesen, bevor Sie etwas wiederverwenden](lizenzen-vor-der-wiederverwendung-lesen.md)
- Weiter: [Eine alte Erweiterung bewerten, bevor Sie sie verwenden](eine-alte-erweiterung-bewerten.md)
- [Namensnennung und Copyright-Hinweise](../teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md)
- [Ethisch umbenennen](../teil-5-erweitern-forken-zurueckgeben/ethisches-rebranding.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
