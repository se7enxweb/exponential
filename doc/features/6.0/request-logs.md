# Request logs: 404.log and security.log

Exponential writes a request for a page that does not exist, and a request that looks like an attack, to logs of
their own instead of `error.log`. `error.log` keeps the real faults, the not-found answers go to `var/log/404.log`
with the reason the kernel found, and what the request classifier takes for a probe or an attack goes to
`var/log/security.log` with its category and rule. Both are kernel logs of [the log viewer](log-viewer.md), are
listed by its error digest and its log health page, and are rotated like the other logs
([log rotation](log-rotation.md)).

## In short

| Log | What | Level in the viewer |
|---|---|---|
| `error.log` | real faults only (a database error, a template that does not compile, a PHP error ...) | error |
| `404.log` | the answers 404 and 410: an undefined module or view, a module or view the siteaccess switches off, a node or object that is not there, a request rule that answers "not found" | notice |
| `notice.log` | the answers 401 and 403, as before: they are access answers; the security side is in `auth.log` and the audit trail | notice |
| `security.log` | a request the request classifier names a probe, a traversal, an injection, an attack tool or a burst of not-found answers, **with any status** (200, 401, 403, 404, 500 ...) | warning |

- **Every request is classified once, whichever way it ends:** at the normal end, through `eZExecution::cleanExit()`
  (a file download, an ezjscore call, a redirect, a view that sends its own output), through the error module, or
  after a fatal error. A probe such as `/content/download/../../etc/passwd` is in `security.log` even when the
  download view answers it itself; a normal download or ezjscore call is not flagged.
- **One line per request, in one log.** A request is in `security.log` *or* `404.log` *or* (for a real fault)
  `error.log`, never in two of them. A classified probe that ends in a 404 is in `security.log` only; its 404 line
  (and the 401/403 notice of a classified request) is not written.
- **Nothing is blocked.** The classifier only classifies: the request gets the answer it would get anyway.
  Listeners can act on a verdict later (see [Listeners](#listeners-the-hook-for-blocking)).
- **Privacy first.** Masked client addresses, a hash and a short part of the user agent, every query value replaced
  by `***`. The raw address is never written to a file.
- **Backwards compatible.** With `site.ini [RequestLogSettings] NotFoundLog=disabled` the not-found answers go to
  `error.log` exactly as before; with `requestclassifier.ini [ClassifierSettings] Classification=disabled` nothing
  is classified and `security.log` stays empty.

## The line formats

Both logs start their lines with the prefix eZLog writes, the time and the masked client address:

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] <the line>
```

### 404.log

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] 404 GET /site/no-such-page?q=*** siteaccess=site reason=module:no-such-page node=- referrer=- ua=3f2a9c1b "Mozilla/5.0 (X11; Linux x86_64)"
[ Oct 17 2026 09:00:04 ] [2001:db8:x:x] 404 GET /content/view/full/9999 siteaccess=site reason=node:9999 node=9999 referrer=https://example.org/news ua=5d41402a "Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5)"
[ Oct 17 2026 09:00:09 ] [198.51.x.x] 410 GET /old-campaign siteaccess=site reason=kernel:kernel/20 node=- referrer=- ua=- ""
```

| Field | Meaning |
|---|---|
| `404` | the status of the answer: 404 or 410 |
| `GET /site/no-such-page?q=***` | the method and the URI: the path as requested (secrets and e-mail addresses in it masked), every query value `***`, control characters removed, spaces encoded, at most `MaxURILength` characters (a longer one ends in `...`) |
| `siteaccess=` | the siteaccess that answered |
| `reason=` | why the page is missing, the first reason the kernel found: `module:<name>` (no such module), `view:<module>/<view>` (no such view), `disabled:<module>[/<view>]` (switched off for this siteaccess), `node:<id>`, `object:<id>`, `rule:<name>` (a rule of `requestrules.ini` that answers "not found"), `file:<object>/<attribute>` (a download whose file is not available), or `kernel:<error type>/<error number>` when nothing more is known |
| `node=` | the node id when it is known, else `-` |
| `referrer=` | the referring address, written like the URI, or `-` |
| `ua=3f2a9c1b "..."` | the first 8 hexadecimal digits of the SHA-1 of the user agent, and its first 60 characters without quotes and control characters (`ua=- ""` without one) |

### security.log

```
[ Oct 17 2026 09:00:00 ] [203.0.x.x] probe/wp-login confidence=high 404 GET /wp-login.php siteaccess=site namespace=/wp-login.php ua=3f2a9c1b "curl/8.5.0"
[ Oct 17 2026 09:00:02 ] [203.0.x.x] injection/sql-union confidence=high 200 GET /content/search?SearchText=1%27%20UNION%20SELECT%20login%20FROM%20ezuser-- siteaccess=site namespace=/content/search ua=3f2a9c1b "curl/8.5.0"
[ Oct 17 2026 09:00:03 ] [192.0.x.x] threshold/404-rate confidence=medium 404 GET /a7f3.php siteaccess=site namespace=/a7f3.php ua=0beec7b5 "Mozilla/5.0"
```

| Field | Meaning |
|---|---|
| `probe/wp-login` | `<category>/<rule>`: the category of the rule set (`probe`, `traversal`, `injection`, `scanner`, `threshold`, or one an extension adds: lower case letters, digits, `_` and `-`) and the rule that matched |
| `confidence=` | `low`, `medium` or `high`: how sure the rule is |
| `404` | the final HTTP status, or `-` when it is not known |
| `GET /wp-login.php` | the method and the URI. Unlike 404.log the query values are kept, because they are the evidence of the attack; secrets, e-mail and IP addresses in them are masked with the log viewer's redactor, and the URI is cut at `MaxURILength` |
| `siteaccess=` | the siteaccess, `-` when unknown |
| `namespace=` | the path below the siteaccess: `/admin/wp-login.php` and `/site/wp-login.php` are both `/wp-login.php`, so a rule sees the same path under every siteaccess |
| `ua=` | as in 404.log |

The log viewer reads both formats: the request, the siteaccess and the status are its fields, and the source column
shows the status (404.log) or `<category>/<rule>` (security.log).

## Settings

### site.ini [RequestLogSettings]

| Setting | Default | Meaning |
|---|---|---|
| `NotFoundLog` | `enabled` | `enabled`: 404 and 410 answers are written to `NotFoundLogFile` and no longer to `error.log` ("Undefined module", "Module/View ... is disabled", "Undefined view", "Error ocurred using URI ... HTTP 404", node not found). `disabled`: `error.log` as before. |
| `NotFoundLogFile` | `404.log` | the file, in the log directory (`var/log`, or `var/<site>/log` with `[FileSettings] UseGlobalLogDir=disabled`). The log viewer knows it by the key of its name: `404.log` is the key `404`. A file of another name has no label and no rotation defaults until you give it its own (see [The log viewer](#the-log-viewer-rotation-and-the-error-digest)). |
| `MaskIP` | `enabled` | the client address with its host part hidden (`203.0.x.x`, `2001:db8:x:x`), the same output as the log viewer's masking. `disabled` writes it in full; only for a site that has a reason and a legal basis for it. |
| `MaxURILength` | `300` | the longest URI (and referrer) written; a longer one is cut and ends in `...`. |

### requestclassifier.ini

`settings/requestclassifier.ini`, overridden in `settings/override/requestclassifier.ini.append.php`, a siteaccess
or an extension.

- `[ClassifierSettings]`
  - `Classification=enabled`: `disabled` classifies nothing; probes are then logged as the not-found answers they are.
  - `LogFile=security.log`: the file, in the same log directory.
  - `RuleSets[]`: the rule sets in the order they are tried; the first one with a verdict wins, so a request is one
    line with one rule. Built in: `probes`, `traversal`, `injection`, `scanner`, `threshold`. Leave one out to
    switch it off.
  - `Listeners[]`: classes told about every verdict (none by default).
  - `CompileCache=enabled`: the compiled patterns are kept in the cache directory, so a fresh worker does not
    compile them again.
- `[ThresholdSettings]`: `Max404PerMinute=30` not-found answers (`Statuses[]`: 404 and 410) to one client within
  `WindowSeconds=60`; every further one in that window is a verdict `threshold/404-rate` (medium; high above twice
  the limit). `0` switches the rule off.
- `[RuleSet-<id>]`, one group per set:
  - `Class=`: the class of the set (empty for a built-in one; needed for an extension's set);
  - `Patterns[<rule>]=<regex>`: a rule of your own, or a built-in rule replaced by giving its name (PCRE without
    delimiters, case-insensitive, no back references or named groups; a pattern that does not compile is left out
    with a warning in the debug output);
  - `Disabled[]=<rule>`: a built-in rule switched off;
  - `Excludes[<name>]=<regex>`: a path never flagged by the set (`probes` excludes downloads, storage, the packed
    scripts and styles and the design assets by default);
  - `Confidence[<rule>]=low|medium|high` and `DefaultConfidence`.

The rule names of each built-in set are listed in the file itself. A search for "union station", O'Reilly or `<b>`
is no injection, and general clients (curl, wget, headless browsers, search engines) are no attack tools.

## Adding a rule set in an extension

A rule set is a class that implements `expRequestRuleSetInterface` (`identifier()`, `evaluate( expRequestContext
$context )` returning an `expRequestVerdict` or `null`). For a set of regular expressions, extend
`expRequestPatternRuleSet`; it compiles the patterns once, applies `Patterns[]`, `Disabled[]`, `Excludes[]` and
`Confidence[]` from the INI group, and caches the result:

```php
<?php
// extension/myext/classes/myextrequestrulesetlegacyapps.php
class myextRequestRuleSetLegacyApps extends expRequestPatternRuleSet
{
    /** The first part of the log line: legacyapp/<rule> */
    public function category()
    {
        return 'legacyapp';
    }

    /** rule => regular expression (no delimiters, matched case-insensitively) */
    protected function builtInPatterns()
    {
        return array(
            'joomla-admin' => '^/administrator(?:/|$)',
            'typo3-backend' => '^/typo3(?:/|$)',
        );
    }

    /** What the patterns are matched against: here the path below the siteaccess, decoded */
    protected function subjects( expRequestContext $context )
    {
        return array( $context->decodedNamespacePath() );
    }
}
```

```ini
# extension/myext/settings/requestclassifier.ini.append.php
[ClassifierSettings]
RuleSets[]=legacyapps

[RuleSet-legacyapps]
Class=myextRequestRuleSetLegacyApps
# optional: a rule of the site, a rule switched off, a confidence
Patterns[drupal-install]=^/install\.php$
Disabled[]
Confidence[typo3-backend]=medium
```

Regenerate the extension autoloads (`php bin/php/ezpgenerateautoloads.php -e`). A class that does not exist or does
not implement the interface is skipped with a warning in the debug output; an exception in `evaluate()` skips the
set for that request. `evaluate()` runs for every classified request, so it must be cheap: no database, no network,
no file access. The lines then read `legacyapp/joomla-admin confidence=high 404 GET /administrator/ ...`, and the
error digest groups them under `legacyapp/joomla-admin` without any further setting.

## Listeners: the hook for blocking

`[ClassifierSettings] Listeners[]` names classes that implement `expRequestVerdictListenerInterface`:

```php
class myextVerdictListener implements expRequestVerdictListenerInterface
{
    public function onVerdict( expRequestVerdict $verdict, expRequestContext $context )
    {
        // $verdict->category, $verdict->rule, $verdict->confidence
        // $context->ip is the raw address: hash or mask it before you keep it anywhere
    }
}
```

A listener runs at the end of the request, after the security line is written, and must be cheap; an exception it
throws is caught and ignored. None is configured by default, and the kernel never blocks, slows down or answers
differently because of a verdict. This is where a ban list, a rate limit or a notification can be added later.

## Privacy

- **Addresses masked** (`MaskIP=enabled`): `203.0.x.x`, `2001:db8:x:x`. An address of a form neither pattern knows
  is written as `x`, never in full.
- **The user agent** as the first 8 hexadecimal digits of its SHA-1 plus its first 60 characters: enough to tell
  one tool from another, not a fingerprint.
- **The query redacted** (404.log): every value is `***` (the names are kept, so `?q=***&page=***` still says what was asked);
  secrets and e-mail addresses in the path are masked with the log viewer's redactor. security.log keeps the
  query values (the evidence of the probe) with secrets, e-mail and IP addresses masked.
- **The not-found counter** of the threshold rule is keyed by a salted SHA-1 of the address (a random salt in the
  cache directory plus `site.ini [HTMLForms] Secret`); the address itself is never stored. A cache clear makes a new
  salt, which only restarts the counting.
- The log viewer masks what it shows again (`[RedactionSettings]`), and the error digest is always masked.

## Performance

- No database access. The patterns of a rule set are compiled into one regular expression per set and cached per
  version of the settings in the cache directory (`CompileCache`).
- A request is classified once; a request with a 200 answer is classified at the end of the request, the cost of a
  few regular expressions on its path, query and user agent.
- Only a 404 or 410 answer touches the not-found counter (one small file update under a lock).
- One `file_put_contents` with `FILE_APPEND | LOCK_EX` per logged request; files created with the mode of a log
  file, so the web server's user and a Velocity worker running as another user can both append.

## The log viewer, rotation and the error digest

`settings/logviewer.ini` makes both files kernel logs of **Setup > Log files**:

- `KernelLogs[]=404.log` and `KernelLogs[]=security.log`: listed under "Kernel", opened at
  `/setup/logs/view/404` and `/setup/logs/view/security`, managed (rotate, prune, compress) like any kernel log.
- `LogLabel[404]=Not found requests (404)`, `LogLabel[security]=Security (request classifier)`: the names the
  navigator and the digest show (translated in the context `kernel/logviewer`). `LogLabel[<key>]` names any log of
  the log directories by its key.
- `FileLevels[]=404.log*;notice` (a not-found answer is an answer, not a fault) and `FileLevels[]=security.log*;warning`
  (worth a look; so the error digest lists it by default).

**The error digest** (Setup > Log files > Error digest) lists both, grouped per log by `[DigestSettings]`:

- `GroupBy[security]=category-rule`: security.log by `<category>/<rule>` (`probe/wp-login 312`,
  `injection/sql-union 4`), not by the whole line, which differs in every URI.
- `GroupBy[404]=reason`: 404.log by its `reason=` field with ids and long paths set aside (`reason=module:wp-admin`,
  `reason=node:#`, `reason=view:content/#`): why pages are missing, not a list of every missing address.
- `LogLevels[404]=error,warning,notice`: 404.log is listed although its lines are notices.
- Other groupings for any log: `message` (the default), `reason-kind` (only `module`, `view`, `node` ...) and `uri`
  (the method and the path without the query, numbers set aside). Each group links to the log filtered to it.

**Rotation** (`[RotationSettings]`, see [log rotation](log-rotation.md)): both logs keep 5 archives, compressed from
`.2` on, with a size limit of 10 MB for the cronjob part `logs`; a prune removes the archives of 404.log after 30
days and of security.log after 90 days. While a line is written, the writer rotates the file at `site.ini
[FileSettings] LogFileMaxSize` like every other log.

```ini
# settings/override/logviewer.ini.append.php: keep security.log longer
[RotationSettings]
Keep[security]=10
MaxAgeDays[security]=365
```

**Log health** (Setup > Log files > Health) counts both against the disk budget and shows their growth and limits.

With `NotFoundLogFile=notfound.log` the key is `notfound`: give it its settings under that key
(`LogLabel[notfound]=...`, `KernelLogs[]=notfound.log`, `FileLevels[]=notfound.log*;notice`, `GroupBy[notfound]=reason`,
`LogLevels[notfound]=error,warning,notice` and its `[RotationSettings]`).

## Request shield and the classifier

[request-shield](https://github.com/cjw-network/request-shield) (cjw-network) is a separate layer that runs **before
PHP**, under Exponential Velocity only: its rules (`var/request-shield/rules/*.rules`) can refuse or log a request
before the kernel starts, in its own log (`var/request-shield/log/shield.log`). A request it refuses never reaches
the kernel, so it is not in `security.log` either; in its monitor mode it only logs, and the same probe can then be in
`shield.log` and in `security.log`.

The request classifier is the kernel's own layer, under Apache/PHP-FPM as under Velocity. It **only classifies**: it
never refuses a request. Where the two overlap they use the same idea of a "probe" and similar names
(`wp-login`, `env`, `git` ...), so a line of one can be found in the other, but they are configured separately and
neither reads the other's rules.

## For developers

- `expRequestLog` (`kernel/classes/requestlog/`): the per-request state of the not-found log. `reason()` records why
  the page is missing (the first reason wins), `handleError()` is called by the error module and writes the security
  line or the 404 line, `finish()` at the end of the request classifies what was not classified yet. `reset()` for
  tests and persistent workers.
- How a request ends does not matter: `ezpKernelWeb::run()` calls `expRequestLog::begin()`, which resets the state
  and registers `expRequestLog::finish()` as a clean-up handler of `eZExecution`. `eZExecution::cleanup()` runs on
  `cleanExit()`, at the normal end (`ezpKernelWeb::shutdown()`, which also calls `finish()` itself) and in the shutdown
  handler after a fatal error; only the first `finish()` of a request counts (`expRequestLog::finished()`). Under
  Velocity's persistent workers `eZExecution` keeps the handlers per request (`REQUEST_TIME_FLOAT`) and adds a handler
  once, so nothing is carried over or doubled. Never wrap a `cleanExit()` in `catch ( Exception )`: under Velocity it
  ends the request by throwing.
- `expRequestClassifier` (`kernel/classes/requestclassifier/`): `instance()`, `classify( expRequestContext )`,
  `securityLine()`, `log()`; with `expRequestContext` (`fromGlobals()`, `create()` for tests), `expRequestVerdict`,
  `expRequestRuleSetInterface`, `expRequestPatternRuleSet` and `expRequestVerdictListenerInterface`.
- The log viewer: `expLogParser::requestHeader()` reads both formats, `expLogDigest::groupText()` the groupings.
- Tests (no database): `tests/tests/kernel/requestlog/`, `tests/tests/kernel/requestclassifier/` and
  `tests/tests/kernel/logviewer/expLogRequestLogsTest.php`.
