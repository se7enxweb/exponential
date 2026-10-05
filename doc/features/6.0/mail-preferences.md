# E-mail preferences: the user's guide

Exponential lets every person choose which e-mail they get from a site, on one page, and stop it at any time with one
click. This page is for visitors, members and editors: where the page is, what each choice means, how to unsubscribe
from a mail, what happens when you sign up, and how to download your data. Administrators find the setup, the status
page and the suppression list in [the administrator's guide](../../guides/mail-preferences-administrator.md);
developers find categories and the mail gate in [the developer's guide](../../guides/mail-preferences-developer.md).

## In short

- Optional e-mail (notifications, newsletters, offers, notices) is **off until you turn it on**.
- Essential e-mail about your account (password reset, activation, orders, legal notices) is always sent and never
  carries advertising.
- Every optional e-mail ends with why you got it, an **Unsubscribe** link that works with one click, and a link to your
  preference page. No login is needed for either.
- Your choices, and every change to them, can be downloaded as JSON or CSV.

## Where the page is

| You are | Open |
|---|---|
| Signed in on the public site | your profile (**Edit profile**), the box **E-mail preferences** > **Open my e-mail preferences**, or `/mailpreferences/settings` |
| Signed in to the administration (admin, editor) | **My account** > **My e-mail preferences**, the link in the current user box, or the box at the top of **My notification settings** |
| Not signed in, or without an account | the **Manage** link at the end of any optional e-mail, or `/mailpreferences/request` ("Send me a link") |

The page looks the same everywhere. It has four parts, top to bottom.

### 1. Send me optional e-mail (the main switch)

One line says whether optional e-mail is **On** or **Off**, with one button:

- **Turn off all optional e-mail** stops every optional kind at once. Your choices below are kept, dormant.
- **Turn on optional e-mail** brings them back exactly as they were.

Essential e-mail is not affected by this switch.

### 2. What you want to receive

One switch per kind of e-mail, with its name and what it contains. Nothing is on until you turn it on. Tick what you
want and press **Save my choices**; untick and save to stop it. The notice at the top says what happened ("Your choices
were saved.", "Nothing was changed.").

| Kind (default installation) | What it is | How often |
|---|---|---|
| Content notifications | New and changed content in the parts of the site you follow | At once, Daily summary or Weekly summary |
| Collaboration and approvals | Content waiting for your approval, messages of your collaboration items | At once, Daily summary or Weekly summary |
| Newsletters | The newsletters you subscribe to | the newsletter's own schedule |
| Offers and news | Offers, events and news about products and services | the sender's schedule |
| Application and system notices | Notices about the site and its features that are not about your account | when it happens |

A site can add kinds of its own; they appear in the same list.

**Newsletters** and **Offers and news** need a confirmation: when you turn one on, the page says "We sent a
confirmation link to ... This starts once you open it." and the switch shows **Waiting for your confirmation**. Open the
link in that mail and press **Yes, confirm**. Until you do, nothing of that kind is sent; after seven days the request
lapses and the switch is off again. Content notifications and the others start at once.

If you followed items before this page existed (**My notification settings**), Content notifications already shows as
on, with the frequency of your digest. What you choose here wins.

### 3. Always sent

The essential kinds, listed with their description: Account security, Orders and receipts, Legal and service notices,
Administration alerts. "These messages are needed to run your account or are required by law. They cannot be turned off,
but they never contain advertising."

### 4. Your e-mail data

The last 20 changes to your preferences: when, what, the change, where it was made (Preference page, Link in an e-mail,
Registration form, Confirmation link, Changed by an administrator, ...) and the exact text you saw when you made it.
**Download my e-mail data (JSON)** and **(CSV)** give you everything: each kind with its state, pending confirmations and
the whole history.

## Unsubscribing from an e-mail

Every optional e-mail ends like this (text version; the HTML version has links):

```
--
You get this e-mail because you turned on "Content notifications" on Example Site.
Unsubscribe with one click: https://www.example.com/mailpreferences/unsubscribe/m1...
Choose which e-mail you get: https://www.example.com/mailpreferences/manage/m1...

Example Organisation
Example Street 1, 12345 Town
```

- **Unsubscribe** opens a page that asks "Stop e-mail of the kind "Content notifications"?" with one button,
  **Unsubscribe**. Pressing it stops that kind at once: "... gets no more e-mail of the kind ...". Opening the link alone
  changes nothing, so a virus scanner that opens links cannot unsubscribe you.
- Many mail programs (Gmail, Apple Mail, Outlook, Yahoo) show their own **Unsubscribe** button next to the sender. It
  uses the same link and unsubscribes you without opening a page.
- **Choose which e-mail you get** opens your full preference page, without login.

The links work as long as the site exists; they are personal, so do not forward a mail with them.

A link that does not work any more (cut off by the mail program, for example) shows the "Send me a link" form with the
notice "This link does not work any more".

## Send me a link (without an account)

`/mailpreferences/request`: type your address and press **Send me the link**. The page answers "Please check your inbox.
If we send e-mail to ..., a message with your personal link is on its way. The link works for 24 hours." It says the same
for every address, so nobody can find out from it who is on a list. One address can ask for three links an hour.

## When you register

The registration form has a box **E-mail from us (optional)** with one unticked box per optional kind. Tick only what you
want; nothing is ticked for you. A kind that needs a confirmation sends its link after the registration. What you ticked is
recorded with the text the form showed.

## Changing your e-mail address

A new address must be confirmed: a link goes to the new address ("Use ... for this account?"), and the account keeps the
old address until you press **Yes, confirm**.

## Your rights

- **See your data**: the history on the page and the download.
- **Withdraw**: any switch, the main switch, or the unsubscribe link, at any time.
- **Erase**: ask the site's administrator. Your preferences are removed and the history is anonymised; a record that you
  withdrew is kept without your address, so the site can prove it stopped writing to you.
- **Stop everything for good**: the unsubscribe link of a mail sent to everyone ("Stop all optional e-mail?") also puts
  your address on the site's block list (only a scrambled form of the address is kept). Turning optional e-mail on again
  on your page lifts that block.

## Languages

The pages and mails are in English and German; the German site (for example `/bold_ger/mailpreferences/settings` on a
demo installation) shows "E-Mail-Einstellungen".

## Related pages

- [E-mail preferences: the administrator's guide](../../guides/mail-preferences-administrator.md)
- [Notifications: the user's guide](notifications.md)
- [The law checklist](../../specifications/6.0/mail-preferences-compliance.md)
