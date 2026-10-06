# Access and view cache filters for extensions

Read this page if you build an extension that changes who may download a file, edit a draft or open a collaboration
item, that decides which publications become notifications, or that needs view cache files of its own. Each of these
used to be a patch of a kernel file; now the kernel asks a filter of `ezpEvent`, and the extension answers.

## In short

| Filter | Value the listener gets | Context | Asked by |
|---|---|---|---|
| `content/view/cachekeys` | the named keys of a view cache file name | user, node, view mode, language, offset, layout, view parameters, cache tweak | `eZNodeviewfunctions::generateViewCacheFile()` |
| `content/download/access` | the kernel's answer, `true` or `false` | object, attribute, version | `content/download` |
| `content/edit/access` | `canEdit()`, `true` or `false` | object, version (or `null`), user ID, language | `eZContentObject::editAccess()`: `content/edit`, `content/history`, `content/removeeditversion`, `content/versionview`, `ezoe/upload`, `ezoe/tags` |
| `collaboration/item/access` | whether the user takes part, `true` or `false` | item, user | `collaboration/item` |
| `content/notification/create` | `true` | object ID, version | `eZContentOperationCollection::createNotificationEvent()` (the publish operation) |

A listener returns the value, changed or not. For the four access filters only `true` allows: a listener that returns
nothing, `1` or `'yes'` refuses. Without listeners everything works as before.

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
call.

## The filters

### `content/view/cachekeys`

The view cache keeps one file per set of keys. The keys are named now (the file names do not change, because the
hash reads the values only):

| Key | Present |
|---|---|
| `node_id`, `viewmode`, `language`, `offset`, `layout`, `access_path` | always |
| `userroles`, `userlimitedlist`, `discountlist` | unless the cache tweak says `ignore_userroles`, ... |
| `siteaccess_type` | for a siteaccess matched by URI |
| `protocol` | with the tweak `protocol` |
| `viewparameters`, `userpreferences` | when the view has them |

The second argument is an array with `user`, `node_id`, `view_mode`, `language`, `offset`, `layout`,
`view_parameters` and `view_cache_tweak`. Add a key when the page depends on something the roles do not show (a
rights matrix stored per user); leave keys out
for a class whose pages are the same for everyone. Values must be strings or numbers. A listener that returns no
array changes nothing.

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

### `content/download/access`

`content/download` first checks that the attribute belongs to the object; no listener can change that. Then the
kernel decides: the user must read the object and one of its locations, and read the version when it is not the
published one. The filter gets that answer with the object, the attribute and the version number. A listener that
lets an approver download the file of a version they may read:

```php
public static function download( $allowed, $object, $attribute, $version )
{
    if ( $allowed )
        return true;
    $versionObject = $object->version( $version );
    return $versionObject instanceof eZContentObjectVersion && $versionObject->canVersionRead();
}
```

A refusal answers with the kernel's error (access denied, or not available for a location or version the user may
not read).

### `content/edit/access`

`eZContentObject::editAccess( $version = null, $language = false )` is the edit check of the views listed above. It
starts from `canEdit()` and passes the answer through the filter with the object, the version (`null` when there is
none yet), the ID of the current user and the language. A listener for further editors of a draft:

```php
public static function editAccess( $allowed, $object, $version, $userID, $language )
{
    return $allowed || ( $version && myExtCoEditors::isEditor( $version, $userID ) );
}
```

The template attribute `$object.can_edit` is unchanged and does not ask the filter. The edit view still sends
someone who did not create a draft to `content/history`, where they can make a draft of their own.

### `collaboration/item/access`

`collaboration/item` opens an item for its participants. The filter gets whether the current user takes part, with
the item and the user; a listener can let a supervisor of an approval in. The template gets `is_participant`. Only
a participant can file the item in a group of the inbox. The actions on an item (approve, reject) are decided by its
collaboration handler, as before.

### `content/notification/create`

The publish operation creates the event that the notification handlers turn into mails. The filter gets `true`,
the object ID and the version; anything else leaves the event out. A listener that only lets some classes through:

```php
public static function create( $create, $objectID, $version )
{
    $object = eZContentObject::fetch( $objectID );
    return $create && $object && in_array( $object->attribute( 'class_identifier' ), array( 'article', 'file' ) );
}
```

## How it works

All five are `ezpEvent::filter()` calls. The download and collaboration checks are in static methods, so they can be
called and tested on their own: `\Exponential\View\Kernel\Content\Download::access()` and `::kernelAccess()`,
`\Exponential\View\Kernel\Collaboration\Item::access()`. The events are described in the settings extension wizard
(**Setup > RAD**).

Tests: `expPermissionHooksTest` (no database).

## Related pages

- [Extension points](../../bc/6.0/rad-extension-points.md)
- [Extensions guide](../../guides/extensions.md)
