# Access and view cache filters for extensions

Read this page if you build an extension that changes who may download a file, edit a draft or open a collaboration
item, that decides which publications become notifications, or that needs view cache files of its own. Each of these
used to be a patch of a kernel file; now the kernel asks a filter of `ezpEvent`, and the extension answers.

## In short

| Filter | Value the listener gets | Context | Asked by |
|---|---|---|---|
| `content/view/cachekeys` | the named keys of a view cache file name | user, node, view mode, language, offset, layout, view parameters, cache tweak | `eZNodeviewfunctions::generateViewCacheFile()` |
| `content/download/access` | the kernel's answer, `true` or `false` | object, attribute, version | `content/download` |
| `content/edit/access` | the kernel's edit answer, `true` or `false` | object, version (or `null`), user ID, language | `eZContentObject::editAccess()` and `::filterEditAccess()`, see [Where edit access is asked](#where-edit-access-is-asked) |
| `collaboration/item/access` | whether the user takes part, `true` or `false` | item, user | `collaboration/item` and `collaboration/action` |
| `content/notification/create` | `true`, `false` for "Publish without notification" | object ID, version | `eZContentOperationCollection::createNotificationEvent()` (the publish operation) |

A listener returns the value, changed or not. Without listeners everything works as before.

## What a listener of an access filter can do

The four access filters (`content/download/access`, `content/edit/access`, `collaboration/item/access`,
`content/notification/create`) work the same way:

- The listener gets the kernel's answer as its first argument and returns the answer that counts.
- **Only `true` allows.** `false`, `null`, `1`, `'yes'` or an object all refuse. A listener that forgets its
  `return` therefore refuses everybody: hand the first argument back when the listener has no opinion.
- A listener can **narrow** (return `false` where the kernel allowed) and **widen** (return `true` where the kernel
  refused). Widening is the purpose of these filters (an approver who may read only the version gets its file; a
  further editor of a draft gets in), so the kernel does not forbid it. Write a widening listener so it returns
  `true` only for the case it is about, and the first argument otherwise.
- Several listeners run in the order they are registered, each getting what the one before returned. A listener
  that widens after one that narrowed undoes the narrowing. If both exist, register the narrowing one last, or let
  the widening one look at the case itself instead of overriding every `false`.
- Some checks come before the filter and no listener can change them:
  - `content/download`: the attribute must belong to the object, and to the version asked for. For a file of
    another object or version no listener is asked at all.
  - `collaboration/action`: the item must exist and be of the type the form names.
  - The edit view still sends someone who did not create a draft to `content/history`, where they can make a draft
    of their own; a listener cannot let someone edit another user's draft in place.
  - Approving or denying an item still needs the approver role; the filter only decides who may open an item and
    comment on it.
- When a listener changes the kernel's answer, a debug notice says so (`A listener of content/edit/access allowed
  editing object 57 for user 14 (the kernel refused it)`), so a surprising answer can be traced in the debug output.

## Register a listener

In `extension/<name>/settings/site.ini.append.php`:

```ini
[Event]
Listeners[]=content/edit/access@myExtCoEditors::editAccess
Listeners[]=content/notification/create@myExtNotificationFilter::create
```

`[Event] Listeners[]` are attached to web requests. A publication that a cronjob part finishes (a workflow that
waited for an approval, the asynchronous publisher) asks `content/notification/create` on the command line. Add the
listener to `[RunnableSettings] Listeners[]` as well; that list is attached to cronjob parts with the extension
settings. On a web request a listener in both lists runs twice, so write it to give the same answer on the second
call. Velocity's persistent workers attach the listeners again on each request and replace the set attached before,
so they do not pile up.

The settings extension wizard (**Setup > RAD**, settings extension, part "Event listeners") writes the listener class
for you: for each of these filters the method names the arguments it is handed, and carries a commented example that
reads its rule from the extension's own `site.ini [<listener class>]` group, which the wizard writes too.

## The filters

### `content/view/cachekeys`

The view cache keeps one file per set of keys. The keys are named (the file names did not change with that, because
the hash reads the values only):

| Key | Present |
|---|---|
| `node_id`, `viewmode`, `language`, `offset`, `layout`, `access_path` | always |
| `userroles`, `userlimitedlist`, `discountlist` | unless the cache tweak says `ignore_userroles`, ... |
| `siteaccess_type` | for a siteaccess matched by URI |
| `protocol` | with the tweak `protocol` |
| `viewparameters`, `userpreferences` | when the view has them |

The second argument is an array with `user`, `node_id`, `view_mode`, `language`, `offset`, `layout`,
`view_parameters` and `view_cache_tweak`. Add a key when the page depends on something the roles do not show (a
rights matrix stored per user); leave keys out for a class whose pages are the same for everyone.

- `node_id`, `viewmode`, `language`, `offset` and `layout` always stay: a listener that leaves one out gets it back
  in its place, with a warning, since without them two views of a node would share one file.
- Values must be strings, numbers, booleans or `null`. An array or an object is left out with a warning.
- A listener that returns no array changes nothing.

```php
class myExtViewCacheKeys
{
    public static function keys( $keys, $context )
    {
        $keys['myext_matrix'] = myExtMatrix::keyFor( $context['user'] );
        return $keys;
    }
}
```

A new key makes new file names, so after a listener is added or changed the old files are simply no longer read;
clear the view cache to free the space (`php bin/php/ezcache.php --clear-tag=content`).

### `content/download/access`

`content/download` first checks that the attribute belongs to the object and to the version asked for
(`Download::attributeBelongs()`); no listener is asked otherwise. Then the kernel decides: the user must read the
object and one of its locations (or an object relating to it, for an object without location), and read the version
when it is not the published one. The filter gets that answer with the object, the attribute and the version number.

A listener that lets an approver download the file of a version they may read:

```php
public static function download( $allowed, $object, $attribute, $version )
{
    if ( $allowed )
        return true;
    $versionObject = $object->version( $version );
    return $versionObject instanceof eZContentObjectVersion && $versionObject->canVersionRead();
}
```

A listener that keeps the files of some classes in, whatever the roles say, with the classes in its settings:

```php
public static function download( $allowed, $object, $attribute, $version )
{
    $ini = eZINI::instance();
    if ( $allowed && $ini->hasVariable( 'MyExtDownloads', 'NoDownloadClasses' ) &&
         in_array( $object->attribute( 'class_identifier' ), $ini->variable( 'MyExtDownloads', 'NoDownloadClasses' ) ) )
        return false;
    return $allowed;
}
```

A refusal answers with the kernel's error (access denied, or not available for a location or version the user may
not read); a refusal by a listener where the kernel allowed answers access denied.

### `content/edit/access`

`eZContentObject::editAccess( $version = null, $language = false )` starts from `canEdit()` and passes the answer
through the filter with the object, the version (`null` when there is none yet), the ID of the current user and the
language (an empty language counts as none). `eZContentObject::filterEditAccess( $allowed, $version, $language )`
passes any other edit answer of the kernel through the same filter; it is how the edit check of one version and the
REST check are decided alike. Without listeners neither asks for the current user.

A listener for further editors of a draft:

```php
public static function editAccess( $allowed, $object, $version, $userID, $language )
{
    return $allowed || ( $version && myExtCoEditors::isEditor( $version, $userID ) );
}
```

A listener that freezes objects in a state (an object state `locked`), whatever the roles say:

```php
public static function editAccess( $allowed, $object, $version, $userID, $language )
{
    if ( $allowed && in_array( myExtStates::lockedStateID(), $object->stateIDArray() ) )
        return false;
    return $allowed;
}
```

#### Where edit access is asked

| Where | What |
|---|---|
| `content/edit` | every edit check: a new draft, the choice of a language, the choice between drafts, a new draft in the language, the final check, and the edit check of one version (`filterEditAccess()`) |
| `content/history` | whether the history offers editing, and the edit of a version |
| `content/removeeditversion` | removing a draft (an object never published also allows someone who may create it there) |
| `content/versionview` | the Edit and Publish buttons of the preview (they also need the version's creator) |
| `content/multiedit` | each object, and a draft it reuses |
| REST interface | `expRestContentPermission::editAllowed()`: the node's `canEdit()` and the language, through `filterEditAccess()` |
| Online editor | the upload and tag dialogs of `ezoe` |

Not asked: checks of a location rather than of the content (sorting, priorities, moving, swapping, adding or
removing locations), the translation settings of an object (`UpdateInitialLanguage`, `UpdateAlwaysAvailable`), user
accounts (`user/edit`, `user/selfedit`), and the template attribute `$object.can_edit`, which shows links and
buttons. A listener that widens edit access therefore gets the edit itself working, but the Edit button of a
template that reads `can_edit` stays hidden; give such users a link of your own, for example from the collaboration
item.

### `collaboration/item/access`

`collaboration/item` opens an item for its participants, and `collaboration/action` acts on it (a comment, approve,
deny) only for someone who may open it. The filter gets whether the current user takes part, with the item and the
user; a listener can let a supervisor of an approval in or keep a participant out. The template gets
`is_participant`. Only a participant can file the item in a group of the inbox. Approving or denying still needs the
approver role, decided by the item's collaboration handler.

```php
public static function itemAccess( $allowed, $item, $user )
{
    $ini = eZINI::instance();
    if ( !$allowed && $ini->hasVariable( 'MyExtCollaboration', 'SupervisorUserIDs' ) &&
         in_array( (string)$user->attribute( 'contentobject_id' ), $ini->variable( 'MyExtCollaboration', 'SupervisorUserIDs' ), true ) )
        return true;
    return $allowed;
}
```

### `content/notification/create`

The publish operation creates the event that the notification handlers turn into mails. The filter gets `true` (`false` for
a publication without notification), the object ID and the version; anything but `true` leaves the event out. A listener that only lets some classes through:

```php
public static function create( $create, $objectID, $version )
{
    $object = eZContentObject::fetch( $objectID );
    return $create && $object && in_array( $object->attribute( 'class_identifier' ), array( 'article', 'file' ) );
}
```

#### Publish without notification

"Publish without notification" (`notification.ini [NotificationSettings] PublishWithoutNotification` and the policy
`content/publish_without_notification`) runs the publish operation with `notify` set to `false`, and the filter then
gets `false`. A listener that returns `true` regardless makes the event anyway and overrides the editor: pass on
`$create` when you do not mean to. The whole feature: [Publish without notification](publish-without-notification.md).

## How it works

All five are `ezpEvent::filter()` calls. `ezpEvent::hasListeners( $name )` says whether anybody listens, for a caller
whose context is costly to gather. The download and collaboration checks are in static methods, so they can be
called and tested on their own: `\Exponential\View\Kernel\Content\Download::access()`, `::kernelAccess()` and
`::attributeBelongs()`, `\Exponential\View\Kernel\Collaboration\Item::access()` and
`\Exponential\View\Kernel\Collaboration\Action::access()`.

There is no filter for read access. `canRead()` is only one of the places that decide what a user sees: list
fetches, search and the tree menu filter in SQL by the role limitations, and the view cache is keyed by the roles.
A read filter would make a node readable on its own page and missing from every list. Use roles and
`content/view/cachekeys` instead.

Tests (no database): `expPermissionHooksTest` (the five filters, the edit checks of content/edit and multiedit, the
checks before the filters, the structural view cache keys), `expRestContentPermissionTest` (CP-07: the REST edit
check), `expRadWizardListsTest` (the generated listeners).

## Related pages

- [Edit access to objects that were never published](../../bc/6.0/draft-edit-access.md)
- [Extension points](../../bc/6.0/rad-extension-points.md)
- [Extensions guide](../../guides/extensions.md)
