---
title: subitemscolumns.ini
audience: developer, admin
category: examples
order: 10
summary: The blocks and settings of subitemscolumns.ini.
keywords: subitemscolumns.ini, examples, reference
type: generated
generated: exp:help:examples
---
# subitemscolumns.ini

The shipped settings of `settings/subitemscolumns.ini`: 129 blocks, 990 settings.

> The columns of the admin subitems list ("Table options" on content/view/full/<node id>).  
> One \[Column\_<key>\] block per column; the key is what presets, defaults and saved choices use.  
> Which columns are visible by default, the presets and the CSV export are set in subitems.ini.  
> Guide: doc/eng-us/bc/6.0/subitems-table-options.md  
>
> A block names exactly one of:  
>   Builtin=true         one of the 15 columns the list renders itself from the node (the keys  
>                        thumbnail, name, visibility, type, modifier, modified, published,  
>                        translations, section, nodeid, noderemoteid, objectid, objectremoteid,  
>                        objectstate, priority); the server never computes them. Only Name, Group,  
>                        Order, Description, Copy, Align and Policy\[\] may be changed here.  
>   Class=<class>        a subclass of expSubitemsColumn, constructed with ( key, settings ).  
>                        The shipped families (expSubitemsNodeColumn, expSubitemsObjectColumn ...)  
>                        pick their column with Field=<name>.  
>   Handler=<class>::<method>   a static method ( eZContentObjectTreeNode $node, array $settings,  
>                        expSubitemsColumn $column ) that returns the value.  
>   Template=design:subitems/columns/<name>.tpl   a template that gets $node, $column (this block  
>                        as a hash) and $key; its output is the cell, its text the value.  
> Settings of every block:  
>   Name=                the column's name (translated in context design/admin/node/view/full)  
>   Group=               Basic, Node, Object, Version, Location, URLs, SEO, Dates, People,  
>                        Relations, Translations, Workflow, Users, Media, Technical, Custom  
>                        (translated like Name=)  
>   Type=                text, html, number, date, datetime, bool, link, code, list, image  
>                        (how the default cell and CSV text are made; html = trusted markup)  
>   SortField=           a subTree() sort field the list can sort the column by: path, path\_string,  
>                        published, modified, section, depth, class\_identifier, class\_name,  
>                        priority, name, modified\_subnode, node\_id, contentobject\_id, visibility,  
>                        or attribute:<class>/<attribute>. Empty: not sortable.  
>   Policy\[\]             module/function entries the user must all have to see the column at all  
>                        (it is then neither offered, computed nor sent).  
>   Description=         the help text in Table options; says what the column costs when it costs  
>                        (translated like Name=).  
>   Copy=true            a click on the cell copies its value.  
>   Align=right          right aligned (numbers).  
>   Order=               the position in Table options, and among the defaults.  
> Every value is computed only for the visible columns, only for the rows of the current page.  
> ---------------------------------------------------------------------------------------------  
> Basic: the 15 built-in columns (their defaults live in expSubitemsBuiltinColumn; repeated here  
> so they can be read and adjusted in one place)  

| Block | Settings |
|---|---|
| [Column_thumbnail](Column_thumbnail.md) | 5 |
| [Column_name](Column_name.md) | 6 |
| [Column_visibility](Column_visibility.md) | 6 |
| [Column_type](Column_type.md) | 6 |
| [Column_modifier](Column_modifier.md) | 5 |
| [Column_modified](Column_modified.md) | 6 |
| [Column_published](Column_published.md) | 6 |
| [Column_translations](Column_translations.md) | 5 |
| [Column_section](Column_section.md) | 6 |
| [Column_nodeid](Column_nodeid.md) | 8 |
| [Column_noderemoteid](Column_noderemoteid.md) | 6 |
| [Column_objectid](Column_objectid.md) | 8 |
| [Column_objectremoteid](Column_objectremoteid.md) | 6 |
| [Column_objectstate](Column_objectstate.md) | 5 |
| [Column_priority](Column_priority.md) | 7 |
| [Column_parentnodeid](Column_parentnodeid.md) | 9 |
| [Column_depth](Column_depth.md) | 9 |
| [Column_pathstring](Column_pathstring.md) | 9 |
| [Column_pathidentification](Column_pathidentification.md) | 9 |
| [Column_mainnodeid](Column_mainnodeid.md) | 9 |
| [Column_childsortfield](Column_childsortfield.md) | 7 |
| [Column_childsortorder](Column_childsortorder.md) | 7 |
| [Column_hidden](Column_hidden.md) | 7 |
| [Column_invisible](Column_invisible.md) | 8 |
| [Column_iscontainer](Column_iscontainer.md) | 7 |
| [Column_childrencount](Column_childrencount.md) | 8 |
| [Column_subtreecount](Column_subtreecount.md) | 8 |
| [Column_childclasses](Column_childclasses.md) | 7 |
| [Column_newestchildname](Column_newestchildname.md) | 7 |
| [Column_classidentifier](Column_classidentifier.md) | 9 |
| [Column_classid](Column_classid.md) | 9 |
| [Column_classgroups](Column_classgroups.md) | 7 |
| [Column_sectionidentifier](Column_sectionidentifier.md) | 9 |
| [Column_sectionid](Column_sectionid.md) | 9 |
| [Column_wordcount](Column_wordcount.md) | 8 |
| [Column_textlength](Column_textlength.md) | 8 |
| [Column_readingtime](Column_readingtime.md) | 8 |
| [Column_namelength](Column_namelength.md) | 8 |
| [Column_rating](Column_rating.md) | 8 |
| [Column_ratingcount](Column_ratingcount.md) | 8 |
| [Column_version](Column_version.md) | 8 |
| [Column_versioncount](Column_versioncount.md) | 8 |
| [Column_draftcount](Column_draftcount.md) | 8 |
| [Column_draftauthors](Column_draftauthors.md) | 7 |
| [Column_latestdraft](Column_latestdraft.md) | 7 |
| [Column_archivedcount](Column_archivedcount.md) | 8 |
| [Column_versioncreated](Column_versioncreated.md) | 7 |
| [Column_parentname](Column_parentname.md) | 7 |
| [Column_ismain](Column_ismain.md) | 7 |
| [Column_locationcount](Column_locationcount.md) | 8 |
| [Column_otherlocations](Column_otherlocations.md) | 7 |
| [Column_mainlocation](Column_mainlocation.md) | 8 |
| [Column_urlalias](Column_urlalias.md) | 9 |
| [Column_allaliases](Column_allaliases.md) | 8 |
| [Column_systemurl](Column_systemurl.md) | 8 |
| [Column_publicpath](Column_publicpath.md) | 9 |
| [Column_publicurl](Column_publicurl.md) | 10 |
| [Column_urlslug](Column_urlslug.md) | 8 |
| [Column_aliascount](Column_aliascount.md) | 8 |
| [Column_customaliases](Column_customaliases.md) | 7 |
| [Column_customaliascount](Column_customaliascount.md) | 8 |
| [Column_historycount](Column_historycount.md) | 8 |
| [Column_pagetitle](Column_pagetitle.md) | 10 |
| [Column_titlelength](Column_titlelength.md) | 10 |
| [Column_metatitle](Column_metatitle.md) | 7 |
| [Column_metadescription](Column_metadescription.md) | 7 |
| [Column_metakeywords](Column_metakeywords.md) | 7 |
| [Column_modifiedsubnode](Column_modifiedsubnode.md) | 8 |
| [Column_publishedage](Column_publishedage.md) | 8 |
| [Column_modifiedage](Column_modifiedage.md) | 8 |
| [Column_dayssincepublished](Column_dayssincepublished.md) | 9 |
| [Column_dayssincemodified](Column_dayssincemodified.md) | 9 |
| [Column_publishediso](Column_publishediso.md) | 9 |
| [Column_modifiediso](Column_modifiediso.md) | 9 |
| [Column_newestchild](Column_newestchild.md) | 7 |
| [Column_owner](Column_owner.md) | 7 |
| [Column_ownerid](Column_ownerid.md) | 9 |
| [Column_initialcreator](Column_initialcreator.md) | 7 |
| [Column_contributors](Column_contributors.md) | 7 |
| [Column_relatedcount](Column_relatedcount.md) | 8 |
| [Column_relatednames](Column_relatednames.md) | 8 |
| [Column_reverserelatedcount](Column_reverserelatedcount.md) | 8 |
| [Column_reverserelatednames](Column_reverserelatednames.md) | 8 |
| [Column_tags](Column_tags.md) | 7 |
| [Column_tagcount](Column_tagcount.md) | 8 |
| [Column_initiallanguage](Column_initiallanguage.md) | 7 |
| [Column_languagecount](Column_languagecount.md) | 8 |
| [Column_languagecodes](Column_languagecodes.md) | 8 |
| [Column_missingtranslations](Column_missingtranslations.md) | 7 |
| [Column_alwaysavailable](Column_alwaysavailable.md) | 7 |
| [Column_statusbadge](Column_statusbadge.md) | 6 |
| [Column_locked](Column_locked.md) | 7 |
| [Column_pendingcount](Column_pendingcount.md) | 8 |
| [Column_rejectedcount](Column_rejectedcount.md) | 8 |
| [Column_workflowprocesses](Column_workflowprocesses.md) | 8 |
| [Column_canedit](Column_canedit.md) | 7 |
| [Column_canremove](Column_canremove.md) | 7 |
| [Column_canmove](Column_canmove.md) | 7 |
| [Column_canhide](Column_canhide.md) | 7 |
| [Column_cantranslate](Column_cantranslate.md) | 7 |
| [Column_cancreate](Column_cancreate.md) | 7 |
| [Column_userlogin](Column_userlogin.md) | 9 |
| [Column_useremail](Column_useremail.md) | 9 |
| [Column_userenabled](Column_userenabled.md) | 8 |
| [Column_userlocked](Column_userlocked.md) | 8 |
| [Column_userlastvisit](Column_userlastvisit.md) | 8 |
| [Column_userlogincount](Column_userlogincount.md) | 9 |
| [Column_userfailedlogins](Column_userfailedlogins.md) | 9 |
| [Column_userroles](Column_userroles.md) | 8 |
| [Column_userrolecount](Column_userrolecount.md) | 9 |
| [Column_hasimage](Column_hasimage.md) | 7 |
| [Column_imagecount](Column_imagecount.md) | 8 |
| [Column_imagedimensions](Column_imagedimensions.md) | 7 |
| [Column_imagewidth](Column_imagewidth.md) | 8 |
| [Column_imageheight](Column_imageheight.md) | 8 |
| [Column_imagesize](Column_imagesize.md) | 8 |
| [Column_imagemime](Column_imagemime.md) | 7 |
| [Column_imagealt](Column_imagealt.md) | 7 |
| [Column_filename](Column_filename.md) | 8 |
| [Column_filesize](Column_filesize.md) | 8 |
| [Column_filemime](Column_filemime.md) | 7 |
| [Column_filedownloads](Column_filedownloads.md) | 8 |
| [Column_searchwords](Column_searchwords.md) | 8 |
| [Column_viewcount](Column_viewcount.md) | 8 |
| [Column_languagemask](Column_languagemask.md) | 8 |
| [Column_attributecount](Column_attributecount.md) | 8 |
| [Column_teaser](Column_teaser.md) | 8 |
| [Column_editlink](Column_editlink.md) | 7 |
| [Column_daysonline](Column_daysonline.md) | 8 |

- [All examples](../../README.md)

---

Generated by `./console exp:help:examples` from `settings/subitemscolumns.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
