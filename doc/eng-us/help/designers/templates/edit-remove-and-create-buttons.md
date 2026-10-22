---
title: Edit, remove and create buttons on the site
audience: designer, developer
category: templates
order: 725
summary: Buttons on public pages that open the edit form, remove an item or create a new one below it - the content/action form fields they post, the permission checks that decide when to show them, and where the user lands afterwards.
keywords: edit button, remove button, create here, NewButton, EditButton, ActionRemove, content/action, can_edit, can_remove, can_create, can_create_class_list, fetch access, RedirectURIAfterPublish, RedirectIfDiscarded, RedirectURIAfterRemove, front-end editing
type: help
---
# Edit, remove and create buttons on the site

This page is for designers who let logged-in users work on content from the public pages: an **Edit** link on an
article, a **Remove** button for one's own comment, or a **New topic** button in a forum. All of them post a small
form to the view `content/action`, which checks the user's rights again and sends them on to the right page.

## Contents

- [Show a button only to users who may use it](#show-a-button-only-to-users-who-may-use-it)
- [Edit](#edit)
- [Create a new item below this one](#create-a-new-item-below-this-one)
- [Remove](#remove)
- [Where the user lands afterwards](#where-the-user-lands-afterwards)
- [Things to keep in mind](#things-to-keep-in-mind)

## Show a button only to users who may use it

The content object answers the common questions for the current user:

| Attribute | True when the user may |
|---|---|
| `$node.object.can_edit` | edit the object |
| `$node.object.can_remove` | remove it |
| `$node.object.can_create` | create something below it |
| `$node.object.can_create_class_list` | the list of classes they may create below it (each entry with `id` and `name`) |

For a question about one class, or about an object you have not loaded as a node, ask the `access` fetch function:

```
{if fetch( 'content', 'access', hash( 'access', 'create',
                                      'contentobject', $node,
                                      'contentclass_id', 'comment' ) )}
    ... show the comment form ...
{/if}
```

`access` takes the name of a content function (`read`, `edit`, `create`, `remove`, `translate` and the others of the
content module); `contentclass_id` takes an ID or a class identifier. Hiding a button is only for comfort: the
action itself checks the permissions again, so a user who forges the form gets an "access denied" page.

## Edit

```
{if $node.object.can_edit}
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
    <input type="hidden" name="ContentObjectLanguageCode" value="{$node.object.current_language|wash}" />
    <button type="submit" name="EditButton" value="1">Edit</button>
</form>
{/if}
```

`content/action` turns `EditButton` into the address `content/edit/<object ID>/f/<language>`: a new draft in that
language, or the user's existing draft. Without `ContentObjectLanguageCode`, the edit view decides the language
itself. A plain link works as well: `{concat( 'content/edit/', $node.contentobject_id )|ezurl}`.

## Create a new item below this one

```
{if $node.object.can_create}
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="NodeID" value="{$node.node_id}" />
    <input type="hidden" name="ClassIdentifier" value="forum_topic" />
    <input type="hidden" name="ContentLanguageCode" value="{$node.object.current_language|wash}" />
    <button type="submit" name="NewButton" value="1">New topic</button>
</form>
{/if}
```

- `NodeID` is the parent of the new item.
- `ClassIdentifier` names the class; `ClassID` with the numeric ID works too, and wins when both are sent. To let
  the user choose, print a `<select name="ClassID">` with the entries of `$node.object.can_create_class_list`.
- `ContentLanguageCode` sets the language of the new item. Without it, a site with more than one language first
  shows a page where the user picks the language.

The object is created as a draft and the edit form opens. If the user discards it, nothing is left behind.

## Remove

```
{if $node.object.can_remove}
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
    <button type="submit" name="ActionRemove" value="1">Remove</button>
</form>
{/if}
```

`ActionRemove` removes this location (`ContentNodeID`) after a confirmation page. Add
`<input type="hidden" name="HideRemoveConfirmation" value="1" />` to skip the confirmation, which is rarely a good
idea on a public page.

## Where the user lands afterwards

Three optional fields in these forms choose the page after the action:

| Field | In | The user goes to |
|---|---|---|
| `RedirectURIAfterPublish` | edit and create forms | this address after **Send for publishing** |
| `RedirectIfDiscarded` | edit and create forms | this address after discarding the draft |
| `RedirectURIAfterRemove` | the remove form | this address after the removal |
| `RedirectIfCancel` | the remove form | this address when the confirmation is cancelled |

`content/action` keeps the values in the session and the edit form carries them on, so they survive validation
errors and previews. Without them, the edit form uses the last page the user viewed before editing. Give an
address on the same site, for example the node's own alias:

```
<input type="hidden" name="RedirectURIAfterPublish" value="{$node.url_alias|wash}" />
```

## Things to keep in mind

- **Only for logged-in users.** The anonymous user normally has no rights to edit or remove, so the buttons stay
  hidden for visitors without any extra check.
- **The form token is added for you.** The `ezformtoken` extension puts its hidden field into every POST form of an
  HTML page and refuses a POST without it. A form you build in JavaScript must add the token itself
  ([ezformtoken and CSRF](../../extension-development/part-8-reference/ezformtoken-and-csrf.md)).
- **Caching.** These buttons differ per user. The view cache of a node is kept per combination of roles and limited
  assignments, so users with the same roles share it; a button that depends on ownership (`can_edit` with an Owner
  limitation) may be cached for the wrong person. Put such buttons in a part of the page that is not cached, or check
  ownership in an Ajax call.
- **Use buttons, not links, for removing.** A link can be followed by a crawler or a prefetching browser. A POST form
  is the safe choice for anything that changes content.

## Related pages

- [Forms in templates](forms-in-templates.md)
- [Custom edit templates](custom-edit-templates.md)
- [Role and policy operators](role-and-policy-operators.md)
- [Fetch functions reference](../../extension-development/part-8-reference/fetch-functions-reference.md)
- [Common template tasks](../../book-learning/common-template-tasks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
