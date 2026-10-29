---
title: Extension points in site.ini
audience: developer, admin
category: examples
summary: Settings of site.ini that list handlers, classes or directories an extension can add to (30, found by name).
keywords: site.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Extension points in site.ini

Settings of `settings/site.ini` whose name says they hold handlers, classes, filters, providers or directories that an extension can add to or replace. **Found by the setting's name: a heuristic.** Read the setting's comment (linked) before relying on it.

An extension adds to a list in its own `settings/site.ini.append.php` (active extensions' settings are read after the kernel's and before `settings/override`):

## Cache_restRoutes purgeClass

Shipped: `ezpRestRoutesCacheClear`. [The setting](../ini-settings/site/Cache_restRoutes.md#purgeclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Cache_restRoutes]
purgeClass=ezpRestRoutesCacheClear    # replace with your class or value
*/ ?>
```

## Cache_exphelpcontent purgeClass

Shipped: `expHelpContentRepository`. [The setting](../ini-settings/site/Cache_exphelpcontent.md#purgeclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Cache_exphelpcontent]
purgeClass=expHelpContentRepository    # replace with your class or value
*/ ?>
```

## DatabaseSettings DatabaseImplementation

Shipped: `ezmysqli`. [The setting](../ini-settings/site/DatabaseSettings.md#databaseimplementation).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[DatabaseSettings]
DatabaseImplementation=ezmysqli    # replace with your class or value
*/ ?>
```

## Event Listeners

Shipped: an empty list. [The setting](../ini-settings/site/Event.md#listeners).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Event]
Listeners[]=expExampleListener    # your class or value, added to the list
*/ ?>
```

## RunnableSettings Implementation

Shipped: an empty list. [The setting](../ini-settings/site/RunnableSettings.md#implementation).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RunnableSettings]
Implementation[]=expExampleImplementation    # your class or value, added to the list
*/ ?>
```

## RunnableSettings Listeners

Shipped: an empty list. [The setting](../ini-settings/site/RunnableSettings.md#listeners).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RunnableSettings]
Listeners[]=expExampleListener    # your class or value, added to the list
*/ ?>
```

## ExtensionSettings ExtensionDirectory

Shipped: `extension`. [The setting](../ini-settings/site/ExtensionSettings.md#extensiondirectory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ExtensionSettings]
ExtensionDirectory=extension    # replace with your class or value
*/ ?>
```

## ExtensionSettings AdditionalExtensionDirectories

Shipped: an empty list. [The setting](../ini-settings/site/ExtensionSettings.md#additionalextensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ExtensionSettings]
AdditionalExtensionDirectories[]=expExampleAdditionalExtensionDirectorie    # your class or value, added to the list
*/ ?>
```

## Session Handler

Shipped: empty. [The setting](../ini-settings/site/Session.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Session]
Handler=    # replace with your class or value
*/ ?>
```

## Session SessionNameHandler

Shipped: `default`. [The setting](../ini-settings/site/Session.md#sessionnamehandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[Session]
SessionNameHandler=default    # replace with your class or value
*/ ?>
```

## URLTranslator FilterClasses

Shipped: a list of 1: `eZURLAliasFilterAppendNodeID`. [The setting](../ini-settings/site/URLTranslator.md#filterclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[URLTranslator]
FilterClasses[]=expExampleFilterClasse    # your class or value, added to the list
*/ ?>
```

## AppendNodeIDFilterSettings ApplyOnClass

Shipped: an empty list. [The setting](../ini-settings/site/AppendNodeIDFilterSettings.md#applyonclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[AppendNodeIDFilterSettings]
ApplyOnClass[]=expExampleApplyOnClas    # your class or value, added to the list
*/ ?>
```

## SiteSettings ErrorHandler

Shipped: `displayerror`. [The setting](../ini-settings/site/SiteSettings.md#errorhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SiteSettings]
ErrorHandler=displayerror    # replace with your class or value
*/ ?>
```

## SearchSettings DelayedIndexingClassList

Shipped: an empty list. [The setting](../ini-settings/site/SearchSettings.md#delayedindexingclasslist).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SearchSettings]
DelayedIndexingClassList[]=expExampleDelayedIndexingClass    # your class or value, added to the list
*/ ?>
```

## SearchSettings ExtensionDirectories

Shipped: an empty list. [The setting](../ini-settings/site/SearchSettings.md#extensiondirectories).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SearchSettings]
ExtensionDirectories[]=expExampleExtensionDirectorie    # your class or value, added to the list
*/ ?>
```

## UserSettings WeakPasswordCheck

Shipped: `enabled`. [The setting](../ini-settings/site/UserSettings.md#weakpasswordcheck).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[UserSettings]
WeakPasswordCheck=enabled    # replace with your class or value
*/ ?>
```

## UserSettings LoginHandler

Shipped: a list of 1: `standard`. [The setting](../ini-settings/site/UserSettings.md#loginhandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[UserSettings]
LoginHandler[]=expExampleLoginHandler    # your class or value, added to the list
*/ ?>
```

## UserSettings ExtensionDirectory

Shipped: an empty list. [The setting](../ini-settings/site/UserSettings.md#extensiondirectory).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[UserSettings]
ExtensionDirectory[]=expExampleExtensionDirectory    # your class or value, added to the list
*/ ?>
```

## PasswordSettings MinCharacterClasses

Shipped: `0`. [The setting](../ini-settings/site/PasswordSettings.md#mincharacterclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[PasswordSettings]
MinCharacterClasses=0    # replace with your class or value
*/ ?>
```

## SignInSecondFactorSettings Handlers

Shipped: an empty list. [The setting](../ini-settings/site/SignInSecondFactorSettings.md#handlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SignInSecondFactorSettings]
Handlers[]=expExampleHandler    # your class or value, added to the list
*/ ?>
```

## PasskeySettings PolicyHandlers

Shipped: an empty list. [The setting](../ini-settings/site/PasskeySettings.md#policyhandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[PasskeySettings]
PolicyHandlers[]=expExamplePolicyHandler    # your class or value, added to the list
*/ ?>
```

## ReauthSettings CodeHandlers

Shipped: a list of 1: `expReauthTotpCode`. [The setting](../ini-settings/site/ReauthSettings.md#codehandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ReauthSettings]
CodeHandlers[]=expExampleCodeHandler    # your class or value, added to the list
*/ ?>
```

## ReauthSettings TriggerHandlers

Shipped: an empty list. [The setting](../ini-settings/site/ReauthSettings.md#triggerhandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ReauthSettings]
TriggerHandlers[]=expExampleTriggerHandler    # your class or value, added to the list
*/ ?>
```

## SiteAccessSettings MobileDeviceFilterClass

Shipped: `ezpMobileDeviceRegexpFilter`. [The setting](../ini-settings/site/SiteAccessSettings.md#mobiledevicefilterclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[SiteAccessSettings]
MobileDeviceFilterClass=ezpMobileDeviceRegexpFilter    # replace with your class or value
*/ ?>
```

## RoleSettings LimitationHandlers

Shipped: an empty list. [The setting](../ini-settings/site/RoleSettings.md#limitationhandlers).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RoleSettings]
LimitationHandlers[]=expExampleLimitationHandler    # your class or value, added to the list
*/ ?>
```

## RegionalSettings LanguageSwitcherClass

Shipped: `ezpLanguageSwitcher`. [The setting](../ini-settings/site/RegionalSettings.md#languageswitcherclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RegionalSettings]
LanguageSwitcherClass=ezpLanguageSwitcher    # replace with your class or value
*/ ?>
```

## ContentSettings StaticCacheHandler

Shipped: `eZStaticCache`. [The setting](../ini-settings/site/ContentSettings.md#staticcachehandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ContentSettings]
StaticCacheHandler=eZStaticCache    # replace with your class or value
*/ ?>
```

## ContentSettings ContentClassEditHandler

Shipped: `eZContentClassEditHandler`. [The setting](../ini-settings/site/ContentSettings.md#contentclassedithandler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[ContentSettings]
ContentClassEditHandler=eZContentClassEditHandler    # replace with your class or value
*/ ?>
```

## MailSettings Transport

Shipped: `sendmail`. [The setting](../ini-settings/site/MailSettings.md#transport).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[MailSettings]
Transport=sendmail    # replace with your class or value
*/ ?>
```

## RSSSettings DefaultFeedItemClasses

Shipped: an empty list. [The setting](../ini-settings/site/RSSSettings.md#defaultfeeditemclasses).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/site.ini.append.php
[RSSSettings]
DefaultFeedItemClasses[]=expExampleDefaultFeedItemClasse    # your class or value, added to the list
*/ ?>
```

## Related

- [All extension points](README.md)
- [An extension skeleton](../cookbook/extension-skeleton.md)
- [The blocks of site.ini](../ini-settings/site/README.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
