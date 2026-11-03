# Fokuspunkt und Zuschnitt von Bildern

Ab Exponential 6.0.15 kann ein Bild (der Datentyp `ezimage`) einen **Fokuspunkt** tragen, den Teil des Bildes, auf
den es ankommt, und ein **Zuschnittsrechteck**, den Teil des Originals, aus dem seine Varianten erzeugt werden. Beide
werden je Attributversion im XML des Attributs gespeichert; ein Entwurf kann also eigene haben, ohne die
veröffentlichte Version zu berühren. Keine Datenbankänderung, keine neue Einstellung: Ein Bild ohne sie wird genau wie
bisher umgerechnet.

## Was sie mit den Varianten machen

- **Das Zuschnittsrechteck wird zuerst ausgeschnitten.** Eine Variante, die aus dem Original erzeugt wird (kein
  `Reference=` oder `Reference=original`), entsteht aus dem Rechteck, vor ihren eigenen Filtern. Eine Variante, die
  aus einer anderen Variante erzeugt wird, erhält es über diese. Das `original` selbst ändert sich nie: Die
  hochgeladene Datei bleibt, wie sie war.
- **Zuschnittfilter zentrieren auf den Fokuspunkt.** Jeder Filter `geometry/crop=<Breite>;<Höhe>;<x>;<y>` einer
  Variante übergeht sein `x;y` und legt sein Fenster um den Fokuspunkt, soweit das Bild es zulässt (das Fenster
  verlässt das Bild nie). Damit behalten Varianten, die skalieren und zuschneiden, etwa

  ```ini
  [teaser]
  Reference=
  Filters[]
  Filters[]=geometry/scalewidth=468
  Filters[]=geometry/crop=468;396;0;0
  ```

  das Gesicht oder das Produkt im Bild statt immer der linken oberen Ecke. Um zu wissen, wo das Fenster liegt, wird
  die Größe des Bildes durch die Filter der Variante verfolgt: die Filter `geometry/scale*`, `geometry/crop` und
  `border` sowie die Filter, die die Größe nicht ändern (`colorspace*`, `luminance/*`, `color/monochrome`, `filter/*`,
  `border/color`, `border/width`, `flatten`). Ein Filter, dessen Ergebnisgröße nicht bekannt ist (`resize`, ein Filter
  eines eigenen Handlers einer Erweiterung), beendet das: Die Zuschnittfilter danach behalten ihr eigenes `x;y`.
- **Ohne Fokuspunkt** behält ein Zuschnittfilter sein eigenes `x;y` (innerhalb des Zuschnittsrechtecks, wenn es eines
  gibt).
- **Der Zuschnitt von ImageMagick setzt jetzt den Seitenversatz zurück** (`image.ini [ImageMagick]
  Filters[]=geometry/crop=-crop %1x%2+%3+%4 +repage`). Ohne `+repage` arbeitete ein Filter nach einem Zuschnitt (der
  eigene Zuschnitt der Variante nach dem Zuschnittsrechteck oder eine Skalierung) auf der alten Leinwand und konnte ein
  leeres Bild erzeugen. Die Pixel einer Variante mit einem einzigen Zuschnitt am Ende bleiben gleich; eine PNG- oder
  GIF-Variante trägt den alten Leinwandversatz nicht mehr. Eine Installation, die die Filterliste in `[ImageMagick]`
  überschreibt, sollte dort ebenfalls `+repage` ergänzen.
- **Varianten, die nur skalieren** (`geometry/scaledownonly` und verwandte), ändern sich nur mit dem
  Zuschnittsrechteck. Ein Design, das sie mit CSS `object-fit: cover` zeigt, kann den Fokuspunkt als
  `object-position` verwenden, siehe unten.

## Ändern

```php
/** @var eZImageAliasHandler $image */
$image = $attribute->attribute( 'content' );
if ( method_exists( $image, 'setFocalPoint' ) && $image->supportsFocalPoint() )
{
    // Anteile an Breite und Höhe des Originals, von 0 bis 1; null entfernt einen Wert
    $image->setFocalPoint( array( 'x' => 0.7, 'y' => 0.3 ),
                           array( 'x' => 0.1, 'y' => 0, 'width' => 0.8, 'height' => 1 ) );
    $image->store( $attribute );
}
```

- `setFocalPoint( $focal, $crop = null )` gibt `false` zurück und ändert nichts, wenn ein Wert nicht gültig ist
  (außerhalb von 0 bis 1, ein Rechteck außerhalb des Bildes oder kleiner als 1 % davon) oder das Bild keinen haben
  kann (kein Bild oder ein SVG, dessen Varianten die Originaldatei sind). Das ganze Bild (`0, 0, 1, 1`) als
  Zuschnittsrechteck bedeutet keines.
- Ändert sich etwas, wird das Original für diese Version neu gespeichert, wie ein Upload in dieser Version, unter
  einer neuen Seriennummer, und jede bisher erzeugte Variante entfällt. Die Varianten werden beim nächsten Anzeigen um
  den neuen Fokuspunkt neu erzeugt. Die Dateien anderer Versionen (der veröffentlichten, wenn dies ein Entwurf ist)
  werden nie berührt, und die neuen Varianten haben neue Dateinamen; kein Browser- oder Proxy-Cache zeigt also die
  alten.
- Ändern Sie sie in einem Entwurf und veröffentlichen Sie ihn auf dem üblichen Weg; das Veröffentlichen verschiebt
  die Dateien wie bei jedem in einem Entwurf hochgeladenen Bild.
- `focalPoint()` und `cropRectangle()` geben die gespeicherten Werte zurück (oder `null`); `supportsFocalPoint()`
  sagt, ob das Bild sie haben kann.
- Pakete übernehmen sie (`focal-x`, `focal-y`, `crop-x`, `crop-y`, `crop-width` und `crop-height` am Knoten des
  Attributs); ein Paket ohne sie wird wie bisher importiert.

Die Berechnung steckt in `expImageFocalPoint` (`lib/ezimage/classes/expimagefocalpoint.php`). Sein `plan()` liefert
für die Filter einer Variante den Teil des Originals, den die Variante zeigt; das nutzen Editoren für
Live-Vorschauen. `filterChain()` liefert die Filter vom Original bis zu einer Variante über ihre `Reference=`-Kette.

## In Templates

```smarty
{def $image = $node.data_map.image.content}
<img src={$image.teaser.url|ezroot} width="{$image.teaser.width}" height="{$image.teaser.height}"
     alt="{$image.alternative_text|wash}"
     {if $image.focal_point}class="focal" data-focal="{$image.focal_point.position|wash}"{/if} />
```

- `focal_point`: `null` oder `x`, `y` (Anteile) und `position` (`"70% 30%"`, fertig für CSS `object-position`).
- `crop_rectangle`: `null` oder `x`, `y`, `width` und `height` (Anteile am Original).

Halten Sie CSS in den Stylesheets des Designs: Geben Sie die Position als `data-`-Attribut oder Klasse weiter und
setzen Sie `object-position` aus einem Skript oder einer Stylesheet-Regel, nicht in einem `style=""`-Attribut.

## Das XML

```xml
<ezimage serial_number="2" ...>
  <original attribute_id="57" attribute_version="3" attribute_language="eng-US"/>
  <focal x="0.7" y="0.3"/>
  <crop x="0.1" y="0" width="0.8" height="1"/>
  <alias name="teaser" .../>
</ezimage>
```

Ein älterer Kernel, der dieses XML liest, übergeht die beiden Elemente und erzeugt seine Varianten wie bisher aus dem
ganzen Bild.

## Prüfungen

`tests/tests/lib/ezimage/expImageFocalPointTest.php` (ohne Datenbank): die Werte, das XML, das Zentrieren durch
Skalier- und Zuschnittfilter, das Zuschnittsrechteck, `Reference=`-Ketten und Pixel von Varianten, die der
Bildmanager mit und ohne Fokuspunkt erzeugt.

## Verwandte Seiten

- [Bilder und Bildvarianten](../../hilfe/cms-entwickler/bilder-und-bildvarianten.md)
- [SVG-Bilder](svg-bilder.md)
