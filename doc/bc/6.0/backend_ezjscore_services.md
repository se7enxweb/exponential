# Backend services over ezjscore (expservices)

`extension/expservices` turns the ezjscore server functions into a library of remote services that offer the
features of Exponential to remote admin apps (GTK, Qt, KDE/GNOME, iOS, Android, shell) and to JavaScript
frontends. Version 0.1.0 (first draft, 2026-10-02).

## Overview

### What an ezjscore server function is

ezjscore (`extension/ezjscore`) lets a page call PHP over HTTP. A server function is a public static method of a class
that is registered in `ezjscore.ini` as a block `[ezjscServer_<name>]` (`Class=<class>`). It is called as

    <root>/ezjscore/call/<name>::<function>[::<arg>[::<arg>...]]

(the admin siteaccess has the same URL below `/admin/`). The router (`ezjscServerRouter`) refuses any class that has no
block in `ezjscore.ini`, calls `<class>::<function>( array $args )` and answers in the format of the `Accept`
header or `?ContentType=json|xml|text`. `ezjscore/call` itself does not check a policy: every function checks for
itself. Several calls can be sent in one request: POST `ezjscServer_function_arguments` with the calls joined by `@SEPARATOR$`.

The ezjscore answer wraps the return value: `{"error_text": "", "content": <what the function returned>}`.
expservices functions return the envelope below as `content`.

### The classes: one domain, one class, one block

Every domain is a class `exp<Domain>Services` extending `expServiceBase`, registered in
`extension/expservices/settings/ezjscore.ini.append.php` as `[ezjscServer_exp<domain>]`, so a service of the domain
`system` is called `ezjscore/call/expsystem::version`. Each public static method is a service and is declared in the
class's `public static $services`:

    'version' => array( 'summary' => '...', 'access' => 'public' | 'user' | array( 'setup', 'system_info' ),
                        'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' )

`ezjscServerRouter::call()` hands every class that extends `expServiceBase` to `expServiceBase::invoke()`, which
checks that the service is declared, calls it, and turns every exception into the error envelope.

### Discovery

| Call | Answers |
|---|---|
| `expservices::catalog[::<domain>]` | every service: domain, method, summary, access, write, args, returns, call |
| `expservices::schema::<domain>` | the services of one domain |
| `expservices::service::<domain>::<method>` | one descriptor |
| `expservices::domains` | the domains with their class and service count |
| `expservices::version` | the expservices, envelope and Exponential versions |

The domain is the part after `exp` of the block name (`session`, `system`, `ini`, `cache`, ...). The calls to the
discovery functions are public; every other service checks its own `access`.

### The envelope

    { "ok": true,  "data": <the result>, "meta": { ... } }
    { "ok": false, "error": { "code": 403, "message": "No access to setup/managecache" } }

Lists are paged: `data` is the items and `meta` is `total`, `offset`, `limit`, `count`, `has_more`.

### Authentication

Sign in once with `expsession::login` (POST `username`, `password`); the response sets the session cookie, which
a non-browser client keeps in a cookie jar and sends with every call. `expsession::whoami` says who the session is,
`expsession::token` returns the form token needed for writes, `expsession::ping` keeps the session alive,
`expsession::logout` ends it. An API-token option (no cookie) is future work.

    curl -c jar -d 'username=admin' --data-urlencode 'password=...' https://site/ezjscore/call/expsession::login
    curl -b jar https://site/ezjscore/call/expsession::whoami

### Access

`'access'` of a service is `public` (anybody), `user` (any signed-in user) or `array( module, function )`, a policy of
the user (`eZUser::hasAccessTo`; a limited policy passes the check, and the service then checks the object, e.g. `canRead()`
of a node). A missing login answers 401, a missing policy 403.

### Paging

Services that list take `limit` and `offset` arguments (`expsystem::phpextensions::10::20` is 10 items from 20).
The default and the maximum are in `expservices.ini [Paging]` (25 and 200).

### Errors

| Code | Meaning |
|---|---|
| 400 | bad or missing arguments |
| 401 | login needed |
| 403 | no policy, no POST, or a wrong form token |
| 404 | no such service, node, object ... |
| 409 | conflict |
| 422 | the data is not valid |
| 500 | the service failed (logged to the debug output, never a PHP fatal) |

### Writes

A service with `'write' => true` needs a POST, the form token (field `ezxform_token` or header `X-CSRF-Token`; get it
from `expsession::token`), and its policy; arguments come in the POST body (`expServiceBase::post()`), not in the URL.
Every successful write records the audit event `service.<domain>.<method>` (`expAudit`), besides the events the
kernel operation writes itself. `expservices.ini [Writes]` can switch the token check (tests only) and the audit off.

### Writing a service

    class expFooServices extends expServiceBase
    {
        public static $services = array( 'bar' => array( 'summary' => '...', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' ) );

        public static function bar( $args )
        {
            self::guard( __FUNCTION__ );
            $node = self::node( self::arg( $args, 0, 'int' ) );
            return self::ok( array( 'name' => $node->attribute( 'name' ) ) );
        }
    }

Helpers: `arg()`, `post()`, `paging()`/`pageOf()`, `ok()`, `page()`, `guard()`, `audit()`, `node()`, `can()`.
Register the class in `ezjscore.ini.append.php`, run `php bin/php/ezpgenerateautoloads.php -e`.

### Testing a call

    php ai/bin/one/expservices_call.php expsystem::version        # as admin, from the command line
    php vendor/bin/phpunit tests/tests/extension/expservices/

## Domains

### Catalogue (`expservices`, 5 services)

Discovery of the API; every call is public.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expservices::catalog::<domain>` | public |  | Every service of every domain: domain, method, summary, access, write, args, returns |
| `expservices::schema::<domain>` | public |  | The services of one domain |
| `expservices::service::<domain>::<method>` | public |  | One service descriptor |
| `expservices::domains` | public |  | The domains with their class and service count |
| `expservices::version` | public |  | The version of expservices, the envelope version and the Exponential version |

### Session and authentication (`expsession`, 8 services)

Remote clients sign in once and keep the session cookie; see the Authentication section.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expsession::login::<username>::<password>` | public |  | Signs in with POST username and password; the session cookie of the response is the session |
| `expsession::logout` | user | POST | Signs out (POST with the form token) |
| `expsession::whoami` | public |  | The current user: id, login, name, email, registered, groups |
| `expsession::token` | public |  | The form token of this session for writes (field ezxform_token or header X-CSRF-Token) |
| `expsession::ping` | public |  | Keeps the session alive and tells whether it is signed in |
| `expsession::access::<module>::<function>` | user |  | Whether the user has module/function (accessWord yes, no or limited) |
| `expsession::roles` | user |  | The roles of the user with the policies of each (module, function) |
| `expsession::groups` | user |  | The user groups of the user |

### System information (`expsystem`, 16 services)

What the installation is and runs on; the secrets of the installation are never part of an answer.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expsystem::version` | public |  | The Exponential version, release, state and the PHP version |
| `expsystem::info` | setup/system_info |  | Site name, URLs, siteaccess, database type, locale |
| `expsystem::settings` | public |  | The public site settings: site name, default access, locale, design |
| `expsystem::time` | public |  | The server time, time zone and the time of the request |
| `expsystem::php` | setup/system_info |  | PHP version, SAPI, limits and OPcache state |
| `expsystem::phpextensions::<limit>::<offset>` | setup/system_info |  | The loaded PHP extensions with their versions |
| `expsystem::database` | setup/system_info |  | The database type, name, server version and connection state (no credentials) |
| `expsystem::siteaccesses` | setup/system_info |  | The available siteaccesses |
| `expsystem::siteaccess` | public |  | The siteaccess of this request |
| `expsystem::languages` | user |  | The content languages of the installation |
| `expsystem::locales::<limit>::<offset>` | user |  | The locales the installation knows |
| `expsystem::health` | setup/system_info |  | Quick checks: database connection, var and cache directories writable |
| `expsystem::directories` | setup/system_info |  | The var, storage and cache directories (relative to the installation) and whether they are writable |
| `expsystem::statistics` | setup/system_info |  | Counts: content objects, nodes, classes, users, sessions |
| `expsystem::load` | setup/system_info |  | The server load average and the PHP memory use of this request |
| `expsystem::urls` | public |  | Host, root URL, index file and server URL of this request |

### Settings (read-only) (`expini`, 10 services)

Reads the INI settings as the siteaccess of the request sees them. Values of secrets (password, token, key ...) are masked exactly as exp:ini masks them.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expini::files::<limit>::<offset>` | setup/setup |  | The INI files of the installation (kernel, override and extension settings) |
| `expini::groups::<file>::<limit>::<offset>` | setup/setup |  | The groups (blocks) of an INI file as the current siteaccess reads it |
| `expini::variables::<file>::<group>` | setup/setup |  | The variables of one group with their values (secrets masked) |
| `expini::get::<file>::<group>::<variable>` | setup/setup |  | One setting value as the current siteaccess reads it (secrets masked) |
| `expini::has::<file>::<group>::<variable>` | setup/setup |  | Whether a group or a variable exists |
| `expini::search::<file>::<text>::<limit>::<offset>` | setup/setup |  | Finds variables whose name contains a text, in one INI file (secrets masked) |
| `expini::siteaccesses` | setup/setup |  | The siteaccesses that can have their own settings |
| `expini::scopes` | setup/setup |  | The settings scopes exp:ini knows (global, siteaccesses, extensions) |
| `expini::secret::<variable>` | setup/setup |  | Whether a variable name is treated as a secret (masked) |
| `expini::activeextensions` | setup/setup |  | The active extensions in the order the settings read them |

### Caches (`expcache`, 14 services)

The clear services are writes: POST, the form token, setup/managecache, an audit event. Pass dry_run=1 to see what would be cleared.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expcache::list::<limit>::<offset>` | setup/managecache |  | Every cache with its id, name, tags and how it is cleared |
| `expcache::tags` | setup/managecache |  | The cache tags with the cache ids each clears |
| `expcache::describe::<id>` | setup/managecache |  | One cache by id, with its path and the size of its files |
| `expcache::php` | setup/managecache |  | The PHP level caches: OPcache and APCu state |
| `expcache::velocity` | setup/managecache |  | Velocity response cache: where it is, how many files, when cleared |
| `expcache::http` | setup/managecache |  | The HTTP cache: whether it is enabled and its status |
| `expcache::staticcache` | setup/managecache |  | The static cache: whether it is enabled and its status |
| `expcache::query` | setup/managecache |  | The database query cache state |
| `expcache::clear::<by>::<names>::<dry_run>` | setup/managecache | POST | Clears caches: POST by=all/tag/id, names=comma list, dry_run=1 to only report |
| `expcache::cleartag::<tag>::<dry_run>` | setup/managecache | POST | Clears the caches of one tag (POST tag, dry_run) |
| `expcache::clearid::<id>::<dry_run>` | setup/managecache | POST | Clears one cache by id (POST id, dry_run) |
| `expcache::clearnode::<node_id>` | setup/managecache | POST | Clears the view cache of a node (POST node_id) |
| `expcache::clearvelocity::<dry_run>` | setup/managecache | POST | Clears the Velocity response cache (POST dry_run) |
| `expcache::clearopcache::<dry_run>` | setup/managecache | POST | Resets OPcache of this PHP process pool (POST dry_run) |

### Cronjobs (`expcronjob`, 7 services)

| Call | Access | Write | Summary |
|---|---|---|---|
| `expcronjob::parts` | setup/managecronjobs |  | The cronjob parts (groups run by runcronjobs.php <part>) with their script count |
| `expcronjob::scripts::<part>` | setup/managecronjobs |  | The scripts of one cronjob part |
| `expcronjob::all::<limit>::<offset>` | setup/managecronjobs |  | Every part with its scripts |
| `expcronjob::settings` | setup/managecronjobs |  | The cronjob settings: script directories, execution time limit, admin console settings |
| `expcronjob::status` | setup/managecronjobs |  | The cronjob log files: whether they exist, their size and last change |
| `expcronjob::deferred` | setup/managecronjobs |  | The workflow processes deferred to cron: how many wait |
| `expcronjob::runnables::<limit>::<offset>` | setup/managecronjobs |  | The cronjobs implemented as runnables (kernel and extension) |

### Extensions (`expextension`, 9 services)

| Call | Access | Write | Summary |
|---|---|---|---|
| `expextension::list::<limit>::<offset>` | setup/system_info |  | The active extensions with version, license and website where known |
| `expextension::available::<limit>::<offset>` | setup/system_info |  | The extensions found in the extension directories (active or not) |
| `expextension::info::<name>` | setup/system_info |  | The ezinfo of one extension: name, version, author, license, website |
| `expextension::isactive::<name>` | setup/system_info |  | Whether an extension is active |
| `expextension::accessextensions` | setup/system_info |  | The extensions active for the current siteaccess (ActiveAccessExtensions) |
| `expextension::designextensions` | setup/system_info |  | The extensions that supply designs (DesignExtensions) |
| `expextension::settingsfiles::<name>` | setup/system_info |  | The settings files an extension ships |
| `expextension::directories` | setup/system_info |  | The extension root directories |
| `expextension::count` | setup/system_info |  | The number of active and available extensions |

### Packages (`exppackage`, 7 services)

| Call | Access | Write | Summary |
|---|---|---|---|
| `exppackage::list::<limit>::<offset>::<type>::<repository>` | package/list |  | The packages: name, version, vendor, type, summary, installed |
| `exppackage::view::<name>::<repository>` | package/read |  | One package: description, vendor, maintainers, dependencies, state |
| `exppackage::files::<name>::<limit>::<offset>` | package/read |  | The file count and the simple file list of a package |
| `exppackage::types` | package/list |  | The package types |
| `exppackage::states` | package/list |  | The package states |
| `exppackage::repositories` | package/list |  | The package repositories |
| `exppackage::count` | package/list |  | The number of packages and of installed packages |

### Workflows and triggers (`expworkflow`, 10 services)

| Call | Access | Write | Summary |
|---|---|---|---|
| `expworkflow::list::<limit>::<offset>` | setup/administrate |  | The workflows (published versions) |
| `expworkflow::view::<id>` | setup/administrate |  | One workflow with its events |
| `expworkflow::events::<id>` | setup/administrate |  | The events of a workflow in order |
| `expworkflow::eventtypes` | setup/administrate |  | The registered workflow event types |
| `expworkflow::groups` | setup/administrate |  | The workflow groups |
| `expworkflow::triggers::<limit>::<offset>::<module>` | setup/administrate |  | The triggers: module, function, connect type and the workflow |
| `expworkflow::trigger::<id>` | setup/administrate |  | One trigger |
| `expworkflow::processes::<limit>::<offset>` | setup/administrate |  | The running workflow processes |
| `expworkflow::process::<id>` | setup/administrate |  | One workflow process |
| `expworkflow::statuses` | setup/administrate |  | The workflow status codes and names |

### Velocity (`expvelocity`, 5 services)

Paths of the server stay on the server and are not part of the answers.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expvelocity::status::<engine>` | setup/system_info |  | Whether Velocity runs: processes, listening ports, engine |
| `expvelocity::engines` | setup/system_info |  | The engines Velocity knows and the configured default |
| `expvelocity::urls` | setup/system_info |  | The URLs the server answers on |
| `expvelocity::cache` | setup/system_info |  | The Velocity response cache: files, bytes, last cleared |
| `expvelocity::installed` | setup/system_info |  | Whether Velocity is part of this installation |

### Extension point survey (rad) (`exprad`, 7 services)

| Call | Access | Write | Summary |
|---|---|---|---|
| `exprad::summary` | setup/system_info |  | The survey counts: ini files, settings, modules, operators, runnables ... |
| `exprad::counts::<name>` | setup/system_info |  | One count of the survey by name |
| `exprad::groups` | setup/system_info |  | The extension point groups with their counts |
| `exprad::runnables::<limit>::<offset>::<kind>` | setup/system_info |  | The runnables (commands, cronjobs, views) with kind and owner |
| `exprad::files::<limit>::<offset>` | setup/system_info |  | The ini files the survey reads |
| `exprad::inicommand` | setup/system_info |  | The exp:ini actions and scope providers |
| `exprad::contentjobtypes` | setup/system_info |  | The content job types |

### Debug summary (`expdebug`, 6 services)

| Call | Access | Write | Summary |
|---|---|---|---|
| `expdebug::summary` | setup/setup |  | Time, SQL, memory, templates and warnings of this request with their levels (the debug bar summary) |
| `expdebug::enabled` | setup/setup |  | Whether debug output is enabled for this request |
| `expdebug::level` | setup/setup |  | The debug level settings of the site |
| `expdebug::thresholds` | setup/setup |  | The warn and high thresholds of the debug bar |
| `expdebug::engine` | user |  | Which engine serves this request (Apache, Velocity, FrankenPHP, CLI) |
| `expdebug::phpversion` | user |  | The PHP version and SAPI of this request |


### Media and other services: 218 services in 14 domains

Written by the media/other part of expservices: `extension/expservices/classes/media/` (images, files, media, tags) and `classes/misc/` (layouts, audit, subitems, newsletters, sitemaps, statistics, designs, languages, links, PDF). Domains of extensions answer 404 `... is not available` when the extension is inactive. Tests: `tests/tests/extension/expservices/media/` and `misc/` (live database, test content under the Media root node 43 removed in tearDown, never a test database); the HTTP check is `ai/bin/one/verify_expservices_a5_over_http.sh`.

### Images and aliases (`expimage`, 18 services)

Image aliases (image.ini), original image information and the URL of any alias of a node image. Reads follow content/read on the node; `purge` needs content/edit.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expimage::aliases` | public |  | The configured image aliases (image.ini AliasList) with size and filters |
| `expimage::alias::<name>` | public |  | One configured image alias |
| `expimage::aliasnames` | public |  | Only the names of the aliases |
| `expimage::formats` | public |  | The image MIME types the converters support |
| `expimage::filters` | public |  | The image filters (image.ini [ImageMagick]/[GD]) known to the manager |
| `expimage::quality` | public |  | The configured output quality per MIME type |
| `expimage::attributes::<node>` | content/read |  | The image attributes of a node with their original file |
| `expimage::info::<node>::<attribute>` | content/read |  | The original image of a node attribute: file name, size, dimensions, MIME type, alt text |
| `expimage::url::<node>::<attribute>::<alias>` | content/read |  | The URL of one alias of a node image (generated when missing) |
| `expimage::urls::<node>::<attribute>` | content/read |  | The URLs of every configured alias of a node image |
| `expimage::generate::<node>::<attribute>::<alias>` | content/read |  | Makes sure an alias file exists for a node image (derived data, no content change) |
| `expimage::alt::<node>::<attribute>` | content/read |  | The alternative text of a node image |
| `expimage::files::<node>::<limit>::<offset>` | content/read |  | The image files registered for the object of a node (ezimagefile) |
| `expimage::list::<parent>::<limit>::<offset>::<alias>` | content/read |  | The image objects below a parent node with their original and one alias |
| `expimage::search::<text>::<parent>::<limit>::<offset>` | content/read |  | Image objects by name below a parent |
| `expimage::stats` | content/read |  | How many image files and images the installation holds |
| `expimage::bymime` | content/read |  | Image file counts per MIME type of the original files |
| `expimage::purge::<node>::<attribute>` | content/edit | POST | Removes the alias files of a node image (they are generated again on demand) |

### Files and downloads (`expfile`, 16 services)

Binary files: information, the `content/download` URL (the download itself checks content/read), time limited signed links (HMAC over node, attribute and expiry with the site secret; `verify` is public) and download statistics.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expfile::attributes::<node>` | content/read |  | The file attributes of a node |
| `expfile::info::<node>::<attribute>` | content/read |  | One file of a node: names, MIME type, size, download count |
| `expfile::downloadurl::<node>::<attribute>` | content/read |  | The content/download URL of a node file (the download itself checks content/read) |
| `expfile::signedurl::<node>::<attribute>::<seconds>` | content/read |  | A time limited signed link to a node file, for clients without a session |
| `expfile::verify::<node>::<attribute_id>::<expires>::<signature>` | public |  | Whether a signed link is genuine and not expired |
| `expfile::exists::<node>::<attribute>` | content/read |  | Whether the stored file of a node attribute exists on disk |
| `expfile::downloads::<node>::<attribute>` | content/read |  | The download count of a node file |
| `expfile::list::<parent>::<limit>::<offset>` | content/read |  | The file objects below a parent node with their file information |
| `expfile::search::<text>::<parent>::<limit>::<offset>` | content/read |  | File objects by name below a parent |
| `expfile::byname::<filename>::<limit>::<offset>` | content/read |  | The stored files with an original or stored file name (exact) |
| `expfile::top::<limit>` | content/read |  | The most downloaded files |
| `expfile::stats` | content/read |  | Number of files, total downloads, and counts per MIME group |
| `expfile::bymime` | content/read |  | File counts per MIME type |
| `expfile::mimegroup::<mime>` | public |  | The MIME group (application, image, ...) of a MIME type |
| `expfile::safename::<filename>` | user |  | Whether a file name is acceptable for upload and its safe form |
| `expfile::resetdownloads::<node>::<attribute>` | content/edit | POST | Sets the download count of a node file back to zero |

### Audio and video (`expmedia`, 10 services)

The ezmedia datatype: player settings, file information, URL, listings and statistics.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expmedia::attributes::<node>` | content/read |  | The media attributes of a node |
| `expmedia::info::<node>::<attribute>` | content/read |  | One media file of a node: file, MIME type, player dimensions and flags |
| `expmedia::player::<node>::<attribute>` | content/read |  | Only the player settings (width, height, controls, autoplay, loop, quality) |
| `expmedia::url::<node>::<attribute>` | content/read |  | The download/stream URL of a node media file |
| `expmedia::mimegroup::<mime>` | public |  | The MIME group (audio, video, ...) of a MIME type |
| `expmedia::safename::<filename>` | user |  | Whether a media file name is acceptable and its safe form |
| `expmedia::list::<parent>::<limit>::<offset>` | content/read |  | The media objects below a parent node |
| `expmedia::byobject::<object>` | content/read |  | The media rows stored for an object (all versions) |
| `expmedia::stats` | content/read |  | Number of media files and per MIME group |
| `expmedia::bymime` | content/read |  | Media file counts per MIME type |

### Tags (`exptags`, 27 services)

The eztags tree with the eztags policies (tags/read, add, edit, delete, addsynonym). Answers 404 "not available" when eztags is inactive. `attach` and `detach` change the tags attribute of a node (content/edit on the node).

| Call | Access | Write | Summary |
|---|---|---|---|
| `exptags::available` | public |  | Whether the eztags extension is active, and how many tags there are |
| `exptags::roots::<limit>::<offset>` | tags/read |  | The top level tags |
| `exptags::get::<id>` | tags/read |  | One tag with counts |
| `exptags::children::<id>::<limit>::<offset>` | tags/read |  | The children of a tag |
| `exptags::childrencount::<id>` | tags/read |  | How many children a tag has |
| `exptags::tree::<id>::<depth>` | tags/read |  | A tag subtree as nested nodes down to a depth (at most 5) |
| `exptags::path::<id>` | tags/read |  | The path of a tag from the root |
| `exptags::parent::<id>` | tags/read |  | The parent of a tag |
| `exptags::search::<text>::<limit>::<offset>` | tags/read |  | Tags whose keyword contains the text |
| `exptags::suggest::<text>::<limit>` | tags/read |  | Autocomplete: tags whose keyword starts with the text |
| `exptags::bykeyword::<keyword>` | tags/read |  | Tags with exactly this keyword |
| `exptags::byremote::<remote>` | tags/read |  | The tag with a remote id |
| `exptags::bypath::<path>` | tags/read |  | The tag at a path string (/1/5/7/) |
| `exptags::synonyms::<id>` | tags/read |  | The synonyms of a tag |
| `exptags::translations::<id>` | tags/read |  | The translations (keyword per locale) of a tag |
| `exptags::related::<id>::<limit>::<offset>` | tags/read |  | The objects tagged with a tag that the user may read |
| `exptags::relatedcount::<id>` | tags/read |  | How many published objects carry a tag |
| `exptags::ofnode::<node>` | content/read |  | The tags attached to a node |
| `exptags::popular::<limit>` | tags/read |  | The most used tags |
| `exptags::recent::<limit>` | tags/read |  | The most recently modified tags |
| `exptags::stats` | tags/read |  | Counts of tags, synonyms, translations and attachments |
| `exptags::add::<parent>` | tags/add | POST | Creates a tag below a parent (0: top level) |
| `exptags::rename::<id>` | tags/edit | POST | Changes the keyword of a tag in a locale |
| `exptags::addsynonym::<id>` | tags/addsynonym | POST | Adds a synonym to a tag |
| `exptags::delete::<id>` | tags/delete | POST | Deletes a tag with its children and synonyms |
| `exptags::attach::<node>::<attribute>` | content/edit | POST | Attaches an existing tag to a node tags attribute |
| `exptags::detach::<node>::<attribute>` | content/edit | POST | Removes a tag from a node tags attribute |

### Layouts (`explayout`, 27 services)

explayouts: layouts, zones (a linked zone answers the blocks of its shared layout), blocks, rules, and the layout a node or path resolves to (computed without touching the resolver cache). Policies explayouts/read and explayouts/edit; the writes use the layout service of explayouts_core.

| Call | Access | Write | Summary |
|---|---|---|---|
| `explayout::available` | public |  | Whether the explayouts extension is active, and the number of published layouts |
| `explayout::layouts::<limit>::<offset>::<status>` | explayouts/read |  | The published layouts |
| `explayout::shared` | explayouts/read |  | The shared layouts (header, footer, ...) |
| `explayout::drafts` | explayouts/read |  | The draft layouts |
| `explayout::view::<id>` | explayouts/read |  | One layout with its zones and block counts |
| `explayout::byidentifier::<identifier>::<status>` | explayouts/read |  | A layout by identifier (published unless status is 1) |
| `explayout::types` | explayouts/read |  | The layout types (explayouts.ini LayoutType_*) with their zones |
| `explayout::blocktypes::<category>` | explayouts/read |  | The block definitions (explayouts.ini BlockDefinition_*) |
| `explayout::zones::<id>` | explayouts/read |  | The zones of a layout, with their link to a shared layout |
| `explayout::zone::<id>::<zone>` | explayouts/read |  | One zone of a layout |
| `explayout::blocks::<id>::<zone>` | explayouts/read |  | The blocks of a zone (a linked zone answers the blocks of the shared layout) |
| `explayout::block::<id>` | explayouts/read |  | One block with its parameters |
| `explayout::blockchildren::<id>` | explayouts/read |  | The child blocks of a container block |
| `explayout::blockparameters::<id>` | explayouts/read |  | The parameters of a block as name => value |
| `explayout::linkedzones` | explayouts/read |  | Every zone that inherits its blocks from a shared layout |
| `explayout::rules::<limit>::<offset>` | explayouts/read |  | The layout rules in priority order |
| `explayout::rule::<id>` | explayouts/read |  | One rule with its targets and conditions |
| `explayout::rulesfor::<id>` | explayouts/read |  | The rules that show a layout |
| `explayout::resolve::<node>` | explayouts/read |  | The layout a content node gets (first matching enabled rule, else the default); changes nothing |
| `explayout::resolvepath::<path>` | explayouts/read |  | The layout a URL path gets |
| `explayout::stats` | explayouts/read |  | Counts of layouts, zones, blocks, rules and block types in use |
| `explayout::createdraft::<id>` | explayouts/edit | POST | Creates (or returns) the draft of a published layout |
| `explayout::publish::<id>` | explayouts/edit | POST | Publishes the draft of a layout |
| `explayout::discard::<id>` | explayouts/edit | POST | Discards the draft of a layout |
| `explayout::enablerule::<id>` | explayouts/edit | POST | Enables a rule |
| `explayout::disablerule::<id>` | explayouts/edit | POST | Disables a rule |
| `explayout::clearcache` | explayouts/edit | POST | Clears the layout resolver cache |

### Audit (read only) (`expaudit`, 19 services)

Policy audit/read, narrowed to the channels of its Channel limitation; every use is recorded as `system.audit.read`. Figures need the audit index (409 when it is not usable). Nothing here verifies, exports or changes the audit.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expaudit::available` | public |  | Whether the audit classes are loaded, whether audit is on and the index usable |
| `expaudit::channels` | audit/read |  | The channels the user may read, with record counts of today |
| `expaudit::recent::<channel>::<limit>::<offset>` | audit/read |  | The latest events, newest first, optionally of one channel |
| `expaudit::search::<q>::<name>::<login>::<result>::<from>::<to>::<limit>::<offset>` | audit/read |  | Events by text, name pattern (access.*), login, result and time range |
| `expaudit::event::<id>` | audit/read |  | One event in full by id |
| `expaudit::related::<id>::<limit>` | audit/read |  | The events of the same request as an event |
| `expaudit::byrequest::<request>::<limit>::<offset>` | audit/read |  | The events of one request id |
| `expaudit::bylogin::<login>::<limit>::<offset>` | audit/read |  | The events of one login |
| `expaudit::byobject::<object>::<limit>::<offset>` | audit/read |  | The events about one object (type or type:id, node:275) |
| `expaudit::refused::<limit>::<offset>` | audit/read |  | The latest refused events |
| `expaudit::failed::<limit>::<offset>` | audit/read |  | The latest failed events |
| `expaudit::names` | audit/read |  | The event names in the index (the name filter suggestions) |
| `expaudit::volume` | audit/read |  | Events of today and of the last 7 days per channel |
| `expaudit::perday::<days>` | audit/read |  | Events per local day for the last N days (at most 30) |
| `expaudit::results` | audit/read |  | Counts of success, refused and failed for today and the week |
| `expaudit::topactors::<limit>` | audit/read |  | The most active logins of today |
| `expaudit::failedlogins` | audit/read |  | Failed logins of the last 24 hours: total and by address |
| `expaudit::chains` | audit/read |  | The hash chain state of each channel as last verified (nothing is verified here) |
| `expaudit::summary` | audit/read |  | The dashboard figures in one answer: volume, results, security, chains |

### Subitems columns (`expsubitems_svc`, 12 services)

The column catalogue and rows of the admin subitems list in the expservices envelope. The block is named `expsubitems_svc` because `expsubitems` is the original ezjscore server function of the admin list, which stays as it is. `rows` takes the columns as an argument (comma list of non built-in column keys).

| Call | Access | Write | Summary |
|---|---|---|---|
| `expsubitems_svc::catalogue` | content/read |  | Every column known to the registry (built-in and INI defined), without the per-parent attribute columns |
| `expsubitems_svc::builtin` | content/read |  | The built-in columns |
| `expsubitems_svc::describe::<key>::<parent>` | content/read |  | One column by key |
| `expsubitems_svc::columns::<parent>` | content/read |  | The columns the user may see under a parent, defaults, presets and the saved choice |
| `expsubitems_svc::defaults::<parent>` | content/read |  | The default column keys under a parent |
| `expsubitems_svc::presets` | content/read |  | The column presets of the INI |
| `expsubitems_svc::pagesizes` | content/read |  | The page sizes the list offers |
| `expsubitems_svc::sortfields::<parent>` | content/read |  | The sort keys a list accepts |
| `expsubitems_svc::groups::<parent>` | content/read |  | The columns grouped by their Group under a parent |
| `expsubitems_svc::preference::<parent>` | content/read |  | The user's own saved column choice for the parent's navigation part |
| `expsubitems_svc::rows::<parent>::<columns>::<limit>::<offset>::<sort>::<ascending>::<filter>` | content/read |  | The children of a parent with the columns asked for (comma list) |
| `expsubitems_svc::settings` | content/read |  | The registry settings: attribute columns, CSV export, CSV limit |

### Newsletters (`expnewsletter`, 13 services)

cjw_newsletter: lists, editions, subscriber counts and a user's own subscriptions. `subscribe` and `unsubscribe` act on the signed-in user's own account e-mail only; a new subscription starts pending and follows the extension's confirmation rules. 404 "not available" when the extension is inactive.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expnewsletter::available` | public |  | Whether cjw_newsletter is active, and how many lists and newsletter users exist |
| `expnewsletter::statuses` | public |  | The subscription status codes and names |
| `expnewsletter::lists::<limit>::<offset>` | content/read |  | The newsletter lists the user may read |
| `expnewsletter::list::<node>` | content/read |  | One newsletter list by node id |
| `expnewsletter::subscriberscount::<node>` | content/read |  | The number of approved subscribers of a list |
| `expnewsletter::statistics::<node>` | newsletter/subscription_list |  | Subscriptions of a list by status |
| `expnewsletter::editions::<node>::<limit>::<offset>` | content/read |  | The editions (issues) below a list node |
| `expnewsletter::edition::<node>` | content/read |  | One edition by node id |
| `expnewsletter::mysubscriptions` | user |  | The logged-in user's own subscriptions (by their account e-mail) |
| `expnewsletter::subscribe::<node>` | user | POST | Subscribes the logged-in user's own e-mail to a list |
| `expnewsletter::unsubscribe::<node>` | user | POST | Removes the logged-in user's own subscription to a list |
| `expnewsletter::users::<limit>::<offset>::<search>` | newsletter/user_list |  | The newsletter users (administration) |
| `expnewsletter::usercount` | newsletter/user_list |  | The number of newsletter users |

### Sitemaps (`expsitemap`, 11 services)

Entries of the content tree as a sitemap lists them (loc, lastmod, changefreq, priority; settings of bcgooglesitemaps.ini), the sitemap index and the generated files.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expsitemap::config` | content/read |  | The sitemap settings (root node, protocol, file name, class filter) |
| `expsitemap::available` | public |  | Whether the sitemap extensions are active |
| `expsitemap::rootnode` | content/read |  | The node the sitemap starts from |
| `expsitemap::entries::<node>::<limit>::<offset>` | content/read |  | Sitemap entries of a subtree: loc, lastmod, changefreq, priority (class filter of the settings applied) |
| `expsitemap::count::<node>` | content/read |  | How many entries a subtree has |
| `expsitemap::entry::<node>` | content/read |  | The sitemap entry of one node |
| `expsitemap::recent::<node>::<limit>` | content/read |  | The most recently changed entries (a news sitemap) |
| `expsitemap::index::<node>` | content/read |  | The sitemap index: the pages of 50000 entries with their entry window |
| `expsitemap::files` | setup/administrate |  | The generated sitemap files (name, size, modified) |
| `expsitemap::classes` | content/read |  | The classes the sitemap includes or excludes |
| `expsitemap::robots` | public |  | The robots.txt Sitemap line for the generated sitemap |

### Statistics (`expstats`, 16 services)

Search phrases, content counts, growth, languages, users and sessions. Content figures follow content/read, the rest needs setup/administrate.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expstats::searchtop::<limit>::<offset>` | setup/administrate |  | The most frequent search phrases with their average result count |
| `expstats::searchtotal` | setup/administrate |  | Number of distinct search phrases and of searches |
| `expstats::searchnoresults::<limit>` | setup/administrate |  | Search phrases that found nothing |
| `expstats::searchphrase::<phrase>` | setup/administrate |  | The statistics of one search phrase |
| `expstats::contenttotals` | content/read |  | Counts of objects, nodes, versions and classes |
| `expstats::classcounts::<limit>` | content/read |  | Published objects per class |
| `expstats::sectioncounts` | content/read |  | Published objects per section |
| `expstats::statecounts` | content/read |  | Objects per object state |
| `expstats::recentpublished::<limit>` | content/read |  | The most recently published objects |
| `expstats::recentmodified::<limit>` | content/read |  | The most recently modified objects |
| `expstats::growth::<days>` | content/read |  | Objects published per day for the last N days (at most 90) |
| `expstats::topowners::<limit>` | setup/administrate |  | The users who own most published objects |
| `expstats::languagecounts` | content/read |  | Published object translations per language |
| `expstats::users` | setup/administrate |  | Number of user accounts, enabled and disabled |
| `expstats::sessions` | setup/administrate |  | Active sessions (not expired) and how many are logged-in users |
| `expstats::overview` | content/read |  | The headline figures in one answer |

### Designs and templates (`expdesign`, 13 services)

Read only, setup/administrate: designs, bases, the overrides of override.ini, the templates a design resolves, and the source of a template inside a design directory (path checked, 512 KB at most).

| Call | Access | Write | Summary |
|---|---|---|---|
| `expdesign::designs` | setup/administrate |  | The design directories of the site and of the extensions |
| `expdesign::current` | setup/administrate |  | The standard design, the site design and the additional designs of this siteaccess |
| `expdesign::bases` | setup/administrate |  | The design bases in priority order (first wins) |
| `expdesign::extensions` | setup/administrate |  | The extensions that provide designs for this siteaccess |
| `expdesign::overrides::<limit>::<offset>::<source>` | setup/administrate |  | The template overrides of override.ini |
| `expdesign::override::<name>` | setup/administrate |  | One override by name |
| `expdesign::overridecount` | setup/administrate |  | How many overrides there are |
| `expdesign::overridesfor::<source>` | setup/administrate |  | The overrides of one template source (node/view/full.tpl) |
| `expdesign::templates::<limit>::<offset>::<prefix>` | setup/administrate |  | The templates of the resolved design, with the design base each comes from |
| `expdesign::templatecount` | setup/administrate |  | How many templates the design resolves |
| `expdesign::resolve::<template>` | setup/administrate |  | Which file a template path resolves to and its overrides |
| `expdesign::source::<path>` | setup/administrate |  | The source of a template of a design directory (design/<name>/templates/..., read only) |
| `expdesign::cachestate` | setup/administrate |  | Template cache settings: compile, cache, override cache and design location cache |

### Languages and translations (`explanguage`, 14 services)

Content languages, language masks, locales, and the progress of the interface translations in share/translations.

| Call | Access | Write | Summary |
|---|---|---|---|
| `explanguage::languages` | public |  | The content languages of the installation with their object counts |
| `explanguage::language::<locale>` | public |  | One content language by locale |
| `explanguage::prioritized` | public |  | The languages in the priority order of this siteaccess |
| `explanguage::top` | public |  | The top priority language of this siteaccess |
| `explanguage::decode::<mask>` | public |  | The languages in a language mask |
| `explanguage::maskfor::<locales>::<always_available>` | public |  | The language mask of some locales |
| `explanguage::locales::<limit>::<offset>` | public |  | The locales the system knows (share/locale) |
| `explanguage::localeinfo::<locale>` | public |  | Names, formats and currency of a locale |
| `explanguage::countries` | public |  | The country codes known to the locales |
| `explanguage::translations` | user |  | The interface translations present in share/translations and their size |
| `explanguage::translationstats::<locale>` | user |  | Messages, finished, unfinished and obsolete of one interface translation |
| `explanguage::translationcontexts::<locale>::<limit>::<offset>` | user |  | The translation contexts (files) of one interface translation with message counts |
| `explanguage::siteaccess` | public |  | The language settings of the current siteaccess |
| `explanguage::objectcounts` | content/read |  | Published object names per language |

### External links (`expurl`, 12 services)

The links stored by URL attributes: lists, search, validity, the objects using a link. `check` requests a stored link with HEAD, without redirects, only for http and https addresses that resolve to public hosts (loopback, private, link-local and credentials are refused with 422).

| Call | Access | Write | Summary |
|---|---|---|---|
| `expurl::list::<limit>::<offset>::<validity>` | url/list |  | The stored links, newest first |
| `expurl::get::<id>` | url/view |  | One link with its check state |
| `expurl::count::<validity>` | url/list |  | Number of stored links |
| `expurl::stats` | url/list |  | Links by validity: valid, invalid, never checked |
| `expurl::invalid::<limit>::<offset>` | url/list |  | The links found invalid |
| `expurl::unchecked::<limit>::<offset>` | url/list |  | The links never checked |
| `expurl::search::<text>::<limit>::<offset>` | url/list |  | Links whose address contains the text |
| `expurl::byurl::<url>` | url/view |  | The stored link with exactly this address |
| `expurl::objects::<id>::<limit>::<offset>` | url/view |  | The objects (readable by the user) that use a link |
| `expurl::bydomain::<limit>` | url/list |  | Link counts per host name |
| `expurl::check::<id>` | url/edit | POST | Requests a stored link (HEAD) and records whether it is valid; only public http/https hosts |
| `expurl::setvalid::<id>` | url/edit | POST | Sets the validity of a stored link by hand |

### PDF (`exppdf`, 10 services)

PDF links of nodes (`content/pdf/<node>`, which checks the policy again when the link is followed) and the PDF export definitions of the pdf module.

| Call | Access | Write | Summary |
|---|---|---|---|
| `exppdf::available` | public |  | Whether PDF export is available (classes present) and how many export definitions exist |
| `exppdf::canpdf::<node>` | content/read |  | Whether the user may export a node as PDF |
| `exppdf::link::<node>::<language>` | content/read |  | The URL that downloads the PDF of a node (optional language) |
| `exppdf::links::<nodes>` | content/read |  | PDF links for several nodes at once (those the user may export) |
| `exppdf::exports::<limit>::<offset>` | pdf/edit |  | The PDF export definitions |
| `exppdf::export::<id>` | pdf/edit |  | One PDF export definition |
| `exppdf::exportcount` | pdf/edit |  | How many PDF export definitions exist |
| `exppdf::exportsfor::<node>` | pdf/edit |  | The export definitions whose source node is a node or one of its ancestors |
| `exppdf::exportfile::<id>` | pdf/edit |  | Whether the generated file of a stored export exists and how large it is |
| `exppdf::statuses` | public |  | The export modes: created once or on the fly |


## Clients

Every service is a plain HTTP call, so any language with an HTTP client and a cookie jar is a client. The contract all
the examples below follow:

1. **Wire format.** `GET|POST <site>/ezjscore/call/<service>[::arg...]?ContentType=json` with `Accept: application/json`.
   ezjscore wraps the answer: `{ "error_text": "", "content": <envelope> }`. `error_text` is set only when the call itself
   could not be routed; the service's own result (or its error) is `content`, see "The envelope". Arguments in the URL are
   percent-encoded and joined with `::`; a write sends its arguments as POST form fields.
2. **Auth.** `POST expsession::login` with `username` and `password` (form encoded). The answer's `Set-Cookie` is the
   session; keep it in a cookie jar. The answer also carries the new session's form token (`data.token`), which replaces any
   token fetched before the login. Anonymous calls work for the services whose access is `public`.
3. **Writes.** Fetch `expsession::token` (`data.token`, `data.field` = `ezxform_token`, `data.header` = `X-CSRF-Token`),
   then `POST` with the token as the field `ezxform_token` and as the header `X-CSRF-Token`. A missing or wrong token
   is a 403.
4. **Errors.** HTTP 200 does not mean success: look at `content.ok`. `content.error.code` is 400/401/403/404/409/422/500
   (table in "Errors"). A 401 means sign in again, a 403 a missing policy or token (fetch a fresh token once and retry
   once, never loop), 404/409/422 are for the user to see. A non-200 HTTP status or a body that is not JSON is a
   transport fault (proxy, TLS, server down): retry with backoff, do not interpret.
5. **Paging.** List services take `offset` and `limit` as their last two URL arguments and answer
   `meta.total`, `meta.offset`, `meta.limit`, `meta.has_more`. Loop while `has_more` is true (or `offset + count < total`);
   keep `limit` at or below `expservices.ini [Paging] MaxLimit` (200).
6. **Discovery.** `expservices::catalog` gives every service with its `args`, `access`, `write`; generate a typed client
   from it, or call `expservices::service::<domain>::<method>` for one descriptor.

In the examples `$SITE` is the base URL, e.g. `https://alpha.se7enx.com` (or `https://alpha.se7enx.com/<siteaccess>`
when the installation needs a siteaccess in the path), `expnode::children` is a paged read and `expforum::reply` stands for
any write service (take a real one from the catalogue). The shell and Python clients are in `extension/expservices/bin/`
and are tested against the live services; the other snippets follow the same contract and are meant as the
starting point of an app.

### Shell (bash + curl): `extension/expservices/bin/expservices-client.sh`

    export EXPSERVICES_URL=https://alpha.se7enx.com
    expservices-client.sh whoami                          # anonymous or signed in, pretty printed
    expservices-client.sh catalog session                 # the services of one domain
    expservices-client.sh call expsystem::version         # any read call; arguments follow, joined with ::
    expservices-client.sh login editor                    # asks for the password (or EXPSERVICES_PASSWORD); cookie jar kept
    expservices-client.sh page expnode::children 2 --limit 50 --max 500   # every item of a paged service, one JSON per line
    expservices-client.sh post expforum::reply topic=123 'message=Hello' # write: token fetched and sent as field and header
    expservices-client.sh --raw call expsession::ping     # the answer as received

The cookie jar is `${XDG_CACHE_HOME:-~/.cache}/expservices/cookies.txt` (`--jar` or `EXPSERVICES_JAR` change it). Exit status:
0 ok, 1 service error (`error 401: ...` on stderr), 2 transport fault. It needs bash and curl, plus jq (or python3) for the JSON.

### Python: `extension/expservices/bin/expservices_client.py`

Standard library only; a module and a command line (`expservices_client.py call expsystem::version`, `page`, `post`, `login`...).

    from expservices_client import Client, ServiceError, TransportError

    c = Client("https://alpha.se7enx.com", jar="expservices.cookies")   # jar optional; keeps the session between runs
    c.login("editor", "secret")                                         # POST, stores the new token
    version = c.call("expsystem::version")                              # read: returns data
    for node in c.pages("expnode::children", 2, limit=50):              # paged read: yields every item
        print(node["name"])
    try:
        c.post("expforum::reply", {"topic": 123, "message": "Hello"})   # write: field + header token
    except ServiceError as e:                                           # the envelope's error
        print(e.code, e)                                                # 401 login again, 403 policy/token, 422 data
    except TransportError as e:                                         # HTTP/network/JSON fault
        print("retry later:", e)

### Kotlin / Android (OkHttp)

    // build.gradle: implementation("com.squareup.okhttp3:okhttp:4.12.0"); org.json comes with Android
    class Expservices(val site: String) {
        private val jar = HashMap<String, List<Cookie>>()                 // use PersistentCookieJar for a real app
        private val http = OkHttpClient.Builder().cookieJar(object : CookieJar {
            override fun saveFromResponse(url: HttpUrl, cookies: List<Cookie>) { jar[url.host] = cookies }
            override fun loadForRequest(url: HttpUrl) = jar[url.host] ?: emptyList()
        }).build()

        class ServiceException(val code: Int, message: String) : Exception(message)

        private fun url(service: String, args: List<Any> = emptyList()) =
            "$site/ezjscore/call/$service" + args.joinToString("") { "::" + Uri.encode(it.toString()) } + "?ContentType=json"

        private fun send(req: Request): JSONObject {                      // returns the envelope, throws on error
            http.newCall(req).execute().use { r ->
                if (!r.isSuccessful) throw IOException("HTTP ${r.code}")
                val outer = JSONObject(r.body!!.string())
                if (outer.optString("error_text").isNotEmpty()) throw ServiceException(500, outer.getString("error_text"))
                val env = outer.getJSONObject("content")
                if (!env.getBoolean("ok")) env.getJSONObject("error").let { throw ServiceException(it.getInt("code"), it.getString("message")) }
                return env
            }
        }

        // auth
        fun login(user: String, pass: String): JSONObject = send(Request.Builder().url(url("expsession::login"))
            .post(FormBody.Builder().add("username", user).add("password", pass).build()).build())

        // read, with paging
        fun children(nodeId: Int, offset: Int = 0, limit: Int = 25): JSONObject =
            send(Request.Builder().url(url("expnode::children", listOf(nodeId, offset, limit))).build())
        // loop: do { val e = children(2, off); ...; off += 25 } while (e.getJSONObject("meta").getBoolean("has_more"))

        // write with the form token
        fun write(service: String, fields: Map<String, String>): JSONObject {
            val t = send(Request.Builder().url(url("expsession::token")).build()).getJSONObject("data").getString("token")
            val body = FormBody.Builder().add("ezxform_token", t)
            fields.forEach { (k, v) -> body.add(k, v) }
            return send(Request.Builder().url(url(service)).header("X-CSRF-Token", t).post(body.build()).build())
        }
    }
    // error handling: catch ServiceException (401: show the sign-in screen; 403: refresh the token once; 422: show the message)
    // and IOException (offline: retry with backoff). Run it off the main thread (coroutines Dispatchers.IO).

### Swift / iOS (URLSession)

    struct ServiceError: Error { let code: Int; let message: String }

    final class Expservices {
        let site: URL                                    // https://alpha.se7enx.com
        let session = URLSession(configuration: .default)   // the default configuration keeps cookies (HTTPCookieStorage.shared)
        init(site: URL) { self.site = site }

        private func url(_ service: String, _ args: [Any] = []) -> URL {
            let tail = args.map { "::" + "\($0)".addingPercentEncoding(withAllowedCharacters: .alphanumerics)! }.joined()
            return URL(string: "\(site)/ezjscore/call/\(service)\(tail)?ContentType=json")!
        }
        private func envelope(_ req: URLRequest) async throws -> [String: Any] {
            let (data, resp) = try await session.data(for: req)
            guard (resp as? HTTPURLResponse)?.statusCode == 200,
                  let outer = try JSONSerialization.jsonObject(with: data) as? [String: Any],
                  let env = outer["content"] as? [String: Any] else { throw ServiceError(code: 0, message: "transport fault") }
            if env["ok"] as? Bool != true {
                let e = env["error"] as? [String: Any] ?? [:]
                throw ServiceError(code: e["code"] as? Int ?? 500, message: e["message"] as? String ?? "error")
            }
            return env
        }
        private func form(_ url: URL, _ fields: [String: String], token: String? = nil) -> URLRequest {
            var r = URLRequest(url: url); r.httpMethod = "POST"
            var f = fields; if let t = token { f["ezxform_token"] = t; r.setValue(t, forHTTPHeaderField: "X-CSRF-Token") }
            var c = URLComponents(); c.queryItems = f.map { URLQueryItem(name: $0.key, value: $0.value) }
            r.httpBody = c.percentEncodedQuery?.data(using: .utf8)
            r.setValue("application/x-www-form-urlencoded", forHTTPHeaderField: "Content-Type"); return r
        }

        func login(_ user: String, _ pass: String) async throws -> [String: Any] {          // auth
            try await envelope(form(url("expsession::login"), ["username": user, "password": pass]))
        }
        func children(_ node: Int, offset: Int = 0, limit: Int = 25) async throws -> [String: Any] {   // read, paged
            try await envelope(URLRequest(url: url("expnode::children", [node, offset, limit])))     // meta.has_more drives the next page
        }
        func write(_ service: String, _ fields: [String: String]) async throws -> [String: Any] {     // write
            let t = ((try await envelope(URLRequest(url: url("expsession::token"))))["data"] as! [String: Any])["token"] as! String
            return try await envelope(form(url(service), fields, token: t))
        }
    }
    // Errors: catch ServiceError (401 sign in, 403 refresh token once, 422 show message); other errors are transport faults.

### Objective-C (NSURLSession)

    // The shared session keeps cookies. Wrap in a method taking a completion block; shown here for one write.
    - (void)post:(NSString *)service fields:(NSDictionary<NSString *, NSString *> *)fields
      completion:(void (^)(NSDictionary *data, NSError *error))done {
        NSString *base = @"https://alpha.se7enx.com/ezjscore/call/";
        NSURL *tokenURL = [NSURL URLWithString:[base stringByAppendingString:@"expsession::token?ContentType=json"]];
        [[NSURLSession.sharedSession dataTaskWithURL:tokenURL completionHandler:^(NSData *d, NSURLResponse *r, NSError *e) {
            NSDictionary *env = [NSJSONSerialization JSONObjectWithData:d options:0 error:nil][@"content"];   // d nil = transport fault
            if (e || ![env[@"ok"] boolValue]) { done(nil, e ?: [self errorFrom:env]); return; }
            NSString *token = env[@"data"][@"token"];
            NSMutableArray *pairs = [NSMutableArray arrayWithObject:[@"ezxform_token=" stringByAppendingString:token]];
            [fields enumerateKeysAndObjectsUsingBlock:^(NSString *k, NSString *v, BOOL *s) {
                [pairs addObject:[NSString stringWithFormat:@"%@=%@", k,
                    [v stringByAddingPercentEncodingWithAllowedCharacters:NSCharacterSet.alphanumericCharacterSet]]];
            }];
            NSString *u = [NSString stringWithFormat:@"%@%@?ContentType=json", base, service];
            NSMutableURLRequest *req = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:u]];
            req.HTTPMethod = @"POST"; req.HTTPBody = [[pairs componentsJoinedByString:@"&"] dataUsingEncoding:NSUTF8StringEncoding];
            [req setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
            [req setValue:token forHTTPHeaderField:@"X-CSRF-Token"];
            [[NSURLSession.sharedSession dataTaskWithRequest:req completionHandler:^(NSData *d2, NSURLResponse *r2, NSError *e2) {
                NSDictionary *env2 = d2 ? [NSJSONSerialization JSONObjectWithData:d2 options:0 error:nil][@"content"] : nil;
                done([env2[@"ok"] boolValue] ? env2[@"data"] : nil, e2 ?: ([env2[@"ok"] boolValue] ? nil : [self errorFrom:env2]));
            }] resume];
        }] resume];
    }
    - (NSError *)errorFrom:(NSDictionary *)env {      // { ok:false, error:{ code, message } } -> NSError (domain "expservices")
        NSDictionary *err = env[@"error"];
        return [NSError errorWithDomain:@"expservices" code:[err[@"code"] integerValue]
                               userInfo:@{NSLocalizedDescriptionKey: err[@"message"] ?: @"transport fault"}];
    }
    // Login: the same POST without token to expsession::login with username and password (cookies are kept by the session).
    // Read with paging: GET .../expnode::children::2::<offset>::<limit>, continue while env[@"meta"][@"has_more"] is true.

### Qt / C++ (QNetworkAccessManager)

    // QT += network. One QNetworkAccessManager per client; set a QNetworkCookieJar (the default one keeps session cookies).
    class Expservices : public QObject {
        Q_OBJECT
        QNetworkAccessManager net; QString site = "https://alpha.se7enx.com"; QString token;
        QUrl url(const QString &svc, const QStringList &args = {}) const {
            QString u = site + "/ezjscore/call/" + svc;
            for (const QString &a : args) u += "::" + QString::fromUtf8(QUrl::toPercentEncoding(a));
            return QUrl(u + "?ContentType=json");
        }
        // Parses the answer; emits failed(code, message) for { ok:false }, for a non-200 status or a body that is not JSON.
        void handle(QNetworkReply *r, std::function<void(const QJsonObject &)> ok) {
            connect(r, &QNetworkReply::finished, this, [=] {
                const QByteArray body = r->readAll(); const int http = r->attribute(QNetworkRequest::HttpStatusCodeAttribute).toInt();
                r->deleteLater();
                const QJsonObject env = QJsonDocument::fromJson(body).object().value("content").toObject();
                if (r->error() != QNetworkReply::NoError && http != 200) { emit failed(0, r->errorString()); return; }   // transport
                if (!env.value("ok").toBool()) { const auto e = env.value("error").toObject();
                    emit failed(e.value("code").toInt(500), e.value("message").toString()); return; }                  // service
                ok(env);
            });
        }
    public:
        void login(const QString &user, const QString &pass) {                        // auth: POST, no token needed
            QNetworkRequest rq(url("expsession::login"));
            rq.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
            QUrlQuery q; q.addQueryItem("username", user); q.addQueryItem("password", pass);
            handle(net.post(rq, q.toString(QUrl::FullyEncoded).toUtf8()), [this](const QJsonObject &env) {
                token = env["data"].toObject()["token"].toString(); emit signedIn(env["data"].toObject()); });
        }
        void children(int node, int offset, int limit) {                              // read, paged
            handle(net.get(QNetworkRequest(url("expnode::children", {QString::number(node), QString::number(offset), QString::number(limit)}))),
                   [this](const QJsonObject &env) { emit page(env["data"].toArray(), env["meta"].toObject()["has_more"].toBool()); });
        }
        void write(const QString &svc, const QMap<QString, QString> &fields) {         // write: field + header token
            QNetworkRequest rq(url(svc));
            rq.setHeader(QNetworkRequest::ContentTypeHeader, "application/x-www-form-urlencoded");
            rq.setRawHeader("X-CSRF-Token", token.toUtf8());
            QUrlQuery q; q.addQueryItem("ezxform_token", token);
            for (auto it = fields.begin(); it != fields.end(); ++it) q.addQueryItem(it.key(), it.value());
            handle(net.post(rq, q.toString(QUrl::FullyEncoded).toUtf8()), [this](const QJsonObject &env) { emit written(env["data"]); });
        }
    signals: void failed(int code, const QString &message); void signedIn(const QJsonObject &); void page(const QJsonArray &, bool more); void written(const QJsonValue &);
    };
    // The token comes from the login answer; after a 403 fetch expsession::token once and retry once.

### GTK / Python (requests, or libsoup)

With PyGObject, run the calls off the main loop (`GLib.idle_add` or a thread) and hand the data to the widgets. `requests` is
the simplest transport; the shell/Python client above has the same logic without it.

    import requests
    from gi.repository import GLib

    class Api:
        def __init__(self, site): self.site, self.s, self.token = site, requests.Session(), None   # Session keeps cookies
        def _url(self, svc, args=()): return f"{self.site}/ezjscore/call/{svc}" + "".join("::" + requests.utils.quote(str(a), safe="") for a in args) + "?ContentType=json"
        def _env(self, r):
            r.raise_for_status()                                  # transport fault: HTTPError
            env = r.json()["content"]
            if not env["ok"]: raise RuntimeError(f'{env["error"]["code"]}: {env["error"]["message"]}')
            return env
        def login(self, u, p): env = self._env(self.s.post(self._url("expsession::login"), data={"username": u, "password": p})); self.token = env["data"]["token"]; return env["data"]
        def read(self, svc, *args): return self._env(self.s.get(self._url(svc, args)))            # paged: args end with offset, limit
        def write(self, svc, **fields):
            self.token = self.token or self.read("expsession::token")["data"]["token"]
            return self._env(self.s.post(self._url(svc), data={"ezxform_token": self.token, **fields}, headers={"X-CSRF-Token": self.token}))

    # in a handler: threading.Thread(target=lambda: GLib.idle_add(fill_list, api.read("expnode::children", 2, 0, 25)["data"])).start()

libsoup (Soup 3): `Soup.Session()` keeps cookies when a `Soup.CookieJar` feature is added
(`session.add_feature(Soup.CookieJar())`); `Soup.Message.new_from_encoded_form("POST", url, Soup.form_encode(...))` builds the POST,
`msg.get_request_headers().append("X-CSRF-Token", token)` adds the header, and `session.send_and_read_async(msg, ...)` returns the
bytes of the same `{ error_text, content }` JSON.

### KDE and GNOME integration notes

- **KDE (Qt/KF6).** Use the Qt client above. Store the password in KWallet (`KWallet::Wallet::openWallet`) and never in the
  config file; only the session cookie lives in memory (or `QNetworkCookieJar` persisted to `~/.cache`). Show the services
  of the catalogue as a KRunner plugin (query `expsearch::search`), a Plasma widget (read services on a timer, `expsession::ping`
  keeps the session), or KIO slave-like browsing of `expnode::children`. `KJob`/`QNetworkReply` give the async model; KNotification
  for 401 ("sign in again").
- **GNOME (GTK4/libadwaita).** Use the GTK/Python client or `Soup 3` from C/Vala/Rust. Store the password with libsecret
  (`Secret.password_store_sync`); show the services in a GNOME Shell search provider (D-Bus `org.gnome.Shell.SearchProvider2`
  backed by `expsearch::search`), a libadwaita app with an `Adw.NavigationView` per node level, `Gio.ListStore` filled page by
  page. `Gio.Task`/`GLib.idle_add` keep the UI responsive; GNotification for 401.
- **Both.** Discover with `expservices::catalog` at start (cache by `expservices::version`), build forms from the `args`
  of a write service, send writes only after the user's action, and treat every 403 as "refresh the token once".
  Online accounts: KDE (KAccounts) and GNOME (GOA) have no generic "cookie session" provider, so keep the account in the app.

### Tests

`python3 ai/bin/one/test_expservices_shell_and_python_clients.py` runs the shell and Python clients against the live site
(read-only calls; the write path is exercised up to the 401 an anonymous session gets).

## Frontend designs: React and wireframe

Both are designs of the extension (`extension/expservices/design/`), shown by test siteaccesses and reached by URI:

| Design | Siteaccess / URL | What |
|---|---|---|
| `expportal_react` | `/portalreact/` | the portal in React 18 with React Bootstrap 2.10 (Bootstrap 5.3), hash routes `#/news`, `#/shop`, `#/shop/basket`, `#/forums`, `#/media`, `#/feeds`, `#/search`, `#/login`, `#/profile` |
| `expportal_wireframe` | `/portalwireframe/` | low-fidelity screens (grey boxes) with an annotation per block naming the service that feeds it, a phone/desktop frame toggle and a notes toggle; with the catalogue readable each service name is marked present or missing |

**No build step; vendored libraries.** The React design needs no Node, JSX or bundler: its code is ES modules
(`javascript/portal/`) using `React.createElement` (`h`). The libraries are vendored into the design
(`javascript/vendor/`, `stylesheets/vendor/`) so the site works offline and the versions cannot move under it: React and
ReactDOM 18.3.1 (UMD) and React Bootstrap 2.10.2 from `cdnjs.cloudflare.com/ajax/libs/` and Bootstrap 5.3.3 CSS. To upgrade, download the
same file names from cdnjs at the new version. Layout: `services.js` (the client: fetch, session cookie, form token,
envelope unwrapping, `ServiceError`), `api.js` (one function per screen need, the only file that names services),
`ui.js` (kit shortcuts, `useAsync`, pager, error and loading states), `pages/*.js` (one component per page), `app.js` (shell and hash router).

**Siteaccesses.** `portalreact` and `portalwireframe` are directories `settings/siteaccess/<name>/site.ini.append.php`
(`SiteDesign` = the design, `IndexPage=user/login` so the shell renders with a 200) plus the entry `AvailableSiteAccessList[]=<name>` in the global
override, added with `exp:ini add site/SiteAccessSettings/AvailableSiteAccessList[] <name> override`. Reverse it with
`exp:ini rem` for the same entry and removing the directory; the live `site` siteaccess is not touched. The extension
registers its designs with `settings/design.ini.append.php` (`DesignExtensions[]=expservices`); without that file an extension design is not found.

## Frontends: jQuery 4 reference and reactive jQuery

Two portal designs in `extension/expservices/design/` consume the services from the browser. Both are corporate
portals (home, news list and article, shop catalogue, product and basket, forums with topics and replies, media
gallery, feeds, search, login and profile), rendered client-side, with no build step, on the jQuery 4 that ezjscore
ships (`ezjsc::jquery`). Both are accessible (skip link, landmarks, `aria-live` announcements, focus moved to the page
on navigation, labelled forms, native `<dialog>` for the lightbox), responsive (mobile first, one breakpoint at 48em)
and light or dark (`prefers-color-scheme`, plus a toggle remembered in `localStorage`).

| Design | Siteaccess (test) | URL | Idea |
|---|---|---|---|
| `expportal_jquery` | `portaljq` | `/portaljq/` | the reference standard: small modules, one script per feature, pages build DOM directly |
| `expportal_reactive` | `portalreactive` | `/portalreactive/` | the same features with a React-like model: components, store, one-way flow |

### Architecture (expportal_jquery)

`templates/pagelayout.tpl` is the only template. It ignores the module result, prints the page frame, passes the
configuration as `data-` attributes of `<body>` (siteaccess URL, `ezjscore/call` URL, content root node ids from
`extension/expservices/settings/expportal.ini`), loads jQuery 4 through `ezscript_load( 'ezjsc::jquery' )` and then
the scripts in `javascript/portal/`, one per feature:

| File | Role |
|---|---|
| `core.js` | config, **the service table** (`ExpPortal.services`), `api.call()`, DOM helpers (text is always set as text), UI states, hash router, theme |
| `home.js`, `news.js`, `shop.js`, `forums.js`, `media.js`, `feeds.js`, `search.js`, `account.js` | one feature each: registers its routes with `router.add( '/news/:id', handler )` |
| `app.js` | boot: chrome, service catalogue, who is logged in, basket count, router |

The address is the hash route (`#/news/12`), so every portal URL is served by the one pagelayout and the portal can be
dropped under any siteaccess. `api.call( feature, args, post )` builds
`<siteaccess>/ezjscore/call/<service>::<arg>::<arg>?ContentType=json`, sends reads as GET and writes as POST with the
form token (`expsession::token`, field `ezxform_token`, header `X-CSRF-Token`; the login is the one write sent without
it), unwraps `{ error_text, content }` and the service envelope `{ ok, data, meta }`, and rejects with
`{ code, message }`. At boot it reads `expservices::catalog`: a service that is **not in the catalogue is not called**
and its page shows a clear "service not available" state instead of an error; if the catalogue itself cannot be read,
calls are tried and a 404 or "no such function" answer gets the same state.

### The reactive variant (expportal_reactive)

It falls back to `expportal_jquery` (`AdditionalSiteDesignList`), so `core.js` (api, services, router, theme) and
`portal.css` are shared. Its own scripts in `javascript/reactive/` implement the pattern, documented in each file:

```
state --(components)--> description --(vdom: keyed diff)--> DOM
  ^                                                            |
  |   dispatch( action ) <--------- event handlers ------------+
  +--- reducer( state, action ) -> new state;  effects run after the reducer and call services
```

| File | Role |
|---|---|
| `vdom.js` | `h( tag, props, ...children )` returns a description; `mount( container, view )` renders and re-renders it. Keyed list reconciliation (nodes move, keep focus and typed text), one delegating listener per event type (handlers are replaced, never stacked), `ref` callback, no way to inject HTML |
| `store.js` | `createStore( reducer )`: `getState`, `dispatch`, `subscribe` (only on a changed state), `effect`; `combine()` for slices |
| `components.js` | every component is a pure function `( state, dispatch ) -> description`; the route table (pattern, page, the resources the page needs) |
| `effects.js` | the only place that calls services: `load( key, feature, args )` dispatches `res/request`, `res/success` or `res/failure`; form posts, login, basket |
| `app.js` | the reducers (`route`, `user`, `basket`, `res`, `form`, `lightbox`, `feedOpen`), the store and the mounts (`#main`, `#nav`, `#account-slot`, `#basket-count`) |

A page never fetches: the route reducer stores the route, the effect reads the route table's `needs` and loads the
missing resources into `state.res[ key ]` (`loading`, `ok`, `error`, `unavailable`), the component reads that and draws.

### How each page maps to services

The one table is `ExpPortal.services` in `core.js`; `ExpPortal.argMap` turns the portal's `( node, offset, limit )`
into the positional order of a service. To follow a renamed service, change the table only.

| Page (route) | Services |
|---|---|
| Home `/` | `expnode::children` (news), `expproduct::list` (shop), `expimage::list` (media) |
| News `/news`, article `/news/:id` | `expnode::children`, `expnode::get` |
| Shop `/shop`, product `/shop/:id`, basket `/basket` | `expproduct::list`, `expproduct::view`, `expbasket::view`, `expbasket::add`, `expbasket::remove` |
| Forums `/forums`, topics `/forums/:id`, topic `/forums/topic/:id` | `expnode::children` (forums), `expforum::topics`, `expforum::replies`, `expforum::reply`, `expforum::create_topic` |
| Media `/media` | `expimage::list` |
| Feeds `/feeds` | `expfeed::list`, `expfeed::items` |
| Search `/search?q=` | `expsearch::search` |
| Login `/login`, profile `/profile`, header | `expsession::login`, `expsession::logout`, `expsession::whoami`, `expsession::token`, `expuser::profile` |
| every page | `expservices::catalog` (which services exist) |

Forum, feed, search and profile services were not yet in the catalogue when the designs were written; their names are
the contract names and those pages show the "not available" state until the services exist. Content root node ids
(news, shop, forums, media) are in `expportal.ini`; the defaults are the content root (2) and Media (43).

### How to run

The test siteaccesses are local settings (not committed): `bash ai/bin/one/expportal_create_test_siteaccesses.sh`
writes `settings/siteaccess/portaljq` and `portalreactive` and adds them to `[SiteAccessSettings]
AvailableSiteAccessList` of the global override with `exp:ini` (reversible: delete the two directories, and
`./console exp:ini rem 'site/SiteAccessSettings/AvailableSiteAccessList[]' <name> override`). They match by URI, so
open `https://alpha.se7enx.com/portaljq/` and `https://alpha.se7enx.com/portalreactive/`; the live `site` siteaccess
and its design are untouched. `DefaultPage` is `user/login` because that module answers 200 to anonymous users and the
pagelayout replaces its output; Velocity workers need a restart to see a new siteaccess, Apache/FPM do not.

### Tests

`python3 ai/bin/one/expportal_playwright_test_portals.py` runs both portals at 960 px with device scale 2 and at 390 px:
every page renders its heading and no unfinished state, the menu (and the phone menu toggle) navigates, search and the
theme toggle work, no horizontal scroll, no console error and no jQuery Migrate warning (the code is jQuery 4 clean:
no `$.isArray`, `$.trim` and the like). In-page unit tests cover the core (node shapes, list folding, URL building,
unavailable handling, text safety) and, for the reactive design, keyed reordering with node identity kept, handler
replacement, text safety, the store (reducer, subscribe on change only, effects) and the route table. expui's browser
harness (`/expui/test`) is an admin page tied to admin sessions, so the portal tests run in the Playwright page instead.

## Users and access (nine domains, 195 services)

Classes in `extension/expservices/classes/users/`, tests in `tests/tests/extension/expservices/users/` (113 tests, run
against the live database; they create a test group, test users, a test role, test sessions and collaboration items and
remove them again). Users are addressed by content object id, user groups by node id, roles and policies by id.

Rules that hold for every service of these domains:

- Nothing returns a password hash, activation or reset key, session key or session data. Login, e-mail and account state
  of a user are only returned for oneself or for a user the caller may edit (`content/edit` on the user object); other
  callers get the public part (id, name, node, class).
- Writes are POST with the form token (`expsession::token`), checked against the policy shown in the table, and recorded
  as `service.<domain>.<method>` plus, where the kernel records one, its own `access.*` event (role, policy, user,
  password and reset events are written by the kernel code the services call).
- Roles are edited like the role editor does: `exprole::draftCreate`, `draftAddPolicy`, `draftRemovePolicy`, `publish`
  (or `draftDiscard`); `exppolicy` changes the published role directly and validates module, function and limitation
  names against the module definition.
- `expaccount` carries the public flows. The reset and activation keys travel by e-mail only and expire after 24 hours
  through the services; `forgotRequest` answers the same for known and unknown addresses. Where a write is open to
  visitors its access is the login or registration policy of the user module (the catalogue allows no public write).
- A collaboration item is visible to its participants only (everybody else gets 404); approve and deny need the approver role
  and a waiting approval, and leave the workflow to resume exactly as the kernel's own view does.
- `expsessionadmin` names a session by a short derived identifier; listing and ending other sessions needs
  `setup/administrate`. Session counts read the `ezsession` table, which is empty when the PHP file session handler is used
  (`expsessionadmin::settings` says which handler is active).
With great power comes great responsibility.

#### `expuser` (42 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `expuser::current` | public |  |  | The current user: id, name, groups, and the private data of oneself |
| `expuser::fetch` | user |  | id:int | A user by content object id; login, e-mail and state only for oneself or users the caller may edit |
| `expuser::byLogin` | user |  | login:string | A user by login name |
| `expuser::byEmail` | user |  | email:string | A user by e-mail address, only when the caller may see that user's private data |
| `expuser::exists` | public |  | login:string | Whether a login name is taken |
| `expuser::search` | user |  | query:string, limit:int, offset:int | Search users by login or name (2 characters or more) |
| `expuser::listAll` | user |  | limit:int, offset:int | All users, paged |
| `expuser::count` | user |  |  | The number of users |
| `expuser::listByGroup` | user |  | group:int, limit:int, offset:int | The users directly in a user group (group node id), paged |
| `expuser::loggedIn` | setup/administrate |  | limit:int, offset:int | The users with an active session |
| `expuser::loggedInCount` | setup/administrate |  |  | Logged in registered and anonymous sessions |
| `expuser::isOnline` | user |  | id:int | Whether a user has an active session |
| `expuser::profile` | user |  | id:int | The content attributes of a user (not the account), oneself when no id |
| `expuser::updateProfile` | user | POST | id:int, fields:json | Update profile attributes of a user the caller may edit (POST id, fields JSON) |
| `expuser::create` | user | POST | group:int, login:string, email:string, password:string, fields:json, enabled:bool, class:string | Create a user in a group (POST group, login, email, password, fields, enabled, class) |
| `expuser::remove` | user | POST | id:int | Move a user to the trash (POST id) |
| `expuser::enable` | user | POST | id:int | Enable an account (POST id) |
| `expuser::disable` | user | POST | id:int | Disable an account and end its sessions (POST id) |
| `expuser::unlock` | user | POST | id:int | Reset the failed login counter (POST id) |
| `expuser::setPassword` | user | POST | id:int, password:string | Set another user's password, ends their sessions (POST id, password) |
| `expuser::changeEmail` | user | POST | id:int, email:string | Change the e-mail address of a user the caller may edit (POST id, email) |
| `expuser::changeLogin` | user | POST | id:int, login:string | Change the login name of a user the caller may edit (POST id, login) |
| `expuser::loginInfo` | user |  | id:int | Login count, last visit, failed attempts, lock and online state |
| `expuser::lastVisit` | user |  | id:int | The last visit of a user |
| `expuser::settings` | user |  | id:int | Account settings: enabled and the maximum number of logins |
| `expuser::setMaxLogin` | user | POST | id:int, max_login:int | Change the maximum number of simultaneous logins (POST id, max_login) |
| `expuser::validateLogin` | public |  | login:string | Check a login name against the validation rules and whether it is taken |
| `expuser::validatePassword` | public |  | password:string | Check a password against the length rule |
| `expuser::passwordPolicy` | public |  |  | The password and e-mail rules of the site |
| `expuser::generatePassword` | user |  | length:int | A generated password |
| `expuser::rolesOf` | role/list |  | id:int | The roles a user has, directly and through groups |
| `expuser::groupsOf` | user |  | id:int | The groups a user is a member of |
| `expuser::effectivePolicies` | role/list |  | id:int, limit:int, offset:int | The effective module/function access of a user, paged |
| `expuser::limitations` | role/list |  | id:int | The limited role assignments of a user |
| `expuser::hasAccess` | public |  | module:string, function:string | Whether the current user has access to a module and function |
| `expuser::hasAccessOf` | role/list |  | id:int, module:string, function:string | Whether a given user has access to a module and function |
| `expuser::canNode` | public |  | node:int | The current user's rights on a node: read, edit, remove, create, move, hide, translate |
| `expuser::accessSummary` | user |  |  | The modules and functions the current user has any access to |
| `expuser::canLoginTo` | role/list |  | id:int, siteaccess:string | Whether a user may log in to a siteaccess |
| `expuser::hashTypes` | user |  |  | The password hash types and the default |
| `expuser::userClasses` | user |  |  | The content class identifiers of users and of user groups |
| `expuser::anonymous` | public |  |  | The id of the anonymous user |

#### `expusergroup` (20 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `expusergroup::root` | user |  |  | The users root node, the default placement and the group classes |
| `expusergroup::tree` | user |  | group:int, depth:int | The group tree below a node, to a depth (default the users root, depth 3) |
| `expusergroup::fetch` | user |  | group:int | A user group by node id |
| `expusergroup::children` | user |  | group:int, limit:int, offset:int | The subgroups of a group, paged |
| `expusergroup::subgroupCount` | user |  | group:int | The number of subgroups |
| `expusergroup::path` | user |  | group:int | The path from the root to a group |
| `expusergroup::parent` | user |  | group:int | The parent group |
| `expusergroup::search` | user |  | query:string, limit:int, offset:int | Search groups by name |
| `expusergroup::members` | user |  | group:int, limit:int, offset:int | The users directly in a group, paged |
| `expusergroup::memberCount` | user |  | group:int | The number of users directly in a group |
| `expusergroup::isMember` | user |  | user:int, group:int | Whether a user is directly in a group |
| `expusergroup::ofUser` | user |  | id:int | The groups of a user |
| `expusergroup::rolesOf` | role/list |  | group:int | The roles assigned to a group |
| `expusergroup::create` | user | POST | parent:int, name:string, class:string, fields:json | Create a group (POST parent, name, class, fields) |
| `expusergroup::rename` | user | POST | group:int, name:string | Rename a group (POST group, name) |
| `expusergroup::remove` | user | POST | group:int, force:bool | Move a group to the trash (POST group, force for a group with children) |
| `expusergroup::addMember` | user | POST | group:int, user:int | Add a user to a group, a new location (POST group, user) |
| `expusergroup::removeMember` | user | POST | group:int, user:int | Remove a user from a group, never the last one (POST group, user) |
| `expusergroup::moveMember` | user | POST | user:int, from:int, to:int | Move a user from one group to another (POST user, from, to) |
| `expusergroup::addMembers` | user | POST | group:int, users:list | Add up to 100 users to a group (POST group, users) |

#### `exprole` (26 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `exprole::listAll` | role/list |  | limit:int, offset:int | All roles, paged |
| `exprole::count` | role/list |  |  | The number of roles |
| `exprole::fetch` | role/list |  | id:int | A role by id |
| `exprole::view` | role/view |  | id:int | A role with its policies and assignments |
| `exprole::byName` | role/list |  | name:string | A role by name |
| `exprole::exists` | role/list |  | name:string | Whether a role name exists |
| `exprole::policies` | role/view |  | id:int, limit:int, offset:int | The policies of a role, paged |
| `exprole::policyCount` | role/view |  | id:int | The number of policies of a role |
| `exprole::assignments` | role/view |  | id:int, limit:int, offset:int | Who a role is assigned to, with limitations |
| `exprole::assignmentsOf` | role/view |  | object:int | The roles assigned to a user or group object |
| `exprole::byLimitation` | role/view |  | identifier:string, value:string | Roles assigned with a limitation (Subtree/Section, value) |
| `exprole::create` | role/edit | POST | name:string | Create an empty role (POST name) |
| `exprole::rename` | role/edit | POST | id:int, name:string | Rename a role (POST id, name) |
| `exprole::copy` | role/edit | POST | id:int, name:string | Copy a role with its policies (POST id, optional name) |
| `exprole::remove` | role/edit | POST | id:int, force:bool | Remove a role (POST id, force when it is assigned) |
| `exprole::draftCreate` | role/edit | POST | id:int | Start editing: create (or return) the draft of a role (POST id) |
| `exprole::draft` | role/edit |  | id:int | The draft of a role |
| `exprole::draftRename` | role/edit | POST | id:int, name:string | Rename the draft (POST id, name) |
| `exprole::draftAddPolicy` | role/edit | POST | id:int, module:string, function:string, limitations:json | Add a policy to the draft (POST id, module, function, limitations JSON) |
| `exprole::draftRemovePolicy` | role/edit | POST | id:int, policy:int | Remove a policy from the draft (POST id, policy) |
| `exprole::draftDiscard` | role/edit | POST | id:int | Throw the draft away (POST id) |
| `exprole::publish` | role/edit | POST | id:int | Publish the draft: it replaces the policies of the role (POST id) |
| `exprole::assign` | role/assign | POST | id:int, object:int, limit:string, limit_value:string | Assign a role to a user or group (POST id, object, limit subtree/section, limit_value) |
| `exprole::unassign` | role/assign | POST | id:int, object:int, assignment:int | Remove an assignment (POST id, object, optional assignment id) |
| `exprole::assigned` | role/view |  | id:int | The objects (users, groups) a role is assigned to |
| `exprole::usersWith` | role/view |  | id:int, limit:int, offset:int | The users and groups holding a role |

#### `exppolicy` (16 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `exppolicy::fetch` | role/view |  | id:int | A policy with its limitations |
| `exppolicy::listOfRole` | role/view |  | id:int, limit:int, offset:int | The policies of a role, paged |
| `exppolicy::limitations` | role/view |  | id:int | The limitations of a policy, identifier => values |
| `exppolicy::summary` | role/view |  | id:int | A policy as one line of text |
| `exppolicy::add` | role/edit | POST | role:int, module:string, function:string, limitations:json | Add a policy to a role, validated against the module (POST role, module, function, limitations) |
| `exppolicy::remove` | role/edit | POST | id:int | Remove a policy (POST id) |
| `exppolicy::setLimitations` | role/edit | POST | id:int, limitations:json | Replace all limitations of a policy (POST id, limitations) |
| `exppolicy::addLimitation` | role/edit | POST | id:int, identifier:string, values:list | Add one limitation (POST id, identifier, values) |
| `exppolicy::removeLimitation` | role/edit | POST | id:int, identifier:string | Remove one limitation (POST id, identifier) |
| `exppolicy::copy` | role/edit | POST | id:int, role:int | Copy a policy into another role (POST id, role) |
| `exppolicy::modules` | role/view |  |  | The modules a policy can name |
| `exppolicy::functions` | role/view |  | module:string | The functions of a module |
| `exppolicy::availableLimitations` | role/view |  | module:string, function:string | The limitation identifiers of a module function |
| `exppolicy::limitationValues` | role/view |  | module:string, function:string, identifier:string, limit:int, offset:int | The values a limitation can take, paged |
| `exppolicy::ofModule` | role/view |  | module:string, limit:int, offset:int | The policies naming a module, over all roles |
| `exppolicy::findByLimitation` | role/view |  | identifier:string, value:string, limit:int, offset:int | The policies with a limitation identifier and value |

#### `expsessionadmin` (16 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `expsessionadmin::own` | public |  |  | The current session: user, started, cookie, short identifier |
| `expsessionadmin::ownList` | user |  | limit:int, offset:int | The active sessions of the current user (other devices) |
| `expsessionadmin::ownCount` | user |  |  | The number of active sessions of the current user |
| `expsessionadmin::ownRemove` | user | POST | id:string | End one of your own sessions (POST id) |
| `expsessionadmin::ownRemoveOthers` | user | POST |  | End all your sessions except this one (POST) |
| `expsessionadmin::regenerate` | user | POST |  | Issue a new session key for this session (POST) |
| `expsessionadmin::listAll` | setup/administrate |  | limit:int, offset:int, registered:bool | The active sessions of the installation, paged |
| `expsessionadmin::byUser` | setup/administrate |  | id:int, limit:int, offset:int | The sessions of a user |
| `expsessionadmin::count` | setup/administrate |  |  | Active, expired and total sessions |
| `expsessionadmin::stats` | setup/administrate |  |  | Registered and anonymous sessions and expired ones |
| `expsessionadmin::expiredCount` | setup/administrate |  |  | The number of expired sessions |
| `expsessionadmin::removeExpired` | setup/administrate | POST |  | Delete expired sessions (POST) |
| `expsessionadmin::remove` | setup/administrate | POST | id:string | End a session by its identifier (POST id) |
| `expsessionadmin::removeByUser` | setup/administrate | POST | id:int | End all sessions of a user but your own (POST id) |
| `expsessionadmin::settings` | setup/administrate |  |  | Session handler and timeouts |
| `expsessionadmin::handler` | setup/administrate |  |  | The session handler class and PHP save handler |

#### `exppreferences` (13 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `exppreferences::get` | user |  | name:string | One preference of the current user |
| `exppreferences::getMany` | user |  | names:list | Several preferences of the current user (comma separated names) |
| `exppreferences::listAll` | user |  | limit:int, offset:int | All preferences of the current user, paged |
| `exppreferences::byPrefix` | user |  | prefix:string, limit:int, offset:int | The preferences whose name starts with a prefix |
| `exppreferences::exists` | user |  | name:string | Whether a preference is set |
| `exppreferences::count` | user |  |  | The number of preferences of the current user |
| `exppreferences::set` | user | POST | name:string, value:string | Set a preference (POST name, value) |
| `exppreferences::setMany` | user | POST | values:json | Set up to 100 preferences (POST values JSON) |
| `exppreferences::remove` | user | POST | name:string | Remove a preference (POST name) |
| `exppreferences::increment` | user | POST | name:string, by:int | Add to a numeric preference (POST name, by) |
| `exppreferences::listOf` | setup/administrate |  | id:int, limit:int, offset:int | The preferences of another user |
| `exppreferences::getOf` | setup/administrate |  | id:int, name:string | One preference of another user |
| `exppreferences::setOf` | setup/administrate | POST | user:int, name:string, value:string | Set a preference of another user (POST user, name, value) |

#### `expnotification` (21 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `expnotification::settings` | notification/use |  |  | Digest settings, subscription count and collaboration types of the current user |
| `expnotification::digestTypes` | notification/use |  |  | The digest types |
| `expnotification::setDigest` | notification/use | POST | receive_digest:bool, digest_type:int, day:string, time:string | Set the digest settings (POST receive_digest, digest_type, day, time) |
| `expnotification::subscriptions` | notification/use |  | limit:int, offset:int | The nodes the current user is subscribed to, paged |
| `expnotification::subscriptionCount` | notification/use |  |  | The number of subscriptions |
| `expnotification::isSubscribed` | notification/use |  | node:int | Whether the current user is subscribed to a node |
| `expnotification::subscribe` | notification/use | POST | node:int, use_digest:bool | Subscribe to a node and its subtree (POST node, use_digest) |
| `expnotification::unsubscribe` | notification/use | POST | node:int | Unsubscribe from a node (POST node) |
| `expnotification::unsubscribeAll` | notification/use | POST |  | Remove all subscriptions of the current user (POST) |
| `expnotification::setDigestForSubscription` | notification/use | POST | node:int, use_digest:bool | Turn the digest on or off for one subscription (POST node, use_digest) |
| `expnotification::collaborationTypes` | notification/use |  |  | The collaboration notification types the current user receives |
| `expnotification::subscribeCollaboration` | notification/use | POST | type:string | Receive notifications of a collaboration type (POST type) |
| `expnotification::unsubscribeCollaboration` | notification/use | POST | type:string | Stop receiving a collaboration type (POST type) |
| `expnotification::handlers` | notification/administrate |  |  | The notification handlers |
| `expnotification::eventTypes` | notification/administrate |  |  | The available notification event types |
| `expnotification::subscriptionsOf` | notification/administrate |  | id:int, limit:int, offset:int | The subscriptions of a user |
| `expnotification::subscribeUser` | notification/administrate | POST | user:int, node:int, use_digest:bool | Subscribe a user to a node (POST user, node, use_digest) |
| `expnotification::unsubscribeUser` | notification/administrate | POST | user:int, node:int | Unsubscribe a user from a node (POST user, node) |
| `expnotification::subscribers` | notification/administrate |  | node:int, limit:int, offset:int | The users subscribed to a node |
| `expnotification::events` | notification/administrate |  | limit:int, offset:int | The notification events, newest first, paged |
| `expnotification::queue` | notification/administrate |  |  | The size of the notification queue |

#### `expcollaboration` (22 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `expcollaboration::items` | user |  | limit:int, offset:int, status:string, is_read:bool, group:int | The collaboration items of the current user, paged (status active/inactive/archive, is_read, group) |
| `expcollaboration::itemCount` | user |  | status:string, is_read:bool, group:int | The number of items, same filters |
| `expcollaboration::summary` | user |  |  | Active, unread, archived and pending approval counts |
| `expcollaboration::fetch` | user |  | id:int | One item, for participants |
| `expcollaboration::messages` | user |  | id:int, limit:int, offset:int | The messages of an item, paged |
| `expcollaboration::messageCount` | user |  | id:int | The message and unread message count of an item |
| `expcollaboration::addMessage` | user | POST | item:int, text:string | Add a message to an item (POST item, text) |
| `expcollaboration::participants` | user |  | id:int | The participants of an item with type and role |
| `expcollaboration::markRead` | user | POST | item:int | Mark an item read (POST item) |
| `expcollaboration::setActive` | user | POST | item:int, active:bool | Show or hide an item in the current user's list (POST item, active) |
| `expcollaboration::handlers` | user |  |  | The active collaboration handlers |
| `expcollaboration::groups` | user |  | limit:int, offset:int | The collaboration groups of the current user |
| `expcollaboration::groupInfo` | user |  | id:int | One collaboration group |
| `expcollaboration::groupItems` | user |  | id:int, limit:int, offset:int | The items of a group, paged |
| `expcollaboration::groupCreate` | user | POST | title:string, parent:int | Create a group (POST title, parent) |
| `expcollaboration::groupRename` | user | POST | group:int, title:string | Rename a group (POST group, title) |
| `expcollaboration::groupRemove` | user | POST | group:int | Remove an empty group (POST group) |
| `expcollaboration::moveToGroup` | user | POST | item:int, group:int | Put an item in a group (POST item, group) |
| `expcollaboration::pendingApprovals` | user |  | limit:int, offset:int | The approvals waiting for the current user |
| `expcollaboration::approvalStatus` | user |  | id:int | The state of an approval item and whether the current user may decide |
| `expcollaboration::approve` | user | POST | item:int, comment:string | Approve a waiting item as an approver (POST item, comment) |
| `expcollaboration::deny` | user | POST | item:int, comment:string | Deny a waiting item as an approver (POST item, comment) |

#### `expaccount` (19 services)

| Service | Access | Write | Arguments | Summary |
|---|---|---|---|---|
| `expaccount::registrationOptions` | public |  |  | Whether registration is open and how it is verified |
| `expaccount::register` | user/register | POST | login:string, email:string, password:string, fields:json | Register a new account (POST login, email, password, fields) |
| `expaccount::activationKeyValid` | public |  | key:string | Whether an activation key is valid |
| `expaccount::activate` | user/login | POST | key:string | Activate an account with the key from the activation mail (POST key) |
| `expaccount::forgotRequest` | user/login | POST | email:string | Mail a password reset link, the same answer for known and unknown addresses (POST email) |
| `expaccount::forgotKeyValid` | public |  | key:string | Whether a reset key is valid |
| `expaccount::reset` | user/login | POST | key:string, password:string | Set a new password with a reset key (POST key, password) |
| `expaccount::changePassword` | user | POST | old_password:string, new_password:string, confirm_password:string | Change your password with the old one (POST old_password, new_password, confirm_password) |
| `expaccount::changeEmail` | user | POST | password:string, email:string | Change your e-mail address, confirmed with the password (POST password, email) |
| `expaccount::me` | user |  |  | Your account: profile data, groups, role count |
| `expaccount::myRoles` | user |  |  | Your roles |
| `expaccount::myGroups` | user |  |  | Your groups |
| `expaccount::myLoginInfo` | user |  |  | Your login count, last visit and failed attempts |
| `expaccount::updateMyProfile` | user | POST | fields:json | Update your own profile attributes (POST fields) |
| `expaccount::unactivated` | role/list |  | limit:int, offset:int | The accounts waiting for activation, paged |
| `expaccount::unactivatedCount` | role/list |  |  | The number of accounts waiting for activation |
| `expaccount::activateUser` | user | POST | id:int | Activate an account by hand (POST id) |
| `expaccount::pendingResets` | role/list |  |  | The number of open password reset requests |
| `expaccount::cancelReset` | user | POST | id:int | Cancel the open reset requests of a user (POST id) |

Total: 195 services.

## Commerce, community and feeds (16 domains, 255 services)

Classes in `extension/expservices/classes/commerce/` and `classes/community/`, tests in
`tests/tests/extension/expservices/commerce/` and `community/` (151 tests, run against the live database like the other
domains: test products, forums, polls, forms and exports are created in a test folder under the Media root (node 43) or as
named test rows and removed in `tearDown`; baskets exist only for a test session key; the checkout tests create the
temporary order of a test basket and cancel it, no order is completed and no payment is created; there is never a test
database). Run: `php vendor/bin/phpunit tests/tests/extension/expservices/commerce/ tests/tests/extension/expservices/community/`.

Rules that hold for every service of these domains:

- **The shop works on the kernel.** Products are content objects whose class has a price attribute (`ezprice`,
  `ezmultiprice`; `expproduct::classes`). Prices come with and without VAT, with the discount of the product and, when a
  currency is asked for (`USD`, `EUR` ...), converted with the rates of the currency table (`in_currency`; `null` when a
  rate is missing). Basket, wish list, orders, VAT, currency, discount and payment services call `eZBasket`,
  `eZOrder`, `eZVatType`/`eZVatRule`, `eZCurrencyData`, `eZDiscountRule`, `eZWishList`, `eZPaymentObject` and the
  `shop` operations (`addtobasket`, `confirmorder`, `checkout`), so workflows, triggers and the kernel's own audit
  events (`commerce.order.status`, `.archive`, `.unarchive`, `.delete`, `commerce.vat.change`, `.currency.change`,
  `.discount.change`, `.payment.approve`, `.basket.checkout`) behave exactly as in the shop views.
- **The basket belongs to the session.** A remote app keeps its session cookie (`expsession::login`); without one,
  reads answer an empty basket and writes answer 401. Checkout is in steps: `checkoutStatus` (what blocks), `startCheckout`
  (the temporary order, nothing charged), `review` (shipping and VAT as the confirm step), `cancelCheckout`, and, only when
  the client means it, `placeOrder`, which runs the `checkout` operation: it activates the order or hands over to the
  payment gateway (`redirect_url`). Nothing else completes an order.
- **Orders**: a customer reads their own (`exporder::mine`, `myOrder`, `myReceipt`); the shop administrators
  (`shop/administrate`) list, view, search, archive, unarchive and delete any order, change the status within their
  `shop/setstatus` policy (`FromStatus`/`ToStatus` limitations are applied by `statusOptions` and `setStatus`) and read
  statistics, customers and the dashboard. Account data is returned by `exporder::account` for administrators only; card
  or account numbers are never stored by the kernel and never returned.
- **Money values** are numbers rounded to four decimals, VAT percentages numbers, currencies three-letter codes, times
  ISO 8601 in UTC.
- **Forums, topics, replies, comments, reviews and polls are content services** over the classes configured in
  `settings/expservices.ini` `[Community]` (`ForumClass=forum`, `TopicClass=forum_topic`, `ReplyClass=forum_reply`,
  `CommentClass=comment`, `PollClass=poll`, `ReviewClass=review`, `TopicStickyAttribute=sticky`), so a site with other
  class identifiers changes the INI, not the code. Create, edit and remove follow the content policies of the caller
  (the owner limitation lets members edit and remove their own posts); the text fields of a class are written as POST
  fields of the attribute's name or as one `fields` JSON object (rich text attributes take plain text and store
  paragraphs); unknown fields are refused (422), required ones checked. Moderation is `hide`/`unhide` (policy
  `content/hide`), `hidden` lists the hidden comments, a sticky topic stays on top, a topic can be moved to another
  forum. Removal goes to the trash.
- **Collected forms and polls** use the information collection of the kernel. `expinfocollection::submit` does what
  `content/collectinformation` does (validation by the datatype, anonymous collection and unique/overwrite/multiple
  handling from `collect.ini`) for text, number, boolean and choice fields; reading, summary, CSV export and removal need the
  `infocollector/read` policy (the CSV neutralises formulas). A poll vote is one collection: `exppoll::vote` answers the
  results; the poll class is `unique` per user in the standard `collect.ini`.
- **Feeds**: `expfeed::exports` and `output` serve the RSS exports of the site as the reader gets them (RSS 1.0, 2.0,
  Atom, OPML, iTunes; `data.content_type` and `data.content`), `rss`, `atom` and `json` build RSS 2.0, Atom 1.0 and JSON
  Feed 1.1 of the newest content below any node the caller may read (`classes` limits the classes, `limit` is capped at
  100), `discover` lists the feed addresses for a node. Export management (`rss/edit`) creates active exports directly
  (the draft/publish copy of the admin editor is not used); RSS imports are only read (`imports`, `importStatus`,
  `importCheck`, `importSetActive`): the import itself is the `rssimport` cronjob, and `importCheck` only fetches a source
  when asked and only for `http`/`https` addresses.
- Writes are POST with the form token (`expsession::token`) and recorded as `service.<domain>.<method>`; where the kernel
  records its own event for the change, it is recorded as well.
- Known kernel fault, worked around: `eZVatRule::removeVatRule()` calls an instance method statically, which PHP 8
  refuses; `expvat::removeRule` and `removeType` remove the rules themselves (with the same audit event).

#### Shop

##### Products (`expproduct`, 19 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `classes` | public |  | The product classes: content classes with a price attribute |
| `list` | public | `parent_node_id:int`, `limit:int`, `offset:int`, `currency:string`, `sort:string name/published/price` | The products below a node, newest or by name, with prices (optionally in a currency) |
| `count` | public | `parent_node_id:int` | How many products are below a node |
| `search` | public | `text:string`, `limit:int`, `offset:int`, `currency:string` | Products by words in name, number or description |
| `view` | public | `node_id:int`, `currency:string` | One product by node id with its fields, prices, options |
| `viewByObject` | public | `object_id:int`, `currency:string` | One product by object id |
| `isProduct` | public | `node_id:int` | Whether a node is a product |
| `price` | public | `node_id:int`, `currency:string` | The price of a product, optionally converted into a currency |
| `prices` | public | `node_id:int` | The price of a product in every active currency with a rate |
| `options` | public | `node_id:int` | The options (ezoption/ezmultioption) a buyer can choose, with additional prices |
| `variations` | public | `node_id:int`, `limit:int` | Every combination of the option groups with its resulting price |
| `optionPrice` | public | `node_id:int`, `choices:json` | The price of a product with chosen options (option attribute id => choice id as JSON) |
| `latest` | public | `limit:int`, `currency:string` | The most recently published products |
| `categories` | public | `parent_node_id:int` | The product categories: nodes below a node that hold products, with their product count |
| `priceRange` | public | `parent_node_id:int` | The lowest, highest and average price below a node |
| `byNumber` | public | `number:string`, `currency:string` | A product by its product number |
| `related` | public | `node_id:int`, `limit:int` | Other products of the same parent |
| `vat` | public | `node_id:int` | The VAT of a product: the type selected and the percentage |
| `setPrice` (POST) | `content/edit` | `node_id:int`, `price:string POST` | Sets the price of a product (publishes a new version) |

##### Basket and checkout (`expbasket`, 25 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `view` | public |  | The basket of this session: lines, options, totals per VAT rate, currency |
| `items` | public |  | The lines of the basket |
| `item` | public | `item_id:int` | One line of the basket |
| `count` | public |  | Number of lines and units in the basket |
| `totals` | public |  | Totals with and without VAT, VAT per rate, shipping |
| `isEmpty` | public |  | Whether the basket has no lines |
| `currency` | public |  | The currency of the basket and the preferred one of the user |
| `canAdd` | public | `object_id:int` | Whether a product can be put in this basket (type and currency compatible) |
| `add` (POST) | `shop/buy` | `object_id:int POST`, `quantity:int POST`, `options:json POST` | Puts a product in the basket (POST fields object_id, quantity, options as JSON attribute id => choice id) |
| `addNode` (POST) | `shop/buy` | `node_id:int POST`, `quantity:int POST`, `options:json POST` | Puts a product in the basket by its node id |
| `update` (POST) | `shop/buy` | `item_id:int POST`, `quantity:int POST` | Sets the quantity of one line (0 removes it) |
| `updateMany` (POST) | `shop/buy` | `quantities:json POST` | Sets the quantities of several lines: JSON object item id => quantity |
| `remove` (POST) | `shop/buy` | `item_id:int POST` | Removes one line |
| `empty` (POST) | `shop/buy` |  | Removes every line |
| `refreshPrices` (POST) | `shop/buy` |  | Brings the prices of the lines up to date with the products |
| `setCurrency` (POST) | user | `currency:string POST` | Sets the preferred currency of the user for new lines |
| `checkoutStatus` | public |  | What is needed before the checkout: lines, login, country, account handler, VAT known |
| `accountInfo` | `shop/buy` |  | The account information of the current checkout (the temporary order) |
| `startCheckout` (POST) | `shop/buy` |  | Creates the temporary order of the basket (step 1); nothing is charged |
| `review` | `shop/buy` |  | The temporary order with shipping and VAT as the confirm step shows it |
| `cancelCheckout` (POST) | `shop/buy` |  | Cancels the temporary order and returns to the basket |
| `placeOrder` (POST) | `shop/buy` |  | Runs the checkout operation of the temporary order: activates it or hands over to the payment gateway |
| `adminList` | `shop/administrate` | `limit:int`, `offset:int` | Baskets of all sessions with their size and age |
| `adminView` | `shop/administrate` | `basket_id:int` | One basket of any session |
| `adminCleanup` (POST) | `shop/administrate` | `days:int POST` | Removes the baskets of sessions that are gone and older than the given days |

##### Orders (`exporder`, 24 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `mine` | user | `limit:int`, `offset:int` | The orders of the logged-in customer, newest first |
| `myOrder` | user | `order_id:int` | One order of the logged-in customer with lines, extra order items and status history |
| `myCount` | user |  | How many orders the logged-in customer has |
| `myReceipt` | user | `order_id:int` | The receipt link and totals of one of the customer's orders |
| `statuses` | `shop/administrate` | `all:bool` | The order statuses |
| `list` | `shop/administrate` | `limit:int`, `offset:int`, `show:string normal/archived/all`, `sort:string created/id/total/user_name`, `order:string asc/desc`, `status_id:int` | Orders of all customers, filterable by status, archived and sorted |
| `count` | `shop/administrate` | `show:string normal/archived/all` | Number of orders |
| `view` | `shop/administrate` | `order_id:int` | One order by id with lines, order items and status history |
| `byNumber` | `shop/administrate` | `order_nr:int` | One order by its order number |
| `items` | `shop/administrate` | `order_id:int` | The product lines of an order |
| `orderItems` | `shop/administrate` | `order_id:int` | The extra items of an order: shipping, discounts |
| `history` | `shop/administrate` | `order_id:int` | The status history of an order |
| `account` | `shop/administrate` | `order_id:int` | The customer account information of an order |
| `statusOptions` | `shop/setstatus` | `order_id:int` | The statuses the current user may set an order to (policy FromStatus/ToStatus) |
| `setStatus` (POST) | `shop/setstatus` | `order_id:int POST`, `status_id:int POST` | Changes the status of an order |
| `archive` (POST) | `shop/administrate` | `order_id:int POST` | Moves an order to the archive |
| `unarchive` (POST) | `shop/administrate` | `order_id:int POST` | Brings an archived order back |
| `remove` (POST) | `shop/administrate` | `order_id:int POST` | Deletes an order with its lines and history (audited) |
| `search` | `shop/administrate` | `text:string`, `limit:int`, `offset:int` | Orders by customer email or account name |
| `statistics` | `shop/administrate` | `year:int`, `month:int` | Products sold in a year or month with totals |
| `customers` | `shop/administrate` | `limit:int`, `offset:int` | The customers (distinct emails) with their order count and sum |
| `customerOrders` | `shop/administrate` | `user_id:int`, `email:string` | The orders of one customer by user id |
| `customerProducts` | `shop/administrate` | `user_id:int`, `email:string` | The products one customer bought |
| `dashboard` | `shop/administrate` |  | Order counts per status and the latest orders |

##### VAT (`expvat`, 19 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `types` | public | `skip_dynamic:bool` | The VAT types with their percentage |
| `type` | public | `id:int` | One VAT type |
| `typeUsage` | `shop/administrate` | `id:int` | How many products, product classes and rules use a VAT type |
| `createType` (POST) | `shop/administrate` | `name:string POST`, `percentage:string POST` | Creates a VAT type |
| `updateType` (POST) | `shop/administrate` | `id:int POST`, `name:string POST`, `percentage:string POST` | Changes the name or percentage of a VAT type |
| `removeType` (POST) | `shop/administrate` | `id:int POST` | Removes a VAT type, its rules; products fall back to the default of their class |
| `rules` | `shop/administrate` | `limit:int`, `offset:int` | The VAT charging rules |
| `rule` | `shop/administrate` | `id:int` | One VAT charging rule |
| `createRule` (POST) | `shop/administrate` | `country_code:string POST`, `vat_type:int POST`, `categories:list POST` | Creates a rule: country code (or Any), VAT type id, product category ids |
| `updateRule` (POST) | `shop/administrate` | `id:int POST`, `country_code:string POST`, `vat_type:int POST`, `categories:list POST` | Changes a rule |
| `removeRule` (POST) | `shop/administrate` | `id:int POST` | Removes a rule |
| `categories` | `shop/administrate` |  | The product categories the rules refer to, with their product count |
| `createCategory` (POST) | `shop/administrate` | `name:string POST` | Creates a product category |
| `removeCategory` (POST) | `shop/administrate` | `id:int POST` | Removes a product category and its references |
| `forProduct` | public | `node_id:int`, `country:string` | The VAT percent of a product for a country (dynamic VAT handler), or the product's own |
| `settings` | public |  | The VAT settings of the shop: dynamic charging, handler, country requirement |
| `userCountry` | public |  | The country used for the VAT of the current user |
| `setUserCountry` (POST) | public | `country:string POST` | Sets the preferred country of the current session for the VAT |
| `countries` | public |  | The country codes with VAT rules and the countries of the installation |

##### Currencies (`expcurrency`, 19 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `list` | public | `only_active:bool` | The currencies with symbol, status and rates |
| `view` | public | `code:string` | One currency by its code |
| `count` | public |  | How many currencies exist |
| `codes` | public |  | The codes of the active currencies |
| `exists` | public | `code:string` | Whether a currency code exists |
| `baseCurrency` | public |  | The base currency of the exchange rates and the shop's default |
| `rate` | public | `code:string` | The rate of a currency against the base |
| `crossRate` | public | `from:string`, `to:string` | The rate between two currencies |
| `convert` | public | `amount:string`, `from:string`, `to:string` | Converts an amount from one currency to another with the rates and rounding of the shop |
| `rounding` | public |  | The rounding settings of conversions |
| `preferred` | public |  | The preferred currency of the current user |
| `setPreferred` (POST) | user | `code:string POST` | Sets the preferred currency of the current user |
| `create` (POST) | `shop/setup` | `code:string POST`, `symbol:string POST`, `locale:string POST`, `custom_rate:string POST`, `rate_factor:string POST`, `status:string POST active/inactive` | Creates a currency |
| `update` (POST) | `shop/setup` | `code:string POST`, `symbol:string POST`, `locale:string POST`, `custom_rate:string POST`, `rate_factor:string POST`, `status:string POST` | Changes symbol, locale, rates, factor or status of a currency |
| `setStatus` (POST) | `shop/setup` | `code:string POST`, `status:string POST active/inactive` | Activates or deactivates a currency |
| `remove` (POST) | `shop/setup` | `code:string POST` | Removes a currency |
| `updateRates` (POST) | `shop/setup` |  | Fetches the automatic rates from the configured provider and stores them |
| `updateAutoprices` (POST) | `shop/setup` |  | Recalculates the automatic prices of multi price products from the rates |
| `providers` | `shop/setup` |  | The exchange rate settings: provider, server, base |

##### Discounts (`expdiscount`, 16 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `groups` | `shop/administrate` | `limit:int`, `offset:int` | The discount groups with their rule and member counts |
| `group` | `shop/administrate` | `id:int` | One discount group with its rules and members |
| `createGroup` (POST) | `shop/administrate` | `name:string POST` | Creates a discount group |
| `renameGroup` (POST) | `shop/administrate` | `id:int POST`, `name:string POST` | Renames a discount group |
| `removeGroup` (POST) | `shop/administrate` | `id:int POST` | Removes a discount group with its rules and memberships |
| `rules` | `shop/administrate` | `group_id:int` | The rules of a discount group |
| `rule` | `shop/administrate` | `id:int` | One rule with its limitations |
| `createRule` (POST) | `shop/administrate` | `group_id:int POST`, `name:string POST`, `percent:string POST`, `classes:list POST`, `sections:list POST`, `products:list POST` | Creates a rule in a group: percent and optional class/section/product limits |
| `updateRule` (POST) | `shop/administrate` | `id:int POST`, `name:string POST`, `percent:string POST`, `classes:list POST`, `sections:list POST`, `products:list POST` | Changes name, percent or limits of a rule |
| `removeRule` (POST) | `shop/administrate` | `id:int POST` | Removes a rule |
| `members` | `shop/administrate` | `group_id:int` | The users and user groups of a discount group |
| `addMember` (POST) | `shop/administrate` | `group_id:int POST`, `object_id:int POST` | Adds a user or user group (object id) to a discount group |
| `removeMember` (POST) | `shop/administrate` | `group_id:int POST`, `object_id:int POST` | Removes a user or user group from a discount group |
| `groupsOfUser` | `shop/administrate` | `object_id:int` | The discount groups a user or user group belongs to |
| `forUser` | `shop/administrate` | `user_id:int`, `node_id:int` | The best discount percent of a user for a product (class, section, object) |
| `mine` | user | `node_id:int` | The discount percent the logged-in user gets for a product |

##### Wish lists (`expwishlist`, 11 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `view` | user |  | The wish list of the logged-in user with its lines |
| `items` | user | `limit:int`, `offset:int` | The lines of the wish list |
| `count` | user |  | How many lines the wish list has |
| `contains` | user | `object_id:int` | Whether a product is on the wish list |
| `add` (POST) | user | `object_id:int POST`, `options:json POST` | Puts a product on the wish list (options as JSON attribute id => choice id) |
| `remove` (POST) | user | `item_id:int POST` | Removes one line |
| `empty` (POST) | user |  | Removes every line |
| `moveToBasket` (POST) | `shop/buy` | `item_id:int POST`, `quantity:int POST` | Puts a wish list line in the basket and removes it from the list |
| `adminList` | `shop/administrate` | `limit:int`, `offset:int` | The wish lists of all users with their size |
| `adminView` | `shop/administrate` | `user_id:int` | The wish list of one user by object id |
| `popular` | `shop/administrate` | `limit:int` | The products on most wish lists |

##### Shipping (`expshipping`, 8 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `status` | public |  | Whether shipping is configured: handler, simple shipping workflow |
| `handler` | `shop/administrate` |  | The shipping handler of shop.ini and where it is searched |
| `simpleShipping` | public |  | The cost and description of the simple shipping workflow |
| `basket` | public |  | The shipping info of the current basket |
| `order` | `shop/administrate` | `order_id:int` | The shipping line of an order (order items of shipping type) |
| `basketInfoHandler` | public |  | The basket info handler that calculates totals and shipping |
| `vatOfShipping` | public |  | The VAT split of a shipping cost (the VAT of the products it is spread over) |
| `settings` | `shop/administrate` |  | The ShippingSettings and BasketInfoSettings of shop.ini |

##### Payments (`exppayment`, 10 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `list` | `shop/administrate` | `limit:int`, `offset:int`, `status:string all/pending/approved` | The payment objects, newest first, optionally only pending or approved |
| `view` | `shop/administrate` | `id:int` | One payment object by id |
| `byOrder` | `shop/administrate` | `order_id:int` | The payment of an order |
| `myStatus` | user | `order_id:int` | The payment status of one of the logged-in customer's orders |
| `counts` | `shop/administrate` |  | How many payments are pending and approved |
| `gateways` | `shop/administrate` |  | The payment gateways available and the directories searched |
| `workflowEvents` | `shop/administrate` |  | The workflow events of the payment gateway type, with their gateway |
| `handlers` | public |  | The account handler and confirm order handler of the checkout |
| `methods` | public |  | What a client can offer as payment: enabled gateways, or the manual method |
| `approve` (POST) | `shop/administrate` | `id:int POST` | Approves a payment and continues its workflow (audited as commerce.payment.approve) |

#### Forms, polls and community

##### Collected forms (`expinfocollection`, 12 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `forms` | `infocollector/read` | `limit:int`, `offset:int` | The content objects that have collected data, with the number of collections |
| `collections` | `infocollector/read` | `object_id:int`, `limit:int`, `offset:int` | The collections of one form, newest first |
| `view` | `infocollector/read` | `collection_id:int` | One collection with its values |
| `count` | `infocollector/read` | `object_id:int` | How many collections a form (or all forms) has |
| `fields` | public | `node_id:int` | The fields of a form: the attributes that collect information, with type and requirement |
| `summary` | `infocollector/read` | `object_id:int` | Per field of a form: how many values and, for choice and number fields, the distribution |
| `mine` | user | `limit:int`, `offset:int` | The collections the logged-in user submitted |
| `export` | `infocollector/read` | `object_id:int` | All collections of a form as CSV text (audited as a data export by the kernel when enabled) |
| `submit` (POST) | public | `object_id:int POST`, `fields:json POST` | Submits a form: POST object_id and fields as JSON identifier => value (text, number, boolean, choice id) |
| `remove` (POST) | `infocollector/read` | `collection_id:int POST` | Removes one collection (audited as data.infocollection.remove) |
| `removeAll` (POST) | `infocollector/read` | `object_id:int POST` | Removes every collection of a form |
| `settings` | public | `node_id:int` | How collection is handled for a form: anonymous allowed, unique/overwrite/multiple, template type |

##### Polls (`exppoll`, 11 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `list` | public | `parent_node_id:int`, `limit:int`, `offset:int` | The polls below a node with their question and number of votes |
| `view` | public | `node_id:int` | One poll: question, choices, votes per choice |
| `results` | public | `node_id:int` | The results of a poll: votes and percent per choice |
| `choices` | public | `node_id:int` | The choices of a poll |
| `canVote` | public | `node_id:int` | Whether the current visitor may vote now, and why not |
| `myVote` | public | `node_id:int` | The choice the current visitor voted for |
| `vote` (POST) | public | `node_id:int POST`, `choice:int POST` | Votes for a choice (POST node_id, choice) |
| `latest` | public | `limit:int` | The newest polls |
| `create` (POST) | `content/create` | `parent_node_id:int POST`, `name:string POST`, `question:string POST`, `choices:list POST` | Creates a poll under a node: name, question and choices |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Removes a poll (to the trash) with its votes |
| `resetVotes` (POST) | `infocollector/read` | `node_id:int POST` | Removes every vote of a poll |

##### Forums (`expforum`, 13 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `containers` | public | `limit:int`, `offset:int` | The forum containers (the "forums" class) of the site |
| `list` | public | `parent_node_id:int`, `limit:int`, `offset:int` | The forums below a node (a container, or the content root: all forums) |
| `view` | public | `node_id:int` | One forum with its topic and reply counts and the latest topic |
| `stats` | public | `node_id:int` | Topics, replies and the latest activity of a forum |
| `latest` | public | `limit:int` | The latest topics over all forums |
| `search` | public | `text:string`, `limit:int`, `offset:int` | Words in forum names and descriptions |
| `path` | public | `node_id:int` | The path (container, forum) of a node for a breadcrumb |
| `fields` | public |  | The fields a forum has and the classes in use |
| `create` (POST) | `content/create` | `parent_node_id:int POST`, `fields:json POST` | Creates a forum in a container (fields as JSON or POST fields: name, description) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Changes the fields of a forum |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Removes a forum with its topics to the trash |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Hides a forum from visitors |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Shows a hidden forum again |

##### Topics (`exptopic`, 17 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `list` | public | `forum_node_id:int`, `limit:int`, `offset:int` | The topics of a forum, sticky ones first, then newest first |
| `view` | public | `node_id:int` | One topic with its text and reply count |
| `count` | public | `forum_node_id:int` | How many topics a forum has |
| `latest` | public | `limit:int`, `forum_node_id:int` | The latest topics, in one forum or everywhere |
| `sticky` | public | `forum_node_id:int` | The sticky topics of a forum |
| `byUser` | public | `user_id:int`, `limit:int`, `offset:int` | The topics a user started |
| `mine` | user | `limit:int`, `offset:int` | The topics the logged-in user started |
| `search` | public | `text:string`, `forum_node_id:int`, `limit:int`, `offset:int` | Words in topic subjects and texts |
| `lastReply` | public | `node_id:int` | The newest reply of a topic |
| `canCreate` | public | `forum_node_id:int` | Whether the current user may start a topic in a forum |
| `create` (POST) | `content/create` | `forum_node_id:int POST`, `fields:json POST` | Starts a topic in a forum (fields subject, message, sticky as JSON or POST fields) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Changes subject or message of a topic |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Removes a topic with its replies to the trash |
| `setSticky` (POST) | `content/edit` | `node_id:int POST`, `sticky:bool POST` | Makes a topic sticky or not (moderation) |
| `move` (POST) | `content/edit` | `node_id:int POST`, `forum_node_id:int POST` | Moves a topic to another forum |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Hides a topic with its replies from visitors (moderation) |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Shows a hidden topic again |

##### Replies (`expreply`, 13 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `list` | public | `topic_node_id:int`, `limit:int`, `offset:int`, `newest_first:bool` | The replies of a topic, oldest first |
| `view` | public | `node_id:int` | One reply |
| `count` | public | `topic_node_id:int` | How many replies a topic has |
| `latest` | public | `limit:int`, `parent_node_id:int` | The latest replies, everywhere or below a node |
| `byUser` | public | `user_id:int`, `limit:int`, `offset:int` | The replies a user wrote |
| `mine` | user | `limit:int`, `offset:int` | The replies the logged-in user wrote |
| `search` | public | `text:string`, `topic_node_id:int`, `limit:int`, `offset:int` | Words in reply subjects and texts |
| `quote` | public | `node_id:int` | A reply as a quote to start an answer from: "Re: subject" and the quoted text |
| `create` (POST) | `content/create` | `topic_node_id:int POST`, `fields:json POST` | Writes a reply to a topic (fields subject, message as JSON or POST fields) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Changes a reply |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Removes a reply to the trash |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Hides a reply (moderation) |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Shows a hidden reply again |

##### Comments and reviews (`expcomment`, 17 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `list` | public | `node_id:int`, `limit:int`, `offset:int`, `newest_first:bool` | The comments below a node, oldest first or newest first |
| `view` | public | `node_id:int` | One comment |
| `count` | public | `node_id:int` | How many comments a node has |
| `latest` | public | `limit:int`, `parent_node_id:int` | The latest comments, everywhere or below a node |
| `byUser` | public | `user_id:int`, `limit:int`, `offset:int` | The comments a user wrote |
| `mine` | user | `limit:int`, `offset:int` | The comments the logged-in user wrote |
| `search` | public | `text:string`, `parent_node_id:int`, `limit:int`, `offset:int` | Words in comment subjects, authors and texts |
| `hidden` | `content/hide` | `parent_node_id:int`, `limit:int`, `offset:int` | The hidden comments below a node (moderation queue) |
| `canComment` | public | `node_id:int` | Whether the current user may comment on a node |
| `create` (POST) | `content/create` | `node_id:int POST`, `fields:json POST` | Comments on a node (fields subject, author, message as JSON or POST fields) |
| `edit` (POST) | `content/edit` | `node_id:int POST`, `fields:json POST` | Changes a comment |
| `remove` (POST) | `content/remove` | `node_id:int POST` | Removes a comment to the trash |
| `hide` (POST) | `content/hide` | `node_id:int POST` | Hides a comment (moderation) |
| `unhide` (POST) | `content/hide` | `node_id:int POST` | Shows a hidden comment again |
| `reviews` | public | `node_id:int`, `limit:int`, `offset:int` | The reviews of a product with the average rating |
| `ratingSummary` | public | `node_id:int` | Average rating and the count per star of a product |
| `addReview` (POST) | `content/create` | `node_id:int POST`, `fields:json POST` | Reviews a product (fields title, rating 1-5, author, body) |

#### Feeds

##### Feeds (`expfeed`, 21 services)

| Service | Access | Arguments | What it does |
|---|---|---|---|
| `exports` | public | `limit:int`, `offset:int`, `only_active:bool` | The RSS exports with their address, format and number of items |
| `export` | public | `id:int` | One RSS export with its sources |
| `exportByUrl` | public | `access_url:string` | One RSS export by its access URL name |
| `output` | public | `access_url:string` | The feed document of an RSS export as the reader gets it (RSS 1.0, 2.0, Atom, OPML, iTunes) |
| `formats` | public |  | The export formats and their names |
| `rss` | public | `node_id:int`, `limit:int`, `classes:list` | An RSS 2.0 feed of the latest content below a node |
| `atom` | public | `node_id:int`, `limit:int`, `classes:list` | An Atom 1.0 feed of the latest content below a node |
| `json` | public | `node_id:int`, `limit:int`, `classes:list` | A JSON Feed 1.1 of the latest content below a node |
| `items` | public | `node_id:int`, `limit:int`, `offset:int`, `classes:list` | The feed items of a node as plain data (title, url, date, summary) |
| `discover` | public | `node_id:int` | The feed addresses a site offers for a node: the generic ones and the exports that cover it |
| `createExport` (POST) | `rss/edit` | `title:string POST`, `access_url:string POST`, `source_node_id:int POST`, `rss_version:string POST 1.0/2.0/ATOM`, `number_of_objects:int POST`, `description:string POST` | Creates an active RSS export of a subtree |
| `updateExport` (POST) | `rss/edit` | `id:int POST`, `title:string POST`, `description:string POST`, `number_of_objects:int POST`, `rss_version:string POST`, `main_node_only:bool POST` | Changes title, description, size or format of an export |
| `setActive` (POST) | `rss/edit` | `id:int POST`, `active:bool POST` | Switches an export on or off |
| `addSource` (POST) | `rss/edit` | `id:int POST`, `source_node_id:int POST`, `subnodes:bool POST` | Adds a source subtree to an export |
| `removeSource` (POST) | `rss/edit` | `id:int POST`, `source_id:int POST` | Removes a source from an export |
| `removeExport` (POST) | `rss/edit` | `id:int POST` | Removes an export with its sources |
| `imports` | `rss/edit` | `limit:int`, `offset:int` | The RSS imports with their source URL, destination and state |
| `import` | `rss/edit` | `id:int` | One RSS import |
| `importStatus` | `rss/edit` | `id:int` | What an import brought in: objects created by it, the newest one and when |
| `importCheck` | `rss/edit` | `id:int`, `fetch:bool` | Whether the source of an import is an address the server may fetch, and optionally its feed version (fetches the source) |
| `importSetActive` (POST) | `rss/edit` | `id:int POST`, `active:bool POST` | Switches an import on or off (the rssimport cronjob runs the active ones) |


Total: 255 services in 16 domains (83 writes).

<!-- expservices-content:begin -->
## Content services: 350 services in 15 domains (112 writes)

Written by the content part of expservices: `extension/expservices/classes/content/` (one class per domain over the shared
`expContentServiceBase`: fetch helpers with the access checks, the exporters that turn nodes, objects, versions, classes,
sections and states into plain arrays, attribute values per datatype, sort and filter parsing, and the now-or-job routing).
Tests: `tests/tests/extension/expservices/content/` (live database like the other domains: reads on the stable nodes, writes
on test content under the Media root node 43 and on test classes, class groups, sections, state groups, aliases and wildcards
that tearDown removes; never a test database). The sample calls were also verified over HTTP on Apache with a signed-in admin session (reads, a refused write without the token, then create, update and remove of a test folder).

How the content services work, beyond the rules above:

- **Policies with limitations.** The access column is the policy the call needs at all (`content/read`, `content/edit`, ...);
  inside, every call checks the node or object the way the kernel does (`canRead`, `canEdit`, `canRemove`, `canMoveFrom`,
  `canCreateClassList`, `canAssignSectionToObject`, the allowed state list ...), so a limitation by class, section, subtree,
  owner or state applies to the services as it does in the admin. Lists use the kernel fetch functions with their permission
  SQL, so a user only counts and pages what they may read.
- **Identifiers.** Nodes, objects, versions, sections and states are addressed by number; classes by id or identifier;
  state groups by id or identifier; languages by locale code (`eng-GB`).
- **Attribute input.** Writes take attribute values as a JSON object `identifier => string`, in the string form the datatype
  reads (`fromString()`: see `expattribute::string`). Datatypes whose string form is a file path or a credential
  (`ezimage`, `ezbinaryfile`, `ezmedia`, `ezuser`, `ezpassword`) are refused with 422: a remote caller must never make the server
  read a local path. Required attributes must be given on create. Values come back per datatype (text, number, boolean, date with
  ISO form, XML text as xml and html, relations as ids, images with their original URL).
- **Lists** answer the paged envelope (`data` items, `meta` total, offset, limit, count, has_more). Node lists take
  `sort` (path, published, modified, section, depth, class_identifier, class_name, priority, name, modified_subnode, node_id,
  contentobject_id), `order` (asc, desc), `limit`, `offset` and a JSON `filter`: `class[]`, `exclude_class[]`, `section`,
  `state[]`, `owner`, `name` (prefix), `language`, `from` and `to` (published), `depth`, `hidden`.
- **Now or job.** Large operations (move, copy of a subtree, hide, reveal, remove of subtrees, section and state of a subtree,
  add and remove locations) take the POST field `mode`: `now` runs in the request (refused above content.ini `NowLimit`
  nodes, answer 422), `job` creates a content job and starts its worker, `auto` (the default) picks by `SynchronousLimit`.
  A job answer is `{ job_id, type, state, spawned }` with `meta.mode = job`; follow it with `expcontentjob::progress::<job_id>`.
  A subtree locked by a running job answers 409 to a synchronous write.
- **Drafts of classes** are the temporary version of the class, the one the admin class editor works on: `editDraft` or
  `createDraft`, change, then `publishDraft` (the class edit handler updates existing objects) or `discardDraft`.
- **Removal.** `remove` takes `move_to_trash` (default 1); the root nodes cannot be removed; `exptrash::purge`,
  `purgeMany` and `emptyTrash` (POST `confirm=yes`) remove for good through the kernel trash service. Classes with objects, sections
  in use, state groups with the `ez` prefix and the last location of an object are refused with 409 or 403.
- **Audit.** Every successful write records `service.<domain>.<method>`; the kernel records its own `content.*` events for the
  operations the services call (create, publish, move, remove, section, state, translation, class, URL alias).

Errors: 400 bad argument, 401 not signed in, 403 denied (policy, method or form token), 404 not found, 409 conflict (exists,
locked, in use), 422 not valid (input refused by a datatype or a rule).

Example: create an article, change it, and follow a large move as a job (shell, after `expsession::login`):

```bash
curl -b jar -c jar --data-urlencode 'class=article' --data-urlencode 'attributes={"title":"Hello","intro":"First"}' \
     --data-urlencode "ezxform_token=$TOKEN" "$BASE/ezjscore/call/expnode::create::43?ContentType=json"
curl -b jar -c jar --data-urlencode 'attributes={"title":"Hello again"}' --data-urlencode "ezxform_token=$TOKEN" \
     "$BASE/ezjscore/call/expnode::update::<node>?ContentType=json"
curl -b jar -c jar --data-urlencode 'mode=job' --data-urlencode "ezxform_token=$TOKEN" \
     "$BASE/ezjscore/call/expnode::move::<node>::<new-parent>?ContentType=json"      # { job_id: ... }
curl -b jar "$BASE/ezjscore/call/expcontentjob::progress::<job_id>?ContentType=json"
```

#### Nodes (`expnode`, 49 services, 16 writes)

The content tree: fetch by id, remote id, path and object; children and subtrees paged, sorted and filtered; counts, ancestors, siblings, data maps, rights; create, update, move, copy, hide, swap, remove, sort and priority.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expnode::get::<node_id>` | content/read |  | A node with its object |
| `expnode::getMany::<node_ids>` | content/read |  | Several nodes by id (unreadable and missing ones are left out) |
| `expnode::getByRemoteId::<remote_id>` | content/read |  | A node by its remote id |
| `expnode::getByPath::<path>` | content/read |  | A node by its URL alias, e.g. Media/Images |
| `expnode::getByObject::<object_id>` | content/read |  | The main node of an object |
| `expnode::exists::<node_id>` | user |  | Whether a node exists and is readable |
| `expnode::children::<node_id>::<sort>::<order>::<limit>::<offset>::<filter>` | content/read |  | Children of a node, paged, sorted and filtered |
| `expnode::childrenCount::<node_id>::<filter>` | content/read |  | Number of children |
| `expnode::childrenNames::<node_id>::<limit>::<offset>` | content/read |  | Light list of children: node id, object id, class, name |
| `expnode::subtree::<node_id>::<sort>::<order>::<limit>::<offset>::<filter>` | content/read |  | Descendants of a node, paged, sorted and filtered (filter.depth limits the depth) |
| `expnode::subtreeCount::<node_id>::<filter>` | content/read |  | Number of descendants |
| `expnode::byClass::<node_id>::<classes>::<limit>::<offset>` | content/read |  | Descendants of one or more classes |
| `expnode::countByClass::<node_id>::<depth>` | content/read |  | Facet: number of descendants per class (needs unrestricted read) |
| `expnode::latest::<node_id>::<limit>::<classes>` | content/read |  | Most recently published descendants |
| `expnode::modifiedSince::<node_id>::<since>::<limit>::<offset>` | content/read |  | Descendants published since a time (timestamp or date) |
| `expnode::find::<node_id>::<name>::<limit>::<offset>` | content/read |  | Descendants whose name starts with a text |
| `expnode::hidden::<node_id>::<limit>::<offset>` | content/read |  | Hidden nodes below a node |
| `expnode::tree::<node_id>::<depth>::<per_level>` | content/read |  | Nested children down to a depth, limited per level |
| `expnode::path::<node_id>` | content/read |  | The ancestors of a node from the root, with the node itself |
| `expnode::breadcrumb::<node_id>` | content/read |  | Light ancestors list: node id, name, url alias |
| `expnode::parent::<node_id>` | content/read |  | The parent node |
| `expnode::siblings::<node_id>::<limit>::<offset>` | content/read |  | Other children of the parent, paged |
| `expnode::neighbours::<node_id>` | content/read |  | Previous and next sibling in the parent sort order |
| `expnode::dataMap::<node_id>::<language>` | content/read |  | The attributes of the node object with their values |
| `expnode::attribute::<node_id>::<identifier>::<language>` | content/read |  | One attribute of the node object |
| `expnode::names::<node_id>` | content/read |  | The node name in every language |
| `expnode::url::<node_id>` | content/read |  | URL alias and system URL of a node |
| `expnode::sortInfo::<node_id>` | content/read |  | Sort field and order of the children, and the names allowed |
| `expnode::visibility::<node_id>` | content/read |  | Hidden and invisible flags with the nearest hidden ancestor |
| `expnode::rights::<node_id>` | user |  | What the current user can do with the node |
| `expnode::creatableClasses::<node_id>` | content/create |  | Classes the current user can create below the node |
| `expnode::locations::<node_id>` | content/read |  | All nodes of the same object |
| `expnode::pendingDrafts::<node_id>::<limit>::<offset>` | content/read |  | Drafts waiting to be published below a node |
| `expnode::create::<parent_node_id>` | content/create | POST | Creates and publishes an object below a node. POST: class, attributes (json identifier => string), language, remote_id |
| `expnode::update::<node_id>` | content/edit | POST | Changes attributes of the node object and publishes a new version. POST: attributes (json), language |
| `expnode::rename::<node_id>` | content/edit | POST | Renames the object of a node. POST: name |
| `expnode::move::<node_id>::<new_parent_node_id>` | content/move | POST | Moves a node and its subtree. POST: mode |
| `expnode::copy::<node_id>::<new_parent_node_id>` | content/create | POST | Copies one node (object) below a node |
| `expnode::copySubtree::<node_id>::<new_parent_node_id>` | content/create | POST | Copies a node with all its descendants. POST: mode, all_versions, keep_creator, keep_time |
| `expnode::hide::<node_id>` | content/hide | POST | Hides a node and its subtree. POST: mode |
| `expnode::reveal::<node_id>` | content/hide | POST | Reveals a hidden node and its subtree. POST: mode |
| `expnode::toggleHide::<node_id>` | content/hide | POST | Hides a visible node, reveals a hidden one |
| `expnode::swap::<node_id>::<other_node_id>` | content/edit | POST | Swaps two nodes (their objects change places) |
| `expnode::remove::<node_id>` | content/remove | POST | Removes a node and its subtree. POST: move_to_trash (default 1), mode |
| `expnode::removeMany` | content/remove | POST | Removes several nodes. POST: node_ids, move_to_trash, mode |
| `expnode::setSort::<node_id>::<field>::<order>` | content/edit | POST | Sets how the children are sorted |
| `expnode::setPriority::<node_id>::<priority>` | content/edit | POST | Sets the priority of a node |
| `expnode::setPriorities::<parent_node_id>` | content/edit | POST | Sets the priorities of children. POST: priorities (json node id => priority) |
| `expnode::setRemoteId::<node_id>::<remote_id>` | content/edit | POST | Sets the remote id of a node |

#### Objects (`expobject`, 36 services, 11 writes)

Content objects: lists by class and owner, data maps and attributes with their values, names, owner, class, section, states, locations, languages; create, update (a new published version), rename, owner, languages, copy, remove.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expobject::get::<object_id>` | content/read |  | An object with its languages and node ids |
| `expobject::getByRemoteId::<remote_id>` | content/read |  | An object by its remote id |
| `expobject::exists::<object_id>` | user |  | Whether an object exists and is readable |
| `expobject::list::<sort>::<order>::<limit>::<offset>::<filter>` | content/read |  | Objects (main nodes) paged, sorted and filtered |
| `expobject::count::<filter>` | content/read |  | Number of objects matching a filter |
| `expobject::byClass::<class>::<limit>::<offset>` | content/read |  | Objects of a class |
| `expobject::byOwner::<owner_id>::<limit>::<offset>` | content/read |  | Objects owned by an object (user) id |
| `expobject::recent::<limit>::<classes>` | content/read |  | Most recently published objects |
| `expobject::recentlyModified::<limit>::<classes>` | content/read |  | Most recently modified objects |
| `expobject::dataMap::<object_id>::<version>::<language>` | content/read |  | The attributes of an object version with their values |
| `expobject::attributes::<object_id>::<language>` | content/read |  | The attributes of an object without values |
| `expobject::attribute::<object_id>::<identifier>::<language>` | content/read |  | One attribute of an object |
| `expobject::name::<object_id>::<language>` | content/read |  | The name of an object in a language |
| `expobject::names::<object_id>` | content/read |  | The name of an object in every language |
| `expobject::owner::<object_id>` | content/read |  | The owner object of an object |
| `expobject::class::<object_id>` | content/read |  | The class of an object with its attributes |
| `expobject::section::<object_id>` | content/read |  | The section of an object |
| `expobject::states::<object_id>` | content/read |  | The states of an object |
| `expobject::nodes::<object_id>` | content/read |  | All nodes (locations) of an object |
| `expobject::mainNode::<object_id>` | content/read |  | The main node of an object |
| `expobject::languages::<object_id>` | content/read |  | Languages the object exists in, initial and always available |
| `expobject::versionCount::<object_id>` | content/read |  | Number of versions |
| `expobject::rights::<object_id>` | user |  | What the current user can do with the object |
| `expobject::url::<object_id>` | content/read |  | URL aliases of the main node of an object |
| `expobject::summary::<object_id>` | content/read |  | Light card of an object: id, name, class, main node, modified |
| `expobject::create` | content/create | POST | Creates and publishes an object. POST: parent_node_id, class, attributes (json), language, remote_id |
| `expobject::update::<object_id>` | content/edit | POST | Changes attributes and publishes a new version. POST: attributes (json), language |
| `expobject::rename::<object_id>` | content/edit | POST | Renames an object. POST: name |
| `expobject::setRemoteId::<object_id>::<remote_id>` | content/edit | POST | Sets the remote id of an object |
| `expobject::setOwner::<object_id>::<owner_id>` | content/edit | POST | Changes the owner of an object (needs unrestricted edit access) |
| `expobject::setInitialLanguage::<object_id>::<language>` | content/translate | POST | Changes the initial language of an object |
| `expobject::setAlwaysAvailable::<object_id>::<value>` | content/translate | POST | Sets whether the object is shown in languages it has no translation for |
| `expobject::copy::<object_id>::<new_parent_node_id>` | content/create | POST | Copies the object of a main node below a node |
| `expobject::remove::<object_id>` | content/remove | POST | Removes an object with all its nodes. POST: move_to_trash (default 1), mode |
| `expobject::expireCache::<object_id>` | content/edit | POST | Clears the view caches of an object |
| `expobject::cleanupDrafts::<object_id>` | content/edit | POST | Removes the current user's internal drafts of an object |

#### Classes (`expclass`, 37 services, 12 writes)

Content class definitions and the class editor as services: reads of classes, attributes, groups, usage and datatypes; draft workflow (createDraft or editDraft, change the draft and its attributes, publishDraft or discardDraft), copy and remove.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expclass::list::<group_id>::<limit>::<offset>` | class/* |  | Content classes, optionally of one group |
| `expclass::count` | class/* |  | Number of classes |
| `expclass::identifiers` | user |  | Light map of class id => identifier and name |
| `expclass::get::<class>` | class/* |  | A class with its attributes and groups (id or identifier) |
| `expclass::getByRemoteId::<remote_id>` | class/* |  | A class by its remote id |
| `expclass::search::<text>::<limit>::<offset>` | class/* |  | Classes whose name or identifier contains a text |
| `expclass::attributes::<class>` | class/* |  | The attributes of a class in order |
| `expclass::attribute::<attribute_id>` | class/* |  | One class attribute by id |
| `expclass::attributeByIdentifier::<class>::<identifier>` | class/* |  | One class attribute by class and identifier |
| `expclass::searchableAttributes::<class>` | class/* |  | The searchable attributes of a class |
| `expclass::requiredAttributes::<class>` | class/* |  | The required attributes of a class |
| `expclass::collectorAttributes::<class>` | class/* |  | The information collector attributes of a class |
| `expclass::groups::<class>` | class/* |  | The groups a class is in |
| `expclass::objectCount::<class>` | class/* |  | Number of objects of a class |
| `expclass::usage::<limit>::<offset>` | class/* |  | Classes with their object counts, most used first |
| `expclass::containers` | class/* |  | Classes that are containers |
| `expclass::names::<class>` | class/* |  | The name of a class in every language |
| `expclass::languages::<class>` | class/* |  | Languages a class has names in |
| `expclass::patterns::<class>` | class/* |  | Object name and URL alias patterns of a class |
| `expclass::removable::<class>` | class/* |  | Whether the class can be removed and what blocks it |
| `expclass::canInstantiate` | user |  | Classes the current user may create objects of |
| `expclass::drafts` | class/* |  | Class drafts (temporary versions) being edited |
| `expclass::draft::<class_id>` | class/* |  | A class draft with its attributes |
| `expclass::dataTypes` | class/* |  | The datatypes available for class attributes |
| `expclass::sortFields` | user |  | The names accepted for the default sort of children |
| `expclass::createDraft` | class/* | POST | Starts a new class draft. POST: name, identifier, group_id, language, description, is_container, always_available, object_name_pattern, url_alias_pattern, sort_field, sort_order |
| `expclass::editDraft::<class_id>` | class/* | POST | Starts or continues a draft of an existing class |
| `expclass::updateDraft::<class_id>` | class/* | POST | Changes the class draft fields (same POST fields as createDraft) |
| `expclass::addAttributeDraft::<class_id>` | class/* | POST | Adds an attribute to the draft. POST: data_type, identifier, name, description, is_required, is_searchable, can_translate, is_information_collector, language |
| `expclass::updateAttributeDraft::<class_id>::<attribute_id>` | class/* | POST | Changes an attribute of the draft (same POST fields except data_type) |
| `expclass::removeAttributeDraft::<class_id>::<attribute_id>` | class/* | POST | Removes an attribute from the draft |
| `expclass::moveAttributeDraft::<class_id>::<attribute_id>::<direction>` | class/* | POST | Moves an attribute of the draft: up, down, top or bottom |
| `expclass::publishDraft::<class_id>` | class/* | POST | Publishes the draft as the class definition |
| `expclass::discardDraft::<class_id>` | class/* | POST | Discards the class draft |
| `expclass::copy::<class_id>` | class/* | POST | Starts a draft that is a copy of a class (publishDraft makes it a class) |
| `expclass::remove::<class_id>` | class/* | POST | Removes a class that has no objects |
| `expclass::setName::<class_id>::<language>::<name>` | class/* | POST | Sets the name of a class in a language (through a draft that is published) |

#### Class groups (`expclassgroup`, 13 services, 5 writes)

The groups classes are filed in: list, members, create, rename, remove (empty groups only), add a class to a group, take it out.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expclassgroup::list::<limit>::<offset>` | class/* |  | All class groups with their class counts |
| `expclassgroup::count` | class/* |  | Number of class groups |
| `expclassgroup::get::<group_id>` | class/* |  | A class group |
| `expclassgroup::getByName::<name>` | class/* |  | A class group by name |
| `expclassgroup::classes::<group_id>::<limit>::<offset>` | class/* |  | The classes in a group |
| `expclassgroup::classCount::<group_id>` | class/* |  | Number of classes in a group |
| `expclassgroup::ofClass::<class>` | class/* |  | The groups a class is in |
| `expclassgroup::isEmpty::<group_id>` | class/* |  | Whether a group has no classes (and can be removed) |
| `expclassgroup::create::<name>` | class/* | POST | Creates a class group |
| `expclassgroup::rename::<group_id>::<name>` | class/* | POST | Renames a class group |
| `expclassgroup::remove::<group_id>` | class/* | POST | Removes an empty class group |
| `expclassgroup::addClass::<group_id>::<class>` | class/* | POST | Files a class in a group |
| `expclassgroup::removeClass::<group_id>::<class>` | class/* | POST | Takes a class out of a group (a class keeps at least one group) |

#### Attributes and datatypes (`expattribute`, 21 services, 5 writes)

The attributes of content objects (values per datatype, strings, titles, history, languages) and the datatype catalogue; set one or several attributes of an object (a new published version) or of a draft.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expattribute::get::<object_id>::<identifier>::<language>` | content/read |  | One attribute of an object with its value |
| `expattribute::getById::<attribute_id>::<version>` | content/read |  | An attribute by its id and version |
| `expattribute::value::<object_id>::<identifier>::<language>` | content/read |  | Only the value of an attribute |
| `expattribute::string::<object_id>::<identifier>::<language>` | content/read |  | The attribute as its string form (the format writes accept) |
| `expattribute::title::<object_id>::<identifier>::<language>` | content/read |  | The title text of an attribute |
| `expattribute::hasContent::<object_id>::<identifier>::<language>` | content/read |  | Whether the attribute has content |
| `expattribute::dataType::<object_id>::<identifier>` | content/read |  | The datatype of an attribute |
| `expattribute::classAttribute::<object_id>::<identifier>` | content/read |  | The class attribute definition of an attribute |
| `expattribute::history::<object_id>::<identifier>::<limit>` | content/read |  | The value of an attribute in every version, newest first |
| `expattribute::languages::<object_id>::<identifier>` | content/read |  | The value of an attribute in every language |
| `expattribute::missingRequired::<object_id>::<version>` | content/read |  | Required attributes without content in a version |
| `expattribute::byDataType::<datatype>::<limit>::<offset>` | content/read |  | Attributes of a datatype across current objects (readable ones) |
| `expattribute::dataTypes` | user |  | The datatype catalogue |
| `expattribute::dataTypeInfo::<datatype>` | user |  | One datatype: name, flags and the classes using it |
| `expattribute::dataTypeClasses::<datatype>` | class/* |  | The classes that have an attribute of a datatype |
| `expattribute::set::<object_id>::<identifier>` | content/edit | POST | Sets one attribute and publishes a new version. POST: value, language |
| `expattribute::setMany::<object_id>` | content/edit | POST | Sets several attributes and publishes a new version. POST: values (json identifier => string), language |
| `expattribute::clear::<object_id>::<identifier>` | content/edit | POST | Empties an attribute and publishes a new version |
| `expattribute::setDraft::<object_id>::<version>::<identifier>` | content/edit | POST | Sets one attribute of a draft version (not published). POST: value, language |
| `expattribute::setDraftMany::<object_id>::<version>` | content/edit | POST | Sets several attributes of a draft version. POST: values (json), language |
| `expattribute::validateDraft::<object_id>::<version>` | content/edit |  | Checks a draft: required attributes filled, input valid |

#### Versions and drafts (`expversion`, 25 services, 8 writes)

The version history of an object, drafts of a user and of all users, compare two versions; create a draft (in a language), publish, discard, remove old versions, revert.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expversion::list::<object_id>::<limit>::<offset>::<status>` | content/versionread |  | The versions of an object, newest first, paged |
| `expversion::count::<object_id>` | content/versionread |  | Number of versions of an object |
| `expversion::get::<object_id>::<version>` | content/versionread |  | One version |
| `expversion::current::<object_id>` | content/versionread |  | The current (published) version |
| `expversion::published::<object_id>` | content/read |  | The published version number |
| `expversion::status::<object_id>::<version>` | content/versionread |  | The status of a version |
| `expversion::creator::<object_id>::<version>` | content/versionread |  | The creator object of a version |
| `expversion::dataMap::<object_id>::<version>::<language>` | content/versionread |  | The attributes of a version with their values |
| `expversion::translations::<object_id>::<version>` | content/versionread |  | The languages of a version |
| `expversion::nodeAssignments::<object_id>::<version>` | content/versionread |  | The locations a version is assigned to |
| `expversion::compare::<object_id>::<version_a>::<version_b>::<language>` | content/diff |  | Differences between two versions, attribute by attribute |
| `expversion::drafts::<object_id>` | content/versionread |  | The drafts of an object |
| `expversion::myDrafts::<limit>::<offset>` | content/edit |  | The current user's drafts of all objects, paged |
| `expversion::allDrafts::<limit>::<offset>` | content/versionread |  | Drafts of all users (needs unrestricted version access), paged |
| `expversion::pending::<limit>::<offset>` | content/pendinglist |  | Versions waiting in the publishing queue, paged |
| `expversion::hasConflicts::<object_id>::<version>::<language>` | content/edit |  | Whether a draft is older than the published version of its language |
| `expversion::viewUrl::<object_id>::<version>::<language>` | content/versionread |  | The relative URL that previews a version |
| `expversion::createDraft::<object_id>` | content/edit | POST | Creates a draft from the current or a given version. POST: language, copy_from_version |
| `expversion::createDraftIn::<object_id>::<language>` | content/edit | POST | Creates a draft for a new or existing language. POST: copy_from_language |
| `expversion::publish::<object_id>::<version>` | content/publish | POST | Publishes a draft |
| `expversion::discard::<object_id>::<version>` | content/edit | POST | Discards a draft |
| `expversion::remove::<object_id>::<version>` | content/versionremove | POST | Removes an archived or draft version (never the published one) |
| `expversion::removeArchived::<object_id>::<keep>` | content/versionremove | POST | Removes archived versions of an object, keeping the newest N |
| `expversion::revert::<object_id>::<version>` | content/edit | POST | Creates a draft from an old version (publish it to bring the old content back). POST: language |
| `expversion::cleanupDrafts::<hours>` | content/versionremove | POST | Removes internal (unsaved) drafts older than a number of hours from all objects. Needs unrestricted version removal |

#### Translations (`exptranslation`, 19 services, 5 writes)

The site languages and the translations of objects: what exists, what is missing, status of a subtree; translate, copy a language, remove a translation, add and remove a site language.

| Call | Access | Write | Summary |
|---|---|---|---|
| `exptranslation::languages` | user |  | The languages of the site |
| `exptranslation::language::<locale>` | user |  | One site language by locale code |
| `exptranslation::prioritized` | public |  | The language codes of the siteaccess in priority order |
| `exptranslation::topPriority` | public |  | The first language of the siteaccess |
| `exptranslation::stats` | content/translations |  | Objects and classes per site language |
| `exptranslation::knownLocales::<text>::<limit>::<offset>` | content/translations |  | Locales that can be added as site languages |
| `exptranslation::ofObject::<object_id>` | content/read |  | The translations of an object with names and initial flag |
| `exptranslation::missing::<object_id>` | content/read |  | Site languages an object has no translation in |
| `exptranslation::content::<object_id>::<language>` | content/read |  | The attributes of an object in one language |
| `exptranslation::names::<object_id>` | content/read |  | The object name in every language |
| `exptranslation::nodeNames::<node_id>` | content/read |  | The names of a node in every language |
| `exptranslation::classNames::<class>` | user |  | The name of a class in every language |
| `exptranslation::canTranslate::<object_id>` | user |  | Whether the current user can translate an object, and into which languages |
| `exptranslation::status::<node_id>` | content/read |  | Translation completeness of a node subtree: objects per language |
| `exptranslation::translate::<object_id>::<language>` | content/translate | POST | Creates or changes a translation and publishes it. POST: attributes (json), copy_from_language |
| `exptranslation::copyLanguage::<object_id>::<from>::<to>` | content/translate | POST | Copies the content of one language into another and publishes |
| `exptranslation::remove::<object_id>::<language>` | content/translate | POST | Removes one translation of an object |
| `exptranslation::addLanguage::<locale>` | content/translations | POST | Adds a site language (locale code). POST: name |
| `exptranslation::removeLanguage::<locale>` | content/translations | POST | Removes a site language that no object or class uses |

#### Relations (`exprelation`, 15 services, 4 writes)

Relations between objects in both directions by type (common, embed, link, attribute), counts, broken relations; add, remove and replace common relations.

| Call | Access | Write | Summary |
|---|---|---|---|
| `exprelation::related::<object_id>::<type>::<limit>::<offset>` | content/read |  | Objects an object relates to (of the current version), paged. type: common, embed, link, attribute or all |
| `exprelation::reverse::<object_id>::<type>::<limit>::<offset>` | content/read |  | Objects that relate to an object, paged |
| `exprelation::counts::<object_id>` | content/read |  | Relation counts per type and direction |
| `exprelation::relatedCount::<object_id>::<type>` | content/read |  | Number of relations from an object |
| `exprelation::reverseCount::<object_id>::<type>` | content/read |  | Number of objects relating to an object |
| `exprelation::reverseCountForNodes::<node_ids>` | content/read |  | Reverse relation counts for a list of nodes |
| `exprelation::byAttribute::<object_id>::<identifier>` | content/read |  | Objects related through one attribute |
| `exprelation::exists::<from_object_id>::<to_object_id>` | content/read |  | Whether a relation between two objects exists, and its types |
| `exprelation::neighbours::<object_id>::<limit>` | content/read |  | Both directions at once: related and reverse related, light |
| `exprelation::broken::<object_id>` | content/read |  | Relations of an object that point at objects that are gone or not published |
| `exprelation::types` | user |  | The relation type names and their codes |
| `exprelation::add::<from_object_id>::<to_object_id>` | content/edit | POST | Adds a relation from an object to another. POST: type (common, embed, link; default common), version |
| `exprelation::remove::<from_object_id>::<to_object_id>` | content/edit | POST | Removes a relation. POST: type, version |
| `exprelation::removeAll::<object_id>` | content/edit | POST | Removes every common relation of an object (embed, link and attribute relations belong to the content) |
| `exprelation::replace::<object_id>` | content/edit | POST | Makes the common relations of an object exactly the given list. POST: object_ids |

#### Locations (`explocation`, 15 services, 5 writes)

The places an object lives at: list, main location, assignments, candidates and checks; add, remove, set the main location (large changes as jobs).

| Call | Access | Write | Summary |
|---|---|---|---|
| `explocation::list::<object_id>` | content/read |  | All locations (nodes) of an object |
| `explocation::count::<object_id>` | content/read |  | Number of locations of an object |
| `explocation::main::<object_id>` | content/read |  | The main location of an object |
| `explocation::parents::<object_id>` | content/read |  | The parent nodes of the locations of an object |
| `explocation::assignments::<object_id>` | content/read |  | The node assignments of an object (current version) |
| `explocation::isMain::<node_id>` | content/read |  | Whether a node is the main location of its object |
| `explocation::canAdd::<object_id>::<parent_node_id>` | user |  | Whether the current user can add the object to a node |
| `explocation::canRemove::<node_id>` | user |  | Whether a location can be removed (it is not the last one and has no children) |
| `explocation::candidates::<node_id>::<class>::<limit>::<offset>` | content/read |  | Container nodes below a node where an object of a class can be added |
| `explocation::orphans::<limit>::<offset>` | content/read |  | Published objects that have no node (needs unrestricted read) |
| `explocation::add::<object_id>::<parent_node_id>` | content/manage_locations | POST | Adds a location of an object below a node |
| `explocation::addMany::<target_node_id>` | content/manage_locations | POST | Adds the objects of several nodes below one node. POST: node_ids, mode |
| `explocation::remove::<node_id>` | content/manage_locations | POST | Removes one location of an object (not the last one) |
| `explocation::removeMany` | content/manage_locations | POST | Removes several locations. POST: node_ids, mode |
| `explocation::setMain::<node_id>` | content/manage_locations | POST | Makes a location the main one |

#### Trash (`exptrash`, 12 services, 4 writes)

Trashed objects: list, filter, original parent; restore, purge one or several, empty the trash (confirm field).

| Call | Access | Write | Summary |
|---|---|---|---|
| `exptrash::list::<sort>::<order>::<limit>::<offset>::<filter>` | content/restore |  | Trashed objects, paged and sorted (sort: name, class_name, published, modified, section) |
| `exptrash::count` | content/restore |  | Number of trashed objects |
| `exptrash::get::<object_id>` | content/restore |  | One trashed object |
| `exptrash::isTrashed::<object_id>` | content/restore |  | Whether an object is in the trash |
| `exptrash::originalParent::<object_id>` | content/restore |  | The node a trashed object was removed from, when it still exists in the same place |
| `exptrash::byClass::<class>::<limit>::<offset>` | content/restore |  | Trashed objects of a class |
| `exptrash::since::<since>::<limit>::<offset>` | content/restore |  | Objects trashed since a time (timestamp or date) |
| `exptrash::canEmpty` | user |  | Whether the current user may purge from the trash |
| `exptrash::restore::<object_id>` | content/restore | POST | Restores an object to its original parent, or to POST parent_node_id |
| `exptrash::purge::<object_id>` | content/cleantrash | POST | Removes one object from the trash for good |
| `exptrash::purgeMany` | content/cleantrash | POST | Purges several objects. POST: object_ids |
| `exptrash::emptyTrash` | content/cleantrash | POST | Empties the whole trash. POST: confirm=yes |

#### Sections (`expsection`, 17 services, 5 writes)

Sections and what is in them; create, change, remove; assign an object or a subtree (jobs for large subtrees).

| Call | Access | Write | Summary |
|---|---|---|---|
| `expsection::list::<limit>::<offset>` | section/view |  | All sections |
| `expsection::count` | section/view |  | Number of sections |
| `expsection::get::<section_id>` | section/view |  | A section by id |
| `expsection::getByIdentifier::<identifier>` | section/view |  | A section by identifier |
| `expsection::objects::<section_id>::<limit>::<offset>` | content/read |  | Objects (main nodes) in a section, paged |
| `expsection::objectCount::<section_id>` | section/view |  | Number of objects in a section |
| `expsection::usage` | section/view |  | Sections with their object counts |
| `expsection::canRemove::<section_id>` | section/view |  | Whether a section can be removed and what blocks it |
| `expsection::assignable` | user |  | Sections the current user may assign |
| `expsection::navigationParts` | section/view |  | The navigation parts a section can use |
| `expsection::ofObject::<object_id>` | content/read |  | The section of an object |
| `expsection::ofNode::<node_id>` | content/read |  | The section of a node |
| `expsection::create` | section/edit | POST | Creates a section. POST: name, identifier, navigation_part |
| `expsection::update::<section_id>` | section/edit | POST | Changes a section (same POST fields) |
| `expsection::remove::<section_id>` | section/edit | POST | Removes an unused section |
| `expsection::assign::<object_id>::<section_id>` | section/assign | POST | Assigns an object to a section |
| `expsection::assignSubtree::<node_id>::<section_id>` | section/assign | POST | Assigns a node and its subtree to a section. POST: mode |

#### Object states (`expstate`, 23 services, 9 writes)

State groups and states, the states of an object, objects in a state; create, change, remove, order; assign to an object or a subtree (jobs for large subtrees).

| Call | Access | Write | Summary |
|---|---|---|---|
| `expstate::groups::<limit>::<offset>` | state/administrate |  | The state groups with their states |
| `expstate::groupCount` | state/administrate |  | Number of state groups |
| `expstate::group::<group>` | state/administrate |  | A state group by id or identifier, with its states |
| `expstate::states::<group>` | state/administrate |  | The states of a group in order |
| `expstate::state::<state_id>` | state/administrate |  | One state by id |
| `expstate::stateByIdentifier::<group>::<identifier>` | state/administrate |  | One state by group and identifier |
| `expstate::translations::<state_id>` | state/administrate |  | The names and descriptions of a state in every language |
| `expstate::groupTranslations::<group>` | state/administrate |  | The names and descriptions of a group in every language |
| `expstate::defaultState::<group>` | state/administrate |  | The first (default) state of a group |
| `expstate::ofObject::<object_id>` | content/read |  | The states of an object |
| `expstate::allowedForObject::<object_id>` | content/read |  | The states the current user may assign to an object |
| `expstate::objects::<state_id>::<limit>::<offset>` | content/read |  | Objects (main nodes) in a state, paged |
| `expstate::objectCount::<state_id>` | state/administrate |  | Number of objects in a state |
| `expstate::limitations` | state/administrate |  | The state limitations policies can use |
| `expstate::createGroup` | state/administrate | POST | Creates a state group. POST: identifier, name, description, language |
| `expstate::updateGroup::<group>` | state/administrate | POST | Changes a state group. POST: identifier, name, description, language |
| `expstate::removeGroup::<group>` | state/administrate | POST | Removes a state group with its states |
| `expstate::createState::<group>` | state/administrate | POST | Creates a state in a group. POST: identifier, name, description, language |
| `expstate::updateState::<state_id>` | state/administrate | POST | Changes a state. POST: identifier, name, description, language |
| `expstate::removeState::<state_id>` | state/administrate | POST | Removes a state (its objects get the group default) |
| `expstate::reorderStates::<group>` | state/administrate | POST | Orders the states of a group. POST: state_ids (the group's state ids in the new order) |
| `expstate::assign::<object_id>::<state_id>` | state/assign | POST | Assigns a state to an object |
| `expstate::assignSubtree::<node_id>::<state_id>` | state/assign | POST | Assigns a state to a node and its subtree. POST: mode |

#### URL aliases and wildcards (`expurlalias`, 24 services, 6 writes)

The nice URLs: aliases of nodes, resolving a path, normalising text, global aliases, redirects, wildcards; create and remove custom aliases and wildcards.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expurlalias::forNode::<node_id>::<type>::<limit>::<offset>` | content/read |  | The URL aliases of a node. type: alias (custom), name (generated) or all |
| `expurlalias::forObject::<object_id>` | content/read |  | The generated URL of every location of an object |
| `expurlalias::path::<node_id>` | content/read |  | The URL path of a node (generated alias) |
| `expurlalias::resolve::<path>` | user |  | Resolves a URL path to its action and node |
| `expurlalias::exists::<path>` | content/read |  | Whether a path is taken by an alias |
| `expurlalias::children::<path>::<limit>::<offset>` | content/urltranslator |  | The alias elements directly below a path (empty path: the top) |
| `expurlalias::normalize::<text>` | user |  | Turns text into the form the URL transformation rules give it |
| `expurlalias::normalizePath::<path>` | user |  | Normalises every element of a path |
| `expurlalias::list::<type>::<limit>::<offset>::<text>` | content/urltranslator |  | All custom aliases (type alias), name or all, paged |
| `expurlalias::count::<type>` | content/urltranslator |  | Number of aliases of a type |
| `expurlalias::redirects::<limit>::<offset>` | content/urltranslator |  | Aliases that redirect to the real URL, paged |
| `expurlalias::byAction::<action>` | content/urltranslator |  | The aliases of an action such as eznode:43 or module:search |
| `expurlalias::pathPrefix` | public |  | The path prefix of the siteaccess |
| `expurlalias::wildcards::<limit>::<offset>` | content/urltranslator |  | The URL wildcards, paged |
| `expurlalias::wildcardCount` | content/urltranslator |  | Number of wildcards |
| `expurlalias::wildcard::<wildcard_id>` | content/urltranslator |  | One wildcard by id |
| `expurlalias::wildcardBySource::<source>` | content/urltranslator |  | A wildcard by its source URL |
| `expurlalias::wildcardMatches::<path>` | content/urltranslator |  | Whether a path is caught by any wildcard |
| `expurlalias::createAlias::<node_id>` | content/urltranslator | POST | Adds a custom URL alias to a node. POST: alias, language, parent_is_root, redirects |
| `expurlalias::removeAlias::<node_id>` | content/urltranslator | POST | Removes custom aliases of a node. POST: elements (list of parent.md5.language from forNode) |
| `expurlalias::removeAllAliases::<node_id>` | content/urltranslator | POST | Removes every custom alias of a node |
| `expurlalias::createWildcard` | content/urltranslator | POST | Adds a wildcard. POST: source, destination, type (forward or direct) |
| `expurlalias::removeWildcard::<wildcard_id>` | content/urltranslator | POST | Removes a wildcard |
| `expurlalias::removeWildcards` | content/urltranslator | POST | Removes several wildcards. POST: ids |

#### Search (`expsearch`, 18 services, 3 writes)

Full text search with paging, class, section, subtree and date filters and facets; suggestions, index status and statistics; index and unindex an object.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expsearch::search::<text>::<limit>::<offset>::<filter>` | content/read |  | Searches the content. filter: class[], section, subtree[], date (day, week, month, 3months, year) |
| `expsearch::count::<text>::<filter>` | content/read |  | Number of matches of a search |
| `expsearch::facets::<text>::<filter>` | content/read |  | Counts of the matches per class and per section (over the first 500 matches) |
| `expsearch::byClass::<text>::<classes>::<limit>::<offset>` | content/read |  | Searches within classes |
| `expsearch::inSubtree::<text>::<node_id>::<limit>::<offset>` | content/read |  | Searches below a node |
| `expsearch::bySection::<text>::<section_id>::<limit>::<offset>` | content/read |  | Searches within a section |
| `expsearch::byAttribute::<text>::<class_attribute_id>::<limit>::<offset>` | content/read |  | Searches the values of one class attribute |
| `expsearch::similar::<object_id>::<limit>` | content/read |  | Objects similar to an object (searches with its name) |
| `expsearch::suggest::<prefix>::<limit>` | content/read |  | Words of the index that start with a prefix, most used first |
| `expsearch::normalize::<text>` | user |  | The text as the search engine normalises it |
| `expsearch::engine` | user |  | The search engine in use and its flags |
| `expsearch::searchableClasses` | user |  | Classes with searchable attributes |
| `expsearch::indexStatus::<object_id>` | content/read |  | Whether an object has words in the index |
| `expsearch::stats` | setup/administrate |  | Index size: words, links, objects with words |
| `expsearch::topPhrases::<limit>::<offset>` | setup/administrate |  | The most frequent search phrases of the site, with their average hits |
| `expsearch::reindex::<object_id>` | content/edit | POST | Indexes an object again |
| `expsearch::removeFromIndex::<object_id>` | content/edit | POST | Removes an object from the index |
| `expsearch::clearPhrases` | setup/administrate | POST | Clears the stored search phrase statistics. POST: confirm=yes |

#### Content jobs (`expcontentjob`, 26 services, 14 writes)

The background jobs for large operations: list and follow them, estimate whether an operation runs now or as a job, create a job of every type, cancel, resume, start.

| Call | Access | Write | Summary |
|---|---|---|---|
| `expcontentjob::list::<state>::<limit>::<offset>` | content/jobs |  | The current user's jobs, newest first. state: queued, running, done, failed, cancelled or all |
| `expcontentjob::listAll::<state>::<limit>::<offset>` | setup/administrate |  | The jobs of all users (needs setup/administrate) |
| `expcontentjob::active` | content/jobs |  | The current user's queued and running jobs |
| `expcontentjob::summary` | content/jobs |  | Counts of the current user's jobs per state |
| `expcontentjob::get::<job_id>::<log_lines>` | content/jobs |  | One job with its progress, result and the last log lines |
| `expcontentjob::progress::<job_id>` | content/jobs |  | Only the progress of a job: done, total, percent, phase, message |
| `expcontentjob::log::<job_id>::<lines>` | content/jobs |  | The last lines of the job log |
| `expcontentjob::result::<job_id>` | content/jobs |  | The result of a finished job |
| `expcontentjob::types` | content/jobs |  | The registered job types with their parameters |
| `expcontentjob::settings` | content/jobs |  | The limits that decide now or job: SynchronousLimit, NowLimit, BatchSize |
| `expcontentjob::estimate::<type>::<params>` | content/jobs |  | How many nodes an operation touches and whether it runs now or as a job. params as in create |
| `expcontentjob::lockOf::<node_id>` | content/jobs |  | The job that locks a node, if any |
| `expcontentjob::create::<type>` | content/jobs | POST | Creates a job and starts its worker. POST: params (json, see types), spawn (default 1) |
| `expcontentjob::remove` | content/remove | POST | Job: removes nodes. POST: node_ids, move_to_trash, spawn |
| `expcontentjob::copy` | content/create | POST | Job: copies a subtree. POST: source_node_id, destination_node_id, all_versions, keep_creator, keep_time, spawn |
| `expcontentjob::move` | content/move | POST | Job: moves a subtree. POST: node_id, new_parent_node_id, spawn |
| `expcontentjob::hide` | content/hide | POST | Job: hides a subtree. POST: node_id, spawn |
| `expcontentjob::reveal` | content/hide | POST | Job: reveals a subtree. POST: node_id, spawn |
| `expcontentjob::section` | section/assign | POST | Job: assigns a section to a subtree. POST: node_id, section_id, spawn |
| `expcontentjob::state` | state/assign | POST | Job: assigns a state to a subtree. POST: node_id, state_id, spawn |
| `expcontentjob::addLocation` | content/manage_locations | POST | Job: adds locations. POST: node_ids, target_node_id, spawn |
| `expcontentjob::removeLocation` | content/manage_locations | POST | Job: removes locations. POST: node_ids, spawn |
| `expcontentjob::cancel::<job_id>` | content/jobs | POST | Cancels a job (a running job stops after its current batch) |
| `expcontentjob::resume::<job_id>` | content/jobs | POST | Resumes a failed job or one whose worker died |
| `expcontentjob::spawn::<job_id>` | content/jobs | POST | Starts the worker of a queued job |
| `expcontentjob::purgeFinished::<days>` | setup/administrate | POST | Removes finished jobs older than a number of days (needs setup/administrate) |

Total: 350 content services in 15 domains (112 writes).
<!-- expservices-content:end -->
