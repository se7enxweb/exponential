---
title: eztags and star rating operators
audience: developer, designer, app-creator
category: development
order: 420
summary: The five operators of the eztags extension (eztagscloud, latest_tags, eztags_parent_string, tag_icon, user_limitations) and the three of ezstarrating (fetch_starrating_stats, fetch_starrating_data, fetch_by_starrating), with parameters, return shapes, the Solr path of the tag cloud, a crash to avoid, and examples rendered on a development installation.
keywords: eztags, eztagscloud, latest_tags, eztags_parent_string, tag_icon, user_limitations, ezstarrating, fetch_starrating_stats, fetch_starrating_data, fetch_by_starrating, tag cloud, rating
type: help
---
# eztags and star rating operators

Two extensions, each with its own `autoloads/eztemplateautoload.php`; the operators exist only while the extension is
active (on a development installation both are in `ActiveExtensions[]`).

| Extension | Class and file | Operators |
|---|---|---|
| eztags | `eZTagsCloud`, `extension/eztags/autoloads/eztagscloud.php` | `eztagscloud` |
| eztags | `eZTagsTemplateFunctions`, `extension/eztags/autoloads/eztagstemplatefunctions.php` | `latest_tags`, `eztags_parent_string`, `tag_icon`, `user_limitations` |
| ezstarrating | `ezsrTemplateOperators`, `extension/ezstarrating/autoloads/ezsrtemplateoperators.php` | `fetch_starrating_stats`, `fetch_starrating_data`, `fetch_by_starrating` |

## Contents

1. [eztags](#eztags): [eztagscloud](#eztagscloud), [latest_tags](#latest_tags),
   [eztags_parent_string](#eztags_parent_string), [tag_icon](#tag_icon), [user_limitations](#user_limitations)
2. [ezstarrating](#ezstarrating): [fetch_starrating_stats](#fetch_starrating_stats),
   [fetch_starrating_data](#fetch_starrating_data), [fetch_by_starrating](#fetch_by_starrating)

## eztags

### eztagscloud

| Parameter | Type | Required | Default |
|---|---|---|---|
| `params` | array (hash) | no | empty |

- **Input:** none.
- **params:** `class_identifier`, `parent_node_id`, `limit`, `offset`, `sort_by` (`array( 'keyword' or 'count',
  ascending )` or a list of them; default keyword ascending) and `post_sort_by` (`keyword`, `keyword_reverse`,
  `count`, `count_reverse`, applied after the limit).
- **Returns:** HTML: `design:tagcloud.tpl` rendered with `$tag_cloud`, a list of `keyword`, `count`, `tag` (the
  `eZTagsObject`) and `font_size` (100 to 200 over the spread of the counts).

When the search engine is `ezsolr` and `eztags.ini` `[GeneralSettings] TagCloudOverSolr=enabled`, the counts come
from a Solr facet on `ezf_df_tag_ids`; otherwise from SQL over the published, visible main nodes the user may read,
counting distinct objects per tag. The SQL path ran here: the five most used tags below node 89, then sorted by
keyword:

```tpl
{eztagscloud( hash( 'parent_node_id', 89, 'sort_by', array( 'count', false() ), 'limit', 5, 'post_sort_by', 'keyword' ) )}
```

Output:

```text
    <a href="/tags/view/Topics/Eating" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="118.75" title="27 objects tagged with 'Eating'">Eating</a>    <a href="/tags/view/Topics/HIIT" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="100" title="21 objects tagged with 'HIIT'">HIIT</a>    <a href="/tags/view/Topics/Health" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="200" title="53 objects tagged with 'Health'">Health</a>    <a href="/tags/view/Topics/Running" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="109.375" title="24 objects tagged with 'Running'">Running</a>    <a href="/tags/view/Topics/Workout" class="eztags-cloud-item eztags-cloud-size-10" data-font-size="146.875" title="36 objects tagged with 'Workout'">Workout</a>
```

### latest_tags

| Parameter | Type | Required | Default |
|---|---|---|---|
| `limit` | integer | no | 10 |

- **Input:** none.
- **Returns:** a hash `result` with the most recently modified main tags (`eZTagsFunctionCollection::fetchLatestTags()`),
  or `result` `false` when there are none. Loop over `.result`, not over the return value.

### eztags_parent_string

| Parameter | Type | Required | Default |
|---|---|---|---|
| `tag_id` | integer | yes | 0 |

- **Input:** none.
- **Returns:** the tag's path of keywords joined by ` / `, the tag itself last, with `(+n)` after a keyword that has
  synonyms (`eZTagsObject::getParentString()`); for an unknown id `(no parent)`, translated.

### tag_icon

| Parameter | Type | Required | Default |
|---|---|---|---|
| `first` | string | no | empty |
| `second` | string | no | empty |

- **Input:** the icon file name. **Returns:** the address of `tag_icons/<size>/<icon>` in the design images, through
  `eZURLOperator::eZImage()`. Piped, `first` is the size (default `small`).
- Without input the code reads `first` as the icon and `second` as the size. On a development installation the function form
  `tag_icon( 'people.png' )` ended in a PHP error ("Call to a member function warning() on null"): the operator calls
  `eZImage()` without a template object, and the lookup it made failed. Use the piped form, rendered on the `admin`
  siteaccess:

```tpl
1 {'people.png'|tag_icon}
2 {'places.png'|tag_icon( 'normal' )}
```

Output:

```text
1 /extension/eztags/design/standard/images/tag_icons/small/people.png
2 /extension/eztags/design/standard/images/tag_icons/normal/places.png
```

### user_limitations

| Parameter | Type | Required | Default |
|---|---|---|---|
| `module` | string | yes | empty |
| `function` | string | yes | empty |

- **Input:** none.
- **Returns:** the result of `hasAccessTo()` for the current user (`accessWord`, `policies`, ...) plus
  `simplifiedLimitations`: for limited access, every limitation name with the unique values of all policies merged.

```tpl
{def $latest = latest_tags( 3 )}
1 latest_tags: {foreach $latest.result as $t}{$t.keyword|wash} (id {$t.id}){delimiter}, {/delimiter}{/foreach}

2 eztags_parent_string: {eztags_parent_string( $latest.result[0].id )|wash}
3 eztags_parent_string of 0: {eztags_parent_string( 0 )|wash}

{def $access = user_limitations( 'tags', 'read' )}
4 user_limitations tags/read: accessWord={$access.accessWord} simplified={$access.simplifiedLimitations|count}
```

Output:

```text
1 latest_tags: Starter (id 243), Angebote (id 244), Urlaub (id 245)
2 eztags_parent_string: Starter
3 eztags_parent_string of 0: (no parent)
4 user_limitations tags/read: accessWord=no simplified=0
```

## ezstarrating

### fetch_starrating_stats

| Parameter | Type | Required | Default |
|---|---|---|---|
| `object_id` | integer | yes | 0 |

- **Input:** none.
- **Returns:** the sum of `rating_count` and the average of `rating_average` of the object's rating rows
  (`ezsrRatingObject::stats()`), kept for the rest of the request. When aggregation finds a row, the hash itself;
  otherwise the raw result list, as for the object without ratings below (one row with empty values).

### fetch_starrating_data

| Parameter | Type | Required | Default |
|---|---|---|---|
| `params` | array (hash) | yes | empty |

- **Input:** none.
- **Returns:** the stored votes (`ezsrRatingDataObject::fetchByConds()`), filtered by `contentobject_id` (or
  `object_id`), `contentobject_attribute_id`, `user_id`, `session_key`, with `limit`, `offset` and `as_object`
  (default `true`).

### fetch_by_starrating

| Parameter | Type | Required | Default |
|---|---|---|---|
| `params` | array (hash) | no | empty |

- **Input:** none.
- **Returns:** nodes ordered by rating (default `rating DESC, rating_count DESC`;
  `ezsrRatingObject::fetchNodeByRating()`).
- **params:** `parent_node_id` or `parent_node_path`, `owner_id`, `owner_parent_node_id` or `owner_parent_node_path`,
  `group_by_owner`, `class_filter_type` and `class_filter_array`, `include_not_rated`, `main_node_only`,
  `ignore_visibility`, `load_data_map`, `as_object`, `limit`, `offset`, `sort_by`.

No content on a development installation has been rated, so only `include_not_rated` returns nodes:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )
     $stats = fetch_starrating_stats( $node.contentobject_id )}
1 stats for an object without ratings: {$stats|get_type}, first row: {foreach $stats[0] as $k => $v}{$k}=[{$v}] {/foreach}

2 votes stored: {fetch_starrating_data( hash( 'contentobject_id', $node.contentobject_id, 'as_object', false() ) )|count}
3 rated nodes below 89: {fetch_by_starrating( hash( 'parent_node_id', 89, 'limit', 3 ) )|count}
4 with include_not_rated: {foreach fetch_by_starrating( hash( 'parent_node_id', 89, 'limit', 3, 'include_not_rated', true() ) ) as $n}{$n.name|wash}{delimiter}, {/delimiter}{/foreach}
```

Output:

```text
1 stats for an object without ratings: array[1], first row: rating_count=[] rating_average=[] 
2 votes stored: 0
3 rated nodes below 89: 0
4 with include_not_rated: Privacy Policy, Cookie Policy, Fitness
```

## Related pages

- [ezwebin operators](ezwebin-operators.md) (`eztagcloud` for the older keyword datatype)
- [Fetch functions in the examples](../../examples/fetch-functions/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
