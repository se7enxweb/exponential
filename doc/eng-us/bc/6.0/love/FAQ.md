# Frequently asked questions

**I run Exponential and never ran LoveStack. Do I need to do anything?**
No. The features are part of Exponential 6.0.15. See
[Getting started](GETTING_STARTED.md#what-changes-on-my-exponential-site) for what changes after the
upgrade and which features you may want to switch on.

**Will my site behave differently after upgrading?**
Only where a feature fixes a clear bug or closes a security gap for every site (logging out
clears the session cookie, uploaded images are checked by their content), plus a few small
changes to logging (deprecations in their own log, 2 MB log files) and a Copy button in the
administration. Features that could change what visitors or editors see are off on existing
sites. [FEATURES.md](FEATURES.md) lists the default of each.

**Why do new installations get different defaults?**
Where a feature makes a site safer but could break something an existing site relies on
(open registration, anonymous JavaScript reading node data, duplicate logins), a new site starts
with the safer setting and an upgraded site keeps today's behaviour until you decide.

**How do I switch a feature on?**
Each entry of the [overview](../lovestack.md#feature-by-feature) has a "Switch on" line. Most are
one setting in an override file, followed by `php bin/php/ezcache.php --clear-tag=ini`.

**Is Exponential copying LoveStack's code?**
Each feature was rebuilt in Exponential's own APIs, with its own tests. Every feature is treated
as derived from Mugo Web's work, so each file with derived code keeps every existing copyright
notice, names the LoveStack commit it comes from, and credits Mugo Web, as the GNU GPL and good
practice ask. See [Licence and credits](../lovestack.md#licence-and-credits).

**Why not merge LoveStack into Exponential?**
The two have developed separately since 2016. Much of what LoveStack changed in that time,
Exponential also changed, in its own way (PHP 8.5 support, its engines, its caching, its audit
trail). Rebuilding feature by feature keeps what each does well and changes nothing an existing
Exponential site relies on.

**Is this endorsed by Mugo Web?**
No endorsement is implied. LoveStack is Mugo Web's distribution and remains theirs; Exponential
uses the name only to say where a feature comes from.

**Which PHP versions?**
Exponential 6: PHP 8.1 to 8.5. LoveStack's `composer.json` (at the commit used for the port):
PHP 7.3–7.4 and 8.1–8.3.

**Do I lose `ezenum` content if I move from LoveStack?**
No. Exponential keeps the `ezenum` datatype in the kernel. Disable LoveStack's `legacy_2018`
extension, which held it there ([migration, step 4.1](MIGRATION.md#41-remove-legacy_2018)).

**My LoveStack database has a unique index on logins. Is that a problem?**
No. Exponential works with it, and new Exponential installations have the same index. On an
upgraded Exponential site, `./console exp:user-login-check` reports duplicates and adds it on
request.

**My LoveStack database has `data_text NOT NULL`. Is that a problem?**
No. Exponential stores an empty string there, never NULL; the column can stay as it is.

**Does Exponential set `Content-Length` like LoveStack's `SetContentLengthHeader`?**
Exponential's engines and HTTP cache send it for the responses they serve. The LoveStack setting
is not needed; remove it.

**How do I know the features work?**
Every feature has PHPUnit tests that run on their own, and LoveStack's own test cases for
behaviour Exponential has are part of the suite. See [TESTING.md](TESTING.md).

**Where do I report a problem?**
In the Exponential issue tracker: <https://github.com/se7enxweb/exponential/issues>. Please do not
report problems with Exponential to Mugo Web.
