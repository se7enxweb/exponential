---
title: "By hand: a datatype"
audience: developer, app-creator
category: extension-development
order: 150
summary: Add the rating datatype by hand: the four files and two settings in build order, a check after each step, and a test plan in the class editor.
keywords: tutorial, by hand, datatype, rating, eZDataType, content.ini, AvailableDataTypes, ExtensionDirectories, edit template, view template, class editor, test plan
type: help
---
# By hand: a datatype

Tutorial 14. You add a rating field (1 to 5) to `mysite_hello`. The class is the one explained line by line in
[Datatype storage, validation and templates](../datatype-storage-validation-and-templates.md); this tutorial is the
build order, with a check after each step. The class passed `php -l` and was created with `eZDataType::create()` and
run on an in-memory attribute with the kernel loaded; the templates were rendered by the template engine.

## 1. The files

```text
extension/mysite_hello/
    datatypes/mysiterating/mysiteratingtype.php
    design/standard/templates/content/datatype/edit/mysiterating.tpl
    design/standard/templates/content/datatype/view/mysiterating.tpl
    settings/content.ini.append.php
    settings/design.ini.append.php        (already there from tutorial 12)
```

## 2. The class

```bash
mkdir -p extension/mysite_hello/datatypes/mysiterating
```

Copy the complete `mysiteRatingType` class from [Datatype storage, validation and templates](../datatype-storage-validation-and-templates.md#the-class)
into `datatypes/mysiterating/mysiteratingtype.php`. The file name, the folder and the string `mysiterating` must match:
the kernel includes `datatypes/<type>/<type>type.php`.

Check:

```bash
php -l extension/mysite_hello/datatypes/mysiterating/mysiteratingtype.php
```

## 3. The registration

`extension/mysite_hello/settings/content.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[DataTypeSettings]
ExtensionDirectories[]=mysite_hello
AvailableDataTypes[]=mysiterating

*/ ?>
```

Check, after clearing the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini
./console exp:ini where content.ini/DataTypeSettings/AvailableDataTypes
```

Your file is in the chain and `mysiterating` is in the list.

## 4. The templates

Copy the edit and view templates from the same chapter into
`design/standard/templates/content/datatype/edit/mysiterating.tpl` and `.../view/mysiterating.tpl`, then:

```bash
php bin/php/ezcache.php --clear-id=template-override,design_base
```

## 5. Use it

1. **Setup > Classes**: open a test class, add an attribute "Rating (1 to 5)", identifier `rating`, store the class.
2. Create an object of the class.

## 6. Test plan

| Input | Expected |
|---|---|
| nothing, attribute not required | saved, nothing shown in the view |
| nothing, attribute required | "Choose a rating." |
| `0`, `6`, `abc` | "A rating is a number from 1 to 5." |
| `4` | saved; the view shows `4 / 5` |
| a new version of the object | the draft starts with the old value (`initializeObjectAttribute()`) |
| a list sorted by the attribute | ordered by rating (`sortKey()`, `sortKeyType()`) |
| a search for `4` | the object is found, if the search engine indexes it (`metaData()`) |

## 7. Commit

```bash
git -C extension/mysite_hello add datatypes design settings
git -C extension/mysite_hello commit -m "Added: The datatype mysiterating, a rating from 1 to 5"
```

## Related pages

- Previous: [By hand: a module protected by a policy](by-hand-module-with-a-policy.md)
- Next: [By hand: a workflow event](by-hand-workflow-event.md)
- [Datatypes](../datatypes.md), [With RAD: a datatype](rad-datatype.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
