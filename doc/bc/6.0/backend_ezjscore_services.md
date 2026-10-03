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
