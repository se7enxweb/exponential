---
title: Datentyp (Strategie für Inhaltsfelder)
audience: developer
category: standards
order: 311
summary: "Jeder Datentyp ist eine Strategie für eine Art von Inhaltsfeld: wie es bearbeitet, validiert, gespeichert, indexiert, verglichen und angezeigt wird. 54 Instanzen in Exponential."
keywords: Entwurfsmuster, Datentyp, Plugins, Musterkatalog, design pattern, datatype, pattern catalogue
type: catalogue
---
# Datentyp (Strategie für Inhaltsfelder)

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jeder Datentyp ist eine Strategie für eine Art von Inhaltsfeld: wie es bearbeitet, validiert, gespeichert, indexiert, verglichen und angezeigt wird.

|  |  |
| --- | --- |
| Gruppe | Plugins |
| Instanzen | 54 |
| Erkannt an | Erweitert eZDataType, direkt oder über eine Elternklasse. |
| Erweiterungspunkt | content.ini [DataTypeSettings] ExtensionDirectories[] und AvailableDataTypes[]; die Klasse registriert sich selbst mit eZDataType::register(). |

## Klassen nach Bereich

### extension/birthday (1)

[Alle Muster von extension/birthday](bereich-extension-birthday.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZBirthdayType` | class extends eZDataType | `extension/birthday/datatypes/ezbirthday/ezbirthdaytype.php:3` | – | – |

### extension/cjw_newsletter (4)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterEditionType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewsletteredition/cjwnewslettereditiontype.php:23` | Klassenbeschreibung hier | – |
| `CjwNewsletterListType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewsletterlist/cjwnewsletterlisttype.php:23` | Verarbeitet cjw_newsletter_list | – |
| `CjwNewsletterListVirtualType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewsletterlistvirtual/cjwnewsletterlistvirtualtype.php:25` | Verarbeitet cjw_newsletter_list_virtual | – |
| `CjwNewsletterSubscriptionType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewslettersubscription/cjwnewslettersubscriptiontype.php:23` | Klassenbeschreibung hier | – |

### extension/enhancedezbinaryfile (1)

[Alle Muster von extension/enhancedezbinaryfile](bereich-extension-enhancedezbinaryfile.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `EnhancedeZBinaryFileType` | class extends eZDataType | `extension/enhancedezbinaryfile/datatypes/enhancedezbinaryfile/enhancedezbinaryfiletype.php:34` | – | – |

### extension/enhancedselection2 (1)

[Alle Muster von extension/enhancedselection2](bereich-extension-enhancedselection2.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `SckEnhancedSelectionType` | class extends eZDataType | `extension/enhancedselection2/datatypes/sckenhancedselection/sckenhancedselectiontype.php:3` | – | – |

### extension/exp_enhanced_link (1)

[Alle Muster von extension/exp_enhanced_link](bereich-extension-exp-enhanced-link.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ngEnhancedLinkType` | class extends eZDataType | `extension/exp_enhanced_link/datatypes/ngenhancedlink/ngenhancedlinktype.php:5` | – | – |

### extension/ezflow (1)

[Alle Muster von extension/ezflow](bereich-extension-ezflow.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZPageType` | class extends eZDataType | `extension/ezflow/datatypes/ezpage/ezpagetype.php:28` | – | – |

### extension/ezgmaplocation (1)

[Alle Muster von extension/ezgmaplocation](bereich-extension-ezgmaplocation.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZGmapLocationType` | class extends eZDataType | `extension/ezgmaplocation/datatypes/ezgmaplocation/ezgmaplocationtype.php:39` | Klasse, die den Datentyp eZGmapLocationType bereitstellt. | – |

### extension/ezmbpaex (1)

[Alle Muster von extension/ezmbpaex](bereich-extension-ezmbpaex.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezpaextype` | class extends eZDataType | `extension/ezmbpaex/datatypes/ezpaex/ezpaextype.php:16` | Verarbeitet den Datentyp, der für den Passwortablauf verwendet wird. | – |

### extension/ezstarrating (1)

[Alle Muster von extension/ezstarrating](bereich-extension-ezstarrating.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezsrRatingType` | class extends eZDataType | `extension/ezstarrating/datatypes/ezsrrating/ezsrratingtype.php:27` | – | – |

### extension/eztags (1)

[Alle Muster von extension/eztags](bereich-extension-eztags.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZTagsType` | class extends eZDataType | `extension/eztags/datatypes/eztags/eztagstype.php:6` | Die Klasse eZTagsType implementiert den Datentyp eztags | – |

### extension/hcaptcha (1)

[Alle Muster von extension/hcaptcha](bereich-extension-hcaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `hcaptchaType` | class extends eZDataType | `extension/hcaptcha/datatypes/hcaptcha/hcaptchatype.php:31` | hcaptcha-Extension für Exponential, geschrieben von Bruce Morrison | – |

### extension/ngclasslist (1)

[Alle Muster von extension/ngclasslist](bereich-extension-ngclasslist.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `NgClassListType` | class extends eZDataType | `extension/ngclasslist/datatypes/ngclasslist/ngclasslisttype.php:6` | Die Klasse NgClassListType implementiert den Datentyp ngclasslist | – |

### extension/recaptcha (1)

[Alle Muster von extension/recaptcha](bereich-extension-recaptcha.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `recaptchaType` | class extends eZDataType | `extension/recaptcha/datatypes/recaptcha/recaptchatype.php:31` | reCAPTCHA-Extension für Exponential, geschrieben von Bruce Morrison | – |

### extension/sevenx_authentication_2fa (1)

[Alle Muster von extension/sevenx_authentication_2fa](bereich-extension-sevenx-authentication-2fa.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `sevenxAuthentication2faType` | class extends eZDataType | `extension/sevenx_authentication_2fa/datatypes/sevenxauthentication2fa/sevenxauthentication2fatype.php:17` | – | – |

### extension/syndication (1)

[Alle Muster von extension/syndication](bereich-extension-syndication.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZSyndicateType` | class extends eZBooleanType | `extension/syndication/datatypes/ezsyndicate/ezsyndicatetype.php:47` | – | – |

### extension/xrowmetadata (1)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowMetaDataType` | class extends eZDataType | `extension/xrowmetadata/datatypes/xrowmetadata/xrowmetadatatype.php:3` | – | – |

### kernel/classes/datatypes (35)

[Alle Muster von kernel/classes/datatypes](bereich-kernel-classes-datatypes.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZAuthorType` | class extends eZDataType | `kernel/classes/datatypes/ezauthor/ezauthortype.php:19` | – | – |
| `eZBinaryFileType` | class extends eZDataType | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php:19` | – | – |
| `eZBooleanType` | class extends eZDataType | `kernel/classes/datatypes/ezboolean/ezbooleantype.php:19` | – | – |
| `eZCountryType` | class extends eZDataType | `kernel/classes/datatypes/ezcountry/ezcountrytype.php:23` | – | – |
| `eZDateTimeType` | class extends eZDataType | `kernel/classes/datatypes/ezdatetime/ezdatetimetype.php:19` | – | – |
| `eZDateType` | class extends eZDataType | `kernel/classes/datatypes/ezdate/ezdatetype.php:19` | – | – |
| `eZEmailType` | class extends eZDataType | `kernel/classes/datatypes/ezemail/ezemailtype.php:19` | – | – |
| `eZEnumType` | class extends eZDataType | `kernel/classes/datatypes/ezenum/ezenumtype.php:18` | – | – |
| `eZFloatType` | class extends eZDataType | `kernel/classes/datatypes/ezfloat/ezfloattype.php:19` | – | – |
| `eZIdentifierType` | class extends eZDataType | `kernel/classes/datatypes/ezidentifier/ezidentifiertype.php:19` | – | – |
| `eZImageType` | class extends eZDataType | `kernel/classes/datatypes/ezimage/ezimagetype.php:26` | – | – |
| `eZIniSettingType` | class extends eZDataType | `kernel/classes/datatypes/ezinisetting/ezinisettingtype.php:22` | – | – |
| `eZIntegerType` | class extends eZDataType | `kernel/classes/datatypes/ezinteger/ezintegertype.php:24` | – | – |
| `eZISBNType` | class extends eZDataType | `kernel/classes/datatypes/ezisbn/ezisbntype.php:19` | – | – |
| `eZKeywordType` | class extends eZDataType | `kernel/classes/datatypes/ezkeyword/ezkeywordtype.php:21` | – | – |
| `eZMatrixType` | class extends eZDataType | `kernel/classes/datatypes/ezmatrix/ezmatrixtype.php:19` | – | – |
| `eZMediaType` | class extends eZDataType | `kernel/classes/datatypes/ezmedia/ezmediatype.php:19` | – | – |
| `eZMultiOption2Type` | class extends eZDataType | `kernel/classes/datatypes/ezmultioption2/ezmultioption2type.php:33` | – | – |
| `eZMultiOptionType` | class extends eZDataType | `kernel/classes/datatypes/ezmultioption/ezmultioptiontype.php:33` | – | – |
| `eZMultiPriceType` | class extends eZDataType | `kernel/classes/datatypes/ezmultiprice/ezmultipricetype.php:19` | – | – |
| `eZObjectRelationListType` | class extends eZDataType | `kernel/classes/datatypes/ezobjectrelationlist/ezobjectrelationlisttype.php:26` | – | – |
| `eZObjectRelationType` | class extends eZDataType | `kernel/classes/datatypes/ezobjectrelation/ezobjectrelationtype.php:19` | – | – |
| `eZOptionType` | class extends eZDataType | `kernel/classes/datatypes/ezoption/ezoptiontype.php:19` | – | – |
| `eZPackageType` | class extends eZDataType | `kernel/classes/datatypes/ezpackage/ezpackagetype.php:20` | – | – |
| `eZPriceType` | class extends eZDataType | `kernel/classes/datatypes/ezprice/ezpricetype.php:19` | – | – |
| `eZProductCategoryType` | class extends eZDataType | `kernel/classes/datatypes/ezproductcategory/ezproductcategorytype.php:19` | – | – |
| `eZRangeOptionType` | class extends eZDataType | `kernel/classes/datatypes/ezrangeoption/ezrangeoptiontype.php:18` | – | – |
| `eZSelectionType` | class extends eZDataType | `kernel/classes/datatypes/ezselection/ezselectiontype.php:21` | – | – |
| `eZStringType` | class extends eZDataType | `kernel/classes/datatypes/ezstring/ezstringtype.php:30` | – | – |
| `eZSubtreeSubscriptionType` | class extends eZDataType | `kernel/classes/datatypes/ezsubtreesubscription/ezsubtreesubscriptiontype.php:18` | – | – |
| `eZTextType` | class extends eZDataType | `kernel/classes/datatypes/eztext/eztexttype.php:19` | – | – |
| `eZTimeType` | class extends eZDataType | `kernel/classes/datatypes/eztime/eztimetype.php:19` | – | – |
| `eZURLType` | class extends eZDataType | `kernel/classes/datatypes/ezurl/ezurltype.php:20` | – | – |
| `eZUserType` | class extends eZDataType | `kernel/classes/datatypes/ezuser/ezusertype.php:21` | – | – |
| `eZXMLTextType` | class extends eZDataType | `kernel/classes/datatypes/ezxmltext/ezxmltexttype.php:83` | – | – |
