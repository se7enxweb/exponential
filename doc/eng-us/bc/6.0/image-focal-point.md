# Image focal point and crop

From Exponential 6.0.15 an image (the `ezimage` datatype) can carry a **focal point**, the part of the picture that
matters, and a **crop rectangle**, the part of the original its aliases are made from. Both are stored per attribute
version, in the attribute's XML, so a draft can have its own without touching the published version. No database
change, no new setting: an image without them is converted exactly as before.

## What they do to the aliases

- **The crop rectangle is cut out first.** An alias made from the original (no `Reference=`, or
  `Reference=original`) is made from the rectangle, before its own filters. An alias made from another alias gets
  it through that alias. The `original` itself never changes: the uploaded file stays as it was.
- **Crop filters center on the focal point.** Every `geometry/crop=<width>;<height>;<x>;<y>` filter of an alias
  ignores its `x;y` and puts its window around the focal point, as far as the image allows (the window never leaves
  the image). This is what makes scale-and-crop aliases such as

  ```ini
  [teaser]
  Reference=
  Filters[]
  Filters[]=geometry/scalewidth=468
  Filters[]=geometry/crop=468;396;0;0
  ```

  keep the face or the product in the picture instead of always the top left corner. To know where the window
  lies, the size of the image is followed through the alias's filters: the `geometry/scale*` filters, `geometry/crop`
  and `border`, and the filters that keep the size (`colorspace*`, `luminance/*`, `color/monochrome`, `filter/*`,
  `border/color`, `border/width`, `flatten`). A filter whose result size is not known (`resize`, a filter of an
  extension's own handler) stops it: the crop filters after it keep their own `x;y`.
- **Without a focal point** a crop filter keeps its own `x;y` (inside the crop rectangle, when there is one).
- **ImageMagick's crop now resets the page offset** (`image.ini [ImageMagick] Filters[]=geometry/crop=-crop
  %1x%2+%3+%4 +repage`). Without `+repage` a filter after a crop (the alias's own crop after the crop rectangle, or a
  scale) worked on the old canvas and could make an empty image. The pixels of an alias with a single crop at its end
  stay the same; a PNG or GIF alias no longer carries the old canvas offset. An installation that overrides the
  `[ImageMagick]` filter list should add `+repage` there too.
- **Scale-only aliases** (`geometry/scaledownonly` and friends) only change with the crop rectangle. A design that
  shows them with CSS `object-fit: cover` can use the focal point as `object-position`, see below.

## Changing them

```php
/** @var eZImageAliasHandler $image */
$image = $attribute->attribute( 'content' );
if ( method_exists( $image, 'setFocalPoint' ) && $image->supportsFocalPoint() )
{
    // fractions of the original's width and height, from 0 to 1; null removes one
    $image->setFocalPoint( array( 'x' => 0.7, 'y' => 0.3 ),
                           array( 'x' => 0.1, 'y' => 0, 'width' => 0.8, 'height' => 1 ) );
    $image->store( $attribute );
}
```

- `setFocalPoint( $focal, $crop = null )` returns `false` and changes nothing when a value is not valid (outside 0
  to 1, a rectangle outside the image or smaller than 1% of it) or the image cannot have one (no image, or an SVG,
  whose aliases are the original file). The whole image (`0, 0, 1, 1`) as the crop rectangle means none.
- When something changes, the original is stored again for this version, as an upload in this version is, under a
  new serial number, and every alias made so far is dropped. The aliases are made again, around the new focal
  point, when they are next shown. The files of other versions (the published one, when this is a draft) are never
  touched, and the new aliases have new file names, so no browser or proxy cache shows the old ones.
- Change them on a draft and publish it with the normal publish flow; publishing moves the files as for any image
  uploaded in a draft.
- `focalPoint()` and `cropRectangle()` return the stored values (or `null`); `supportsFocalPoint()` says whether the
  image can have them.
- Packages carry them (`focal-x`, `focal-y`, `crop-x`, `crop-y`, `crop-width` and `crop-height` on the attribute's
  node); a package without them imports as before.

The math is in `expImageFocalPoint` (`lib/ezimage/classes/expimagefocalpoint.php`). Its `plan()` returns, for an
alias's filters, the part of the original each alias shows, which is what editors use for live previews;
`filterChain()` gives the filters from the original to an alias through its `Reference=` chain.

## In templates

```smarty
{def $image = $node.data_map.image.content}
<img src={$image.teaser.url|ezroot} width="{$image.teaser.width}" height="{$image.teaser.height}"
     alt="{$image.alternative_text|wash}"
     {if $image.focal_point}class="focal" data-focal="{$image.focal_point.position|wash}"{/if} />
```

- `focal_point`: `null`, or `x`, `y` (fractions) and `position` (`"70% 30%"`, ready for CSS `object-position`).
- `crop_rectangle`: `null`, or `x`, `y`, `width` and `height` (fractions of the original).

Keep CSS in the design's stylesheets: pass the position as a `data-` attribute or a class and set `object-position`
from a script or a stylesheet rule, not in a `style=""` attribute.

## The XML

```xml
<ezimage serial_number="2" ...>
  <original attribute_id="57" attribute_version="3" attribute_language="eng-US"/>
  <focal x="0.7" y="0.3"/>
  <crop x="0.1" y="0" width="0.8" height="1"/>
  <alias name="teaser" .../>
</ezimage>
```

An older kernel reading this XML ignores the two elements and makes its aliases from the whole image as before.

## Checks

`tests/tests/lib/ezimage/expImageFocalPointTest.php` (DB-less): the values, the XML, the centering through scale
and crop filters, the crop rectangle, `Reference=` chains, and pixels of aliases made by the image manager with and
without a focal point.

## Related pages

- [Images and image aliases](../../help/cms-developers/images-and-image-aliases.md)
- [SVG images](svg-images.md)
