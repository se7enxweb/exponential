---
title: exp:expbackfillordersiteaccess
audience: developer, admin
category: examples
summary: Leitet den Siteaccess von Bestellungen ab, die vor der Erfassung von ezorder.siteaccess aufgegeben wurden (Probelauf ohne --apply).
keywords: Konsole, Befehl, exp:expbackfillordersiteaccess, expbackfillordersiteaccess, console, command, CLI, expbackfillordersiteaccess.php
type: generated
generated: exp:help:examples
---
# exp:expbackfillordersiteaccess

Leitet den Siteaccess von Bestellungen ab, die vor der Erfassung von ezorder.siteaccess aufgegeben wurden (Probelauf ohne --apply).

Skript `bin/php/expbackfillordersiteaccess.php`, Code in `kernel/private/classes/commands/expbackfillordersiteaccess.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expbackfillordersiteaccess --help
php bin/php/expbackfillordersiteaccess.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Infer the siteaccess of orders that have none (placed before Exponential 6.0.15 recorded it)
from the content tree each site siteaccess serves: an order whose products all lie in the tree of
exactly one site (its PathPrefix node, else its RootNode) gets that siteaccess; orders matching none
or several stay unknown. Administrations (admin, editor ...) never take orders and are left out,
decided as the siteaccess menu decides them ([AdminSiteAccessMenu] AdminSiteAccessList[], else the
design). Siteaccesses named with --exclude are never chosen either: the next candidate can win, and an
order only excluded siteaccesses serve stays unknown. An order that has a siteaccess is never changed.
Without --apply nothing is written: the siteaccesses considered, excluded and left out are listed,
then each order with the siteaccess it would get, or why it stays unknown.

Example: php bin/php/expbackfillordersiteaccess.php
         php bin/php/expbackfillordersiteaccess.php --prefer-deepest-root
         php bin/php/expbackfillordersiteaccess.php --exclude=site_mobile,site_preview
         php bin/php/expbackfillordersiteaccess.php --exclude=site_mobile --exclude=site_preview --prefer-deepest-root
         php bin/php/expbackfillordersiteaccess.php --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Schreibt die abgeleiteten Siteaccesses (standardmäßig ein Probelauf, der nichts ändert) |
| `--prefer-deepest-root` |  | Standardmäßig aus. Nur für öffentliche Sites mit verschachtelten Wurzeln (ein Shop im Baum einer anderen Site): Von mehreren Sites, die jedes Produkt ausliefern, die mit der tiefsten Wurzel nehmen, wenn sie als einzige so tief liegt |
| `--exclude` | erforderlich | Diese Siteaccesses nie wählen: eine durch Kommas getrennte Liste, die Option kann wiederholt werden (--exclude=a,b --exclude=c). Ein Name, der nicht in AvailableSiteAccessList steht, ergibt eine Warnung, keinen Fehler |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expbackfillordersiteaccess.php`, `kernel/private/classes/commands/expbackfillordersiteaccess.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
