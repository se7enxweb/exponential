---
title: "Lektion 3.2: Override-Regeln in override.ini"
audience: trainer, developer
category: training
order: 32
summary: Die Teilnehmenden schreiben eine Override-Regel von Hand in override.ini mit Source, MatchFile, Subdir und Match-Bedingungen, legen das Template in ihre Erweiterung, leeren die richtigen Caches und prüfen, dass ihre Datei antwortet. 60 Minuten.
keywords: entwickler, override, override.ini, override-regel, Source, MatchFile, Subdir, Match, class_identifier, node, object, section, viewmode, Priority, template-override-cache, vollansicht, developers course, override rule, template-override cache, full view
type: help
---
# Lektion 3.2: Override-Regeln in override.ini

Ein Override sagt: „Verwende für diese Klasse, diesen Knoten oder diese Sektion mein Template statt des Standards.“ Er
besteht aus zwei Teilen, einer Template-Datei und einer Regel in `override.ini`. In dieser Lektion schreiben die
Teilnehmenden beides von Hand in ihrer Erweiterung, damit die Änderung unter Versionskontrolle steht und sich durch
das Entfernen von zwei Dateien rückgängig machen lässt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 3: Designs, Overrides und Fehlersuche](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 3.1](die-design-kette.md): `mysite_hello` ist aktiv und hat einen Design-Ordner |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen Override-Block mit `Source`, `MatchFile`, `Subdir` und `Match[...]` schreiben;
- einen Match-Schlüssel wählen: `class_identifier`, `node`, `object`, `section`, `parent_node`, `viewmode` und andere;
- das Template in `design/<design>/override/templates/` ablegen;
- nach dem Hinzufügen einer Regel die Caches `template-override`, Template und Inhalt leeren;
- erklären, wie `Priority` zwischen mehreren passenden Regeln entscheidet.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Templates und Design, Teil 4](../../../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen).

1. **Eine Beispielregel** (5 Minuten). Projizieren Sie diesen Block und lesen Sie ihn Zeile für Zeile:

   ```ini
   [full_article_course]
   Source=node/view/full.tpl
   MatchFile=full/article_course.tpl
   Subdir=templates
   Match[class_identifier]=article
   ```

   `Source` ist das ersetzte Template, `MatchFile` die neue Datei relativ zu `override/templates/`, `Subdir` ist immer
   `templates`, und jedes `Match[...]` muss zutreffen.
2. **Match-Schlüssel** (5 Minuten). Der Template-Editor bietet `class_identifier`, `class`, `node`, `object`,
   `section`, `section_identifier`, `remote_id`, `node_remote_id`, `parent_node`, `class_group`, `depth`, `url_alias`,
   `viewmode`, `navigation_part_identifier`, `persistent_variable`, `state` und `state_identifier`. Seit 6.0.15 hat
   eine Knotenansicht außerdem den Schlüssel `layout` (leer, solange `layout/set` nicht verwendet wird).
3. **Wo die Regel steht** (4 Minuten). In `settings/siteaccess/<sa>/override.ini.append.php` für einen Siteaccess oder
   in `settings/siteaccess/<sa>/override.ini.append.php` einer Erweiterung. Steht sie in der Erweiterung, wird die
   Regel zusammen mit ihrem Template ausgeliefert.
4. **Reihenfolge** (3 Minuten). Regeln mit einer `Priority` werden von der niedrigsten Zahl aufwärts geprüft; Regeln
   ohne `Priority` kommen danach. Die erste Regel, deren Bedingungen alle zutreffen, gewinnt.
5. **Caches** (3 Minuten). Eine neue Regel oder ein neuer Template-Pfad verlangt, dass `template-override` und die
   Template-Caches geleert werden; Seiten, die schon im View-Cache der Inhalte liegen, brauchen `--clear-tag=content`.

### Häufige Fragen

**Wie überschreibe ich für zwei Klassen?** Mit zwei Blöcken, einem pro Klasse. Bedingungen in einem Block werden mit
„und“ verknüpft.

**Kann ich eine Zeilenansicht nur innerhalb eines Ordners überschreiben?** Ja: `Source=node/view/line.tpl` mit
`Match[parent_node]=<node ID>`.

**Was steht im Template?** Dieselben Variablen wie im Original: in einer Knotenansicht `$node` und die
View-Parameter. Beginnen Sie mit einer Kopie des Originals.

### Stolperfallen

- **`MatchFile` mit dem Präfix `override/templates/`.** Der Pfad ist relativ zu `override/templates/`; schreiben Sie
  `full/article_course.tpl`.
- **Ein Blockname, den es anderswo schon gibt.** Blocknamen müssen über alle Dateien `override.ini` des Siteaccess
  hinweg eindeutig sein; ein doppelter Name wird mit dem anderen Block zusammengeführt. Stellen Sie den Namen Ihrer
  Erweiterung voran.
- **Den Siteaccess-Ordner vergessen.** Eine Regel in `settings/override.ini.append.php` der Erweiterung, außerhalb
  eines Siteaccess-Ordners, gilt für jeden Siteaccess, die Administration eingeschlossen. Legen Sie die Regeln einer
  Website wie in der Übung in den Ordner `settings/siteaccess/<sa>/` der Erweiterung und prüfen Sie mit
  `exp:ini where override.ini/<block>/MatchFile <sa>`, welche Dateien einen Block definieren.

## Praktische Übung

Auf der Entwicklungsinstallation, mit `mysite_hello` aktiv aus Lektion 3.1. `<sa>` ist Ihr öffentlicher Siteaccess,
`<design>` sein Website-Design.

1. Kopieren Sie die Vollansicht des Kernels als Ausgangspunkt:

   ```bash
   mkdir -p extension/mysite_hello/design/<design>/override/templates/full
   cp design/standard/templates/node/view/full.tpl extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl
   ```

2. Fügen Sie oben in der Kopie eine sichtbare Zeile ein: `<p class="mysite-note">Course override: {$node.name|wash}</p>`.
3. Legen Sie die Regel mit `exp:ini` im Siteaccess-Bereich der Erweiterung an:

   ```bash
   php bin/php/console exp:ini set override.ini/mysite_full_article/Source node/view/full.tpl extension:mysite_hello:siteaccess:<sa>
   php bin/php/console exp:ini set override.ini/mysite_full_article/MatchFile full/mysite_article.tpl extension:mysite_hello:siteaccess:<sa>
   php bin/php/console exp:ini set override.ini/mysite_full_article/Subdir templates extension:mysite_hello:siteaccess:<sa>
   php bin/php/console exp:ini set "override.ini/mysite_full_article/Match[class_identifier]" article extension:mysite_hello:siteaccess:<sa>
   ```

4. Öffnen Sie die geschriebene Datei `extension/mysite_hello/settings/siteaccess/<sa>/override.ini.append.php` und
   vergleichen Sie sie mit dem Beispielblock oben.
5. Prüfen Sie die Syntax des Templates: `php bin/php/eztemplatecheck.php extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl`.
6. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=template-override
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-tag=content
   ```

7. Öffnen Sie einen Artikel auf der Website. Die Zeile aus Schritt 2 erscheint.
8. Öffnen Sie einen Ordner: Die Zeile erscheint nicht.
9. Beschränken Sie die Regel auf einen Artikel: Fügen Sie auf dieselbe Weise `Match[node]` mit der Knoten-ID dieses
   Artikels hinzu, leeren Sie die Caches `ini`, `template-override` und `content` und prüfen Sie einen anderen Artikel.
10. Committen Sie das Template und die Einstellungsdatei.

## Erwartetes Ergebnis

- Schritt 4: ein Block `[mysite_full_article]` mit den vier Zeilen, innerhalb der Hülle `<?php /* ... */ ?>` einer
  Datei `.ini.append.php`.
- Schritt 7: Die Artikelseite beginnt mit „Course override:“ und seinem Namen; Schritt 8: Ordner sind unverändert.
- Schritt 9: Nur der gewählte Artikel zeigt die Zeile.
- Der Trainer prüft das, indem er den Artikel einer Person mit eingeschalteten Template-Pfadkommentaren öffnet
  (Lektion 3.4).

## Quiz

1. Was nennt `Source` in einem Override-Block?
2. Relativ zu welchem Ordner ist `MatchFile=full/article_course.tpl`?
   A. `templates/` B. `override/templates/` C. `design/` D. `settings/`
3. Zwei Bedingungen in einem Block: Werden sie mit „und“ oder mit „oder“ verknüpft?
4. Welche Caches müssen nach dem Hinzufügen einer neuen Regel und eines neuen Templates geleert werden?
5. Zwei Regeln passen; eine hat `Priority=10`, die andere keine. Welche gewinnt?

<details>
<summary>Antworten</summary>

1. Das ersetzte Template, zum Beispiel `node/view/full.tpl`.
2. **B.** `override/templates/` eines Designs in der Kette.
3. Mit „und“: Jedes `Match[...]` muss zutreffen.
4. `ini` (für die neue Regel), `template-override`, `template` und `content` für bereits zwischengespeicherte Seiten.
5. Die Regel mit `Priority=10`: Regeln ohne Priorität werden zuletzt eingeordnet
   ([Template-Editor](../../../../funktionen/6.0/template-editor-ueberschreibungen.md)).

</details>

## Weiterführende Literatur

- [Templates und Design](../../../../anleitungen/templates-und-design.md): Overrides per Klick und per Datei, der Schlüssel
  `layout`.
- [Template-Editor](../../../../funktionen/6.0/template-editor-ueberschreibungen.md): Match-Schlüssel, Prioritäten und wie der
  Editor Regeln schreibt.
- [Modul-Override aus einer Erweiterung](../../../../funktionen/6.0/modulueberschreibung-durch-extensions.md): die Templates eines
  Moduls aus einer Erweiterung überschreiben.

## Verwandte Seiten

- [Modul 3: Designs, Overrides und Fehlersuche](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
