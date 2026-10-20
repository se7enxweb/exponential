# Who calls `ezjscnode`, and what requiring a policy changes (L01)

For **administrators** deciding whether to switch on
`ezjscore.ini [ezjscServer_ezjscnode] RequirePolicy=enabled` on an upgraded site
([L01 in the overview](../lovestack.md#l01--node-data-calls-can-require-a-policy)). New
installations have it on already.

## The three calls

| Call | What it returns or does | Its own check |
|---|---|---|
| `ezjscnode::load::<node id or ezobject_<id>>` | node and object metadata: name, ids, remote id, state, owner, section, dates, class, translations, the data map on request | the object's `canRead()` |
| `ezjscnode::subtree::<node>::<limit>::<offset>::<sort>` | a list of children with the same metadata | content/read through `subTree()` |
| `ezjscnode::updatepriority` (POST) | sets priorities | the parent's `canEdit()` |

With `RequirePolicy=disabled` (the upgrade default), anyone who may read a node can read its
metadata this way, anonymous visitors included, for example
`GET /ezjscore/call/ezjscnode::load::2?ContentType=json`. `ezjscore/call` itself is in
`PolicyOmitList[]` (ezjscore's own `site.ini.append.php`), so `Functions[]=ezjscnode` with
`RequirePolicy=enabled` is what adds the policy check. A refused request is then answered with
403 Forbidden.

## Who calls it in the distribution

| Where | Call | Used by | Anonymous? |
|---|---|---|---|
| `design/admin/templates/children_detailed.tpl` with `design/admin/javascript/ezajaxsubitems_expdatatable.js` | `ezjscnode::subtree` (the fallback data source of the sub-items list) and `ezjscnode::updatepriority` (inline priority editing) | the sub-items list of the administration | no |
| `design/admin4/templates/children_detailed.tpl` with `design/admin4/javascript/ezajaxsubitems_expdatatable.js` | the same | the sub-items list of admin4 | no |
| `extension/exp_adminui/design/adminui/templates/children_detailed.tpl` | the same | the sub-items list of adminui | no |
| `extension/ezoe/design/standard/templates/ezoe/tag_embed_images.tpl` | `ezjscnode::load::ezobject_<id>::0::<size>` | the rich text editor's embed dialog: the image preview per size | no (content editing) |
| `kernel/classes/subitems/expsubitemsserverfunctions.php` | does not call it; reads `[ezjscServer_ezjscnode] HardLimit` | `expsubitems::rows`, the sub-items list's own data source | no; has its own checks |
| `extension/expui` (`exp/io.js`, its tests and its book) | only as an example of `Exp.io.call()` | documentation and tests | — |

No call was found in ezflow, ezwt (it has its own `ezwt::updatepriority`), ezstarrating, eztags,
the Exponential Layouts extensions, the media, simple, starter and ezwebin designs, or `vendor/`.
**No shipped design calls `ezjscnode` for anonymous visitors.** Search your own extensions and
designs before switching on:

```sh
grep -rn "ezjscnode" extension/myextension design/mydesign
```

## What switching on changes

| Who | Effect |
|---|---|
| anonymous visitors | nothing visible with the shipped designs; reading node metadata through `ezjscnode` is refused (403) |
| roles with full access (Administrator) | nothing |
| roles that have *ezjscore / call* without a function limitation | nothing |
| a role without *ezjscore / call* that edits in the administration (often **Editor**) | loses inline priority editing in the sub-items list, the list itself where `expsubitems::rows` is not available, and the image size preview in the embed dialog, **until it gets the policy** |
| your own JavaScript calling `ezjscnode` for anonymous visitors | needs the policy for the Anonymous role |

Other server functions granted with a function limitation (for example `ezstarrating_rate` for
the Anonymous role) are not affected.

## How to switch it on

1. Give *ezjscore / call* with the function list **ezjscnode** to every role whose users work in
   the administration without full access (on most sites: Editor).
2. Add to `settings/override/ezjscore.ini.append.php`:

   ```ini
   [ezjscServer_ezjscnode]
   RequirePolicy=enabled
   ```

3. Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`); under the Velocity engine,
   restart it so its workers read the new setting.
4. Check: as an anonymous visitor, `/ezjscore/call/ezjscnode::load::2?ContentType=json` answers 403;
   as an editor, the sub-items list and its priority column still work.

Thanks to Mugo Web, whose LoveStack commit
[`aa307a7ff3`](https://github.com/mugoweb/ezpublish-legacy/commit/aa307a7ff3110121006b1b97f19fdcbdeab41190)
(2024) put these calls behind the permission system.
