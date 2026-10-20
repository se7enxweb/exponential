---
title: exphelp.ini [PdfSettings]
audience: developer, admin
category: examples
summary: "The settings of [PdfSettings] in exphelp.ini: Enabled, PageSize, FontFiles, BoldFontFiles, ItalicFontFiles, BoldItalicFontFiles, MonoFontFiles, FallbackFontFiles"
keywords: exphelp.ini, PdfSettings, INI, setting, Enabled, PageSize, FontFiles, BoldFontFiles, ItalicFontFiles, BoldItalicFontFiles, MonoFontFiles, FallbackFontFiles
type: generated
generated: exp:help:examples
---
# exphelp.ini [PdfSettings]

The block `[PdfSettings]` of `settings/exphelp.ini`, with 8 setting(s).

> "Download as PDF" of a help page (exphelp/pdf/<page id>, ./console exp:help pdf <page id>), drawn by the kernel's  
> own PDF library (lib/ezpdf); nothing outside the installation is called. Files are cached in <help cache>/pdf.  

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Enabled

> false hides the button and makes exphelp/pdf a 404  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
Enabled=true
*/ ?>
```

## PageSize

> A4 or Letter  

Default: `A4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
PageSize=A4
*/ ?>
```

## FontFiles

> TrueType (.ttf) fonts: of each list the first file that exists is used (a relative path is taken from the install  
> root). A TrueType font draws every character it has (embedded as a composite font, the text stays selectable).  
> With no regular font found the PDF core fonts are used and text is transliterated to Windows-1252.  

Default: a list of 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf`, `/usr/share/fonts/TTF/DejaVuSans.ttf`, `/usr/share/fonts/dejavu/DejaVuSans.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# an empty "FontFiles[]" first replaces the shipped list; without it the items are added
FontFiles[]
FontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf
FontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf
FontFiles[]=/usr/share/fonts/TTF/DejaVuSans.ttf
FontFiles[]=/usr/share/fonts/dejavu/DejaVuSans.ttf
*/ ?>
```

## BoldFontFiles

Default: a list of 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf`, `/usr/share/fonts/TTF/DejaVuSans-Bold.ttf`, `/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# an empty "BoldFontFiles[]" first replaces the shipped list; without it the items are added
BoldFontFiles[]
BoldFontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf
BoldFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf
BoldFontFiles[]=/usr/share/fonts/TTF/DejaVuSans-Bold.ttf
BoldFontFiles[]=/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf
*/ ?>
```

## ItalicFontFiles

Default: a list of 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Oblique.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans-Oblique.ttf`, `/usr/share/fonts/TTF/DejaVuSans-Oblique.ttf`, `/usr/share/fonts/dejavu/DejaVuSans-Oblique.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# an empty "ItalicFontFiles[]" first replaces the shipped list; without it the items are added
ItalicFontFiles[]
ItalicFontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Oblique.ttf
ItalicFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans-Oblique.ttf
ItalicFontFiles[]=/usr/share/fonts/TTF/DejaVuSans-Oblique.ttf
ItalicFontFiles[]=/usr/share/fonts/dejavu/DejaVuSans-Oblique.ttf
*/ ?>
```

## BoldItalicFontFiles

Default: a list of 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-BoldOblique.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans-BoldOblique.ttf`, `/usr/share/fonts/TTF/DejaVuSans-BoldOblique.ttf`, `/usr/share/fonts/dejavu/DejaVuSans-BoldOblique.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# an empty "BoldItalicFontFiles[]" first replaces the shipped list; without it the items are added
BoldItalicFontFiles[]
BoldItalicFontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-BoldOblique.ttf
BoldItalicFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans-BoldOblique.ttf
BoldItalicFontFiles[]=/usr/share/fonts/TTF/DejaVuSans-BoldOblique.ttf
BoldItalicFontFiles[]=/usr/share/fonts/dejavu/DejaVuSans-BoldOblique.ttf
*/ ?>
```

## MonoFontFiles

> Monospace for code; without one the core font Courier is used (and the regular font for what Courier lacks)  

Default: a list of 4: `/usr/share/fonts/dejavu-sans-mono-fonts/DejaVuSansMono.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf`, `/usr/share/fonts/TTF/DejaVuSansMono.ttf`, `/usr/share/fonts/dejavu/DejaVuSansMono.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# an empty "MonoFontFiles[]" first replaces the shipped list; without it the items are added
MonoFontFiles[]
MonoFontFiles[]=/usr/share/fonts/dejavu-sans-mono-fonts/DejaVuSansMono.ttf
MonoFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf
MonoFontFiles[]=/usr/share/fonts/TTF/DejaVuSansMono.ttf
MonoFontFiles[]=/usr/share/fonts/dejavu/DejaVuSansMono.ttf
*/ ?>
```

## FallbackFontFiles

> Characters the regular font lacks (Chinese, Japanese, Korean ...); loaded only when a page needs it  

Default: a list of 3: `/usr/share/fonts/google-droid-sans-fonts/DroidSansFallbackFull.ttf`, `/usr/share/fonts/truetype/droid/DroidSansFallbackFull.ttf`, `/usr/share/fonts/google-noto-cjk/NotoSansCJK-Regular.ttc`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# an empty "FallbackFontFiles[]" first replaces the shipped list; without it the items are added
FallbackFontFiles[]
FallbackFontFiles[]=/usr/share/fonts/google-droid-sans-fonts/DroidSansFallbackFull.ttf
FallbackFontFiles[]=/usr/share/fonts/truetype/droid/DroidSansFallbackFull.ttf
FallbackFontFiles[]=/usr/share/fonts/google-noto-cjk/NotoSansCJK-Regular.ttc
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
