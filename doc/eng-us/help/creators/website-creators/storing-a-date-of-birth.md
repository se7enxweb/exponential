---
title: Storing a date of birth with the birthday datatype
audience: website-creator, developer
category: website-creation
order: 195
summary: Add the birthday datatype to a class, what editors see, what is stored, and how to print the date, the age and the zodiac sign in a template.
keywords: birthday, ezbirthday, date of birth, age, zodiac, datatype, website creator
type: help
---
# Storing a date of birth with the birthday datatype

This page is for website creators whose content has a person's date of birth. The `birthday` extension provides the
datatype **Birthday** (`ezbirthday`). Use it instead of a date attribute when you also want the age or the zodiac
sign, or when the year may be far in the past.

## Add it to a class

1. Make sure the extension is active (`ActiveExtensions[]=birthday`).
2. In **Setup > Classes**, edit your class and add an attribute of the datatype **Birthday**.
3. Choose the default: **Empty** or **Current date**. Tick **Required** if every object needs a date.

## What editors see

Three labelled drop-downs: Year (the current year back 100 years), Month and Day. A date with a missing part, or one
that does not exist such as 30 February, is refused with a message. An optional attribute can stay completely empty.

## What is stored

The date as `YYYY-MM-DD` in the attribute's text column, which is also the sort key and the search text.

## Show it

```
{def $b=$node.data_map.birthday.content}
{if $b.is_valid}
    {$b.day}.{$b.month}.{$b.year}, {$b.age} years, {$b.zodiac_name}
{/if}
```

The available values are `year`, `month`, `day`, `is_valid`, `age`, `days_on_earth`, `zodiac_no` and `zodiac_name`.

More detail, the settings and the upgrade notes: [birthday](../../../features/6.0/extensions/birthday.md) and the
`doc/birthday.md` of the extension. See also [Choosing datatypes](choosing-datatypes.md).
