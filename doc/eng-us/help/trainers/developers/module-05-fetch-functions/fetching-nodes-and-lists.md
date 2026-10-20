---
title: "Lesson 5.1: Fetching nodes and lists"
audience: trainer, developer
category: training
order: 51
summary: Learners read data in templates with fetch, load one node by ID or path, list the children of a node with content list, count them with list_count, and know where each fetch function and its parameters are defined. 45 minutes.
keywords: developers course, fetch, content node, content list, list_count, parent_node_id, limit, function_definition.php, eZContentFunctionCollection, as_object, load_data_map
type: help
---
# Lesson 5.1: Fetching nodes and lists

`fetch()` is how a template asks the kernel for data: a node, a list of children, the current user. Every module can
offer fetch functions, and each is described in its `function_definition.php`. This lesson teaches the content
fetches every template uses and shows learners where to read the parameters of any fetch.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 5: Fetch functions](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Module 2](../module-02-the-template-language/README.md): the template playground |

## Learning objectives

After this lesson, learners can:

- write `fetch( '<module>', '<function>', hash( ... ) )` and read its result;
- load one node with `fetch( 'content', 'node', hash( 'node_id', ... ) )`;
- list children with `fetch( 'content', 'list', ... )` and count them with `list_count`;
- find a fetch function and its parameters in `kernel/<module>/function_definition.php`;
- explain why a list fetch needs a `limit`.

## Trainer's notes

### How to present it

Sources: `kernel/content/function_definition.php` and
[Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes).

1. **The form** (3 minutes). `fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'limit', 5 ) )`:
   module, function, named parameters. The result is whatever the function returns, here an array of nodes.
2. **Where fetches are defined** (4 minutes). Open `kernel/content/function_definition.php`. Each
   `$FunctionList['<name>']` names the PHP method (`eZContentFunctionCollection::fetchContentNode` for `node`) and
   its parameters with type, default and whether they are required. List the content functions:
   `grep -o "FunctionList\['[a-z_]*'\]" kernel/content/function_definition.php`.
3. **`node`** (2 minutes). Parameters `node_id`, `node_path`, `language_code` and `remote_id`.
4. **`list` and `list_count`** (4 minutes). `list` takes `parent_node_id`, `limit`, `offset`, `sort_by`,
   `class_filter_type`, `class_filter_array`, `attribute_filter`, `depth`, `language`, `load_data_map` and more;
   `list_count` takes the same filters and returns a number. Always fetch with a `limit` and count with
   `list_count` for paging.
5. **Permissions** (2 minutes). Content fetches return only what the current user may read. An anonymous visitor
   sees fewer items than an editor.

### Common questions

**`fetch` or `$node.children`?** `$node.children` loads every child without limit or sorting control; `fetch` with a
`limit` loads one page.

**Why does my fetch return nothing in a script?** Scripts run as the anonymous user unless they log in a user;
fetches respect permissions.

**Can I fetch by path?** `fetch( 'content', 'node', hash( 'node_path', 'about-us' ) )` finds a node by its URL alias.

### Pitfalls

- **A list without `limit`.** On a folder with thousands of items the page becomes slow and memory-hungry.
- **Fetching in a loop.** A `fetch` inside a `foreach` runs once per item; fetch once before the loop when you can.
- **Parameter names.** A misspelt parameter is ignored silently; compare with `function_definition.php`.

## Hands-on exercise

Use the template playground (`extension/mysite_hello/scripts/play.tpl`) and run it after each step.

1. Load node 2 with a fetch instead of the variable:

   ```
   {def $top = fetch( 'content', 'node', hash( 'node_id', 2 ) )}
   [{$top.name|wash}]
   ```

2. List its five newest children:

   ```
   {def $items = fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                                  'limit', 5,
                                                  'sort_by', array( 'published', false() ) ) )}
   {foreach $items as $item}{$item.name|wash} ({$item.class_identifier}){delimiter}, {/delimiter}{/foreach}
   ```

3. Count all children: `{fetch( 'content', 'list_count', hash( 'parent_node_id', 2 ) )}`.
4. List the content fetch functions in a shell:
   `grep -o "FunctionList\['[a-z_]*'\]" kernel/content/function_definition.php`.
5. Read the parameters of `list` in `kernel/content/function_definition.php` and find `class_filter_array`.
6. Load your course folder by its node ID and list its children with a `limit` of 3.
7. Compare with the administration: the same items, in the order you chose.
8. Free the variables with `{undef}` and commit the template.

## Expected result

- Step 1 prints the name of node 2.
- Step 2 prints up to five names with their class identifiers, newest first.
- Step 3 prints a number at least as large as the list of step 2.
- Step 6 lists the learner's own items from the earlier modules.
- The trainer asks each learner to name the PHP method behind `fetch( 'content', 'node', ... )`.

## Quiz

1. What are the three parts of a fetch call?
2. Which fetch returns the number of children without loading them?
   A. `list` B. `list_count` C. `tree` D. `node`
3. Where are the parameters of the content fetch functions defined?
4. Why does an anonymous visitor see fewer items in a fetch than an editor?
5. Name two parameters of `fetch( 'content', 'node', ... )`.

<details>
<summary>Answers</summary>

1. The module, the function name and a hash of named parameters.
2. **B.** `list_count`.
3. In `kernel/content/function_definition.php`.
4. Content fetches return only what the current user may read.
5. Two of `node_id`, `node_path`, `language_code`, `remote_id`.

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): the language and a complete list template.
- [The content model and editing content, part 9](../../../../guides/content-model-and-editing.md#class-filter-default-for-fetches-template-developers):
  the class filter default for fetches.
- [Access and cache filters](../../../../features/6.0/access-and-cache-filters.md): how extensions can take part in
  access checks and view caching.

## Related pages

- [Module 5: Fetch functions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
