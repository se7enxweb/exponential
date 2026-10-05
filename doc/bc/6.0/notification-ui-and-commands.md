# Notifications: changed behaviour

- **`/notification/addtonotification/<node>` no longer subscribes on a plain GET.** It shows a confirmation; the subscription is made by
  a POST with the form token. Designs that bring their own `addingresult.tpl` keep it; the confirmation pages come from
  `notification/addconfirm.tpl` and `removeresult.tpl` (standard and admin provide them, other designs fall back to standard).
- `eZNotificationEventFilter::process()` takes an optional list of event ids and returns a result array (it returned nothing). A
  handler that throws no longer stops the pass.
- Handled events nothing waits for are removed at the end of a pass (they stayed for ever once their digest was sent).
- `eZNotificationCollection::addItem()` now sets the send date it is given; `eZNotificationCollectionItem::fetchByDate()` finds the
  items that are due (its condition overwrote itself).
- The general digest settings are validated on save, and a user without a settings row can save them. The collaboration notification
  rules of the settings page are looked up by user id (they were looked up by e-mail address).
- `eZFileTransport` writes to `[MailSettings] FileTransportDirectory` (default `var/log/mail`, as before).
- The settings page is a new overview in admin4 and admin4l; the form fields of the handler templates keep their names.
- The cronjob part notification runs through `expNotificationService` (lock, record, status). New: cronjob part `notification`,
  views `status` and `job`, commands `exp:notification:status|run|events|subscriptions`.
