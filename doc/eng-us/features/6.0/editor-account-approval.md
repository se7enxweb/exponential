# Administrators approve new editor accounts

**For:** site owners, administrators and editors. **Since:** 6.0.15.

An editor may create the account of a new editor, but the account waits until an administrator approves it. Before
that it is not published and cannot sign in. The administrators get an e-mail with a link to the approval; they sign in
and approve or deny it in the administration, as they approve content.

## In short

- Editors create accounts in **Users > Editors**. The account is held: *Waiting for approval*.
- Every administrator gets the mail **New user account waiting for approval**. Its one link opens the approval item in
  the administration after signing in. The mail approves nothing by itself and holds no key.
- **Approve** publishes the account at once: it can sign in. **Deny** keeps it unpublished; it is a draft of the editor
  again.
- Administrators' own new editor accounts are published directly.
- An editor cannot move a member into Editors or give an account a location there: that would skip the approval.
- Everything is recorded in the audit trail: request, approval, denial, refused attempts.
- Which groups need approval, who approves and who needs none are the settings of the workflow event *Approve user
  accounts* at Setup > Workflows. A new installation has them; an existing one runs `php bin/php/adduserapproval.php`.

## For editors

1. Open **Users > Editors** and choose **Create here: User account**.
2. Fill in the name, the username, the e-mail address and a password, and press **Send for publishing**.
3. The page says the object waits for approval. The account is listed in your *Collaboration* under *Your items waiting
   for others* and in *My pending items*.
4. When an administrator approved it, the new editor signs in with the username and password you set. When it was
   denied, the account is in your drafts again: change it and send it again, or remove the draft.

You cannot see or change the other editors' accounts; Editors shows only what you create there.

## For administrators

1. The mail *New user account waiting for approval: <name>* names the account, its username, the group and who asked.
2. Press **Open the approval** (or open Dashboard > Collaboration > *Waiting for your decision*). Sign in if asked.
3. The approval page shows the account. Add a comment if you like, then press **Approve** or **Deny** and confirm.
4. The message says what happened: *The account ... was approved and published: it can sign in now*, or *... was
   denied*.

The approval is the one every administrator sees; the first decision counts, the item then says *Approved* or
*Denied* for all.

## Settings

Setup > Workflows > *Approve new editor accounts* > the event *Approve user accounts*:

- **User groups whose new accounts need approval** (default Editors)
- **Users who approve the accounts** and **Groups who approve the accounts** (default Administrator users)
- **User groups that need no approval**
- **Send the approvers an e-mail** (default on)

The address in the mail is the administration's: set `site.ini [UserSettings] RegistrationAdminBaseURL` when it has a
host of its own (for example `https://admin.example.com`).

More: the technical note [Approval of new user accounts](../../bc/6.0/user-account-approval.md), the guide
[Approving new editor accounts](../../guides/approving-new-editor-accounts.md), and
[member management](../../bc/6.0/member-management.md), which lets editors manage the site's members.
