---
title: "Lektion 3.4: Das Template hinter einer Seite finden"
audience: trainer, developer
category: training
order: 34
summary: Die Teilnehmenden führen mit Pfadkommentaren und der Liste der verwendeten Templates jeden Teil einer Seite auf sein Template zurück, finden heraus, warum ein Override nicht greift, und prüfen Templates mit eztemplatecheck, bevor sie neu laden. 45 Minuten.
keywords: entwickler, template-debug, ShowTemplatePathComments, ShowUsedTemplates, Exp Debug bar, kurzeinstellungen, eztemplatecheck, override greift nicht, exp:ini where, fehlersuche in templates, developers course, template debug, Quick settings, override not applied, template debugging
type: help
---
# Lektion 3.4: Das Template hinter einer Seite finden

Die häufigste Frage eines Template-Entwicklers lautet „Welche Datei hat das erzeugt?“, die zweite „Warum nicht meine?“.
Diese Lektion gibt den Teilnehmenden eine Routine, die beide Fragen in Minuten beantwortet: Template-Pfadkommentare,
die Liste der verwendeten Templates, `exp:ini where` für Override-Regeln und die Syntaxprüfung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 3: Designs, Overrides und Fehlersuche](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 3.2](override-regeln.md) und [Lektion 1.3](../modul-01-die-entwicklungsinstallation/debug-ausgabe-und-logs.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Kommentare `START` und `STOP` im Quelltext der Seite lesen, einschließlich des ursprünglichen Namens in Klammern;
- die Liste der verwendeten Templates in der Debug-Ausgabe lesen und ihren Bearbeitungslinks folgen;
- mit `exp:ini where` jede Datei finden, die einen Override-Block definiert;
- Templates mit `eztemplatecheck.php` prüfen und seine Fehlermeldungen lesen;
- eine Checkliste abarbeiten, wenn ein Override nicht greift.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Template-Pfadkommentare](../../../../../eng-us/features/6.0/template-path-comments.md) und
[Templates und Design, Teil 7](../../../../anleitungen/templates-und-design.md#7-templates-debuggen).

1. **Pfadkommentare** (4 Minuten). Mit `[TemplateSettings] ShowTemplatePathComments=enabled` wird die Ausgabe jedes
   Templates in `<!-- START ... -->` und `<!-- STOP ... -->` eingeschlossen. Hat ein Override geantwortet, folgt der
   ursprüngliche Name in Klammern: `<!-- START .../full/article.tpl (node/view/full.tpl) -->` bedeutet „Die Datei
   `article.tpl` hat die Anfrage `node/view/full.tpl` beantwortet“. Nur für die Entwicklung: Die Kommentare
   zerstören JSON-, XML- und RSS-Ausgaben.
2. **Verwendete Templates** (3 Minuten). Mit `ShowUsedTemplates=enabled` listet die Debug-Ausgabe jedes Template der
   Seite auf, das angeforderte und das verwendete. In der Administration hat der Kasten **Kurzeinstellungen** die
   Schalter **Debug-Ausgabe**, **Template-Debug**, **Inline-Template-Debug** und **Liste der verwendeten Templates**
   für den aktuellen Siteaccess.
3. **Die Checkliste** (5 Minuten). Projizieren Sie diese Tabelle:

   | Frage | Vorgehen |
   |---|---|
   | Welche Datei hat es geschrieben? | Pfadkommentare |
   | Warum diese Datei und nicht meine? | `Source`, `MatchFile`, `Match[...]` prüfen; `exp:ini where override.ini/<block>/MatchFile <sa>` |
   | Stimmt die Syntax? | `php bin/php/eztemplatecheck.php <file>` |
   | Immer noch die alte Ausgabe? | `ini`, `template-override`, `template`, `content` leeren |
   | Welche Templates, wie lange, wie viele Abfragen? | Die Exp Debug bar: Templates, Zeitmessung, SQL |

4. **Die Prüfung** (3 Minuten). `eztemplatecheck.php` nimmt eine Datei, ein Verzeichnis oder `-s<siteaccess>` für
   alle Templates eines Siteaccess an. Ein falsches schließendes Tag wird als „Unterminated tag ... does not match
   tag ...“ gemeldet.

### Häufige Fragen

**Kann ich Pfadkommentare nur für einen Siteaccess einschalten?** Ja: `exp:ini set
site.ini/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<sa>`.

**Die Bearbeitungslinks der verwendeten Templates öffnen den falschen Editor.** `[DebugSettings] UsedTemplateEditUrl`
legt den Link als Format für `sprintf()` fest, zum Beispiel auf den Editor der Administration
([Templates und Design, Teil 7](../../../../anleitungen/templates-und-design.md#bearbeiten-links-in-der-liste-der-verwendeten-templates)).

**Was ist mit Operator-Warnungen?** Eine Warnung wie „Operator 'my_operator' has no default value for parameter
'length'“ nennt das Template und die Zeile; sie wird einmal pro Kompilierung angezeigt.

### Stolperfallen

- **Kommentare in der Produktion.** Schalten Sie die Pfadkommentare nach der Lektion wieder aus.
- **Im kompilierten Cache suchen.** Die kompilierten Templates unter `var/` sind nicht die Dateien, die man bearbeitet.
  Bearbeiten Sie immer die `.tpl`, die der Kommentar nennt.
- **Ein Override in einem anderen Siteaccess.** Die Regel wurde für `site` geschrieben, die Seite wird aber von einem
  anderen Siteaccess ausgeliefert. `exp:ini where` mit dem richtigen Siteaccess-Namen zeigt es.

## Praktische Übung

Auf der Entwicklungsinstallation, mit den Overrides aus Lektion 3.2.

1. Schalten Sie die Pfadkommentare für Ihren öffentlichen Siteaccess ein und leeren Sie den Template-Cache:

   ```bash
   php bin/php/console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled siteaccess:<sa>
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   ```

2. Öffnen Sie einen Artikel auf der Website, sehen Sie sich den Quelltext der Seite an und suchen Sie nach `START`.
   Suchen Sie den Kommentar mit `(node/view/full.tpl)` und schreiben Sie auf, welche Datei geantwortet hat.
3. Suchen Sie auf dieselbe Weise das Template der Kopf- oder Fußzeile der Seite.
4. Öffnen Sie in der Debug-Leiste die Liste der verwendeten Templates und suchen Sie dieselben beiden Dateien.
5. Listen Sie die Dateien auf, die Ihren Override-Block definieren:
   `php bin/php/console exp:ini where override.ini/mysite_full_article/MatchFile <sa>`.
6. Machen Sie die Regel absichtlich kaputt: Setzen Sie `Match[class_identifier]` mit `exp:ini set` auf `artcle` (ein
   Tippfehler), leeren Sie die Caches `ini`, `template-override` und `content` und laden Sie neu. Finden Sie die
   Ursache mit der Checkliste und beheben Sie sie dann.
7. Machen Sie das Template absichtlich kaputt: Ändern Sie ein `{/if}` in `{/foreach}` und führen Sie
   `php bin/php/eztemplatecheck.php extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl` aus.
   Beheben Sie den Fehler.
8. Prüfen Sie alle Templates Ihrer Erweiterung auf einmal: `php bin/php/eztemplatecheck.php extension/mysite_hello/design/`.
9. Schalten Sie die Pfadkommentare wieder aus (`disabled`) und leeren Sie den Template-Cache.

## Erwartetes Ergebnis

- Schritt 2: ein Kommentar wie `<!-- START extension/mysite_hello/design/<design>/override/templates/full/mysite_article.tpl (node/view/full.tpl) -->`.
- Schritt 5: die Siteaccess-Datei der Erweiterung und der wirksame Wert.
- Schritt 6: Die Seite fällt auf ein anderes Template zurück; nach der Korrektur ist die Kurszeile wieder da.
- Schritt 7: `Template file invalid` mit der Zeile; Schritt 8 endet nach der Korrektur ohne Fehler.
- Der Trainer prüft, dass alle Teilnehmenden die Klammer aus Schritt 2 erklären können.

## Quiz

1. Was sagt Ihnen `(node/view/full.tpl)` am Ende eines Kommentars `START`?
2. Welcher Befehl listet jede Datei, die einen Override-Block definiert, in Ladereihenfolge auf?
   A. `exp:ini get` B. `exp:ini where` C. `eztemplatecheck.php` D. `ezcache.php --list-ids`
3. Nennen Sie drei Gründe, warum ein Override nicht greift.
4. Warum müssen die Pfadkommentare für einen Feed oder eine JSON-Ansicht ausgeschaltet sein?
5. Welcher Kasten der Administration schaltet die Debug-Ausgabe und die Liste der verwendeten Templates für den
   aktuellen Siteaccess?

<details>
<summary>Antworten</summary>

1. Ein Override hat geantwortet; die vor der Klammer genannte Datei hat das angeforderte `node/view/full.tpl` ersetzt.
2. **B.** `exp:ini where override.ini/<block>/MatchFile <sa>`.
3. Eine Bedingung passt nicht (Tippfehler, falsche Klasse); die Regel steht in einem anderen Siteaccess; die Caches
   wurden nicht geleert; eine Regel mit höherer Priorität passt zuerst; die Template-Datei liegt in keinem Design der
   Kette.
4. Sie fügen der Ausgabe HTML-Kommentare hinzu, was JSON, XML und RSS zerstört.
5. **Kurzeinstellungen**, mit **Debug-Ausgabe** und **Liste der verwendeten Templates**.

</details>

## Weiterführende Literatur

- [Template-Pfadkommentare](../../../../../eng-us/features/6.0/template-path-comments.md): Format, Overrides und Grenzen.
- [Die Exp Debug bar](../../../../../eng-us/features/6.0/exp-debug-bar.md): die Tabs Templates, Zeitmessung und SQL.
- [Verbesserungen der Debug-Ausgabe](../../../../../eng-us/features/6.0/debug-output-improvements.md): was sich an der
  Debug-Ausgabe geändert hat.

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
