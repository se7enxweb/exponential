---
title: Customer and partner areas
audience: businesses, admin, partner
category: business-solutions
order: 30
summary: How an Exponential extranet gives each partner, customer or tier its own area - user groups per organization, sections per tier, subtree limitations per organization, object states for audiences that change, landing pages after sign-in, and how to test that nobody sees another's data.
keywords: partner areas, customer areas, per-partner area, tiers, sections, subtree limitation, object states, StateGroup, user groups, assign with limitation, LoginRedirectionUriAttribute, data separation, extranet structure
type: help
---
# Customer and partner areas

This page is for portal owners and administrators. The promise of a partner portal is that each partner sees what is
meant for them and nothing that belongs to another. This page shows how Exponential keeps that promise with user
groups, sections, subtree limitations and object states, and how to prove it before partners are invited.

## Contents

1. [The building blocks](#the-building-blocks)
2. [Tiers with sections](#tiers-with-sections)
3. [One area per organization with subtrees](#one-area-per-organization-with-subtrees)
4. [Audiences that change with object states](#audiences-that-change-with-object-states)
5. [Combining the models](#combining-the-models)
6. [Sending partners to their area after sign-in](#sending-partners-to-their-area-after-sign-in)
7. [Testing the separation](#testing-the-separation)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## The building blocks

| Block | Role in a portal | Guide |
|---|---|---|
| User group per organization | the people of Partner A, Customer B | [Roles and policies](../../../guides/roles-and-policies.md) |
| Section | a tier or kind of content (Partner material, Gold tier) | [Sections](../../../guides/sections.md) |
| Subtree | the folder of one organization | [Roles and policies, 5](../../../guides/roles-and-policies.md#5-assign-the-role) |
| Object state | an audience label that can change (Internal, Partners, Public) | [Object states](../../../guides/object-states.md) |
| Role | what a group may do, limited by the above | [Roles and policies](../../../guides/roles-and-policies.md) |

Every object belongs to exactly one section; a role can be assigned to a group with a Section or a Subtree
limitation, and a policy can be limited by states. Several limitations of one policy must all be met; policies of
several roles add up ([Roles and policies, 3](../../../guides/roles-and-policies.md#3-create-a-role-with-limited-policies)).

## Tiers with sections

When every partner of a tier receives the same material:

1. Create a section per tier, for example *Partner material* (`partner_material`) and *Gold tier* (`gold_tier`)
   ([Sections, 5](../../../guides/sections.md#5-creating-a-section)).
2. Assign each tier's folder to its section with **Assign content**.
3. Create a role *Partner reader* with `content/read` for Section( Partner material ) and `user/login` for the portal
   siteaccess; assign it to the group of all partners.
4. Create a role *Gold reader* with `content/read` for Section( Gold tier ); assign it to the Gold partners group.

Moving a partner up a tier is one change: add the organization's group to the Gold group, or assign the Gold role to
it. The section's page then lists every role and group that uses it, which helps reviews
([Sections, 4](../../../guides/sections.md#4-one-sections-page)).

## One area per organization with subtrees

When each organization has its own documents (contracts, invoices, project files):

1. Create a folder per organization, for example *Partners > Partner A*, in a section *Partner private* that no
   general role reads.
2. Create one role *Organization area* with `content/read` (and, if partners upload, `content/create` for the File
   class) without a subtree in the policy.
3. Assign it to the Partner A group **with a Subtree limitation** on the Partner A folder; repeat per organization
   ([Roles and policies, 5](../../../guides/roles-and-policies.md#5-assign-the-role)).

One role serves every organization; each assignment carries the organization's subtree. Removing a partner removes
one assignment, and the role page lists every assignment with its limitation.

> **Warning**
> A subtree limitation follows the place in the tree. Content moved out of Partner A's folder is no longer readable
> by Partner A, and content moved into it becomes readable. Train editors to move partner content with care.

## Audiences that change with object states

Some content changes audience over its life: a product sheet is internal while drafted, shared with partners before
launch, public after launch. A state group `audience` with *Internal*, *Partners* and *Public* expresses this:

- The partner reader role gets `content/read` with `StateGroup_audience = Partners, Public`.
- The public Anonymous role gets `content/read` with `StateGroup_audience = Public`.
- Product managers get `state/assign` with `NewState` limited to the states they may set.

Changing one state moves an item between audiences without moving it in the tree
([Object states, 8](../../../guides/object-states.md#8-states-in-roles-and-policies)). Remember that the first state
of a new group is given to every existing object, so set existing public content to *Public* right after creating
the group ([Object states, 9](../../../guides/object-states.md#9-a-worked-example-a-review-workflow) shows the same
care for a review group).

## Combining the models

| Content | Model |
|---|---|
| Material for all partners | section *Partner material* |
| Material for a tier | section per tier |
| Documents of one organization | subtree per organization in section *Partner private* |
| Items that move from internal to partners to public | state group *audience* |

## Sending partners to their area after sign-in

`site.ini [UserSettings] LoginRedirectionUriAttribute[group]` names a text attribute of the user group class that
holds the address users of that group are sent to after signing in; `[user]` does the same per user (commented
examples in `settings/site.ini`, read by `kernel/private/classes/views/user/login.php`). With it, Partner A's people
land on Partner A's page. The attribute must be added to the class first; ask your partner to set it up.

## Testing the separation

Before inviting partners, test as each kind of user, in a private browser window
([Testing as a visitor](../../creators/website-creators/visitor-permissions.md#testing-as-a-visitor)):

| Signed in as | Must see | Must not see |
|---|---|---|
| Partner A user | Partner material, Partner A folder | Partner B folder, Gold tier (if not Gold) |
| Gold partner user | Gold tier | other organizations' folders |
| Visitor | public pages | anything of the portal |

Also check search, menus and file downloads for each, because all of them follow the same permissions. The command
`./console exp:access:check` asks the permission checks for another user from the command line
([Content policy limitations, In short](../../../features/6.0/content-limitation-handlers.md#in-short)).

## Checklist

- [ ] A user group per partner organization.
- [ ] Sections for shared and tier material.
- [ ] One organization-area role, assigned per organization with a Subtree limitation.
- [ ] State group created only if audiences really change; existing content set to the right state.
- [ ] Separation tested as at least two partner users and a visitor, including search and downloads.
- [ ] A procedure for removing an organization's assignment when a contract ends.

## Related pages

- [Partner portal planning](partner-portal-planning.md)
- [Departments, roles and sections](../intranets/departments-roles-and-sections.md)
- [Sections and object states for site builders](../../creators/website-creators/sections-and-object-states.md)
- [Extranet permissions review](extranet-permissions-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
