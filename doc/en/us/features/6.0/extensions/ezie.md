# ezie: the image editor

This page is for editors who adjust images while editing content, and for administrators who add watermarks. `ezie`
("eZ Image Editor") is an image editor inside the edit form of any content object that has an image attribute. Crop,
flip, rotate, add a watermark, change contrast and brightness, then **Save & Close**: the edited image is stored as a
new version of the attribute.

The September to October 2026 releases (6.0.3 to 6.0.8) repaired it for current PHP and jQuery, fixed several defects
that made tools fail, and closed two security holes. If you tried the editor before and gave up on it, try it again.

## Requirements

Exponential 6, PHP 8.1 or later, GD2 or ImageMagick, and an Exponential edit form (admin, or a design that loads the
edit form scripts). It uses jQuery, jQuery UI, the Jcrop and colorpicker plugins and the Zeta Components image
conversion.

## Use it

1. Edit an object that has an image attribute, and open the editor from the image field.
2. Choose a tool in the **Actions** window: select, crop, flip, rotate, watermark, contrast, brightness. Keyboard
   shortcuts are shown in each tool's title.
3. Work on the selection (keep ratio, free, or type a size), apply, and undo as needed.
4. Click **Save & Close**. The edit form shows the saved image wherever the attribute is.

**Quit** asks a translated question ("If you leave without saving, all your modifications will be definitely lost"),
and only when something was changed.

### What runs where

The server side of each tool is a view of the `ezie` module (`module.php`):

| View | Purpose |
|---|---|
| `prepare` | opens the editing session |
| `tool_crop`, `tool_flip_hor`, `tool_flip_ver`, `tool_rotation`, `tool_pixelate`, `tool_watermark` | tools |
| `filter_bw` (black and white), `filter_sepia`, `filter_contrast`, `filter_brightness` | filters |
| `save_and_quit`, `no_save_and_quit` | the two ways out |

Select, zoom, undo and redo work in the browser (`design/standard/javascript/ezie.gui.config.bind.tool_*.js`). So the
editor also has pixelate, black and white and sepia, which the tool list above does not name.

The third-party scripts it bundles are named in `ezinfo.php` (jQuery UI 1.8.9, Jcrop 0.9.8, jQuery Hotkeys 0.7.9); the
colour picker is in `design/standard/javascript/colorpicker`. jQuery itself comes from `ezjscore`.

### Add your own watermark

1. Put the image (usually a PNG file) in a `design/standard/images/watermarks` folder of your extension, for example
   `extension/mydesign/design/standard/images/watermarks/logo_ubuntu.png`.
2. Register it in `extension/mydesign/settings/image.ini.append.php`:

```ini
[eZIE]
watermarks[]=logo_ubuntu.png
```

3. Clear the caches. The watermark is offered in the editor.

Only a plain file name that exists in a `design/standard/images/watermarks` folder is accepted. The shipped ones are
`elephpant.png` and `ez-logo.png`.

## What was fixed

| Release | Problem | Fixed |
|---|---|---|
| 6.0.3 | The editor requested a `jquery-migrate-1.1.1.min.js` file that nobody ships | The request is gone |
| 6.0.6 | Sites that allow **WebP** output (`image.ini [OutputSettings] AllowedOutputFormat` contains `image/webp`): the ImageMagick and GD handlers cannot write it, so every tool answered 500 | Only output formats the chosen converter supports are passed on (JPEG, PNG and GIF if none is left). The GD2 check read the wrong setting and logged an undefined variable on every call |
| 6.0.6 | GD handler on **PHP 8.5**: `imagedestroy()` deprecated; pixelate passed fractional sizes | No deprecations; pixel sizes are whole numbers of at least one |
| 6.0.6 | A **horizontal flip** with ImageMagick flipped the whole image (`-flop` ignores `-region`) while GD flipped the selection | The selection is cut out, flopped and composed back |
| 6.0.6 | Blur, levels and saturation answered a fatal error (classes missing) | The views are removed; the editor no longer offers them |
| 6.0.6 | Working folders stayed in the public cache when the edit page was closed without closing the editor | They are removed; opening the editor removes your own folders older than a day |
| 6.0.6 | Opening the editor failed with `e.indexOf is not a function` on jQuery 3.7 (`.load()` shortcut removed) | Bound with `.on('load')` |
| 6.0.6 | Select, crop and watermark threw before the selection box appeared (Jcrop 0.9.8 read the removed `$.browser`) | Fixed |
| 6.0.6 | The thumbnail box could not be attached again after detaching | Fixed |
| 6.0.6 | The editor opened under the admin's left column and Enter in its fields submitted the draft | Opens above the admin, side by side and centred, fits the window; long panels scroll |
| 6.0.6 | Slider styles requested missing images (404) | Use the shipped icon sheet |
| 6.0.6 | Tool handlers bound again on every open ("Select (s) (s) (s)"), current tool not highlighted, shortcuts `i` and `1` threw | Namespaced handlers; bindings without function skipped |
| 6.0.6 | Selection options "Keep ratio", "Free" threw an exception each time | Work |
| 6.0.6 | A failed action showed only "image not loaded" and Close discarded all changes | The error dialog shows the server's reason and Close only hides it |
| 6.0.7 | jQuery 4 and jQuery UI 1.14 | Opening, tools, undo, selection and watermark work |

## Security (6.0.6)

* The watermark name from the request used to be appended to a folder path, so `../` read any image
  the web server can read. Only a plain file name found in the watermark folder is accepted.
* The working folder of an edit came from a `key` the browser sent, so any user with access to the
  `ezie` module could make the tools write into, and `no_save_and_quit` recursively delete, any folder
  under the installation. The folder is now built from the current user and the image; the key is
  ignored.
* Only `prepare` checked permissions, with the language code cast to an integer, which disabled the
  language limitation. Every view now requires the image attribute to belong to a **draft of the
  current user** that the user may edit in that language, and `prepare` checks that the attribute belongs
  to the object and language in its URL. A published version can no longer be changed through
  `save_and_quit`.
* Failures answer JSON `{"error": ...}` with 400, 403, 404 or 500 (before: an HTML error page, a `die()`
  text, or a 200 with a broken image). Request values are read from POST only and validated (numbers, a
  six digit colour, a selection with four positive numbers).
* The editor sends the form token as the `ezxform_token` field and as the `X-CSRF-Token` header, read for
  every request from the hidden span or from the `csrf-token` meta tag.

## Languages

The extension carries translation files in `translations/<locale>/translation.ts`: cro-HR, ell-GR, eng-US, esl-ES,
fre-FR, ger-DE, ita-IT, jpn-JP, pol-PL, por-BR. The German file holds 47 messages (count `<message` in
`translations/ger-DE/translation.ts`). The texts are looked up in the contexts `design/standard/ezie` and
`design/standard/content/datatype`.

After editing a translation file, refresh the compiled translation cache with `./console exp:ezgeneratetranslationcache`
and clear the template and content caches. `./console exp:ezchecktranslation ger-DE` prints statistics of the kernel's
`share/translations/ger-DE/translation.ts`, not of this extension's file.

## Related pages

- [YUI removal and jQuery 4](../../../bc/6.0/yui-removal.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/ezie.md) and [release notes](../../../changelogs/extensions/ezie.md)
- [Change ledger](../../../history/ledger/ezie.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-03](../../../history/extensions/months/2024-03.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
