# API reference

The classes, methods, settings, template functions and console commands that deliver the
LoveStack features in Exponential 6.0.15, for developers. What each feature does for editors and
administrators is in the [overview](../lovestack.md#feature-by-feature).

Conventions, as everywhere in Exponential:

- new kernel classes are named `exp*` and live in `kernel/classes/exp*.php`;
- existing `eZ*` classes only gain methods and optional parameters; no public signature changed;
- settings are in the existing INI files, documented there with their default; change them in an
  override (`settings/override/<file>.append.php`) or a siteaccess;
- console commands are `exp:<name>`, run with `./console`;
- nothing is kept between requests, so everything works under Exponential's Velocity engine,
  whose PHP workers stay alive across requests.

Code derived from a LoveStack change carries the line
`Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit <sha>), Copyright (C) Mugo Web, GPL v2`.

---

## Security and sessions

### Node data calls and `RequirePolicy` (L01)

```ini
# extension/ezjscore/settings/ezjscore.ini (shipped)
[ezjscServer]
FunctionList[]=ezjscnode

[ezjscServer_ezjscnode]
Class=ezjscServerFunctionsNode
Functions[]=ezjscnode
RequirePolicy=disabled
```

`RequirePolicy` works for every `[ezjscServer_<name>]` block:

| Value | `Functions[]` | A refused call |
|---|---|---|
| not set | checked, as before | answered with its error text (HTTP 200) |
| `disabled` | not checked | — |
| `enabled` | checked | a request whose calls are all refused for this reason is answered with 403 Forbidden |

`ezjscServerRouter::getInstance( $args, $requireIniGroup, $checkFunctionExistence, &$refusal )`
sets `$refusal` to `invalid`, `access` or `policy-required`;
`ezjscServerRouter::refusedStatus( array $refusals )` gives 200 or 403 (403 only when every refusal
is `policy-required`).

New installations write `RequirePolicy=enabled` to `settings/override/ezjscore.ini.append.php`.
To switch it on yourself:

```ini
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
RequirePolicy=enabled
```

after giving *ezjscore / call* with the function list `ezjscnode` to the roles that need it
([who calls ezjscnode](L01-callers.md)).

### Registration and `PolicyOmitListExclude[]` (L02)

```ini
# settings/override/site.ini.append.php
[RoleSettings]
PolicyOmitListExclude[]=user/register
```

`PolicyOmitListExclude[]` takes entries out of `PolicyOmitList[]` after every settings file is
merged, so the entries extensions add stay (resetting `PolicyOmitList[]` in an override would
drop them).

```php
expPolicyOmitList::entries( $ini = null );          // PolicyOmitList[] without the excluded entries
expPolicyOmitList::excluded( $ini = null );         // PolicyOmitListExclude[]
expPolicyOmitList::omits( $module, $view = '', $ini = null ); // true: no policy needed
```

Used by the web kernel, the view access check, ezjscore's `run` and the benchmark command. In
templates the register link is shown with:

```tpl
{if fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) )} ... {/if}
```

`can_open` honours the omit list; `has_access_to` does not, and would hide the link on every
site that keeps registration open.

### Logout (L03)

`eZUser::logoutCurrent()` ends with `eZSession::remove()`. The session handlers' `destroy()`
expire the session cookie with the path, domain, `Secure`, `HttpOnly` and `SameSite` that
`eZSession::start()` set it with.

```php
eZSession::expiredCookie();                       // array( 'name', 'value' => '', 'options' => setcookie() options )
eZSession::expireCookie();                        // sends it once per response; false when headers are out
eZSession::listsExpiredCookie( $headers, $name ); // does a header list already remove the cookie
```

`eZSession::remove()` also closes without writing a session the handler left running, expires
the cookie for a handler of your own that does not, unsets `$_COOKIE[<session name>]` and the
session id, so a session started later in the request gets a new id.

### Unique logins (L04)

```php
class expUserLoginIntegrity   // kernel/classes/expuserloginintegrity.php
{
    const INDEX_NAME = 'ezuser_login';
    /** groups of accounts sharing a login: array{login, user_ids, logins, blocks_index}[];
     *  blocks_index false = differ only in case, which the engine's index accepts */
    public static function duplicates( $db = null ): array;
    /** array{engine, exists, unique, names} */
    public static function indexStatus( $db = null ): array;
    /** adds the unique index (replacing the plain one); refuses while duplicates() is not empty */
    public static function addUniqueIndex( $db = null, &$message = null ): bool;
    /** content object id of another account with this login (case-insensitive), or null */
    public static function loginTakenBy( $login, $userID ): ?int;
    /** stores unless the login is taken */
    public static function storeUser( eZUser $user ): bool;
}
```

```sh
./console exp:user-login-check                 # report only; --show-logins, --json
./console exp:user-login-check --fix-index     # adds the index after a clean report
# exit code: 0 clean (or index added), 1 duplicates found (or index refused), 2 error
```

Callers: `eZUserType` (a first version that did not validate is not stored),
`eZLDAPUser::publishUpdateUser()` (a new account through `storeUser()`; on a lost race the new
object is removed and the existing account returned).

### Image upload check (L05, L06)

```ini
# settings/image.ini (shipped)
[ImageUploadSettings]
ValidateMIMEType=enabled
AllowedMIMETypes[]
AllowedMIMETypes[]=image/jpeg
AllowedMIMETypes[]=image/png
AllowedMIMETypes[]=image/gif
AllowedMIMETypes[]=image/webp
AllowedMIMETypes[]=image/avif
AllowedMIMETypes[]=image/tiff
AllowedMIMETypes[]=image/bmp
```

```php
expImageUploadValidator::validate( $path, $originalName = null );   // array( 'valid' => bool, 'mime' => string )
expImageUploadValidator::validateAgainst( $path, array $allowed, $checkAllowList = true, $originalName = null );
expImageUploadValidator::allowedMIMETypes();
expImageUploadValidator::isEnabled();
expImageUploadValidator::normalize( $type );       // aliases such as image/x-ms-bmp, image/pjpeg
eZImageType::imageFileError( $filePath, $originalName = null );                  // one place for every image path
eZContentUpload::imageUploadError( $dataMap, $fileAttribute, $filePath, $originalName = null );
eZPackageCreationHandler::httpFileIsImage( $httpFileName, &$errorList );         // L06
```

New message: "Images of the type %1 are not accepted." An SVG added to the list is accepted by
the validator, but the image datatype still refuses it (its `getimagesize()` and markup check).

### LoveStack's `[Session] SameSite`

`site.ini [Session] SameSite=` is read when `CookieSameSite` is not set, and logs a deprecation
once per request; `CookieSameSite` wins when both are set.

---

## Diagnostics and logging (L07–L12)

```php
eZDebug::LEVEL_DEPRECATED;          // new level
eZDebug::SHOW_DEPRECATED;           // new show flag, part of SHOW_ALL
eZDebug::writeDeprecated( $string, $label = '', $backgroundClass = '' );
eZDebug::deprecatedCount();         // this request, every occurrence
eZDebug::isDeprecatedLogEnabled();
eZDebug::resetDeprecations();       // the web kernel calls it when a request starts
eZLog::fileSetting( 'LogFileMaxSize', 1 ); // int|null from site.ini [FileSettings]
```

```ini
# settings/site.ini (shipped)
[DebugSettings]
# written to var/log/deprecated.log; not kept for the debug toolbar
DeprecatedLog=enabled
# %1$s template edit URI, %2$s siteaccess, %3$s template file; empty: the usual link
UsedTemplateEditUrl=

[FileSettings]
LogFileMaxSize=2097152
LogFileRotateCount=3
```

Order of the log size: `eZLog::setMaxLogSize()`, then the constant in `config.php`, then
`site.ini`, then the class default (2 MB). `AlwaysLog[]=deprecated` writes deprecations while
debug output is off.

- L09: `eZTemplate` warns `Operator '<op>' has no default value for parameter '<name>'`;
  `eZTemplate::operatorParametersWithoutDefault()`. Compiled templates are warned at compile time.
- L10: `eZTemplatesStatisticsReporter::templateEditUrlFormat()`, `templateEditUrl()`.
- L11: `eZDBException::getCode()` returns the driver's error number, as `errorNumber()`: MySQL
  number, PostgreSQL SQLSTATE string, SQLite result code, Oracle ORA number; MongoDB only
  `errorNumber()`. `eZExecution::isDatabaseUnavailableCode()` recognises the codes of a lost or
  refused connection.
- L12: `eZContentCacheManager::cacheThresholdWarningText( $nodeCount, $cleanupValue )`.

---

## Content, datatypes and the administration (L13–L21)

### Audit of location changes (L13)

Written through Exponential's audit trail, channel `content`, from `eZContentOperationCollection`
(`addAssignment`, `removeNodes`, `updateMainAssignment`):

| Event | Console label | Default |
|---|---|---|
| `content.node.add` (one per new location; target = the new node; after: locations, node, parent, main node) | Location add | off |
| `content.node.remove` (the removed node; before: parent, path, object id, name, main) | Location remove | on |
| `content.node.main` (target = the new main node; before/after main node; `cause` `location-remove` when the next location is promoted) | Main location change | off |

```ini
# settings/override/audit.ini.append.php
[AuditEventSettings]
Enabled[]=content.node.add
Enabled[]=content.node.main
```

LoveStack's names are mapped: `audit.ini [AuditCompatSettings] Map[location-assign]`,
`Map[location-remove]`, `Map[main-node-update]`, and `[AuditSettings] AuditFileNames[]` with
LoveStack's file names; `eZAudit::writeAudit()` calls of those names land on the same events.

### Site data (L14)

```ini
# settings/override/site.ini.append.php
[SiteDataSettings]
TemplateReadable[]=my-flag
```

```tpl
{def $row = fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )}
{if $row}{$row.value|wash}{/if}
```

The fetch returns the eZSiteData object or false (not listed, not stored); whole names only.
PHP: `eZSiteData::createNew()`, `templateReadableNames()`, `isTemplateReadable()`,
`fetchList( $filter, $offset, $limit )`, `fetchListCount( $filter )`. Admin: **Setup > Site
data** (`setup/sitedata`), read only, policy `setup/system_info`.

### Copy button (L15)

`node/view/full.tpl` of admin, admin3 and admin4: a Copy button next to Move, enabled with
`can_create` at the parent, as in the context menu; it opens `content/copy`.

### Required alternative text (L16) and trimmed text lines (L17)

Two class attribute options, on the class edit page, both off by default:

| Option | Datatype | Field |
|---|---|---|
| Alternative text is required | `ezimage` | `eZImageType::ALT_TEXT_REQUIRED_FIELD` = `data_int2` |
| Trim the stored value | `ezstring` | `eZStringType::TRIM_FIELD` = `data_int2` |

```php
eZImageType::isAltTextRequired( $classOrObjectAttribute );
eZImageType::validateImageAltText( $text );
eZImageType::classAltTextRequired( $classOrIdentifierOrId, $attributeIdentifier = false ); // null: no image attribute
eZStringType::isTrimEnabled( $classAttribute );
eZStringType::storedString( $classAttribute, $text );
```

Package XML: `<alternative-text-required>1</alternative-text-required>`, `<trim>1</trim>` (only
when on). Checked in the content edit form, the rich text editor's upload (also Tiptap) and the
AJAX uploader (`UploadAlternativeText`).

### Name pattern resolver classes (L18)

```php
interface expNamePatternResolverInterface
{
    // constructed with the rest of the pattern after "{ClassName}"
    public function resolveNamePattern( eZContentObject $object, $version = false,
                                        $translation = false, $length = false,
                                        $sequence = '' );   // returns the name (string)
}
```

```ini
# settings/override/content.ini.append.php
[NamePatternSettings]
ResolverClasses[]=myNameResolver
```

A class's object name pattern or URL alias pattern `{myNameResolver}` then calls that class.
Classes not listed, or not implementing the interface, are ignored and logged. PHP:
`eZNamePatternResolver::resolve( $pattern, $object, $version, $translation, $limit, $sequence )`,
`customResolver( $pattern )`, `limitName( $name, $limit, $sequence )`.

### Class filter default (L19)

```ini
# settings/content.ini (shipped)
[FetchSettings]
# empty: a class_filter_array without a type is ignored, as before; include: it filters
DefaultClassFilterType=
```

PHP: `eZContentFunctionCollection::classFilterType( $type, $array )`.

### Template fixes (L20, L21)

`content/edit_locations.tpl` (admin designs) preselects "Unchanged" for an existing location;
`content/datatype/view/ezselection.tpl` and `ezmatrix.tpl` write no stray `<br />`.

---

## Rich text editor (L22–L24)

```ini
# settings/override/content.ini.append.php
[CustomTagSettings]
IsHidden[]=quote

# settings/override/ezoe.ini.append.php
[EditorSettings]
# disabled shipped; new installations write enabled
CustomTagControls=enabled
```

One switch for the three editors: TinyMCE 3 loads the plugin `exp_customtag_controls` when it is
on (no `Plugins[]` entry needed; TinyMCE 3's plugin API and ezjscore's jQuery, no YUI), TinyMCE 8
gets move buttons and shortcuts in its custom tag toolbar (`ez_custom_tag_controls`), Tiptap the
same in exp_oe_tiptap 0.1.2 (`options.customTagControls`). Stylesheet `ezoe_customtags.css`
(scoped, acts only when on). Keys: Alt+Shift+Up / Down / E / Delete; Backspace removes an empty
block tag.

```php
eZOEXMLInput::hiddenCustomTags( $ini = null );                         // IsHidden[], trimmed, unique
eZOEXMLInput::offeredCustomTags( array $tags, $current = '', $hidden = null );
eZOEXMLInput::customTagPreview( $name, $designBases = null );          // URL of images/editor/custom_tags_thumbs/<name>.png, or ''
eZOEXMLInput::customTagControlsEnabled();
// getCustomTagDefinitions(): each definition also has 'hidden' (bool) and 'preview' (string)
// template attribute: $input_handler.custom_tag_controls_enabled
```

L24 needed no change: `tag_custom.tpl` already closed the sub/sup dialog on OK.

---

## Developer APIs and settings (L25–L31)

```php
eZURI::build( string $url, bool $full = false, bool $htmlEscape = true ): string;          // L25
eZMailTransport::send( eZMail $mail, ?string $transportType = null );                      // L26
eZTemplateDesignResource::setKeyValue( string $key, $value, bool $override = true );       // L31
expErrorHeaderList::build( eZINI $errorINI, $httpErrorCode, $serverProtocol ): array;      // L29
eZINI::includedFiles(): array;   // L28: files and directories read through [Include ...]
```

```ini
# settings/template.ini (shipped), L27
[PHP]
PHPOperatorList[rawurlencode]=rawurlencode
PHPOperatorList[rawurldecode]=rawurldecode

# settings/override/error.ini.append.php, L29
[HTTPError-404]
HeaderList[Cache-Control]=no-store

# extension/ezjscore/settings/ezfind.ini.append.php (shipped), L30
[SearchSettings]
SortBy[]
SortBy[published]=desc
```

### `[Include …]` (L28)

Anywhere in an INI file:

```ini
[Include site.d/*.ini]
```

The path is relative to the including file; wildcards are allowed; files are read in name order
where the line stands; the line ends the group before it; includes nest (depth limit 16); a loop
is skipped with a warning; a pattern without a match is a notice. The INI cache holds the
included files and the pattern directories (cache key `includes`), checked like the main files
when the file time check is on, so `php bin/php/ezcache.php --clear-tag=ini`, the INI step of
`exp:velocity deploy` and Velocity's settings refresh pick up their changes. Direct-access
instances (the settings page, the classic settings editor) do not expand includes and keep the
line as written; `expIniWriter` has the entry type `include`.

---

## URLs and search (L32–L36)

```ini
# settings/site.ini (shipped)
[URLTranslator]
DecodeOnce=disabled
TrailingCleanup=disabled
```

```php
new eZURI( string $uri, ?bool $decode = null );               // L32: null follows DecodeOnce
eZURI::setURIString( $uri, $fullInitialize = true, ?bool $decode = null );
eZURI::decodeOnce(): bool;
eZCharTransform::trailingCleanup(): bool;                     // L33: '"' reserved in urlalias_iri
$db::MAX_INT; $db::MIN_INT;                                   // L34: signed 32-bit MySQL, PostgreSQL and the base class; 64-bit SQLite, MongoDB
$db->clampInteger( $value ): int;                             // used by eZSearchEngine
```

- L33 adds only the double quote: the trailing strip of `!`, dots and separators has been in
  Exponential since 2016.
- L35 has no setting: Exponential has left the relation fields of related objects out of the
  index since 2018 (EZP-27101, `eZContentObjectAttribute::metaDataArray( $attributes, true )`,
  `eZDataType::isRelationType()`).
- L36: `content/cache` event listeners receive `( array $nodeIDs, array $objectIDs )` from every
  sender; the hide cronjob calls `eZSearch::updateNodeVisibility()`.

---

## Migration

```sh
./console exp:lovestack check             # read only; --json; exit 0 nothing to do, 1 attention
./console exp:lovestack migrate           # dry run (default): prints what it would write
./console exp:lovestack migrate --apply   # 'lovestack' version row -> 6.0.15stable; records lovestack-schema-version
```

PHP: `expLoveStack::migrationPlan()` and `expLoveStack::migrate( $apply )`
(`kernel/classes/explovestack.php`). `migrate` writes only the two `ezsite_data` rows
(`ezpublish-version`, `lovestack-schema-version`); LoveStack's `db-schema-version` row is kept; a
version row other than `lovestack` is left to the update SQL; a database that does not come from
LoveStack is refused; a second run changes nothing. It prints the settings to change, taken from
`check`. Step by step: [MIGRATION.md](MIGRATION.md).
