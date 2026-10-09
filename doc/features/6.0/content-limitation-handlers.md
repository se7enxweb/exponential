# Content policy limitations of extensions

Read this page if you build an extension that narrows who may read or edit content by a rule the kernel does not
know (an organisation, a region, a rights matrix), or if you check access on behalf of another user (notifications,
lists sent by mail). Administrators who keep roles find what changes for them under
[What changes for existing installations](#what-changes-for-existing-installations).

## In short

| | |
|---|---|
| What is new | An extension can add its own limitation to a content function (`content/read`, `content/edit`, ...), next to Class, Section and Subtree. A handler class decides for objects, nodes and versions and gives the SQL condition of list and tree fetches. |
| Registered by | The filter `module/functionlist` (`site.ini [Event] Listeners[]`) adds the limitation to the function; `site.ini [RoleSettings] LimitationHandlers[<limitation>]=<class>` names its handler. |
| Contract | `ezpContentLimitationHandler`: `checkAccess( $limitation, $values, $functionName, $subject, $userID )` and `permissionSQL( $limitation, $values, $tableAliasName, $userID )`. |
| Also new | `checkAccess()` of `eZContentObject`, `eZContentObjectTreeNode` and `eZContentObjectVersion` takes the user to check for as a sixth argument, `editAccess()` a third; `./console exp:access:check` asks them from the command line. |
| Safety rule | A limitation that no handler evaluates denies, everywhere. So does a handler that is missing, cannot be made, throws, or answers with SQL that is not a self-contained condition; each is logged once per request. |
| Start from | **Setup > RAD > Settings extension** with the event `module/functionlist`: it writes the listener and a working handler (`<class>MaxDepth`). |

## What you can do

Before, a limitation of your own meant a patch of the kernel: a `case` in the `checkAccess()` methods of objects, nodes
and versions, one in `eZContentObjectTreeNode::createPermissionCheckingSQL()` for fetches, and a line in
`kernel/content/module.php` so that the role editor offers it. Now the extension brings all three:

| Where the kernel checks a policy | What it asks the handler |
|---|---|
| `eZContentObject::checkAccess()` (`$object.can_read`, `can_edit`, ...) | `checkAccess()` with the object |
| `eZContentObjectTreeNode::checkAccess()` (`$node.can_read`, ...) | `checkAccess()` with the node |
| `eZContentObjectVersion::checkAccess()` (`versionread`, `versionremove`) | `checkAccess()` with the version |
| List and tree fetches, the search of the kernel, related objects (`createPermissionCheckingSQL()`) | `permissionSQL()` for the current user |
| Subtree notifications (who gets the mail about a new object) | `checkAccess()` with the object, for each subscriber |

The limitation works like a kernel limitation: the limitations of one policy are joined by AND, the policies of a user
by OR.

## Build one

The example limits reading by region. The extension `myext` keeps the region of each object in its own table
`myext_object_region (contentobject_id, region_id)`.

1. Add the limitation to `content/read`, so that the role editor offers it. A listener of `module/functionlist` gets
   the function list of every module with the module name and returns it, changed or not:

   ```php
   class myExtRegionLimitation implements ezpContentLimitationHandler
   {
       public static function functionList( $functionList, $moduleName )
       {
           if ( $moduleName === 'content' && isset( $functionList['read'] ) )
           {
               $functionList['read']['Region'] = array(
                   'name' => 'Region',
                   'label' => ezpI18n::tr( 'extension/myext', 'Sales region' ),
                   'values' => array( array( 'Name' => ezpI18n::tr( 'extension/myext', 'North' ), 'value' => '1' ),
                                      array( 'Name' => ezpI18n::tr( 'extension/myext', 'South' ), 'value' => '2' ) ) );
           }
           return $functionList;
       }
   ```

   The entry has the form of the limitations in `kernel/content/module.php`: a `name` of letters, digits and `_`,
   fixed `values`, or `class`, `function` and `parameter` of a method that lists them. `label` (optional, new) is
   what the role screens show instead of the name; translate it, and the value names, in the listener, which runs
   in the language of the request. An entry of another form is logged and left out; a listener that throws or
   returns no array is logged and the module keeps its own list. The listeners run once per module and request,
   however often the module is looked up.

2. Decide in PHP. `$subject` is an `eZContentObject`, an `eZContentObjectTreeNode` or an `eZContentObjectVersion`;
   `$userID` is the user the check is for, which is not always the current user. Only `true` allows:

   ```php
       public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
       {
           $objectID = $subject instanceof eZContentObject
               ? $subject->attribute( 'id' )
               : $subject->attribute( 'contentobject_id' );
           $rows = eZDB::instance()->arrayQuery(
               'SELECT region_id FROM myext_object_region WHERE contentobject_id = ' . (int)$objectID );
           return count( array_intersect( array_column( $rows, 'region_id' ), $values ) ) > 0;
       }
   ```

3. Give the same rule as SQL for fetches. The condition is joined by AND to the other limitations of the policy. It
   may use the table `ezcontentobject` and the node table under the alias `$tableAliasName`. Cast or escape every
   value; return `false` when the rule cannot be written as SQL, and the policy then gives no access in fetches:

   ```php
       public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
       {
           $regions = implode( ', ', array_map( 'intval', $values ) );
           return "ezcontentobject.id IN ( SELECT contentobject_id FROM myext_object_region WHERE region_id IN ( $regions ) )";
       }
   }
   ```

   Where the rule is "this column has one of these values", return the column and the values instead, and the
   kernel writes the `IN` statement with each value cast to an integer or escaped:

   ```php
   return array( 'column' => 'ezcontentobject.section_id', 'values' => $values, 'type' => 'int' );
   // 'type' => 'string' escapes instead; 'not' => true writes NOT IN; a list of such arrays is joined by AND
   ```

4. Register both in `extension/myext/settings/site.ini.append.php`:

   ```ini
   [Event]
   Listeners[]=module/functionlist@myExtRegionLimitation::functionList

   [RoleSettings]
   LimitationHandlers[Region]=myExtRegionLimitation
   ```

5. Regenerate the autoloads and clear the caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

6. In **Users > Roles and policies**, edit a role, add a policy for `content/read` and choose **Region** next to Class
   and Section.

The handler must give the same answer in `checkAccess()` and in `permissionSQL()`. Otherwise a list shows an object
whose page is then refused, or hides one the user may open.

### The contract of a handler

| Rule | Why |
|---|---|
| `$values` is a list of strings, as the policy stores them. | The role cache and the database hand them over as strings. |
| Only `true` from `checkAccess()` allows; `1`, `'yes'` and `null` refuse. | One reading of the answer in every check. |
| A string from `permissionSQL()` is self-contained: quotes and parentheses balanced, and outside quoted strings no `;` and no comment (`--`, `#`, `/*`). Anything else gives the policy no access in fetches and is logged. | It is put in parentheses and joined by AND; a condition that closed them (`1 = 1 ) OR ( 1 = 1`) would open a policy of its own for everybody. The kernel cannot check that values were escaped: cast them, escape them, or use the column form. |
| An exception denies and is logged once per request; it does not end the request. | A handler that fails must never allow, nor take a list page down. |
| One instance per request, kept for the rest of it. Keep what you looked up in properties of the instance if you like; never in static properties. | Velocity runs the next request in the same process; the kernel makes the handler again for it. |
| Take the user from `$userID`, never from the session or `eZUser::currentUser()`. | Checks are made for other users (subtree notifications, `exp:access:check`). |

The settings extension wizard in **Setup > RAD** writes a handler that keeps all of these, as a sample: choose the
event `module/functionlist`. It adds `<class>MaxDepth` (read only down to a depth of the tree) with the depths of
`site.ini [<class>] MaxDepths[]`, and the commented `[RoleSettings] LimitationHandlers[]` line that switches it on.

## In the role screens

- The role view, the role editor, the policy lists of the node and user views and the policy forms show the
  `label` of a limitation where it has one, and the note **(no handler, denies)** next to a content limitation that
  no handler evaluates, with a tooltip saying that its policy gives no access.
- A limitation whose extension is no longer active is shown with its stored values; before, it was shown empty.
- The editors store only values the form offered for a limitation (its `values`, or what its `class` and `function`
  list). A value made up in the request is left out and logged; a single value posted instead of a list no longer
  ends the request. When nothing that was offered is left, no limitation is stored, as with **Any**: whoever edits
  roles can choose Any anyway.

## Checking access for another user

The three `checkAccess()` methods take the user as a sixth argument, after the language:

```php
$node->checkAccess( 'read', false, false, false, false, $subscriberID );
$object->checkAccess( 'edit', false, false, false, false, $userID );
$version->checkAccess( 'versionread', false, false, false, false, $userID );
```

`false` (the default) checks for the current user, as before. The check reads the roles of that user and passes the
user to the handlers. An ID that is no user gets no access. The rule "anonymous users may edit what they created in
this session" (`Owner` limitation value 2) holds only for the current user, since only the current user has a session
to look at. `eZUser::accessUser( $userID )` returns the user a check is made for:

| `$userID` | User |
|---|---|
| `false`, `0`, `null`, `''` | the current user |
| the current user's ID | the current user's own object |
| another user's ID | that user, fetched once per request and kept for the rest of it; forgotten for the next request (Velocity), by `eZUser::cleanupCache()` and when that user's cache is purged |
| a disabled account, an ID that is no user, anything but a positive whole number (`'1 OR 1'`, `true`, an array) | none: no access |

It is a PHP interface only; nothing a visitor sends reaches it. The other user is a separate `eZUser` with roles of
its own: the current user's roles, session and the answers cached on objects (`can_read`, `can_edit`) take no part,
and the check keeps nothing on the object.

Edit access goes through the filter `content/edit/access` as well, so it has its own method:

```php
$object->editAccess( $version, $language, $userID );   // the filter gets $userID
```

For another user it uses that user's `content/edit` policies and its `user/selfedit` for its own user object.

From the command line, `exp:access:check` answers for a user and names what refused:

```bash
./console exp:access:check --user=editor --node=2
./console exp:access:check --user=14 --object=57 --function=edit --language=eng-GB
./console exp:access:check --user=anonymous --node=2 --json
```

```
User      14 (editor)
Subject   node 2, object 1 "Home"
Function  content/read
  refused: policy 342, Class( 2 )
  refused: policy 343, Region( 4 ), no handler evaluates it
DENIED
```

It prints `ALLOWED` (exit 0) or `DENIED` (exit 1), exit 2 when the user, node or object is not found. It is a
command rather than a "view as this user" page in the admin: whoever can run it can read the database anyway, and no
session of the other user is made. `expContentAccessReport::check()` gives the same answer to PHP code.

## What the kernel does without a handler

| Situation | Result |
|---|---|
| A limitation the kernel does not know, without `LimitationHandlers[]` entry | The policy gives no access: `checkAccess()` refuses, the fetch condition is `1 = 0`, the subtree notification is not sent through it. |
| The class does not exist, does not implement `ezpContentLimitationHandler`, or its constructor throws | As without a handler; an error in the debug output names the class and the reason, once per request. |
| A handler registered for a kernel limitation (`Section`, `Subtree`, `StateGroup_<identifier>`, ...) | Ignored: the kernel evaluates its own limitations. |
| A kernel limitation of another function in a fetch (`Language`, `ParentClass`) | Left out of the SQL, as before. |
| An exception thrown by `checkAccess()` or `permissionSQL()` | Denies (`false`, `1 = 0`) and is logged once per request; the request goes on. |
| `permissionSQL()` answers with something other than `false`, a self-contained string or a column condition | The policy gives no access in fetches; logged once per request with the start of the answer. |
| `solrFilter()` without a handler, for a handler without `ezpContentLimitationSolrHandler`, for a kernel limitation, a handler that returns `false`, throws or answers with no filter | `DENY_SOLR` (`( *:* -*:* )`): the policy gives no access in searches; logged once per request (a missing interface as a warning). |

## What changes for existing installations

- A role with a limitation that the kernel does not know and that no extension handles (left over from an old patch
  or a removed extension) used to narrow `checkAccess()` but not fetches: lists showed the objects, the pages refused
  them. Now lists leave them out as well. Look for such limitations in **Users > Roles and policies**, or in the table
  `ezpolicy_limitation`, and remove them or install the extension that handles them.
- `eZContentObjectVersion::checkAccess()` stops at the first limitation of a policy that denies. Before, a `Language`
  limitation that denied, or a limitation it did not know, could be allowed again by the next limitation of the same
  policy.
- Subtree notifications check the `Group` limitation ("Self group") of `content/read`; they ignored it and sent the
  mail to everyone the rest of the policy allowed.
- A class that overrides `checkAccess()` of `eZContentObject`, `eZContentObjectTreeNode` or `eZContentObjectVersion`
  must accept the sixth argument `$userID = false`. PHP 8 refuses a method with fewer parameters than the one it
  overrides, with a fatal error when the class is loaded.

## Searches

A search engine that filters by the policies of the user itself, such as eZ Find with Solr, does not run the SQL of
the fetches. A handler that implements `ezpContentLimitationSolrHandler` as well supplies the filter for it:

```php
class myExtLimitationHandler implements ezpContentLimitationSolrHandler
{
    // checkAccess() and permissionSQL() as above

    public function solrFilter( $limitation, array $values, $userID )
    {
        // the safer form: the kernel writes "meta_section_id_si:(1 OR 2)" and escapes the values
        return array( 'field' => eZSolr::getMetaFieldName( 'section_id' ), 'values' => $values );
    }
}
```

The search extension calls `ezpContentLimitation::solrFilter( $limitation, $values, $userID )` for every
limitation that is not a kernel limitation (`ezpContentLimitation::isKernelLimitation()`; those it translates
itself) and joins the answer with AND to the other limitations of the policy. The answer is always a filter in
parentheses. When the policy gives no access in the search, it is `ezpContentLimitation::DENY_SOLR`, a filter that
matches no document: without a handler, for a handler that does not implement the interface, when it returns false
or throws, for a kernel limitation, and for an answer that is no filter:

- A string must be self-contained: double quotes closed, parentheses balanced and never closed before they open,
  range brackets (`[ ]`, `{ }`) closed and not nested, no local parameters (`{!`) and no nested query (`_query_`),
  not even in quotes, no NUL byte. Values in it are escaped with `ezpContentLimitation::solrValue()`, which also
  turns a value `AND`, `OR` or `NOT` into a term. A filter that only excludes (`-field:x`) matches nothing inside its
  parentheses: write `*:* -field:x`, or use the array form with `'not' => true`.
- An array `array( 'field' => ..., 'values' => ..., 'not' => false )`, or a list of them joined by AND: the field
  is a name of letters, digits and `_`, the values are scalars in UTF-8 and not empty, `not` is a boolean if it is
  given. No values matches nothing (with `'not' => true`: everything).

The search extension must not leave the policy out instead of joining `DENY_SOLR`: eZ Find filters by nothing at
all when no policy is left, so a user whose only policy has such a limitation would find everything.

## Limits

- `[Event] Listeners[]` are attached to web requests. A command or cronjob part that reads the function list of a
  module (rare: the role editor and the policy view do) sees it without the added limitation. Access checks are not
  affected, because they read the policies, not the function list.
- The view cache keeps one copy per set of roles and limitations of a user. A handler whose answer depends on data
  that is not in the role (a matrix stored per user) needs view cache keys of its own, or a view cache that is off
  for the classes it guards.
- A search engine that builds its own permission filter asks the handler only if the handler implements
  `ezpContentLimitationSolrHandler` and the search extension calls `ezpContentLimitation::solrFilter()` (see
  [Searches](#searches)). eZ Find as released leaves a limitation it does not know out of the policy: until it calls
  `solrFilter()`, a search shows a user with such a policy more than the policy allows. Disable the search for such
  users, or keep the content guarded by the limitation out of the index, until the search extension is changed.
- The `state/assign` policy (`eZContentObject::allowedAssignStateIDList()`) does not ask handlers yet.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `RoleSettings` | `LimitationHandlers[<limitation>]` | empty | extension |
| `settings/site.ini` | `Event` | `Listeners[]=module/functionlist@<callback>` | none | extension |

## How it works

- `ezpContentLimitation` (`kernel/private/classes/ezpcontentlimitation.php`) makes the handler once per request
  (the request is told by `REQUEST_TIME_FLOAT`, the setting by a hash of `LimitationHandlers[]`; `resetCache()`
  forgets both) and returns `false`, `1 = 0` or `DENY_SOLR` without one. `sqlCondition()` and `solrCondition()`
  check or write the condition of a handler; `isKernelLimitation()` names the limitations the kernel keeps for
  itself. The exit signal of Velocity passes through its `catch`.
- The interfaces are `ezpContentLimitationHandler` (`kernel/private/interfaces/ezpcontentlimitationhandler.php`) and
  `ezpContentLimitationSolrHandler` (`kernel/private/interfaces/ezpcontentlimitationsolrhandler.php`).
- `eZModule::initialize()` (`lib/ezutils/classes/ezmodule.php`) passes the `$FunctionList` of every module through the
  filter `module/functionlist` (`eZModule::filterFunctionList()`): only when a listener is attached, once per module
  file, request and set of listeners (`ezpEvent::listenerIds()`), keeping what has the form of `module.php`.
- `eZPolicyLimitation` gives the role screens `label`, `denies_without_handler` and `validValues()`.
- `eZUser::accessUser()` and `expContentAccessReport` (`kernel/private/classes/expcontentaccessreport.php`) make the
  checks for another user; `exp:access:check` is `kernel/private/classes/commands/accesscheck.php`.
- The kernel's own Class, Section, Node and object state values are cast to integers in the fetch SQL.
- The REST interface (`expRestContentPermission`) and the remote services (`expservices`) decide with the same
  `checkAccess()`, `canRead()` and `editAccess()`, so extension limitations hold there too.
- The extension point is listed in **Setup > RAD** and in [Extension points](../../bc/6.0/rad-extension-points.md).

Tests: `ezpContentLimitationTest`, `eZContentPermissionSQLTest`, `eZContentAccessForAnotherUserTest`,
`eZPolicyLimitationLabelsAndValuesTest` and `expRadWizardListsTest` (no database), `eZContentAccessForUserLiveTest`
(on an installation; skipped without one).

## Related pages

- [Extension points](../../bc/6.0/rad-extension-points.md)
- [Extensions guide](../../guides/extensions.md)
- [Edit access to objects that were never published](../../bc/6.0/draft-edit-access.md)
- [Behaviour changes of 1 and 2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
