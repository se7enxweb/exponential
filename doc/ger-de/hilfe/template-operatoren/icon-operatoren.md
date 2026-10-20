---
title: Icon-Operatoren
audience: developer, designer, app-creator
category: development
order: 320
summary: Die acht Operatoren von eZWordToImageOperator - class_icon, classgroup_icon, mimetype_icon, action_icon, icon, flag_icon, icon_info und wordtoimage - mit ihren Positionsparametern, der Suche nach Thema und Größe in icon.ini und Beispielen, auf alpha gerendert.
keywords: Icons, Symbole, Icon-Thema, Flaggen, Smileys, class_icon, classgroup_icon, mimetype_icon, action_icon, icon, flag_icon, icon_info, wordtoimage, icon.ini, wordtoimage.ini, icon theme, crystal, bootstrap-icons, eZWordToImageOperator
type: help
---
# Icon-Operatoren

`eZWordToImageOperator` (`kernel/common/ezwordtoimageoperator.php`) registriert acht Operatoren in
`kernel/common/eztemplateautoload.php`. Sieben davon finden Icon-Dateien über `icon.ini`; `wordtoimage` ersetzt Smileys in
einem Text über `wordtoimage.ini`. Die Klasse deklariert keine benannten Parameter: Alle Parameter sind Positionsparameter
und werden in `modify()` gelesen. Keiner der Operatoren schreibt etwas.

## Inhalt

1. [Wie ein Icon gefunden wird](#wie-ein-icon-gefunden-wird)
2. [Die Operatoren](#die-operatoren): [class_icon](#class_icon), [classgroup_icon](#classgroup_icon), [mimetype_icon](#mimetype_icon), [action_icon](#action_icon), [icon](#icon), [flag_icon](#flag_icon), [icon_info](#icon_info), [wordtoimage](#wordtoimage)

## Wie ein Icon gefunden wird

`icon.ini` `[IconSettings]` nennt das Verzeichnis (`Repository=share/icons`), das Thema (`Theme=crystal`), ein
Ausweichthema (`StandardTheme`) und die Standardgröße (`Size=normal`). `eZWordToImageOperator::themeList()` baut die Liste
der Themen auf, die versucht werden:

1. das eigene `Theme` eines Typs (`[ClassIcons] Theme` usw.), wenn gesetzt, sonst das Thema des Designs aus
   `DesignTheme[<SiteDesign>]` (auf alpha verwenden `admin4` und `admin4l` `bootstrap-icons`), sonst `Theme`;
2. dann `Theme`, wenn ein Designthema es ersetzt hat, dann `AdditionalThemeList[]`, dann `StandardTheme`.

Für jedes Thema sucht der Operator im Ordner `icons/` jeder Erweiterung in `[ExtensionSettings] IconExtensions[]` und
danach im Verzeichnis. Die eigene `icon.ini` des Themenordners definiert `Sizes[]` (`normal=32x32`, `small=16x16`,
optional `<size>;<subfolder>`) und die Zuordnungen (`ClassMap[]`, `MimeMap[]`, `ClassGroupMap[]`, `ActionMap[]`,
`IconMap[]`), die die Haupt-`icon.ini` erweitern kann. Das erste Thema mit einer echten Datei (nicht der Standarddatei)
gewinnt; das Standard-Icon einer Gruppe wird nur verwendet, wenn kein Thema eine echte hat.

## Die Operatoren

Die fünf Such-Operatoren teilen ihre Parameter und ihren Code; sie unterscheiden sich in der Zuordnung, die sie lesen.

| Position | Bedeutung | Standard |
|---|---|---|
| 1 | Name der Größe (`small`, `normal`, ein Schlüssel von `Sizes[]`) | `[IconSettings] Size`; ein unbekannter Name verwendet die erste Größe |
| 2 | Alt- und Titeltext | die Eingabe |
| 3 | `true()`: nur die Adresse zurückgeben | `false` |

- **Rückgabe:** ein `<img>`-Tag mit `src`, `width`/`height` aus der Größe, `alt` und `title` (beide HTML-maskiert); mit
  Parameter 3 nur die Adresse. PNG-Icons in der Größe `normal` oder `original` erhalten `class="transparent-png-icon"`.
- Die Eingabe wird vor der Suche in Kleinbuchstaben umgewandelt. Ein Schlüssel ohne Zuordnung erhält den `Default` der
  Gruppe. Eine fehlende Datei wird nur dann als Fehler protokolliert, wenn das zuletzt versuchte Thema das Standardthema
  im Standardverzeichnis ist.

### class_icon

Generierte Seite: [class_icon](../../../eng-us/examples/template-operators/class_icon.md). Eingabe: ein Klassenbezeichner, gesucht
in `[ClassIcons] ClassMap[]`. Zeilen 1 und 2 des Beispiels unten.

### classgroup_icon

Generierte Seite: [classgroup_icon](../../../eng-us/examples/template-operators/classgroup_icon.md). Eingabe: der Name einer
Klassengruppe, gesucht in `[ClassGroupIcons] ClassGroupMap[]`. Zeile 5.

### mimetype_icon

Generierte Seite: [mimetype_icon](../../../eng-us/examples/template-operators/mimetype_icon.md). Eingabe: ein MIME-Typ, gesucht in
`[MimeIcons] MimeMap[]`; hat der vollständige Typ keinen Eintrag, wird die Gruppe vor dem `/` (`image`) versucht.
Zeilen 3 und 4.

### action_icon

Generierte Seite: [action_icon](../../../eng-us/examples/template-operators/action_icon.md). Eingabe: der Name einer Aktion, gesucht
in `[ActionIcons] ActionMap[]`. Zeile 6.

### icon

Generierte Seite: [icon](../../../eng-us/examples/template-operators/icon.md). Eingabe: ein Icon-Name, gesucht in
`[Icons] IconMap[]` (`role`, `section`, `translation`, `pdfexport`, `url` im Thema crystal). Zeile 9.
```tpl
1 {'folder'|class_icon( 'small' )}
2 {'folder'|class_icon( 'normal', 'Folder', true() )}
3 {'image/jpeg'|mimetype_icon( 'small', 'JPEG image' )}
4 {'application/pdf'|mimetype_icon( 'small', 'PDF', true() )}
5 {'content'|classgroup_icon( 'small', 'Content', true() )}
6 {'remove'|action_icon( 'small', 'Remove', true() )}
7 {'ger-DE'|flag_icon}
8 {'no-such-locale'|flag_icon}
9 {'role'|icon( 'small', 'Roles' )}
```

Ausgabe auf alpha:

```text
1 <img src="/share/icons/crystal/16x16/filesystems/folder.png" width="16" height="16" alt="folder" title="folder" />
2 /share/icons/crystal/32x32/filesystems/folder.png
3 <img src="/share/icons/crystal/16x16/mimetypes/image.png" width="16" height="16" alt="JPEG image" title="JPEG image" />
4 /share/icons/crystal/16x16/mimetypes/pdf.png
5 /share/icons/crystal/16x16/filesystems/folder_txt.png
6 /share/icons/crystal/16x16/
7 /share/icons/flags/ger-DE.gif
8 /share/icons/flags/unknown.gif
9 <img src="/share/icons/crystal/16x16/actions/identity.png" width="16" height="16" alt="Roles" title="Roles" />
```

Zeile 6 zeigt eine Lücke: Das Thema crystal hat keine Gruppe `[ActionIcons]`, daher findet `action_icon` keinen Dateinamen,
und die Adresse endet beim Größenordner. Zeile 8 zeigt den Ausweichweg für Flaggen, der als Nächstes beschrieben wird.

### flag_icon

Generierte Seite: [flag_icon](../../../eng-us/examples/template-operators/flag_icon.md).

- **Eingabe:** ein Locale-Code (`ger-DE`). **Parameter:** keine. **Rückgabe:** die Adresse des Flaggenbildes (kein Tag).

Der Operator liest `icon.ini` `[FlagIcons]` (`Repository`, `Theme`, `IconFormat`, `DefaultIcon`) und sucht
`<locale>.<IconFormat>` in den `icons/`-Ordnern der Icon-Erweiterungen und danach im Verzeichnis. Fehlt eine Flagge, wird
auf das `DefaultIcon` des Themas ausgewichen, und fehlt auch dieses, auf `<locale>.gif` im Verzeichnis. Zeilen 7 und 8
oben.

### icon_info

Generierte Seite: [icon_info](../../../eng-us/examples/template-operators/icon_info.md).

- **Eingabe:** keine. **Parameter:** der Typ, `mimetype`, `class`, `classgroup`, `action` oder `icon` (erforderlich: ohne ihn wird die Warnung "Missing parameter type" protokolliert und nichts zurückgegeben; ein unbekannter Typ protokolliert die Warnung "Unknown icon type").
- **Rückgabe:** ein Hash, der das verwendete Thema beschreibt: `repository`, `theme`, `theme_path`,
  `size_path_list`, `size_info_list` (Breite und Höhe je Größe), `icons` (die Zuordnung, beschränkt auf Icons, deren
  Datei existiert) und `default`. Das Ergebnis wird je Typ und Themenliste für den Rest der Anfrage behalten.

Verwenden Sie ihn, um Icon-Auswahlen oder Legenden zu bauen, ohne Pfade zu raten.

```tpl
{def $info = icon_info( 'class' )}
theme={$info.theme} repository={$info.repository}
sizes={foreach $info.size_path_list as $k => $p}{$k}={$p} {/foreach}

default={$info.default}
folder icon={$info.icons.folder}
mapped classes={$info.icons|count}
```

Ausgabe auf alpha:

```text
theme=crystal repository=share/icons
sizes=normal=32x32 small=16x16 
default=mimetypes/empty.png
folder icon=filesystems/folder.png
mapped classes=31
```

### wordtoimage

Generierte Seite: [wordtoimage](../../../eng-us/examples/template-operators/wordtoimage.md).

- **Eingabe:** ein Text. **Parameter:** keine. **Rückgabe:** der Text, in dem jedes `ReplaceText[]` aus `wordtoimage.ini`
  `[WordToImageSettings]` durch ein `<img>` des passenden `ReplaceIcon[]` unterhalb von `IconRoot` ersetzt ist, mit dem
  Dateinamen (ohne Endung) als `alt`.

Die Eingabe wird nicht maskiert: Wenden Sie wash auf Benutzertext **vor** `wordtoimage` an, nie danach (ein wash danach
würde die Bild-Tags maskieren).

```tpl
{'Nice day :-)'|wordtoimage}
```

Ausgabe auf alpha:

```text
Nice day <img src="/share/icons/happy.gif" alt="happy"/>
```

## Verwandte Seiten

- [Operator-Referenz](../../../eng-us/help/designers/templates/operator-reference.md)
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
