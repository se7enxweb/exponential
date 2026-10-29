---
title: INI settings
audience: developer, admin
category: examples
order: 0
summary: "Every shipped INI setting by file and block: comment, default and an override example."
keywords: INI settings, examples, reference
type: generated
generated: exp:help:examples
---
# INI settings

Every shipped INI file of the kernel and the bundled extensions, one page per block: each setting with the comment of the shipped file, its default and an override to copy. Settings are changed in overrides, never in the shipped files (`settings/override/<file>.append.php`, `settings/siteaccess/<siteaccess>/<file>.append.php` or an extension's `settings/`).

| File | From | Blocks |
|---|---|---|
| [expportal.ini](expservices-expportal/README.md) | expservices | 1 |
| [expservices.ini](expservices-expservices/README.md) | expservices | 4 |
| [ezjscore.ini](ezjscore-ezjscore/README.md) | ezjscore | 11 |
| [ezoe.ini](ezoe-ezoe/README.md) | ezoe | 6 |
| [ezoe_attributes.ini](ezoe-ezoe_attributes/README.md) | ezoe | 0 |
| [admininterface.ini](admininterface/README.md) | kernel | 10 |
| [audit.ini](audit/README.md) | kernel | 34 |
| [binaryfile.ini](binaryfile/README.md) | kernel | 3 |
| [browse.ini](browse/README.md) | kernel | 34 |
| [codetemplate.ini](codetemplate/README.md) | kernel | 1 |
| [collaboration.ini](collaboration/README.md) | kernel | 4 |
| [collect.ini](collect/README.md) | kernel | 4 |
| [content.ini](content/README.md) | kernel | 53 |
| [contentcheck.ini](contentcheck/README.md) | kernel | 7 |
| [contentfinder.ini](contentfinder/README.md) | kernel | 1 |
| [contentstructuremenu.ini](contentstructuremenu/README.md) | kernel | 1 |
| [country.ini](country/README.md) | kernel | 250 |
| [countrygroups.ini](countrygroups/README.md) | kernel | 1 |
| [cronjob.ini](cronjob/README.md) | kernel | 24 |
| [dashboard.ini](dashboard/README.md) | kernel | 8 |
| [datatable.ini](datatable/README.md) | kernel | 3 |
| [datatype.ini](datatype/README.md) | kernel | 5 |
| [datetime.ini](datetime/README.md) | kernel | 1 |
| [dbschema.ini](dbschema/README.md) | kernel | 4 |
| [debug.ini](debug/README.md) | kernel | 2 |
| [debugbar.ini](debugbar/README.md) | kernel | 42 |
| [design.ini](design/README.md) | kernel | 5 |
| [error.ini](error/README.md) | kernel | 7 |
| [exphelp.ini](exphelp/README.md) | kernel | 69 |
| [extendedattributefilter.ini](extendedattributefilter/README.md) | kernel | 0 |
| [ezxml.ini](ezxml/README.md) | kernel | 4 |
| [fetchalias.ini](fetchalias/README.md) | kernel | 8 |
| [file.ini](file/README.md) | kernel | 6 |
| [httpcache.ini](httpcache/README.md) | kernel | 1 |
| [i18n.ini](i18n/README.md) | kernel | 2 |
| [icon.ini](icon/README.md) | kernel | 3 |
| [image.ini](image/README.md) | kernel | 21 |
| [ini.ini](ini/README.md) | kernel | 1 |
| [kernelservices.ini](kernelservices/README.md) | kernel | 15 |
| [layout.ini](layout/README.md) | kernel | 3 |
| [ldap.ini](ldap/README.md) | kernel | 1 |
| [logfile.ini](logfile/README.md) | kernel | 1 |
| [logviewer.ini](logviewer/README.md) | kernel | 10 |
| [mailpreferences.ini](mailpreferences/README.md) | kernel | 19 |
| [membermanagement.ini](membermanagement/README.md) | kernel | 4 |
| [menu.ini](menu/README.md) | kernel | 28 |
| [mime.ini](mime/README.md) | kernel | 192 |
| [module.ini](module/README.md) | kernel | 2 |
| [notification.ini](notification/README.md) | kernel | 6 |
| [override.ini](override/README.md) | kernel | 0 |
| [package.ini](package/README.md) | kernel | 64 |
| [paymentgateways.ini](paymentgateways/README.md) | kernel | 1 |
| [pdf.ini](pdf/README.md) | kernel | 3 |
| [querycache.ini](querycache/README.md) | kernel | 2 |
| [rad.ini](rad/README.md) | kernel | 4 |
| [requestclassifier.ini](requestclassifier/README.md) | kernel | 7 |
| [requestrules.ini](requestrules/README.md) | kernel | 4 |
| [rest.ini](rest/README.md) | kernel | 17 |
| [secrets.ini](secrets/README.md) | kernel | 1 |
| [setup.ini](setup/README.md) | kernel | 24 |
| [shop.ini](shop/README.md) | kernel | 10 |
| [shopaccount.ini](shopaccount/README.md) | kernel | 3 |
| [site.ini](site/README.md) | kernel | 60 |
| [soap.ini](soap/README.md) | kernel | 2 |
| [solr.ini](solr/README.md) | kernel | 1 |
| [staticcache.ini](staticcache/README.md) | kernel | 1 |
| [subitems.ini](subitems/README.md) | kernel | 7 |
| [subitemscolumns.ini](subitemscolumns/README.md) | kernel | 129 |
| [syscard.ini](syscard/README.md) | kernel | 16 |
| [template.ini](template/README.md) | kernel | 7 |
| [textfile.ini](textfile/README.md) | kernel | 1 |
| [texttoimage.ini](texttoimage/README.md) | kernel | 11 |
| [toolbar.ini](toolbar/README.md) | kernel | 17 |
| [transfer.ini](transfer/README.md) | kernel | 1 |
| [transform.ini](transform/README.md) | kernel | 13 |
| [uiswitch.ini](uiswitch/README.md) | kernel | 3 |
| [units.ini](units/README.md) | kernel | 4 |
| [upload.ini](upload/README.md) | kernel | 12 |
| [velocity.ini](velocity/README.md) | kernel | 13 |
| [viewcache.ini](viewcache/README.md) | kernel | 1 |
| [webdav.ini](webdav/README.md) | kernel | 2 |
| [wordmatch.ini](wordmatch/README.md) | kernel | 10 |
| [wordtoimage.ini](wordtoimage/README.md) | kernel | 1 |
| [workflow.ini](workflow/README.md) | kernel | 3 |

- [All examples](../README.md)

---

Generated by `./console exp:help:examples` from the sources named on each page. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
