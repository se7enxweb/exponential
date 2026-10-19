---
title: "Lesson 5.2: Filters, sorting and paging"
audience: trainer, developer
category: training
order: 52
summary: Learners narrow fetches by class and attribute, fetch a whole subtree with tree and depth, sort by one or several fields, and page a long list with offset, view_parameters and the google navigator. 55 minutes.
keywords: developers course, fetch, tree, tree_count, depth, class_filter_type, class_filter_array, attribute_filter, sort_by, offset, limit, paging, view_parameters, navigator, google.tpl, DefaultClassFilterType
type: help
---
# Lesson 5.2: Filters, sorting and paging

Real lists are filtered and sorted: the ten newest articles below a section, the events of this month, page 3 of
a search. This lesson teaches the parameters that do it in the database, so the template only receives the items it
shows, and the kernel's navigator template that draws the page links.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 5: Fetch functions](README.md) |
| Duration | 55 minutes (20 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 5.1](fetching-nodes-and-lists.md) |

## Learning objectives

After this lesson, learners can:

- filter by class with `class_filter_type` and `class_filter_array`;
- filter by attribute values with `attribute_filter`;
- fetch a subtree with `tree`, `tree_count` and `depth`;
- sort with `sort_by` on one or several fields;
- page a list with `offset`, `limit`, `$view_parameters.offset` and `design:navigator/google.tpl`.

## Trainer's notes

### How to present it

Sources: `kernel/content/function_definition.php`, `design/standard/templates/node/view/full.tpl` (its paging) and
[The content model and editing content, part 9](../../../../guides/content-model-and-editing.md#class-filter-default-for-fetches-template-developers).

1. **Class filters** (4 minutes). `'class_filter_type', 'include', 'class_filter_array', array( 'article' )`.
   Without `class_filter_type` the array is ignored unless `content.ini [FetchSettings] DefaultClassFilterType` is
   set; always write both.
2. **`tree` and `depth`** (3 minutes). `tree` goes below the children; `depth` limits how deep, counted from the
   parent (`'depth', 2` is children and grandchildren). `tree_count` counts.
3. **Attribute filters** (4 minutes). `'attribute_filter', array( array( 'article/title', 'like', '*news*' ) )`:
   `class/attribute`, an operator (`=`, `!=`, `<`, `>`, `like`, `in`, `between` ...), a value. Several conditions are
   combined with "and" by default. Built-in fields such as `published` or `name` work too.
4. **Sorting** (3 minutes). `'sort_by', array( 'published', false() )`: field and ascending (`true()`) or
   descending (`false()`). Several fields: `array( array( 'priority', true() ), array( 'name', true() ) )`.
5. **Paging** (6 minutes). A full view receives `$view_parameters`; `/(offset)/20` in the address sets
   `$view_parameters.offset`. Fetch with `'offset', $view_parameters.offset`, count with `list_count`, and include
   the navigator as the kernel's `node/view/full.tpl` does:

   ```
   {include name=navigator
            uri='design:navigator/google.tpl'
            page_uri=$node.url_alias
            item_count=$list_count
            view_parameters=$view_parameters
            item_limit=$page_limit}
   ```

### Common questions

**What is the difference between `list` and `tree`?** `list` returns the children only; `tree` the whole subtree,
limited by `depth`.

**How do I sort the way the editor chose in the administration?** Use the node's own sort order:
`'sort_by', $node.sort_array`.

**Can I filter by a relation or a keyword?** There are fetches for that: `related_objects`, `reverse_related_objects`
and `keyword` in `kernel/content/function_definition.php`.

### Pitfalls

- **`class_filter_array` without `class_filter_type`.** Ignored on a default installation: the list contains every
  class.
- **`tree` without `depth` and `limit`.** It can return the whole site.
- **Attribute filters on a sort key.** Filtering works on the attribute's sort key: a rich text field cannot be
  filtered meaningfully.

## Hands-on exercise

Use the template playground and run it after each step.

1. Count the folders in the subtree of node 2, three levels deep:

   ```
   {fetch( 'content', 'tree_count', hash( 'parent_node_id', 2,
                                          'depth', 3,
                                          'class_filter_type', 'include',
                                          'class_filter_array', array( 'folder' ) ) )}
   ```

2. Remove `class_filter_type` from step 1 and run again. Compare the numbers.
3. List three items of the subtree sorted by name, skipping the first:

   ```
   {foreach fetch( 'content', 'tree', hash( 'parent_node_id', 2, 'depth', 3, 'limit', 3, 'offset', 1,
                                             'sort_by', array( 'name', true() ) ) ) as $item}
       {$item.name|wash} (depth {$item.depth})
   {/foreach}
   ```

4. Filter folders whose name contains an `e`:

   ```
   {foreach fetch( 'content', 'tree', hash( 'parent_node_id', 2, 'limit', 5,
                                             'class_filter_type', 'include', 'class_filter_array', array( 'folder' ),
                                             'attribute_filter', array( array( 'folder/name', 'like', '*e*' ) ) ) ) as $f}
       {$f.name|wash}
   {/foreach}
   ```

5. Sort the children of node 2 the way the editor set them: `'sort_by', $node.sort_array`.
6. Write a paged list for the full view override of lesson 3.2: add to `mysite_article.tpl` a list of the article's
   siblings with `limit` 2, `offset` `$view_parameters.offset`, the count, and the navigator include shown above
   (set `$page_limit` to 2 and `$list_count` with `list_count`).
7. Open an article on the site and follow the page links; check that the address gains `/(offset)/2`.
8. Commit the templates.

## Expected result

- Step 2 prints a larger number than step 1: without `class_filter_type` the class filter is ignored.
- Step 3 prints three names in alphabetical order with their depth.
- Step 4 prints folder names containing `e`.
- Step 7: the navigator shows page numbers; each page shows two siblings; the address carries `(offset)`.
- The trainer checks the navigator on one learner's site.

## Quiz

1. Which two parameters filter by class?
2. What does `'depth', 2` return in a `tree` fetch?
   A. Only the children B. Children and grandchildren C. Only the grandchildren D. The whole subtree
3. Write a `sort_by` for "newest first".
4. Where does a full view get the offset of the current page?
5. Why is `class_filter_array` alone not enough on a default installation?

<details>
<summary>Answers</summary>

1. `class_filter_type` and `class_filter_array`.
2. **B.** Children and grandchildren.
3. `'sort_by', array( 'published', false() )`.
4. From `$view_parameters.offset`, set by `/(offset)/<n>` in the address.
5. Without `class_filter_type` the array is ignored unless `DefaultClassFilterType` is set in `content.ini`.

</details>

## Further reading

- [The content model and editing content](../../../../guides/content-model-and-editing.md#class-filter-default-for-fetches-template-developers):
  `DefaultClassFilterType`.
- [Custom items per page](../../../../features/6.0/custom-items-per-page.md) and
  [Admin list paging](../../../../features/6.0/admin-list-paging.md): paging in the administration.
- [Order list sorting](../../../../features/6.0/order-list-sorting.md): sorting in another list of the kernel.

## Related pages

- [Module 5: Fetch functions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
