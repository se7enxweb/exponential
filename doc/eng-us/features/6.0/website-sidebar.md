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
- **Scheduling** (`expContentSchedule`): publish at and take down at through the wait-until-date workflow and the hide
  cronjob, see [Scheduling](#scheduling-one-mechanism-for-the-administration-and-the-sidebar).

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

## Scheduling: one mechanism for the administration and the sidebar

Publishing later and taking a page down at a date use the kernel's own mechanism, the same one the administration
uses (`expContentSchedule`, decision F21 of the expwt plan). Nothing of it belongs to the sidebar:

| | how it works | what the class needs |
|---|---|---|
| publish at | the draft's date field carries the date; publishing starts the workflow on `content/publish` (before), whose "Wait until date" event (`ezwaituntildate`) holds the draft until then; the workflow cronjob (`workflow.php`, frequent part) publishes it. Every other event of that workflow, an approval for one, applies as for any publish | a date field (`ezdatetime`, or `ezdate` for a day) that the event lists, the event directly in the workflow connected to the trigger (one inside a multiplexer is not counted: it may not run for every object, and a date that is not waited for would publish at once) |
| take down at | the date field named by `content.ini [HideSettings] HideDateAttributeList[<class>]`; the hide cronjob (`hide.php`) hides the page once the date has come | that field (`ezdatetime` or `ezdate`), and the page below a node of `[HideSettings] RootNodeList[]` |

To switch it on for a class, for example `article`:

1. Add a date field to the class, e.g. `publish_date` and `hide_date` (Date and time).
2. Setup, Workflows: a workflow with the event "Wait until date" listing `article/publish_date`; Setup, Triggers:
   connect it to `content` `publish` `before`. An approval event may sit in the same workflow.
3. `settings/override/content.ini.append.php`:
   ```ini
   [HideSettings]
   RootNodeList[]=2
   HideDateAttributeList[article]=hide_date
   ```
4. The cronjobs: `workflow.php` (frequent part) and `hide.php` (default part) run already in a standard crontab.

The website sidebar writes these same fields: its "Publish at" and "Take down at" are offered for a class that has
them, and a class without them shows why instead of the field. In the administration the fields are edited as any
other.

### The schedule of earlier expwt versions

Before this, expwt kept its own schedule (site data rows `expwt_publish_<object>_<version>` and
`expwt_unpublish_<node>`) and its own cronjob `expwt_scheduled`. `exp:wt:migrate-from-ezwt` (and the upgrade step
`6.0.15-expwt-enable`) moves every entry to the kernel's schedule: the dates go into the draft's date fields and the
draft is published as the user who scheduled it, so the wait-until-date event holds it; a take-down date goes into
the published version's field. The entry is removed once it has moved. An entry whose draft was published or removed
meanwhile is removed; one whose class lacks the date field stays, is listed as a note, and moves on a later run once
the class has it:

```
would move the sidebar's schedule entry expwt_publish_59_4 to the kernel's schedule: publish object 59 version 4 at 2026-11-02 09:30, take down at 2026-12-01 00:00
the sidebar's schedule entry expwt_unpublish_88 cannot move to the kernel's schedule yet: its class has no date field in content.ini [HideSettings] HideDateAttributeList, or the page is not below a node of RootNodeList; it stays until it can
```

## Every action checks two policies

The expwt policies (`expwt/use`, `edit`, `create`, `structure`, and `layouts`, `templates`, `shop`, `admin` for later
panels) decide what the sidebar offers. Every action also checks the kernel's policy of what it changes
(`content/edit` of the object, `content/create` below the parent, `content/hide`, `content/remove` ...), through the
same operations the administration uses, workflows included. "Verify it's you" asks for the sidebar's actions with
the same trigger groups as for the administration's views they stand for.
