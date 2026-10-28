# Aus welchem Siteaccess eine Bestellung kam

Diese Seite ist für Shop-Administratoren, die mehr als einen Shop oder eine Site in einer Installation betreiben,
und für Entwickler, die auf dem Shop aufbauen. Jede Bestellung speichert jetzt den Siteaccess (Shop oder Site), in
dem sie aufgegeben wurde, und die Bestelllisten zeigen ihn und filtern danach. Hinzugefügt am 28.10.2026.

## Verwenden

1. Öffnen Sie **Shop > Bestellungen** (oder **Archiv**).
2. Wählen Sie im Filter **Siteaccess** über der Liste einen Siteaccess und klicken Sie auf **Filtern**. **Alle**
   zeigt jede Bestellung; **Unbekannt** zeigt die Bestellungen, die aufgegeben wurden, bevor der Siteaccess
   gespeichert wurde.
3. Klicken Sie in der Bestellliste auf die Überschrift **Siteaccess**, um danach zu sortieren.

Der Filter ist Teil der Adresse, so dass Blättern, Sortieren und ein kopierter Link ihn behalten:

    /shop/orderlist/(sort)/<spalte>/(dir)/<asc|desc>/(siteaccess)/<name>
    /shop/archivelist/(siteaccess)/-

`-` steht für die Bestellungen ohne Siteaccess. Der Filter bietet die Siteaccesses aus
`site.ini [SiteAccessSettings] AvailableSiteAccessList` und jeden Siteaccess an, in dem eine Bestellung aufgegeben
wurde, so dass auch ein inzwischen entfernter Siteaccess wählbar bleibt.

Die Bestellansicht zeigt den Siteaccess, mit dem Namen des Shops (`site.ini [SiteSettings] SiteName` dieses
Siteaccess), wenn er davon abweicht. Die Bestellungen eines Kunden zeigen ihn je Bestellung. Die Bestellansicht, die
ein Kunde sieht (`design/standard`, `design/base`), zeigt den Namen des Shops.

## Was gespeichert wird

- `eZBasket::createOrder()` schreibt den Namen des aktuellen Siteaccess in `ezorder.siteaccess`.
- `eZOrder::activate()` trägt ihn bei einer Bestellung nach, die noch keinen hat, etwa bei einem Warenkorb von vor
  der Aktualisierung.
- Auf der Kommandozeile speichert die Bestellung den Siteaccess, in dem das Skript läuft (sein `--siteaccess` oder
  den konfigurierten Standard); über REST den Siteaccess, in dem REST läuft. Ist kein Siteaccess bekannt, bleibt die
  Spalte `''`.
- Bestellungen von vor der Aktualisierung behalten `''` und werden als unbekannt gezeigt. Aus welchem Siteaccess sie
  kamen, lässt sich nachträglich nicht feststellen; deshalb gibt es kein Skript zum Nachtragen.

## Für Entwickler

- Spalte `ezorder.siteaccess varchar(255) NOT NULL default ''`, in jedem Kernel-Schema, in `share/db_schema.dba` und
  in `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. Die Anweisungen für MySQL und PostgreSQL legen sie nur
  an, wenn sie fehlt, und können deshalb erneut laufen.
- Oracle (die Extension `ezoracle`): `update/database/ezpublish/6.0/dbupdate-6.0.0-6.0.15-order-siteaccess.sql`
  legt ein `VARCHAR2(255)` ohne `NOT NULL` in einem PL/SQL-Block an, der zuerst `user_tab_columns` prüft, und kann
  deshalb erneut laufen. Oracle speichert `''` als `NULL`, daher kann die Spalte nicht `NOT NULL DEFAULT ''` sein;
  der Kernel liest `NULL` als keinen Siteaccess, und der Filter Unbekannt findet beides.
- Bevor die Aktualisierung gelaufen ist, lässt `eZOrder::definition()` die Spalte weg
  (`eZOrder::hasSiteaccessColumn()`), so dass der Shop mit der alten Tabelle weiter arbeitet; die Listen zeigen dann
  jede Bestellung als unbekannt und bieten keinen Filter.
- Template-Attribute einer Bestellung: `siteaccess` (die Spalte), `siteaccess_name` (dasselbe, `''` vor der
  Aktualisierung) und `store_name`.
- `eZOrder::active()`, `activeSorted()` und `activeCount()` nehmen ein optionales letztes Argument: `null` für jede
  Bestellung, `''` für die Bestellungen ohne Siteaccess oder einen Namen. `sortColumnsForList()` enthält `siteaccess`.
- `expOrderSiteaccess` (`kernel/classes/expordersiteaccess.php`) speichert den Namen und baut die Filter.
- Tests: `tests/tests/kernel/classes/shop/expOrderSiteaccessTest.php` (ohne Datenbank).

## Verwandte Seiten

- [Die Bestellliste nach jeder Spalte sortieren](sortierung-der-bestellliste.md)
- [Shop-Dashboard](shop-dashboard.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
