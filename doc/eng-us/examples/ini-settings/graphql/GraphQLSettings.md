---
title: graphql.ini [GraphQLSettings]
audience: developer, admin
category: examples
summary: "The settings of [GraphQLSettings] in graphql.ini: Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList ..."
keywords: graphql.ini, GraphQLSettings, INI, setting, Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList, Introspection, MaxDepth, MaxComplexity, MaxListLimit, DefaultListLimit, PersistedQueries, AutomaticPersistedQueries, MaxPersistedQueries, PersistedQueriesOnly, PersistedQueryDirectoryList, MaxQueryLength, ResponseCache, ResponseCacheTtl, CredentialCacheTtl, RateLimit, RateLimitAnonymous, RateLimitWindow, SchemaExtensionList, ImageAliasList, MutationsEnabled, AnonymousMutations, MaxUploadSize
type: generated
generated: exp:help:examples
---
# graphql.ini [GraphQLSettings]

The block `[GraphQLSettings]` of `settings/graphql.ini`, with 36 setting(s).

Never edit `settings/graphql.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/graphql.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/graphql.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/graphql.ini.append.php` in an extension (read when the extension is active).

## Enabled

> enabled or disabled (the default). Off until it is switched on, for every siteaccess or in the settings of one  
> (settings/siteaccess/<siteaccess>/graphql.ini.append.php). Disabled: /api/graphql answers 404, the session end  
> point graphql/query answers 404, nothing else changes.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Enabled=disabled
*/ ?>
```

## SiteAccessList

> The siteaccesses that answer when Enabled=enabled; empty: every siteaccess. A request is answered by the  
> siteaccess it matches (host, path or the X-Siteaccess header, as every REST request).  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "SiteAccessList[]" first replaces the shipped list; without it the items are added
SiteAccessList[]
*/ ?>
```

## LibraryPath

> Where webonyx/graphql-php 15 is loaded from when the installation's Composer autoload has no GraphQL\\GraphQL: a  
> directory with vendor/autoload.php (a Composer directory that requires webonyx/graphql-php), or the package  
> itself (the directory holding src/GraphQL.php). Relative to the installation root, or absolute. The environment  
> variable GRAPHQL\_LIBRARY\_PATH is read the same way when this is empty.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
LibraryPath=
*/ ?>
```

## NamingStyle

> How content classes and their attributes are named in the schema:  
>   identifier  (the default) as in the administration: the class ng\_article is the type ng\_article, its attribute  
>               short\_title the field short\_title  
>   pascal      types in PascalCase (NgArticle), fields in camelCase (shortTitle)  
>   map         as identifier, but the names of \[GraphQLNaming\] TypeNames\[\] and FieldNames\[\] win  
> A name that is not a valid GraphQL name (a digit first, a dash) has every other character replaced by \_ and a \_  
> in front when it starts with a digit. An attribute named like a field every content has (id, name, children ...)  
> gets the suffix \_field (name\_field).  

Default: `identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
NamingStyle=identifier
*/ ?>
```

## ClassList

> The classes the schema shows. Empty: every class except those of ExcludedClassList\[\]. Policies always apply:  
> a class in the schema is still read only by who may read its objects.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "ClassList[]" first replaces the shipped list; without it the items are added
ClassList[]
*/ ?>
```

## ExcludedClassList

> Never in the schema (unless ClassList\[\] names them): the user accounts, the user groups and the classes of  
> settings.  

Default: a list of 4: `user`, `user_group`, `common_ini_settings`, `template_look`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "ExcludedClassList[]" first replaces the shipped list; without it the items are added
ExcludedClassList[]
ExcludedClassList[]=user
ExcludedClassList[]=user_group
ExcludedClassList[]=common_ini_settings
ExcludedClassList[]=template_look
*/ ?>
```

## HiddenFieldList

> Attributes never in the schema, <class>/<attribute> or \*/<attribute> for every class.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "HiddenFieldList[]" first replaces the shipped list; without it the items are added
HiddenFieldList[]
*/ ?>
```

## HiddenDatatypeList

> Datatypes never in the schema: the user account (login, e-mail, password hash) and the password field.  

Default: a list of 2: `ezuser`, `ezpassword`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "HiddenDatatypeList[]" first replaces the shipped list; without it the items are added
HiddenDatatypeList[]
HiddenDatatypeList[]=ezuser
HiddenDatatypeList[]=ezpassword
*/ ?>
```

## Introspection

> Introspection (the schema read by a client, as the playground does):  
>   signed-in  (the default) for a request signed in by a token, a personal API key or a session; refused for  
>              anonymous requests  
>   enabled    for every request  
>   disabled   for none (the console commands still print the schema)  

Default: `signed-in`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Introspection=signed-in
*/ ?>
```

## MaxDepth

> The deepest nesting of fields a query may have, and the highest complexity (every field counts 1, a list field  
> counts its limit or first argument times the fields below it). A query over the limit is refused before it runs  
> (extensions.code DEPTH\_LIMIT or COMPLEXITY\_LIMIT).  

Default: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxDepth=12
*/ ?>
```

## MaxComplexity

Default: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxComplexity=1000
*/ ?>
```

## MaxListLimit

> The most items one list field returns (limit, first); a bigger value is lowered to it.  

Default: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxListLimit=100
*/ ?>
```

## DefaultListLimit

> The items a list field returns when the query does not say.  

Default: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
DefaultListLimit=25
*/ ?>
```

## PersistedQueries

> Persisted queries: queries stored on the server (var/<site>/graphql/persisted/) and called by a name  
> (GET /api/graphql?id=articles) or by their SHA-256 (Apollo's extensions.persistedQuery.sha256Hash). Stored with  
> ./console exp:graphql:persist <file or directory> --apply. enabled (the default) or disabled.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
PersistedQueries=enabled
*/ ?>
```

## AutomaticPersistedQueries

> Who may store a query by sending its text with its SHA-256 (Apollo's automatic persisted queries):  
>   signed-in  (the default) a request signed in by a token, a personal API key or a session  
>   enabled    every request, the anonymous one too (at most MaxPersistedQueries are stored)  
>   disabled   nobody; queries are stored with exp:graphql:persist only  
> A query that may not be stored still runs, as an ad-hoc query.  

Default: `signed-in`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
AutomaticPersistedQueries=signed-in
*/ ?>
```

## MaxPersistedQueries

> The most queries the store holds before an automatic persisted query is no longer stored (exp:graphql:persist  
> stores regardless).  

Default: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxPersistedQueries=1000
*/ ?>
```

## PersistedQueriesOnly

> Only persisted queries are run at /api/graphql (an ad-hoc query is refused with PERSISTED\_QUERY\_REQUIRED):  
>   disabled   (the default) any query  
>   anonymous  anonymous requests may run persisted queries only, signed-in ones any query  
>   enabled    every request to /api/graphql runs persisted queries only  
> The session end point (the playground of Setup > GraphQL) and the console always run any query.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
PersistedQueriesOnly=disabled
*/ ?>
```

## PersistedQueryDirectoryList

> Directories of persisted queries shipped with the code: every <name>.graphql file is the persisted query <name>,  
> read only (exp:graphql:persist --list shows them). Relative to the installation root, or absolute; an extension  
> adds its own directory in its settings/graphql.ini.append.php.  
> PersistedQueryDirectoryList\[\]=extension/myfrontend/graphql  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "PersistedQueryDirectoryList[]" first replaces the shipped list; without it the items are added
PersistedQueryDirectoryList[]
*/ ?>
```

## MaxQueryLength

> The longest query text accepted, in bytes.  

Default: `100000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxQueryLength=100000
*/ ?>
```

## ResponseCache

> The response cache: the answers of persisted queries, kept per siteaccess, schema, user rights (roles and their  
> limitations, as the view cache), variables and address, in var/cache/graphql/responses/. An anonymous GET of a  
> persisted query is answered from it at the start of index\_rest.php, before the settings and the database. Every  
> answer is expired when content is published (whenever view caches are cleared), when a class changes, when  
> persisted queries are stored or removed, and by ezcache --clear-id=graphql (also --clear-tag=ini and content).  
> Ad-hoc queries are never kept. enabled (the default) or disabled.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
ResponseCache=enabled
*/ ?>
```

## ResponseCacheTtl

> Seconds an answer is kept at most, also without a publish; 0: until it is expired.  

Default: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
ResponseCacheTtl=86400
*/ ?>
```

## CredentialCacheTtl

> A signed-in GET of a persisted query is answered at the start of index\_rest.php too, once the kernel has checked its  
> token or key: the rights of the credentials (their user's roles) are remembered this many seconds  
> (var/cache/graphql/credentials/), and forgotten at once when an API key, a role or a user's rights change. A token  
> revoked otherwise still reads the answers cached for its rights until then. 0: signed-in requests always go  
> through the kernel.  

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
CredentialCacheTtl=60
*/ ?>
```

## RateLimit

> Rate limits: the requests one signed-in user or one personal API key (RateLimit), or one IP address of anonymous  
> requests (RateLimitAnonymous), may send in RateLimitWindow seconds; 0: no limit. A request over its limit is  
> answered 429 with RATE\_LIMITED and Retry-After; every answer carries X-RateLimit-Limit, X-RateLimit-Remaining and  
> X-RateLimit-Reset. Counted in var/cache/graphql/rate/ by every web server worker alike. The console is not limited.  

Default: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
RateLimit=600
*/ ?>
```

## RateLimitAnonymous

Default: `120`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
RateLimitAnonymous=120
*/ ?>
```

## RateLimitWindow

Default: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
RateLimitWindow=60
*/ ?>
```

## SchemaExtensionList

> Schema extensions: classes implementing expGraphQLSchemaExtension that add root queries, fields of Location and the  
> types of their datatypes; each answers only where it is available (its extension active). The kernel ships the one  
> of eztags (the root queries tag and tags, the eztags fields as \[Tag\]) and the one of explayouts (the root query  
> layout, Location.layout). An extension adds its own in its settings/graphql.ini.append.php.  

Default: a list of 2: `expGraphQLTagsExtension`, `expGraphQLLayoutsExtension`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "SchemaExtensionList[]" first replaces the shipped list; without it the items are added
SchemaExtensionList[]
SchemaExtensionList[]=expGraphQLTagsExtension
SchemaExtensionList[]=expGraphQLLayoutsExtension
*/ ?>
```

## ImageAliasList

> Image aliases (settings/image.ini) the field variations lists when the query names none; url(alias:) takes any  
> alias of image.ini.  

Default: a list of 3: `small`, `medium`, `large`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "ImageAliasList[]" first replaces the shipped list; without it the items are added
ImageAliasList[]
ImageAliasList[]=small
ImageAliasList[]=medium
ImageAliasList[]=large
*/ ?>
```

## MutationsEnabled

> Mutations (createContent, updateContent, translateContent, publishDraft, deleteContent, moveContent, hideContent,  
> revealContent, addLocation, uploadImage, uploadFromUrl, createPreviewToken): enabled or disabled (the default).  
> Disabled: the schema has no type Mutation and a mutation is refused with DISABLED. Enabled: each mutation checks  
> the policies of the user on the content it changes, runs the kernel's operations (the workflows of content/publish,  
> content/move ... included), is recorded in the audit (content.graphql.mutation) and expires the response cache.  
> A mutation is sent with POST only; with a personal API key it needs the scope graphql:write; with the session of  
> the web interface it needs the form token (header X-CSRF-Token). Switch it on per siteaccess, for the siteaccess  
> a front end writes through.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MutationsEnabled=disabled
*/ ?>
```

## AnonymousMutations

> Mutations of anonymous requests (they run with the Anonymous role's policies): disabled (the default) refuses  
> them with FORBIDDEN; enabled lets them through to the policy checks (a guest book, a form that creates content).  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
AnonymousMutations=disabled
*/ ?>
```

## MaxUploadSize

> The largest file a mutation accepts (uploadImage, a FieldInput upload, uploadFromUrl), in bytes or with k, M, G.  
> PHP's own upload\_max\_filesize and post\_max\_size limit a request before it gets here.  

Default: `32M`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxUploadSize=32M
*/ ?>
```

## UploadCheckList

> Checks every uploaded file passes before it is stored: classes implementing expGraphQLUploadCheck (a virus  
> scanner, a type filter). The first that gives a reason refuses the file; a class that is missing refuses every  
> file. UploadCheckList\[\]=myClamAvUploadCheck  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "UploadCheckList[]" first replaces the shipped list; without it the items are added
UploadCheckList[]
*/ ?>
```

## FileDatatypeList

> Datatypes whose value is a file, besides ezimage, ezbinaryfile and ezmedia: a FieldInput writes them with an  
> upload only, never with a value (a path in a value could import any file of the server).  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "FileDatatypeList[]" first replaces the shipped list; without it the items are added
FileDatatypeList[]
*/ ?>
```

## UploadFromUrlHostList

> The hosts uploadFromUrl may fetch files from (https only, no redirects, at most MaxUploadSize). Empty (the  
> default): uploadFromUrl is switched off. UploadFromUrlHostList\[\]=images.example.com  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "UploadFromUrlHostList[]" first replaces the shipped list; without it the items are added
UploadFromUrlHostList[]
*/ ?>
```

## PreviewTokenTtl

> Preview tokens (createPreviewToken, the query preview(token:)): the seconds a token is valid when the mutation  
> does not say, and the longest it may ask for. The tokens are signed with the installation's secret key  
> (EXP\_SECRET\_KEY or var/secrets/exp\_secret.key); rotating it ends every token.  

Default: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
PreviewTokenTtl=3600
*/ ?>
```

## MaxPreviewTokenTtl

Default: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxPreviewTokenTtl=86400
*/ ?>
```

## Debug

> enabled or disabled (the default). Enabled: an error carries the PHP exception and its trace in  
> extensions.debug. Only for a development siteaccess: a trace tells paths and code to whoever asks.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Debug=disabled
*/ ?>
```

## Related

- [All blocks of graphql.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/graphql.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
