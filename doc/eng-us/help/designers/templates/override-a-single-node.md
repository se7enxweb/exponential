---
title: Override a single node
audience: designer, developer
category: templates
order: 600
summary: A special template for one page (the front page, a campaign page, a contact page): matching by remote id rather than node id, priority over class rules, and when a layout or a dedicated class is the better tool.
keywords: override single node, front page template, one page template, Match node, Match remote_id, node_remote_id, special page, homepage override, campaign page
type: help
---
# Override a single node

This page is for designers who need one page to look different from all others of its class: the front page, a
landing page, the contact page.

## Contents

1. [The rule](#the-rule)
2. [Node id or remote id](#node-id-or-remote-id)
3. [Finding the remote id](#finding-the-remote-id)
4. [Alternatives](#alternatives)

## The rule

```ini
[mytheme_full_front]
Source=node/view/full.tpl
MatchFile=full/front.tpl
Subdir=templates
Match[node_remote_id]=mytheme-front-node
Priority=0
```

`Priority=0` puts it before the class rules ([Override ordering](override-ordering.md)).

## Node id or remote id

| Key | Works on | Survives a reinstall or a copy to another installation |
|---|---|---|
| `Match[node]=2` | that node id | no: ids differ between installations |
| `Match[node_remote_id]=...` | the node's remote id | yes, when the content is installed from a package that keeps remote ids |
| `Match[remote_id]=...` | the object's remote id (all its locations) | yes |

The starter theme matches its root page with `Match[remote_id]=cjw-starter-o-root`, set by its content package.

## Finding the remote id

In the administration, the node's details show it, or in a template on a test page:
`{$node.remote_id|wash}` (node) and `{$node.object.remote_id|wash}` (object). A developer can set a readable remote id
when the content is created by a package or script.

## Alternatives

- **A layout** for the page, when editors should be able to change its structure ([Layouts for designers](../theme-design/layouts-for-designers.md)).
- **A class of its own** (for example `frontpage`), when several pages share the special look.
- **A section**, when a whole area differs ([Override by section](override-by-section.md)).

## Related pages

- [Override match keys](override-match-keys.md)
- [Override basics](override-ini-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
