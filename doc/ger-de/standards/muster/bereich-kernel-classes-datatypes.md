---
title: Muster in kernel/classes/datatypes
audience: developer
category: standards
order: 490
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/datatypes mit den Entwurfsmustern.
keywords: kernel/classes/datatypes, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/datatypes

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/datatypes` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (81)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expCountryGroups` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezcountry/expcountrygroups.php:24` | Die Länderliste, wie man daraus auswählt: zuerst die Gruppen aus settings/countrygroups.ini (standardmäßig eine Gruppe der meistgenutzten Länder, in gereihter R… |
| `eZAuthor` | class | – | `kernel/classes/datatypes/ezauthor/ezauthor.php:29` | – |
| `eZAuthorType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezauthor/ezauthortype.php:19` | – |
| `eZBinaryFile` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfile.php:19` | – |
| `eZBinaryFileType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php:19` | – |
| `eZBooleanType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezboolean/ezbooleantype.php:19` | – |
| `eZCountryType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezcountry/ezcountrytype.php:23` | – |
| `eZDateTimeType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezdatetime/ezdatetimetype.php:19` | – |
| `eZDateType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezdate/ezdatetype.php:19` | – |
| `eZEmailType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezemail/ezemailtype.php:19` | – |
| `eZEnum` | class | – | `kernel/classes/datatypes/ezenum/ezenum.php:19` | – |
| `eZEnumObjectValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezenum/ezenumobjectvalue.php:18` | – |
| `eZEnumType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezenum/ezenumtype.php:18` | – |
| `eZEnumValue` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezenum/ezenumvalue.php:19` | – |
| `eZFloatType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezfloat/ezfloattype.php:19` | – |
| `eZForgotPassword` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezuser/ezforgotpassword.php:19` | – |
| `eZIdentifierType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezidentifier/ezidentifiertype.php:19` | – |
| `eZImageAliasHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php:28` | – |
| `eZImageFile` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezimage/ezimagefile.php:19` | – |
| `eZImageType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezimage/ezimagetype.php:26` | – |
| `eZIniSettingType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezinisetting/ezinisettingtype.php:22` | – |
| `eZIntegerType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezinteger/ezintegertype.php:24` | – |
| `eZISBN10To13Converter` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/datatypes/ezisbn/ezisbn10to13converter.php:12` | – |
| `eZISBN13` | class | – | `kernel/classes/datatypes/ezisbn/ezisbn13.php:21` | – |
| `eZISBNGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezisbn/ezisbngroup.php:24` | – |
| `eZISBNGroupRange` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezisbn/ezisbngrouprange.php:26` | – |
| `eZISBNRegistrantRange` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezisbn/ezisbnregistrantrange.php:30` | – |
| `eZISBNType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezisbn/ezisbntype.php:19` | – |
| `eZKeyword` | class | – | `kernel/classes/datatypes/ezkeyword/ezkeyword.php:19` | – |
| `eZKeywordType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezkeyword/ezkeywordtype.php:21` | – |
| `eZLDAPUser` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezuser/ezldapuser.php:20` | – |
| `eZMatrix` | class | – | `kernel/classes/datatypes/ezmatrix/ezmatrix.php:19` | – |
| `eZMatrixDefinition` | class | – | `kernel/classes/datatypes/ezmatrix/ezmatrixdefinition.php:19` | – |
| `eZMatrixType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezmatrix/ezmatrixtype.php:19` | – |
| `eZMedia` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezmedia/ezmedia.php:19` | – |
| `eZMediaType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezmedia/ezmediatype.php:19` | – |
| `eZMultiOption` | class | – | `kernel/classes/datatypes/ezmultioption/ezmultioption.php:114` | – |
| `eZMultiOption2` | class | – | `kernel/classes/datatypes/ezmultioption2/ezmultioption2.php:17` | – |
| `eZMultiOption2Type` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezmultioption2/ezmultioption2type.php:33` | – |
| `eZMultiOptionType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezmultioption/ezmultioptiontype.php:33` | – |
| `eZMultiPrice` | class extends eZSimplePrice | – | `kernel/classes/datatypes/ezmultiprice/ezmultiprice.php:32` | – |
| `eZMultiPriceType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezmultiprice/ezmultipricetype.php:19` | – |
| `eZObjectRelationListType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezobjectrelationlist/ezobjectrelationlisttype.php:26` | – |
| `eZObjectRelationType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezobjectrelation/ezobjectrelationtype.php:19` | – |
| `eZOption` | class | – | `kernel/classes/datatypes/ezoption/ezoption.php:29` | – |
| `eZOptionType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezoption/ezoptiontype.php:19` | – |
| `eZPackageType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezpackage/ezpackagetype.php:20` | – |
| `eZPDFParser` | class | – | `kernel/classes/datatypes/ezbinaryfile/plugins/ezpdfparser.php:19` | – |
| `eZPDFXMLOutput` | class extends eZXHTMLXMLOutput | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/datatypes/ezxmltext/handlers/output/ezpdfxmloutput.php:12` | – |
| `eZPlainTextParser` | class | – | `kernel/classes/datatypes/ezbinaryfile/plugins/ezplaintextparser.php:19` | – |
| `eZPlainXMLOutput` | class extends eZXMLOutputHandler | – | `kernel/classes/datatypes/ezxmltext/handlers/output/ezplainxmloutput.php:12` | – |
| `eZPrice` | class extends eZSimplePrice | – | `kernel/classes/datatypes/ezprice/ezprice.php:19` | – |
| `eZPriceType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezprice/ezpricetype.php:19` | – |
| `eZProductCategoryType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezproductcategory/ezproductcategorytype.php:19` | – |
| `eZRangeOption` | class | – | `kernel/classes/datatypes/ezrangeoption/ezrangeoption.php:19` | – |
| `eZRangeOptionType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezrangeoption/ezrangeoptiontype.php:18` | – |
| `eZSelectionType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezselection/ezselectiontype.php:21` | – |
| `eZSimplifiedXMLEditOutput` | class | – | `kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmleditoutput.php:13` | – |
| `eZSimplifiedXMLInput` | class extends eZXMLInputHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinput.php:12` | – |
| `eZSimplifiedXMLInputParser` | class extends eZXMLInputParser | – | `kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinputparser.php:13` | – |
| `eZStringType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezstring/ezstringtype.php:30` | – |
| `eZSubtreeSubscriptionType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezsubtreesubscription/ezsubtreesubscriptiontype.php:18` | – |
| `eZTextFileUser` | class extends eZUser | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/datatypes/ezuser/eztextfileuser.php:27` | – |
| `eZTextType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/eztext/eztexttype.php:19` | – |
| `eZTimeType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/eztime/eztimetype.php:19` | – |
| `eZURL` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Registry](registry.md) | `kernel/classes/datatypes/ezurl/ezurl.php:23` | – |
| `eZURLObjectLink` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezurl/ezurlobjectlink.php:19` | – |
| `eZURLType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezurl/ezurltype.php:20` | – |
| `eZUser` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Singleton / instance()](singleton.md) | `kernel/classes/datatypes/ezuser/ezuser.php:21` | – |
| `eZUserAccountKey` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezuser/ezuseraccountkey.php:19` | – |
| `eZUserLoginHandler` | class | [Zuständigkeitskette (Chain of Responsibility)](kette.md), [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/datatypes/ezuser/ezuserloginhandler.php:19` | – |
| `eZUserSetting` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/datatypes/ezuser/ezusersetting.php:18` | – |
| `eZUserType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezuser/ezusertype.php:21` | – |
| `eZWordParser` | class | – | `kernel/classes/datatypes/ezbinaryfile/plugins/ezwordparser.php:19` | – |
| `eZXHTMLXMLOutput` | class extends eZXMLOutputHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php:12` | – |
| `eZXMLInputHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/datatypes/ezxmltext/ezxmlinputhandler.php:19` | – |
| `eZXMLInputParser` | class | – | `kernel/classes/datatypes/ezxmltext/ezxmlinputparser.php:25` | – |
| `eZXMLOutputHandler` | class | [Strategie / Handler](strategie-handler.md) | `kernel/classes/datatypes/ezxmltext/ezxmloutputhandler.php:20` | – |
| `eZXMLSchema` | class | [Singleton / instance()](singleton.md) | `kernel/classes/datatypes/ezxmltext/ezxmlschema.php:12` | – |
| `eZXMLText` | class | – | `kernel/classes/datatypes/ezxmltext/ezxmltext.php:19` | – |
| `eZXMLTextType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `kernel/classes/datatypes/ezxmltext/ezxmltexttype.php:83` | – |

## Ereignis-Hooks (3)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `image/purgeAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php:741` |
| `image/removeAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagealiashandler.php:811` |
| `image/trashAliases` | notify | `kernel/classes/datatypes/ezimage/ezimagetype.php:120` |
