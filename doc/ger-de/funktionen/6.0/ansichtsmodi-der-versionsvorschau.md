# Versionsvorschau in anderen Ansichtsmodi

Lesen Sie diese Seite, wenn Redakteure oder Freigebende einen Entwurf so sehen müssen, wie er gedruckt oder exportiert
wird (ein PDF-Layout eines Rundschreibens, bevor es veröffentlicht wird), nicht nur in der Vollansicht der Website.

## Kurz gesagt

`content/versionview` zeigt eine Version in einem Ansichtsmodus, der als ungeordneter Parameter `(view_mode)` angegeben
wird:

```
content/versionview/<object>/<version>/<language>/(view_mode)/print
```

gibt die Version mit `node/view/print.tpl` aus. Erlaubt sind nur die Ansichtsmodi, die in `content.ini [VersionView]
ViewModes[]` aufgeführt sind; jeder andere antwortet mit „nicht verfügbar“ (Kernel-Fehler 3). Ohne den Parameter ist
die Vorschau wie bisher `full`.

## Einrichten

In `settings/override/content.ini.append.php` (oder in der `content.ini.append.php` einer Extension):

```ini
[VersionView]
ViewModes[]
ViewModes[]=full
ViewModes[]=print
```

Behalten Sie `full` in der Liste: Das zeigt die Vorschau ohne den Parameter, und das bietet die
Administrationsoberfläche zuerst an.

`design/standard` hat `node/view/print.tpl`: den Namen des Knotens und jedes Attribut mit Inhalt, ohne Navigation,
Schaltflächen oder Kinder. Eine Website gibt einer Klasse mit einer Überschreibung ein eigenes Drucklayout:

```ini
# design/<site design>/override.ini.append.php
[print_article]
Source=node/view/print.tpl
MatchFile=print/article.tpl
Subdir=templates
Match[class_identifier]=article
```

Der Ansichtsmodus ist der Design-Schlüssel `viewmode` der Vorschau, sodass `Match[viewmode]` in einer Überschreibung wie
bei `content/view` funktioniert, und die Template-Variable `view_mode` sagt einem `content/view/versionview.tpl`, in
welchem Ansichtsmodus es ausgeben soll.

## In der Administrationsoberfläche

Sind mehrere Ansichtsmodi aufgeführt, bietet sie das Feld **Ansichtskontrolle** der Versionsvorschau (Designs admin,
admin3 und admin4) neben der Übersetzung, der Platzierung und dem Siteaccess an; **Ansicht aktualisieren** zeigt die
Version im gewählten Modus und behält ihn bei, wenn eine andere Sprache oder ein anderer Siteaccess gewählt wird. Der
Vorschaurahmen und sein Link zur Vollbildansicht tragen den Ansichtsmodus. Ist nur `full` aufgeführt, ändert sich auf
der Seite nichts.

## Sicherheit

- Ein Ansichtsmodus benennt ein Template, daher wird nur ein aufgeführter verwendet, und nur ein Name aus Buchstaben,
  Ziffern, `_` und `-` zählt als einer, auch wenn die Einstellung etwas anderes aufführt (`Versionview::viewMode()`).
- Ein mit **Ansicht aktualisieren** gesendeter Ansichtsmodus, der nicht aufgeführt ist, wird ignoriert; der aktuelle
  bleibt (`Versionview::changedViewMode()`).
- Die Vorschau braucht wie bisher `content/versionread`; der Ansichtsmodus ändert nichts daran, wer eine Version sehen
  darf.

## Website-Designs mit eigenem Vorschau-Template

Die Vorschau einer Version in einem Siteaccess (`/site_access/<name>`) gibt das `content/view/versionview.tpl` des
Website-Designs aus. Das Template von `design/standard` gibt den Knoten im gewählten Ansichtsmodus aus; ein
Website-Design mit eigener Kopie (ezwebin, ezdemo und das Media-Theme haben eine) zeigt `full`, bis auch sein Template
die Variable verwendet:

```
{node_view_gui view=cond( is_set( $view_mode ), $view_mode, 'full' ) content_node=$node ...}
```

Die Seite um die Vorschau herum ist das Seitenlayout der Website. Für einen Ausdruck ohne Kopfbereich und Menü der
Website lassen Sie das Druck-Stylesheet der Website (`@media print`) sie ausblenden, oder drucken Sie den
Vorschaurahmen für sich (über seinen Link zur Vollbildansicht).

## Wie es funktioniert

`\Exponential\View\Kernel\Content\Versionview::viewMode()` entscheidet über den Ansichtsmodus der Adresse,
`::changedViewMode()` über den des Formulars, `::viewModes()` listet die Einstellung auf. `kernel/content/module.php`
deklariert `(view_mode)` und das Formularfeld `SelectedViewMode` der Aktion `ChangeSettings`.

Tests (ohne Datenbank): `eZContentVersionviewViewModeTest`.

## Verwandte Seiten

- [Filter für Zugriff und Ansichts-Cache](zugriffs-und-cache-filter.md)
- [PDF-Export](pdf-export.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
