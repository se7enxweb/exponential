# Your devices and sessions: where an account is signed in

**For:** every user who signs in, and the administrators who look after their accounts. **Time:** 10 minutes.

Every sign-in is a session of its own: a browser on a computer, a phone, a tablet, a program that signs in through
WebDAV. The page **Your devices** lists them, and ends any of them with one click. This guide shows the page, the
e-mail that tells you about a sign-in from a new device, what an administrator can do for a user, and what to check
when something looks wrong. How it works inside: [Your devices: signed-in sessions per user](../features/6.0/user-sessions.md).

## In short

- **My account > Your devices** (`user/sessions`): every device signed in to your account, this one first.
- **Sign out this session** ends one device; **Sign out all other sessions** ends all but the one you use.
- A signed-out device sees the sign-in page at its next click, also when it stayed signed in with Remember me.
- An e-mail **New sign-in to your account** tells you when your account signs in from a device or browser it has not
  used lately. You turn it off on the same page.
- Administrators with the policy `user/sessions` open **Sessions** on a user's account page and sign the user out
  everywhere.

## 1. Opening the page

In the administration: the user menu at the top right, **My account**, then the card **Your devices**. On the public
site: **My profile**, then **Manage devices**. The address is `/user/sessions` on either.

Each running session is a card:

- **The title**: browser and system, for example "Firefox 131 on Windows 10/11" or "Safari 17 on iOS 17".
- **Device**: computer, phone, tablet, program or app (a WebDAV drive, a script), automated client.
- **Address**: the network the device was in, for example `203.0.113.x`: enough to tell "that was me at home" from a
  place you have never been, without showing the whole address on the screen.
- **Signed in**: when, and how: with the password, with the password and the two-step code, through WebDAV, SOAP or
  REST, or by single sign-on.
- **Last active**: the last click (accurate to five minutes). "Now" for the device you are using.
- **Stays signed in**: "Remember me, until ..." when the box was ticked at sign-in (it is ticked by default and lasts
  360 days), else until the browser is closed.
- **This device** marks the session you are looking with.

Below, **Recently ended** shows the sessions of the last 90 days that are over, and how they ended.

## 2. Signing a device out

1. Find the card of the device you do not want signed in.
2. Click **Sign out this session**. The page says "The session was signed out."
3. The device sees the sign-in page at its next click. Remember me does not bring it back: the remembered cookie
   belongs to the session that was ended.

The session you are using has no such button: sign out with **Sign out** instead.

**Sign out all other sessions** (above the cards, it asks once more) ends every device except this one. Use it after
you signed in on a computer that is not yours, or when a phone was lost.

If you do not recognise a device: sign it out, then change your password (My account > Password). Changing the
password signs out the other sessions as well.

## 3. The e-mail about a new sign-in

When your account signs in from a browser, system and kind of device it has not used in the last 90 days, you get
**New sign-in to your account** at your e-mail address: the device, the time and the approximate address, and the link
to Your devices. If it was you, there is nothing to do. If it was not, follow the link, sign the session out and
change your password.

The very first sign-in of an account sends nothing. To stop the e-mails: untick **E-mail me when my account signs in
from a new device or browser** under **Sign-in alerts** on Your devices and click **Save**. The page E-mail preferences
points to this box: the e-mail is part of account security, so it is switched here.

## 4. For administrators: a user's sessions

Open the user's account page (`user/edit/<id>`, in the user list **Edit** or the account icon). The card **Sessions**
shows how many sessions are signed in; **Show sessions** opens `user/sessions/<id>`:

- the same cards, with the full address next to the network;
- **Sign out this session** for one device, **Sign out everywhere** for all of them (when an account is
  compromised, after a role was taken away, before an account is disabled);
- the user's own choice about the e-mail is not shown or changed here.

The card and the page need the policy function **user/sessions** (Roles and policies > a role > New policy > module
user, function sessions). Without it, the page answers "access denied".

Every sign-out from these pages is recorded in the audit trail as `access.session.revoke`, with who did it; a sign-in
from a new device as `access.session.new_device` (Setup > Audit, filter by name).

Optionally the administrators get a note about each sign-in from a new device:

```ini
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceAdminMail=enabled
AdminMailReceivers[]
AdminMailReceivers[]=security@example.com
```

## 5. For the operator: setting it up

New installations of 6.0.15 have the table `expuser_session`. After an upgrade, run the database update of 6.0.15
for your engine, or on any engine:

```bash
php update/common/scripts/6.0/createusersessiontable.php --dry-run   # says whether it is missing
php update/common/scripts/6.0/createusersessiontable.php
```

Until then the page says the table is missing and every other page works as before. Sessions that were signed in
before appear on their next click, as "signed in before sessions were recorded".

The cronjob `usersessions.php` runs in the part `infrequent` (or `php runcronjobs.php usersessions`): it ends the
sessions not used for `[Session] SessionTimeout` and removes ended sessions after `RetentionDays`. The settings are in
`site.ini [UserSessionSettings]`; see the [feature page](../features/6.0/user-sessions.md#settings-siteini-usersessionsettings).

## 6. Problems

| What you see | Why, and what to do |
|---|---|
| The page says "The table expuser_session is missing" | The database update was not run: see section 5 |
| A device shows "Unknown device" | Its browser sends no usual identification (a privacy tool, an old program); open **Browser identification** on the card to see what it sent |
| The address is the same for every device | The site is behind a proxy that does not pass the visitor's address on: `site.ini [HTTPHeaderSettings] ClientIpByCustomHTTPHeader` and `TrustedProxies[]`, and the header the proxy sets |
| The signed-out device is still signed in | It has not made a request yet: the next click shows the sign-in page. A device that only keeps a page open does not ask the server |
| No e-mail after a sign-in from a new phone | The box is unticked on Your devices, or `NewDeviceMail=disabled`, or it was the account's first sign-in, or the same browser and system were used within 90 days |
| A program (WebDAV, REST with an app password) is not listed | Requests that sign in for themselves only, without a session, are not sessions; revoke the app password or API key instead |

## References

- [Your devices: signed-in sessions per user](../features/6.0/user-sessions.md) (the feature, the settings, the table)
- [Sessions](sessions.md) (the session handler and the session list of the administration)
- [Two-step sign-in](two-factor-login.md)
- [Security and audit](security-and-audit.md)
