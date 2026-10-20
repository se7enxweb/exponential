---
title: pdf.ini [PDFGeneral]
audience: developer, admin
category: examples
summary: "The settings of [PDFGeneral] in pdf.ini: Format, Orientation, TopMargin, BottomMargin, LeftMargin, RightMargin, OutputCharset, Transliterate ..."
keywords: pdf.ini, PDFGeneral, INI, setting, Format, Orientation, TopMargin, BottomMargin, LeftMargin, RightMargin, OutputCharset, Transliterate, LinkUnderline, MaxImageWidth, MaxImageHeight, ImageScaling, Font, FontEncoding
type: generated
generated: exp:help:examples
---
# pdf.ini [PDFGeneral]

The block `[PDFGeneral]` of `settings/pdf.ini`, with 14 setting(s).

Never edit `settings/pdf.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/pdf.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/pdf.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/pdf.ini.append.php` in an extension (read when the extension is active).

## Format

Default: `A4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Format=A4
*/ ?>
```

## Orientation

Default: `portrait`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Orientation=portrait
*/ ?>
```

## TopMargin

Default: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
TopMargin=80
*/ ?>
```

## BottomMargin

Default: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
BottomMargin=100
*/ ?>
```

## LeftMargin

Default: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
LeftMargin=80
*/ ?>
```

## RightMargin

Default: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
RightMargin=80
*/ ?>
```

## OutputCharset

> The charset the pdf text is written in.  
>
> Ignored when Font below names a .ttf file, because a composite font needs no  
> conversion at all - see Font.  
>
> Otherwise it has to be a single byte charset. The bundled fonts have a 256  
> entry encoding, so a multi byte charset would be drawn one byte at a time and  
> read as mojibake - "cafÃ©" rather than "café". Setting utf-8 here while using  
> one of those fonts is refused, with an error in the debug log, and  
> windows-1252 used instead. Point Font at a .ttf to get real utf-8.  
>
> This shipped as iso-8859-1, which was the wrong choice: the fonts are written  
> with /Encoding /WinAnsiEncoding, and WinAnsi and iso-8859-1 disagree over  
> 0x80-0x9F. That range holds the em dash, the euro sign, the bullet and the  
> curly quotes, so the font could draw them and the conversion threw them away  
> and wrote a question mark. windows-1252 is what WinAnsiEncoding is, so it  
> keeps them. Change this only together with FontEncoding below.  

Default: `windows-1252`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
OutputCharset=windows-1252
*/ ?>
```

## Transliterate

> Ignored when Font names a .ttf file, where nothing needs degrading.  
>
> Degrade a character the charset above cannot hold to its closest ascii rather  
> than to a question mark, so a name in Polish or Czech comes out readable:  
> Lódz rather than ód. Characters with no ascii likeness at all, Japanese for  
> instance, still cannot be drawn by these fonts.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Transliterate=enabled
*/ ?>
```

## LinkUnderline

> Draw a rule under a link as well as colouring it blue.  
>
> The rule used to be drawn far too short, because the end of a link was  
> measured from the start of the line rather than from the start of the link.  
> That is fixed, so this is on. Set it to disabled for links that are coloured  
> and clickable but not underlined.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
LinkUnderline=enabled
*/ ?>
```

## MaxImageWidth

> How large an image may be drawn, in points, 72 to the inch.  
>
> The templates hand images over at their pixel size and that size is then used  
> as points, so a photograph 1600 pixels wide asked for 1600 points on a page  
> 595 points wide and ran off the paper in every direction. Images are now  
> scaled down to fit, keeping their proportions. Only down: something already  
> small enough is left as it is.  
>
> Left empty each falls back to what the page has room for - the paper less its  
> margins - which is the sensible ceiling. Set them to hold images well inside  
> the text, for instance 360 for a half page width on A4.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
MaxImageWidth=
*/ ?>
```

## MaxImageHeight

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
MaxImageHeight=
*/ ?>
```

## ImageScaling

> Set to disabled to draw images at whatever size they are handed over at,  
> which is what happened before, overflowing page and all.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
ImageScaling=enabled
*/ ?>
```

## Font

> The font used unless a template selects another.  
>
> A name without an extension is one of the faces bundled beside the library -  
> Helvetica, Times-Roman, Courier. Those are simple fonts: 256 slots, one byte  
> per character, so the text has to be squeezed into OutputCharset first and  
> anything that does not fit is transliterated or lost.  
>
> Give the full path of a .ttf file instead and the font is embedded as a  
> composite font, which addresses its glyphs by number rather than through a  
> 256 entry table. Then there is nothing to squeeze text into: utf-8 goes  
> through as it is, OutputCharset and Transliterate stop applying, and every  
> character the font actually has is drawn. The text stays selectable and  
> searchable, because the map back to unicode is written with it.  
>
> What you get is then decided by the font rather than by this software.  
> DejaVuSans covers latin, latin extended, greek, cyrillic, hebrew, armenian  
> and georgian. For chinese, japanese or korean point this at a font that has  
> them - DroidSansFallback, or one of the Noto CJK faces.  
>
> Font=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf  

Default: `lib/ezpdf/classes/fonts/Helvetica`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
Font=lib/ezpdf/classes/fonts/Helvetica
*/ ?>
```

## FontEncoding

> Which glyph each of the font's 256 slots holds. Must agree with OutputCharset  
> or accents land on the wrong characters. WinAnsiEncoding is windows-1252,  
> MacRomanEncoding is mac-roman, and StandardEncoding is the fonts' own layout,  
> which is neither.  

Default: `WinAnsiEncoding`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[PDFGeneral]
FontEncoding=WinAnsiEncoding
*/ ?>
```

## Related

- [All blocks of pdf.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/pdf.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
