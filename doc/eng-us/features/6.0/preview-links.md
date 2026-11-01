# Preview links: a draft for a reviewer without an account (6.0.15)

Read this page if you want someone outside the installation (a client, a lawyer, a translator) to see a draft before it
is published, or if you write a tool that makes such links. The [website sidebar](https://github.com/se7enxweb/expwt)
offers them in its Export panel.

## What a link is

A preview link shows **one version of one object in one language**, in the site's design, to anybody who has the link,
signed in or not. Above the content a notice says that it is a draft, not published, and until when the link works.

```
https://www.example.com/content/sharedpreview/3f9c0a1b2c3d4e5f60718293.Yp0w...43 characters
```

- **Signed.** The token is the link's id and an HMAC-SHA256 of its object, version, language and expiry. The key is
  the secret `content.previewlink.key` in the [secret store](secret-store.md), made with the first link. Removing it on
  Setup > Secrets ends every link at once.
- **Expiring.** One day unless its maker chooses otherwise, never longer than `MaxLifetime` (seven days).
- **Revocable.** Each link is a row of `ezsite_data` (`exp_previewlink_<id>`) with who made it, when, its expiry, and
  how often it was opened. A revoked link stops working at once. Ended links are listed seven more days, then removed.
- **Only while it is a draft.** Once the version is published, discarded or archived, or its maker's account is removed
  or disabled, the link answers 410 and says so.
- **Audited.** `content.previewlink.create`, `content.previewlink.revoke` and `content.previewlink.open`
  ([audit catalogue](../../bc/6.0/audit.md)). The token itself is never recorded.

## Who may make one

A user who may read the version (`content/versionread`) and edit its object (`content/edit`). The website sidebar also
asks "Verify it's you" first, in the `export` group of `site.ini [ReauthSettings]`: a link hands unpublished content to
somebody outside.

## What the reviewer gets, and what not

- The view `content/sharedpreview/<token>` needs no login (`site.ini [RoleSettings] PolicyOmitList[]`). The token is the
  permission, for that version only; menus, related content and everything else on the page are what an anonymous
  visitor may see anyway.
- The page is never stored in a cache (the role-aware page cache, Velocity's response cache, proxies:
  `Cache-Control: private, no-store`), never indexed (`X-Robots-Tag: noindex, nofollow`), and the address is not sent on
  as a referrer (`Referrer-Policy: no-referrer`).
- A token that is not a link, or whose signature does not match, answers 404 with a page that says the link does not
  work; an expired, revoked or ended one answers 410.

## Settings

`content.ini`:

```ini
[PreviewLinkSettings]
Enabled=true
DefaultLifetime=86400
MaxLifetime=604800
MaxActivePerVersion=20
```

## For developers

```php
$made = expContentPreviewLink::create( $version, 'eng-GB', 3 * 86400, 'For the client' );
// array( 'link' => ..., 'token' => '...', 'path' => 'content/sharedpreview/<token>' ), or array( 'error' => 'not_allowed' )
$links = expContentPreviewLink::listFor( $objectID );   // newest first, each with state active, expired or revoked
expContentPreviewLink::revoke( $links[0]['id'] );
```

`expContentPreviewLink::available()` says whether links can be made (switched on, a secret store to keep the key in).
The signing, the token's form, the lifetime and the state are pure functions, tested in
`tests/tests/kernel/classes/expContentPreviewLinkTest.php`.
