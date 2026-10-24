# expimageeditor: the image editor of admin4

This page is for editors who adjust images while editing content, and for administrators who switch the editor on.
`expimageeditor` is an image editor beside the image attribute in the edit form of the `admin4` design (and `admin4l`,
which draws with it). It edits raster images (crop, rotate, flip, resize, brightness, contrast, black and white,
sepia, pixelate or blur a region, watermark) and SVG drawings (shapes, paths, freehand, text, fill and stroke), and it
can start a new SVG drawing for an image attribute without an image. It works without jQuery and under the
administration's Content Security Policy. It ships inside the kernel's repository, like `ezjscore` and `ezoe`, and
is off until it is activated; the setup wizard and the kickstarter activate it for a new installation. Added in
6.0.15 (2026-10-22). The full reference is the extension's
`extension/expimageeditor/README.md`.

## Switch it on

1. In `settings/override/site.ini.append.php`, `[ExtensionSettings] ActiveExtensions[]=expimageeditor`. Switch `ezie`
   off: both replace the same include of `content/datatype/edit/ezimage.tpl`.
2. Give editors the policy **expimageeditor/edit** (administrators with `*/*` have it). Without it the button is not
   shown.
3. `php bin/php/ezpgenerateautoloads.php -e`, clear the caches, and deploy with `--kernel --packer` (new classes,
   new scripts and styles).

## Use it

1. Edit an object with an image attribute in admin4. Beside the image, **Edit image** (or **New SVG drawing** when
   the attribute has no image yet).
2. Choose a tool in the toolbar; every tool has a label, a keyboard shortcut and a panel with its settings. The
   arrow keys move a selection, Escape asks before it discards changes, and the focus returns to the button when
   the dialog closes.
3. **Save** stores the result as the image of your draft; the form shows the new image without a reload. The
   published version keeps its own image until you publish the draft.

## What the server checks when it stores an image

- You are logged in, the draft is **yours**, and you may edit the object in the attribute's language (as
  `content/edit` requires); the request carries the session's form token (`X-CSRF-Token`).
- The kind stays the same: an SVG stays an SVG, a raster image a raster image. The type is judged by content and
  must be on `expimageeditor.ini OutputMIMETypes[]` and `image.ini AllowedMIMETypes[]`.
- Limits: the class attribute's maximum file size, `expimageeditor.ini MaxFileSize` (20 MB) and `MaxPixels`
  (25 megapixels), and `image.ini MaxImagePixels`.
- **A raster image is re-encoded**: GD decodes it and writes it anew, so nothing but the pixels is stored (no
  appended data, no metadata). The copy waits in `<cache directory>/expimageeditor/` under a random name and is
  removed when the request ends. `ReEncodeRaster=disabled` turns this off.
- **An SVG is stored only as the output of `expSvgSanitizer`**: no script, no event attribute, no `foreignObject`, no
  reference outside the drawing, no `javascript:` link.

Each refusal answers with an HTTP status and a translated message, which the editor shows in its status line.

## Settings

`expimageeditor.ini [EditorSettings]`: `OutputMIMETypes[]`, `Quality`, `ReEncodeRaster`, `MaxPixels`,
`MaxFileSize`, `NewDrawingSize`, `AspectRatios[]`, `Watermarks[]`, `Fonts[]`, `TextToPaths`. The table with every
default is in the extension's README, "Settings".

## Languages and designs

English and German (`extension/expimageeditor/translations/ger-DE/`). The colors come from admin4's tokens, so the
dialog follows the light and the dark theme. Only admin4 (and admin4l) has the editor; the classic admin designs
have `ezie` where a site installs it ([ezie, Install it](ezie.md#install-it)).

## Tests

`php vendor/bin/phpunit --testsuite expimageeditor` (DB-less: the store's checks, the re-encoding, the limits, the
sanitizer, the form token, the template and scripts under the Content Security Policy).

## Related

- [ezie, the image editor of the classic admin designs](ezie.md)
- [SVG images](../../../bc/6.0/svg-images.md)
- [Content Security Policy](../content-security-policy.md)
