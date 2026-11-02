# Review hold: stop editing and publishing until a reviewer releases it (6.0.15)

Read this page if a page must not change while somebody reviews it (legal, compliance, a client, a press embargo), or
if you write a tool that places or releases such a hold. The [website sidebar](https://github.com/se7enxweb/expwt)
offers holds in its Page panel.

## What a hold is

A hold is an **object state**: the state group `review_hold` with two states.

| State | Identifier | Meaning |
|---|---|---|
| Not held | `not_held` | the default of every object, old and new: editing and publishing as the roles allow |
| Held for review | `held` | editing and publishing wait until a reviewer releases the object |

Because it is an object state, everything the kernel knows about states works with it: the object's States tab in
the administration, `fetch( 'content', 'object' ).state_identifier_array`, the `StateGroup_review_hold` limitation in
any policy, search filters on states.

## What a hold stops

- **Editing**, by the roles' own policies. Every `content/edit` and `content/publish` policy carries the limitation
  `StateGroup_review_hold = not_held` (the upgrade step adds it), so `canEdit()` is false for a held object: no new
  draft, no editing of an existing draft, no Edit button.
- **Publishing**, by the publish operation itself. While an object is held, a version of it is published only by a
  user whose `content/publish` allows the held state. Otherwise the publication is cancelled, the version stays a draft
  and `content.object.hold.refused` is recorded. The check runs before anything changes and again after the
  `pre_publish` workflows, so a publication that was **scheduled** or **waiting for approval** before the hold is not
  made either: it is refused when the workflow resumes, and the version is a draft again.

A hold does not hide the page: visitors keep seeing the published version. Reading, moving, hiding and removing are
not changed (limit them with the same limitation if you want to).

## Who may edit anyway

- A policy that already names the group is left as it is. A role that may edit held objects (the reviewers' own
  corrections) says so with both states: `content/edit` with `StateGroup_review_hold = not_held, held`.
- Module-wide policies (`content/*`, `*/*`) cannot carry a limitation: administrators keep editing and publishing.
  The upgrade step lists every such role.

## Who places and releases a hold

Placing and releasing are `state/assign` of the group. The upgrade step creates the role **Review hold**, assigned to
nobody:

```
state/assign  StateGroup_review_hold = not_held, held   NewState = not_held, held
```

Assign it to your reviewers (Roles and policies > Review hold > Assign, limited to a subtree if you like). A role that
may only place holds gets `NewState = held`; one that may only release them `NewState = not_held`.

## Audit

- `content.object.state`: who placed or released a hold, with the states before and after (the kernel records every
  state assignment).
- `content.object.hold.refused`: a publication that a hold stopped, with the object and the version.

See the [audit catalogue](../../bc/6.0/audit.md). The website sidebar adds its own record with the reviewer's reason.

## Setting it up

The upgrade step `6.0.15-review-hold` does everything, idempotently:

```bash
./console exp:upgrade:scripts --only=6.0.15-review-hold           # what it would change (a dry run)
./console exp:upgrade:scripts --only=6.0.15-review-hold --apply
```

1. creates the state group `review_hold` and its states (the first, `not_held`, goes to every object);
2. adds `StateGroup_review_hold = not_held` to every `content/edit` and `content/publish` policy that does not name
   the group yet;
3. creates the role **Review hold**.

Nothing changes for editors until an object is held. To undo it, remove the limitation from the policies and the state
group in Setup > States.

## Auto-hold

Content that visitors submit (comments, reviews, forum posts) can be held automatically until a moderator has looked
at it:

```ini
# content.ini (or settings/siteaccess/<public siteaccess>/content.ini.append.php)
[ReviewHoldSettings]
AutoHold=enabled
AutoHoldClassList[]=comment
AutoHoldClassList[]=review
ExemptPolicyList[]=state/assign
ExemptPolicyList[]=moderation/act
```

The **first publication** of an object of a listed class, made by a user who has none of the exempt policies, is put
into `review_hold/held` and **its locations are hidden**. It happens in the publish operation itself (the step
`auto-review-hold`, after the object is published and before the content edit handlers, the commit, the view cache and
the search index), so the page never shows it, whatever published it: a web form, the REST API, a cronjob, an approval.
Later versions of the object are not auto-held. `content.object.hold.auto` is recorded with the submitter's address and
browser. Exempt: a user who may place the held state on the object (`state/assign`, e.g. the role Review hold),
`content/hide` of its location, `moderation/act`, or any other `module/function` listed.

Why hidden and not unreadable: the kernel's own visibility (the hidden flag, `ShowHiddenNodes`) is what every view,
fetch, menu, sitemap, feed and search engine already honour for visitors, with no change to any role. Making held
objects unreadable would mean adding the review hold limitation to every `content/read` policy of the visitors' roles,
and would also hide pages an editor holds on purpose while visitors should keep seeing the published version. So a
hold by itself never hides; the auto-hold hides what it holds, and remembers which locations it hid.

**Releasing** (Dashboard > Recent content, *Release the hold*, or the website sidebar's Page panel) sets `not_held` and
reveals exactly the locations the auto-hold hid, when the releasing user may hide them (`content/hide`); otherwise the
result says it stays hidden. A location an editor hid themselves is never revealed by a release. The kept data (who,
when, why, which locations) is in `ezsite_data` as `review_hold_<object id>` while the hold lasts.

### Auto-hold and expwt's HideNew

The website sidebar has its own pre-moderation of comments, expwt.ini `[Comments] HideNew`: a new comment by someone
who may not hide it is hidden (not held), and the sidebar's *Approve* reveals it. When both are on, the auto-hold runs
first; HideNew sees the comment hidden already and leaves it, and *Approve* in the sidebar also releases the hold. We
recommend the auto-hold: it works for any class, for every way of publishing, without the sidebar, it shows in Dashboard >
Recent content with the submitter's address, and a held comment cannot be edited or republished by its author until a
moderator releases it. Keep HideNew only on installations that do not run the review hold yet.

## For developers

```php
expReviewHold::isHeld( $object );          // held now?
expReviewHold::refusesPublish( $object );  // would the current user's publication be refused?
expReviewHold::state( expReviewHold::STATE_HELD );   // the eZContentObjectState, null before the upgrade step
```

Place or release a hold with the kernel's own operation, so the policy and the audit apply:

```php
eZOperationHandler::execute( 'content', 'updateobjectstate',
    array( 'object_id' => $objectID, 'state_id_list' => array( $stateID ) ) );
```
