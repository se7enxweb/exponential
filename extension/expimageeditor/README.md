<!--
Copyright CJW Network and Exponential Foundation. All rights reserved.
GNU General Public License v2.0 (or any later version).
-->
# expimageeditor — image editor for the image attribute in admin4

An image editor for the image datatype (`ezimage`) in the admin design `admin4`. It takes the place of the
editor of `ezie`: a button **Edit image** beside the stored image of a draft opens a modal dialog, the image is
edited in the browser, and **Save** stores the result as the image of that draft. Nothing changes in the published
version until the draft is published; **Cancel** leaves the draft untouched. An image attribute without an image
offers **New SVG drawing** instead: the SVG editor starts with an empty drawing, and **Save** stores it as
`drawing.svg` (only where SVG is accepted, see `NewDrawingSize`).

Two editors share one frame:

- **Raster images** (JPEG, PNG, WebP, GIF), on [Cropper.js](https://github.com/fengyuanchen/cropperjs) 2.3.1 and
  canvas tools of its own: crop (free, the image's own ratio, the ratios of fixed-shape image aliases and of
  `AspectRatios[]`), rotate by 90° or by a free angle with a background colour, flip, resize, brightness, contrast,
  black and white, sepia (each shown on the image at once, for the selection or the whole image, and made a step
  with **Apply**), pixelate or blur a region, watermark (text in the system font or a configured font, or a
  configured image), undo/redo, zoom.
- **SVG images**, on [@svgedit/svgcanvas](https://github.com/SVG-Edit/svgedit) 7.4.2 with a toolbar of its own (not
  SVG-Edit's UI): select, rectangle, ellipse, line, path, freehand, text, delete, duplicate, bring to front, send to
  back, fill / no fill, stroke, stroke width, opacity, font size, font, undo/redo, zoom. Optionally the text in
  configured fonts is saved as glyph outlines (opentype.js).

Everything runs without jQuery and under the admin's Content Security Policy: no inline script, no inline style,
no `eval()`. The libraries are loaded only when the editor of their kind opens for the first time.

## Requirements

- Exponential 6 with SVG support in the image datatype (`expSvgSanitizer`, `eZImageType::imageFileError()`,
  `eZImageAliasHandler::initializeFromFile()`; see `doc/bc/6.0/svg-images.md`).
- The admin design `admin4` with its `content/datatype/edit/ezimage.tpl`, which includes
  `content/datatype/edit/ezimage_editor.tpl` — this extension overrides that include.
- `ezformtoken` active: the save request carries the page's form token (`X-CSRF-Token`), and ezformtoken is what
  checks it.
- A current browser with `<dialog>`, `fetch`, `Promise`, custom elements (Cropper.js 2), the FontFace API (fonts) and
  constructed style sheets (SVG drawings with a `<style>` element). Tested in Chromium, Firefox and WebKit.

## Activation

In `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
#ActiveExtensions[]=ezie
ActiveExtensions[]=expimageeditor
```

Switch `ezie` off: both replace the same include, and only one of them can win. Then regenerate the autoloads and
clear all caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
rm -f var/cache/ini/*.php
php bin/php/ezcache.php --clear-all      # once per siteaccess with its own VarDir: -s <siteaccess>
```

The extension registers itself as a design extension (`settings/design.ini.append.php`), as a module repository
(`settings/module.ini.append.php`) and as a translation extension (`settings/site.ini.append.php`, German in
`translations/ger-DE/`).

Only `admin4` has a template. The classic admin designs (`admin3`, `admin2` and the
siteaccesses that use them) have **no** image editor once `ezie` is switched off.

## Permissions

The module `expimageeditor` has one function, **`expimageeditor/edit`**. A role needs a policy for it before its
users see the button; the template asks `fetch( 'user', 'has_access_to', ... )`. Administrators with `*/*` have it.

The policy only opens the door. The save view checks on every request that

- the user is logged in (the anonymous user is refused: all anonymous visitors share one user ID),
- the attribute is an `ezimage` attribute of a version that is a draft, internal draft or repeat draft **created by
  this user**,
- the user may edit the object in the attribute's language (`eZContentObject::canEdit()`, as `content/edit` does).

## Settings: `expimageeditor.ini`

All in `[EditorSettings]`; override in `settings/override/expimageeditor.ini.append.php` or a siteaccess.

| Setting | Default | Meaning |
|---|---|---|
| `OutputMIMETypes[]` | `image/jpeg`, `image/png`, `image/webp`, `image/svg+xml` | The types the editor may store. A raster image is saved in its own type when that type is listed, otherwise as PNG (so a GIF becomes a PNG by default, and an animated GIF loses its animation). An SVG stays an SVG. Each type must also be on `image.ini [ImageUploadSettings] AllowedMIMETypes[]`, or the datatype refuses it. |
| `Quality` | `0.92` | Quality of a saved JPEG or WebP, 0.1 to 1. |
| `NewDrawingSize` | `800x600` | The size of a new drawing in pixels, `<width>x<height>`. **New SVG drawing** is offered beside an image attribute without an image when SVG is accepted (`image.ini AllowedMIMETypes[]` with `image/svg+xml`, `SanitizeSVG` not disabled) and `OutputMIMETypes[]` lists `image/svg+xml`. admin4's `content/datatype/edit/ezimage.tpl` includes `ezimage_new.tpl` there; its own copy draws nothing, this extension's draws the button. |
| `AspectRatios[]` | `1:1`, `4:3`, `3:2`, `16:9` | Ratios the crop tool offers, `<width>:<height>`. They come after the ratios of the image aliases that give an image a fixed shape: an alias of `image.ini [AliasSettings] AliasList[]` with a `Filters[]` entry `geometry/scaleexact=<w>;<h>` or `geometry/crop=<w>;<h>;...`, labelled `<alias> (<w>:<h>)`. A ratio appears once, under its first label. "Free" and "Original" are always there. |
| `Watermarks[]` | empty | Images the watermark tool offers besides a text, `<label>;<design path>`, resolved like `ezdesign` (a PNG with transparency works best). Example: `Watermarks[]=Logo;images/watermarks/logo.png`. An entry without exactly two parts is ignored. |
| `Fonts[]` | empty | Fonts the text tools offer besides the system font (the watermark text of the raster editor, the text tool of the SVG editor): `<label>;<font-family>;<design path>[;<outline path>]`. See below. |
| `TextToPaths` | `enabled` | Whether "Save text in added fonts as paths" is ticked when the SVG editor opens. Only offered when a font of `Fonts[]` can be converted. `enabled` or `disabled`. |

### Fonts

```ini
Fonts[]=Lora;Lora;fonts/expimageeditor/lora-latin-400-normal.woff2;fonts/expimageeditor/lora-latin-400-normal.woff
```

- `<label>` is what the font choice shows.
- `<font-family>` is the name the text carries in the drawing. Letters, digits, spaces and hyphens only; any other
  entry is left out without a message.
- `<design path>` is the file the browser loads (`.woff2`, `.woff`, `.ttf` or `.otf`), resolved like `ezdesign` on
  this site; a file of another origin is left out. The editor loads it with the FontFace API when it opens, once per
  page. A font that fails to load is named in the status line and the default font is used.
- `<outline path>` (optional) is for "Save text in added fonts as paths": opentype.js reads `.woff`, `.ttf` and
  `.otf`, **not** `.woff2`. Give the same font in one of those formats here. Without it, the design path is used if
  it is not `.woff2`; a font given only as `.woff2` is offered all the same, but its text stays text in the saved SVG.

Three fonts are shipped in `design/admin4/fonts/expimageeditor/`, all under the SIL Open Font License 1.1; the
shipped settings list none of them:

```ini
Fonts[]=Lora;Lora;fonts/expimageeditor/lora-latin-400-normal.woff2;fonts/expimageeditor/lora-latin-400-normal.woff
Fonts[]=Noto Serif;Noto Serif;fonts/expimageeditor/noto-serif-regular.woff2;fonts/expimageeditor/noto-serif-regular.woff
Fonts[]=Noto Sans;Noto Sans;fonts/expimageeditor/noto-sans-regular.woff2;fonts/expimageeditor/noto-sans-regular.woff
```

- Lora Regular, Latin (@fontsource/lora 5.3.0, `LICENSE`).
- Noto Serif and Noto Sans Regular 2.015 (`LICENSE-Noto.txt`), from the releases of
  [notofonts/latin-greek-cyrillic](https://github.com/notofonts/latin-greek-cyrillic), unhinted TTF converted to
  woff2 and woff with fontTools, glyphs unchanged: Latin with its extensions, Greek, Cyrillic and Vietnamese
  (2965 characters each, about 155 KB as woff2).

With "Save text in added fonts as paths" ticked, a text whose characters are all in configured fonts is saved as a
`<g role="img" aria-label="...">` with a `<title>` (the words) and one `<path>` per run of characters, so it looks
the same where the font is not installed. The drawing keeps its `<text>` until saving, and so does a text that
cannot be converted as a whole (another font, a character the font lacks, collapsed white space, `<textPath>`).

## Saving

**Save** posts the result (a `Blob`, field `image`) with the page's form token in `X-CSRF-Token` to

```
expimageeditor/save/<attribute id>/<version>
```

The view (`modules/expimageeditor/save.php`, code in `classes/runnable/views/expimageeditor/save.php`) answers JSON:
`{ "html": ... }` on success, `{ "error": "<translated message>" }` with an HTTP status otherwise. Nothing is stored
unless every check passes (`expImageEditorStore`):

1. POST only (405); attribute ID and version are numbers (400).
2. Permissions as above (403/404).
3. An upload arrived without error, and it is an HTTP upload (400; 413 when PHP's upload limits cut it off).
4. Not larger than the class attribute's maximum file size (413).
5. The attribute has a stored image (404).
6. The same kind: an SVG stays an SVG, a raster image stays a raster image, judged by content (415).
7. `eZImageType::imageFileError()`: type by content, `AllowedMIMETypes[]`, `MaxImagePixels`, SVG through the
   sanitizer (415, with the datatype's message).
8. The type is on `OutputMIMETypes[]` (415).

The image is stored with `eZImageAliasHandler::initializeFromFile()` — an SVG only as the output of
`expSvgSanitizer` — under the stored image's name with the extension of the new type; the alternative text is kept,
the view cache of the object is cleared. The previous version keeps its own image.

The answer's `html` is the attribute's edit template rendered anew, **without `style=""` attributes** (the standard
image view writes `style="border: ..."`, which an enforced `style-src 'self'` would report when the editor parses the
fragment). The editor puts its first `.block` in place of the old one, so the edit form shows the new image without
a reload.

## Content Security Policy

The admin's policy (`site.ini [ContentSecurityPolicy]`, `script-src 'self'; style-src 'self'`, report-only on admin
siteaccesses by default) is kept without exceptions:

- Settings and texts reach the scripts as JSON in `data-conf` / `data-labels` of `#expimageeditor-config`; the
  scripts are files of this site loaded with `ezscript_require()` or `<script src>`.
- Styles come from `expimageeditor.css` or are set through the CSSOM (`element.style.setProperty()`), which the
  policy does not restrict. The svgcanvas bundle is patched at build time for that (see "Rebuilding").
- Choices are radio buttons rather than `<select>`, except the font choice with more than six fonts: WebKit reports
  one `style-src-attr` violation for every `<select>`, even one in plain HTML — a browser quirk, not something the
  editor writes.
- An SVG's `style=""` attributes are turned into presentation attributes and its `<style>` elements are applied
  through a constructed style sheet (only plain rules, scoped to the drawing, without anything that loads a URL);
  both are put back on save, where the server sanitises the file again.

Measured with the policy enforced: 0 violations in Chromium, Firefox and WebKit, apart from the WebKit `<select>`
quirk above.

## SVG specifics

- The stored SVG has already passed `expSvgSanitizer`; the editor sanitises it again through svgcanvas, and the
  server sanitises the saved result once more.
- **Aliases are the original.** The kernel serves the original SVG for every image alias and never hands an SVG to
  GD or ImageMagick (`doc/bc/6.0/svg-images.md`). The editor does not create PNG aliases for SVGs.
- `width`, `height` and `viewBox` of the stored file are written back on save; a layer svgcanvas added only to hold
  the content is unwrapped again.
- Not supported: PDF or raster export of svgcanvas (left out of the bundle); there are no tools for gradients,
  patterns, filters, clip paths or markers, and none for editing `<style>` rules (they are put back unchanged).

## Keyboard

The toolbar is one tab stop; the arrow keys, Home and End move between its buttons. Escape asks, like Cancel,
whether to discard changes. The shortcuts work while the focus is not in a field (in a field only those with
Ctrl/⌘, except the ones the field needs itself, such as undo in a text field). Every tool button names its shortcut
in its tooltip and in `aria-keyshortcuts`.

| Both editors | |
|---|---|
| Ctrl/⌘+Z | Undo |
| Ctrl/⌘+Shift+Z, Ctrl/⌘+Y | Redo |
| `+` / `-` / `0` | Zoom in / zoom out / fit to window (SVG: `=` zooms in as well) |

| Raster editor | |
|---|---|
| `C` | Crop (the selection: drag on the image, or type X, Y, width, height; the arrow keys move it) |
| `[` / `]` | Rotate left / right by 90° |
| `A` | Rotate by an angle |
| `H` / `V` | Flip horizontally / vertically |
| `S` | Resize |
| `B` / `K` | Brightness / contrast |
| `G` / `E` | Black and white / sepia |
| `P` / `U` | Pixelate / blur region |
| `W` | Watermark |

| SVG editor | |
|---|---|
| `V` `R` `E` `L` `P` `F` `T` | Select, rectangle, ellipse, line, path, freehand, text |
| Delete, Backspace | Delete the selection |
| Ctrl/⌘+D | Duplicate |
| Ctrl/⌘+mouse wheel | Zoom |
| With the focus on the drawing: | |
| `N` / Shift+`N` | Select the next / previous object |
| Arrow keys (Shift: by 10) | Move the selection |
| Enter | Add a shape of the current tool in the middle of the view (drawing without a mouse) |
| Escape | Drop the tool, then the selection; ends text editing |

## Files

| Path | Content |
|---|---|
| `classes/expimageeditorstore.php` | The checks and the store of the save view |
| `classes/expimageeditorexception.php` | A refusal: message (English, translated when shown) and HTTP status |
| `classes/expimageeditorfunctioncollection.php` | `fetch( 'expimageeditor', 'aspect_ratios' )` |
| `classes/runnable/views/expimageeditor/save.php`, `modules/expimageeditor/` | The module, the view `save`, the function `edit` |
| `design/admin4/templates/content/datatype/edit/ezimage_editor.tpl` | The button and, once per page, the settings and texts |
| `design/admin4/javascript/expimageeditor.js` | The frame: dialog, toolbar, panel, shortcuts, fonts, lazy loading, save |
| `design/admin4/javascript/expimageeditor_raster.js`, `expimageeditor_svg.js` | The two editors |
| `design/admin4/javascript/cropper.min.js`, `svgcanvas.min.js`, `opentype.min.js` | The libraries |
| `design/admin4/javascript/licenses/` | Their licence texts |
| `design/admin4/stylesheets/expimageeditor.css` | The dialog in admin4's tokens, light and dark |
| `design/admin4/images/expimageeditor/icons/` | Bootstrap Icons 1.13.2 (MIT), used as CSS masks |
| `design/admin4/fonts/expimageeditor/` | The sample fonts Lora, Noto Serif and Noto Sans (OFL 1.1) |
| `build/svgcanvas/`, `build/opentype/` | Reproducible esbuild bundles of the two libraries |
| `translations/` | German, and the untranslated template |

## Bundled libraries and licences

| Library | Version | Licence | File |
|---|---|---|---|
| Cropper.js | 2.3.1 | MIT | `cropper.min.js` (the release file, unchanged) |
| @svgedit/svgcanvas | 7.4.2 | MIT | `svgcanvas.min.js` (built, patched) |
| pathseg | 1.2.1 | BSD 3-Clause | in `svgcanvas.min.js` |
| opentype.js | 2.0.0 | MIT | `opentype.min.js` (built, unchanged) |
| tiny-inflate | 1.0.3 | MIT | in `opentype.min.js` |
| Bootstrap Icons | 1.13.2 | MIT | `images/expimageeditor/icons/` |
| Lora (@fontsource/lora 5.3.0) | 3.008 | SIL OFL 1.1 | `fonts/expimageeditor/` |
| Noto Serif, Noto Sans (notofonts/latin-greek-cyrillic) | 2.015 | SIL OFL 1.1 | `fonts/expimageeditor/LICENSE-Noto.txt` |

The licence texts are in `design/admin4/javascript/licenses/`, `design/admin4/images/expimageeditor/icons/LICENSE`
and `design/admin4/fonts/expimageeditor/LICENSE`; `extension.xml` lists them. The extension itself is GPL v2 or later.

## Rebuilding the bundles

`build/svgcanvas/README.md` and `build/opentype/README.md` give the exact steps (Node 20+, `npm ci`,
`node build.mjs --outfile=...`; keep `node_modules` off the repository). The lock files make the bundles
reproducible. The svgcanvas build stubs out jsPDF/svg2pdf.js and replaces svgcanvas' `setAttribute( 'style', ... )`
calls by `style.setProperty()`; a patch that no longer matches stops the build, so an update cannot drop one
silently. Cropper.js is the release file and is replaced as a whole. On every update copy the licence texts and
update `extension.xml`.

## Tests

```bash
vendor/bin/phpunit --no-coverage --fail-on-risky --fail-on-warning \
    --bootstrap tests/bootstrap.php extension/expimageeditor/tests/phpunit/unit/
```

The same call runs as the step `expimageeditor` of `.github/workflows/tests.yml`.

- `expImageEditorStoreTest` (IE-01 … IE-07): the checks of the save view without a database — target name, broken
  uploads, same kind, output types, SVG through the sanitizer, maximum file size, own draft and edit access.
- `expImageEditorDesignTest` (ID-01 … ID-05): the design files as files — the template behind the policy and its
  data, every text a script asks for is in `data-labels` and every label is used, every icon has its file and CSS
  class, the scripts the template names exist, and the scripts build no inline code or style.

### Checks

The store itself (`initializeFromFile()`, the `ezimagefile` rows) and everything in the browser need a running
installation. Check by hand, with the admin's policy enforced and the developer console open:

1. Raster image: crop, rotate, a filter, Save; the edit form shows the new image, the draft has it, the published
   version still has the old one; 0 CSP reports.
2. SVG image: draw a shape and a text, Save; the stored file is an SVG; 0 CSP reports.
3. A user without `expimageeditor/edit` sees no button; another user's draft is refused (403).

## Known gaps

- **Classic admin designs** (`admin3`, `admin2`): no editor; only `admin4` has a template.
- **WebKit `<select>`**: WebKit reports one `style-src-attr` violation per `<select>`, also in plain HTML. The
  editor uses radio buttons instead; only the font choice with more than six fonts is a `<select>`.
- **localStorage**: svgcanvas writes one key per opened SVG editor into `localStorage` (`<prefix>_startup`, its
  mechanism to coordinate between windows); the editor does not use it.
- **Text to paths** places the glyphs one by one where the browser laid out each character: no ligatures, no
  shaping of Arabic, Hebrew or other complex or right-to-left scripts.
- **viewBox not starting at `0 0`**: the drawing is shown shifted while editing; the saved file keeps its viewBox.
- **Fill of shapes coloured by a `<style>` class** has no visible effect: the class rule wins over the presentation
  attribute the editor sets.
- **GIF** is saved as PNG unless `image/gif` is added to `OutputMIMETypes[]`; animation is lost either way.
- **SVG aliases** are the original SVG; there are no raster aliases of an SVG.
- **No focus point**: the image datatype has no field for one.
