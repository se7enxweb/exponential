# The extension surface

Read off this installation, not written by hand. `doc/bc/6.0/rad-extension-points.md`
beside this is the curated list: the points somebody thought worth explaining, each
with a tool where there is one. This is everything that is actually here.

Regenerate it with:

```
php ai/bin/one/write_rad_survey_doc.php
```

or read it live in the admin at **Setup → RAD tools → Extension point survey**,
where it can be searched.

## What was found

| | |
| --- | --- |
| Extension points | **1743** |
| ini files read | 381 |
| Settings naming a class | 350 (333 resolve to a class, 16 take an alias, 1 look like a class and are not one) |
| Places the kernel looks | 236 |
| Interfaces and abstract classes | 41 (29 implemented) |
| Modules | 60 |
| Module views | 406 |
| Policy functions | 179 |
| Template operators and functions | 414 (364 operators, 50 functions) |
| Events something can listen to | 34 |
| Templates already overridden | 259 |
| Kernel classes replaced | 3 |

A count is not a promise that all of them are worth extending. It is a promise that
none of them was left out because nobody remembered it.

## Settings that name a class

Change one of these and something else answers instead.

Several of these settings take an alias rather than a class, and the two cannot be
told apart by looking at the setting - only by looking at the value. Every class in
this system has a capital in it somewhere and every alias is one lower case word,
so a lower case value that names no class is an *alias* doing its job. A row marked
**not declared** is shaped like a class and is not one, which is worth a look.

### admininterface.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `WindowControlsSettings` | `AdditionalTabs[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |

### binaryfile.ini (3)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `HandlerSettings` | `MetaDataExtractor[application/msword]` | `ezword` | *alias* |
| `HandlerSettings` | `MetaDataExtractor[application/pdf]` | `ezpdf` | `lib/ezpdf/classes/ezpdf.php` |
| `HandlerSettings` | `MetaDataExtractor[text/plain]` | `ezplaintext` | *alias* |

### block.ini (5)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `Dynamic3Items` | `FetchClass` | `eZFlowLatestObjects` | `extension/ezflow/classes/fetches/ezflowlatestobjects.php` |
| `GMapItems` | `CustomAttributes[]` | `attribute` | `(declared at runtime)` |
| `Keyword` | `FetchClass` | `eZFlowKeywordsFetch` | `extension/ezflow/classes/fetches/ezflowkeywordsfetch.php` |
| `LatestContent` | `FetchClass` | `eZFlowLatestContent` | `extension/ezflow/classes/fetches/ezflowlatestcontent.php` |
| `MultimediaCarousel` | `FetchClass` | `eZFlowMCFetch` | `extension/ezflow/classes/fetches/ezflowmcfetch.php` |

### changeclass.ini (6)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `General` | `UnsupportedDataTypeArray[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `ezobjectrelationlist` | `Class` | `expChangeClassConverters` | `extension/expchangeclass/classes/converters.php` |
| `ezstring` | `Class` | `expChangeClassConverters` | `extension/expchangeclass/classes/converters.php` |
| `ezstring` | `SupportedDestination[]` | `ezxmltext` | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |
| `eztext` | `Class` | `expChangeClassConverters` | `extension/expchangeclass/classes/converters.php` |
| `eztext` | `SupportedDestination[]` | `ezxmltext` | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |

### cie.ini (4)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `CieSettings` | `ExportOutputFormatHandlers[cp1252]` | `bccieExportFormatOutputHandlerCP1252` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerCP1252.php` |
| `CieSettings` | `ExportOutputFormatHandlers[utf16le]` | `bccieExportFormatOutputHandlerUtf16Le` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf16Le.php` |
| `CieSettings` | `ExportOutputFormatHandlers[utf8]` | `bccieExportFormatOutputHandlerUtf8` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8.php` |
| `CieSettings` | `ExportOutputFormatHandlers[utf8bom]` | `bccieExportFormatOutputHandlerUtf8Bom` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8Bom.php` |

### cjw_newsletter.ini (2)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `NewsletterFilterSettings` | `AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeSalutation` | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypesalutation.php` |
| `NewsletterFilterSettings` | `AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeEmail` | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypeemail.php` |

### content.ini (35)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezxmltext` | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezdate` | `lib/ezlocale/classes/ezdate.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezdatetime` | `lib/ezlocale/classes/ezdatetime.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `eztime` | `lib/ezlocale/classes/eztime.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezenum` | `kernel/classes/datatypes/ezenum/ezenum.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezbinaryfile` | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezmedia` | `kernel/classes/datatypes/ezmedia/ezmedia.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezauthor` | `kernel/classes/datatypes/ezauthor/ezauthor.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezurl` | `kernel/classes/datatypes/ezurl/ezurl.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezoption` | `kernel/classes/datatypes/ezoption/ezoption.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezmultioption` | `kernel/classes/datatypes/ezmultioption/ezmultioption.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezmultioption2` | `kernel/classes/datatypes/ezmultioption2/ezmultioption2.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezrangeoption` | `kernel/classes/datatypes/ezrangeoption/ezrangeoption.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezprice` | `kernel/classes/datatypes/ezprice/ezprice.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezmultiprice` | `kernel/classes/datatypes/ezmultiprice/ezmultiprice.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezkeyword` | `kernel/classes/datatypes/ezkeyword/ezkeyword.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezmatrix` | `kernel/classes/datatypes/ezmatrix/ezmatrix.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezpackage` | `kernel/classes/ezpackage.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezproductcategory` | `kernel/classes/ezproductcategory.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `xrowmetadata` | `extension/xrowmetadata/classes/structs/xrowmetadata.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezbirthday` | `extension/birthday/datatypes/ezbirthday/ezbirthday.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `cjwnewsletterlist` | `extension/cjw_newsletter/classes/cjwnewsletterlist.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `cjwnewsletteredition` | `extension/cjw_newsletter/classes/cjwnewsletteredition.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `cjwnewslettersubscription` | `extension/cjw_newsletter/classes/cjwnewslettersubscription.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `cjwnewsletterlistvirtual` | `extension/cjw_newsletter/classes/cjwnewsletterlistvirtual.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `sckenhancedselection` | `extension/enhancedselection2/classes/sckenhancedselection.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezpage` | `extension/ezflow/classes/ezpage.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezgmaplocation` | `extension/ezgmaplocation/classes/ezgmaplocation.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `ezpaex` | `extension/ezmbpaex/classes/ezpaex.php` |
| `DataTypeSettings` | `AvailableDataTypes[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |
| `DataTypeSettings` | `UserDataTypes[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `PublishingSettings` | `AsynchronousPublishingQueueReader` | `ezpContentPublishingQueue` | `kernel/private/classes/ezpcontentpublishingqueue.php` |
| `TestingSettings` | `MultivariateTestingHandlerClass` | `ezpMultivariateTestHandler` | `kernel/private/classes/ezpmultivariatetesthandler.php` |
| `children_menu` | `CustomAttributes[]` | `like` | `vendor/zetacomponents/persistent-object/tests/data/keywordtest/where_class.php` |

### csv.ini (34)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `General` | `ExportableDatatypes[]` | `ezbirthday` | `extension/birthday/datatypes/ezbirthday/ezbirthday.php` |
| `General` | `ExportableDatatypes[]` | `ezurl` | `kernel/classes/datatypes/ezurl/ezurl.php` |
| `General` | `ExportableDatatypes[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `General` | `ExportableDatatypes[]` | `ezxmltext` | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |
| `General` | `ExportableDatatypes[]` | `ezdate` | `lib/ezlocale/classes/ezdate.php` |
| `General` | `ExportableDatatypes[]` | `ezenum` | `kernel/classes/datatypes/ezenum/ezenum.php` |
| `General` | `ExportableDatatypes[]` | `ezmedia` | `kernel/classes/datatypes/ezmedia/ezmedia.php` |
| `General` | `ExportableDatatypes[]` | `ezbinaryfile` | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php` |
| `General` | `ExportableDatatypes[]` | `ezmatrix` | `kernel/classes/datatypes/ezmatrix/ezmatrix.php` |
| `General` | `ExportableDatatypes[]` | `ezprice` | `kernel/classes/datatypes/ezprice/ezprice.php` |
| `ezbinaryfile` | `HandlerClass` | `XroweZBinaryfileExportHandler` | `extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php` |
| `ezbirthday` | `HandlerClass` | `eZBirthdayCsvHandler` | `extension/birthday/classes/parsers/ezbirthdaycsvhandler.php` |
| `ezboolean` | `HandlerClass` | `XroweZBooleanHandler` | `extension/xrowextract/classes/parsers/xrowezbooleanhandler.php` |
| `ezcountry` | `HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/classes/parsers/xrowezselectionhandler.php` |
| `ezdate` | `HandlerClass` | `XroweZDateHandler` | `extension/xrowextract/classes/parsers/xrowezdatehandler.php` |
| `ezemail` | `HandlerClass` | `XroweZEmailHandler` | `extension/xrowextract/classes/parsers/xrowezemailhandler.php` |
| `ezenhancedobjectrelation` | `HandlerClass` | `XroweZenhancedobjectrelationHandler` | `extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php` |
| `ezenhancedselection` | `HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/classes/parsers/xrowezstringhandler.php` |
| `ezenum` | `HandlerClass` | `XroweZEnumHandler` | `extension/xrowextract/classes/parsers/xrowezenumhandler.php` |
| `ezfloat` | `HandlerClass` | `XroweZFloatHandler` | `extension/xrowextract/classes/parsers/xrowezfloathandler.php` |
| `ezidentifier` | `HandlerClass` | `XroweZIdentifierHandler` | `extension/xrowextract/classes/parsers/xrowezidentifierhandler.php` |
| `ezimage` | `HandlerClass` | `XroweZImageExportHandler` | `extension/xrowextract/classes/parsers/xrowezimagehandler.php` |
| `ezinteger` | `HandlerClass` | `XroweZIntegerHandler` | `extension/xrowextract/classes/parsers/xrowezintegerhandler.php` |
| `ezmatrix` | `HandlerClass` | `XroweZMatrixExportHandler` | `extension/xrowextract/classes/parsers/xrowezmatrixhandler.php` |
| `ezmedia` | `HandlerClass` | `XroweZMediaExportHandler` | `extension/xrowextract/classes/parsers/xrowezmediahandler.php` |
| `ezobjectrelationlist` | `HandlerClass` | `XroweZObjectRelationListHandler` | `extension/xrowextract/classes/parsers/xrowezobjectrelationlisthandler.php` |
| `ezprice` | `HandlerClass` | `XroweZPriceHandler` | `extension/xrowextract/classes/parsers/xrowezpricehandler.php` |
| `ezselection` | `HandlerClass` | `XroweZSelectionHandler` | `extension/xrowextract/classes/parsers/xrowezselectionhandler.php` |
| `ezstring` | `HandlerClass` | `XroweZStringHandler` | `extension/xrowextract/classes/parsers/xrowezstringhandler.php` |
| `eztext` | `HandlerClass` | `XroweZTextHandler` | `extension/xrowextract/classes/parsers/xroweztexthandler.php` |
| `ezurl` | `HandlerClass` | `XroweZURLHandler` | `extension/xrowextract/classes/parsers/xrowezurlhandler.php` |
| `ezuser` | `HandlerClass` | `XroweZUserHandler` | `extension/xrowextract/classes/parsers/xrowezuserhandler.php` |
| `ezxmltext` | `HandlerClass` | `XroweZXMLTextHandler` | `extension/xrowextract/classes/parsers/xrowezxmltexthandler.php` |
| `hmregexpline` | `HandlerClass` | `XrowhmregexplineHandler` | `extension/xrowextract/classes/parsers/xrowhmregexplinehandler.php` |

### datatype.ini (38)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `CollectionSettings` | `GroupedInput[]` | `ezauthor` | `kernel/classes/datatypes/ezauthor/ezauthor.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezbinaryfile` | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezdate` | `lib/ezlocale/classes/ezdate.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezdatetime` | `lib/ezlocale/classes/ezdatetime.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezmatrix` | `kernel/classes/datatypes/ezmatrix/ezmatrix.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezmedia` | `kernel/classes/datatypes/ezmedia/ezmedia.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezmultioption` | `kernel/classes/datatypes/ezmultioption/ezmultioption.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezpackage` | `kernel/classes/ezpackage.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezrangeoption` | `kernel/classes/datatypes/ezrangeoption/ezrangeoption.php` |
| `CollectionSettings` | `GroupedInput[]` | `eztime` | `lib/ezlocale/classes/eztime.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezurl` | `kernel/classes/datatypes/ezurl/ezurl.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `CollectionSettings` | `GroupedInput[]` | `ezpaex` | `extension/ezmbpaex/classes/ezpaex.php` |
| `CollectionSettings` | `GroupedInput[]` | `xrowmetadata` | `extension/xrowmetadata/classes/structs/xrowmetadata.php` |
| `EditSettings` | `GroupedInput[]` | `ezauthor` | `kernel/classes/datatypes/ezauthor/ezauthor.php` |
| `EditSettings` | `GroupedInput[]` | `ezbinaryfile` | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php` |
| `EditSettings` | `GroupedInput[]` | `ezdate` | `lib/ezlocale/classes/ezdate.php` |
| `EditSettings` | `GroupedInput[]` | `ezdatetime` | `lib/ezlocale/classes/ezdatetime.php` |
| `EditSettings` | `GroupedInput[]` | `ezmatrix` | `kernel/classes/datatypes/ezmatrix/ezmatrix.php` |
| `EditSettings` | `GroupedInput[]` | `ezmedia` | `kernel/classes/datatypes/ezmedia/ezmedia.php` |
| `EditSettings` | `GroupedInput[]` | `ezoption` | `kernel/classes/datatypes/ezoption/ezoption.php` |
| `EditSettings` | `GroupedInput[]` | `ezmultioption` | `kernel/classes/datatypes/ezmultioption/ezmultioption.php` |
| `EditSettings` | `GroupedInput[]` | `ezpackage` | `kernel/classes/ezpackage.php` |
| `EditSettings` | `GroupedInput[]` | `ezrangeoption` | `kernel/classes/datatypes/ezrangeoption/ezrangeoption.php` |
| `EditSettings` | `GroupedInput[]` | `eztime` | `lib/ezlocale/classes/eztime.php` |
| `EditSettings` | `GroupedInput[]` | `ezurl` | `kernel/classes/datatypes/ezurl/ezurl.php` |
| `EditSettings` | `GroupedInput[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `EditSettings` | `GroupedInput[]` | `ezprice` | `kernel/classes/datatypes/ezprice/ezprice.php` |
| `EditSettings` | `GroupedInput[]` | `ezgmaplocation` | `extension/ezgmaplocation/classes/ezgmaplocation.php` |
| `EditSettings` | `GroupedInput[]` | `ezpaex` | `extension/ezmbpaex/classes/ezpaex.php` |
| `EditSettings` | `GroupedInput[]` | `xrowmetadata` | `extension/xrowmetadata/classes/structs/xrowmetadata.php` |
| `ViewSettings` | `GroupedInput[]` | `ezmultioption` | `kernel/classes/datatypes/ezmultioption/ezmultioption.php` |
| `ViewSettings` | `GroupedInput[]` | `ezoption` | `kernel/classes/datatypes/ezoption/ezoption.php` |
| `ViewSettings` | `GroupedInput[]` | `ezrangeoption` | `kernel/classes/datatypes/ezrangeoption/ezrangeoption.php` |
| `ViewSettings` | `GroupedInput[]` | `ezpackage` | `kernel/classes/ezpackage.php` |
| `ViewSettings` | `GroupedInput[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `ViewSettings` | `GroupedInput[]` | `ezpaex` | `extension/ezmbpaex/classes/ezpaex.php` |
| `ViewSettings` | `GroupedInput[]` | `xrowmetadata` | `extension/xrowmetadata/classes/structs/xrowmetadata.php` |

### dbschema.ini (6)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `SchemaSettings` | `SchemaHandlerClasses[mongo]` | `expMongoSchema` | `lib/ezdbschema/classes/expmongoschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[mysql]` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[mysqli]` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[postgresql]` | `eZPgsqlSchema` | `lib/ezdbschema/classes/ezpgsqlschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[sqlite]` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |

### error.ini (2)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `ErrorSettings` | `DefaultErrorHandler` | `displayerror` | *alias* |
| `ErrorSettings-kernel` | `ErrorHandler[1]` | `embed` | *alias* |

### explayouts.ini (58)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `BlockDefinition_about` | `Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/classes/explayoutscomponentblockhandler.php` |
| `BlockDefinition_accordion` | `Handler` | `expLayoutsAccordionBlockHandler` | `extension/explayouts/classes/explayoutsaccordionblockhandler.php` |
| `BlockDefinition_alert` | `Handler` | `expLayoutsAlertBlockHandler` | `extension/explayouts/classes/explayoutsalertblockhandler.php` |
| `BlockDefinition_badge` | `Handler` | `expLayoutsBadgeBlockHandler` | `extension/explayouts/classes/explayoutsbadgeblockhandler.php` |
| `BlockDefinition_button` | `Handler` | `expLayoutsButtonBlockHandler` | `extension/explayouts/classes/explayoutsbuttonblockhandler.php` |
| `BlockDefinition_card` | `Handler` | `expLayoutsCardBlockHandler` | `extension/explayouts/classes/explayoutscardblockhandler.php` |
| `BlockDefinition_carousel` | `Handler` | `expLayoutsCarouselBlockHandler` | `extension/explayouts/classes/explayoutscarouselblockhandler.php` |
| `BlockDefinition_column` | `Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/classes/explayoutscontainerblockhandler.php` |
| `BlockDefinition_divider` | `Handler` | `expLayoutsDividerBlockHandler` | `extension/explayouts/classes/explayoutsdividerblockhandler.php` |
| `BlockDefinition_features` | `Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/classes/explayoutscomponentblockhandler.php` |
| `BlockDefinition_four_columns` | `Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/classes/explayoutscontainerblockhandler.php` |
| `BlockDefinition_full_view` | `Handler` | `expLayoutsFullViewBlockHandler` | `extension/explayouts/classes/explayoutsfullviewblockhandler.php` |
| `BlockDefinition_gallery` | `Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/classes/explayoutsgalleryblockhandler.php` |
| `BlockDefinition_grid` | `Handler` | `expLayoutsGridBlockHandler` | `extension/explayouts/classes/explayoutsgridblockhandler.php` |
| `BlockDefinition_grid_gallery` | `Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/classes/explayoutsgalleryblockhandler.php` |
| `BlockDefinition_hero` | `Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/classes/explayoutscomponentblockhandler.php` |
| `BlockDefinition_html` | `Handler` | `expLayoutsHtmlBlockHandler` | `extension/explayouts/classes/explayoutshtmlblockhandler.php` |
| `BlockDefinition_ibexa_component_about` | `Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php` |
| `BlockDefinition_ibexa_component_features` | `Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php` |
| `BlockDefinition_ibexa_component_hero` | `Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php` |
| `BlockDefinition_ibexa_component_lead` | `Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php` |
| `BlockDefinition_ibexa_component_logos` | `Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php` |
| `BlockDefinition_ibexa_component_quote` | `Handler` | `expLayoutsContentComponentBlockHandler` | `extension/explayouts/classes/explayoutscontentcomponentblockhandler.php` |
| `BlockDefinition_image` | `Handler` | `expLayoutsImageBlockHandler` | `extension/explayouts/classes/explayoutsimageblockhandler.php` |
| `BlockDefinition_lead` | `Handler` | `expLayoutsComponentBlockHandler` | `extension/explayouts/classes/explayoutscomponentblockhandler.php` |
| `BlockDefinition_list` | `Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/classes/explayoutslistblockhandler.php` |
| `BlockDefinition_list_accordion` | `Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/classes/explayoutslistblockhandler.php` |
| `BlockDefinition_list_zigzag` | `Handler` | `expLayoutsListBlockHandler` | `extension/explayouts/classes/explayoutslistblockhandler.php` |
| `BlockDefinition_logos` | `Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/classes/explayoutsgalleryblockhandler.php` |
| `BlockDefinition_map` | `Handler` | `expLayoutsMapBlockHandler` | `extension/explayouts/classes/explayoutsmapblockhandler.php` |
| `BlockDefinition_markdown` | `Handler` | `expLayoutsMarkdownBlockHandler` | `extension/explayouts/classes/explayoutsmarkdownblockhandler.php` |
| `BlockDefinition_progress` | `Handler` | `expLayoutsProgressBlockHandler` | `extension/explayouts/classes/explayoutsprogressblockhandler.php` |
| `BlockDefinition_quote` | `Handler` | `expLayoutsQuoteBlockHandler` | `extension/explayouts/classes/explayoutsquoteblockhandler.php` |
| `BlockDefinition_rich_text` | `Handler` | `expLayoutsRichTextBlockHandler` | `extension/explayouts/classes/explayoutsrichtextblockhandler.php` |
| `BlockDefinition_single` | `Handler` | `expLayoutsSingleBlockHandler` | `extension/explayouts/classes/explayoutssingleblockhandler.php` |
| `BlockDefinition_slider` | `Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/classes/explayoutsgalleryblockhandler.php` |
| `BlockDefinition_spacer` | `Handler` | `expLayoutsSpacerBlockHandler` | `extension/explayouts/classes/explayoutsspacerblockhandler.php` |
| `BlockDefinition_sushi_bar` | `Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/classes/explayoutsgalleryblockhandler.php` |
| `BlockDefinition_tabs` | `Handler` | `expLayoutsTabsBlockHandler` | `extension/explayouts/classes/explayoutstabsblockhandler.php` |
| `BlockDefinition_text` | `Handler` | `expLayoutsTextBlockHandler` | `extension/explayouts/classes/explayoutstextblockhandler.php` |
| `BlockDefinition_three_columns` | `Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/classes/explayoutscontainerblockhandler.php` |
| `BlockDefinition_thumb_gallery` | `Handler` | `expLayoutsGalleryBlockHandler` | `extension/explayouts/classes/explayoutsgalleryblockhandler.php` |
| `BlockDefinition_title` | `Handler` | `expLayoutsTitleBlockHandler` | `extension/explayouts/classes/explayoutstitleblockhandler.php` |
| `BlockDefinition_tpl_block` | `Handler` | `expLayoutsTplBlockHandler` | `extension/explayouts/classes/explayoutstplblockhandler.php` |
| `BlockDefinition_two_columns` | `Handler` | `expLayoutsContainerBlockHandler` | `extension/explayouts/classes/explayoutscontainerblockhandler.php` |
| `BlockDefinition_video` | `Handler` | `expLayoutsVideoBlockHandler` | `extension/explayouts/classes/explayoutsvideoblockhandler.php` |
| `QueryType_children` | `Handler` | `expLayoutsChildrenQueryHandler` | `extension/explayouts/classes/explayoutschildrenqueryhandler.php` |
| `QueryType_content_by_topic` | `Handler` | `expLayoutsContentByTopicQueryHandler` | `extension/explayouts/classes/explayoutscontentbytopicqueryhandler.php` |
| `QueryType_exp_content_relation_list` | `Handler` | `expLayoutsRelationListQueryHandler` | `extension/explayouts/classes/explayoutsrelationlistqueryhandler.php` |
| `QueryType_exp_content_reverse_relation_list` | `Handler` | `expLayoutsReverseRelationListQueryHandler` | `extension/explayouts/classes/explayoutsreverserelationlistqueryhandler.php` |
| `QueryType_exp_content_tags` | `Handler` | `expLayoutsTagsQueryHandler` | `extension/explayouts/classes/explayoutstagsqueryhandler.php` |
| `QueryType_exponential_content_search` | `Handler` | `expLayoutsExponentialContentSearchQueryHandler` | `extension/explayouts/classes/explayoutsexponentialcontentsearchqueryhandler.php` |
| `QueryType_latest` | `Handler` | `expLayoutsLatestQueryHandler` | `extension/explayouts/classes/explayoutslatestqueryhandler.php` |
| `QueryType_manual` | `Handler` | `expLayoutsManualQueryHandler` | `extension/explayouts/classes/explayoutsmanualqueryhandler.php` |
| `QueryType_parent` | `Handler` | `expLayoutsParentQueryHandler` | `extension/explayouts/classes/explayoutsparentqueryhandler.php` |
| `QueryType_random` | `Handler` | `expLayoutsRandomQueryHandler` | `extension/explayouts/classes/explayoutsrandomqueryhandler.php` |
| `QueryType_siblings` | `Handler` | `expLayoutsSiblingsQueryHandler` | `extension/explayouts/classes/explayoutssiblingsqueryhandler.php` |
| `QueryType_subtree` | `Handler` | `expLayoutsSubtreeQueryHandler` | `extension/explayouts/classes/explayoutssubtreequeryhandler.php` |

### export.ini (28)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `General` | `ExportableDatatypes[]` | `ezdate` | `lib/ezlocale/classes/ezdate.php` |
| `General` | `ExportableDatatypes[]` | `ezdatetime` | `lib/ezlocale/classes/ezdatetime.php` |
| `General` | `ExportableDatatypes[]` | `ezoption` | `kernel/classes/datatypes/ezoption/ezoption.php` |
| `General` | `ExportableDatatypes[]` | `eztime` | `lib/ezlocale/classes/eztime.php` |
| `General` | `ExportableDatatypes[]` | `ezurl` | `kernel/classes/datatypes/ezurl/ezurl.php` |
| `General` | `ExportableDatatypes[]` | `ezxmltext` | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |
| `General` | `ExportableDatatypes[]` | `ezbirthday` | `extension/birthday/datatypes/ezbirthday/ezbirthday.php` |
| `ezbirthday` | `HandlerClass` | `eZBirthdayHandler` | `extension/bccie/classes/handlers/ezbirthdayhandler.php` |
| `ezbirthday` | `HandlerClass` | `eZBirthdayExportHandler` | `extension/bccie/classes/handlers/ezbirthdayexporthandler.php` |
| `ezboolean` | `HandlerClass` | `eZBooleanHandler` | `extension/bccie/classes/handlers/ezbooleanhandler.php` |
| `ezcountry` | `HandlerClass` | `eZCountryHandler` | `extension/bccie/classes/handlers/ezcountrtyhandler.php` |
| `ezdate` | `HandlerClass` | `eZDateHandler` | `extension/bccie/classes/handlers/ezdatehandler.php` |
| `ezdatetime` | `HandlerClass` | `eZDateTimeHandler` | `extension/bccie/classes/handlers/ezdatetimehandler.php` |
| `ezemail` | `HandlerClass` | `eZEmailHandler` | `extension/bccie/classes/handlers/ezemailhandler.php` |
| `ezenhancedobjectrelation` | `HandlerClass` | `ezenhancedobjectrelationHandler` | `extension/bccie/classes/handlers/ezenhancedobjectrelationhandler.php` |
| `ezfloat` | `HandlerClass` | `eZFloatHandler` | `extension/bccie/classes/handlers/ezfloathandler.php` |
| `ezidentifier` | `HandlerClass` | `eZIdentifierHandler` | `extension/bccie/classes/handlers/ezidentifierhandler.php` |
| `ezinteger` | `HandlerClass` | `eZIntegerHandler` | `extension/bccie/classes/handlers/ezintegerhandler.php` |
| `ezobjectrelation` | `HandlerClass` | `eZObjectRelationHandler` | `extension/bccie/classes/handlers/ezobjectrelationhandler.php` |
| `ezoption` | `HandlerClass` | `eZOptionHandler` | `extension/bccie/classes/handlers/ezoptionhandler.php` |
| `ezselection` | `HandlerClass` | `eZSelectionHandler` | `extension/bccie/classes/handlers/ezselectionhandler.php` |
| `ezstring` | `HandlerClass` | `eZStringHandler` | `extension/bccie/classes/handlers/ezstringhandler.php` |
| `eztext` | `HandlerClass` | `eZTextHandler` | `extension/bccie/classes/handlers/eztexthandler.php` |
| `eztime` | `HandlerClass` | `eZTimeHandler` | `extension/bccie/classes/handlers/eztimehandler.php` |
| `ezurl` | `HandlerClass` | `eZURLHandler` | `extension/bccie/classes/handlers/ezurlhandler.php` |
| `ezxmltext` | `HandlerClass` | `eZXMLTextHandler` | `extension/bccie/classes/handlers/ezxmltexthandler.php` |
| `owenhancedselection` | `HandlerClass` | `OWEnhancedSelectionHandler` | `extension/bccie/classes/handlers/owenhancedselectionhandler.php` |
| `smileobjectrelationlist` | `HandlerClass` | `smileobjectrelationlistHandler` | `extension/bccie/classes/handlers/smileobjectrelationlisthandler.php` |

### extendedattributefilter.ini (9)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `CjwNewsletterEditionFilter` | `ClassName` | `CjwNewsletterEditionFilter` | `extension/cjw_newsletter/extendedattributefilter/cjwnewslettereditionfilter.php` |
| `CjwNewsletterListFilter` | `ClassName` | `CjwNewsletterListFilter` | `extension/cjw_newsletter/extendedattributefilter/cjwnewsletterlistfilter.php` |
| `TagsAttributeAndFilter` | `ClassName` | `eZTagsAttributeFilter` | `extension/eztags/classes/eztagsattributefilter.php` |
| `TagsAttributeAndMultipleFilter` | `ClassName` | `eZTagsAttributeFilter` | `extension/eztags/classes/eztagsattributefilter.php` |
| `TagsAttributeFilter` | `ClassName` | `eZTagsAttributeFilter` | `extension/eztags/classes/eztagsattributefilter.php` |
| `TagsTreeAttributeFilter` | `ClassName` | `eZTagsTreeAttributeFilter` | `extension/eztags/classes/eztagstreeattributefilter.php` |
| `ezgmlLocationFilter` | `ClassName` | `ezgmlLocationFilter` | `extension/ezgmaplocation/classes/ezgmllocationfilter.php` |
| `ezgmlLocationFilter` | `ExtensionName` | `ezgmaplocation` | `extension/ezgmaplocation/classes/ezgmaplocation.php` |
| `ezsrRatingFilter` | `ClassName` | `ezsrRatingFilter` | `extension/ezstarrating/classes/ezsrratingfilter.php` |

### ezfind.ini (3)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `SolrFieldMapSettings` | `CustomMap[eztags]` | `ezfSolrDocumentFieldeZTags` | `extension/eztags/classes/ezfsolrdocumentfieldeztags.php` |
| `SolrFieldMapSettings` | `CustomMap[sckenhancedselection]` | `ezfSolrDocumentFieldSckEnhancedSelection` | `extension/enhancedselection2/classes/ezfsolrdocumentfieldsckenhancedselection.php` |
| `SolrFieldMapSettings` | `CustomMap[xrowmetadata]` | `ezfSolrDocumentFieldxrowMetadata` | `extension/xrowmetadata/classes/ezfSolrDocumentFieldxrowMetadata.php` |

### ezjscore.ini (18)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `AjaxUploader` | `AjaxUploadHandler[ezobjectrelation]` | `ezpRelationAjaxUploader` | `extension/ezjscore/classes/ajaxuploader/ezprelationajaxuploader.php` |
| `AjaxUploader` | `AjaxUploadHandler[ezobjectrelationlist]` | `ezpRelationListAjaxUploader` | `extension/ezjscore/classes/ajaxuploader/ezprelationlistajaxuploader.php` |
| `eZJSCore` | `CssOptimizer[]` | `ezjscCssOptimizer` | `extension/ezjscore/classes/ezjsccssoptimizer.php` |
| `eZJSCore` | `JavaScriptOptimizer[]` | `ezjscJavascriptOptimizer` | `extension/ezjscore/classes/ezjscjavascriptoptimizer.php` |
| `ezjscServer` | `FunctionList[]` | `ezjsctags` | `extension/eztags/classes/ezjsctags.php` |
| `ezjscServer` | `FunctionList[]` | `ezjsctagschildren` | `extension/eztags/classes/ezjsctagschildren.php` |
| `ezjscServer_expajaxloadmore` | `Class` | `expLayoutsAjaxLoadMoreServer` | `extension/explayouts/classes/explayoutsajaxloadmoreserver.php` |
| `ezjscServer_ezajaxuploader` | `Class` | `ezjscServerFunctionsAjaxUploader` | `extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php` |
| `ezjscServer_ezautosave` | `Class` | `ezjscServerFunctionsAutosave` | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php` |
| `ezjscServer_ezflow` | `Class` | `eZFlowServerCallFunctions` | `extension/ezflow/classes/ezflowservercallfunctions.php` |
| `ezjscServer_ezjsc` | `Class` | `ezjscServerFunctionsJs` | `extension/ezjscore/classes/ezjscserverfunctionsjs.php` |
| `ezjscServer_ezjscnode` | `Class` | `ezjscServerFunctionsNode` | `extension/ezjscore/classes/ezjscserverfunctionsnode.php` |
| `ezjscServer_ezjsctags` | `Class` | `ezjscTags` | `extension/eztags/classes/ezjsctags.php` |
| `ezjscServer_ezjsctagschildren` | `Class` | `ezjscTagsChildren` | `extension/eztags/classes/ezjsctagschildren.php` |
| `ezjscServer_ezoe` | `Class` | `ezoeServerFunctions` | `extension/ezoe/classes/ezoeserverfunctions.php` |
| `ezjscServer_ezpublishingqueue` | `Class` | `ezjscServerFunctionsPublishingQueue` | `extension/ezjscore/classes/ezjscserverfunctionspublishingqueue.php` |
| `ezjscServer_ezstarrating` | `Class` | `ezsrServerFunctions` | `extension/ezstarrating/classes/ezsrserverfunctions.php` |
| `ezjscServer_ezwt` | `Class` | `ezwtServerCallFunctions` | `extension/ezwt/classes/ezwtservercallfunctions.php` |

### ezmcp.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `TransportSettings` | `DefaultTransport` | `stdio` | *alias* |

### ezoe.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `SpellChecker` | `config[general.engine]` | `GoogleSpell` | `extension/ezoe/modules/ezoe/classes/GoogleSpell.php` |

### ezrest.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `RESTSettings` | `HandlerList[]` | `eZRESTODFHandler` | `extension/ezodf/classes/ezrestodfhandler.php` |

### ezxml.ini (6)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `InputSettings` | `AliasClasses[eZSimplifiedXMLInput]` | `eZOEXMLInput` | `extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php` |
| `InputSettings` | `HandlerClass` | `eZSimplifiedXMLInput` | `kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinput.php` |
| `OutputSettings` | `AliasClasses[ezpdf]` | `eZPDFXMLOutput` | `kernel/classes/datatypes/ezxmltext/handlers/output/ezpdfxmloutput.php` |
| `OutputSettings` | `HandlerClass` | `eZXHTMLXMLOutput` | `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php` |
| `OutputSettings` | `HandlerClass` | `ExplBlockXHTMLXMLOutput` | `extension/explayouts/classes/explblockxhtmlxmloutput.php` |
| `OutputSettings` | `HandlerClass` | `sevenxThemesMediaXHTMLXMLOutput` | `extension/sevenx_themes_media/classes/sevenxthemesmediaxhtmlxmloutput.php` |

### file.ini (8)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `BinaryFileSettings` | `Handler` | `eZFilePassthroughHandler` | `kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php` |
| `ClusteringSettings` | `FileHandler` | `eZFSFileHandler` | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php` |
| `FileSettings` | `FileExtensionBlackList[]` | `phar` | `(declared at runtime)` |
| `FileSettings` | `Handlers[gzip]` | `ezgzipcompressionhandler` | `lib/ezfile/classes/ezgzipcompressionhandler.php` |
| `FileSettings` | `Handlers[gzipshell]` | `ezgzipshellcompressionhandler` | `lib/ezfile/classes/ezgzipshellcompressionhandler.php` |
| `FileSettings` | `Handlers[gzipzlib]` | `ezgzipzlibcompressionhandler` | `lib/ezfile/classes/ezgzipzlibcompressionhandler.php` |
| `eZDFSClusteringSettings` | `DBBackend` | `eZDFSFileHandlerMySQLiBackend` | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php` |
| `eZDFSClusteringSettings` | `DFSBackend` | `eZDFSFileHandlerDFSBackend` | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php` |

### git_manager.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `GitManagerSettings` | `VarExcludeDirs[]` | `cache` | `vendor/zetacomponents/signal-slot/docs/tutorial_multiple_slots_example.php` |

### http.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `HTTPCacheHandlers` | `Handlers[]` | `eZSquidCacheManager` | `extension/ezflow/classes/ezsquidcachemanager.php` |

### image.ini (4)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `EXIFAnalyzer` | `Handler` | `ezexif` | *alias* |
| `GD` | `Handler` | `eZImageGDFactory` | `lib/ezimage/classes/ezimagegdfactory.php` |
| `GIFAnalyzer` | `Handler` | `ezgif` | *alias* |
| `ImageMagick` | `Handler` | `eZImageShellFactory` | `lib/ezimage/classes/ezimageshellfactory.php` |

### menu.ini (2)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `TopAdminMenu` | `Tabs[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |
| `TopAdminMenu` | `Tabs[]` | `gitmanager` | `extension/git_manager/classes/git_manager.php` |

### notification.ini (2)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `RuleSettings` | `Alias[keyword]` | `ezkeyword` | `kernel/classes/datatypes/ezkeyword/ezkeyword.php` |
| `TransportSettings` | `DefaultTransport` | `mail` | *alias* |

### package.ini (21)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `CreationSettings` | `HandlerAlias[ezcontentclass]` | `eZContentClassPackageCreator` | `kernel/classes/packagecreators/ezcontentclass/ezcontentclasspackagecreator.php` |
| `CreationSettings` | `HandlerAlias[ezcontentobject]` | `eZContentObjectPackageCreator` | `kernel/classes/packagecreators/ezcontentobject/ezcontentobjectpackagecreator.php` |
| `CreationSettings` | `HandlerAlias[ezextension]` | `eZExtensionPackageCreator` | `kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php` |
| `CreationSettings` | `HandlerAlias[ezstyle]` | `eZStylePackageCreator` | `kernel/classes/packagecreators/ezstyle/ezstylepackagecreator.php` |
| `CreationSettings` | `HandlerList[]` | `ezcontentclass` | `kernel/classes/ezcontentclass.php` |
| `CreationSettings` | `HandlerList[]` | `ezcontentobject` | `kernel/classes/ezcontentobject.php` |
| `CreationSettings` | `HandlerList[]` | `ezextension` | `lib/ezutils/classes/ezextension.php` |
| `InstallerSettings` | `HandlerAlias[ezcontentobject]` | `eZContentObjectPackageInstaller` | `kernel/classes/packageinstallers/ezcontentobject/ezcontentobjectpackageinstaller.php` |
| `InstallerSettings` | `HandlerAlias[ezinstallscript]` | `eZInstallScriptPackageInstaller` | `kernel/classes/packageinstallers/ezinstallscript/ezinstallscriptpackageinstaller.php` |
| `InstallerSettings` | `HandlerList[]` | `ezcontentobject` | `kernel/classes/ezcontentobject.php` |
| `PackageSettings` | `HandlerAlias[ezcontentclass]` | `eZContentClassPackageHandler` | `kernel/classes/packagehandlers/ezcontentclass/ezcontentclasspackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezcontentobject]` | `eZContentObjectPackageHandler` | `kernel/classes/packagehandlers/ezcontentobject/ezcontentobjectpackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezdesign]` | `eZFilePackageHandler` | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezextension]` | `eZExtensionPackageHandler` | `kernel/classes/packagehandlers/ezextension/ezextensionpackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezfile]` | `eZFilePackageHandler` | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezini]` | `eZFilePackageHandler` | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php` |
| `PackageSettings` | `HandlerAlias[eziniaddon]` | `eZINIAddonPackageHandler` | `kernel/classes/packagehandlers/eziniaddon/eziniaddonpackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezinstallscript]` | `eZInstallScriptPackageHandler` | `kernel/classes/packagehandlers/ezinstallscript/ezinstallscriptpackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezsql]` | `eZDBPackageHandler` | `kernel/classes/packagehandlers/ezdb/ezdbpackagehandler.php` |
| `PackageSettings` | `HandlerAlias[eztemplate]` | `eZFilePackageHandler` | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php` |
| `PackageSettings` | `HandlerAlias[ezthumbnail]` | `eZFilePackageHandler` | `kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php` |

### rest.ini (8)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `ApiProvider` | `ProviderClass[auth]` | `ezpRestAuthProvider` | `kernel/private/rest/classes/auth/auth_provider.php` |
| `ApiProvider` | `ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapi/classes/rest_provider.php` |
| `ApiProvider` | `ProviderClass[ezp]` | `ezpRestApiProvider` | `extension/ezprestapiprovider/classes/rest_provider.php` |
| `ApiProvider` | `ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapi/classes/rest_provider.php` |
| `Authentication` | `AuthenticationStyle` | `ezpRestOauthAuthenticationStyle` | `kernel/private/rest/classes/auth/styles/oauth.php` |
| `OutputSettings` | `RendererClass[xhtml]` | `ezpContentXHTMLRenderer` | `kernel/private/rest/classes/renderers/xhtml_content_renderer.php` |
| `RouteSettings` | `RouteSettingImpl` | `ezpRestIniRouteFilter` | `kernel/private/rest/classes/auth/ini_route_security.php` |
| `System` | `PrefixFilterClass` | `ezpRestDefaultRegexpPrefixFilter` | `kernel/private/rest/classes/default_regexp_prefix_filter.php` |

### setup.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `ezcversion` | `TestClass` | `ezcBaseFile` | `vendor/zetacomponents/base/src/file.php` |

### shop.ini (3)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `BasketInfoSettings` | `Handler` | `ezdefault` | *alias* |
| `ExchangeRatesSettings` | `ExchangeRatesUpdateHandler` | `eZECB` | **not declared** |
| `MathSettings` | `MathHandler` | `eZPHPMath` | `lib/ezmath/classes/mathhandlers/ezphpmath.php` |

### shopaccount.ini (2)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `AccountSettings` | `Handler` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `ConfirmOrderSettings` | `Handler` | `ezdefault` | *alias* |

### site.ini (29)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `Cache_ezjscore` | `class` | `ezjscCacheManager` | `extension/ezjscore/classes/ezjsccachemanager.php` |
| `Cache_restRoutes` | `class` | `ezpRestRoutesCacheClear` | `kernel/private/rest/classes/cache/clear_routes.php` |
| `Cache_restRoutes` | `purgeClass` | `ezpRestRoutesCacheClear` | `kernel/private/rest/classes/cache/clear_routes.php` |
| `ContentSettings` | `ContentClassEditHandler` | `eZContentClassEditHandler` | `kernel/classes/ezcontentclassedithandler.php` |
| `ContentSettings` | `DatatypeBlackListForExternal[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `ContentSettings` | `StaticCacheHandler` | `eZStaticCache` | `kernel/classes/ezstaticcache.php` |
| `DatabaseSettings` | `DatabaseImplementation` | `ezmysqli` | *alias* |
| `DatabaseSettings` | `ImplementationAlias[ezmysql]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[ezmysqli]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[ezpostgresql]` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| `DatabaseSettings` | `ImplementationAlias[mongodb]` | `expMongoDB` | `lib/ezdb/classes/expmongodb.php` |
| `DatabaseSettings` | `ImplementationAlias[mysql]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[mysqli]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[pgsql]` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| `DatabaseSettings` | `ImplementationAlias[postgresql]` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| `DatabaseSettings` | `ImplementationAlias[sqlite3]` | `eZSQLite3DB` | `lib/ezdb/classes/ezsqlite3db.php` |
| `DebugSettings` | `AlwaysLog[]` | `error` | `(declared at runtime)` |
| `FileSettings` | `CacheDir` | `cache` | `vendor/zetacomponents/signal-slot/docs/tutorial_multiple_slots_example.php` |
| `MailSettings` | `Transport` | `sendmail` | *alias* |
| `MailSettings` | `TransportAlias[file]` | `eZFileTransport` | `lib/ezutils/classes/ezfiletransport.php` |
| `MailSettings` | `TransportAlias[sendmail]` | `eZSendmailTransport` | `lib/ezutils/classes/ezsendmailtransport.php` |
| `MailSettings` | `TransportAlias[smtp]` | `eZSMTPTransport` | `lib/ezutils/classes/ezsmtptransport.php` |
| `RegionalSettings` | `LanguageSwitcherClass` | `ezpLanguageSwitcher` | `kernel/private/classes/ezplanguageswitcher.php` |
| `SearchSettings` | `SearchEngine` | `eZSearchEngine` | `kernel/search/plugins/ezsearchengine/ezsearchengine.php` |
| `SiteAccessSettings` | `MobileDeviceFilterClass` | `ezpMobileDeviceRegexpFilter` | `kernel/private/classes/ezpmobiledeviceregexpfilter.php` |
| `SiteSettings` | `ErrorHandler` | `displayerror` | *alias* |
| `URLTranslator` | `FilterClasses[]` | `eZURLAliasFilterAppendNodeID` | `kernel/private/classes/urlaliasfilters/ezurlaliasfilterappendnodeid.php` |
| `UserSettings` | `LoginHandler[]` | `standard` | *alias* |
| `UserSettings` | `LoginHandler[]` | `paex` | *alias* |

### syndication.ini (1)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `SyndicationFilters` | `FilterArray[]` | `Attribute` | `(declared at runtime)` |

### template.ini (3)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `AttributeOperator` | `DefaultFormatter` | `html` | *alias* |
| `AttributeOperator` | `OutputFormatter[html]` | `ezpAttributeOperatorHTMLFormatter` | `kernel/private/eztemplate/ezpattributeoperatorhtmlformatter.php` |
| `AttributeOperator` | `OutputFormatter[text]` | `ezpAttributeOperatorTextFormatter` | `kernel/private/eztemplate/ezpattributeoperatortextformatter.php` |

### upload.ini (3)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `CreateSettings` | `MimeUploadHandlerMap[application/msword]` | `ezopenofficeuploadhandler` | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php` |
| `CreateSettings` | `MimeUploadHandlerMap[application/rtf]` | `ezopenofficeuploadhandler` | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php` |
| `CreateSettings` | `MimeUploadHandlerMap[application/vnd.oasis.opendocument.text]` | `ezopenofficeuploadhandler` | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php` |

## Places the kernel looks

Every setting that names a directory to search or an extension to search in. Add
your extension to one of these and your file is found; leave it out and the class
is never loaded, however correctly it is written. Most of the time something works
and should not, or does not work and should, the answer is one of these lines.

| ini | Section | Setting | Currently |
| --- | --- | --- | --- |
| `binaryfile.ini` | `HandlerSettings` | `ExtensionRepositories` | empty |
| `content.ini` | `ActionSettings` | `ExtensionDirectories` | empty |
| `content.ini` | `DataTypeSettings` | `RepositoryDirectories` | `kernel/classes/datatypes` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | empty |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `expauthentication_2fa`, `exp_enhanced_link`, `ngclasslist`, `xrowmetadata` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `birthday` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `cjw_newsletter` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `enhancedezbinaryfile` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `enhancedselection2` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `exp_enhanced_link` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `ezflow` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `ezgmaplocation` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `ezmbpaex` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `ezstarrating` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `eztags` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `hcaptcha` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `ngclasslist` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `recaptcha` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `sevenx_themes_media` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | `xrowmetadata` |
| `content.ini` | `EditSettings` | `ExtensionDirectories` | empty |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | empty |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `bccie` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `bcgooglesitemaps` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `cjw_newsletter` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `ezflow` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `ezmbpaex` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `syndication` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `xrowmetadata` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | empty |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `expauthentication_2fa` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `autonotifications` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `autorss` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `bccie` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `bcgooglesitemaps` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `bcwebsitestatistics` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `birthday` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `cjw_newsletter` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `enhancedezbinaryfile` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `enhancedselection2` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `expchangeclass` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `expdse` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `explayouts` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `explayouts_content_browser_ui` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `explayouts_relation_list_query` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `explayouts_site_api` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `explayouts_tags_query` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `explayouts_ui` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `explayouts_ui_api` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `expsite_api` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `expsite_core` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezauthorize` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezautosave` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezdemo` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezflow` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezgmaplocation` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezie` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezjscore` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezmbpaex` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezmultiupload` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezodf` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezoe` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezownerchange` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezpm` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezssp` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezstarrating` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `eztags` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezupdate` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezwebin` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ezwt` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `git_manager` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `hcaptcha` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `ngclasslist` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `powercontent` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `recaptcha` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `sevenx_dse` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `sevenx_themes_media` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `sevenx_themes_simple` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `sevenx_themes_super` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `swark` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `syndication` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `xrowextract` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | `xrowmetadata` |
| `ezxml.ini` | `HandlerSettings` | `ExtensionRepositories` | `ezoe` |
| `icon.ini` | `ExtensionSettings` | `IconExtensions` | empty |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | empty |
| `module.ini` | `ModuleSettings` | `ModuleList` | `class`, `collaboration`, `content`, `error`, `ezinfo` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `bccie` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `bccie` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `bcgooglesitemaps` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `cjw_newsletter` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `newsletter` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `expchangeclass` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `expdse` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `dse` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `explayouts` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts_content_browser_ui` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `explayouts_content_browser_ui` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts_ui` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `explayouts_ui` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts_ui_api` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `explayouts_ui_api` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezauthorize` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezauthorize` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezflow` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `ezflow`, `flash` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezie` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `ezie` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezjscore` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `ezjscore` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezmbpaex` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `userpaex` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezmultiupload` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `ezmultiupload` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezodf` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `ezodf` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezoe` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `ezoe` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezownerchange` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `owner` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezpaypal` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `paypal` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezpm` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `pm` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `eztags` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `tags` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezupdate` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `update` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezwt` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `websitetoolbar` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `git_manager` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `git_manager` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `hcaptcha` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `hcaptcha` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `nxc_powercontent` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `content` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `powercontent` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `powercontent` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `recaptcha` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `recaptcha` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `sevenx_dse` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `dse` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `sevenx_themes_media` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `info-collection` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `syndication` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `xrowextract` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `xrowextract` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `xrowmetadata` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `sitemaps` |
| `notification.ini` | `NotificationEventHandlerSettings` | `RepositoryDirectories` | `kernel/classes/notification/handler/` |
| `notification.ini` | `NotificationEventHandlerSettings` | `ExtensionDirectories` | empty |
| `notification.ini` | `NotificationEventTypeSettings` | `RepositoryDirectories` | `kernel/classes/notification/event/` |
| `notification.ini` | `NotificationEventTypeSettings` | `ExtensionDirectories` | empty |
| `notification.ini` | `RuleSettings` | `RepositoryDirectories` | `kernel/notification/rules` |
| `notification.ini` | `RuleSettings` | `ExtensionDirectories` | empty |
| `package.ini` | `PackageSettings` | `RepositoryDirectories` | `kernel/classes` |
| `package.ini` | `PackageSettings` | `ExtensionDirectories` | empty |
| `shop.ini` | `BasketInfoSettings` | `RepositoryDirectories` | `kernel/classes/basketinfohandlers` |
| `shop.ini` | `BasketInfoSettings` | `ExtensionDirectories` | empty |
| `shop.ini` | `ExchangeRatesSettings` | `RepositoryDirectories` | `kernel/shop/classes/exchangeratehandlers` |
| `shop.ini` | `ExchangeRatesSettings` | `ExtensionDirectories` | empty |
| `shop.ini` | `ShippingSettings` | `RepositoryDirectories` | `kernel/classes/shippinghandlers` |
| `shop.ini` | `ShippingSettings` | `ExtensionDirectories` | empty |
| `shop.ini` | `VATSettings` | `RepositoryDirectories` | `kernel/classes/vathandlers` |
| `shop.ini` | `VATSettings` | `ExtensionDirectories` | empty |
| `shopaccount.ini` | `HandlerSettings` | `ExtensionRepositories` | empty |
| `site.ini` | `DesignSettings` | `DesignLocationCache` | `disabled` |
| `site.ini` | `DesignSettings` | `DesignLocationCache` | `enabled` |
| `site.ini` | `DesignSettings` | `DesignExtensions` | `expsite_app` |
| `site.ini` | `ExtensionSettings` | `ExtensionDirectory` | `extension` |
| `site.ini` | `ExtensionSettings` | `AdditionalExtensionDirectories` | empty |
| `site.ini` | `ExtensionSettings` | `ActiveExtensions` | `xrowmetadata` |
| `site.ini` | `ExtensionSettings` | `ActiveAccessExtensions` | empty |
| `site.ini` | `ExtensionSettings` | `ActiveExtensions` | `ezjscore`, `ezoe`, `ezformtoken`, `xrowmetadata`, `ezjscore` |
| `site.ini` | `ExtensionSettings` | `ActiveExtensions` | `expsite_app` |
| `site.ini` | `RSSSettings` | `ActiveExtensions` | empty |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | empty |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `autonotifications` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `bccie` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `birthday` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `enhancedezbinaryfile` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezautosave` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezdemo` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezflow` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezgmaplocation` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezie` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezmbpaex` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezmultiupload` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezodf` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezoe` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezpm` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezstarrating` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `eztags` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezwebin` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ezwt` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `hcaptcha` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `ngclasslist` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `recaptcha` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `xrowextract` |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | `xrowmetadata` |
| `site.ini` | `SearchSettings` | `ExtensionDirectories` | empty |
| `site.ini` | `TemplateSettings` | `AutoloadPathList` | `lib/eztemplate/classes/`, `kernel/common/`, `lib/ezpdf/classes/`, `kernel/private/eztemplate/` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | empty |
| `site.ini` | `TemplateSettings` | `AutoloadPathList` | `extension/bcwebsitestatistics/autoloads/` |
| `site.ini` | `TemplateSettings` | `ExtensionRepositories` | `bcwebsitestatistics` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `cjw_newsletter` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `enhancedezbinaryfile` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `explayouts` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `ezdemo` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `ezflow` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `ezjscore` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `ezoe` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `ezstarrating` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `eztags` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `ezwebin` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `ezwt` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `hcaptcha` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `owsimpleoperator` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `powercontent` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `recaptcha` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `sevenx_themes_media` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `str_replace` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `swark` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | `xrowmetadata` |
| `site.ini` | `UserSettings` | `ExtensionDirectory` | empty |
| `site.ini` | `UserSettings` | `ExtensionDirectory` | `ezmbpaex` |
| `workflow.ini` | `EventSettings` | `RepositoryDirectories` | `kernel/classes/workflowtypes` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | empty |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `autonotifications` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `autorss` |
| `workflow.ini` | `EventSettings` | `RepositoryDirectories` | `extension/bcwebsitestatistics/workflowtypes` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `bcwebsitestatistics` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `ezflow` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `ezssp` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `swark` |

## Interfaces and abstract classes

What the kernel declares for somebody else to implement, heaviest first. The ones
with many methods and one implementation are the deep water: replacing them means
answering for everything the one that ships already answers for.

| Contract | Kind | Methods | Implemented by |
| --- | --- | --- | --- |
| `eZClusterFileHandlerInterface` | interface | 44 | `eZFSFileHandler` |
| `expExtensionWizard` | abstract class | 28 | `expDesignExtensionWizard`, `expModuleExtensionWizard`, `expWorkflowEventWizard`, `expHandlerWizard`, `expDatatypeWizard`, `expTemplateExtensionWizard` |
| `ezpSessionHandler` | abstract class | 16 | `ezpSessionHandlerDB`, `ezpSessionHandlerPHP`, `ezpSessionHandlerSymfony` |
| `eZDFSFileHandlerDFSBackendInterface` | interface | 12 | `eZDFSFileHandlerDFSBackend` |
| `ezpAutoloadOutput` | interface | 10 | `ezpAutoloadCliOutput` |
| `ezpRestMvcController` | abstract class | 10 | `ezp7xRestContentController`, `ezpRestContentController` |
| `eZClusterEventListener` | interface | 9 | *nothing yet* |
| `ezpClusterGateway` | abstract class | 8 | `ezpDfsMySQLiClusterGateway`, `ezpDfsPostgresqlClusterGateway` |
| `ezpRestCacheStorageCluster` | abstract class | 8 | `ezpRestCacheStorageClusterObject` |
| `ezpSearchEngine` | interface | 8 | `eZSearchEngine` |
| `ezpRestPrefixFilterInterface` | abstract class | 7 | `ezpRestDefaultRegexpPrefixFilter` |
| `ezpKernelHandler` | interface | 6 | `ezpKernelTreeMenu`, `ezpKernelRest` |
| `ezpStaticCache` | interface | 6 | `eZStaticCache` |
| `ezpLanguageSwitcherCapable` | interface | 5 | `ezpLanguageSwitcher` |
| `ezpMobileDeviceDetectFilterInterface` | interface | 4 | `ezpMobileDeviceRegexpFilter` |
| `ezpRestAuthenticationStyleInterface` | interface | 4 | `ezpRestBasicAuthStyle`, `ezpRestNoAuthStyle`, `ezpRestOauthAuthenticationStyle` |
| `ezpAttributeOperatorFormatterInterface` | interface | 3 | `ezpAttributeOperatorHTMLFormatter`, `ezpAttributeOperatorTextFormatter` |
| `ezpRestAuthenticationStyle` | abstract class | 3 | *nothing yet* |
| `eZMySQLCharset` | abstract class | 2 | *nothing yet* |
| `eZURLAliasFilter` | abstract class | 2 | `eZURLAliasFilterAppendNodeID` |
| `ezpMultivariateTestHandlerInterface` | interface | 2 | `ezpMultivariateTestHandler` |
| `ezpRestContentRendererInterface` | abstract class | 2 | `ezpContentXHTMLRenderer` |
| `ezpRestPreRoutingFilterInterface` | interface | 2 | *nothing yet* |
| `ezpRestProviderInterface` | interface | 2 | `ezpRestAuthProvider`, `ezp7xRestApiProvider`, `ezpRestApiProvider` |
| `ezpRestRequestFilterInterface` | interface | 2 | *nothing yet* |
| `ezpRestResponseFilterInterface` | interface | 2 | *nothing yet* |
| `ezpRestResultFilterInterface` | interface | 2 | *nothing yet* |
| `ezpRestRouteFilterInterface` | abstract class | 2 | `ezpRestIniRouteFilter` |
| `ezpUpdatedContent` | abstract class | 2 | *nothing yet* |
| `eZClusterEventLogger` | interface | 1 | `eZClusterEventLoggerEzdebug`, `eZClusterEventLoggerPhp` |
| `eZClusterEventNotifier` | interface | 1 | `eZDFSFileHandlerMySQLiBackend` |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | interface | 1 | *nothing yet* |
| `ezpAsynchronousPublisherOutput` | interface | 1 | `ezpAsynchronousPublisherCliOutput`, `ezpAsynchronousPublisherLogOutput` |
| `ezpAsynchronousPublishingFilter` | abstract class | 1 | *nothing yet* |
| `ezpAsynchronousPublishingFilterInterface` | interface | 1 | *nothing yet* |
| `ezpContentCriteriaInterface` | interface | 1 | `ezpContentClassCriteria`, `ezpContentDepthCriteria`, `ezpContentFieldCriteria`, `ezpContentLimitCriteria`, `ezpContentLocationCriteria`, `ezpContentSortingCriteria` |
| `ezpDatabaseBasedClusterFileHandler` | interface | 1 | `eZDFSFileHandler` |
| `ezpRestViewControllerInterface` | interface | 1 | `ezpRestApiViewController` |
| `ezpWebBasedKernelHandler` | interface | 1 | `ezpKernel`, `ezpKernelWeb` |
| `ezpRestCacheStorageFile` | abstract class | 0 | *nothing yet* |
| `ezpRestModel` | abstract class | 0 | `ezpOauthUtility`, `ezpRestContentModel` |

## Modules and their views

Every page the system serves. A view can be replaced by an extension carrying a
module of the same name, and a module of your own can add views beside them. A view
with no policy listed is reachable by anybody who can reach the module.

### bccie

`extension/bccie/modules/bccie` — policies: `read`

| View | Needs | Parameters |
| --- | --- | --- |
| `bccie/overview` | `read` | 0 + 1 named |
| `bccie/export` | `read` | 1 |
| `bccie/doexport` | `read` | 1 |

### changeclass

`extension/expchangeclass/modules/changeclass` — policies: `convert`

| View | Needs | Parameters |
| --- | --- | --- |
| `changeclass/select_class` | `convert` | 0 |
| `changeclass/map_attributes` | `convert` | 0 |
| `changeclass/action` | `convert` | 0 |

Fetch functions: `simple_conversion`

### class

`kernel/class`

| View | Needs | Parameters |
| --- | --- | --- |
| `class/edit` | *nothing* | 3 + 1 named |
| `class/view` | *nothing* | 1 + 2 named |
| `class/copy` | *nothing* | 1 |
| `class/down` | *nothing* | 2 |
| `class/up` | *nothing* | 2 |
| `class/removeclass` | *nothing* | 1 |
| `class/removegroup` | *nothing* | 0 |
| `class/classlist` | *nothing* | 1 |
| `class/grouplist` | *nothing* | 0 |
| `class/groupedit` | *nothing* | 1 |
| `class/translation` | *nothing* | 0 |

Fetch functions: `list`, `list_by_groups`, `latest_list`, `attribute_list`, `override_template_list`

### collaboration

`kernel/collaboration`

| View | Needs | Parameters |
| --- | --- | --- |
| `collaboration/action` | *nothing* | 0 |
| `collaboration/view` | *nothing* | 1 + 2 named |
| `collaboration/item` | *nothing* | 2 + 2 named |
| `collaboration/group` | *nothing* | 2 + 2 named |

Fetch functions: `participant`, `participant_list`, `participant_map`, `message_list`, `item_list`, `item_count`, `group_tree`, `tree_count`

### content

`kernel/content` — policies: `bookmark`, `move`, `read`, `diff`, `view_embed`, `create`, `edit`, `publish`, `manage_locations`, `hide`, `reverserelatedlist`, `translate`, `remove`, `versionread`, `versionremove`, `pdf`, `translations`, `urltranslator`, `pendinglist`, `restore`, `cleantrash`, `tipafriend`, `dashboard`

| View | Needs | Parameters |
| --- | --- | --- |
| `content/edit` | `edit or create` | 4 |
| `content/removenode` | `edit` | 4 |
| `content/removeassignment` | `edit` | 0 |
| `content/pdf` | `pdf` | 1 + 5 named |
| `content/view` | `read` | 2 + 5 named |
| `content/copy` | `read` | 1 |
| `content/copysubtree` | `create` | 1 |
| `content/versionview` | `versionread` | 4 + 3 named |
| `content/restore` | `restore` | 1 |
| `content/search` | `read` | 0 + 1 named |
| `content/urlalias` | `edit` | 1 + 1 named |
| `content/urltranslator` | `urltranslator` | 0 + 1 named |
| `content/urlwildcards` | `urltranslator` | 0 + 1 named |
| `content/advancedsearch` | `read` | 1 + 1 named |
| `content/browse` | `read` | 3 + 1 named |
| `content/upload` | `create` | 0 |
| `content/removeobject` | `read` | 0 |
| `content/removeuserobject` | `read` | 0 |
| `content/removemediaobject` | `read` | 0 |
| `content/removeeditversion` | `read` | 0 |
| `content/download` | `read` | 2 + 1 named |
| `content/action` | `read` | 0 |
| `content/collectinformation` | `read` | 0 |
| `content/draft` | `edit` | 0 + 1 named |
| `content/history` | `read`, `edit` | 2 + 1 named |
| `content/trash` | `restore` | 0 + 1 named |
| `content/translations` | `translations` | 1 |
| `content/tipafriend` | `tipafriend`, `read` | 1 |
| `content/keyword` | `read` | 1 + 2 named |
| `content/collectedinfo` | `read` | 1 |
| `content/bookmark` | `bookmark` | 0 + 1 named |
| `content/pendinglist` | `pendinglist` | 0 + 1 named |
| `content/new` | `read` | 0 |
| `content/hide` | `hide` | 1 |
| `content/move` | `edit` | 1 |
| `content/reverserelatedlist` | `reverserelatedlist` | 1 + 1 named |
| `content/translation` | `read` | 0 |
| `content/treemenu` | `read` | 4 |
| `content/dashboard` | `dashboard` | 0 |
| `content/queued` | `edit` | 2 |

Fetch functions: `object`, `version`, `node`, `locale_list`, `locale`, `prioritized_languages`, `prioritized_language_codes`, `translation_list`, `non_translation_list`, `class`, `class_attribute_list`, `class_attribute`, `calendar`, `list`, `list_count`, `tree`, `tree_count`, `search`, `trash_count`, `trash_object_list`, `draft_count`, `draft_version_list`, `pending_count`, `pending_list`, `version_count`, `version_list`, `can_instantiate_class_list`, `class_list`, `can_instantiate_classes`, `contentobject_attributes`, `bookmarks`, `recent`, `section_list`, `tipafriend_top_list`, `view_top_list`, `collected_info_count`, `collected_info_count_list`, `collected_info_collection`, `collected_info_list`, `object_by_attribute`, `object_count_by_user_id`, `same_classattribute_node`, `keyword`, `keyword_count`, `access`, `navigation_parts`, `navigation_part`, `related_objects`, `related_objects_count`, `reverse_related_objects`, `reverse_related_objects_count`, `available_sort_fields`, `country_list`, `related_objects_ids`, `reverse_related_objects_ids`, `content_tree_menu_expiry`

### content

`extension/nxc_powercontent/modules/content` — policies: `bookmark`, `move`, `read`, `diff`, `view_embed`, `create`, `edit`, `publish`, `manage_locations`, `hide`, `reverserelatedlist`, `translate`, `remove`, `versionread`, `versionremove`, `pdf`, `translations`, `urltranslator`, `pendinglist`, `restore`, `cleantrash`, `tipafriend`, `dashboard`

| View | Needs | Parameters |
| --- | --- | --- |
| `content/edit` | `edit or create` | 4 |
| `content/removenode` | `edit` | 4 |
| `content/removeassignment` | `edit` | 0 |
| `content/pdf` | `pdf` | 1 + 5 named |
| `content/view` | `read` | 2 + 5 named |
| `content/copy` | `read` | 1 |
| `content/copysubtree` | `create` | 1 |
| `content/versionview` | `versionread` | 4 + 3 named |
| `content/restore` | `restore` | 1 |
| `content/search` | `read` | 0 + 1 named |
| `content/urlalias` | `edit` | 1 + 1 named |
| `content/urltranslator` | `urltranslator` | 0 + 1 named |
| `content/urlwildcards` | `urltranslator` | 0 + 1 named |
| `content/advancedsearch` | `read` | 1 + 1 named |
| `content/browse` | `read` | 3 + 1 named |
| `content/upload` | `create` | 0 |
| `content/removeobject` | `read` | 0 |
| `content/removeuserobject` | `read` | 0 |
| `content/removemediaobject` | `read` | 0 |
| `content/removeeditversion` | `read` | 0 |
| `content/download` | `read` | 2 + 1 named |
| `content/action` | `read` | 0 |
| `content/collectinformation` | `read` | 0 |
| `content/draft` | `edit` | 0 + 1 named |
| `content/history` | `read`, `edit` | 2 + 1 named |
| `content/trash` | `restore` | 0 + 1 named |
| `content/translations` | `translations` | 1 |
| `content/tipafriend` | `tipafriend`, `read` | 1 |
| `content/keyword` | `read` | 1 + 2 named |
| `content/collectedinfo` | `read` | 1 |
| `content/bookmark` | `bookmark` | 0 + 1 named |
| `content/pendinglist` | `pendinglist` | 0 + 1 named |
| `content/new` | `read` | 0 |
| `content/hide` | `hide` | 1 |
| `content/move` | `edit` | 1 |
| `content/reverserelatedlist` | `reverserelatedlist` | 1 + 1 named |
| `content/translation` | `read` | 0 |
| `content/treemenu` | `read` | 4 |
| `content/dashboard` | `dashboard` | 0 |
| `content/queued` | `edit` | 2 |

Fetch functions: `object`, `version`, `node`, `locale_list`, `locale`, `prioritized_languages`, `prioritized_language_codes`, `translation_list`, `non_translation_list`, `class`, `class_attribute_list`, `class_attribute`, `calendar`, `list`, `list_count`, `tree`, `tree_count`, `search`, `trash_count`, `trash_object_list`, `draft_count`, `draft_version_list`, `pending_count`, `pending_list`, `version_count`, `version_list`, `can_instantiate_class_list`, `class_list`, `can_instantiate_classes`, `contentobject_attributes`, `bookmarks`, `recent`, `section_list`, `tipafriend_top_list`, `view_top_list`, `collected_info_count`, `collected_info_count_list`, `collected_info_collection`, `collected_info_list`, `object_by_attribute`, `object_count_by_user_id`, `same_classattribute_node`, `keyword`, `keyword_count`, `access`, `navigation_parts`, `navigation_part`, `related_objects`, `related_objects_count`, `reverse_related_objects`, `reverse_related_objects_count`, `available_sort_fields`, `country_list`, `related_objects_ids`, `reverse_related_objects_ids`, `content_tree_menu_expiry`

### dse

`extension/expdse/modules/dse` — policies: `dse`, `dump`

| View | Needs | Parameters |
| --- | --- | --- |
| `dse/dashboard` | `dse` | 0 |
| `dse/adminneo` | `dse` | 0 |

### dse

`extension/sevenx_dse/modules/dse` — policies: `dse`, `dump`

| View | Needs | Parameters |
| --- | --- | --- |
| `dse/dashboard` | `dse` | 0 |
| `dse/adminneo` | `dse` | 0 |

### error

`kernel/error`

| View | Needs | Parameters |
| --- | --- | --- |
| `error/view` | *nothing* | 3 |

### explayouts

`extension/explayouts/modules/explayouts` — policies: `read`, `edit`

| View | Needs | Parameters |
| --- | --- | --- |
| `explayouts/dashboard` | `read` | 0 |
| `explayouts/setup` | `edit` | 0 |
| `explayouts/template_editor` | `read`, `edit` | 1 |
| `explayouts/layout_list` | `read` | 0 |
| `explayouts/layout_edit` | `edit` | 1 |
| `explayouts/layout_preview` | `read` | 2 |
| `explayouts/block_edit` | `edit` | 1 |
| `explayouts/rule_list` | `read` | 0 |
| `explayouts/rule_edit` | `edit` | 1 |

Fetch functions: `layout`, `resolve_layout`, `resolve_layout_for_node`, `rules_for_node`

### explayouts_content_browser_ui

`extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui` — policies: `read`

| View | Needs | Parameters |
| --- | --- | --- |
| `explayouts_content_browser_ui/browser` | `read` | 1 |

### explayouts_ui

`extension/explayouts_ui/modules/explayouts_ui` — policies: `read`, `edit`

| View | Needs | Parameters |
| --- | --- | --- |
| `explayouts_ui/layout_list` | `read` | 0 |
| `explayouts_ui/layout_create` | `edit` | 0 |
| `explayouts_ui/layout_edit` | `edit` | 1 |
| `explayouts_ui/rule_list` | `read` | 0 |
| `explayouts_ui/shared_layouts_list` | `read` | 0 |
| `explayouts_ui/rule_edit` | `edit` | 1 |
| `explayouts_ui/block_edit` | `edit` | 1 |
| `explayouts_ui/dashboard` | `read` | 0 |
| `explayouts_ui/layout_preview` | `read` | 2 |
| `explayouts_ui/setup` | `edit` | 0 |
| `explayouts_ui/template_editor` | `read`, `edit` | 1 |
| `explayouts_ui/transfer_import` | `edit` | 0 |
| `explayouts_ui/components` | `read` | 0 |

### explayouts_ui_api

`extension/explayouts_ui_api/modules/explayouts_ui_api` — policies: `read`

| View | Needs | Parameters |
| --- | --- | --- |
| `explayouts_ui_api/layouts` | `read` | 1 |
| `explayouts_ui_api/rules` | `read` | 1 |
| `explayouts_ui_api/blocks` | `read` | 1 |
| `explayouts_ui_api/app` | `read` | 0 |

### ezflow

`extension/ezflow/modules/ezflow` — policies: `timeline`, `edit`, `call`, `changelayout`

| View | Needs | Parameters |
| --- | --- | --- |
| `ezflow/get` | `edit` | 0 |
| `ezflow/timeline` | `timeline` | 2 |
| `ezflow/preview` | `timeline` | 2 |
| `ezflow/zone` | `edit` | 3 |
| `ezflow/request` | `edit` | 0 + 2 named |
| `ezflow/push` | `edit` | 1 |
| `ezflow/block` | `call` | 2 |

Fetch functions: `waiting`, `valid`, `archived`, `valid_nodes`, `block`, `allowed_zones`

### ezie

`extension/ezie/modules/ezie`

| View | Needs | Parameters |
| --- | --- | --- |
| `ezie/prepare` | *nothing* | 4 |
| `ezie/filter_bw` | *nothing* | 0 |
| `ezie/filter_sepia` | *nothing* | 0 |
| `ezie/filter_blur` | *nothing* | 0 |
| `ezie/filter_contrast` | *nothing* | 0 |
| `ezie/filter_brightness` | *nothing* | 0 |
| `ezie/tool_flip_hor` | *nothing* | 0 |
| `ezie/tool_flip_ver` | *nothing* | 0 |
| `ezie/tool_rotation` | *nothing* | 0 |
| `ezie/tool_levels` | *nothing* | 0 |
| `ezie/tool_saturation` | *nothing* | 0 |
| `ezie/tool_pixelate` | *nothing* | 0 |
| `ezie/tool_crop` | *nothing* | 0 |
| `ezie/tool_watermark` | *nothing* | 0 |
| `ezie/no_save_and_quit` | *nothing* | 0 |
| `ezie/save_and_quit` | *nothing* | 0 |

### ezinfo

`kernel/ezinfo` — policies: `read`

| View | Needs | Parameters |
| --- | --- | --- |
| `ezinfo/copyright` | `read` | 0 |
| `ezinfo/about` | `read` | 0 |
| `ezinfo/is_alive` | `read` | 0 |

### ezjscore

`extension/ezjscore/modules/ezjscore` — policies: `run`, `call`

| View | Needs | Parameters |
| --- | --- | --- |
| `ezjscore/hello` | *nothing* | 1 |
| `ezjscore/call` | `call` | 4 |
| `ezjscore/run` | `run` | 0 |

### ezmultiupload

`extension/ezmultiupload/modules/ezmultiupload`

| View | Needs | Parameters |
| --- | --- | --- |
| `ezmultiupload/upload` | *nothing* | 1 |

### ezodf

`extension/ezodf/modules/ezodf` — policies: `import`, `export`

| View | Needs | Parameters |
| --- | --- | --- |
| `ezodf/import` | `import` | 0 + 2 named |
| `ezodf/export` | `export` | 0 + 2 named |

### ezoe

`extension/ezoe/modules/ezoe` — policies: `relations`, `editor`, `search`, `browse`, `disable_editor`

| View | Needs | Parameters |
| --- | --- | --- |
| `ezoe/relations` | `editor` | 6 |
| `ezoe/upload` | `editor` | 4 |
| `ezoe/tags` | `editor` | 4 |
| `ezoe/dialog` | `editor` | 3 |
| `ezoe/embed_view` | `editor` | 1 |
| `ezoe/load` | `editor` | 3 |
| `ezoe/spellcheck_rpc` | `editor` | 0 |
| `ezoe/atd_rpc` | `editor` | 0 |

### flash

`extension/ezflow/modules/flash`

| View | Needs | Parameters |
| --- | --- | --- |
| `flash/embed` | *nothing* | 1 |

### git_manager

`extension/git_manager/modules/git_manager` — policies: `git_manager`, `dump`

| View | Needs | Parameters |
| --- | --- | --- |
| `git_manager/dashboard` | `git_manager` | 0 |
| `git_manager/commit_details` | `git_manager` | 1 |
| `git_manager/dump` | `dump` | 0 |
| `git_manager/download` | `dump` | 2 |

### googlesitemapdynamic

`extension/bcgooglesitemaps/modules/googlesitemapdynamic` — policies: `sitemap`

| View | Needs | Parameters |
| --- | --- | --- |
| `googlesitemapdynamic/sitemap` | `sitemap` | 1 |

### hcaptcha

`extension/hcaptcha/modules/hcaptcha` — policies: `bypass_captcha`

### info-collection

`extension/sevenx_themes_media/modules/info-collection` — policies: `read`

| View | Needs | Parameters |
| --- | --- | --- |
| `info-collection/view-modal` | `read` | 2 |
| `info-collection/submit` | `read` | 1 |

### infocollector

`kernel/infocollector` — policies: `read`

| View | Needs | Parameters |
| --- | --- | --- |
| `infocollector/overview` | `read` | 0 + 1 named |
| `infocollector/collectionlist` | `read` | 1 + 1 named |
| `infocollector/view` | `read` | 1 |

Fetch functions: `collected_info_count`, `collected_info_count_list`, `collected_info_collection`, `collected_info_list`

### layout

`kernel/layout`

| View | Needs | Parameters |
| --- | --- | --- |
| `layout/set` | *nothing* | 1 |

Fetch functions: `sitedesign_list`

### newsletter

`extension/cjw_newsletter/modules/newsletter` — policies: `subscribe`, `configure`, `unsubscribe`, `subscription_list_csvimport`, `subscription_list_csvimport_import`, `subscription_list_csvexport`, `subscription_list`, `subscription_view`, `user_list`, `user_view`, `user_remove`, `user_edit`, `user_create`, `preview`, `archive`, `index`, `settings`, `send`, `mailbox_item_list`, `mailbox_item_view`, `mailbox_list`, `mailbox_edit`, `blacklist_item`, `import_list`, `import_view`, `admin`

| View | Needs | Parameters |
| --- | --- | --- |
| `newsletter/index` | `index` | 0 |
| `newsletter/settings` | `settings` | 0 |
| `newsletter/mailbox_item_list` | `mailbox_item_list` | 0 |
| `newsletter/mailbox_list` | `mailbox_list` | 0 |
| `newsletter/mailbox_edit` | `mailbox_edit` | 1 |
| `newsletter/mailbox_item_view` | `mailbox_item_view` | 1 |
| `newsletter/blacklist_item_list` | `blacklist_item` | 0 |
| `newsletter/blacklist_item_add` | `blacklist_item` | 0 |
| `newsletter/blacklist_item_remove` | `blacklist_item` | 0 |
| `newsletter/import_list` | `import_list` | 0 |
| `newsletter/import_view` | `import_view` | 1 |
| `newsletter/user_list` | `user_list` | 0 |
| `newsletter/user_view` | `user_view` | 1 |
| `newsletter/user_remove` | `user_remove` | 1 |
| `newsletter/user_edit` | `user_edit` | 1 |
| `newsletter/user_create` | `user_create`, `user_edit` | 0 |
| `newsletter/subscription_list` | `subscription_list` | 1 |
| `newsletter/subscription_view` | `subscription_view` | 1 |
| `newsletter/subscription_list_csvimport` | `subscription_list_csvimport` | 2 |
| `newsletter/subscription_list_csvexport` | `subscription_list_csvexport` | 1 |
| `newsletter/subscribe` | `subscribe` | 0 |
| `newsletter/subscribe_infomail` | `subscribe` | 0 |
| `newsletter/configure` | `configure` | 2 |
| `newsletter/unsubscribe` | `unsubscribe` | 1 |
| `newsletter/preview` | `preview` | 5 |
| `newsletter/preview_archive` | `preview` | 3 |
| `newsletter/send` | `send` | 1 |
| `newsletter/send_abort` | `send` | 1 |
| `newsletter/archive` | `archive` | 3 |

Fetch functions: `subscription_list`, `subscription_list_count`, `import_subscription_list`, `import_subscription_list_count`, `user_list`, `user_list_count`, `edition_send_item_list`, `edition_send_item_list_count`

### notification

`kernel/notification` — policies: `use`, `administrate`

| View | Needs | Parameters |
| --- | --- | --- |
| `notification/settings` | `use` | 0 + 1 named |
| `notification/runfilter` | `administrate` | 0 |
| `notification/addtonotification` | `use` | 1 |

Fetch functions: `handler_list`, `digest_handlers`, `digest_items`, `event_content`, `subscribed_nodes`, `subscribed_nodes_count`

### oauth

`kernel/private/modules/oauth`

| View | Needs | Parameters |
| --- | --- | --- |
| `oauth/authorize` | *nothing* | 0 |

### oauthadmin

`kernel/private/modules/oauthadmin`

| View | Needs | Parameters |
| --- | --- | --- |
| `oauthadmin/list` | *nothing* | 0 |
| `oauthadmin/edit` | *nothing* | 1 |
| `oauthadmin/action` | *nothing* | 0 |
| `oauthadmin/view` | *nothing* | 1 |

### owner

`extension/ezownerchange/modules/owner` — policies: `change`

| View | Needs | Parameters |
| --- | --- | --- |
| `owner/change` | *nothing* | 1 + 1 named |

### package

`kernel/package` — policies: `read`, `list`, `create`, `edit`, `remove`, `install`, `import`, `export`

| View | Needs | Parameters |
| --- | --- | --- |
| `package/list` | `list` | 1 + 1 named |
| `package/upload` | `import` | 0 |
| `package/create` | `create` | 0 |
| `package/export` | `export` | 1 |
| `package/view` | `read` | 3 |
| `package/install` | `install` | 1 |
| `package/uninstall` | `install` | 1 |

Fetch functions: `list`, `maintainer_role_list`, `can_create`, `can_edit`, `can_import`, `can_install`, `can_export`, `can_read`, `can_list`, `can_remove`, `item`, `dependent_list`, `repository_list`

### paypal

`extension/ezpaypal/modules/paypal`

| View | Needs | Parameters |
| --- | --- | --- |
| `paypal/notify_url` | *nothing* | 0 |

### pdf

`kernel/pdf` — policies: `create`, `edit`

| View | Needs | Parameters |
| --- | --- | --- |
| `pdf/edit` | `edit` | 2 + 1 named |
| `pdf/list` | `edit` | 0 + 1 named |

### pm

`extension/ezpm/modules/pm` — policies: `list`, `blacklist`, `contacts`, `send_message`, `add_contact`, `stats`

| View | Needs | Parameters |
| --- | --- | --- |
| `pm/message` | *nothing* | 1 |
| `pm/list` | *nothing* | 0 |
| `pm/list_inbox` | *nothing* | 0 |
| `pm/list_sent` | *nothing* | 0 |
| `pm/list_drafts` | *nothing* | 0 |
| `pm/create` | *nothing* | 0 |
| `pm/reply` | *nothing* | 1 |
| `pm/edit` | *nothing* | 1 |
| `pm/add` | *nothing* | 0 |
| `pm/action` | *nothing* | 0 |
| `pm/blacklist` | *nothing* | 0 |
| `pm/contacts` | *nothing* | 0 |

Fetch functions: `view_message`, `list`, `messages_stats`, `check_contact`

### powercontent

`extension/powercontent/modules/powercontent`

| View | Needs | Parameters |
| --- | --- | --- |
| `powercontent/action` | *nothing* | 0 |

### recaptcha

`extension/recaptcha/modules/recaptcha` — policies: `bypass_captcha`

### refund

`extension/ezauthorize/modules/refund`

| View | Needs | Parameters |
| --- | --- | --- |
| `refund/refund` | *nothing* | 0 + 1 named |

### role

`kernel/role`

| View | Needs | Parameters |
| --- | --- | --- |
| `role/list` | *nothing* | 0 + 1 named |
| `role/edit` | *nothing* | 1 |
| `role/copy` | *nothing* | 1 |
| `role/policyedit` | *nothing* | 1 |
| `role/view` | *nothing* | 1 |
| `role/assign` | *nothing* | 3 |

Fetch functions: `role`

### rss

`kernel/rss` — policies: `feed`, `edit`

| View | Needs | Parameters |
| --- | --- | --- |
| `rss/list` | `edit` | 0 + 8 named |
| `rss/edit_export` | `edit` | 3 |
| `rss/edit_import` | `edit` | 2 |
| `rss/feed` | `feed` | 1 |

Fetch functions: `has_export_by_node`, `export_by_node`

### search

`kernel/search`

| View | Needs | Parameters |
| --- | --- | --- |
| `search/stats` | *nothing* | 0 + 1 named |

Fetch functions: `list_count`, `list`

### section

`kernel/section` — policies: `assign`, `edit`, `view`

| View | Needs | Parameters |
| --- | --- | --- |
| `section/list` | `view or edit or assign` | 0 + 1 named |
| `section/view` | `view or assign` | 1 + 1 named |
| `section/edit` | `edit` | 1 |
| `section/assign` | `assign` | 1 |

Fetch functions: `object`, `list`, `object_list`, `object_list_count`, `roles`, `user_roles`

### settings

`kernel/settings`

| View | Needs | Parameters |
| --- | --- | --- |
| `settings/view` | *nothing* | 2 |
| `settings/edit` | *nothing* | 5 |

### setup

`kernel/setup` — policies: `administrate`, `install`, `managecache`, `managecronjobs`, `preload`, `setup`, `system_info`

| View | Needs | Parameters |
| --- | --- | --- |
| `setup/init` | `install` | 0 |
| `setup/cache` | `managecache` | 0 |
| `setup/cachetoolbar` | `managecache` | 0 |
| `setup/settingstoolbar` | `setup` | 0 |
| `setup/session` | `administrate` | 1 |
| `setup/info` | `system_info` | 1 |
| `setup/rad` | `setup` | 0 + 1 named |
| `setup/radsurvey` | `setup` | 0 + 4 named |
| `setup/settingsextension` | `setup` | 0 |
| `setup/contentextension` | `setup` | 0 |
| `setup/modulewizard` | `setup` | 0 |
| `setup/designextension` | `setup` | 0 |
| `setup/handlerextension` | `setup` | 1 |
| `setup/workflowevent` | `setup` | 0 |
| `setup/moduleextension` | `setup` | 0 |
| `setup/datatype` | `setup` | 0 |
| `setup/templateoperator` | `setup` | 0 |
| `setup/extensions` | `setup` | 2 |
| `setup/menu` | `setup` | 0 |
| `setup/preload` | `preload` | 0 |
| `setup/preloadstream` | `preload` | 0 + 2 named |
| `setup/cronjobs` | `managecronjobs` | 0 |
| `setup/cronjobsstream` | `managecronjobs` | 0 + 1 named |
| `setup/staticcachestream` | `managecache` | 0 + 4 named |
| `setup/systemupgrade` | `setup` | 0 |
| `setup/toolbarlist` | `setup` | 1 |
| `setup/toolbar` | `setup` | 2 |
| `setup/menuconfig` | `setup` | 0 |
| `setup/templatelist` | `setup` | 0 + 1 named |
| `setup/templateview` | `setup` | 0 |
| `setup/templateedit` | `setup` | 0 |
| `setup/templatecreate` | `setup` | 0 |

Fetch functions: `version`, `alias`, `major_version`, `minor_version`, `release`, `state`, `is_development`, `database_version`, `database_release`, `edition`

### shop

`kernel/shop` — policies: `setup`, `administrate`, `buy`, `edit_status`, `setstatus`

| View | Needs | Parameters |
| --- | --- | --- |
| `shop/add` | `buy` | 2 |
| `shop/orderview` | `buy` | 1 |
| `shop/updatebasket` | `buy` | 0 |
| `shop/basket` | `buy` | 0 + 1 named |
| `shop/register` | `buy` | 0 |
| `shop/userregister` | `buy` | 0 |
| `shop/wishlist` | `buy` | 0 + 1 named |
| `shop/orderlist` | `administrate` | 0 + 1 named |
| `shop/archivelist` | `administrate` | 0 + 1 named |
| `shop/removeorder` | `administrate` | 0 |
| `shop/archiveorder` | `administrate` | 0 |
| `shop/unarchiveorder` | `administrate` | 0 |
| `shop/customerlist` | `administrate` | 0 + 1 named |
| `shop/customerorderview` | `administrate` | 2 |
| `shop/statistics` | `administrate` | 2 |
| `shop/confirmorder` | `buy` | 0 |
| `shop/checkout` | `buy` | 0 |
| `shop/vattype` | `setup` | 0 |
| `shop/vatrules` | `setup` | 0 |
| `shop/editvatrule` | `setup` | 1 + 1 named |
| `shop/productcategories` | `setup` | 0 |
| `shop/discountgroup` | `setup` | 0 |
| `shop/discountgroupedit` | `setup` | 1 |
| `shop/discountruleedit` | `setup` | 2 |
| `shop/discountgroupview` | `setup` | 1 |
| `shop/status` | `edit_status` | 0 |
| `shop/setstatus` | `setstatus` | 0 |
| `shop/currencylist` | `setup` | 0 + 1 named |
| `shop/editcurrency` | `setup` | 0 + 1 named |
| `shop/preferredcurrency` | `buy` | 0 |
| `shop/productsoverview` | `administrate` | 0 + 2 named |
| `shop/setpreferredcurrency` | `buy` | 0 + 1 named |
| `shop/setusercountry` | `buy` | 0 + 1 named |

Fetch functions: `basket`, `best_sell_list`, `related_purchase`, `wish_list`, `wish_list_count`, `current_wish_list`, `order`, `order_status_history_count`, `order_status_history`, `currency_list`, `currency`, `preferred_currency_code`, `user_country`, `product_category_list`, `product_category`

### sitemaps

`extension/xrowmetadata/modules/sitemaps`

| View | Needs | Parameters |
| --- | --- | --- |
| `sitemaps/index` | *nothing* | 0 |
| `sitemaps/robots` | *nothing* | 0 |

### state

`kernel/state` — policies: `administrate`, `assign`

| View | Needs | Parameters |
| --- | --- | --- |
| `state/assign` | `assign` | 2 |
| `state/groups` | `administrate` | 0 + 1 named |
| `state/group` | `administrate` | 2 |
| `state/group_edit` | `administrate` | 1 |
| `state/view` | `administrate` | 3 |
| `state/edit` | `administrate` | 2 |

### switchlanguage

`kernel/private/modules/switchlanguage`

| View | Needs | Parameters |
| --- | --- | --- |
| `switchlanguage/to` | *nothing* | 1 |

Fetch functions: `url_alias`

### syndication

`extension/syndication/modules/syndication` — policies: `import_object_status`, `view_export`, `edit_export`, `remove_feed`, `create_feed`, `menu`, `view_import`, `edit_import`, `view_export_info`, `create_import`, `fetch_feed`

| View | Needs | Parameters |
| --- | --- | --- |
| `syndication/menu` | `menu` | 0 |
| `syndication/list` | `view_export` | 0 + 1 named |
| `syndication/import_list` | `view_export` | 0 + 1 named |
| `syndication/import_edit` | `edit_import` | 1 + 1 named |
| `syndication/pending_edit` | `import_object_status` | 1 + 2 named |
| `syndication/edit` | `edit_export` | 1 |
| `syndication/add_feed_source` | `edit_export` | 2 + 1 named |
| `syndication/list_source_filter` | `edit_export` | 1 |
| `syndication/edit_source_filter` | `edit_export` | 1 |
| `syndication/edit_import_filter` | `edit_import` | 1 |
| `syndication/import_info` | `import_view` | 1 |
| `syndication/feed_info` | `view_export_info` | 1 |

### tags

`extension/eztags/modules/tags` — policies: `read`, `dashboard`, `id`, `view`, `add`, `addsynonym`, `edit`, `editsynonym`, `delete`, `deletesynonym`, `makesynonym`, `merge`, `search`

| View | Needs | Parameters |
| --- | --- | --- |
| `tags/treemenu` | `read` | 4 |
| `tags/dashboard` | `dashboard` | 0 + 2 named |
| `tags/id` | `id` | 2 + 2 named |
| `tags/list_objects` | `id` | 2 + 1 named |
| `tags/view` | `view` | 1 + 1 named |
| `tags/add` | `add` | 2 |
| `tags/addsynonym` | `addsynonym` | 2 |
| `tags/edit` | `edit` | 2 |
| `tags/movetags` | `edit` | 0 |
| `tags/translation` | `edit` | 0 |
| `tags/editsynonym` | `editsynonym` | 2 |
| `tags/delete` | `delete` | 1 |
| `tags/deletetags` | `delete` | 0 |
| `tags/deletesynonym` | `deletesynonym` | 1 |
| `tags/makesynonym` | `makesynonym` | 1 |
| `tags/merge` | `merge` | 1 |
| `tags/search` | `search` | 0 + 1 named |

Fetch functions: `tag`, `tags_by_keyword`, `tag_by_remote_id`, `tag_by_url`, `list`, `list_count`, `tree`, `tree_count`, `latest_tags`

### trigger

`kernel/trigger`

| View | Needs | Parameters |
| --- | --- | --- |
| `trigger/list` | *nothing* | 0 |

### update

`extension/ezupdate/modules/update` — policies: `ezupdate`, `dump`

| View | Needs | Parameters |
| --- | --- | --- |
| `update/dashboard` | `ezupdate` | 0 |
| `update/show` | `ezupdate` | 1 |

### url

`kernel/url`

| View | Needs | Parameters |
| --- | --- | --- |
| `url/list` | *nothing* | 1 + 1 named |
| `url/view` | *nothing* | 1 + 1 named |
| `url/edit` | *nothing* | 1 |

Fetch functions: `list`, `list_count`

### user

`kernel/user` — policies: `login`, `password`, `preferences`, `register`, `selfedit`, `activation`

| View | Needs | Parameters |
| --- | --- | --- |
| `user/logout` | `login` | 0 |
| `user/login` | `login` | 0 |
| `user/setting` | `preferences` | 1 |
| `user/preferences` | `login` | 3 |
| `user/password` | `password` | 1 |
| `user/forgotpassword` | `password` | 1 |
| `user/edit` | `login` | 1 |
| `user/register` | `register` | 1 |
| `user/activate` | `login` | 2 |
| `user/success` | `register` | 0 |
| `user/unactivated` | `activation` | 2 + 1 named |

Fetch functions: `current_user`, `is_logged_in`, `logged_in_count`, `anonymous_count`, `logged_in_list`, `logged_in_users`, `user_role`, `member_of`, `has_access_to`

### userpaex

`extension/ezmbpaex/modules/userpaex` — policies: `password`, `editpaex`

| View | Needs | Parameters |
| --- | --- | --- |
| `userpaex/password` | `password` | 1 |
| `userpaex/forgotpassword` | `password` | 1 |

### visual

`kernel/visual`

| View | Needs | Parameters |
| --- | --- | --- |
| `visual/toolbarlist` | *nothing* | 1 |
| `visual/toolbar` | *nothing* | 2 |
| `visual/menuconfig` | *nothing* | 0 |
| `visual/templatelist` | *nothing* | 0 + 1 named |
| `visual/templateview` | *nothing* | 0 |
| `visual/templateedit` | *nothing* | 0 + 1 named |
| `visual/templatecreate` | *nothing* | 0 + 4 named |

### websitetoolbar

`extension/ezwt/modules/websitetoolbar` — policies: `use`

| View | Needs | Parameters |
| --- | --- | --- |
| `websitetoolbar/sort` | `use` | 1 + 5 named |

### workflow

`kernel/workflow`

| View | Needs | Parameters |
| --- | --- | --- |
| `workflow/view` | *nothing* | 1 |
| `workflow/edit` | *nothing* | 3 |
| `workflow/groupedit` | *nothing* | 1 |
| `workflow/down` | *nothing* | 2 |
| `workflow/up` | *nothing* | 2 |
| `workflow/workflowlist` | *nothing* | 1 |
| `workflow/grouplist` | *nothing* | 0 |
| `workflow/process` | *nothing* | 1 |
| `workflow/run` | *nothing* | 1 |
| `workflow/event` | *nothing* | 2 |
| `workflow/processlist` | *nothing* | 0 + 1 named |

Fetch functions: `workflow_statuses`, `workflow_type_statuses`

### xrowextract

`extension/xrowextract/modules/xrowextract` — policies: `csv`

| View | Needs | Parameters |
| --- | --- | --- |
| `xrowextract/csv` | `csv` | 0 |

## What a template can call

Every operator and function the engine has been taught, read out of the autoload
arrays where they are really declared - there is no ini listing them, which is why
an operator that is not found is so often looked for in the wrong place.

A row marked **not active** belongs to an extension that is not switched on here:
the name is declared and nothing answers to it.

### Operators (364)

| Name | Class | Declared in |
| --- | --- | --- |
| `abs` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `absolute_url` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `action_icon` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `add_view_parameters` | `SwarkAddViewParametersOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `addcslashes` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `addslashes` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `alphabet` | `eZAlphabetOperator` | `kernel/common/eztemplateautoload.php` |
| `and` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `app` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `append` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `array` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `array_append` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `array_merge` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `array_prepend` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `array_search` | `SwarkArraySearchOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `array_sum` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `arsort` | `SwarkARSortOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `asort` | `SwarkASortOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `asset` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `attribute` | `eZTemplateAttributeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `autolink` | `eZAutoLinkOperator` | `kernel/common/eztemplateautoload.php` |
| `bc_ga_formatNumericDecimal` | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php` |
| `bc_ga_jsEscapedString` | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php` |
| `bc_ga_urchin` | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php` |
| `bc_ga_urchinOrder` | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php` |
| `bc_ga_xmlAttributeValue` | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php` |
| `begins_with` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `bin2hex` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `break` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ceil` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `charset` | `SwarkCharsetOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `choose` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `chr` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `chunk_split` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `cjw_newsletter_preg_replace` | `CjwNewsletterOperators` | `extension/cjw_newsletter/autoloads/cjwnewsletteroperators.php` |
| `cjw_newsletter_str_replace` | `CjwNewsletterOperators` | `extension/cjw_newsletter/autoloads/cjwnewsletteroperators.php` |
| `cjw_newsletter_variable` | `CjwNewsletterOperators` | `extension/cjw_newsletter/autoloads/cjwnewsletteroperators.php` |
| `class_icon` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `classgroup_icon` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `clear_object_cache` | `SwarkClearObjectCacheOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `compare` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `component_content` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `concat` | `eZTemplateTextOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `cond` | `eZTemplateControlOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `contains` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `content_link` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `content_structure_tree` | `eZContentStructureTreeOperator` | `kernel/common/eztemplateautoload.php` |
| `content_tags` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `controller` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `convert_uudecode` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `convert_uuencode` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `cookie` | `SwarkCookieOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `count` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `count_chars` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `count_words` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `crc32` | `eZTemplateDigestOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `current_layout` | `SwarkCurrentLayoutOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `current_siteaccess` | `SwarkCurrentSiteaccessOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `currentdate` | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `d18n` | `eZi18nOperator` | `kernel/common/eztemplateautoload.php` |
| `datetime` | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `debug` | `SwarkDebugOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `debug_attributes` | `SwarkDebugAttributesOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `dec` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `div` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `downcase` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `dump` | `eZTemplateAttributeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `embed_image` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ends_with` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `enhanced_link` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `eq` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ereg_replace` | `PHPFunctionOperator` | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php` |
| `expinfo` | `eZTemplateExpInfoOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `expl_first` | `ExplBlockOperator` | `extension/explayouts/classes/explblockoperator.php` |
| `expl_has` | `ExplBlockOperator` | `extension/explayouts/classes/explblockoperator.php` |
| `expl_parse` | `ExplBlockOperator` | `extension/explayouts/classes/explblockoperator.php` |
| `expl_strip` | `ExplBlockOperator` | `extension/explayouts/classes/explblockoperator.php` |
| `explode` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `extract` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `extract_left` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `extract_right` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `exturl` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezarchive` | `eZArchive` | `extension/ezdemo/autoloads/ezarchive.php` |
| `ezarchive` | `eZArchive` | `extension/ezwebin/autoloads/ezarchive.php` |
| `ezcreateclasslistgroups` | `eZCreateClassListGroups` | `extension/ezwt/autoloads/ezcreateclasslistgroups.php` |
| `ezcss` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezcss_load` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezcss_require` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezcssfiles` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezdesign` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezhttp` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezhttp_hasvariable` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezimage` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezini` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezini_hasvariable` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezkeywordlist` | `eZKeywordList` | `extension/ezdemo/autoloads/ezkeywordlist.php` |
| `ezkeywordlist` | `eZKeywordList` | `extension/ezwebin/autoloads/ezkeywordlist.php` |
| `ezmodule` | `eZModuleOperator` | `kernel/common/eztemplateautoload.php` |
| `ezoe_ini_section` | `eZOETemplateUtils` | `extension/ezoe/autoloads/ezoetemplateutils.php` |
| `ezpackage` | `eZPackageOperator` | `kernel/common/eztemplateautoload.php` |
| `ezpagedata` | `eZPageData` | `extension/ezdemo/autoloads/ezpagedata.php` |
| `ezpagedata` | `eZPageData` | `extension/ezwebin/autoloads/ezpagedata.php` |
| `ezpagedata_append` | `eZPageData` | `extension/ezdemo/autoloads/ezpagedata.php` |
| `ezpagedata_append` | `eZPageData` | `extension/ezwebin/autoloads/ezpagedata.php` |
| `ezpagedata_set` | `eZPageData` | `extension/ezdemo/autoloads/ezpagedata.php` |
| `ezpagedata_set` | `eZPageData` | `extension/ezwebin/autoloads/ezpagedata.php` |
| `ezpreference` | `eZKernelOperator` | `kernel/common/eztemplateautoload.php` |
| `ezroot` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `ezscript` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezscript_load` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezscript_require` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezscriptfiles` | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php` |
| `ezstr_replace` *(not active)* | `MyStrReplaceOperator` | `extension/str_replace/autoloads/str_replace_controloperator.php` |
| `ezsys` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `eztagcloud` | `eZTagCloud` | `extension/ezdemo/autoloads/eztagcloud.php` |
| `eztagcloud` | `eZTagCloud` | `extension/ezwebin/autoloads/eztagcloud.php` |
| `eztags_parent_string` | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztagstemplatefunctions.php` |
| `eztagscloud` | `eZTagsCloud` | `extension/eztags/autoloads/eztagscloud.php` |
| `eztoc` | `eZTOCOperator` | `kernel/common/eztemplateautoload.php` |
| `ezurl` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `false` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `feedreader` *(not active)* | `eZFeedReader` | `extension/ezflow/autoloads/ezfeedreader.php` |
| `fetch` | `eZTemplateExecuteOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `fetch_alias` | `eZTemplateExecuteOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `fetch_by_starrating` | `ezsrTemplateOperators` | `extension/ezstarrating/autoloads/ezsrtemplateoperators.php` |
| `fetch_starrating_data` | `ezsrTemplateOperators` | `extension/ezstarrating/autoloads/ezsrtemplateoperators.php` |
| `fetch_starrating_stats` | `ezsrTemplateOperators` | `extension/ezstarrating/autoloads/ezsrtemplateoperators.php` |
| `fieldRelation` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `fieldRelations` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `fieldValue` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `file_get_contents` | `PHPFunctionOperator` | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php` |
| `filterChildren` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `filterFieldRelationLocations` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `filterFieldRelations` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `firstNonEmptyField` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `first_set` | `eZTemplateControlOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `flag_icon` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `float` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `floor` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ge` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `getParameter` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `get_class` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `get_netgen_open_graph` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `get_type` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `getdate` | `PHPFunctionOperator` | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php` |
| `gettime` | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `gt` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `hasField` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `hasParameter` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `has_access_to_limitation` | `ezjscAccessTemplateFunctions` | `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php` |
| `hash` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `haveToPaginate` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `hcaptcha_get_html` *(not active)* | `hCaptchaTemplateOperator` | `extension/hcaptcha/autoloads/hcaptchatemplateoperator.php` |
| `hebrev` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `hex2bin` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `html_entity_decode` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `htmlentities` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `htmlspecialchars_decode` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `i18n` | `eZi18nOperator` | `kernel/common/eztemplateautoload.php` |
| `ibexa` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ibexa_path` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ibexa_url` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `icon` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `icon_info` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `image` | `eZTemplateImageOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `image` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `image_link` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `imagefile` | `eZTemplateImageOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `implode` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `inc` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `indent` | `eZTemplateTextOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `insert` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `installation_name` | `ExpInstallationOperator` | `kernel/common/eztemplateautoload.php` |
| `int` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `intro` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `is_array` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_boolean` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_class` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_float` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_integer` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_null` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_numeric` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_object` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_post_request` | `SwarkIsPostRequestOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `is_production_system` | `ExpInstallationOperator` | `kernel/common/eztemplateautoload.php` |
| `is_set` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_string` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `is_unset` | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `item_content_link` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `item_image_link` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `item_params` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `item_view_template` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `json` *(not active)* | `eZJSON` | `extension/ezflow/autoloads/ezjson.php` |
| `json_encode` | `ezjscEncodingTemplateFunctions` | `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php` |
| `json_encode` | `SwarkJSONEncodeOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `krsort` | `SwarkKRSortOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `ksort` | `SwarkKSortOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `l10n` | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `latest_tags` | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztagstemplatefunctions.php` |
| `layout_title` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `lcfirst` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `le` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `levenshtein` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `locale` | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `lt` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ltrim` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ltrim` | `SwarkLTrimOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `makedate` | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `maketime` | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `max` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `md5` | `eZTemplateDigestOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `merge` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `metadata` | `xrowMetaDataOperator` | `extension/xrowmetadata/autoloads/xrowmetadataoperator.php` |
| `metaphone` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `mimetype_icon` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `min` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `mktime` | `PHPFunctionOperator` | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php` |
| `mod` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `modify_view_parameter` | `SwarkModifyViewParameterOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `module_params` | `eZModuleParamsOperator` | `kernel/common/eztemplateautoload.php` |
| `month_overview` | `eZDateOperatorCollection` | `kernel/common/eztemplateautoload.php` |
| `mul` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ne` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ng_image_alias` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ng_query` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ng_render_field` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ng_view_content` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `nglayouts_render_result` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `nglayouts_render_zone` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ngsite` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ngsite_group_fields` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ngsite_language_name` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `ngsite_topic_path` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `nl2br` | `eZTemplateNl2BrOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `node_encode` | `ezjscEncodingTemplateFunctions` | `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php` |
| `not` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `null` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `number_format` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `or` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ord` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `pad` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `pagelink` *(not active)* | `eZPageLink` | `extension/ezflow/autoloads/ezpagelink.php` |
| `pagerfanta` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `parameter` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `parent` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `parsexml` | `TemplateParseXMLOperator` | `extension/enhancedezbinaryfile/autoloads/templateparsexmloperator.php` |
| `path` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `pdf` | `eZPDF` | `lib/ezpdf/classes/eztemplateautoload.php` |
| `player` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `player_slide` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `poster` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `poster_slide` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `preg_match` | `SwarkPregMatchOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `preg_replace` | `SwarkPregReplaceOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `prepend` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `quoted_printable_decode` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `quoted_printable_encode` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `quotemeta` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `rand` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `range` | `SwarkRangeOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `recaptcha_get_html` | `reCAPTCHATemplateOperator` | `extension/recaptcha/autoloads/recaptchatemplateoperator.php` |
| `recipe_schema` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `red5list` *(not active)* | `eZRed5StreamListOperator` | `extension/ezflow/autoloads/ezred5streamlist.php` |
| `redirect` | `SwarkRedirectOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `redirect_to_site_root` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `remove` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `remove_array_element` | `SwarkRemoveArrayElementOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `render` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `render_esi` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `repeat` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `replace` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `return` | `SwarkReturnOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `reverse` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ristring` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `roman` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `rot13` | `eZTemplateDigestOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `round` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `rsort` | `SwarkRSortOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `rstring` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `rtrim` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `rtrim` | `SwarkRTrimOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `saveXML` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `server` | `SwarkServerOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `set_array_element` | `SwarkSetArrayElementOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `shorten` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `shortenw` | `SwarkShortenWOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `shuffle` | `SwarkShuffleOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `si` | `eZTemplateUnitOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `similar_text` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `simpletags` | `eZSimpleTagsOperator` | `kernel/common/eztemplateautoload.php` |
| `simplify` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `sort` | `SwarkSortOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `soundex` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `split_by_length` | `SwarkSplitByLengthOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `sprintf` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `str_contains` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `str_ends_with` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `str_getcsv` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `str_replace` | `PHPFunctionOperator` | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php` |
| `str_replace` | `SwarkStrReplaceOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `str_rot13` | `PHPFunctionOperator` | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php` |
| `str_shuffle` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `str_split` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `str_starts_with` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `str_word_count` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strcasecmp` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strcmp` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strcoll` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strcspn` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strip_tags` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `stripcslashes` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `stripos` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `stripslashes` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `stristr` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strlen` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strnatcasecmp` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strnatcmp` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strncasecmp` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strncmp` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strpbrk` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strpos` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strpos` | `SwarkStrPosOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `strrchr` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strripos` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strrpos` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strrpos` | `SwarkStrRPosOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `strspn` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strstr` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strtok` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `strtr` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `sub` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `substr` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `substr` | `SwarkSubStrOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `substr_compare` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `substr_count` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `substr_replace` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `sum` | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `tag_icon` | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztagstemplatefunctions.php` |
| `tag_url` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `texttoimage` | `eZTemplateImageOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `time` | `PHPFunctionOperator` | `extension/owsimpleoperator/autoloads/phpfunctionoperator.php` |
| `title` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `topmenu` | `eZTopMenuOperator` | `kernel/common/eztemplateautoload.php` |
| `tpl_block_template` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `trans` | `sevenxThemesMediaOperators` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` |
| `treemenu` | `eZTreeMenuOperator` | `kernel/common/eztemplateautoload.php` |
| `trim` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `trim` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `true` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `unique` | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `unserialize` | `eZUnserialize` | `extension/ezflow/autoloads/ezunserialize.php` |
| `upcase` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `upfirst` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `upword` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `uri_path_segment` | `SwarkURIPathSegmentOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `user_id_by_login` | `SwarkUserIDByLoginOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `user_limitations` | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztagstemplatefunctions.php` |
| `variable_names` | `SwarkVariableNamesOperator` | `extension/swark/autoloads/eztemplateautoload.php` |
| `vsprintf` | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `wash` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `wordtoimage` | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php` |
| `wrap` | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `x18n` | `eZi18nOperator` | `kernel/common/eztemplateautoload.php` |
| `xml_encode` | `ezjscEncodingTemplateFunctions` | `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php` |

### Functions (50)

| Name | Class | Declared in |
| --- | --- | --- |
| `append-block` | `eZTemplateBlockFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `attribute_diff_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `attribute_edit_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `attribute_pdf_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `attribute_result_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `attribute_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `block_edit_gui` | `eZPageForwardInit` | `extension/ezflow/autoloads/eztemplateautoload.php` |
| `block_view_gui` | `eZPageForwardInit` | `extension/ezflow/autoloads/eztemplateautoload.php` |
| `cache-block` | `eZTemplateCacheFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `class_attribute_edit_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `class_attribute_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `collaboration_icon` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `collaboration_participation_view` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `collaboration_simple_message_view` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `collaboration_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `content_pdf_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `content_version_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `content_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `debug-accumulator` | `eZTemplateDebugFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `debug-log` | `eZTemplateDebugFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `debug-timing-point` | `eZTemplateDebugFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `debug-trace` | `eZTemplateDebugFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `def` | `eZTemplateDefFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `default` | `eZTemplateSetFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `do` | `eZTemplateDoFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `event_edit_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `event_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `explblock` | `ExplBlockFunction` | `extension/explayouts/classes/explblockfunction.php` |
| `for` | `eZTemplateForFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `foreach` | `eZTemplateForeachFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `if` | `eZTemplateIfFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `include` | `eZTemplateIncludeFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `ldelim` | `eZTemplateDelimitFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `let` | `eZTemplateSetFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `menu` | `eZTemplateMenuFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `node_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `powercontent_attribute_create_gui` | `eZPowercontentForwardInit` | `extension/powercontent/autoloads/eztemplateautoload.php` |
| `powercontent_create_gui` | `eZPowercontentForwardInit` | `extension/powercontent/autoloads/eztemplateautoload.php` |
| `rdelim` | `eZTemplateDelimitFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `related_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `run-once` | `eZTemplateBlockFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `section` | `eZTemplateSectionFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `sequence` | `eZTemplateSequenceFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `set` | `eZTemplateSetFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `set-block` | `eZTemplateBlockFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `shop_account_view_gui` | `eZObjectForwardInit` | `kernel/common/eztemplateautoload.php` |
| `switch` | `eZTemplateSwitchFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `tool_bar` | `eZTemplateToolbarFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `undef` | `eZTemplateDefFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `while` | `eZTemplateWhileFunction` | `lib/eztemplate/classes/eztemplateautoload.php` |

## Events something can listen to

Registered as `site.ini [Event] Listeners[]=<event>@<Class>::<method>`. The
lightest way there is to add behaviour: no module, no handler, no class to
replace, just a static method that runs when something happens.

A **filter** event uses what the listener returns, so one that forgets to return
the value destroys it. A **notify** event ignores it.

| Event | Kind | Announced in |
| --- | --- | --- |
| `content/cache` | filter | `kernel/classes/ezcontentcachemanager.php`, `kernel/content/ezcontentoperationcollection.php`, `kernel/content/urlalias.php` |
| `content/cache/all` | notify | `kernel/classes/ezcache.php`, `kernel/classes/ezcontentcachemanager.php` |
| `content/cache/version` | notify | `kernel/content/attribute_edit.php`, `extension/ezautosave/classes/ezjscserverfunctionsautosave.php`, `extension/nxc_powercontent/modules/content/attribute_edit.php` |
| `content/class/cache` | notify | `kernel/class/delete.php`, `kernel/class/edit.php`, `kernel/class/removeclass.php` |
| `content/class/cache/all` | notify | `kernel/classes/ezcache.php` |
| `content/class/group/cache` | notify | `kernel/class/groupedit.php`, `kernel/class/removegroup.php` |
| `content/download` | notify | `kernel/content/download.php`, `extension/nxc_powercontent/modules/content/download.php` |
| `content/section/cache` | notify | `kernel/section/edit.php`, `kernel/section/list.php` |
| `content/state/assign` | notify | `kernel/content/ezcontentoperationcollection.php`, `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` |
| `content/state/cache` | notify | `kernel/state/edit.php` |
| `content/state/cache/all` | notify | `kernel/classes/ezcache.php` |
| `content/state/group/cache` | notify | `kernel/state/group_edit.php`, `kernel/state/groups.php` |
| `content/translations/cache` | notify | `kernel/content/translations.php`, `extension/nxc_powercontent/modules/content/translations.php` |
| `content/view` | filter | `kernel/content/view.php`, `extension/nxc_powercontent/modules/content/view.php` |
| `image/alias` | notify | `lib/ezimage/classes/ezimagemanager.php` |
| `image/invalidateAliases` | notify | `kernel/classes/ezcache.php` |
| `image/purgeAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php` |
| `image/removeAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php` |
| `image/trashAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagetype.php` |
| `request/input` | notify | `kernel/private/classes/ezpkernelweb.php` |
| `request/preinput` | notify | `kernel/private/classes/ezpkernelweb.php` |
| `response/output` | filter | `kernel/private/classes/global_functions.php`, `lib/ezutils/classes/ezdebug.php`, `extension/ezjscore/modules/ezjscore/run.php` |
| `response/preoutput` | filter | `kernel/private/classes/global_functions.php` |
| `session/cleanup` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php`, `lib/ezsession/classes/ezpsessionhandlerphp.php` |
| `session/destroy` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php`, `lib/ezsession/classes/ezpsessionhandlerphp.php`, `lib/ezsession/classes/ezpsessionhandlersymfony.php` |
| `session/gc` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php`, `lib/ezsession/classes/ezpsessionhandlerphp.php`, `lib/ezsession/classes/ezpsessionhandlersymfony.php` |
| `session/regenerate` | notify | `lib/ezsession/classes/ezpsessionhandlerdb.php`, `lib/ezsession/classes/ezpsessionhandlerphp.php`, `lib/ezsession/classes/ezpsessionhandlersymfony.php` |
| `tag/add` | filter | `extension/eztags/datatypes/eztags/eztags.php`, `extension/eztags/modules/tags/add.php`, `extension/eztags/modules/tags/addsynonym.php` |
| `tag/delete` | filter | `extension/eztags/modules/tags/delete.php`, `extension/eztags/modules/tags/deletesynonym.php`, `extension/eztags/modules/tags/deletetags.php` |
| `tag/edit` | filter | `extension/eztags/modules/tags/edit.php`, `extension/eztags/modules/tags/editsynonym.php`, `extension/eztags/modules/tags/movetags.php` |
| `tag/makesynonym` | filter | `extension/eztags/modules/tags/addsynonym.php`, `extension/eztags/modules/tags/makesynonym.php` |
| `tag/merge` | filter | `extension/eztags/modules/tags/merge.php` |
| `tag/transferobjects` | filter | `extension/eztags/modules/tags/deletesynonym.php` |
| `user/cache/all` | notify | `kernel/classes/ezcache.php` |

## Templates already replaced

Each is a place a template has already been overridden - something to learn from,
and something to collide with, since two overrides matching the same thing are
decided by load order rather than by intent.

| Name | Replaces | With |
| --- | --- | --- |
| `article` | `node/view/admin_preview.tpl` | `admin_preview/article.tpl` |
| `article_listitem` | `node/view/listitem.tpl` | `listitem/article.tpl` |
| `article_listitem` | `node/view/listitem.tpl` | `listitem/article.tpl` |
| `billboard_banner` | `content/view/billboard.tpl` | `billboard/banner.tpl` |
| `billboard_banner` | `content/view/billboard.tpl` | `billboard/banner.tpl` |
| `billboard_flash` | `content/view/billboard.tpl` | `billboard/flash.tpl` |
| `billboard_flash` | `content/view/billboard.tpl` | `billboard/flash.tpl` |
| `comment` | `node/view/admin_preview.tpl` | `admin_preview/comment.tpl` |
| `company` | `node/view/admin_preview.tpl` | `admin_preview/company.tpl` |
| `edit_comment` | `content/edit.tpl` | `edit/comment.tpl` |
| `edit_comment` | `content/edit.tpl` | `edit/comment.tpl` |
| `edit_ezsubtreesubscription_forum_topic` | `content/datatype/edit/ezsubtreesubscription.tpl` | `datatype/edit/forum_topic.tpl` |
| `edit_ezsubtreesubscription_forum_topic` | `content/datatype/edit/ezsubtreesubscription.tpl` | `datatype/edit/forum_topic.tpl` |
| `edit_file` | `content/edit.tpl` | `edit/file.tpl` |
| `edit_file` | `content/edit.tpl` | `edit/file.tpl` |
| `edit_forum_reply` | `content/edit.tpl` | `edit/forum_reply.tpl` |
| `edit_forum_reply` | `content/edit.tpl` | `edit/forum_reply.tpl` |
| `edit_forum_topic` | `content/edit.tpl` | `edit/forum_topic.tpl` |
| `edit_forum_topic` | `content/edit.tpl` | `edit/forum_topic.tpl` |
| `embed-inline_image` | `content/view/embed-inline.tpl` | `embed-inline_image.tpl` |
| `embed-inline_node_image` | `node/view/embed-inline.tpl` | `embed-inline_image.tpl` |
| `embed_article` | `content/view/embed.tpl` | `embed/article.tpl` |
| `embed_article` | `content/view/embed.tpl` | `embed/article.tpl` |
| `embed_banner` | `content/view/embed.tpl` | `embed/banner.tpl` |
| `embed_banner` | `content/view/embed.tpl` | `embed/banner.tpl` |
| `embed_event_calendar` | `content/view/embed.tpl` | `embed/event_calendar.tpl` |
| `embed_event_calendar` | `content/view/embed.tpl` | `embed/event_calendar.tpl` |
| `embed_file` | `content/view/embed.tpl` | `embed/file.tpl` |
| `embed_file` | `content/view/embed.tpl` | `embed/file.tpl` |
| `embed_flash` | `content/view/embed.tpl` | `embed/flash.tpl` |
| `embed_flash` | `content/view/embed.tpl` | `embed/flash.tpl` |
| `embed_folder` | `content/view/embed.tpl` | `embed/folder.tpl` |
| `embed_folder` | `content/view/embed.tpl` | `embed/folder.tpl` |
| `embed_forum` | `content/view/embed.tpl` | `embed/forum.tpl` |
| `embed_forum` | `content/view/embed.tpl` | `embed/forum.tpl` |
| `embed_gallery` | `content/view/embed.tpl` | `embed/gallery.tpl` |
| `embed_gallery` | `content/view/embed.tpl` | `embed/gallery.tpl` |
| `embed_horizontallylistedsubitems_article` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/article.tpl` |
| `embed_horizontallylistedsubitems_article` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/article.tpl` |
| `embed_horizontallylistedsubitems_event` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/event.tpl` |
| `embed_horizontallylistedsubitems_event` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/event.tpl` |
| `embed_horizontallylistedsubitems_image` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/image.tpl` |
| `embed_horizontallylistedsubitems_image` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/image.tpl` |
| `embed_horizontallylistedsubitems_product` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/product.tpl` |
| `embed_horizontallylistedsubitems_product` | `node/view/horizontallylistedsubitems.tpl` | `horizontallylistedsubitems/product.tpl` |
| `embed_image` | `content/view/embed.tpl` | `embed_image.tpl` |
| `embed_image` | `content/view/embed.tpl` | `embed/image.tpl` |
| `embed_image` | `content/view/embed.tpl` | `embed/image.tpl` |
| `embed_inline_image` | `content/view/embed-inline.tpl` | `embed-inline/image.tpl` |
| `embed_inline_image` | `content/view/embed-inline.tpl` | `embed-inline/image.tpl` |
| `embed_itemizedsubitems_documentation_page` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/documentation_page.tpl` |
| `embed_itemizedsubitems_documentation_page` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/documentation_page.tpl` |
| `embed_itemizedsubitems_event_calendar` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/event_calendar.tpl` |
| `embed_itemizedsubitems_event_calendar` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/event_calendar.tpl` |
| `embed_itemizedsubitems_folder` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/folder.tpl` |
| `embed_itemizedsubitems_folder` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/folder.tpl` |
| `embed_itemizedsubitems_forum` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/forum.tpl` |
| `embed_itemizedsubitems_forum` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/forum.tpl` |
| `embed_itemizedsubitems_gallery` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/gallery.tpl` |
| `embed_itemizedsubitems_gallery` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/gallery.tpl` |
| `embed_itemizedsubitems_itemized_sub_items` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/itemized_sub_items.tpl` |
| `embed_itemizedsubitems_itemized_sub_items` | `content/view/itemizedsubitems.tpl` | `itemizedsubitems/itemized_sub_items.tpl` |
| `embed_node_image` | `node/view/embed.tpl` | `embed_image.tpl` |
| `embed_poll` | `content/view/embed.tpl` | `embed/poll.tpl` |
| `embed_poll` | `content/view/embed.tpl` | `embed/poll.tpl` |
| `embed_product` | `content/view/embed.tpl` | `embed/product.tpl` |
| `embed_product` | `content/view/embed.tpl` | `embed/product.tpl` |
| `embed_quicktime` | `content/view/embed.tpl` | `embed/quicktime.tpl` |
| `embed_quicktime` | `content/view/embed.tpl` | `embed/quicktime.tpl` |
| `embed_real_video` | `content/view/embed.tpl` | `embed/real_video.tpl` |
| `embed_real_video` | `content/view/embed.tpl` | `embed/real_video.tpl` |
| `embed_windows_media` | `content/view/embed.tpl` | `embed/windows_media.tpl` |
| `embed_windows_media` | `content/view/embed.tpl` | `embed/windows_media.tpl` |
| `factbox` | `content/datatype/view/ezxmltags/factbox.tpl` | `datatype/ezxmltext/factbox.tpl` |
| `factbox` | `content/datatype/view/ezxmltags/factbox.tpl` | `datatype/ezxmltext/factbox.tpl` |
| `feedback_form` | `node/view/admin_preview.tpl` | `admin_preview/feedback_form.tpl` |
| `file` | `node/view/admin_preview.tpl` | `admin_preview/file.tpl` |
| `flash` | `node/view/admin_preview.tpl` | `admin_preview/flash.tpl` |
| `folder` | `node/view/admin_preview.tpl` | `admin_preview/folder.tpl` |
| `forum` | `node/view/admin_preview.tpl` | `admin_preview/forum.tpl` |
| `forum_reply` | `node/view/admin_preview.tpl` | `admin_preview/forum_reply.tpl` |
| `forum_topic` | `node/view/admin_preview.tpl` | `admin_preview/forum_topic.tpl` |
| `full_article` | `node/view/full.tpl` | `full/article.tpl` |
| `full_article` | `node/view/full.tpl` | `full/article.tpl` |
| `full_article_mainpage` | `node/view/full.tpl` | `full/article_mainpage.tpl` |
| `full_article_mainpage` | `node/view/full.tpl` | `full/article_mainpage.tpl` |
| `full_article_sevenx_themes_super` | `node/view/full.tpl` | `full/article.tpl` |
| `full_article_subpage` | `node/view/full.tpl` | `full/article_subpage.tpl` |
| `full_article_subpage` | `node/view/full.tpl` | `full/article_subpage.tpl` |
| `full_banner` | `node/view/full.tpl` | `full/banner.tpl` |
| `full_banner` | `node/view/full.tpl` | `full/banner.tpl` |
| `full_blog` | `node/view/full.tpl` | `full/blog.tpl` |
| `full_blog` | `node/view/full.tpl` | `full/blog.tpl` |
| `full_blog_post` | `node/view/full.tpl` | `full/blog_post.tpl` |
| `full_blog_post` | `node/view/full.tpl` | `full/blog_post.tpl` |
| `full_comment` | `node/view/full.tpl` | `full/comment.tpl` |
| `full_comment` | `node/view/full.tpl` | `full/comment.tpl` |
| `full_documentation_page` | `node/view/full.tpl` | `full/documentation_page.tpl` |
| `full_documentation_page` | `node/view/full.tpl` | `full/documentation_page.tpl` |
| `full_event` | `node/view/full.tpl` | `full/event.tpl` |
| `full_event` | `node/view/full.tpl` | `full/event.tpl` |
| `full_event_calendar` | `node/view/full.tpl` | `full/event_calendar.tpl` |
| `full_event_calendar` | `node/view/full.tpl` | `full/event_calendar.tpl` |
| `full_feedback_form` | `node/view/full.tpl` | `full/feedback_form.tpl` |
| `full_feedback_form` | `node/view/full.tpl` | `full/feedback_form.tpl` |
| `full_file` | `node/view/full.tpl` | `full/file.tpl` |
| `full_file` | `node/view/full.tpl` | `full/file.tpl` |
| `full_flash` | `node/view/full.tpl` | `full/flash.tpl` |
| `full_flash` | `node/view/full.tpl` | `full/flash.tpl` |
| `full_folder` | `node/view/full.tpl` | `full/folder.tpl` |
| `full_folder` | `node/view/full.tpl` | `full/folder.tpl` |
| `full_forum` | `node/view/full.tpl` | `full/forum.tpl` |
| `full_forum` | `node/view/full.tpl` | `full/forum.tpl` |
| `full_forum_reply` | `node/view/full.tpl` | `full/forum_reply.tpl` |
| `full_forum_reply` | `node/view/full.tpl` | `full/forum_reply.tpl` |
| `full_forum_topic` | `node/view/full.tpl` | `full/forum_topic.tpl` |
| `full_forum_topic` | `node/view/full.tpl` | `full/forum_topic.tpl` |
| `full_forums` | `node/view/full.tpl` | `full/forums.tpl` |
| `full_forums` | `node/view/full.tpl` | `full/forums.tpl` |
| `full_frontpage` | `node/view/full.tpl` | `full/frontpage.tpl` |
| `full_frontpage` | `node/view/full.tpl` | `full/frontpage.tpl` |
| `full_gallery` | `node/view/full.tpl` | `full/gallery.tpl` |
| `full_gallery` | `node/view/full.tpl` | `full/gallery.tpl` |
| `full_geo_article` | `node/view/full.tpl` | `full/geo_article.tpl` |
| `full_geo_article` | `node/view/full.tpl` | `full/geo_article.tpl` |
| `full_image` | `node/view/full.tpl` | `full/image.tpl` |
| `full_image` | `node/view/full.tpl` | `full/image.tpl` |
| `full_infobox` | `node/view/full.tpl` | `full/infobox.tpl` |
| `full_infobox` | `node/view/full.tpl` | `full/infobox.tpl` |
| `full_link` | `node/view/full.tpl` | `full/link.tpl` |
| `full_link` | `node/view/full.tpl` | `full/link.tpl` |
| `full_multicalendar` | `node/view/full.tpl` | `full/multicalendar.tpl` |
| `full_multicalendar` | `node/view/full.tpl` | `full/multicalendar.tpl` |
| `full_poll` | `node/view/full.tpl` | `full/poll.tpl` |
| `full_poll` | `node/view/full.tpl` | `full/poll.tpl` |
| `full_product` | `node/view/full.tpl` | `full/product.tpl` |
| `full_product` | `node/view/full.tpl` | `full/product.tpl` |
| `full_quicktime` | `node/view/full.tpl` | `full/quicktime.tpl` |
| `full_quicktime` | `node/view/full.tpl` | `full/quicktime.tpl` |
| `full_real_video` | `node/view/full.tpl` | `full/real_video.tpl` |
| `full_real_video` | `node/view/full.tpl` | `full/real_video.tpl` |
| `full_silverlight` | `node/view/full.tpl` | `full/silverlight.tpl` |
| `full_silverlight` | `node/view/full.tpl` | `full/silverlight.tpl` |
| `full_windows_media` | `node/view/full.tpl` | `full/windows_media.tpl` |
| `full_windows_media` | `node/view/full.tpl` | `full/windows_media.tpl` |
| `gallery` | `node/view/admin_preview.tpl` | `admin_preview/gallery.tpl` |
| `googlesitemap_node_view` | `node/view/googlesitemap.tpl` | `googlesitemapdynamic/sitemap.tpl` |
| `googlesitemap_view` | `googlesitemapdynamic/sitemap.tpl` | `googlesitemapdynamic/sitemap.tpl` |
| `highlighted_object` | `content/view/embed.tpl` | `embed/highlighted_object.tpl` |
| `highlighted_object` | `content/view/embed.tpl` | `embed/highlighted_object.tpl` |
| `horizontally_listed_sub_items` | `content/view/embed.tpl` | `embed/horizontally_listed_sub_items.tpl` |
| `horizontally_listed_sub_items` | `content/view/embed.tpl` | `embed/horizontally_listed_sub_items.tpl` |
| `image` | `node/view/admin_preview.tpl` | `admin_preview/image.tpl` |
| `image_galleryline` | `node/view/galleryline.tpl` | `galleryline/image.tpl` |
| `image_galleryline` | `node/view/galleryline.tpl` | `galleryline/image.tpl` |
| `image_galleryslide` | `node/view/galleryslide.tpl` | `galleryslide/image.tpl` |
| `image_galleryslide` | `node/view/galleryslide.tpl` | `galleryslide/image.tpl` |
| `image_listitem` | `node/view/listitem.tpl` | `listitem/image.tpl` |
| `image_listitem` | `node/view/listitem.tpl` | `listitem/image.tpl` |
| `itemized_sub_items` | `content/view/embed.tpl` | `embed/itemized_sub_items.tpl` |
| `itemized_sub_items` | `content/view/embed.tpl` | `embed/itemized_sub_items.tpl` |
| `itemized_subtree_items` | `content/view/embed.tpl` | `embed/itemized_subtree_items.tpl` |
| `itemized_subtree_items` | `content/view/embed.tpl` | `embed/itemized_subtree_items.tpl` |
| `line_article` | `node/view/line.tpl` | `line/article.tpl` |
| `line_article` | `node/view/line.tpl` | `line/article.tpl` |
| `line_article_mainpage` | `node/view/line.tpl` | `line/article_mainpage.tpl` |
| `line_article_mainpage` | `node/view/line.tpl` | `line/article_mainpage.tpl` |
| `line_article_subpage` | `node/view/line.tpl` | `line/article_subpage.tpl` |
| `line_article_subpage` | `node/view/line.tpl` | `line/article_subpage.tpl` |
| `line_banner` | `node/view/line.tpl` | `line/banner.tpl` |
| `line_banner` | `node/view/line.tpl` | `line/banner.tpl` |
| `line_blog` | `node/view/line.tpl` | `line/blog.tpl` |
| `line_blog` | `node/view/line.tpl` | `line/blog.tpl` |
| `line_blog_post` | `node/view/line.tpl` | `line/blog_post.tpl` |
| `line_blog_post` | `node/view/line.tpl` | `line/blog_post.tpl` |
| `line_comment` | `node/view/line.tpl` | `line/comment.tpl` |
| `line_comment` | `node/view/line.tpl` | `line/comment.tpl` |
| `line_documentation_page` | `node/view/line.tpl` | `line/documentation_page.tpl` |
| `line_documentation_page` | `node/view/line.tpl` | `line/documentation_page.tpl` |
| `line_event` | `node/view/line.tpl` | `line/event.tpl` |
| `line_event` | `node/view/line.tpl` | `line/event.tpl` |
| `line_event_calendar` | `node/view/line.tpl` | `line/event_calendar.tpl` |
| `line_event_calendar` | `node/view/line.tpl` | `line/event_calendar.tpl` |
| `line_feedback_form` | `node/view/line.tpl` | `line/feedback_form.tpl` |
| `line_feedback_form` | `node/view/line.tpl` | `line/feedback_form.tpl` |
| `line_file` | `node/view/line.tpl` | `line/file.tpl` |
| `line_file` | `node/view/line.tpl` | `line/file.tpl` |
| `line_flash` | `node/view/line.tpl` | `line/flash.tpl` |
| `line_flash` | `node/view/line.tpl` | `line/flash.tpl` |
| `line_folder` | `node/view/line.tpl` | `line/folder.tpl` |
| `line_folder` | `node/view/line.tpl` | `line/folder.tpl` |
| `line_forum` | `node/view/line.tpl` | `line/forum.tpl` |
| `line_forum` | `node/view/line.tpl` | `line/forum.tpl` |
| `line_forum_reply` | `node/view/line.tpl` | `line/forum_reply.tpl` |
| `line_forum_reply` | `node/view/line.tpl` | `line/forum_reply.tpl` |
| `line_forum_topic` | `node/view/line.tpl` | `line/forum_topic.tpl` |
| `line_forum_topic` | `node/view/line.tpl` | `line/forum_topic.tpl` |
| `line_forums` | `node/view/line.tpl` | `line/forums.tpl` |
| `line_forums` | `node/view/line.tpl` | `line/forums.tpl` |
| `line_gallery` | `node/view/line.tpl` | `line/gallery.tpl` |
| `line_gallery` | `node/view/line.tpl` | `line/gallery.tpl` |
| `line_geo_article` | `node/view/line.tpl` | `line/geo_article.tpl` |
| `line_geo_article` | `node/view/line.tpl` | `line/geo_article.tpl` |
| `line_image` | `node/view/line.tpl` | `line/image.tpl` |
| `line_image` | `node/view/line.tpl` | `line/image.tpl` |
| `line_infobox` | `node/view/line.tpl` | `line/infobox.tpl` |
| `line_infobox` | `node/view/line.tpl` | `line/infobox.tpl` |
| `line_link` | `node/view/line.tpl` | `line/link.tpl` |
| `line_link` | `node/view/line.tpl` | `line/link.tpl` |
| `line_multicalendar` | `node/view/line.tpl` | `line/multicalendar.tpl` |
| `line_multicalendar` | `node/view/line.tpl` | `line/multicalendar.tpl` |
| `line_poll` | `node/view/line.tpl` | `line/poll.tpl` |
| `line_poll` | `node/view/line.tpl` | `line/poll.tpl` |
| `line_product` | `node/view/line.tpl` | `line/product.tpl` |
| `line_product` | `node/view/line.tpl` | `line/product.tpl` |
| `line_quicktime` | `node/view/line.tpl` | `line/quicktime.tpl` |
| `line_quicktime` | `node/view/line.tpl` | `line/quicktime.tpl` |
| `line_real_video` | `node/view/line.tpl` | `line/real_video.tpl` |
| `line_real_video` | `node/view/line.tpl` | `line/real_video.tpl` |
| `line_silverlight` | `node/view/line.tpl` | `line/silverlight.tpl` |
| `line_silverlight` | `node/view/line.tpl` | `line/silverlight.tpl` |
| `line_thumbnail_article` | `content/view/line_thumbnail.tpl` | `content/view/line_thumbnail/article.tpl` |
| `line_thumbnail_file` | `content/view/line_thumbnail.tpl` | `content/view/line_thumbnail/file.tpl` |
| `line_thumbnail_flash_player` | `content/view/line_thumbnail.tpl` | `content/view/line_thumbnail/flash_player.tpl` |
| `line_thumbnail_image` | `content/view/line_thumbnail.tpl` | `content/view/line_thumbnail/image.tpl` |
| `line_windows_media` | `node/view/line.tpl` | `line/windows_media.tpl` |
| `line_windows_media` | `node/view/line.tpl` | `line/windows_media.tpl` |
| `link` | `node/view/admin_preview.tpl` | `admin_preview/link.tpl` |
| `node/view/full#cjw_newsletter_edition` | `node/view/full.tpl` | `node/view/full/cjw_newsletter_edition.tpl` |
| `node/view/full#cjw_newsletter_list` | `node/view/full.tpl` | `node/view/full/cjw_newsletter_list.tpl` |
| `node/view/full#cjw_newsletter_list_virtual` | `node/view/full.tpl` | `node/view/full/cjw_newsletter_list_virtual.tpl` |
| `node/view/line#cjw_newsletter_edition` | `node/view/line.tpl` | `node/view/line/cjw_newsletter_edition.tpl` |
| `pdf_category` | `node/view/pdf.tpl` | `pdf_category.tpl` |
| `pdf_recipe` | `node/view/pdf.tpl` | `pdf_recipe.tpl` |
| `person` | `node/view/admin_preview.tpl` | `admin_preview/person.tpl` |
| `poll` | `node/view/admin_preview.tpl` | `admin_preview/poll.tpl` |
| `product` | `node/view/admin_preview.tpl` | `admin_preview/product.tpl` |
| `quicktime` | `node/view/admin_preview.tpl` | `admin_preview/quicktime.tpl` |
| `quote` | `content/datatype/view/ezxmltags/quote.tpl` | `datatype/ezxmltext/quote.tpl` |
| `quote` | `content/datatype/view/ezxmltags/quote.tpl` | `datatype/ezxmltext/quote.tpl` |
| `real_video` | `node/view/admin_preview.tpl` | `admin_preview/real_video.tpl` |
| `review` | `node/view/admin_preview.tpl` | `admin_preview/review.tpl` |
| `table_cols` | `content/datatype/view/ezxmltags/table.tpl` | `datatype/ezxmltext/table_cols.tpl` |
| `table_cols` | `content/datatype/view/ezxmltags/table.tpl` | `datatype/ezxmltext/table_cols.tpl` |
| `table_comparison` | `content/datatype/view/ezxmltags/table.tpl` | `datatype/ezxmltext/table_comparison.tpl` |
| `table_comparison` | `content/datatype/view/ezxmltags/table.tpl` | `datatype/ezxmltext/table_comparison.tpl` |
| `thumbnail_banner` | `node/view/thumbnail.tpl` | `thumbnail/image.tpl` |
| `thumbnail_banner_browse` | `node/view/browse_thumbnail.tpl` | `thumbnail/image_browse.tpl` |
| `thumbnail_image` | `node/view/thumbnail.tpl` | `thumbnail/image.tpl` |
| `thumbnail_image_browse` | `node/view/browse_thumbnail.tpl` | `thumbnail/image_browse.tpl` |
| `tiny_image` | `content/view/tiny.tpl` | `tiny_image.tpl` |
| `tiny_image` | `content/view/tiny.tpl` | `tiny_image.tpl` |
| `tiny_image` | `content/view/tiny.tpl` | `tiny_image.tpl` |
| `tiny_image` | `content/view/tiny.tpl` | `tiny_image.tpl` |
| `tiny_image` | `content/view/tiny.tpl` | `tiny_image.tpl` |
| `vertically_listed_sub_items` | `content/view/embed.tpl` | `embed/vertically_listed_sub_items.tpl` |
| `vertically_listed_sub_items` | `content/view/embed.tpl` | `embed/vertically_listed_sub_items.tpl` |
| `weblog` | `node/view/admin_preview.tpl` | `admin_preview/weblog.tpl` |
| `windows_media` | `node/view/admin_preview.tpl` | `admin_preview/windows_media.tpl` |

## Kernel classes replaced outright

The heaviest mechanism there is, and the first thing to know before anything
else is diagnosed: a replaced kernel class is not the kernel any more, whatever
the kernel source says.

| Class | Replaced by | Instead of |
| --- | --- | --- |
| `eZContentFunctionCollection` | `extension/nxc_powercontent/modules/content/ezcontentfunctioncollection.php` | `kernel/content/ezcontentfunctioncollection.php` |
| `eZContentOperationCollection` | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` | `kernel/content/ezcontentoperationcollection.php` |
| `ezpContentPublishingBehaviour` | `extension/nxc_powercontent/modules/content/ezcontentpublishingbehaviour.php` | `kernel/content/ezcontentpublishingbehaviour.php` |

