# Proposal

Redmine #16741 (https://pm.team.in-mv.com/issues/16741), change 7 of 7.
Binding design source: `openspec/handoff/marketing-automation-16741.md`, sections 2 and 5.7.
Meant for upstream (se7enxweb). No dependency; `add-channel-posts` uses the same text and image when it posts.

## Why

How a link looks when somebody shares it — title, teaser, image in a post, a chat or a messenger — decides more
about reach than the post itself. Today the bundled site designs write Open Graph tags themselves: three of them
carry copies of the same page-head code, one has a separate template with its own fallbacks. All of them read the
site metadata datatype, but nothing is configurable per class, and an editor cannot see what a shared link will look
like. The older share and social extensions are not maintained, and share buttons that load scripts of the networks
send visitor data to them before anybody clicks.

## What Changes

- **One source of share metadata** per page: title, description, image, type, canonical URL, site name and language,
  taken per content class from configured attributes with fallbacks (for example: share title, else meta title,
  else the object name), written as Open Graph and Twitter Card tags by one template that any design includes.
- **Image rules**: the image is a configured image alias of a sufficient size; when the content has no image, a
  site default is used.
- **Preview in admin4**: a tab on the content shows how the link will look when shared (large card and small card),
  with warnings for a missing image, a too long title or description, and a missing description.
- **Share links** without third-party scripts: plain links for e-mail, Mastodon (with the visitor's own instance),
  Bluesky, LinkedIn and copying the link (which also serves Matrix and other messengers); nothing is loaded from a
  network before a click.
- `add-channel-posts` takes the text and image of a post from the same metadata.
- The four bundled site designs switch to the shared template and drop their own tags in the same step, so a page
  never carries both; a design outside the bundle that writes its own tags keeps working.

## Capabilities

### New Capabilities
- `sharing/metadata`: the metadata of a page, its sources and fallbacks, the image rules, the admin4 preview.
- `sharing/share-links`: share links without third-party scripts.

### Modified Capabilities
- none.

## Impact

- The carrying extension is decided in design.md (a new extension, or the extension that already holds the
  site metadata datatype, which every bundled design reads today); the designs `bootstrap5`, `starter`, `media` and
  `simple` include its template.
- No kernel change expected.

## Out of scope

- Search engine metadata beyond what sharing needs (structured data, sitemaps).
- Counting shares.
- Posting to networks → `add-channel-posts`.
