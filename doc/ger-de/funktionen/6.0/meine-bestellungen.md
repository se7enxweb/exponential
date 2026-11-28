# Meine Bestellungen: die Bestellliste des Kunden

Diese Seite ist für Shop-Betreiber, die angemeldeten Kunden ihre eigenen Bestellungen zeigen möchten. `shop/myorders`
listet die Bestellungen des aktuellen Benutzers, die neueste zuerst: Bestellnummer, Datum, Status und Summe, jeweils mit
einem Link zur Bestellung.

Vorher listete der Kernel die Bestellungen eines Kunden nur in `shop/customerorderview`, einer Ansicht der
Administration, die `shop/administrate` braucht. Ein Kunde konnte eine Bestellung über ihren Link öffnen, hatte aber
keine Liste.

## Was der Kunde sieht

- Nur die eigenen Bestellungen. Der Benutzer kommt aus der Sitzung, nie aus der Adresse, also lässt sich die Liste eines
  anderen nicht abrufen.
- Nur abgeschlossene Bestellungen. Eine Bestellung, die noch im Checkout ist (temporär), erscheint nicht. Archivierte
  Bestellungen erscheinen, denn das Archivieren räumt die Listen des Shops auf, und die Bestellung gehört weiter dem
  Kunden.
- Jede Bestellung verlinkt dorthin, wohin der Shop Bestellungen überall verlinkt: auf die Bestellansicht oder, mit
  `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt`, auf den dauerhaften Beleg
  ([Dauerhafte Bestellbelege](bestellbelege.md)).
- 20 Bestellungen pro Seite, mit der üblichen Seitennavigation (`/(offset)/20`).
- Ein Besucher, der nicht angemeldet ist, bekommt das Anmeldeformular und kommt danach zur Liste zurück.

## Einschalten

Die Ansicht hat eine eigene Richtlinienfunktion, `shop/myorders`. Sie entscheiden, welche Benutzer die Liste bekommen.

1. Öffnen Sie in der Administration **Rollen und Richtlinien**, bearbeiten Sie die Rolle Ihrer Kunden (zum Beispiel
   *Member*) und fügen Sie die Richtlinie **shop / myorders** hinzu. Sie hat keine Einschränkungen.
2. Verlinken Sie die Seite im Design der Site, zum Beispiel auf der Kontoseite:

   ```
   <a href={'shop/myorders'|ezurl}>My orders</a>
   ```

3. Optional: die Seitengröße in `settings/override/shop.ini.append.php`:

   ```ini
   [MyOrdersSettings]
   # Bestellungen pro Seite, 1 bis 100 (Standard 20)
   Limit=20
   ```

4. Leeren Sie den INI- und den Template-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   ```

## Anpassen

Die Seite ist `design/standard/templates/shop/myorders.tpl`. Kopieren Sie sie unter demselben Pfad in das Design Ihrer
Site, um sie zu ändern. Sie bekommt `$order_list`, `$order_count`, `$offset`, `$limit` und `$view_parameters`. Jede
Bestellung hat `link_url`, `status_name`, `order_nr`, `created` und `total_inc_vat`.

In PHP liefern `eZOrder::fetchListByUserID( $userID, $offset, $limit )` und `eZOrder::countByUserID( $userID )`
dieselben Bestellungen.
