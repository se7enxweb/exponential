# Publish without notification

An editor who corrects a typo or the metadata of a published article does not want every subscriber of its folder to
get a mail for it. Where it is switched on, the edit form and the version preview offer **Publish without
notification** next to the publish button: the version is published as with the other button, but no notification
event is made for it, so the subscriptions of its locations send nothing.

## Switching it on

Two things are needed, the setting and the policy:

1. `notification.ini [NotificationSettings] PublishWithoutNotification=enabled` (`disabled` by default). Set it in
   `settings/override/notification.ini.append.php` for every siteaccess, or in
   `settings/siteaccess/<admin>/notification.ini.append.php` to offer it in the administration interface only.
2. The policy function **content / publish_without_notification** in the roles of the editors who may use it. It has
   no limitations of its own. A `content/*` policy includes it, so the Administrator role has it without a change; a
   role that lists its content functions one by one gets it only when it is added in the role editor.

| Setting | Policy | The button | A posted `PublishNotNotifyButton` |
|---|---|---|---|
| `disabled` | any | hidden | publishes **with** notification |
| `enabled` | none | hidden | publishes **with** notification (and logs a warning) |
| `enabled` | granted (also through a limited `content/*` policy) | shown | publishes **without** notification |

The button only changes what happens after publishing. Whether the user may publish the draft at all is decided as
before: by `content/edit` for the edit form, and for the version preview by the edit access and being the draft's
creator. A posted button is never a way around those checks; with the setting off, or without the policy, it is the
ordinary publish. The button is part of the same form as the publish button, so with the extension `ezformtoken`
active (as the installer sets it up) it is protected by the same form token.

Where it is: `content/edit` (both button bars) and `content/versionview` in the `admin`, `admin3`, `admin4` and
`standard` designs, and the conflict page (`content/edit_conflict.tpl` in `admin`, `admin4` and `standard`), which
offers it again when it was pressed before the conflict was found. Designs of extensions (the website interface,
`exp_adminui`, site themes) keep their own templates and show the button only once they add it, with the condition

```
{if and( ezini( 'NotificationSettings', 'PublishWithoutNotification', 'notification.ini' )|eq( 'enabled' ),
         fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish_without_notification' ) ) )}
<input class="button" type="submit" name="PublishNotNotifyButton" value="{'Publish without notification'|i18n( 'design/admin/content/edit' )}" />
{/if}
```

(`PreviewPublishNotNotifyButton` in the version preview).

## Notifications for some classes only

A site that sends notifications for its articles and files but not for every folder, image or banner can name the
classes whose publications make a notification event:

```ini
# settings/override/notification.ini.append.php
[NotificationSettings]
NotificationFilterByClassIdentifier=enabled
IncludeClasses[]
IncludeClasses[]=article
IncludeClasses[]=file
```

The list takes class identifiers; an entry made of digits is taken as a class ID and names its class
(`IncludeClasses[]=16`), so a list written with IDs does not quietly match nothing. Identifiers are compared exactly
(`Article` is not `article`).

With the setting enabled, a publication of any other class counts as published without notification: the filter
`content/notification/create` gets `false`, and the subscriptions of its locations send nothing. With an empty list
nothing makes an event. `disabled` (the default) notifies for every class. This is independent of the button: the
button leaves out one version of any class, the class filter leaves out every version of the classes not listed.
Approval (collaboration) notifications are not concerned. The class is looked up only when the filter is enabled and
lists classes (`eZContentOperationCollection::notificationIncludesClass()`).

## What is left out, and what is not

| Notification | Without notification |
|---|---|
| Subscriptions of the object's locations (`ezsubtree`), sent at once | not sent: no `ezpublish` event |
| Digest items of those subscriptions (`ezgeneraldigest`) | not collected: they come from the same event |
| Collaboration notifications of an approval (`ezcollaborationnotification`): the approver's request, the author's answer | **sent as before**: they are events of their own type, made by the approval workflow |
| Newsletters, RSS feeds, the search index, the static cache, view cache clearing | unchanged: they are not notifications |

So "without notification" means: nobody is told about this version through the notification subscriptions. Later
versions published the ordinary way notify again.

## How it works

The publish operation (`kernel/content/operation_definition.php`) has the optional parameter `notify`, `true` by
default. The views pass `false` when `eZContentOperationCollection::publishWithoutNotification( $buttonName )` says
so (the setting, the posted button, the policy). The parameter reaches:

- `create-notification`: `eZContentOperationCollection::createNotificationEvent( $objectID, $version, $notify )`
  gives it to the filter `content/notification/create` instead of `true`. The filter has the last word: a listener
  that returns `true` makes the event even for a publication without notification, so a listener should pass on what
  it gets rather than return `true` (see
  [Access and view cache filters](access-and-cache-filters.md#contentnotificationcreate)).
- `send-to-publishing-queue`: with `content.ini [PublishingSettings] AsynchronousPublishing=enabled`, a publication
  without notification is not queued but published at once. The asynchronous publisher runs the operation again with
  only the object and the version, so the parameter would be lost there.
- The memento of an interrupted operation: a workflow that holds the publication back (an approval) stores the
  operation's parameters, `notify` among them, and the workflow cronjob resumes the operation with them, so a version
  approved later still goes without notification.

Code that publishes through the operation passes the parameter the same way:

```php
eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $id, 'version' => $version, 'notify' => false ) );
```

`false`, `0` and `"0"` mean no; anything else, and a missing parameter, means yes. Every other caller of the
operation (uploads, imports, the REST interface, the content jobs) is unchanged and notifies.

`eZContentFunctions::createAndPublishObject( $params, $notify = true )` and
`eZContentFunctions::updateAndPublishObject( $object, $params, $notify = true )` take the parameter as well, for
imports and scripts that create or update content without telling the subscribers. Existing callers are unchanged:
without the argument both notify, as they did before it existed, so a script or cronjob has to ask for `false` to stay
silent. The value is read as the operation reads it (`eZContentOperationCollection::notifyRequested()`): `false`, `0`
and `"0"` mean no, anything else (`null` too) yes. `createAndPublishObject()` returns
the object as the publish operation left it (published, its current version and main node); before, it returned the
object it had instantiated, which still said draft.

Nothing is kept between requests: the answer comes from the setting, the request and the user each time, so the
persistent workers of Velocity give every request its own answer.

## Tests

- `eZPublishWithoutNotificationTest` (no database): the setting, the policy and the posted button
  (`publishWithoutNotification()`, `canPublishWithoutNotification()`), the operation definition, the filter getting
  `false`, the spellings of no, the asynchronous queue left out, the parameter passed on from a stored memento, the
  button mapping, the policy function and the templates, the class filter (`notificationIncludesClass()`), `notifyRequested()` and the
  `eZContentFunctions` defaults.
- `eZPublishWithoutNotificationLiveTest` (on an installation, skipped without one): `notify=false` publishes without
  event, the next version with one; a publication resumed from a stored memento keeps `notify=false`; the class
  filter, also with a class ID; `createAndPublishObject()` and `updateAndPublishObject()` with `notify=false`, `null`
  and `"0"`. The live tests set the file mail transport in-process and refuse to run without it.

## Related pages

- [Notifications: the user's guide](notifications.md)
- [Notifications: the administrator's guide](../../guides/notifications-administrator.md#8-publishing-without-notification)
- [Access and view cache filters](access-and-cache-filters.md)
