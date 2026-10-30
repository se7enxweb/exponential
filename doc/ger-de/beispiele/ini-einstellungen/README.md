---
title: INI-Einstellungen
audience: developer, admin
category: examples
order: 0
summary: "Jede ausgelieferte INI-Einstellung nach Datei und Block: Kommentar, Standardwert und ein Override-Beispiel."
keywords: Beispiele, Referenz, INI-Einstellungen, examples, reference
type: generated
generated: exp:help:examples
---
# INI-Einstellungen

Jede ausgelieferte INI-Datei des Kernels und der mitgelieferten Erweiterungen, eine Seite pro Block: jede Einstellung mit dem Kommentar der ausgelieferten Datei, ihrem Standardwert und einem Override zum Kopieren. Einstellungen werden in Overrides geändert, nie in den ausgelieferten Dateien (`settings/override/<file>.append.php`, `settings/siteaccess/<siteaccess>/<file>.append.php` oder das `settings/` einer Erweiterung).

| Datei | Aus | Blöcke |
|---|---|---|
| [expportal.ini](expservices-expportal/README.md) | expservices | 1 |
| [expservices.ini](expservices-expservices/README.md) | expservices | 4 |
| [ezjscore.ini](ezjscore-ezjscore/README.md) | ezjscore | 11 |
| [ezoe.ini](ezoe-ezoe/README.md) | ezoe | 6 |
| [ezoe_attributes.ini](ezoe-ezoe_attributes/README.md) | ezoe | 0 |
| [admininterface.ini](admininterface/README.md) | Kernel | 10 |
| [audit.ini](audit/README.md) | Kernel | 34 |
| [binaryfile.ini](binaryfile/README.md) | Kernel | 3 |
| [bookmark.ini](bookmark/README.md) | Kernel | 2 |
| [browse.ini](browse/README.md) | Kernel | 34 |
| [codetemplate.ini](codetemplate/README.md) | Kernel | 1 |
| [collaboration.ini](collaboration/README.md) | Kernel | 4 |
| [collect.ini](collect/README.md) | Kernel | 4 |
| [content.ini](content/README.md) | Kernel | 53 |
| [contentcheck.ini](contentcheck/README.md) | Kernel | 7 |
| [contentfinder.ini](contentfinder/README.md) | Kernel | 1 |
| [contentstructuremenu.ini](contentstructuremenu/README.md) | Kernel | 1 |
| [country.ini](country/README.md) | Kernel | 250 |
| [countrygroups.ini](countrygroups/README.md) | Kernel | 1 |
| [cronjob.ini](cronjob/README.md) | Kernel | 24 |
| [dashboard.ini](dashboard/README.md) | Kernel | 8 |
| [datatable.ini](datatable/README.md) | Kernel | 3 |
| [datatype.ini](datatype/README.md) | Kernel | 5 |
| [datetime.ini](datetime/README.md) | Kernel | 1 |
| [dbschema.ini](dbschema/README.md) | Kernel | 4 |
| [debug.ini](debug/README.md) | Kernel | 2 |
| [debugbar.ini](debugbar/README.md) | Kernel | 42 |
| [design.ini](design/README.md) | Kernel | 5 |
| [error.ini](error/README.md) | Kernel | 7 |
| [exphelp.ini](exphelp/README.md) | Kernel | 69 |
| [extendedattributefilter.ini](extendedattributefilter/README.md) | Kernel | 0 |
| [ezxml.ini](ezxml/README.md) | Kernel | 4 |
| [fetchalias.ini](fetchalias/README.md) | Kernel | 8 |
| [file.ini](file/README.md) | Kernel | 6 |
| [httpcache.ini](httpcache/README.md) | Kernel | 1 |
| [i18n.ini](i18n/README.md) | Kernel | 2 |
| [icon.ini](icon/README.md) | Kernel | 3 |
| [image.ini](image/README.md) | Kernel | 21 |
| [ini.ini](ini/README.md) | Kernel | 1 |
| [kernelservices.ini](kernelservices/README.md) | Kernel | 15 |
| [layout.ini](layout/README.md) | Kernel | 3 |
| [ldap.ini](ldap/README.md) | Kernel | 1 |
| [logfile.ini](logfile/README.md) | Kernel | 1 |
| [logviewer.ini](logviewer/README.md) | Kernel | 10 |
| [mailpreferences.ini](mailpreferences/README.md) | Kernel | 19 |
| [membermanagement.ini](membermanagement/README.md) | Kernel | 4 |
| [menu.ini](menu/README.md) | Kernel | 28 |
| [mime.ini](mime/README.md) | Kernel | 192 |
| [module.ini](module/README.md) | Kernel | 2 |
| [notification.ini](notification/README.md) | Kernel | 6 |
| [override.ini](override/README.md) | Kernel | 0 |
| [package.ini](package/README.md) | Kernel | 64 |
| [paymentgateways.ini](paymentgateways/README.md) | Kernel | 1 |
| [pdf.ini](pdf/README.md) | Kernel | 3 |
| [querycache.ini](querycache/README.md) | Kernel | 2 |
| [rad.ini](rad/README.md) | Kernel | 4 |
| [requestclassifier.ini](requestclassifier/README.md) | Kernel | 7 |
| [requestrules.ini](requestrules/README.md) | Kernel | 4 |
| [rest.ini](rest/README.md) | Kernel | 17 |
| [secrets.ini](secrets/README.md) | Kernel | 1 |
| [setup.ini](setup/README.md) | Kernel | 24 |
| [shop.ini](shop/README.md) | Kernel | 10 |
| [shopaccount.ini](shopaccount/README.md) | Kernel | 3 |
| [site.ini](site/README.md) | Kernel | 63 |
| [smarty.ini](smarty/README.md) | Kernel | 1 |
| [soap.ini](soap/README.md) | Kernel | 2 |
| [solr.ini](solr/README.md) | Kernel | 1 |
| [staticcache.ini](staticcache/README.md) | Kernel | 1 |
| [subitems.ini](subitems/README.md) | Kernel | 7 |
| [subitemscolumns.ini](subitemscolumns/README.md) | Kernel | 129 |
| [syscard.ini](syscard/README.md) | Kernel | 16 |
| [template.ini](template/README.md) | Kernel | 7 |
| [textfile.ini](textfile/README.md) | Kernel | 1 |
| [texttoimage.ini](texttoimage/README.md) | Kernel | 11 |
| [toolbar.ini](toolbar/README.md) | Kernel | 17 |
| [transfer.ini](transfer/README.md) | Kernel | 1 |
| [transform.ini](transform/README.md) | Kernel | 13 |
| [twig.ini](twig/README.md) | Kernel | 1 |
| [uiswitch.ini](uiswitch/README.md) | Kernel | 3 |
| [units.ini](units/README.md) | Kernel | 4 |
| [upgrade.ini](upgrade/README.md) | Kernel | 1 |
| [upload.ini](upload/README.md) | Kernel | 12 |
| [velocity.ini](velocity/README.md) | Kernel | 13 |
| [viewcache.ini](viewcache/README.md) | Kernel | 1 |
| [webdav.ini](webdav/README.md) | Kernel | 2 |
| [wordmatch.ini](wordmatch/README.md) | Kernel | 10 |
| [wordtoimage.ini](wordtoimage/README.md) | Kernel | 1 |
| [workflow.ini](workflow/README.md) | Kernel | 3 |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus den auf jeder Seite genannten Quellen. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
