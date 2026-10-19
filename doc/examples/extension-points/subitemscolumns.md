---
title: Extension points in subitemscolumns.ini
audience: developer, admin
category: examples
summary: Settings of subitemscolumns.ini that list handlers, classes or directories an extension can add to (112, found by name).
keywords: subitemscolumns.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Extension points in subitemscolumns.ini

Settings of `settings/subitemscolumns.ini` whose name says they hold handlers, classes, filters, providers or directories that an extension can add to or replace. **Found by the setting's name: a heuristic.** Read the setting's comment (linked) before relying on it.

An extension adds to a list in its own `settings/subitemscolumns.ini.append.php` (active extensions' settings are read after the kernel's and before `settings/override`):

## Column_parentnodeid Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_parentnodeid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_parentnodeid]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_depth Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_depth.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_depth]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_pathstring Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_pathstring.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pathstring]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_pathidentification Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_pathidentification.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pathidentification]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_mainnodeid Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_mainnodeid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_mainnodeid]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_childsortfield Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_childsortfield.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childsortfield]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_childsortorder Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_childsortorder.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childsortorder]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_hidden Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_hidden.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_hidden]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_invisible Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_invisible.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_invisible]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_iscontainer Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_iscontainer.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_iscontainer]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_childrencount Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_childrencount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childrencount]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_subtreecount Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_subtreecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_subtreecount]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_childclasses Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_childclasses.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_childclasses]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_newestchildname Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_newestchildname.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_newestchildname]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_classidentifier Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_classidentifier.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_classidentifier]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_classid Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_classid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_classid]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_classgroups Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_classgroups.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_classgroups]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_sectionidentifier Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_sectionidentifier.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_sectionidentifier]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_sectionid Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_sectionid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_sectionid]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_wordcount Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_wordcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_wordcount]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_textlength Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_textlength.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_textlength]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_readingtime Class

Shipped: `expSubitemsReadingTimeColumn`. [The setting](../ini-settings/subitemscolumns/Column_readingtime.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_readingtime]
Class=expSubitemsReadingTimeColumn    # replace with your class or value
*/ ?>
```

## Column_namelength Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_namelength.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_namelength]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_rating Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_rating.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_rating]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_ratingcount Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_ratingcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_ratingcount]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_version Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_version.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_version]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_versioncount Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_versioncount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_versioncount]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_draftcount Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_draftcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_draftcount]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_draftauthors Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_draftauthors.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_draftauthors]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_latestdraft Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_latestdraft.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_latestdraft]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_archivedcount Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_archivedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_archivedcount]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_versioncreated Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_versioncreated.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_versioncreated]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_parentname Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_parentname.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_parentname]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_ismain Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_ismain.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_ismain]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_locationcount Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_locationcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_locationcount]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_otherlocations Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_otherlocations.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_otherlocations]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_mainlocation Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_mainlocation.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_mainlocation]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_urlalias Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_urlalias.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_urlalias]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_allaliases Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_allaliases.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_allaliases]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_systemurl Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_systemurl.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_systemurl]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_publicpath Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_publicpath.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publicpath]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_publicurl Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_publicurl.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publicurl]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_urlslug Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_urlslug.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_urlslug]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_aliascount Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_aliascount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_aliascount]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_customaliases Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_customaliases.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_customaliases]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_customaliascount Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_customaliascount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_customaliascount]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_historycount Class

Shipped: `expSubitemsURLColumn`. [The setting](../ini-settings/subitemscolumns/Column_historycount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_historycount]
Class=expSubitemsURLColumn    # replace with your class or value
*/ ?>
```

## Column_pagetitle Class

Shipped: `expSubitemsSEOColumn`. [The setting](../ini-settings/subitemscolumns/Column_pagetitle.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pagetitle]
Class=expSubitemsSEOColumn    # replace with your class or value
*/ ?>
```

## Column_titlelength Class

Shipped: `expSubitemsSEOColumn`. [The setting](../ini-settings/subitemscolumns/Column_titlelength.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_titlelength]
Class=expSubitemsSEOColumn    # replace with your class or value
*/ ?>
```

## Column_metatitle Class

Shipped: `expSubitemsSEOColumn`. [The setting](../ini-settings/subitemscolumns/Column_metatitle.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_metatitle]
Class=expSubitemsSEOColumn    # replace with your class or value
*/ ?>
```

## Column_metadescription Class

Shipped: `expSubitemsSEOColumn`. [The setting](../ini-settings/subitemscolumns/Column_metadescription.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_metadescription]
Class=expSubitemsSEOColumn    # replace with your class or value
*/ ?>
```

## Column_metakeywords Class

Shipped: `expSubitemsSEOColumn`. [The setting](../ini-settings/subitemscolumns/Column_metakeywords.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_metakeywords]
Class=expSubitemsSEOColumn    # replace with your class or value
*/ ?>
```

## Column_modifiedsubnode Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_modifiedsubnode.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_modifiedsubnode]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_publishedage Class

Shipped: `expSubitemsDateColumn`. [The setting](../ini-settings/subitemscolumns/Column_publishedage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publishedage]
Class=expSubitemsDateColumn    # replace with your class or value
*/ ?>
```

## Column_modifiedage Class

Shipped: `expSubitemsDateColumn`. [The setting](../ini-settings/subitemscolumns/Column_modifiedage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_modifiedage]
Class=expSubitemsDateColumn    # replace with your class or value
*/ ?>
```

## Column_dayssincepublished Class

Shipped: `expSubitemsDateColumn`. [The setting](../ini-settings/subitemscolumns/Column_dayssincepublished.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_dayssincepublished]
Class=expSubitemsDateColumn    # replace with your class or value
*/ ?>
```

## Column_dayssincemodified Class

Shipped: `expSubitemsDateColumn`. [The setting](../ini-settings/subitemscolumns/Column_dayssincemodified.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_dayssincemodified]
Class=expSubitemsDateColumn    # replace with your class or value
*/ ?>
```

## Column_publishediso Class

Shipped: `expSubitemsDateColumn`. [The setting](../ini-settings/subitemscolumns/Column_publishediso.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_publishediso]
Class=expSubitemsDateColumn    # replace with your class or value
*/ ?>
```

## Column_modifiediso Class

Shipped: `expSubitemsDateColumn`. [The setting](../ini-settings/subitemscolumns/Column_modifiediso.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_modifiediso]
Class=expSubitemsDateColumn    # replace with your class or value
*/ ?>
```

## Column_newestchild Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_newestchild.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_newestchild]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_owner Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_owner.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_owner]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_ownerid Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_ownerid.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_ownerid]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_initialcreator Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_initialcreator.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_initialcreator]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_contributors Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_contributors.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_contributors]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_relatedcount Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_relatedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_relatedcount]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_relatednames Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_relatednames.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_relatednames]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_reverserelatedcount Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_reverserelatedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_reverserelatedcount]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_reverserelatednames Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_reverserelatednames.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_reverserelatednames]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_tags Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_tags.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_tags]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_tagcount Class

Shipped: `expSubitemsRelationColumn`. [The setting](../ini-settings/subitemscolumns/Column_tagcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_tagcount]
Class=expSubitemsRelationColumn    # replace with your class or value
*/ ?>
```

## Column_initiallanguage Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_initiallanguage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_initiallanguage]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_languagecount Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_languagecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_languagecount]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_languagecodes Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_languagecodes.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_languagecodes]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_missingtranslations Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_missingtranslations.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_missingtranslations]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_alwaysavailable Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_alwaysavailable.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_alwaysavailable]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_locked Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_locked.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_locked]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_pendingcount Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_pendingcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_pendingcount]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_rejectedcount Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_rejectedcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_rejectedcount]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_workflowprocesses Class

Shipped: `expSubitemsVersionColumn`. [The setting](../ini-settings/subitemscolumns/Column_workflowprocesses.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_workflowprocesses]
Class=expSubitemsVersionColumn    # replace with your class or value
*/ ?>
```

## Column_canedit Class

Shipped: `expSubitemsPermissionColumn`. [The setting](../ini-settings/subitemscolumns/Column_canedit.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canedit]
Class=expSubitemsPermissionColumn    # replace with your class or value
*/ ?>
```

## Column_canremove Class

Shipped: `expSubitemsPermissionColumn`. [The setting](../ini-settings/subitemscolumns/Column_canremove.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canremove]
Class=expSubitemsPermissionColumn    # replace with your class or value
*/ ?>
```

## Column_canmove Class

Shipped: `expSubitemsPermissionColumn`. [The setting](../ini-settings/subitemscolumns/Column_canmove.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canmove]
Class=expSubitemsPermissionColumn    # replace with your class or value
*/ ?>
```

## Column_canhide Class

Shipped: `expSubitemsPermissionColumn`. [The setting](../ini-settings/subitemscolumns/Column_canhide.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_canhide]
Class=expSubitemsPermissionColumn    # replace with your class or value
*/ ?>
```

## Column_cantranslate Class

Shipped: `expSubitemsPermissionColumn`. [The setting](../ini-settings/subitemscolumns/Column_cantranslate.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_cantranslate]
Class=expSubitemsPermissionColumn    # replace with your class or value
*/ ?>
```

## Column_cancreate Class

Shipped: `expSubitemsPermissionColumn`. [The setting](../ini-settings/subitemscolumns/Column_cancreate.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_cancreate]
Class=expSubitemsPermissionColumn    # replace with your class or value
*/ ?>
```

## Column_userlogin Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userlogin.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlogin]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_useremail Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_useremail.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_useremail]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_userenabled Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userenabled.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userenabled]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_userlocked Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userlocked.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlocked]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_userlastvisit Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userlastvisit.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlastvisit]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_userlogincount Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userlogincount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userlogincount]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_userfailedlogins Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userfailedlogins.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userfailedlogins]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_userroles Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userroles.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userroles]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_userrolecount Class

Shipped: `expSubitemsUserColumn`. [The setting](../ini-settings/subitemscolumns/Column_userrolecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_userrolecount]
Class=expSubitemsUserColumn    # replace with your class or value
*/ ?>
```

## Column_hasimage Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_hasimage.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_hasimage]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_imagecount Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_imagecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagecount]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_imagedimensions Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_imagedimensions.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagedimensions]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_imagewidth Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_imagewidth.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagewidth]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_imageheight Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_imageheight.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imageheight]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_imagesize Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_imagesize.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagesize]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_imagemime Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_imagemime.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagemime]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_imagealt Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_imagealt.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_imagealt]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_filename Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_filename.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filename]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_filesize Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_filesize.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filesize]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_filemime Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_filemime.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filemime]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_filedownloads Class

Shipped: `expSubitemsMediaColumn`. [The setting](../ini-settings/subitemscolumns/Column_filedownloads.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_filedownloads]
Class=expSubitemsMediaColumn    # replace with your class or value
*/ ?>
```

## Column_searchwords Class

Shipped: `expSubitemsPermissionColumn`. [The setting](../ini-settings/subitemscolumns/Column_searchwords.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_searchwords]
Class=expSubitemsPermissionColumn    # replace with your class or value
*/ ?>
```

## Column_viewcount Class

Shipped: `expSubitemsNodeColumn`. [The setting](../ini-settings/subitemscolumns/Column_viewcount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_viewcount]
Class=expSubitemsNodeColumn    # replace with your class or value
*/ ?>
```

## Column_languagemask Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_languagemask.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_languagemask]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_attributecount Class

Shipped: `expSubitemsObjectColumn`. [The setting](../ini-settings/subitemscolumns/Column_attributecount.md#class).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_attributecount]
Class=expSubitemsObjectColumn    # replace with your class or value
*/ ?>
```

## Column_editlink Handler

Shipped: `expSubitemsColumnHandlers::editLink`. [The setting](../ini-settings/subitemscolumns/Column_editlink.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_editlink]
Handler=expSubitemsColumnHandlers::editLink    # replace with your class or value
*/ ?>
```

## Column_daysonline Handler

Shipped: `expSubitemsColumnHandlers::daysOnline`. [The setting](../ini-settings/subitemscolumns/Column_daysonline.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/subitemscolumns.ini.append.php
[Column_daysonline]
Handler=expSubitemsColumnHandlers::daysOnline    # replace with your class or value
*/ ?>
```

## Related

- [All extension points](README.md)
- [An extension skeleton](../cookbook/extension-skeleton.md)
- [The blocks of subitemscolumns.ini](../ini-settings/subitemscolumns/README.md)

---

Generated by `./console exp:help:examples` from `settings/subitemscolumns.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
