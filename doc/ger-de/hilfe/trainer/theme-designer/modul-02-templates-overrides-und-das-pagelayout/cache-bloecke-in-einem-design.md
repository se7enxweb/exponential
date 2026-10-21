---
title: "Lektion 2.4: Cache-Blöcke in einem Design"
audience: trainer, designer, developer
category: training
order: 24
summary: Die Teilnehmenden erklären, warum das Pagelayout Cache-Blöcke braucht, legen ihr Kopfzeilenmenü mit den richtigen Schlüsseln in einen solchen Block, sehen, wie eine Veröffentlichung ihn erneuert, und finden den Fehler eines Blocks, dessen Schlüssel den Benutzer vergessen. 45 Minuten.
keywords: theme-design, cache-block, schlüssel, keys, expiry, subtree_expiry, ignore_content_expiry, template-block-cache, view-cache der inhalte, ViewCaching, leistung des pagelayouts, debug-leiste, theme design, template-block cache, content view cache, pagelayout performance, debug bar
type: help
---
# Lektion 2.4: Cache-Blöcke in einem Design

Der View-Cache der Inhalte bewahrt den Hauptbereich jeder Seite auf, aber das Pagelayout läuft bei jeder Anfrage. Ein
Menü, das im Pagelayout aus einem Fetch gebaut wird, kostet deshalb auf jeder Seite Datenbankarbeit. Cache-Blöcke
bewahren solche Teile auf. Sie sind aber auch die Quelle des peinlichsten Theme-Fehlers überhaupt: Der Name eines
Besuchers wird einem anderen angezeigt. Diese Lektion vermittelt beide Seiten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 2: Templates, Overrides und das Pagelayout](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 2.2](das-pagelayout.md): die Begrüßung in `page_footer.tpl`; ein Redakteurs- oder Administratorkonto auf der Schulungs-Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, welchen Teil einer Seite der View-Cache der Inhalte aufbewahrt und warum das Pagelayout nicht dazugehört;
- einen aufwendigen Teil eines Templates in `{cache-block}` legen, mit Schlüsseln, die alles abdecken, wovon seine
  Ausgabe abhängt;
- zwischen `expiry`, `subtree_expiry` und `ignore_content_expiry` wählen;
- einen Cache-Block erkennen und reparieren, dessen Schlüssel den Benutzer vergessen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [View-Cache und Cache-Blöcke](../../../../../eng-us/help/creators/website-creators/view-cache-and-cache-blocks.md) und
[Das Pagelayout, Cache-Blöcke](../../../../../eng-us/help/creators/website-creators/the-pagelayout.md#cache-blocks).

1. **Zwei Caches** (4 Minuten). `site.ini [ContentSettings] ViewCaching=enabled` bewahrt die Ausgabe von
   `content/view` pro Knoten, Ansichtsmodus, Sprache, Siteaccess, Rollen und mehr auf. Das Pagelayout gehört nicht
   dazu und läuft jedes Mal.
2. **Der Cache-Block** (5 Minuten).

   ```
   {cache-block keys=array( $module_result.uri, $current_user.contentobject_id )}
     ... expensive menu ...
   {/cache-block}
   ```

   Die gespeicherte Ausgabe wird wiederverwendet, solange die Schlüssel passen und der Block nicht abgelaufen ist.
   Ohne `keys` ist ein Block auf jeder Seite derselbe.
3. **Ablauf** (4 Minuten). `expiry` in Sekunden (Standard zwei Stunden, `0` heißt nie nach Zeit); `subtree_expiry`
   leert den Block nur, wenn unterhalb eines Knotens etwas veröffentlicht wird; `ignore_content_expiry` behält ihn über
   Veröffentlichungen hinweg. Standardmäßig leert eine Veröffentlichung alle Cache-Blöcke, deshalb erscheint ein neuer
   Ordner im Menü.
4. **Der Fehler** (4 Minuten). Eine Begrüßung „Angemeldet als Anna“ in einem Block, dessen einziger Schlüssel die
   Adresse ist, wird für den ersten Besucher gespeichert und danach allen gezeigt. Fügen Sie entweder
   `$current_user.contentobject_id` zu den Schlüsseln hinzu oder halten Sie benutzerbezogene Ausgabe außerhalb von
   Cache-Blöcken. Zeigen Sie es live in der Übung.
5. **Leeren** (3 Minuten). `php bin/php/ezcache.php --clear-id=template-block` leert alle Cache-Blöcke.

### Häufige Fragen

**Woran sehe ich, ob ein Block verwendet wurde?** Bei eingeschalteter Debug-Ausgabe zeigt die Exp Debug bar eine Zahl
**CB** (verwendete und auf der Seite vorhandene Cache-Blöcke) ([Exp Debug bar](../../../../../eng-us/features/6.0/exp-debug-bar.md)).

**Sollte das ganze Pagelayout ein einziger Block sein?** Mitgelieferte Themes legen oft den größten Teil in einen Block
mit Adresse und Benutzer als Schlüssel. Mehrere kleinere Blöcke mit passenden Schlüsseln sind in der Schulung leichter
nachzuvollziehen.

**Warum ist mein Menü veraltet, nachdem ich einen Ordner umbenannt habe?** Der Block wurde nicht geleert, zum Beispiel
wegen `ignore_content_expiry` oder eines `subtree_expiry` an anderer Stelle. Leeren Sie `template-block` und prüfen Sie
die Parameter.

### Stolperfallen

- **Testen, während man als derselbe Benutzer angemeldet ist.** Der Schlüsselfehler zeigt sich nur mit zwei
  verschiedenen Benutzern. Verwenden Sie für den zweiten ein privates Fenster.
- **Zu breite Schlüssel.** Ein Schlüssel, der sich bei jeder Anfrage ändert (eine Uhrzeit, ein Zufallswert), speichert
  jedes Mal einen neuen Block und füllt die Festplatte.
- **Menüs, die sich nach Berechtigung unterscheiden.** Ein Fetch liefert nur, was der aktuelle Benutzer lesen darf.
  Auf einer Website, auf der angemeldete Benutzer mehr Elemente der obersten Ebene sehen als Besucher, braucht auch der
  Menüblock den Benutzer (oder dessen Rollen) in seinen Schlüsseln.
- **Den Fehler stehen lassen.** Beenden Sie die Übung mit der reparierten Fußzeile und prüfen Sie sie erneut.

## Praktische Übung

Auf Ihrer Schulungsinstallation. Die Dateien liegen unterhalb von
`extension/training_theme/design/training_theme/templates/`.

1. Öffnen Sie `page_header.tpl`. Legen Sie das `<nav>` mit seinem `{def}` und `{undef}` in einen Cache-Block mit einem
   konstanten Schlüssel (das Menü ist auf jeder Seite und für jeden Benutzer gleich):

   ```
   {cache-block keys=array( 'training_theme_menu' )}
   ...the def, the nav and the undef...
   {/cache-block}
   ```

2. Prüfen Sie es mit `php bin/php/eztemplatecheck.php -s<site> <file>` und laden Sie zwei Seiten von `<site>` neu.
3. Legen Sie in der Administration einen Ordner **Test Schulungsmenü** direkt unterhalb der obersten Ebene des
   Inhaltsbaums der Website an und veröffentlichen Sie ihn.
4. Laden Sie eine Seite von `<site>` neu: Der Ordner erscheint im Menü, weil die Veröffentlichung die Cache-Blöcke
   geleert hat.
5. Provozieren Sie nun den Fehler. Legen Sie in `page_footer.tpl` die Begrüßung aus Lektion 2.2 in einen Block, dessen
   einziger Schlüssel eine Konstante ist:

   ```
   {cache-block keys=array( 'training_theme_footer' )}
   ...the greeting line...
   {/cache-block}
   ```

6. Leeren Sie die Cache-Blöcke: `php bin/php/ezcache.php --clear-id=template-block`.
7. Melden Sie sich auf `<site>` mit Ihrem Konto an und laden Sie eine Seite: Die Fußzeile begrüßt Sie.
8. Öffnen Sie ein privates Browserfenster, das nicht angemeldet ist, und laden Sie dieselbe Seite. Notieren Sie, was
   die Fußzeile sagt.
9. Reparieren Sie die Schlüssel: `keys=array( 'training_theme_footer', $current_user.contentobject_id )`. Leeren Sie
   `template-block` erneut und wiederholen Sie die Schritte 7 und 8.
10. Entfernen Sie den Ordner **Test Schulungsmenü** (verschieben Sie ihn in den Papierkorb) und laden Sie neu: Das Menü
    zeigt ihn nicht mehr.

## Erwartetes Ergebnis

- Schritt 4 und Schritt 10: Das Menü folgt Veröffentlichung und Entfernen ohne manuelles Leeren des Caches.
- Schritt 8: Das anonyme Fenster zeigt die Begrüßung der angemeldeten Person; das ist der Fehler.
- Schritt 9: Das anonyme Fenster zeigt keine Begrüßung, das angemeldete zeigt sie.
- Der Trainer prüft die endgültige `page_footer.tpl` von zwei Teilnehmenden: Der Benutzer ist Teil der Schlüssel, oder
  die Begrüßung steht außerhalb des Blocks.

## Quiz

1. Welchen Teil einer Seite bewahrt der View-Cache der Inhalte auf, und welcher Teil läuft bei jeder Anfrage?
2. Was geschieht standardmäßig mit allen Cache-Blöcken, wenn Inhalte veröffentlicht werden?
3. Ein Block gibt „Hallo, <name>“ aus und hat `keys=array( $module_result.uri )`. Was geht schief?
4. Welcher Parameter leert einen Block nur, wenn unterhalb eines bestimmten Knotens etwas veröffentlicht wird?
   A. `expiry` B. `keys` C. `subtree_expiry` D. `ignore_content_expiry`
5. Welcher Befehl leert alle Cache-Blöcke?

<details>
<summary>Antworten</summary>

1. Der Hauptbereich der Seiten von `content/view` wird aufbewahrt; das Pagelayout läuft jedes Mal
   ([View-Cache und Cache-Blöcke](../../../../../eng-us/help/creators/website-creators/view-cache-and-cache-blocks.md)).
2. Sie werden geleert, damit Menüs neue Inhalte zeigen.
3. Die Begrüßung des ersten Besuchers wird pro Adresse gespeichert und allen anderen Besuchern dieser Seite gezeigt.
4. **C.**
5. `php bin/php/ezcache.php --clear-id=template-block`.

</details>

## Weiterführende Literatur

- [View-Cache und Cache-Blöcke](../../../../../eng-us/help/creators/website-creators/view-cache-and-cache-blocks.md): Parameter und
  Fehlersuche bei veralteter Ausgabe.
- [Leistung und Caching für Websites](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md): das größere
  Bild.
- [Caches](../../../../anleitungen/caches.md): alle Caches einer Installation und wie Sie sie leeren.

## Verwandte Seiten

- [Modul 2: Templates, Overrides und das Pagelayout](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
