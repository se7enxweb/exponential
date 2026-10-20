---
title: exphelp.ini [PdfSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PdfSettings] in exphelp.ini: Enabled, PageSize, FontFiles, BoldFontFiles, ItalicFontFiles, BoldItalicFontFiles, MonoFontFiles, FallbackFontFiles"
keywords: Einstellung, exphelp.ini, PdfSettings, INI, setting, Enabled, PageSize, FontFiles, BoldFontFiles, ItalicFontFiles, BoldItalicFontFiles, MonoFontFiles, FallbackFontFiles
type: generated
generated: exp:help:examples
---
# exphelp.ini [PdfSettings]

Der Block `[PdfSettings]` von `settings/exphelp.ini`, mit 8 Einstellung(en).

> "Download as PDF" of a help page (exphelp/pdf/<page id>, ./console exp:help pdf <page id>), drawn by the kernel's  
> own PDF library (lib/ezpdf); nothing outside the installation is called. Files are cached in <help cache>/pdf.  

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> false hides the button and makes exphelp/pdf a 404  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
Enabled=true
*/ ?>
```

## PageSize

> A4 or Letter  

Standard: `A4`.

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

Standard: eine Liste mit 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf`, `/usr/share/fonts/TTF/DejaVuSans.ttf`, `/usr/share/fonts/dejavu/DejaVuSans.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# ein leeres "FontFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FontFiles[]
FontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans.ttf
FontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf
FontFiles[]=/usr/share/fonts/TTF/DejaVuSans.ttf
FontFiles[]=/usr/share/fonts/dejavu/DejaVuSans.ttf
*/ ?>
```

## BoldFontFiles

Standard: eine Liste mit 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf`, `/usr/share/fonts/TTF/DejaVuSans-Bold.ttf`, `/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# ein leeres "BoldFontFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BoldFontFiles[]
BoldFontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Bold.ttf
BoldFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf
BoldFontFiles[]=/usr/share/fonts/TTF/DejaVuSans-Bold.ttf
BoldFontFiles[]=/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf
*/ ?>
```

## ItalicFontFiles

Standard: eine Liste mit 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Oblique.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans-Oblique.ttf`, `/usr/share/fonts/TTF/DejaVuSans-Oblique.ttf`, `/usr/share/fonts/dejavu/DejaVuSans-Oblique.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# ein leeres "ItalicFontFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ItalicFontFiles[]
ItalicFontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-Oblique.ttf
ItalicFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans-Oblique.ttf
ItalicFontFiles[]=/usr/share/fonts/TTF/DejaVuSans-Oblique.ttf
ItalicFontFiles[]=/usr/share/fonts/dejavu/DejaVuSans-Oblique.ttf
*/ ?>
```

## BoldItalicFontFiles

Standard: eine Liste mit 4: `/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-BoldOblique.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSans-BoldOblique.ttf`, `/usr/share/fonts/TTF/DejaVuSans-BoldOblique.ttf`, `/usr/share/fonts/dejavu/DejaVuSans-BoldOblique.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# ein leeres "BoldItalicFontFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BoldItalicFontFiles[]
BoldItalicFontFiles[]=/usr/share/fonts/dejavu-sans-fonts/DejaVuSans-BoldOblique.ttf
BoldItalicFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSans-BoldOblique.ttf
BoldItalicFontFiles[]=/usr/share/fonts/TTF/DejaVuSans-BoldOblique.ttf
BoldItalicFontFiles[]=/usr/share/fonts/dejavu/DejaVuSans-BoldOblique.ttf
*/ ?>
```

## MonoFontFiles

> Monospace for code; without one the core font Courier is used (and the regular font for what Courier lacks)  

Standard: eine Liste mit 4: `/usr/share/fonts/dejavu-sans-mono-fonts/DejaVuSansMono.ttf`, `/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf`, `/usr/share/fonts/TTF/DejaVuSansMono.ttf`, `/usr/share/fonts/dejavu/DejaVuSansMono.ttf`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# ein leeres "MonoFontFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MonoFontFiles[]
MonoFontFiles[]=/usr/share/fonts/dejavu-sans-mono-fonts/DejaVuSansMono.ttf
MonoFontFiles[]=/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf
MonoFontFiles[]=/usr/share/fonts/TTF/DejaVuSansMono.ttf
MonoFontFiles[]=/usr/share/fonts/dejavu/DejaVuSansMono.ttf
*/ ?>
```

## FallbackFontFiles

> Characters the regular font lacks (Chinese, Japanese, Korean ...); loaded only when a page needs it  

Standard: eine Liste mit 3: `/usr/share/fonts/google-droid-sans-fonts/DroidSansFallbackFull.ttf`, `/usr/share/fonts/truetype/droid/DroidSansFallbackFull.ttf`, `/usr/share/fonts/google-noto-cjk/NotoSansCJK-Regular.ttc`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
# ein leeres "FallbackFontFiles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FallbackFontFiles[]
FallbackFontFiles[]=/usr/share/fonts/google-droid-sans-fonts/DroidSansFallbackFull.ttf
FallbackFontFiles[]=/usr/share/fonts/truetype/droid/DroidSansFallbackFull.ttf
FallbackFontFiles[]=/usr/share/fonts/google-noto-cjk/NotoSansCJK-Regular.ttc
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
