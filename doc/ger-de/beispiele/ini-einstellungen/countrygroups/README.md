---
title: countrygroups.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von countrygroups.ini.
keywords: Beispiele, Referenz, countrygroups.ini, examples, reference
type: generated
generated: exp:help:examples
---
# countrygroups.ini

Die ausgelieferten Einstellungen von `settings/countrygroups.ini`: 1 Blöcke, 4 Einstellungen.

> Exponential configuration file for the grouped country list  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/countrygroups.ini.append or  
>       settings/override/countrygroups.ini.append.php for more security  
>       in non-virtualhost modes.  
>
> Every country select (the country attribute, the shop's account form, the  
> VAT rules, the user country toolbar) shows these groups first, each in the  
> order given here, then "All countries" A-Z by the translated name.  
> The countries themselves come from country.ini; a code that is not in it is  
> skipped. These settings are not in country.ini because every group of that  
> file is read as a country.  
>
> The ranking of the default list is explained in  
> doc/eng-us/features/6.0/country-groups.md  

| Block | Einstellungen |
|---|---|
| [CountrySettings](CountrySettings.md) | 4 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/countrygroups.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
