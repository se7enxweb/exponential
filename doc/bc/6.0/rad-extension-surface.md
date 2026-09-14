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
| Extension points | **923** |
| ini files read | 381 |
| Settings naming a class | 367 (350 resolve to a class, 16 take an alias, 1 look like a class and are not one) |
| Directories searched for handlers | 116 |
| Interfaces and abstract classes | 41 (29 implemented) |
| Modules | 57 |
| Module views | 399 |
| Policy functions | 179 |

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

### design.ini (5)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `ExtensionSettings` | `DesignExtensions[]` | `ezgmaplocation` | `extension/ezgmaplocation/classes/ezgmaplocation.php` |
| `ExtensionSettings` | `DesignExtensions[]` | `ezpm` | `extension/ezpm/classes/ezpm.php` |
| `ExtensionSettings` | `DesignExtensions[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |
| `ExtensionSettings` | `DesignExtensions[]` | `powercontent` | `extension/powercontent/classes/powercontent.php` |
| `ExtensionSettings` | `DesignExtensions[]` | `xrowmetadata` | `extension/xrowmetadata/classes/structs/xrowmetadata.php` |

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

### module.ini (2)

| Section | Setting | Value | Declared in |
| --- | --- | --- | --- |
| `ModuleSettings` | `ModuleList[]` | `error` | `(declared at runtime)` |
| `ModuleSettings` | `ModuleList[]` | `powercontent` | `extension/powercontent/classes/powercontent.php` |

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

### site.ini (39)

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
| `ExtensionSettings` | `ActiveExtensions[]` | `xrowmetadata` | `extension/xrowmetadata/classes/structs/xrowmetadata.php` |
| `ExtensionSettings` | `ActiveExtensions[]` | `ezgmaplocation` | `extension/ezgmaplocation/classes/ezgmaplocation.php` |
| `ExtensionSettings` | `ActiveExtensions[]` | `owsimpleoperator` | `extension/owsimpleoperator/autoloads/owsimpleoperator.php` |
| `ExtensionSettings` | `ActiveExtensions[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |
| `ExtensionSettings` | `ActiveExtensions[]` | `powercontent` | `extension/powercontent/classes/powercontent.php` |
| `ExtensionSettings` | `ActiveExtensions[]` | `ezprestapiprovider` | `extension/ezprestapiprovider/classes/rest_provider.php` |
| `FileSettings` | `CacheDir` | `cache` | `vendor/zetacomponents/signal-slot/docs/tutorial_multiple_slots_example.php` |
| `MailSettings` | `Transport` | `sendmail` | *alias* |
| `MailSettings` | `TransportAlias[file]` | `eZFileTransport` | `lib/ezutils/classes/ezfiletransport.php` |
| `MailSettings` | `TransportAlias[sendmail]` | `eZSendmailTransport` | `lib/ezutils/classes/ezsendmailtransport.php` |
| `MailSettings` | `TransportAlias[smtp]` | `eZSMTPTransport` | `lib/ezutils/classes/ezsmtptransport.php` |
| `RegionalSettings` | `LanguageSwitcherClass` | `ezpLanguageSwitcher` | `kernel/private/classes/ezplanguageswitcher.php` |
| `RegionalSettings` | `TranslationExtensions[]` | `ezgmaplocation` | `extension/ezgmaplocation/classes/ezgmaplocation.php` |
| `RegionalSettings` | `TranslationExtensions[]` | `ezpm` | `extension/ezpm/classes/ezpm.php` |
| `RegionalSettings` | `TranslationExtensions[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |
| `RegionalSettings` | `TranslationExtensions[]` | `xrowmetadata` | `extension/xrowmetadata/classes/structs/xrowmetadata.php` |
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

## Directories searched for handlers

Places the kernel looks for a file whose path it works out from a name. Add your
extension to one of these and your file is found; leave it out and the class is
never loaded, however correctly it is written. This is the single most common
reason a handler that looks right does nothing.

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
| `ezxml.ini` | `HandlerSettings` | `ExtensionRepositories` | `ezoe` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | empty |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `bccie` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `bcgooglesitemaps` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `cjw_newsletter` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `expchangeclass` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `expdse` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts_content_browser_ui` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts_ui` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `explayouts_ui_api` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezauthorize` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezauthorize` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezflow` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezie` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezjscore` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezmbpaex` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezmultiupload` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezodf` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezoe` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezownerchange` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezpaypal` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezpm` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `eztags` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezupdate` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `ezwt` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `git_manager` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `hcaptcha` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `nxc_powercontent` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `powercontent` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `recaptcha` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `sevenx_dse` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `sevenx_themes_media` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `syndication` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `xrowextract` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `xrowmetadata` |
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
| `site.ini` | `ExtensionSettings` | `ExtensionDirectory` | `extension` |
| `site.ini` | `SearchSettings` | `ExtensionDirectories` | empty |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | empty |
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
| `setup/radsurvey` | `setup` | 0 + 3 named |
| `setup/settingsextension` | `setup` | 0 |
| `setup/contentextension` | `setup` | 0 |
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

