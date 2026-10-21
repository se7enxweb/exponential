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

> „Als PDF herunterladen“ einer Hilfeseite (exphelp/pdf/<page id>, ./console exp:help pdf <page id>), gezeichnet von der eigenen  
> PDF-Bibliothek des Kernels (lib/ezpdf); nichts außerhalb der Installation wird aufgerufen. Die Dateien werden in <help cache>/pdf gecacht.  

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> false blendet die Schaltfläche aus und macht exphelp/pdf zu 404  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
Enabled=true
*/ ?>
```

## PageSize

> A4 oder Letter  

Standard: `A4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[PdfSettings]
PageSize=A4
*/ ?>
```

## FontFiles

> TrueType-Schriften (.ttf): Aus jeder Liste wird die erste vorhandene Datei verwendet (ein relativer Pfad gilt ab dem Wurzelverzeichnis  
> der Installation). Eine TrueType-Schrift zeichnet jedes Zeichen, das sie hat (als zusammengesetzte Schrift eingebettet, der Text bleibt markierbar).  
> Wird keine normale Schrift gefunden, werden die PDF-Grundschriften verwendet und der Text nach Windows-1252 transliteriert.  

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

> Festbreitenschrift für Code; ohne eine wird die Grundschrift Courier verwendet (und die normale Schrift für das, was Courier fehlt)  

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

> Zeichen, die der normalen Schrift fehlen (Chinesisch, Japanisch, Koreanisch ...); nur geladen, wenn eine Seite sie braucht  

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
