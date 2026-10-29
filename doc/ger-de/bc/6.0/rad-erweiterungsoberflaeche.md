# Die Erweiterungsoberfläche

Lesen Sie diese Seite, wenn Sie alles in einer Exponential-Installation sehen möchten, was sich erweitern oder ersetzen
lässt: jede Einstellung, die eine Klasse nennt, jede Stelle, an der der Kernel nach Dateien sucht, die Schnittstellen,
Module, Views, Richtlinienfunktionen, Template-Operatoren, Ereignisse, überschriebenen Templates und ersetzten
Kernel-Klassen. [Erweiterungspunkte](rad-erweiterungspunkte.md) ist die kuratierte Liste, jeder Punkt erklärt und mit
einem Werkzeug verbunden; diese Seite ist die vollständige Bestandsaufnahme.

Die Bestandsaufnahme unten wurde von einer Referenzinstallation mit ihrem eigenen Satz aktiver Erweiterungen
abgelesen, nicht von Hand geschrieben. Zwei Erweiterungen der Referenzinstallation, die nicht Teil des Produkts sind,
fehlen in den Listen unten; die Zahlen schließen sie ein. Ihre Installation hat ihre eigene Bestandsaufnahme: Lesen Sie
sie live in der Administration unter **Setup → RAD-Werkzeuge → Übersicht der Erweiterungspunkte**
(`/setup/radsurvey`), wo sie sich durchsuchen lässt.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Die Übersicht der Erweiterungspunkte (`setup/radsurvey`) liest die Installation und führt jeden Erweiterungspunkt auf, den sie findet. |
| Wer betroffen ist | Niemand muss etwas ändern. Vor einem Upgrade zeigt der letzte Abschnitt der Übersicht, **Vollständig ersetzte Kernel-Klassen**, die Kernel-Klassen, die eine Erweiterung ersetzt: Dort sollten Sie zuerst nachsehen, wenn eine Änderung am Kernel keine Wirkung zu haben scheint. |
| Wie Sie es prüfen | Öffnen Sie **Setup → RAD-Werkzeuge → Übersicht der Erweiterungspunkte** in der Administration. |
| Wie Sie es beheben | Ersetzen Sie ein Override einer Kernel-Klasse durch einen registrierten Erweiterungspunkt, wo es einen gibt ([Erweiterungspunkte](rad-erweiterungspunkte.md)). |

## Was gefunden wurde

| | |
| --- | --- |
| Erweiterungspunkte | **1744** |
| Gelesene INI-Dateien | 381 |
| Einstellungen, die eine Klasse nennen | 350 (333 lösen sich zu einer Klasse auf, 16 nehmen einen Alias, 1 sieht wie eine Klasse aus und ist keine) |
| Stellen, an denen der Kernel sucht | 236 |
| Schnittstellen und abstrakte Klassen | 41 (29 implementiert) |
| Module | 60 |
| Modul-Views | 407 |
| Richtlinienfunktionen | 179 |
| Template-Operatoren und -Funktionen | 414 (364 Operatoren, 50 Funktionen) |
| Ereignisse, auf die etwas hören kann | 34 |
| Bereits überschriebene Templates | 259 |
| Ersetzte Kernel-Klassen | 3 |

Eine Zahl ist kein Versprechen, dass es sich lohnt, sie alle zu erweitern. Sie ist ein Versprechen, dass keiner davon
weggelassen wurde, weil niemand an ihn gedacht hat.

## Einstellungen, die eine Klasse nennen

Ändern Sie eine davon, und etwas anderes antwortet stattdessen.

Mehrere dieser Einstellungen nehmen einen Alias statt einer Klasse, und die beiden lassen sich nicht an der Einstellung
unterscheiden - nur am Wert. Jede Klasse in diesem System hat irgendwo einen Großbuchstaben, und jeder Alias ist ein
kleingeschriebenes Wort; ein kleingeschriebener Wert, der keine Klasse nennt, ist also ein *Alias*, der seine Arbeit
tut. Eine als **nicht deklariert** markierte Zeile hat die Form einer Klasse und ist keine, was einen Blick wert ist.

### admininterface.ini (1)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `WindowControlsSettings` | `AdditionalTabs[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |

### binaryfile.ini (3)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `HandlerSettings` | `MetaDataExtractor[application/msword]` | `ezword` | *Alias* |
| `HandlerSettings` | `MetaDataExtractor[application/pdf]` | `ezpdf` | `lib/ezpdf/classes/ezpdf.php` |
| `HandlerSettings` | `MetaDataExtractor[text/plain]` | `ezplaintext` | *Alias* |

### block.ini (5)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `Dynamic3Items` | `FetchClass` | `eZFlowLatestObjects` | `extension/ezflow/classes/fetches/ezflowlatestobjects.php` |
| `GMapItems` | `CustomAttributes[]` | `attribute` | `(zur Laufzeit deklariert)` |
| `Keyword` | `FetchClass` | `eZFlowKeywordsFetch` | `extension/ezflow/classes/fetches/ezflowkeywordsfetch.php` |
| `LatestContent` | `FetchClass` | `eZFlowLatestContent` | `extension/ezflow/classes/fetches/ezflowlatestcontent.php` |
| `MultimediaCarousel` | `FetchClass` | `eZFlowMCFetch` | `extension/ezflow/classes/fetches/ezflowmcfetch.php` |

### changeclass.ini (6)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `General` | `UnsupportedDataTypeArray[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `ezobjectrelationlist` | `Class` | `expChangeClassConverters` | `extension/expchangeclass/classes/converters.php` |
| `ezstring` | `Class` | `expChangeClassConverters` | `extension/expchangeclass/classes/converters.php` |
| `ezstring` | `SupportedDestination[]` | `ezxmltext` | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |
| `eztext` | `Class` | `expChangeClassConverters` | `extension/expchangeclass/classes/converters.php` |
| `eztext` | `SupportedDestination[]` | `ezxmltext` | `kernel/classes/datatypes/ezxmltext/ezxmltext.php` |

### cie.ini (4)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `CieSettings` | `ExportOutputFormatHandlers[cp1252]` | `bccieExportFormatOutputHandlerCP1252` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerCP1252.php` |
| `CieSettings` | `ExportOutputFormatHandlers[utf16le]` | `bccieExportFormatOutputHandlerUtf16Le` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf16Le.php` |
| `CieSettings` | `ExportOutputFormatHandlers[utf8]` | `bccieExportFormatOutputHandlerUtf8` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8.php` |
| `CieSettings` | `ExportOutputFormatHandlers[utf8bom]` | `bccieExportFormatOutputHandlerUtf8Bom` | `extension/bccie/classes/exportFormatOutputHandlers/bccieExportFormatOutputHandlerUtf8Bom.php` |

### cjw_newsletter.ini (2)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `NewsletterFilterSettings` | `AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeSalutation` | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypesalutation.php` |
| `NewsletterFilterSettings` | `AvailableFilterTypeClassArray[]` | `CjwNewsletterFilterTypeEmail` | `extension/cjw_newsletter/classes/filtertypes/cjwnewsletterfiltertypeemail.php` |

### content.ini (35)

| Abschnitt | Einstellung | Wert | Deklariert in |
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

| Abschnitt | Einstellung | Wert | Deklariert in |
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

| Abschnitt | Einstellung | Wert | Deklariert in |
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

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `SchemaSettings` | `SchemaHandlerClasses[mongo]` | `expMongoSchema` | `lib/ezdbschema/classes/expmongoschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[mysql]` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[mysqli]` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[postgresql]` | `eZPgsqlSchema` | `lib/ezdbschema/classes/ezpgsqlschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[sqlite3]` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |
| `SchemaSettings` | `SchemaHandlerClasses[sqlite]` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |

### error.ini (2)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `ErrorSettings` | `DefaultErrorHandler` | `displayerror` | *Alias* |
| `ErrorSettings-kernel` | `ErrorHandler[1]` | `embed` | *Alias* |

### explayouts.ini (58)

| Abschnitt | Einstellung | Wert | Deklariert in |
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

| Abschnitt | Einstellung | Wert | Deklariert in |
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

| Abschnitt | Einstellung | Wert | Deklariert in |
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

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `SolrFieldMapSettings` | `CustomMap[eztags]` | `ezfSolrDocumentFieldeZTags` | `extension/eztags/classes/ezfsolrdocumentfieldeztags.php` |
| `SolrFieldMapSettings` | `CustomMap[sckenhancedselection]` | `ezfSolrDocumentFieldSckEnhancedSelection` | `extension/enhancedselection2/classes/ezfsolrdocumentfieldsckenhancedselection.php` |
| `SolrFieldMapSettings` | `CustomMap[xrowmetadata]` | `ezfSolrDocumentFieldxrowMetadata` | `extension/xrowmetadata/classes/ezfSolrDocumentFieldxrowMetadata.php` |

### ezjscore.ini (18)

| Abschnitt | Einstellung | Wert | Deklariert in |
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

### ezoe.ini (1)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `SpellChecker` | `config[general.engine]` | `GoogleSpell` | `extension/ezoe/modules/ezoe/classes/GoogleSpell.php` |

### ezrest.ini (1)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `RESTSettings` | `HandlerList[]` | `eZRESTODFHandler` | `extension/ezodf/classes/ezrestodfhandler.php` |

### ezxml.ini (6)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `InputSettings` | `AliasClasses[eZSimplifiedXMLInput]` | `eZOEXMLInput` | `extension/ezoe/ezxmltext/handlers/input/ezoexmlinput.php` |
| `InputSettings` | `HandlerClass` | `eZSimplifiedXMLInput` | `kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinput.php` |
| `OutputSettings` | `AliasClasses[ezpdf]` | `eZPDFXMLOutput` | `kernel/classes/datatypes/ezxmltext/handlers/output/ezpdfxmloutput.php` |
| `OutputSettings` | `HandlerClass` | `eZXHTMLXMLOutput` | `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php` |
| `OutputSettings` | `HandlerClass` | `ExplBlockXHTMLXMLOutput` | `extension/explayouts/classes/explblockxhtmlxmloutput.php` |
| `OutputSettings` | `HandlerClass` | `sevenxThemesMediaXHTMLXMLOutput` | `extension/sevenx_themes_media/classes/sevenxthemesmediaxhtmlxmloutput.php` |

### file.ini (8)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `BinaryFileSettings` | `Handler` | `eZFilePassthroughHandler` | `kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php` |
| `ClusteringSettings` | `FileHandler` | `eZFSFileHandler` | `kernel/classes/clusterfilehandlers/ezfsfilehandler.php` |
| `FileSettings` | `FileExtensionBlackList[]` | `phar` | `(zur Laufzeit deklariert)` |
| `FileSettings` | `Handlers[gzip]` | `ezgzipcompressionhandler` | `lib/ezfile/classes/ezgzipcompressionhandler.php` |
| `FileSettings` | `Handlers[gzipshell]` | `ezgzipshellcompressionhandler` | `lib/ezfile/classes/ezgzipshellcompressionhandler.php` |
| `FileSettings` | `Handlers[gzipzlib]` | `ezgzipzlibcompressionhandler` | `lib/ezfile/classes/ezgzipzlibcompressionhandler.php` |
| `eZDFSClusteringSettings` | `DBBackend` | `eZDFSFileHandlerMySQLiBackend` | `kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php` |
| `eZDFSClusteringSettings` | `DFSBackend` | `eZDFSFileHandlerDFSBackend` | `kernel/private/classes/clusterfilehandlers/dfsbackends/dfs.php` |

### git_manager.ini (1)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `GitManagerSettings` | `VarExcludeDirs[]` | `cache` | `vendor/zetacomponents/signal-slot/docs/tutorial_multiple_slots_example.php` |

### http.ini (1)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `HTTPCacheHandlers` | `Handlers[]` | `eZSquidCacheManager` | `extension/ezflow/classes/ezsquidcachemanager.php` |

### image.ini (4)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `EXIFAnalyzer` | `Handler` | `ezexif` | *Alias* |
| `GD` | `Handler` | `eZImageGDFactory` | `lib/ezimage/classes/ezimagegdfactory.php` |
| `GIFAnalyzer` | `Handler` | `ezgif` | *Alias* |
| `ImageMagick` | `Handler` | `eZImageShellFactory` | `lib/ezimage/classes/ezimageshellfactory.php` |

### menu.ini (2)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `TopAdminMenu` | `Tabs[]` | `eztags` | `extension/eztags/datatypes/eztags/eztags.php` |
| `TopAdminMenu` | `Tabs[]` | `gitmanager` | `extension/git_manager/classes/git_manager.php` |

### notification.ini (2)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `RuleSettings` | `Alias[keyword]` | `ezkeyword` | `kernel/classes/datatypes/ezkeyword/ezkeyword.php` |
| `TransportSettings` | `DefaultTransport` | `mail` | *Alias* |

### package.ini (21)

| Abschnitt | Einstellung | Wert | Deklariert in |
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

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `ApiProvider` | `ProviderClass[auth]` | `ezpRestAuthProvider` | `kernel/private/rest/classes/auth/auth_provider.php` |
| `ApiProvider` | `ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapi/classes/rest_provider.php` |
| `ApiProvider` | `ProviderClass[ezp]`, `ProviderClass[ezpl]` (set again by the deprecated `ezprestapiprovider` 6.0.4 shim) | `ezp7xRestApiProvider` | `extension/ezprestapi/classes/rest_provider.php` |
| `ApiProvider` | `ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapi/classes/rest_provider.php` |
| `Authentication` | `AuthenticationStyle` | `ezpRestOauthAuthenticationStyle` | `kernel/private/rest/classes/auth/styles/oauth.php` |
| `OutputSettings` | `RendererClass[xhtml]` | `ezpContentXHTMLRenderer` | `kernel/private/rest/classes/renderers/xhtml_content_renderer.php` |
| `RouteSettings` | `RouteSettingImpl` | `ezpRestIniRouteFilter` | `kernel/private/rest/classes/auth/ini_route_security.php` |
| `System` | `PrefixFilterClass` | `ezpRestDefaultRegexpPrefixFilter` | `kernel/private/rest/classes/default_regexp_prefix_filter.php` |

### setup.ini (1)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `ezcversion` | `TestClass` | `ezcBaseFile` | `vendor/zetacomponents/base/src/file.php` |

### shop.ini (3)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `BasketInfoSettings` | `Handler` | `ezdefault` | *Alias* |
| `ExchangeRatesSettings` | `ExchangeRatesUpdateHandler` | `eZECB` | **nicht deklariert** |
| `MathSettings` | `MathHandler` | `eZPHPMath` | `lib/ezmath/classes/mathhandlers/ezphpmath.php` |

### shopaccount.ini (2)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `AccountSettings` | `Handler` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `ConfirmOrderSettings` | `Handler` | `ezdefault` | *Alias* |

### site.ini (29)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `Cache_ezjscore` | `class` | `ezjscCacheManager` | `extension/ezjscore/classes/ezjsccachemanager.php` |
| `Cache_restRoutes` | `class` | `ezpRestRoutesCacheClear` | `kernel/private/rest/classes/cache/clear_routes.php` |
| `Cache_restRoutes` | `purgeClass` | `ezpRestRoutesCacheClear` | `kernel/private/rest/classes/cache/clear_routes.php` |
| `ContentSettings` | `ContentClassEditHandler` | `eZContentClassEditHandler` | `kernel/classes/ezcontentclassedithandler.php` |
| `ContentSettings` | `DatatypeBlackListForExternal[]` | `ezuser` | `kernel/classes/datatypes/ezuser/ezuser.php` |
| `ContentSettings` | `StaticCacheHandler` | `eZStaticCache` | `kernel/classes/ezstaticcache.php` |
| `DatabaseSettings` | `DatabaseImplementation` | `ezmysqli` | *Alias* |
| `DatabaseSettings` | `ImplementationAlias[ezmysql]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[ezmysqli]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[ezpostgresql]` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| `DatabaseSettings` | `ImplementationAlias[mongodb]` | `expMongoDB` | `lib/ezdb/classes/expmongodb.php` |
| `DatabaseSettings` | `ImplementationAlias[mysql]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[mysqli]` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| `DatabaseSettings` | `ImplementationAlias[pgsql]` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| `DatabaseSettings` | `ImplementationAlias[postgresql]` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| `DatabaseSettings` | `ImplementationAlias[sqlite3]` | `eZSQLite3DB` | `lib/ezdb/classes/ezsqlite3db.php` |
| `DebugSettings` | `AlwaysLog[]` | `error` | `(zur Laufzeit deklariert)` |
| `FileSettings` | `CacheDir` | `cache` | `vendor/zetacomponents/signal-slot/docs/tutorial_multiple_slots_example.php` |
| `MailSettings` | `Transport` | `sendmail` | *Alias* |
| `MailSettings` | `TransportAlias[file]` | `eZFileTransport` | `lib/ezutils/classes/ezfiletransport.php` |
| `MailSettings` | `TransportAlias[sendmail]` | `eZSendmailTransport` | `lib/ezutils/classes/ezsendmailtransport.php` |
| `MailSettings` | `TransportAlias[smtp]` | `eZSMTPTransport` | `lib/ezutils/classes/ezsmtptransport.php` |
| `RegionalSettings` | `LanguageSwitcherClass` | `ezpLanguageSwitcher` | `kernel/private/classes/ezplanguageswitcher.php` |
| `SearchSettings` | `SearchEngine` | `eZSearchEngine` | `kernel/search/plugins/ezsearchengine/ezsearchengine.php` |
| `SiteAccessSettings` | `MobileDeviceFilterClass` | `ezpMobileDeviceRegexpFilter` | `kernel/private/classes/ezpmobiledeviceregexpfilter.php` |
| `SiteSettings` | `ErrorHandler` | `displayerror` | *Alias* |
| `URLTranslator` | `FilterClasses[]` | `eZURLAliasFilterAppendNodeID` | `kernel/private/classes/urlaliasfilters/ezurlaliasfilterappendnodeid.php` |
| `UserSettings` | `LoginHandler[]` | `standard` | *Alias* |
| `UserSettings` | `LoginHandler[]` | `paex` | *Alias* |

### syndication.ini (1)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `SyndicationFilters` | `FilterArray[]` | `Attribute` | `(zur Laufzeit deklariert)` |

### template.ini (3)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `AttributeOperator` | `DefaultFormatter` | `html` | *Alias* |
| `AttributeOperator` | `OutputFormatter[html]` | `ezpAttributeOperatorHTMLFormatter` | `kernel/private/eztemplate/ezpattributeoperatorhtmlformatter.php` |
| `AttributeOperator` | `OutputFormatter[text]` | `ezpAttributeOperatorTextFormatter` | `kernel/private/eztemplate/ezpattributeoperatortextformatter.php` |

### upload.ini (3)

| Abschnitt | Einstellung | Wert | Deklariert in |
| --- | --- | --- | --- |
| `CreateSettings` | `MimeUploadHandlerMap[application/msword]` | `ezopenofficeuploadhandler` | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php` |
| `CreateSettings` | `MimeUploadHandlerMap[application/rtf]` | `ezopenofficeuploadhandler` | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php` |
| `CreateSettings` | `MimeUploadHandlerMap[application/vnd.oasis.opendocument.text]` | `ezopenofficeuploadhandler` | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php` |

## Stellen, an denen der Kernel sucht

Jede Einstellung, die ein zu durchsuchendes Verzeichnis oder eine zu durchsuchende Erweiterung nennt. Fügen Sie Ihre
Erweiterung zu einer davon hinzu, und Ihre Datei wird gefunden; lassen Sie sie weg, und die Klasse wird nie geladen,
wie korrekt sie auch geschrieben ist. Wenn etwas funktioniert, das nicht funktionieren sollte, oder nicht funktioniert,
das funktionieren sollte, ist die Antwort meistens eine dieser Zeilen.

| INI | Abschnitt | Einstellung | Derzeit |
| --- | --- | --- | --- |
| `binaryfile.ini` | `HandlerSettings` | `ExtensionRepositories` | leer |
| `content.ini` | `ActionSettings` | `ExtensionDirectories` | leer |
| `content.ini` | `DataTypeSettings` | `RepositoryDirectories` | `kernel/classes/datatypes` |
| `content.ini` | `DataTypeSettings` | `ExtensionDirectories` | leer |
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
| `content.ini` | `EditSettings` | `ExtensionDirectories` | leer |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | leer |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `bccie` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `bcgooglesitemaps` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `cjw_newsletter` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `ezflow` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `ezmbpaex` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `syndication` |
| `cronjob.ini` | `CronjobSettings` | `ExtensionDirectories` | `xrowmetadata` |
| `design.ini` | `ExtensionSettings` | `DesignExtensions` | leer |
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
| `icon.ini` | `ExtensionSettings` | `IconExtensions` | leer |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | leer |
| `module.ini` | `ModuleSettings` | `ModuleList` | `class`, `collaboration`, `content`, `error`, `ezinfo` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `bccie` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `bccie` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `bcgooglesitemaps` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `cjw_newsletter` |
| `module.ini` | `ModuleSettings` | `ModuleList` | `newsletter` |
| `module.ini` | `ModuleSettings` | `ExtensionRepositories` | `expchangeclass` |
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
| `notification.ini` | `NotificationEventHandlerSettings` | `ExtensionDirectories` | leer |
| `notification.ini` | `NotificationEventTypeSettings` | `RepositoryDirectories` | `kernel/classes/notification/event/` |
| `notification.ini` | `NotificationEventTypeSettings` | `ExtensionDirectories` | leer |
| `notification.ini` | `RuleSettings` | `RepositoryDirectories` | `kernel/notification/rules` |
| `notification.ini` | `RuleSettings` | `ExtensionDirectories` | leer |
| `package.ini` | `PackageSettings` | `RepositoryDirectories` | `kernel/classes` |
| `package.ini` | `PackageSettings` | `ExtensionDirectories` | leer |
| `shop.ini` | `BasketInfoSettings` | `RepositoryDirectories` | `kernel/classes/basketinfohandlers` |
| `shop.ini` | `BasketInfoSettings` | `ExtensionDirectories` | leer |
| `shop.ini` | `ExchangeRatesSettings` | `RepositoryDirectories` | `kernel/shop/classes/exchangeratehandlers` |
| `shop.ini` | `ExchangeRatesSettings` | `ExtensionDirectories` | leer |
| `shop.ini` | `ShippingSettings` | `RepositoryDirectories` | `kernel/classes/shippinghandlers` |
| `shop.ini` | `ShippingSettings` | `ExtensionDirectories` | leer |
| `shop.ini` | `VATSettings` | `RepositoryDirectories` | `kernel/classes/vathandlers` |
| `shop.ini` | `VATSettings` | `ExtensionDirectories` | leer |
| `shopaccount.ini` | `HandlerSettings` | `ExtensionRepositories` | leer |
| `site.ini` | `DesignSettings` | `DesignLocationCache` | `disabled` |
| `site.ini` | `DesignSettings` | `DesignLocationCache` | `enabled` |
| `site.ini` | `DesignSettings` | `DesignExtensions` | `expsite_app` |
| `site.ini` | `ExtensionSettings` | `ExtensionDirectory` | `extension` |
| `site.ini` | `ExtensionSettings` | `AdditionalExtensionDirectories` | leer |
| `site.ini` | `ExtensionSettings` | `ActiveExtensions` | `xrowmetadata` |
| `site.ini` | `ExtensionSettings` | `ActiveAccessExtensions` | leer |
| `site.ini` | `ExtensionSettings` | `ActiveExtensions` | `ezjscore`, `ezoe`, `ezformtoken`, `xrowmetadata`, `ezjscore` |
| `site.ini` | `ExtensionSettings` | `ActiveExtensions` | `expsite_app` |
| `site.ini` | `RSSSettings` | `ActiveExtensions` | leer |
| `site.ini` | `RegionalSettings` | `TranslationExtensions` | leer |
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
| `site.ini` | `SearchSettings` | `ExtensionDirectories` | leer |
| `site.ini` | `TemplateSettings` | `AutoloadPathList` | `lib/eztemplate/classes/`, `kernel/common/`, `lib/ezpdf/classes/`, `kernel/private/eztemplate/` |
| `site.ini` | `TemplateSettings` | `ExtensionAutoloadPath` | leer |
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
| `site.ini` | `UserSettings` | `ExtensionDirectory` | leer |
| `site.ini` | `UserSettings` | `ExtensionDirectory` | `ezmbpaex` |
| `workflow.ini` | `EventSettings` | `RepositoryDirectories` | `kernel/classes/workflowtypes` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | leer |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `autonotifications` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `autorss` |
| `workflow.ini` | `EventSettings` | `RepositoryDirectories` | `extension/bcwebsitestatistics/workflowtypes` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `bcwebsitestatistics` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `ezflow` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `ezssp` |
| `workflow.ini` | `EventSettings` | `ExtensionDirectories` | `swark` |

## Schnittstellen und abstrakte Klassen

Was der Kernel deklariert, damit jemand anderes es implementiert, die schwersten zuerst. Die mit vielen Methoden und
einer Implementierung sind das tiefe Wasser: Sie zu ersetzen heißt, für alles einzustehen, wofür die ausgelieferte
bereits einsteht.

| Vertrag | Art | Methoden | Implementiert von |
| --- | --- | --- | --- |
| `eZClusterFileHandlerInterface` | Schnittstelle | 44 | `eZFSFileHandler` |
| `expExtensionWizard` | abstrakte Klasse | 28 | `expDesignExtensionWizard`, `expModuleExtensionWizard`, `expWorkflowEventWizard`, `expHandlerWizard`, `expDatatypeWizard`, `expTemplateExtensionWizard` |
| `ezpSessionHandler` | abstrakte Klasse | 16 | `ezpSessionHandlerDB`, `ezpSessionHandlerPHP`, `ezpSessionHandlerSymfony` |
| `eZDFSFileHandlerDFSBackendInterface` | Schnittstelle | 12 | `eZDFSFileHandlerDFSBackend` |
| `ezpAutoloadOutput` | Schnittstelle | 10 | `ezpAutoloadCliOutput` |
| `ezpRestMvcController` | abstrakte Klasse | 10 | `ezp7xRestContentController`, `ezpRestContentController` |
| `eZClusterEventListener` | Schnittstelle | 9 | *noch nichts* |
| `ezpClusterGateway` | abstrakte Klasse | 8 | `ezpDfsMySQLiClusterGateway`, `ezpDfsPostgresqlClusterGateway` |
| `ezpRestCacheStorageCluster` | abstrakte Klasse | 8 | `ezpRestCacheStorageClusterObject` |
| `ezpSearchEngine` | Schnittstelle | 8 | `eZSearchEngine` |
| `ezpRestPrefixFilterInterface` | abstrakte Klasse | 7 | `ezpRestDefaultRegexpPrefixFilter` |
| `ezpKernelHandler` | Schnittstelle | 6 | `ezpKernelTreeMenu`, `ezpKernelRest` |
| `ezpStaticCache` | Schnittstelle | 6 | `eZStaticCache` |
| `ezpLanguageSwitcherCapable` | Schnittstelle | 5 | `ezpLanguageSwitcher` |
| `ezpMobileDeviceDetectFilterInterface` | Schnittstelle | 4 | `ezpMobileDeviceRegexpFilter` |
| `ezpRestAuthenticationStyleInterface` | Schnittstelle | 4 | `ezpRestBasicAuthStyle`, `ezpRestNoAuthStyle`, `ezpRestOauthAuthenticationStyle` |
| `ezpAttributeOperatorFormatterInterface` | Schnittstelle | 3 | `ezpAttributeOperatorHTMLFormatter`, `ezpAttributeOperatorTextFormatter` |
| `ezpRestAuthenticationStyle` | abstrakte Klasse | 3 | *noch nichts* |
| `eZMySQLCharset` | abstrakte Klasse | 2 | *noch nichts* |
| `eZURLAliasFilter` | abstrakte Klasse | 2 | `eZURLAliasFilterAppendNodeID` |
| `ezpMultivariateTestHandlerInterface` | Schnittstelle | 2 | `ezpMultivariateTestHandler` |
| `ezpRestContentRendererInterface` | abstrakte Klasse | 2 | `ezpContentXHTMLRenderer` |
| `ezpRestPreRoutingFilterInterface` | Schnittstelle | 2 | *noch nichts* |
| `ezpRestProviderInterface` | Schnittstelle | 2 | `ezpRestAuthProvider`, `ezp7xRestApiProvider`, `ezpRestApiProvider` |
| `ezpRestRequestFilterInterface` | Schnittstelle | 2 | *noch nichts* |
| `ezpRestResponseFilterInterface` | Schnittstelle | 2 | *noch nichts* |
| `ezpRestResultFilterInterface` | Schnittstelle | 2 | *noch nichts* |
| `ezpRestRouteFilterInterface` | abstrakte Klasse | 2 | `ezpRestIniRouteFilter` |
| `ezpUpdatedContent` | abstrakte Klasse | 2 | *noch nichts* |
| `eZClusterEventLogger` | Schnittstelle | 1 | `eZClusterEventLoggerEzdebug`, `eZClusterEventLoggerPhp` |
| `eZClusterEventNotifier` | Schnittstelle | 1 | `eZDFSFileHandlerMySQLiBackend` |
| `eZDFSFileHandlerDFSBackendFactoryInterface` | Schnittstelle | 1 | *noch nichts* |
| `ezpAsynchronousPublisherOutput` | Schnittstelle | 1 | `ezpAsynchronousPublisherCliOutput`, `ezpAsynchronousPublisherLogOutput` |
| `ezpAsynchronousPublishingFilter` | abstrakte Klasse | 1 | *noch nichts* |
| `ezpAsynchronousPublishingFilterInterface` | Schnittstelle | 1 | *noch nichts* |
| `ezpContentCriteriaInterface` | Schnittstelle | 1 | `ezpContentClassCriteria`, `ezpContentDepthCriteria`, `ezpContentFieldCriteria`, `ezpContentLimitCriteria`, `ezpContentLocationCriteria`, `ezpContentSortingCriteria` |
| `ezpDatabaseBasedClusterFileHandler` | Schnittstelle | 1 | `eZDFSFileHandler` |
| `ezpRestViewControllerInterface` | Schnittstelle | 1 | `ezpRestApiViewController` |
| `ezpWebBasedKernelHandler` | Schnittstelle | 1 | `ezpKernel`, `ezpKernelWeb` |
| `ezpRestCacheStorageFile` | abstrakte Klasse | 0 | *noch nichts* |
| `ezpRestModel` | abstrakte Klasse | 0 | `ezpOauthUtility`, `ezpRestContentModel` |

## Module und ihre Views

Jede Seite, die das System ausliefert. Eine View kann durch eine Erweiterung ersetzt werden, die ein Modul gleichen
Namens mitbringt, und ein eigenes Modul kann Views daneben hinzufügen. Eine View ohne aufgeführte Richtlinie ist für
jeden erreichbar, der das Modul erreichen kann.

### bccie

`extension/bccie/modules/bccie` — Richtlinien: `read`

| View | Braucht | Parameter |
| --- | --- | --- |
| `bccie/overview` | `read` | 0 + 1 benannt |
| `bccie/export` | `read` | 1 |
| `bccie/doexport` | `read` | 1 |

### changeclass

`extension/expchangeclass/modules/changeclass` — Richtlinien: `convert`

| View | Braucht | Parameter |
| --- | --- | --- |
| `changeclass/select_class` | `convert` | 0 |
| `changeclass/map_attributes` | `convert` | 0 |
| `changeclass/action` | `convert` | 0 |

Fetch-Funktionen: `simple_conversion`

### class

`kernel/class`

| View | Braucht | Parameter |
| --- | --- | --- |
| `class/edit` | *nichts* | 3 + 1 benannt |
| `class/view` | *nichts* | 1 + 2 benannt |
| `class/copy` | *nichts* | 1 |
| `class/down` | *nichts* | 2 |
| `class/up` | *nichts* | 2 |
| `class/removeclass` | *nichts* | 1 |
| `class/removegroup` | *nichts* | 0 |
| `class/classlist` | *nichts* | 1 |
| `class/grouplist` | *nichts* | 0 |
| `class/groupedit` | *nichts* | 1 |
| `class/translation` | *nichts* | 0 |

Fetch-Funktionen: `list`, `list_by_groups`, `latest_list`, `attribute_list`, `override_template_list`

### collaboration

`kernel/collaboration`

| View | Braucht | Parameter |
| --- | --- | --- |
| `collaboration/action` | *nichts* | 0 |
| `collaboration/view` | *nichts* | 1 + 2 benannt |
| `collaboration/item` | *nichts* | 2 + 2 benannt |
| `collaboration/group` | *nichts* | 2 + 2 benannt |

Fetch-Funktionen: `participant`, `participant_list`, `participant_map`, `message_list`, `item_list`, `item_count`, `group_tree`, `tree_count`

### content

`kernel/content` — Richtlinien: `bookmark`, `move`, `read`, `diff`, `view_embed`, `create`, `edit`, `publish`, `manage_locations`, `hide`, `reverserelatedlist`, `translate`, `remove`, `versionread`, `versionremove`, `pdf`, `translations`, `urltranslator`, `pendinglist`, `restore`, `cleantrash`, `tipafriend`, `dashboard`

| View | Braucht | Parameter |
| --- | --- | --- |
| `content/edit` | `edit or create` | 4 |
| `content/removenode` | `edit` | 4 |
| `content/removeassignment` | `edit` | 0 |
| `content/pdf` | `pdf` | 1 + 5 benannt |
| `content/view` | `read` | 2 + 5 benannt |
| `content/copy` | `read` | 1 |
| `content/copysubtree` | `create` | 1 |
| `content/versionview` | `versionread` | 4 + 3 benannt |
| `content/restore` | `restore` | 1 |
| `content/search` | `read` | 0 + 1 benannt |
| `content/urlalias` | `edit` | 1 + 1 benannt |
| `content/urltranslator` | `urltranslator` | 0 + 1 benannt |
| `content/urlwildcards` | `urltranslator` | 0 + 1 benannt |
| `content/advancedsearch` | `read` | 1 + 1 benannt |
| `content/browse` | `read` | 3 + 1 benannt |
| `content/upload` | `create` | 0 |
| `content/removeobject` | `read` | 0 |
| `content/removeuserobject` | `read` | 0 |
| `content/removemediaobject` | `read` | 0 |
| `content/removeeditversion` | `read` | 0 |
| `content/download` | `read` | 2 + 1 benannt |
| `content/action` | `read` | 0 |
| `content/collectinformation` | `read` | 0 |
| `content/draft` | `edit` | 0 + 1 benannt |
| `content/history` | `read`, `edit` | 2 + 1 benannt |
| `content/trash` | `restore` | 0 + 1 benannt |
| `content/translations` | `translations` | 1 |
| `content/tipafriend` | `tipafriend`, `read` | 1 |
| `content/keyword` | `read` | 1 + 2 benannt |
| `content/collectedinfo` | `read` | 1 |
| `content/bookmark` | `bookmark` | 0 + 1 benannt |
| `content/pendinglist` | `pendinglist` | 0 + 1 benannt |
| `content/new` | `read` | 0 |
| `content/hide` | `hide` | 1 |
| `content/move` | `edit` | 1 |
| `content/reverserelatedlist` | `reverserelatedlist` | 1 + 1 benannt |
| `content/translation` | `read` | 0 |
| `content/treemenu` | `read` | 4 |
| `content/dashboard` | `dashboard` | 0 |
| `content/queued` | `edit` | 2 |

Fetch-Funktionen: `object`, `version`, `node`, `locale_list`, `locale`, `prioritized_languages`, `prioritized_language_codes`, `translation_list`, `non_translation_list`, `class`, `class_attribute_list`, `class_attribute`, `calendar`, `list`, `list_count`, `tree`, `tree_count`, `search`, `trash_count`, `trash_object_list`, `draft_count`, `draft_version_list`, `pending_count`, `pending_list`, `version_count`, `version_list`, `can_instantiate_class_list`, `class_list`, `can_instantiate_classes`, `contentobject_attributes`, `bookmarks`, `recent`, `section_list`, `tipafriend_top_list`, `view_top_list`, `collected_info_count`, `collected_info_count_list`, `collected_info_collection`, `collected_info_list`, `object_by_attribute`, `object_count_by_user_id`, `same_classattribute_node`, `keyword`, `keyword_count`, `access`, `navigation_parts`, `navigation_part`, `related_objects`, `related_objects_count`, `reverse_related_objects`, `reverse_related_objects_count`, `available_sort_fields`, `country_list`, `related_objects_ids`, `reverse_related_objects_ids`, `content_tree_menu_expiry`

### content

`extension/nxc_powercontent/modules/content` — Richtlinien: `bookmark`, `move`, `read`, `diff`, `view_embed`, `create`, `edit`, `publish`, `manage_locations`, `hide`, `reverserelatedlist`, `translate`, `remove`, `versionread`, `versionremove`, `pdf`, `translations`, `urltranslator`, `pendinglist`, `restore`, `cleantrash`, `tipafriend`, `dashboard`

| View | Braucht | Parameter |
| --- | --- | --- |
| `content/edit` | `edit or create` | 4 |
| `content/removenode` | `edit` | 4 |
| `content/removeassignment` | `edit` | 0 |
| `content/pdf` | `pdf` | 1 + 5 benannt |
| `content/view` | `read` | 2 + 5 benannt |
| `content/copy` | `read` | 1 |
| `content/copysubtree` | `create` | 1 |
| `content/versionview` | `versionread` | 4 + 3 benannt |
| `content/restore` | `restore` | 1 |
| `content/search` | `read` | 0 + 1 benannt |
| `content/urlalias` | `edit` | 1 + 1 benannt |
| `content/urltranslator` | `urltranslator` | 0 + 1 benannt |
| `content/urlwildcards` | `urltranslator` | 0 + 1 benannt |
| `content/advancedsearch` | `read` | 1 + 1 benannt |
| `content/browse` | `read` | 3 + 1 benannt |
| `content/upload` | `create` | 0 |
| `content/removeobject` | `read` | 0 |
| `content/removeuserobject` | `read` | 0 |
| `content/removemediaobject` | `read` | 0 |
| `content/removeeditversion` | `read` | 0 |
| `content/download` | `read` | 2 + 1 benannt |
| `content/action` | `read` | 0 |
| `content/collectinformation` | `read` | 0 |
| `content/draft` | `edit` | 0 + 1 benannt |
| `content/history` | `read`, `edit` | 2 + 1 benannt |
| `content/trash` | `restore` | 0 + 1 benannt |
| `content/translations` | `translations` | 1 |
| `content/tipafriend` | `tipafriend`, `read` | 1 |
| `content/keyword` | `read` | 1 + 2 benannt |
| `content/collectedinfo` | `read` | 1 |
| `content/bookmark` | `bookmark` | 0 + 1 benannt |
| `content/pendinglist` | `pendinglist` | 0 + 1 benannt |
| `content/new` | `read` | 0 |
| `content/hide` | `hide` | 1 |
| `content/move` | `edit` | 1 |
| `content/reverserelatedlist` | `reverserelatedlist` | 1 + 1 benannt |
| `content/translation` | `read` | 0 |
| `content/treemenu` | `read` | 4 |
| `content/dashboard` | `dashboard` | 0 |
| `content/queued` | `edit` | 2 |

Fetch-Funktionen: `object`, `version`, `node`, `locale_list`, `locale`, `prioritized_languages`, `prioritized_language_codes`, `translation_list`, `non_translation_list`, `class`, `class_attribute_list`, `class_attribute`, `calendar`, `list`, `list_count`, `tree`, `tree_count`, `search`, `trash_count`, `trash_object_list`, `draft_count`, `draft_version_list`, `pending_count`, `pending_list`, `version_count`, `version_list`, `can_instantiate_class_list`, `class_list`, `can_instantiate_classes`, `contentobject_attributes`, `bookmarks`, `recent`, `section_list`, `tipafriend_top_list`, `view_top_list`, `collected_info_count`, `collected_info_count_list`, `collected_info_collection`, `collected_info_list`, `object_by_attribute`, `object_count_by_user_id`, `same_classattribute_node`, `keyword`, `keyword_count`, `access`, `navigation_parts`, `navigation_part`, `related_objects`, `related_objects_count`, `reverse_related_objects`, `reverse_related_objects_count`, `available_sort_fields`, `country_list`, `related_objects_ids`, `reverse_related_objects_ids`, `content_tree_menu_expiry`

### dse

`extension/sevenx_dse/modules/dse` — Richtlinien: `dse`, `dump`

| View | Braucht | Parameter |
| --- | --- | --- |
| `dse/dashboard` | `dse` | 0 |
| `dse/adminneo` | `dse` | 0 |

### error

`kernel/error`

| View | Braucht | Parameter |
| --- | --- | --- |
| `error/view` | *nichts* | 3 |

### explayouts

`extension/explayouts/modules/explayouts` — Richtlinien: `read`, `edit`

| View | Braucht | Parameter |
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

Fetch-Funktionen: `layout`, `resolve_layout`, `resolve_layout_for_node`, `rules_for_node`

### explayouts_content_browser_ui

`extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui` — Richtlinien: `read`

| View | Braucht | Parameter |
| --- | --- | --- |
| `explayouts_content_browser_ui/browser` | `read` | 1 |

### explayouts_ui

`extension/explayouts_ui/modules/explayouts_ui` — Richtlinien: `read`, `edit`

| View | Braucht | Parameter |
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

`extension/explayouts_ui_api/modules/explayouts_ui_api` — Richtlinien: `read`

| View | Braucht | Parameter |
| --- | --- | --- |
| `explayouts_ui_api/layouts` | `read` | 1 |
| `explayouts_ui_api/rules` | `read` | 1 |
| `explayouts_ui_api/blocks` | `read` | 1 |
| `explayouts_ui_api/app` | `read` | 0 |

### ezflow

`extension/ezflow/modules/ezflow` — Richtlinien: `timeline`, `edit`, `call`, `changelayout`

| View | Braucht | Parameter |
| --- | --- | --- |
| `ezflow/get` | `edit` | 0 |
| `ezflow/timeline` | `timeline` | 2 |
| `ezflow/preview` | `timeline` | 2 |
| `ezflow/zone` | `edit` | 3 |
| `ezflow/request` | `edit` | 0 + 2 benannt |
| `ezflow/push` | `edit` | 1 |
| `ezflow/block` | `call` | 2 |

Fetch-Funktionen: `waiting`, `valid`, `archived`, `valid_nodes`, `block`, `allowed_zones`

### ezie

`extension/ezie/modules/ezie`

| View | Braucht | Parameter |
| --- | --- | --- |
| `ezie/prepare` | *nichts* | 4 |
| `ezie/filter_bw` | *nichts* | 0 |
| `ezie/filter_sepia` | *nichts* | 0 |
| `ezie/filter_blur` | *nichts* | 0 |
| `ezie/filter_contrast` | *nichts* | 0 |
| `ezie/filter_brightness` | *nichts* | 0 |
| `ezie/tool_flip_hor` | *nichts* | 0 |
| `ezie/tool_flip_ver` | *nichts* | 0 |
| `ezie/tool_rotation` | *nichts* | 0 |
| `ezie/tool_levels` | *nichts* | 0 |
| `ezie/tool_saturation` | *nichts* | 0 |
| `ezie/tool_pixelate` | *nichts* | 0 |
| `ezie/tool_crop` | *nichts* | 0 |
| `ezie/tool_watermark` | *nichts* | 0 |
| `ezie/no_save_and_quit` | *nichts* | 0 |
| `ezie/save_and_quit` | *nichts* | 0 |

### ezinfo

`kernel/ezinfo` — Richtlinien: `read`

| View | Braucht | Parameter |
| --- | --- | --- |
| `ezinfo/copyright` | `read` | 0 |
| `ezinfo/about` | `read` | 0 |
| `ezinfo/is_alive` | `read` | 0 |

### ezjscore

`extension/ezjscore/modules/ezjscore` — Richtlinien: `run`, `call`

| View | Braucht | Parameter |
| --- | --- | --- |
| `ezjscore/hello` | *nichts* | 1 |
| `ezjscore/call` | `call` | 4 |
| `ezjscore/run` | `run` | 0 |

### ezmultiupload

`extension/ezmultiupload/modules/ezmultiupload`

| View | Braucht | Parameter |
| --- | --- | --- |
| `ezmultiupload/upload` | *nichts* | 1 |

### ezodf

`extension/ezodf/modules/ezodf` — Richtlinien: `import`, `export`

| View | Braucht | Parameter |
| --- | --- | --- |
| `ezodf/import` | `import` | 0 + 2 benannt |
| `ezodf/export` | `export` | 0 + 2 benannt |

### ezoe

`extension/ezoe/modules/ezoe` — Richtlinien: `relations`, `editor`, `search`, `browse`, `disable_editor`

| View | Braucht | Parameter |
| --- | --- | --- |
| `ezoe/relations` | `editor` | 6 |
| `ezoe/upload` | `editor` | 4 |
| `ezoe/tags` | `editor` | 4 |
| `ezoe/dialog` | `editor` | 3 |
| `ezoe/embed_view` | `editor` | 1 |
| `ezoe/load` | `editor` | 3 |
| `ezoe/spellcheck_rpc` | `editor` | 0 |

### flash

`extension/ezflow/modules/flash`

| View | Braucht | Parameter |
| --- | --- | --- |
| `flash/embed` | *nichts* | 1 |

### git_manager

`extension/git_manager/modules/git_manager` — Richtlinien: `git_manager`, `dump`

| View | Braucht | Parameter |
| --- | --- | --- |
| `git_manager/dashboard` | `git_manager` | 0 |
| `git_manager/commit_details` | `git_manager` | 1 |
| `git_manager/dump` | `dump` | 0 |
| `git_manager/download` | `dump` | 2 |

### googlesitemapdynamic

`extension/bcgooglesitemaps/modules/googlesitemapdynamic` — Richtlinien: `sitemap`

| View | Braucht | Parameter |
| --- | --- | --- |
| `googlesitemapdynamic/sitemap` | `sitemap` | 1 |

### hcaptcha

`extension/hcaptcha/modules/hcaptcha` — Richtlinien: `bypass_captcha`

### info-collection

`extension/sevenx_themes_media/modules/info-collection` — Richtlinien: `read`

| View | Braucht | Parameter |
| --- | --- | --- |
| `info-collection/view-modal` | `read` | 2 |
| `info-collection/submit` | `read` | 1 |

### infocollector

`kernel/infocollector` — Richtlinien: `read`

| View | Braucht | Parameter |
| --- | --- | --- |
| `infocollector/overview` | `read` | 0 + 1 benannt |
| `infocollector/collectionlist` | `read` | 1 + 1 benannt |
| `infocollector/view` | `read` | 1 |

Fetch-Funktionen: `collected_info_count`, `collected_info_count_list`, `collected_info_collection`, `collected_info_list`

### layout

`kernel/layout`

| View | Braucht | Parameter |
| --- | --- | --- |
| `layout/set` | *nichts* | 1 |

Fetch-Funktionen: `sitedesign_list`

### newsletter

`extension/cjw_newsletter/modules/newsletter` — Richtlinien: `subscribe`, `configure`, `unsubscribe`, `subscription_list_csvimport`, `subscription_list_csvimport_import`, `subscription_list_csvexport`, `subscription_list`, `subscription_view`, `user_list`, `user_view`, `user_remove`, `user_edit`, `user_create`, `preview`, `archive`, `index`, `settings`, `send`, `mailbox_item_list`, `mailbox_item_view`, `mailbox_list`, `mailbox_edit`, `blacklist_item`, `import_list`, `import_view`, `admin`

| View | Braucht | Parameter |
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

Fetch-Funktionen: `subscription_list`, `subscription_list_count`, `import_subscription_list`, `import_subscription_list_count`, `user_list`, `user_list_count`, `edition_send_item_list`, `edition_send_item_list_count`

### notification

`kernel/notification` — Richtlinien: `use`, `administrate`

| View | Braucht | Parameter |
| --- | --- | --- |
| `notification/settings` | `use` | 0 + 1 benannt |
| `notification/runfilter` | `administrate` | 0 |
| `notification/addtonotification` | `use` | 1 |

Fetch-Funktionen: `handler_list`, `digest_handlers`, `digest_items`, `event_content`, `subscribed_nodes`, `subscribed_nodes_count`

### oauth

`kernel/private/modules/oauth`

| View | Braucht | Parameter |
| --- | --- | --- |
| `oauth/authorize` | *nichts* | 0 |

### oauthadmin

`kernel/private/modules/oauthadmin`

| View | Braucht | Parameter |
| --- | --- | --- |
| `oauthadmin/list` | *nichts* | 0 |
| `oauthadmin/edit` | *nichts* | 1 |
| `oauthadmin/action` | *nichts* | 0 |
| `oauthadmin/view` | *nichts* | 1 |

### owner

`extension/ezownerchange/modules/owner` — Richtlinien: `change`

| View | Braucht | Parameter |
| --- | --- | --- |
| `owner/change` | *nichts* | 1 + 1 benannt |

### package

`kernel/package` — Richtlinien: `read`, `list`, `create`, `edit`, `remove`, `install`, `import`, `export`

| View | Braucht | Parameter |
| --- | --- | --- |
| `package/list` | `list` | 1 + 1 benannt |
| `package/upload` | `import` | 0 |
| `package/create` | `create` | 0 |
| `package/export` | `export` | 1 |
| `package/view` | `read` | 3 |
| `package/install` | `install` | 1 |
| `package/uninstall` | `install` | 1 |

Fetch-Funktionen: `list`, `maintainer_role_list`, `can_create`, `can_edit`, `can_import`, `can_install`, `can_export`, `can_read`, `can_list`, `can_remove`, `item`, `dependent_list`, `repository_list`

### paypal

`extension/ezpaypal/modules/paypal`

| View | Braucht | Parameter |
| --- | --- | --- |
| `paypal/notify_url` | *nichts* | 0 |

### pdf

`kernel/pdf` — Richtlinien: `create`, `edit`

| View | Braucht | Parameter |
| --- | --- | --- |
| `pdf/edit` | `edit` | 2 + 1 benannt |
| `pdf/list` | `edit` | 0 + 1 benannt |

### pm

`extension/ezpm/modules/pm` — Richtlinien: `list`, `blacklist`, `contacts`, `send_message`, `add_contact`, `stats`

| View | Braucht | Parameter |
| --- | --- | --- |
| `pm/message` | *nichts* | 1 |
| `pm/list` | *nichts* | 0 |
| `pm/list_inbox` | *nichts* | 0 |
| `pm/list_sent` | *nichts* | 0 |
| `pm/list_drafts` | *nichts* | 0 |
| `pm/create` | *nichts* | 0 |
| `pm/reply` | *nichts* | 1 |
| `pm/edit` | *nichts* | 1 |
| `pm/add` | *nichts* | 0 |
| `pm/action` | *nichts* | 0 |
| `pm/blacklist` | *nichts* | 0 |
| `pm/contacts` | *nichts* | 0 |

Fetch-Funktionen: `view_message`, `list`, `messages_stats`, `check_contact`

### powercontent

`extension/powercontent/modules/powercontent`

| View | Braucht | Parameter |
| --- | --- | --- |
| `powercontent/action` | *nichts* | 0 |

### recaptcha

`extension/recaptcha/modules/recaptcha` — Richtlinien: `bypass_captcha`

### refund

`extension/ezauthorize/modules/refund`

| View | Braucht | Parameter |
| --- | --- | --- |
| `refund/refund` | *nichts* | 0 + 1 benannt |

### role

`kernel/role`

| View | Braucht | Parameter |
| --- | --- | --- |
| `role/list` | *nichts* | 0 + 1 benannt |
| `role/edit` | *nichts* | 1 |
| `role/copy` | *nichts* | 1 |
| `role/policyedit` | *nichts* | 1 |
| `role/view` | *nichts* | 1 |
| `role/assign` | *nichts* | 3 |

Fetch-Funktionen: `role`

### rss

`kernel/rss` — Richtlinien: `feed`, `edit`

| View | Braucht | Parameter |
| --- | --- | --- |
| `rss/list` | `edit` | 0 + 8 benannt |
| `rss/edit_export` | `edit` | 3 |
| `rss/edit_import` | `edit` | 2 |
| `rss/feed` | `feed` | 1 |

Fetch-Funktionen: `has_export_by_node`, `export_by_node`

### search

`kernel/search`

| View | Braucht | Parameter |
| --- | --- | --- |
| `search/stats` | *nichts* | 0 + 1 benannt |

Fetch-Funktionen: `list_count`, `list`

### section

`kernel/section` — Richtlinien: `assign`, `edit`, `view`

| View | Braucht | Parameter |
| --- | --- | --- |
| `section/list` | `view or edit or assign` | 0 + 1 benannt |
| `section/view` | `view or assign` | 1 + 1 benannt |
| `section/edit` | `edit` | 1 |
| `section/assign` | `assign` | 1 |

Fetch-Funktionen: `object`, `list`, `object_list`, `object_list_count`, `roles`, `user_roles`

### settings

`kernel/settings`

| View | Braucht | Parameter |
| --- | --- | --- |
| `settings/view` | *nichts* | 2 |
| `settings/edit` | *nichts* | 5 |

### setup

`kernel/setup` — Richtlinien: `administrate`, `install`, `managecache`, `managecronjobs`, `preload`, `setup`, `system_info`

| View | Braucht | Parameter |
| --- | --- | --- |
| `setup/init` | `install` | 0 |
| `setup/cache` | `managecache` | 0 |
| `setup/cachetoolbar` | `managecache` | 0 |
| `setup/settingstoolbar` | `setup` | 0 |
| `setup/session` | `administrate` | 1 |
| `setup/info` | `system_info` | 1 |
| `setup/rad` | `setup` | 0 + 1 benannt |
| `setup/radsurvey` | `setup` | 0 + 4 benannt |
| `setup/settingsextension` | `setup` | 0 |
| `setup/contentextension` | `setup` | 0 |
| `setup/modulewizard` | `setup` | 0 |
| `setup/kerneloverride` | `setup` | 0 |
| `setup/designextension` | `setup` | 0 |
| `setup/handlerextension` | `setup` | 1 |
| `setup/workflowevent` | `setup` | 0 |
| `setup/moduleextension` | `setup` | 0 |
| `setup/datatype` | `setup` | 0 |
| `setup/templateoperator` | `setup` | 0 |
| `setup/extensions` | `setup` | 2 |
| `setup/menu` | `setup` | 0 |
| `setup/preload` | `preload` | 0 |
| `setup/preloadstream` | `preload` | 0 + 2 benannt |
| `setup/cronjobs` | `managecronjobs` | 0 |
| `setup/cronjobsstream` | `managecronjobs` | 0 + 1 benannt |
| `setup/staticcachestream` | `managecache` | 0 + 4 benannt |
| `setup/systemupgrade` | `setup` | 0 |
| `setup/toolbarlist` | `setup` | 1 |
| `setup/toolbar` | `setup` | 2 |
| `setup/menuconfig` | `setup` | 0 |
| `setup/templatelist` | `setup` | 0 + 1 benannt |
| `setup/templateview` | `setup` | 0 |
| `setup/templateedit` | `setup` | 0 |
| `setup/templatecreate` | `setup` | 0 |

Fetch-Funktionen: `version`, `alias`, `major_version`, `minor_version`, `release`, `state`, `is_development`, `database_version`, `database_release`, `edition`

### shop

`kernel/shop` — Richtlinien: `setup`, `administrate`, `buy`, `edit_status`, `setstatus`

| View | Braucht | Parameter |
| --- | --- | --- |
| `shop/add` | `buy` | 2 |
| `shop/orderview` | `buy` | 1 |
| `shop/updatebasket` | `buy` | 0 |
| `shop/basket` | `buy` | 0 + 1 benannt |
| `shop/register` | `buy` | 0 |
| `shop/userregister` | `buy` | 0 |
| `shop/wishlist` | `buy` | 0 + 1 benannt |
| `shop/orderlist` | `administrate` | 0 + 1 benannt |
| `shop/archivelist` | `administrate` | 0 + 1 benannt |
| `shop/removeorder` | `administrate` | 0 |
| `shop/archiveorder` | `administrate` | 0 |
| `shop/unarchiveorder` | `administrate` | 0 |
| `shop/customerlist` | `administrate` | 0 + 1 benannt |
| `shop/customerorderview` | `administrate` | 2 |
| `shop/statistics` | `administrate` | 2 |
| `shop/confirmorder` | `buy` | 0 |
| `shop/checkout` | `buy` | 0 |
| `shop/vattype` | `setup` | 0 |
| `shop/vatrules` | `setup` | 0 |
| `shop/editvatrule` | `setup` | 1 + 1 benannt |
| `shop/productcategories` | `setup` | 0 |
| `shop/discountgroup` | `setup` | 0 |
| `shop/discountgroupedit` | `setup` | 1 |
| `shop/discountruleedit` | `setup` | 2 |
| `shop/discountgroupview` | `setup` | 1 |
| `shop/status` | `edit_status` | 0 |
| `shop/setstatus` | `setstatus` | 0 |
| `shop/currencylist` | `setup` | 0 + 1 benannt |
| `shop/editcurrency` | `setup` | 0 + 1 benannt |
| `shop/preferredcurrency` | `buy` | 0 |
| `shop/productsoverview` | `administrate` | 0 + 2 benannt |
| `shop/setpreferredcurrency` | `buy` | 0 + 1 benannt |
| `shop/setusercountry` | `buy` | 0 + 1 benannt |

Fetch-Funktionen: `basket`, `best_sell_list`, `related_purchase`, `wish_list`, `wish_list_count`, `current_wish_list`, `order`, `order_status_history_count`, `order_status_history`, `currency_list`, `currency`, `preferred_currency_code`, `user_country`, `product_category_list`, `product_category`

### sitemaps

`extension/xrowmetadata/modules/sitemaps`

| View | Braucht | Parameter |
| --- | --- | --- |
| `sitemaps/index` | *nichts* | 0 |
| `sitemaps/robots` | *nichts* | 0 |

### state

`kernel/state` — Richtlinien: `administrate`, `assign`

| View | Braucht | Parameter |
| --- | --- | --- |
| `state/assign` | `assign` | 2 |
| `state/groups` | `administrate` | 0 + 1 benannt |
| `state/group` | `administrate` | 2 |
| `state/group_edit` | `administrate` | 1 |
| `state/view` | `administrate` | 3 |
| `state/edit` | `administrate` | 2 |

### switchlanguage

`kernel/private/modules/switchlanguage`

| View | Braucht | Parameter |
| --- | --- | --- |
| `switchlanguage/to` | *nichts* | 1 |

Fetch-Funktionen: `url_alias`

### syndication

`extension/syndication/modules/syndication` — Richtlinien: `import_object_status`, `view_export`, `edit_export`, `remove_feed`, `create_feed`, `menu`, `view_import`, `edit_import`, `view_export_info`, `create_import`, `fetch_feed`

| View | Braucht | Parameter |
| --- | --- | --- |
| `syndication/menu` | `menu` | 0 |
| `syndication/list` | `view_export` | 0 + 1 benannt |
| `syndication/import_list` | `view_export` | 0 + 1 benannt |
| `syndication/import_edit` | `edit_import` | 1 + 1 benannt |
| `syndication/pending_edit` | `import_object_status` | 1 + 2 benannt |
| `syndication/edit` | `edit_export` | 1 |
| `syndication/add_feed_source` | `edit_export` | 2 + 1 benannt |
| `syndication/list_source_filter` | `edit_export` | 1 |
| `syndication/edit_source_filter` | `edit_export` | 1 |
| `syndication/edit_import_filter` | `edit_import` | 1 |
| `syndication/import_info` | `import_view` | 1 |
| `syndication/feed_info` | `view_export_info` | 1 |

### tags

`extension/eztags/modules/tags` — Richtlinien: `read`, `dashboard`, `id`, `view`, `add`, `addsynonym`, `edit`, `editsynonym`, `delete`, `deletesynonym`, `makesynonym`, `merge`, `search`

| View | Braucht | Parameter |
| --- | --- | --- |
| `tags/treemenu` | `read` | 4 |
| `tags/dashboard` | `dashboard` | 0 + 2 benannt |
| `tags/id` | `id` | 2 + 2 benannt |
| `tags/list_objects` | `id` | 2 + 1 benannt |
| `tags/view` | `view` | 1 + 1 benannt |
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
| `tags/search` | `search` | 0 + 1 benannt |

Fetch-Funktionen: `tag`, `tags_by_keyword`, `tag_by_remote_id`, `tag_by_url`, `list`, `list_count`, `tree`, `tree_count`, `latest_tags`

### trigger

`kernel/trigger`

| View | Braucht | Parameter |
| --- | --- | --- |
| `trigger/list` | *nichts* | 0 |

### update

`extension/ezupdate/modules/update` — Richtlinien: `ezupdate`, `dump`

| View | Braucht | Parameter |
| --- | --- | --- |
| `update/dashboard` | `ezupdate` | 0 |
| `update/show` | `ezupdate` | 1 |

### url

`kernel/url`

| View | Braucht | Parameter |
| --- | --- | --- |
| `url/list` | *nichts* | 1 + 1 benannt |
| `url/view` | *nichts* | 1 + 1 benannt |
| `url/edit` | *nichts* | 1 |

Fetch-Funktionen: `list`, `list_count`

### user

`kernel/user` — Richtlinien: `login`, `password`, `preferences`, `register`, `selfedit`, `activation`

| View | Braucht | Parameter |
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
| `user/unactivated` | `activation` | 2 + 1 benannt |

Fetch-Funktionen: `current_user`, `is_logged_in`, `logged_in_count`, `anonymous_count`, `logged_in_list`, `logged_in_users`, `user_role`, `member_of`, `has_access_to`

### userpaex

`extension/ezmbpaex/modules/userpaex` — Richtlinien: `password`, `editpaex`

| View | Braucht | Parameter |
| --- | --- | --- |
| `userpaex/password` | `password` | 1 |
| `userpaex/forgotpassword` | `password` | 1 |

### visual

`kernel/visual`

| View | Braucht | Parameter |
| --- | --- | --- |
| `visual/toolbarlist` | *nichts* | 1 |
| `visual/toolbar` | *nichts* | 2 |
| `visual/menuconfig` | *nichts* | 0 |
| `visual/templatelist` | *nichts* | 0 + 1 benannt |
| `visual/templateview` | *nichts* | 0 |
| `visual/templateedit` | *nichts* | 0 + 1 benannt |
| `visual/templatecreate` | *nichts* | 0 + 4 benannt |

### websitetoolbar

`extension/ezwt/modules/websitetoolbar` — Richtlinien: `use`

| View | Braucht | Parameter |
| --- | --- | --- |
| `websitetoolbar/sort` | `use` | 1 + 5 benannt |

### workflow

`kernel/workflow`

| View | Braucht | Parameter |
| --- | --- | --- |
| `workflow/view` | *nichts* | 1 |
| `workflow/edit` | *nichts* | 3 |
| `workflow/groupedit` | *nichts* | 1 |
| `workflow/down` | *nichts* | 2 |
| `workflow/up` | *nichts* | 2 |
| `workflow/workflowlist` | *nichts* | 1 |
| `workflow/grouplist` | *nichts* | 0 |
| `workflow/process` | *nichts* | 1 |
| `workflow/run` | *nichts* | 1 |
| `workflow/event` | *nichts* | 2 |
| `workflow/processlist` | *nichts* | 0 + 1 benannt |

Fetch-Funktionen: `workflow_statuses`, `workflow_type_statuses`

### xrowextract

`extension/xrowextract/modules/xrowextract` — Richtlinien: `csv`

| View | Braucht | Parameter |
| --- | --- | --- |
| `xrowextract/csv` | `csv` | 0 |

## Was ein Template aufrufen kann

Jeder Operator und jede Funktion, die der Engine beigebracht wurden, ausgelesen aus den Autoload-Arrays, in denen sie
wirklich deklariert sind - es gibt keine INI, die sie aufführt, und deshalb wird ein nicht gefundener Operator so oft
an der falschen Stelle gesucht.

Eine als **nicht aktiv** markierte Zeile gehört zu einer Erweiterung, die hier nicht eingeschaltet ist: Der Name ist
deklariert, und nichts antwortet darauf.

### Operatoren (364)

| Name | Klasse | Deklariert in |
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
| `ezstr_replace` *(nicht aktiv)* | `MyStrReplaceOperator` | `extension/str_replace/autoloads/str_replace_controloperator.php` |
| `ezsys` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `eztagcloud` | `eZTagCloud` | `extension/ezdemo/autoloads/eztagcloud.php` |
| `eztagcloud` | `eZTagCloud` | `extension/ezwebin/autoloads/eztagcloud.php` |
| `eztags_parent_string` | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztagstemplatefunctions.php` |
| `eztagscloud` | `eZTagsCloud` | `extension/eztags/autoloads/eztagscloud.php` |
| `eztoc` | `eZTOCOperator` | `kernel/common/eztemplateautoload.php` |
| `ezurl` | `eZURLOperator` | `kernel/common/eztemplateautoload.php` |
| `false` | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php` |
| `feedreader` *(nicht aktiv)* | `eZFeedReader` | `extension/ezflow/autoloads/ezfeedreader.php` |
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
| `hcaptcha_get_html` *(nicht aktiv)* | `hCaptchaTemplateOperator` | `extension/hcaptcha/autoloads/hcaptchatemplateoperator.php` |
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
| `json` *(nicht aktiv)* | `eZJSON` | `extension/ezflow/autoloads/ezjson.php` |
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
| `pagelink` *(nicht aktiv)* | `eZPageLink` | `extension/ezflow/autoloads/ezpagelink.php` |
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
| `red5list` *(nicht aktiv)* | `eZRed5StreamListOperator` | `extension/ezflow/autoloads/ezred5streamlist.php` |
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

### Funktionen (50)

| Name | Klasse | Deklariert in |
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

## Ereignisse, auf die etwas hören kann

Registriert als `site.ini [Event] Listeners[]=<event>@<Class>::<method>`. Der leichteste Weg, Verhalten hinzuzufügen,
den es gibt: kein Modul, kein Handler, keine zu ersetzende Klasse, nur eine statische Methode, die läuft, wenn etwas
geschieht.

Ein Ereignis **filter** verwendet, was der Listener zurückgibt; einer, der vergisst, den Wert zurückzugeben, zerstört
ihn also. Ein Ereignis **notify** ignoriert ihn.

| Ereignis | Art | Angekündigt in |
| --- | --- | --- |
| `content/cache` | filter | `kernel/classes/ezcontentcachemanager.php`, `kernel/content/ezcontentoperationcollection.php`, `kernel/content/urlalias.php` |
| `content/cache/all` | notify | `kernel/classes/ezcache.php`, `kernel/classes/ezcontentcachemanager.php` |
| `content/cache/version` | notify | `kernel/content/attribute_edit.php`, `extension/ezautosave/classes/ezjscserverfunctionsautosave.php`, `extension/nxc_powercontent/modules/content/attribute_edit.php` |
| `content/class/cache` | notify | `kernel/class/edit.php`, `kernel/class/removeclass.php` |
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

## Bereits ersetzte Templates

Jedes ist eine Stelle, an der ein Template bereits überschrieben wurde - etwas, wovon man lernen kann, und etwas, womit
man kollidieren kann, denn über zwei Overrides, die auf dasselbe passen, entscheidet die Ladereihenfolge statt der
Absicht.

| Name | Ersetzt | Durch |
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

## Vollständig ersetzte Kernel-Klassen

Der schwerste Mechanismus, den es gibt, und das Erste, was man wissen muss, bevor irgendetwas anderes untersucht wird:
Eine ersetzte Kernel-Klasse ist nicht mehr der Kernel, was auch immer der Quelltext des Kernels sagt.

| Klasse | Ersetzt durch | Statt |
| --- | --- | --- |
| `eZContentFunctionCollection` | `extension/nxc_powercontent/modules/content/ezcontentfunctioncollection.php` | `kernel/content/ezcontentfunctioncollection.php` |
| `eZContentOperationCollection` | `extension/nxc_powercontent/modules/content/ezcontentoperationcollection.php` | `kernel/content/ezcontentoperationcollection.php` |
| `ezpContentPublishingBehaviour` | `extension/nxc_powercontent/modules/content/ezcontentpublishingbehaviour.php` | `kernel/content/ezcontentpublishingbehaviour.php` |


## In rad.ini deklarierte Registries

Eine Registry ist eine Liste von Implementierungen, die etwas aus seinen Einstellungen oder aus seinem Code liest: die
Spalten der Tabelle der Unterelemente, die Typen von Inhalts-Jobs, die KI-Provider oder Typen von Warteschlangen-Jobs
einer Erweiterung. Der Abschnitt **Registries** der Übersicht (`setup/radsurvey/(show)/registries`) führt jeden
Eintrag jeder Registry auf, prüft ihn und fügt die Einträge, die nicht bereits als Einstellungen gezählt sind, zur
Gesamtzahl hinzu, die `setup/rad` zeigt.

Die eigenen Registries des Kernels (Spalten der Unterelemente, Typen von Inhalts-Jobs, `exp:ini`, die Debug-Leiste, das
Audit) sind in `kernel/setup/expradsurvey.php` eingebaut. Jede andere Registry ist **in rad.ini deklariert**, damit
der Kernel nie eine Erweiterung nennen muss: `settings/rad.ini` enthält die Deklarationen des Kernels und beschreibt
das Format, und eine Erweiterung fügt ihre eigenen in `extension/<name>/settings/rad.ini.append.php` hinzu. Nur die
Deklarationen aktiver Erweiterungen werden gelesen, und eine deklarierte Registry liest nur die Einstellungen aktiver
Erweiterungen, da die anderen nicht in Kraft sind. Die beiden im Kernel-Repository mitgelieferten Erweiterungen
deklarieren ihre auf dieselbe Weise: ezjscore seine Server-Funktionen (`extension/ezjscore/settings/rad.ini.append.php`,
Schlüssel `ezjscserver`; die Übersicht leitet die **Remote-Dienste (expservices)** weiterhin aus den Klassen dieser
Registry ab) und ezoe die Engines des Online-Editors (`extension/ezoe/settings/rad.ini.append.php`, Schlüssel
`ezoeengines`). Ist ezjscore oder ezoe inaktiv, wird ihre Registry nicht aufgeführt, da ihre Einstellungen dann nicht
in Kraft sind.

Seit #390 (2026-10-12) sind die Hauptzahl von `setup/rad` und jede Gruppe der Gesamtzahl dieselben wie zu der Zeit, als
die beiden eingebaut waren (4734 auf der Referenzinstallation, auf :443 und :8080). Eine Zahl hat sich absichtlich
verschoben: Es sind 91 statt 92 Server-Funktionen, weil der Block `[ezjscServer_ezflow]` der inaktiven Erweiterung
ezflow nicht mehr gelesen wird (er war bereits eine Einstellung und fügte der Gesamtzahl nichts hinzu).

**Eine Zeile zum Zurücksetzen leert die Liste.** Wie überall in den Einstellungen vergisst eine Zeile `Registries[]`
ohne Wert (und ebenso `Sections[]` in einem Block), was frühere Dateien gesagt haben, und die Zeilen darüber in
derselben Datei: `settings/rad.ini` beginnt mit einer, eine Erweiterung tut es nie, sonst würde sie die Registries des
Kernels und die jeder anderen Erweiterung verwerfen, die vor ihr kam. Vor #390 fügte die Übersicht die Werte nach
einer solchen Zeile zu den früheren hinzu.

```ini
[RegistrySettings]
Registries[]=myext_widgets
Registries[]=myext_commands

# entries read from ini blocks: every value of Class= in [Widget_<name>] of myext.ini
[Registry_myext_widgets]
Title=My widgets
Description=The widgets of myext.ini [Widget_<name>], each a class implementing myExtWidget.
Doc=extension/myext/doc/widgets.md
TranslationContext=extension/myext/rad
Ini=myext.ini
Section=Widget_*
Entries[Class]=myExtWidget
Entries[Mode]=one-of:inline,block

# entries a class gives: name => class, name => array( 'class' => ... ), or a list of names
[Registry_myext_commands]
Title=My commands
List=myExtCommand::COMMANDS
```

| Variable | Bedeutung |
| --- | --- |
| `Title` | Was die Einträge sind, angezeigt auf `setup/rad` und `setup/radsurvey` (erforderlich). |
| `Description` | Ein Satz, angezeigt, wenn der Mauszeiger auf `setup/rad` über der Registry ruht. |
| `Doc` | Wo die Registry dokumentiert ist. |
| `TranslationContext` | Der Kontext von `Title` und `Description` in den Übersetzungen der Erweiterung (Standard `kernel/setup/rad`). |
| `Ini` | Die INI-Datei; die Kopie des Kernels, des Overrides und jeder aktiven Erweiterung wird gelesen. |
| `Section`, `Sections[]` | Ein Block (`ToolSettings`), eine Familie von Blöcken (`Tool_*`: der Rest des Namens ist der Name des Eintrags) oder ein Muster (`/^Tool_(.+)$/`). |
| `Entries[<variable>]` | Jeder Wert von `<variable>` in diesen Blöcken ist ein Eintrag und muss die angegebene Prüfung bestehen. |
| `Skip` | Eine Variable, die, auf true gesetzt, einen Block zu keinem Punkt macht (einem eingebauten). |
| `List`, `Implements` | Eine Registry, die im Code lebt: eine Klassenkonstante oder eine öffentliche statische Methode ohne Argumente, und was jede Klasse, die sie nennt, erweitern oder implementieren muss. |
| `InTotal` | `disabled`: Die Einträge werden aufgeführt und geprüft, aber nicht zur Gesamtzahl hinzugefügt, für eine Liste, die nur auf anderswo gezählte Punkte verweist. |

Die Prüfungen: ein Klassen- oder Schnittstellenname (der Wert ist eine Klasse, die ihn erweitert oder implementiert),
`class` (irgendeine Klasse), `callable` (`<class>::<method>`), `listener` (`<event>@<callback>`, wie
`[Event] Listeners[]`), `template` (ein Template, das irgendein Design hat), `section:<prefix>` (dieselbe INI-Datei hat
einen Block `[<prefix><value>]`), `in:<class>::<NAME>` (ein Schlüssel oder ein Wert dieser Konstante oder statischen
Methode), `one-of:<a>,<b>` und `name` (gezählt, nicht geprüft). Ein Eintrag, der seine Prüfung nicht besteht, wird mit
dem Grund als defekt angezeigt; ebenso eine Deklaration, die sich nicht lesen lässt: ein aufgeführter Schlüssel ohne
seinen Block, ein Block ohne `Title`, einer, der weder `Ini`, `Section` und `Entries[]` noch `List` nennt, ein `List`,
dessen Klasse oder Konstante fehlt.

Ein Eintrag, dessen Wert eine Klasse nennt, ist meist zugleich eine Einstellung, die eine Klasse nennt, und wird dort
gezählt; die Übersicht markiert ihn als „zugleich eine Einstellung, die eine Klasse nennt“ und zählt ihn nicht doppelt.
Die Deklarationen selbst sind keine Einstellungen der Installation und fehlen in der Gruppe der Einstellungen.

In dieser Installation deklariert:

| Deklariert von | Registries |
| --- | --- |
| Kernel (`settings/rad.ini`) | Event-Listener (`site.ini [Event] Listeners[]`, `[RunnableSettings] Listeners[]`), Skripte von Cronjob-Teilen (aufgeführt, nicht hinzugezählt), Quellen der Audit-Konsole (`audit.ini [AuditSourceSettings] Sources[]`) |
| ezjscore | Server-Funktionen (`ezjscore.ini [ezjscServer_<name>] Class=`) und daraus die Remote-Dienste (expservices) |
| ezoe | Engines des Online-Editors (`ezoe.ini [EditorSettings] Engines[<id>]=`, die `expOEEditorEngine` implementieren; tinymce3, tinymce8 und tiptap von exp_oe_tiptap) |
| expai | Provider-Typen, Provider, Modelle, Batch-APIs, Lanes, Werkzeuge, Provider für die Websuche, MCP-Server, Werkzeuge der MCP-Brücke (aufgeführt, nicht hinzugezählt), Dienstwerkzeuge (aufgeführt, nicht hinzugezählt), Ketten, Kettenschritte, Feldaktionen, Voreinstellungen, Anwender von Anfragen, Diff-Segmentierer, Abschnitte und Bereiche des Panels, Verlaufs-Repositorys, Regeln der Datenrichtlinie, Veto-Regeln für Anfragen |
| expqueue | Job-Typen, die Prioritätsklasse jedes Job-Typs (aufgeführt, nicht hinzugezählt), Prioritätsklassen, Ressourcen, Befehle |
| expmcp | Werkzeuge, Werkzeuge der prozessinternen Brücke (aufgeführt, nicht hinzugezählt), Werkzeugprofile, die Werkzeuge, die die Profile nennen (aufgeführt, nicht hinzugezählt), Positivlisten (aufgeführt, nicht hinzugezählt) |

Neben den Registries zählt die Übersicht jede **Richtlinien-Einschränkung** jedes Moduls (`module.php` `FunctionList`,
mit der Klasse und Methode, die ihre Werte liefern; eine, deren Klasse fehlt, ist defekt) in der Gruppe
**Richtlinien-Einschränkungen**, und ihr Durchlauf nach Ereignissen findet Ereignisse, die über eine Variable oder eine
Eigenschaft mit `ezpEvent` angekündigt werden (`$event->notify( $name, ... )` mit `$name = 'package/install/drafts';`),
nicht nur über `ezpEvent::getInstance()`.

## Verwandte Seiten

- [Erweiterungspunkte](rad-erweiterungspunkte.md)
- [Sicherheit der RAD-Werkzeuge](rad-sicherheit.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](cli-cronjob-und-view-abstraktionen.md)
- [Anleitung zu Erweiterungen](../../anleitungen/extensions.md)
