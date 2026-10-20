---
title: "With RAD: a datatype"
audience: developer, app-creator
category: extension-development
order: 30
summary: The Datatype wizard writes a rating datatype: form, storage and abilities, the files, what you still write, and two corrections to make by hand, from the wizard's real output.
keywords: RAD, datatype wizard, setup/datatype, datatype, storage, data_int, sort_key_int, capabilities, editing, searchable, sortable, generated templates, content.ini
type: help
---
# With RAD: a datatype

Tutorial 3. The **Datatype** wizard (`/setup/datatype`) writes a datatype extension: the class with every method the
abilities you choose need, the settings, the templates, a README and examples. This tutorial makes a rating datatype
like the one in [Datatype storage, validation and templates](../datatype-storage-validation-and-templates.md). The
output below is the wizard's real output: its generator (`expDatatypeWizard::files()`) was run with the same input and
every PHP file passed `php -l`.

## 1. The form

| Field | Value |
|---|---|
| Extension name | `mysite_rating` |
| Datatype identifier | `mysiterating` ("Lower case letters and digits only"; it "goes in the database against every attribute of this type") |
| Class name | `mysiteRatingType` |
| Name in the class editor | `Rating` |
| Group it is listed under | `Custom` (the default; see the note in step 2) |
| Where the value lives | `data_int` and `sort_key_int` |
| What it has to do | Editing, Searchable, Sortable, Text in and out, Packages, Translatable |

The page explains the storage choice: "An attribute is one row in ezcontentobject_attribute", with a fixed set of
columns; a datatype needing more keeps a table of its own. The abilities are groups of methods: "Turning one on writes
all of them, each with a note saying what the kernel calls it for". The full list is Editing, Class settings, Default
value, Naming objects, Searchable, Sortable, Text in and out, Information collector, Files, Simple string insertion,
Packages, Version differences, Buttons of its own, On publish, Cleaning up, Translatable, Relates to other objects and
Batch initialisation.

## 2. The files

```text
.gitignore
LICENSE
README.md
composer.json
datatypes/mysiterating/mysiteratingtype.php
doc/examples.php
doc/storage.md
extension.xml
ezinfo.php
settings/content.ini.append.php
settings/design.ini.append.php
templates/content/datatype/edit/mysiterating.tpl
templates/content/datatype/view/mysiterating.tpl
```

`settings/content.ini.append.php` registers everything, with comments:

```ini
[DataTypeSettings]
# The extension whose datatypes/ directory is searched. Without this line
# the class below is never looked for, however correctly it is named.
ExtensionDirectories[]=mysite_rating

# What the class editor offers. The file is looked for at
# datatypes/mysiterating/mysiteratingtype.php inside this extension.
AvailableDataTypes[]=mysiterating

# Which group the class editor lists it under.
DataTypeGroup[mysiterating]=Custom
```

> **Note**
> When this page was written, nothing in the kernel read `DataTypeGroup[]` (a search of `kernel/`, `lib/` and
> `settings/` finds it only in the wizard), so the class editor lists the datatype among the others whatever the group.

## 3. Two corrections before you use it

When this page was written, the generated output needed two changes by hand:

1. **Move the templates into a design.** The wizard writes them to `templates/...` at the extension's root, while the
   design chain looks in `design/<design>/templates/` of the extensions in `DesignExtensions[]`. Move them:

   ```bash
   mkdir -p extension/mysite_rating/design/standard
   mv extension/mysite_rating/templates extension/mysite_rating/design/standard/templates
   php bin/php/ezcache.php --clear-id=template-override,design_base
   ```

2. **Match the edit field to your storage.** The generated edit template posts the field as
   `{$attribute_base}_data_text_{$attribute.id}` and the generated `objectAttributeContent()` returns `data_text`, as the
   closest kernel datatype does. For a value in `data_int`, rename the field and read and write `data_int`, as in the
   complete class of [Datatype storage, validation and templates](../datatype-storage-validation-and-templates.md).

## 4. Write the methods

Every generated method "returns what the kernel expects and does nothing else", with a note on what the kernel calls it
for, for example on `validateObjectAttributeHTTPInput()`: "Return STATE_INVALID and set a validation error on the
attribute to send the editor back to the form; returning ACCEPTED without looking is how bad content gets in." Fill
them in group by group, starting with Editing, using the complete class of the previous chapter as a model.

## 5. Switch it on and try it

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_rating global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

**Setup > Classes**: add an attribute of type "Rating" to a test class, create an object, try valid
and invalid values, sort a list by the attribute.

## Related pages

- Previous: [With RAD: a template operator in five minutes](rad-template-operator.md)
- Next: [With RAD: a module with views](rad-module.md)
- [Datatypes](../datatypes.md), [By hand: a datatype](by-hand-datatype.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
