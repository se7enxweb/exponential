---
title: How do I change the state of an object?
audience: editor, admin
category: manual
order: 395
summary: Open the object, choose the new state of each state group in its Details tab and click Set states; states decide, through roles, who may see or edit the object.
keywords: object states, state, set states, state group, workflow state, state/assign, lock, review
type: help
---
# How do I change the state of an object?

Object states are labels such as "Draft, Review, Released" or "Locked, Not locked". A role can allow reading or editing
only in certain states, so changing a state can change who sees the object.

## Who can do this

Users whose role has `state/assign`, often limited to certain states (for example from Draft to Review only).

## Steps

1. Open the object in **Content structure**.
2. Open its **Details** tab.
   Each state group is shown with the object's current state.
3. Choose the new state in the group's list.
4. Click **Set states**.

## What can go wrong

- **The state you want is not offered.** Your role may not assign it. Ask whoever may (for example the release role).
- **The object disappeared from the site after the change.** Visitors may read it only in some states. Set the state
  back, or ask an administrator.

## Related pages

- [Object states](../../guides/object-states.md)
- [Sections and object states](../creators/website-creators/sections-and-object-states.md)
- [User manual](README.md)
