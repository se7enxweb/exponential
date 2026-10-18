---
title: Datatype (Strategy for content fields)
audience: developer
category: standards
order: 311
summary: Each datatype is a strategy for one kind of content field: how it is edited, validated, stored, indexed, compared and shown. 54 instances in Exponential.
keywords: design pattern, datatype, plugins, pattern catalogue
type: catalogue
---
# Datatype (Strategy for content fields)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Each datatype is a strategy for one kind of content field: how it is edited, validated, stored, indexed, compared and shown.

| | |
|---|---|
| Group | Plugins |
| Instances | 54 |
| Recognized by | Extends eZDataType, directly or through a parent class. |
| Extension point | content.ini [DataTypeSettings] ExtensionDirectories[] and AvailableDataTypes[]; the class registers itself with eZDataType::register(). |

## Classes by area

### extension/birthday (1)

[All patterns of extension/birthday](area-extension-birthday.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZBirthdayType` | class extends eZDataType | `extension/birthday/datatypes/ezbirthday/ezbirthdaytype.php:3` | – | – |

### extension/cjw_newsletter (4)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterEditionType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewsletteredition/cjwnewslettereditiontype.php:23` | Class description here | – |
| `CjwNewsletterListType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewsletterlist/cjwnewsletterlisttype.php:23` | Handles cjw_newsletter_list | – |
| `CjwNewsletterListVirtualType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewsletterlistvirtual/cjwnewsletterlistvirtualtype.php:25` | Handles cjw_newsletter_list_virtual | – |
| `CjwNewsletterSubscriptionType` | class extends eZDataType | `extension/cjw_newsletter/datatypes/cjwnewslettersubscription/cjwnewslettersubscriptiontype.php:23` | Class description here | – |

### extension/enhancedezbinaryfile (1)

[All patterns of extension/enhancedezbinaryfile](area-extension-enhancedezbinaryfile.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `EnhancedeZBinaryFileType` | class extends eZDataType | `extension/enhancedezbinaryfile/datatypes/enhancedezbinaryfile/enhancedezbinaryfiletype.php:34` | – | – |

### extension/enhancedselection2 (1)

[All patterns of extension/enhancedselection2](area-extension-enhancedselection2.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `SckEnhancedSelectionType` | class extends eZDataType | `extension/enhancedselection2/datatypes/sckenhancedselection/sckenhancedselectiontype.php:3` | – | – |

### extension/exp_enhanced_link (1)

[All patterns of extension/exp_enhanced_link](area-extension-exp-enhanced-link.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ngEnhancedLinkType` | class extends eZDataType | `extension/exp_enhanced_link/datatypes/ngenhancedlink/ngenhancedlinktype.php:5` | – | – |

### extension/ezflow (1)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPageType` | class extends eZDataType | `extension/ezflow/datatypes/ezpage/ezpagetype.php:28` | – | – |

### extension/ezgmaplocation (1)

[All patterns of extension/ezgmaplocation](area-extension-ezgmaplocation.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZGmapLocationType` | class extends eZDataType | `extension/ezgmaplocation/datatypes/ezgmaplocation/ezgmaplocationtype.php:39` | Class providing the eZGmapLocationType datatype. | – |

### extension/ezmbpaex (1)

[All patterns of extension/ezmbpaex](area-extension-ezmbpaex.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpaextype` | class extends eZDataType | `extension/ezmbpaex/datatypes/ezpaex/ezpaextype.php:16` | Handles the datatype used for password expiration. | – |

### extension/ezstarrating (1)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsrRatingType` | class extends eZDataType | `extension/ezstarrating/datatypes/ezsrrating/ezsrratingtype.php:27` | – | – |

### extension/eztags (1)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTagsType` | class extends eZDataType | `extension/eztags/datatypes/eztags/eztagstype.php:6` | eZTagsType class implements the eztags datatype | – |

### extension/hcaptcha (1)

[All patterns of extension/hcaptcha](area-extension-hcaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `hcaptchaType` | class extends eZDataType | `extension/hcaptcha/datatypes/hcaptcha/hcaptchatype.php:31` | hcaptcha extension for Exponential Written by Bruce Morrison | – |

### extension/ngclasslist (1)

[All patterns of extension/ngclasslist](area-extension-ngclasslist.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `NgClassListType` | class extends eZDataType | `extension/ngclasslist/datatypes/ngclasslist/ngclasslisttype.php:6` | NgClassListType class implements the ngclasslist datatype | – |

### extension/recaptcha (1)

[All patterns of extension/recaptcha](area-extension-recaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `recaptchaType` | class extends eZDataType | `extension/recaptcha/datatypes/recaptcha/recaptchatype.php:31` | reCAPTCHA extension for Exponential Written by Bruce Morrison | – |

### extension/sevenx_authentication_2fa (1)

[All patterns of extension/sevenx_authentication_2fa](area-extension-sevenx-authentication-2fa.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxAuthentication2faType` | class extends eZDataType | `extension/sevenx_authentication_2fa/datatypes/sevenxauthentication2fa/sevenxauthentication2fatype.php:17` | – | – |

### extension/syndication (1)

[All patterns of extension/syndication](area-extension-syndication.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZSyndicateType` | class extends eZBooleanType | `extension/syndication/datatypes/ezsyndicate/ezsyndicatetype.php:47` | – | – |

### extension/xrowmetadata (1)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowMetaDataType` | class extends eZDataType | `extension/xrowmetadata/datatypes/xrowmetadata/xrowmetadatatype.php:3` | – | – |

### kernel/classes/datatypes (35)

[All patterns of kernel/classes/datatypes](area-kernel-classes-datatypes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
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
