# Schaltflächen für schnelle Platzierungen

Manche Seiten fragen nach einer Stelle im Inhaltsbaum: der Startplatz der Inhaltsprüfungen
([content-checks.md](inhaltspruefungen.md), „Wo eine Prüfung beginnt“) und die Upload-Seite von ezmultiupload (ihr
Zielplatz). Neben der Knotenauswahl bieten sie Schaltflächen für die bekannten Stellen mit einem Klick an, **Websites**
und **Medien**, und eine Zeile mit den Stellen direkt unterhalb einer davon (die Websites unter Websites, Bilder,
Dateien und Multimedia unter Medien). Die Schaltflächen werden aus dem Inhaltsbaum und den Einstellungen ermittelt, nie
aus festen Knoten-IDs, sodass sie auf jeder Installation stimmen.

## Der Kernel-Teil: `expNodeQuickPick`

`kernel/classes/expnodequickpick.php` findet die Stellen und baut die Zeilen; jede Seite entscheidet, was eine
Schaltfläche tut (ein Link oder eine Schaltfläche, die ein Feld setzt) und welche Stellen sie anbieten darf.

| Methode | |
|---|---|
| `source( $key )` | wie ein Schlüssel gefunden wird: `tree` (Knoten 1), `content`, `media`, `users`, `setup`, `design` (content.ini `[NodeSettings] RootNode`, `MediaRootNode` ...), eine Knoten-ID (`58`), `remote:<node remote id>`, ein URL-Alias-Pfad (`media/images`); `null` für alles andere |
| `nodeID( $source )` | die Knoten-ID einer Quelle, 0, wenn sie nicht vorhanden ist |
| `label( $key, $nodeName )` | „Whole tree“, „Websites“, „Media“, „Users“, „Setup“, „Design“ für die benannten Schlüssel, sonst der Name des Knotens |
| `childRows( $parent )` | die Kinder eines Knotens in seiner eigenen Sortierreihenfolge (Lesezugriff gilt), jedes mit seinem Knoten |
| `row( $children, $currentPath, $currentID, $limit, $accept )` | die sichtbaren Kinder, die `$accept` annimmt (etwa eine Berechtigungsprüfung), die ersten `$limit`, `more`, wenn es mehr gibt; jeder Eintrag hat `current` (die aktuelle Stelle) und `within` (die aktuelle Stelle liegt darunter) |
| `state( $nodeID, $currentID, $currentPath )` | `current` und `within` einer Schaltfläche |

`source()`, `label()`, `row()` und `state()` lesen keine Datenbank und werden ohne eine getestet
(`tests/tests/kernel/expNodeQuickPickTest.php`).

## Das Aussehen: `.a4-quickpick`

Eine gemeinsame Komponente von `stylesheets/components.css` (design/admin und design/admin4):

```html
<div class="a4-toolbar a4-quickpick">
  <div class="a4-row" role="group" aria-labelledby="x-presets">
    <span class="a4-quickpick-label" id="x-presets">Quick locations</span>
    <button type="button" class="a4-btn a4-btn-small" aria-pressed="true">Media</button>
    ...
  </div>
</div>
```

Die aktuelle Stelle ist gefüllt: `aria-pressed="true"` bei einer Schaltfläche, `aria-current="true"` bei einem Link
(oder `.a4-btn-primary`). Die Stelle, in der die aktuelle liegt, hat `.is-within` (umrandet; sagen Sie es auch in einem
Text mit `.a4-sr-only`). Zeilen brechen um; auf einem Telefon bekommt die Beschriftung eine eigene Zeile. Nur Tokens,
sodass der dunkle Modus und erzwungene Farben folgen.

## Verwender

- `expContentCheckScope` (Inhaltsprüfungen): Voreinstellungen `contentcheck.ini [ScopeSettings] PresetList[]`; seine
  Methoden `presetSource()`, `presetNodeID()`, `presetLabel()` und `siteRow()` sind jetzt die von `expNodeQuickPick`.
- ezmultiupload 6.1.8 (Upload-Seite, Zielplatz): `ezmultiupload.ini [QuickLocationSettings]`, siehe
  `doc/upload-page.md` der Extension.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
