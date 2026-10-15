# SVG images

From 15 October 2026 the image datatype accepts SVG images (`.svg`, and `.svgz` compressed with gzip) on every
site, upgrades included. An SVG is a document a browser can run script from, so it is never stored as it was
uploaded: the kernel's SVG sanitizer, `expSvgSanitizer`, keeps only what is known to be safe, and the stored file
is its output. The file is also served with headers that stop it from running or loading anything when it is opened
directly, as a second line of defence.

## Where it applies

Every path that stores an image: the edit form, `content/upload` and the upload module, the multi-upload tool, the
rich text editor's upload dialog, the AJAX uploader of the object relation list, WebDAV, the package import and
`fromString()` imports. They all store through `eZImageAliasHandler::initializeFromHTTPFile()` or
`initializeFromFile()`, which sanitize every file that is an SVG by its content (its root element is `<svg>`,
whatever its name or the type the browser sent). The checks before storing (`eZImageType::imageFileError()`, used by
the edit form, `eZContentUpload` and the AJAX uploader) refuse an SVG the sanitizer would refuse, with a message.

The package wizard's thumbnails and style images stay raster images: an SVG is refused there.

## The settings

`settings/image.ini`:

```ini
[ImageUploadSettings]
AllowedMIMETypes[]=image/svg+xml
# enabled (default) or disabled
SanitizeSVG=enabled
```

- `image/svg+xml` on `AllowedMIMETypes[]` and `SanitizeSVG=enabled` (both shipped): SVG is accepted and stored
  sanitized.
- `SanitizeSVG=disabled`, or `image/svg+xml` not on the list: SVG is refused with "Images of the type
  image/svg+xml are not accepted.", as before. There is no setting that stores an SVG unsanitized.

`eZImageType::svgAccepted()` tells whether SVG is accepted.

## What the sanitizer keeps

The sanitizer parses the file with `DOMDocument`, without network access, without entity substitution and without
loading a DTD, and builds a new document from an allow-list (the svg-sanitize and DOMPurify SVG profiles were the
reference):

- **Elements**: `svg`, `g`, `defs`, `symbol`, `use`, `switch`, `title`, `desc`, `metadata`, `view`, `a`, `path`,
  `rect`, `circle`, `ellipse`, `line`, `polyline`, `polygon`, `image`, `style`, `text`, `tspan`, `textPath`,
  `linearGradient`, `radialGradient`, `stop`, `pattern`, `clipPath`, `mask`, `marker`, `filter` and the filter
  primitives (`feBlend` to `feTurbulence`), `animate`, `animateTransform`, `animateMotion`, `mpath`, `set`. Always
  written in the SVG namespace; an SVG without the namespace gets it, so it renders as an image.
- **Attributes**: the SVG geometry and presentation attributes (`d`, `points`, `viewBox`, `fill`, `stroke`,
  `transform`, the gradient, filter and text attributes ...), `id`, `class`, `style`, `role`, `aria-*`, `xml:space`,
  `xml:lang`.
- **References**: `href` and `xlink:href` to an internal `#fragment`. `<a>` also keeps `http:`, `https:` and
  `mailto:` links, and `<image>` keeps an embedded `data:image/png`, `jpeg`, `gif` or `webp` in base64 that really
  is such an image.
- **CSS**, in `style` attributes and `<style>` elements, after CSS escapes and comments are resolved: declarations
  without a script or `data:` URL and with `url()` only to an internal `#fragment`.

## What it removes

- `<script>`, `<foreignObject>` (with any HTML inside it), `iframe`, `handler`, `listener`, XInclude, and every
  element of another namespace (XHTML, Inkscape, Sodipodi, Illustrator), with its content.
- Every `on*` event attribute, and every attribute of another namespace.
- `javascript:`, `vbscript:` and `data:` URLs (other than the embedded raster images above), anywhere.
- `<use>`, `<image>`, `<feImage>`, `<mpath>` and `<textPath>` references to anything but an internal fragment (an
  `<image>`, `<use>`, `<feImage>` or `<mpath>` without a safe reference is removed altogether).
- `url(...)` to anything but an internal fragment, in style and presentation attributes (`fill`, `mask`, `filter`,
  `clip-path`, `marker-*` ...).
- `@import`, `expression()`, `behavior` and `-moz-binding` in CSS.
- An animation (`animate`, `set` ...) of `href`, `xlink:href`, `style` or an event attribute, or with a value that
  would be removed.
- Comments, processing instructions (`<?xml-stylesheet?>`), CDATA wrappers and the DOCTYPE.

It refuses the file (and the upload says why) when:

- it is not well-formed XML, or its root element is not `<svg>`;
- it has a DOCTYPE with an internal subset or an entity declaration (XXE, "billion laughs");
- it is UTF-16 or UTF-32 (only UTF-8 and ASCII-based encodings are read);
- it is larger than 5 MB after decompression, has more than 50 000 nodes or is nested deeper than 128 elements;
- a `.svgz` does not decompress, or decompresses to more than 5 MB (a gzip bomb);
- nothing that draws remains.

A `.svgz` upload is stored as plain `.svg`.

```php
$clean = expSvgSanitizer::sanitize( $markup );          // the safe markup, or false
echo expSvgSanitizer::lastError();                       // why it was refused
expSvgSanitizer::sanitizeFile( $path, $targetPath );     // writes plain SVG; false leaves the file unchanged
list( $width, $height ) = expSvgSanitizer::dimensions( $path );
```

## Aliases

An SVG is never scaled, and never handed to GD or ImageMagick (`eZImageManager::createImageAlias()` and
`convert()` refuse it). Every alias of an SVG image (`small`, `medium`, `large`, `reference`, an alias of your own)
is the original file: the same URL, with the width and height of the original, read from its `width` and `height`
attributes (`px`, `pt`, `pc`, `mm`, `cm`, `in`) or, without them, its `viewBox`. So
`{attribute_view_gui attribute=$node.data_map.image image_class=medium}` shows the SVG with the size of the
original; scale it in the template or with CSS where a smaller size is wanted. The aliases are not written to the
attribute's XML, so moving, copying and removing the image handle one file.

## Serving headers

An SVG shown with `<img>` or from a style sheet never runs script. Opened directly (its URL in the address bar, an
`<object>` or `<iframe>`) it is a document of the site's origin. As a second line of defence the SVG files are
served with:

```
Content-Security-Policy: sandbox; default-src 'none'; style-src 'unsafe-inline'
X-Content-Type-Options: nosniff
```

What ships:

- **`.htaccess_root`** sets them for `.svg` and `.svgz` files (needs `mod_headers`).
- **Exponential Velocity** sets them for every `.svg` and `.svgz` it serves as a static file, over HTTP/1.1 and
  HTTP/2 (from v0.0.4.53; `Q.webserver.svgSandbox false` turns it off).
- **`content/download`** (`eZFile::downloadHeaders()`) and the cluster gateway (`index_cluster.php`) send them for
  an SVG.

An installation served from a virtual host configuration instead of `.htaccess` needs the lines there.

**Apache** (inside the site's `<VirtualHost>` or `<Directory>`, with `mod_headers`):

```apache
<FilesMatch "\.svgz?$">
    Header always set Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'"
    Header always set X-Content-Type-Options "nosniff"
</FilesMatch>
```

**nginx** (in the `server` block, before the location that serves the storage):

```nginx
location ~* \.svgz?$ {
    add_header Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'" always;
    add_header X-Content-Type-Options "nosniff" always;
    try_files $uri =404;
}
```

In nginx an `add_header` in a location replaces the ones of the server block: repeat there any header the server
block sets for every response.

## For extension developers

- An upload path of your own that stores an image file directly, without the image datatype, must call
  `eZImageType::sanitizeSVGFile( $path )` for a file `expSvgSanitizer::isSVGFile( $path )` recognises, and refuse
  the file when it returns false.
- An upload tool that maps file types to classes (`upload.ini [CreateSettings] MimeClassMap[]`) maps
  `image/svg+xml` to the image class through the shipped `MimeClassMap[image]=image`; a file type filter of the
  tool needs `*.svg` (and `*.svgz`).

## Tests

- `tests/tests/lib/ezimage/expSvgSanitizerTest.php`: 63 harmful SVGs (script, event attributes on every kind of
  element, foreignObject and iframe, script and data links, entity tricks, XXE, billion laughs, CSS `url()`,
  `@import` and `expression()`, animation of `href`, mixed case, namespaces, CDATA, comments, base64, nested
  `<svg>`) all come out without anything that runs or loads; 12 Bootstrap Icons and 3 drawings keep every element
  and attribute (and render pixel for pixel the same in Chromium).
- `tests/tests/kernel/datatypes/ezimage/expImageSvgTest.php`: the datatype's acceptance and refusals, the setting,
  the package thumbnails, the aliases and the image manager.
