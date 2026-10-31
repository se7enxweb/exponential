# The website sidebar (expwt) in place of the website toolbar (ezwt)

expwt is the site-side management sidebar of Exponential: a slim tab at the edge of the site's own pages that opens
into a panel for editing the page, creating content, moving it in the tree, and the editor's own account, without
going to the administration. It replaces ezwt, the website toolbar. expwt is its own extension (package
`se7enxweb/expwt`); this page describes what the kernel provides for it and how an installation moves from ezwt to
expwt. The extension's own guide (settings, policies, panels, data layers) is `extension/expwt/doc/eng-us/expwt.md`.

## What the kernel provides

- **The pagelayout hook** ([pagelayout hooks](pagelayout-hooks.md)): expwt registers `BodyEnd[]=expwt/hook.tpl` and so
  reaches every design whose pagelayout carries the hook line; a pagelayout without it gets the sidebar from expwt's
  response filter.
- **Mutations of schema extensions** (`expGraphQLMutationExtension`, [the GraphQL API](graphql.md#schema-extensions)):
  expwt's GraphQL data layer adds `expwtWrite` next to the kernel's mutations.
- **The move from ezwt**: the console command `exp:wt:migrate-from-ezwt` and the upgrade step `6.0.15-expwt-enable`.

## Moving from ezwt to expwt

With expwt in the extension directory:

```bash
./console exp:wt:migrate-from-ezwt            # what would change; nothing is written
./console exp:wt:migrate-from-ezwt --apply    # make the changes
./console exp:upgrade --apply                 # or as part of an upgrade (step 6.0.15-expwt-enable)
```

What it does:

| ezwt | becomes |
|---|---|
| a policy `websitetoolbar/use` or `websitetoolbar/*` | `expwt/use`, `expwt/edit`, `expwt/create` and `expwt/structure` in the same role, with the same limitations |
| the Editor role | `expwt/use`, `expwt/edit`, `expwt/create`, `expwt/structure` where it lacks them |
| the Administrator role | `expwt/*`, unless it has `*/*` |
| `websitetoolbar.ini [WebsiteToolbarSettings] HiddenContentClasses[]` (settings/override and every siteaccess) | `expwt.ini [CreateSettings] HiddenClassList[]` beside it |
| `ActiveExtensions[]=ezwt`, `ActiveAccessExtensions[]=ezwt` | `expwt` at the same place; ezwt is no longer loaded. Where the file activates expwt already (before or after ezwt), the ezwt line is only removed: "remove ezwt (expwt already active)" |

A role with several toolbar policies gets one expwt policy per function, not one per toolbar policy: limitations that
differ in the values of one limitation only are joined (two `Class` lists become one with the classes of both), as
that grants exactly what the toolbar policies granted together; limitation sets that cannot be joined that way (two
classes in two different sections, or a policy with both `Node` and `Subtree`) give one expwt policy each. A policy the
role holds already, the same or broader, is not granted again. Each line names the limitation values and the source:

```
would remove ezwt (expwt already active) from ./settings/override/site.ini.append.php
would grant expwt/use to Editor (3) limited to Class 1, 2, 5, 11, ... (from 2 websitetoolbar/use policies)
```

A file it changes is kept as `<file>.before-expwt`. The eZODF buttons and the custom toolbar templates of ezwt have
no counterpart and are listed as notes, not copied. Running it again changes nothing. The sidebar itself stays off
until it is switched on (`expwt.ini [ExpwtSettings] Enabled=enabled`, per siteaccess), as expwt ships it off until
its 1.0 release.

## Every action checks two policies

The expwt policies (`expwt/use`, `edit`, `create`, `structure`, and `layouts`, `templates`, `shop`, `admin` for later
panels) decide what the sidebar offers. Every action also checks the kernel's policy of what it changes
(`content/edit` of the object, `content/create` below the parent, `content/hide`, `content/remove` ...), through the
same operations the administration uses, workflows included. "Verify it's you" asks for the sidebar's actions with
the same trigger groups as for the administration's views they stand for.
