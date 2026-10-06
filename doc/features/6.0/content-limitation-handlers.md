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
| Also new | `checkAccess()` of `eZContentObject`, `eZContentObjectTreeNode` and `eZContentObjectVersion` takes the user to check for as a sixth argument. |
| Safety rule | A limitation that no handler evaluates denies, everywhere. Fetches ignored it before and listed objects that `checkAccess()` refused. |

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
                   'values' => array( array( 'Name' => 'North', 'value' => '1' ),
                                      array( 'Name' => 'South', 'value' => '2' ) ) );
           }
           return $functionList;
       }
   ```

   The entry has the form of the limitations in `kernel/content/module.php`: fixed `values`, or `class`, `function`
   and `parameter` of a method that lists them.

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
to look at. `eZUser::accessUser( $userID )` returns the user a check is made for.

## What the kernel does without a handler

| Situation | Result |
|---|---|
| A limitation the kernel does not know, without `LimitationHandlers[]` entry | The policy gives no access: `checkAccess()` refuses, the fetch condition is `1 = 0`, the subtree notification is not sent through it. |
| The class does not exist | As without a handler; an error in the debug output names the class. |
| The class does not implement `ezpContentLimitationHandler` | As without a handler; an error in the debug output says so. |
| A handler registered for a kernel limitation (`Section`, `Subtree`, `StateGroup_<identifier>`, ...) | Ignored: the kernel evaluates its own limitations. |
| A kernel limitation of another function in a fetch (`Language`, `ParentClass`) | Left out of the SQL, as before. |
| An exception thrown by a handler | It is not caught: the request ends with the error rather than allowing access. |

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

## Limits

- `[Event] Listeners[]` are attached to web requests. A command or cronjob part that reads the function list of a
  module (rare: the role editor and the policy view do) sees it without the added limitation. Access checks are not
  affected, because they read the policies, not the function list.
- The view cache keeps one copy per set of roles and limitations of a user. A handler whose answer depends on data
  that is not in the role (a matrix stored per user) needs view cache keys of its own, or a view cache that is off
  for the classes it guards.
- Search engines that build their own permission filter (eZ Find) do not ask the handler. Check with the search
  extension how it treats limitations it does not know.
- The `state/assign` policy (`eZContentObject::allowedAssignStateIDList()`) does not ask handlers yet.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `RoleSettings` | `LimitationHandlers[<limitation>]` | empty | extension |
| `settings/site.ini` | `Event` | `Listeners[]=module/functionlist@<callback>` | none | extension |

## How it works

- `ezpContentLimitation` (`kernel/private/classes/ezpcontentlimitation.php`) resolves the handler through
  `eZExtension::getHandlerClass()` and returns `false` or `1 = 0` without one. `ezpContentLimitation::isKernelLimitation()`
  names the limitations the kernel keeps for itself.
- The interface is `ezpContentLimitationHandler` (`kernel/private/interfaces/ezpcontentlimitationhandler.php`).
- `eZModule::initialize()` (`lib/ezutils/classes/ezmodule.php`) passes the `$FunctionList` of every module through the
  filter `module/functionlist`.
- The extension point is listed in **Setup > RAD** and in [Extension points](../../bc/6.0/rad-extension-points.md).

Tests: `ezpContentLimitationTest` and `eZContentPermissionSQLTest` (no database), `eZContentAccessForUserLiveTest`
(on an installation; skipped without one).

## Related pages

- [Extension points](../../bc/6.0/rad-extension-points.md)
- [Extensions guide](../../guides/extensions.md)
- [Edit access to objects that were never published](../../bc/6.0/draft-edit-access.md)
- [Behaviour changes of 1 and 2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
