# The grouped country list

Every country select of Exponential shows the countries in two groups:

1. **Most used countries**: a ranked list, most used first (the United States, Canada and Mexico on top).
2. **All countries**: every country of `settings/country.ini`, A-Z by its translated name.

Both are native `<optgroup>`s of a native `<select>`, so they work with the keyboard, screen readers and every
browser's own picker, and need no script and no CSS.

The selects that use it:

| Where | Template | Option value |
| --- | --- | --- |
| Country attribute (`ezcountry`), content edit | `content/datatype/edit/ezcountry.tpl` (standard, AdminUI) | ISO alpha-2 code |
| Country attribute, information collection | `content/datatype/collect/ezcountry.tpl` | code |
| Country attribute, class edit (default selection) | `class/datatype/edit/ezcountry.tpl` (standard, AdminUI) | code |
| Shop account form `shop/userregister` (standard, admin, admin4, admin4l, ezwebin, ezdemo, media) | `shop/country/edit.tpl` | name |
| Shop VAT rules, user country toolbar | `shop/country/edit.tpl` with `use_country_code` | code, or name |

They all draw their options with one template, `design:country/options.tpl`, from one fetch,
`fetch( 'content', 'country_groups' )` (class `expCountryGroups`). The admin designs and ezwebin, ezdemo have no copy
of `shop/country/edit.tpl` of their own and use the standard one; the media design keeps its own copy only to add
`form-control`.

## What is stored

Nothing changes in the stored data, so no migration is needed:

- The country attribute still posts and stores ISO alpha-2 codes (`data_text`, comma-separated). Values stored by
  old versions as a country name are still selected.
- The shop's account form still posts the country's name, which the order's account XML stores as before; with
  `use_country_code` (VAT rules) it posts the code.

## Autofill

The bug this fixes: a browser filling the form from a United States address picked *United States Minor Outlying
Islands*. When a browser cannot match an option exactly it takes the first option whose text starts with the
country name of the profile ("United States"), and A-Z that is the Minor Outlying Islands.

Now:

- The United States is the first option of the list, so the first option starting with "United States" is the right
  one. The same holds for "United States of America", "US" and "USA" (a code is matched against the option value).
- The selects say which address field they are: `autocomplete="country"` where the value is a code (the country
  attribute), `autocomplete="country-name"` where it is a name (the shop). The media design's account form also
  names the other fields (`given-name`, `family-name`, `email`, `address-line1`, `address-line2`, `postal-code`,
  `address-level2`, `address-level1`, `tel`), so a browser fills the whole address.
- The shop's select starts with an empty *Choose a country* option when no country is known yet, instead of
  silently preselecting the first country. That also keeps keyboard type-ahead right: typing "United States" from
  the empty option lands on the ranked United States; typing it from a preselected United States walked on to the
  Minor Outlying Islands.
- Names are no longer shortened to 20 characters by default (`max_len`): "United States of A..." is not the name
  autofill looks for.

**Repeat or omit.** A country of the ranked group is listed again in *All countries* in a single select, so the A-Z
list stays complete; only its first option can be selected, so a stored value comes back once. Autofill and
type-ahead both land on the first, ranked option (checked in Chromium and WebKit). A select that allows several
countries lists each country once: there the A-Z group leaves the ranked countries out, because two options of the
same country could be selected separately.

## Settings: `settings/countrygroups.ini`

```ini
[CountrySettings]
GroupList[]
GroupList[]=common
RepeatInAllCountries=enabled
GroupName_common=Most used countries
GroupCountries_common[]
GroupCountries_common[]=US
GroupCountries_common[]=CA
...
```

- `GroupList[]`: the groups before *All countries*, in this order. An empty list leaves only *All countries*.
- `GroupName_<group>`: the label, translated in the context `kernel/country` (eng-US and ger-DE ship).
- `GroupCountries_<group>[]`: ISO 3166-1 alpha-2 codes, in ranked order. A code missing from `country.ini` is
  skipped; a code already listed in an earlier group is skipped.
- `RepeatInAllCountries`: `enabled` (default) repeats the ranked countries in *All countries* of a single select;
  `disabled` leaves them out there too.

The settings are not in `country.ini` because every group of that file is read as a country.

Override per site in `settings/override/countrygroups.ini.append.php` or per siteaccess, for example a shop that
sells mostly in German-speaking countries:

```ini
[CountrySettings]
GroupCountries_common[]
GroupCountries_common[]=DE
GroupCountries_common[]=AT
GroupCountries_common[]=CH
```

## The shipped ranking

37 countries, in the order the product owner set: North America, South America, Europe, the United Kingdom, Asia,
New Zealand, Australia, Africa. Within each region the countries are ranked by expected use: online shoppers and
registered users per country (population times internet and e-commerce use), which puts the largest markets first.

| Region | Countries, in order |
| --- | --- |
| North America | United States, Canada, Mexico |
| South America | Brazil, Argentina, Colombia, Chile, Peru |
| Europe | Germany, France, Italy, Spain, Netherlands, Poland, Sweden, Belgium, Switzerland, Austria, Ireland, Portugal, Denmark, Norway, Finland |
| United Kingdom | United Kingdom |
| Asia | Japan, China, India, South Korea, Singapore, United Arab Emirates |
| Oceania | New Zealand, Australia |
| Africa | South Africa, Nigeria, Egypt, Kenya, Morocco |

## Sorting

*All countries* is sorted by the translated name in the collation of the reader's locale (`Collator` of the intl
extension, secondary strength: accents count after letters, case does not), so German readers find *Österreich*
with O and Swedish readers after Z. Without intl the names are compared with their accents removed and their case
folded ("Åland" sorts with A).

## Templates

```
{include uri='design:country/options.tpl'
         country_value_field='Alpha2'           {* or 'Name' *}
         country_selected=array( 'US' )         {* codes or names *}
         country_selected_map=$hash_keyed_by_code
         country_multiple=false()
         country_max_len=0}
```

The caller draws the `<select>` (name, id, its `<label>`, `autocomplete`) and includes the options inside it.

`fetch( 'content', 'country_groups', hash( 'repeat', true() ) )` returns the groups: a list of
`hash( 'identifier', ..., 'label', ..., 'countries', array( hash( 'Name', ..., 'Alpha2', ..., 'Alpha3', ..., 'Repeated', true() ) ) )`.

## Tests

`tests/tests/kernel/classes/datatypes/expCountryGroupsTest.php` (no database): the ranked order, unknown and
repeated codes, the A-Z order with and without intl, repeats and their marks, the shipped ranking against
`country.ini`, the first option matching a United States profile, and the option values of the templates.
