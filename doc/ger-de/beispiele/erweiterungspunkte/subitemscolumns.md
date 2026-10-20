---
title: Erweiterungspunkte in subitemscolumns.ini
audience: developer, admin
category: examples
summary: Einstellungen von subitemscolumns.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (112, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, subitemscolumns.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in subitemscolumns.ini

Einstellungen von `settings/subitemscolumns.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/subitemscolumns.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## Column_parentnodeid Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_parentnodeid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_parentnodeid]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_depth Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_depth.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_depth]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_pathstring Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_pathstring.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pathstring]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_pathidentification Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_pathidentification.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pathidentification]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_mainnodeid Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_mainnodeid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_mainnodeid]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_childsortfield Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_childsortfield.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childsortfield]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_childsortorder Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_childsortorder.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childsortorder]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_hidden Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_hidden.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_hidden]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_invisible Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_invisible.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_invisible]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_iscontainer Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_iscontainer.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_iscontainer]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_childrencount Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_childrencount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childrencount]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_subtreecount Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_subtreecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_subtreecount]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_childclasses Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_childclasses.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childclasses]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_newestchildname Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_newestchildname.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_newestchildname]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_classidentifier Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_classidentifier.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_classidentifier]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_classid Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_classid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_classid]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_classgroups Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_classgroups.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_classgroups]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_sectionidentifier Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_sectionidentifier.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_sectionid Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_sectionid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_sectionid]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_wordcount Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_wordcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_wordcount]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_textlength Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_textlength.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_textlength]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_readingtime Class

Ausgeliefert: `expSubitemsReadingTimeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_readingtime.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_readingtime]
Class=expSubitemsReadingTimeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_namelength Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_namelength.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_namelength]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_rating Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_rating.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_rating]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_ratingcount Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_ratingcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_ratingcount]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_version Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_version.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_version]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_versioncount Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_versioncount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_versioncount]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_draftcount Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_draftcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_draftcount]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_draftauthors Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_draftauthors.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_draftauthors]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_latestdraft Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_latestdraft.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_latestdraft]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_archivedcount Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_archivedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_archivedcount]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_versioncreated Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_versioncreated.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_versioncreated]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_parentname Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_parentname.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_parentname]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_ismain Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_ismain.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_ismain]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_locationcount Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_locationcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_locationcount]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_otherlocations Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_otherlocations.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_otherlocations]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_mainlocation Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_mainlocation.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_mainlocation]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_urlalias Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_urlalias.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_urlalias]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_allaliases Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_allaliases.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_allaliases]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_systemurl Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_systemurl.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_systemurl]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_publicpath Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_publicpath.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publicpath]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_publicurl Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_publicurl.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publicurl]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_urlslug Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_urlslug.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_urlslug]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_aliascount Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_aliascount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_aliascount]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_customaliases Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_customaliases.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_customaliases]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_customaliascount Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_customaliascount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_customaliascount]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_historycount Class

Ausgeliefert: `expSubitemsURLColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_historycount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_historycount]
Class=expSubitemsURLColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_pagetitle Class

Ausgeliefert: `expSubitemsSEOColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_pagetitle.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pagetitle]
Class=expSubitemsSEOColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_titlelength Class

Ausgeliefert: `expSubitemsSEOColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_titlelength.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_titlelength]
Class=expSubitemsSEOColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_metatitle Class

Ausgeliefert: `expSubitemsSEOColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_metatitle.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_metatitle]
Class=expSubitemsSEOColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_metadescription Class

Ausgeliefert: `expSubitemsSEOColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_metadescription.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_metadescription]
Class=expSubitemsSEOColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_metakeywords Class

Ausgeliefert: `expSubitemsSEOColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_metakeywords.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_metakeywords]
Class=expSubitemsSEOColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_modifiedsubnode Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_modifiedsubnode.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_publishedage Class

Ausgeliefert: `expSubitemsDateColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_publishedage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publishedage]
Class=expSubitemsDateColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_modifiedage Class

Ausgeliefert: `expSubitemsDateColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_modifiedage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_modifiedage]
Class=expSubitemsDateColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_dayssincepublished Class

Ausgeliefert: `expSubitemsDateColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_dayssincepublished.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Class=expSubitemsDateColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_dayssincemodified Class

Ausgeliefert: `expSubitemsDateColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_dayssincemodified.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Class=expSubitemsDateColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_publishediso Class

Ausgeliefert: `expSubitemsDateColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_publishediso.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publishediso]
Class=expSubitemsDateColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_modifiediso Class

Ausgeliefert: `expSubitemsDateColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_modifiediso.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_modifiediso]
Class=expSubitemsDateColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_newestchild Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_newestchild.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_newestchild]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_owner Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_owner.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_owner]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_ownerid Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_ownerid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_ownerid]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_initialcreator Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_initialcreator.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_initialcreator]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_contributors Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_contributors.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_contributors]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_relatedcount Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_relatedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_relatedcount]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_relatednames Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_relatednames.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_relatednames]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_reverserelatedcount Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_reverserelatedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_reverserelatedcount]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_reverserelatednames Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_reverserelatednames.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_tags Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_tags.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_tags]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_tagcount Class

Ausgeliefert: `expSubitemsRelationColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_tagcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_tagcount]
Class=expSubitemsRelationColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_initiallanguage Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_initiallanguage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_initiallanguage]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_languagecount Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_languagecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_languagecount]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_languagecodes Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_languagecodes.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_languagecodes]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_missingtranslations Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_missingtranslations.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_missingtranslations]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_alwaysavailable Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_alwaysavailable.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_alwaysavailable]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_locked Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_locked.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_locked]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_pendingcount Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_pendingcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pendingcount]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_rejectedcount Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_rejectedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_rejectedcount]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_workflowprocesses Class

Ausgeliefert: `expSubitemsVersionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_workflowprocesses.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_workflowprocesses]
Class=expSubitemsVersionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_canedit Class

Ausgeliefert: `expSubitemsPermissionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_canedit.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canedit]
Class=expSubitemsPermissionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_canremove Class

Ausgeliefert: `expSubitemsPermissionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_canremove.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canremove]
Class=expSubitemsPermissionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_canmove Class

Ausgeliefert: `expSubitemsPermissionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_canmove.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canmove]
Class=expSubitemsPermissionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_canhide Class

Ausgeliefert: `expSubitemsPermissionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_canhide.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canhide]
Class=expSubitemsPermissionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_cantranslate Class

Ausgeliefert: `expSubitemsPermissionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_cantranslate.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_cantranslate]
Class=expSubitemsPermissionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_cancreate Class

Ausgeliefert: `expSubitemsPermissionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_cancreate.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_cancreate]
Class=expSubitemsPermissionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userlogin Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userlogin.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlogin]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_useremail Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_useremail.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_useremail]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userenabled Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userenabled.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userenabled]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userlocked Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userlocked.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlocked]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userlastvisit Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userlastvisit.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlastvisit]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userlogincount Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userlogincount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlogincount]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userfailedlogins Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userfailedlogins.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userroles Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userroles.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userroles]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_userrolecount Class

Ausgeliefert: `expSubitemsUserColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_userrolecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userrolecount]
Class=expSubitemsUserColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_hasimage Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_hasimage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_hasimage]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_imagecount Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_imagecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagecount]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_imagedimensions Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_imagedimensions.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagedimensions]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_imagewidth Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_imagewidth.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagewidth]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_imageheight Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_imageheight.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imageheight]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_imagesize Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_imagesize.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagesize]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_imagemime Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_imagemime.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagemime]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_imagealt Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_imagealt.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagealt]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_filename Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_filename.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filename]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_filesize Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_filesize.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filesize]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_filemime Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_filemime.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filemime]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_filedownloads Class

Ausgeliefert: `expSubitemsMediaColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_filedownloads.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filedownloads]
Class=expSubitemsMediaColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_searchwords Class

Ausgeliefert: `expSubitemsPermissionColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_searchwords.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_searchwords]
Class=expSubitemsPermissionColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_viewcount Class

Ausgeliefert: `expSubitemsNodeColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_viewcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_viewcount]
Class=expSubitemsNodeColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_languagemask Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_languagemask.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_languagemask]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_attributecount Class

Ausgeliefert: `expSubitemsObjectColumn`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_attributecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_attributecount]
Class=expSubitemsObjectColumn    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_editlink Handler

Ausgeliefert: `expSubitemsColumnHandlers::editLink`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_editlink.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_editlink]
Handler=expSubitemsColumnHandlers::editLink    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Column_daysonline Handler

Ausgeliefert: `expSubitemsColumnHandlers::daysOnline`. [Die Einstellung](../ini-einstellungen/subitemscolumns/Column_daysonline.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_daysonline]
Handler=expSubitemsColumnHandlers::daysOnline    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von subitemscolumns.ini](../ini-einstellungen/subitemscolumns/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitemscolumns.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
