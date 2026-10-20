---
title: Icon operators
audience: developer, designer, app-creator
category: development
order: 320
summary: The eight operators of eZWordToImageOperator - class_icon, classgroup_icon, mimetype_icon, action_icon, icon, flag_icon, icon_info and wordtoimage - with their positional parameters, the icon.ini theme and size lookup, and examples rendered on alpha.
keywords: class_icon, classgroup_icon, mimetype_icon, action_icon, icon, flag_icon, icon_info, wordtoimage, icon.ini, wordtoimage.ini, icon theme, crystal, bootstrap-icons, eZWordToImageOperator
type: help
---
# Icon operators

`eZWordToImageOperator` (`kernel/common/ezwordtoimageoperator.php`) registers eight operators in
`kernel/common/eztemplateautoload.php`. Seven of them find icon files through `icon.ini`; `wordtoimage` replaces
smileys in a text through `wordtoimage.ini`. The class declares no named parameters: all parameters are positional and
read in `modify()`. None of them writes anything.

## Contents

1. [How an icon is found](#how-an-icon-is-found)
2. [The operators](#the-operators): [class_icon](#class_icon), [classgroup_icon](#classgroup_icon), [mimetype_icon](#mimetype_icon), [action_icon](#action_icon), [icon](#icon), [flag_icon](#flag_icon), [icon_info](#icon_info), [wordtoimage](#wordtoimage)

## How an icon is found

`icon.ini` `[IconSettings]` names the repository (`Repository=share/icons`), the theme (`Theme=crystal`), a fallback
theme (`StandardTheme`) and the default size (`Size=normal`). `eZWordToImageOperator::themeList()` builds the list of
themes to try:

1. a type's own `Theme` (`[ClassIcons] Theme` and so on) when set, otherwise the design's theme from
   `DesignTheme[<SiteDesign>]` (on alpha `admin4` and `admin4l` use `bootstrap-icons`), otherwise `Theme`;
2. then `Theme` when a design theme replaced it, then `AdditionalThemeList[]`, then `StandardTheme`.

For each theme it looks in the `icons/` folder of every extension in `[ExtensionSettings] IconExtensions[]` and then
in the repository. The theme folder's own `icon.ini` defines `Sizes[]` (`normal=32x32`, `small=16x16`, optionally
`<size>;<subfolder>`) and the maps (`ClassMap[]`, `MimeMap[]`, `ClassGroupMap[]`, `ActionMap[]`, `IconMap[]`), which
the main `icon.ini` can extend. The first theme that has a real (non-default) file wins; the default icon of a group is
used only when no theme has a real one.

## The operators

The five lookup operators share their parameters and their code; they differ in the map they read.

| Position | Meaning | Default |
|---|---|---|
| 1 | size name (`small`, `normal`, a key of `Sizes[]`) | `[IconSettings] Size`; an unknown name uses the first size |
| 2 | alt and title text | the input |
| 3 | `true()`: return only the address | `false` |

- **Returns:** an `<img>` tag with `src`, `width`/`height` from the size, `alt` and `title` (both HTML-escaped); with
  parameter 3 only the address. PNG icons in the `normal` or `original` size get `class="transparent-png-icon"`.
- The input is lowercased before the lookup. A key without a mapping gets the group's `Default`. A missing file is
  logged as an error only when the last theme tried is the standard theme in the default repository.

### class_icon

Generated page: [class_icon](../../examples/template-operators/class_icon.md). Input: a class identifier, looked up
in `[ClassIcons] ClassMap[]`. Lines 1 and 2 of the example below.

### classgroup_icon

Generated page: [classgroup_icon](../../examples/template-operators/classgroup_icon.md). Input: a class group name,
looked up in `[ClassGroupIcons] ClassGroupMap[]`. Line 5.

### mimetype_icon

Generated page: [mimetype_icon](../../examples/template-operators/mimetype_icon.md). Input: a MIME type, looked up
in `[MimeIcons] MimeMap[]`; when the full type has no entry, the group before the `/` (`image`) is tried. Lines 3
and 4.

### action_icon

Generated page: [action_icon](../../examples/template-operators/action_icon.md). Input: an action name, looked up in
`[ActionIcons] ActionMap[]`. Line 6.

### icon

Generated page: [icon](../../examples/template-operators/icon.md). Input: an icon name, looked up in
`[Icons] IconMap[]` (`role`, `section`, `translation`, `pdfexport`, `url` in the crystal theme). Line 9.
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

Output on alpha:

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

Line 6 shows a gap: the crystal theme has no `[ActionIcons]` group, so `action_icon` finds no file name and the address
ends at the size folder. Line 8 shows the flag fallback described next.

### flag_icon

Generated page: [flag_icon](../../examples/template-operators/flag_icon.md).

- **Input:** a locale code (`ger-DE`). **Parameters:** none. **Returns:** the address of the flag image (not a tag).

It reads `icon.ini` `[FlagIcons]` (`Repository`, `Theme`, `IconFormat`, `DefaultIcon`) and looks for
`<locale>.<IconFormat>` in the `icons/` folders of the icon extensions and then the repository. A missing flag falls
back to the theme's `DefaultIcon`, and when that is missing too, to `<locale>.gif` in the repository. Lines 7 and 8
above.

### icon_info

Generated page: [icon_info](../../examples/template-operators/icon_info.md).

- **Input:** none. **Parameter:** the type, `mimetype`, `class`, `classgroup`, `action` or `icon` (required: without it the warning "Missing parameter type" is logged and nothing is returned; an unknown type logs the warning "Unknown icon type").
- **Returns:** a hash describing the theme that would be used: `repository`, `theme`, `theme_path`,
  `size_path_list`, `size_info_list` (width and height per size), `icons` (the map, limited to icons whose file
  exists) and `default`. The result is kept per type and theme list for the rest of the request.

Use it to build icon pickers or legends without guessing paths.

```tpl
{def $info = icon_info( 'class' )}
theme={$info.theme} repository={$info.repository}
sizes={foreach $info.size_path_list as $k => $p}{$k}={$p} {/foreach}

default={$info.default}
folder icon={$info.icons.folder}
mapped classes={$info.icons|count}
```

Output on alpha:

```text
theme=crystal repository=share/icons
sizes=normal=32x32 small=16x16 
default=mimetypes/empty.png
folder icon=filesystems/folder.png
mapped classes=31
```

### wordtoimage

Generated page: [wordtoimage](../../examples/template-operators/wordtoimage.md).

- **Input:** a text. **Parameters:** none. **Returns:** the text with every `ReplaceText[]` of `wordtoimage.ini`
  `[WordToImageSettings]` replaced by an `<img>` of the matching `ReplaceIcon[]` below `IconRoot`, with the file
  name (without extension) as `alt`.

The input is not escaped: wash user text **before** `wordtoimage`, never after (washing afterwards would escape the
image tags).

```tpl
{'Nice day :-)'|wordtoimage}
```

Output on alpha:

```text
Nice day <img src="/share/icons/happy.gif" alt="happy"/>
```

## Related pages

- [Operator reference](../designers/templates/operator-reference.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
