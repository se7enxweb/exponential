---
title: ezoe.ini [EditorSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EditorSettings] in ezoe.ini (ezoe): EditorEngine, Engines, UploadExtensionCheck, UploadFromUrl, UploadFromUrlMaxSize, UploadFromUrlTimeout, UploadFileExtensions, Plugins ..."
keywords: Einstellung, ezoe.ini, EditorSettings, INI, setting, EditorEngine, Engines, UploadExtensionCheck, UploadFromUrl, UploadFromUrlMaxSize, UploadFromUrlTimeout, UploadFileExtensions, Plugins, CustomTagControls, Skin, CompatibilityMode, ValidateEmbedObjects, TagPathOpenDialog, BrowseImageAlias, CustomAttributeStyleMap, XmlTagNameAlias
type: generated
generated: exp:help:examples
---
# ezoe.ini [EditorSettings]

Der Block `[EditorSettings]` von `extension/ezoe/settings/ezoe.ini` (Erweiterung ezoe), mit 16 Einstellung(en).

Bearbeiten Sie `extension/ezoe/settings/ezoe.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezoe.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezoe.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezoe.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EditorEngine

> Editor-Engine für Attribute vom Typ ezxmltext  
>  tinymce3 = eZ Online Editor auf TinyMCE 3.5 (Standard)  
>  tinymce8 = Prototyp auf TinyMCE 8, unterstützt Formatierung, Listen, Links, Anker,  
>             Tabellen, embed/embed-inline, eigene Tags und literal  
> Kann pro Benutzer mit der Einstellung ezoe\_engine überschrieben werden:  
>  /user/preferences/set/ezoe\_engine/tinymce8  

Standard: `tinymce3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
EditorEngine=tinymce3
*/ ?>
```

## Engines

> Unter dem Editor eine Schaltfläche zeigen, um zwischen tinymce3 und tinymce8 zu wechseln (als Benutzereinstellung gespeichert),  
> nützlich, solange beide Editoren parallel laufen. enabled / disabled; aus, wenn nicht gesetzt.  
> Hier absichtlich nicht gesetzt: Diese Datei wird auf Erweiterungsebene gelesen, nach jeder Siteaccess-Einstellung und jeder  
> nur für einen Siteaccess aktivierten Erweiterung (ActiveAccessExtensions\[\]), ein Wert hier würde also deren Werte überschreiben.  
> EngineSwitch=disabled  
> Eine Engine für jeden Redakteur eines Siteaccess erzwingen, egal was seine Einstellung ezoe\_engine sagt (der Engine-Schalter ist  
> dann ausgeblendet, und die Engine-Seite zeigt die Wahl als fest). Pro Siteaccess setzen, z. B. ForceEngine=tiptap. Eine Engine,  
> die nicht verwendet werden kann (fehlende Dateien, inaktive Erweiterung), wird ignoriert, die Redakteure fallen also auf die übliche Wahl zurück.  
> Hier absichtlich nicht gesetzt, aus demselben Grund wie EditorEngine und EngineSwitch.  
> ForceEngine=  
> Die Editor-Engines: Engines\[<identifier>\]=<class implementing expOEEditorEngine>. Eine Erweiterung fügt eine  
> Engine mit einer Klasse und einer Zeile in ihrer eigenen ezoe.ini.append.php hinzu; EditorEngine / die Benutzereinstellung  
> nennen sie dann. Ein Eintrag, dessen Klasse fehlt oder keine Engine ist, wird ignoriert (und in setup/rad angezeigt).  
> Anleitung: doc/eng-us/bc/6.0/ezoe-tinymce8.md  
> Kein leeres Engines\[\] vor der Liste: Auf Erweiterungsebene gelesen, würde es die Engines einer nur für einen  
> Siteaccess aktivierten Erweiterung (ActiveAccessExtensions\[\]) und der Siteaccess-Einstellungen entfernen.  

Standard: ein Hash mit 2: `tinymce3=expOETinyMCE3Engine`, `tinymce8=expOETinyMCE8Engine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# ein leeres "Engines[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Engines[]
Engines[tinymce3]=expOETinyMCE3Engine
Engines[tinymce8]=expOETinyMCE8Engine
*/ ?>
```

## UploadExtensionCheck

> Serverseitige Prüfung des Typs der hochgeladenen Datei gegen UploadFileExtensions\[\] (ezoe/upload):  
>  engine   = wenn die Engine des Benutzers nicht tinymce3 ist (Standard; der Dialog von TinyMCE 3 hat die Typen nie beschränkt)  
>  always   = jeder Upload des Online-Editors  
>  disabled = keiner  
> In jedem Modus wird ein Name abgelehnt, der irgendwo einen ausführbaren Typ enthält (php, phtml, phar, sh, cgi, .htaccess, ...).  

Standard: `engine`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadExtensionCheck=engine
*/ ?>
```

## UploadFromUrl

> Hochladen von einer URL im Reiter Hochladen des Einbettungsdialogs (beide Editoren): Der Server holt eine Datei von  
> einer http(s)-Adresse und legt das Objekt wie bei einem Browser-Upload an. enabled / disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadFromUrl=enabled
*/ ?>
```

## UploadFromUrlMaxSize

> Größte von einer URL geholte Datei (K, M, G). Nur dies gilt, upload\_max\_filesize von PHP nicht (ein Abruf  
> ist kein Browser-Upload); der Inhalt wird in eine temporäre Datei gestreamt, nie im Speicher gehalten  

Standard: `145M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadFromUrlMaxSize=145M
*/ ?>
```

## UploadFromUrlTimeout

> Gesamtzeit in Sekunden, die ein Abruf dauern darf (die Verbindung muss innerhalb von 5 Sekunden zustande kommen)  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
UploadFromUrlTimeout=300
*/ ?>
```

## UploadFileExtensions

> Dateiendungen, die der Reiter Hochladen im Einbettungsdialog von TinyMCE 8 akzeptiert; die Klasse des neuen  
> Objekts wählt upload.ini wie im Upload-Dialog von TinyMCE 3  

Standard: eine Liste mit 30: `jpg`, `jpeg`, `png`, `gif`, `webp`, `svg`, `bmp`, `tif`, `tiff`, `pdf`, `doc`, `docx` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# ein leeres "UploadFileExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UploadFileExtensions[]
UploadFileExtensions[]=jpg
UploadFileExtensions[]=jpeg
UploadFileExtensions[]=png
UploadFileExtensions[]=gif
UploadFileExtensions[]=webp
UploadFileExtensions[]=svg
UploadFileExtensions[]=bmp
UploadFileExtensions[]=tif
UploadFileExtensions[]=tiff
UploadFileExtensions[]=pdf
UploadFileExtensions[]=doc
UploadFileExtensions[]=docx
UploadFileExtensions[]=xls
UploadFileExtensions[]=xlsx
UploadFileExtensions[]=ppt
UploadFileExtensions[]=pptx
UploadFileExtensions[]=odt
UploadFileExtensions[]=ods
UploadFileExtensions[]=odp
UploadFileExtensions[]=rtf
# ... 10 weitere in der ausgelieferten Datei
*/ ?>
```

## Plugins

> TinyMCE-Plugins, die Sie verwenden wollen  
> Hier nicht genannte Plugins sind nicht getestet und werden daher mit dem Online Editor nicht unterstützt  
> Plugins\[\]=insertdatetime  
> Plugins\[\]=visualchars  
> Unterstützung für die Schaltfläche zum Einfügen eines geschützten Leerzeichens  
> Plugins\[\]=nonbreaking  
> Voraussetzungen der Rechtschreibprüfung:  
> http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/spellchecker  
> Plugins\[\]=spellchecker  
> Plugins\[\]=searchreplace  
> eZ-Variante des Plugins emotions  
> Plugins\[\]=ezemotions  
> Bietet zwei Schaltflächen für die Textrichtung: ltr und rtl  
> Plugins\[\]=directionality  
> Plugin autolink  
> Plugin list  
> Unterstützt Strg+Eingabe, um ein eigenes Tag zu verlassen  

Standard: eine Liste mit 7: `eztable`, `paste`, `fullscreen`, `inlinepopups`, `autolink`, `lists`, `breakoutspace`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# ein leeres "Plugins[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Plugins[]
Plugins[]=eztable
Plugins[]=paste
Plugins[]=fullscreen
Plugins[]=inlinepopups
Plugins[]=autolink
Plugins[]=lists
Plugins[]=breakoutspace
*/ ?>
```

## CustomTagControls

> Bedienelemente für eigene Tags (TinyMCE 3, TinyMCE 8 und Tiptap): Ein fokussiertes eigenes Tag erhält Schaltflächen, um es nach oben oder  
> unten zu verschieben, zu bearbeiten und zu entfernen (Alt+Umschalt+Hoch / Runter / E / Entf; Rücktaste entfernt ein leeres eigenes Block-Tag),  
> und jedes eigene Block-Tag zeigt seinen Namen als Beschriftung. enabled / disabled. Neue Installationen schreiben  
> CustomTagControls=enabled; eine aktualisierte Website behält disabled, bis es hier oder in einem Override eingeschaltet wird.  
> TinyMCE 3 lädt dafür das Plugin exp\_customtag\_controls, es braucht also keinen Eintrag in Plugins\[\].  
> (Aus LoveStack, Commit 63334f1681, mit Dank an Mugo Web.)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
CustomTagControls=disabled
*/ ?>
```

## Skin

> Skin des Editors; 'default' und 'o2k7' sind standardmäßig enthalten  

Standard: `o2k7`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
Skin=o2k7
*/ ?>
```

## CompatibilityMode

> Im Skin o2k7 enthaltene Varianten sind: 'silver' und 'black'  
> SkinVariant=  
> Diese Option legt die Standard-Schreibrichtung fest. Manche Sprachen (wie Hebräisch, Arabisch, Urdu ...)  
> schreiben von rechts nach links statt von links nach rechts. Der Standardwert dieser Option ist "ltr"; wenn  
> Sie den Modus von rechts nach links verwenden wollen, geben Sie stattdessen "rtl" an.  
> Directionality=ltr  
> Steuert die Ausrichtung der Schaltflächen der Werkzeugleiste \[left|right\]  
> ToolbarAlign=left  
> Aktiviert den Kompatibilitätsmodus, in dem sich der Editor mehr wie OE 4.x verhält  
> - verwendet ein Anhangsymbol für eingebettete Verknüpfungen, was das Platzieren eingebetteter Objekte im Text erleichtert,  
>   aber die Vorschau verliert (der Name des eingebetteten Objekts erscheint jedoch, wenn Sie über das Symbol fahren)  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
CompatibilityMode=disabled
*/ ?>
```

## ValidateEmbedObjects

> Prüft eingebettete Objekte und warnt, wenn eines im Papierkorb liegt  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
ValidateEmbedObjects=disabled
*/ ?>
```

## TagPathOpenDialog

> Wenn deaktiviert, gilt beim Klick auf einen Tag-Namen das Standardverhalten von TinyMCE  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
TagPathOpenDialog=enabled
*/ ?>
```

## BrowseImageAlias

> Bild-Alias für die Vorschau beim Durchsuchen  

Standard: `small`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
BrowseImageAlias=small
*/ ?>
```

## CustomAttributeStyleMap

> Ordnet Namen eigener Attribute Style-Werten zu, damit sie im Editor in der Vorschau erscheinen.  
> Unterstützte Styles sind auf folgende Typen beschränkt:  
>     border\* (also funktionieren border oder border-color)  
>     margin\*  
>     padding\*  
>     width  
>     height  

Standard: ein Hash mit 8: `width=width`, `height=height`, `margin=margin`, `padding=padding`, `border=border`, `border_color=border-color`, `border_size=border-width`, `border_style=border-style`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# ein leeres "CustomAttributeStyleMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CustomAttributeStyleMap[]
CustomAttributeStyleMap[width]=width
CustomAttributeStyleMap[height]=height
CustomAttributeStyleMap[margin]=margin
CustomAttributeStyleMap[padding]=padding
CustomAttributeStyleMap[border]=border
CustomAttributeStyleMap[border_color]=border-color
CustomAttributeStyleMap[border_size]=border-width
CustomAttributeStyleMap[border_style]=border-style
*/ ?>
```

## XmlTagNameAlias

> Lesbarer Name für Tags, wird in Statusleiste und Dialogen angezeigt  

Standard: ein Hash mit 6: `th=table header`, `td=table cell`, `tr=table row`, `ul=unordered list`, `ol=ordered list`, `li=list item`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezoe.ini.append.php
[EditorSettings]
# ein leeres "XmlTagNameAlias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
XmlTagNameAlias[]
XmlTagNameAlias[th]=table header
XmlTagNameAlias[td]=table cell
XmlTagNameAlias[tr]=table row
XmlTagNameAlias[ul]=unordered list
XmlTagNameAlias[ol]=ordered list
XmlTagNameAlias[li]=list item
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezoe.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezoe/settings/ezoe.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
