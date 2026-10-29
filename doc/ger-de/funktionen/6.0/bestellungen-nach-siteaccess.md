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

## Den Shop und die Produkte so öffnen, wie der Kunde sie sah

Die Bestellansicht (`/shop/orderview/<id>`, Designs `admin` und `admin4`) verlinkt den Siteaccess, in dem die
Bestellung aufgegeben wurde:

- **URL der Website**, unter dem Siteaccess: die Adresse, unter der seine Startseite ausgeliefert wird, in einem neuen
  Tab. Es ist die Adresse, die das Siteaccess-Menü der Administration verlinkt (`ezpSiteAccessURL::root()`): ein Host,
  eine URI oder beides, so wie der Siteaccess erkannt wird, mit Schema und Port der Anfrage der Administration.
- **Auf der Website ansehen**, neben jedem Produkt, hinter dem Link auf das Produkt in der Administration: der Knoten
  des Produkts auf diesem Siteaccess, in einem neuen Tab. Die Adresse ist die Adresse der Startseite und der URL-Alias
  des Knotens in den Sprachen des Siteaccess (`[RegionalSettings] SiteLanguageList`), ohne sein
  `[SiteAccessSettings] PathPrefix`. Auf einem Siteaccess mit `PathPrefix=fit-healthy` wird der Knoten
  `fit-healthy/recipes/x` als `<Startseite>/recipes/x` verlinkt. Ein Knoten außerhalb des Präfixes wird dort nicht
  ausgeliefert und bekommt keinen Link; `PathPrefixExclude[]` lässt einen Pfad ganz.

Eine Bestellung ohne Siteaccess, oder mit einem Siteaccess, der nicht mehr konfiguriert ist, zeigt keinen der beiden
Links. Die Bestellansicht des Kunden und die Bestelllisten bleiben unverändert.

## Was gespeichert wird

- `eZBasket::createOrder()` schreibt den Namen des aktuellen Siteaccess in `ezorder.siteaccess`.
- `eZOrder::activate()` trägt ihn bei einer Bestellung nach, die noch keinen hat, etwa bei einem Warenkorb von vor
  der Aktualisierung.
- Auf der Kommandozeile speichert die Bestellung den Siteaccess, in dem das Skript läuft (sein `--siteaccess` oder
  den konfigurierten Standard); über REST den Siteaccess, in dem REST läuft. Ist kein Siteaccess bekannt, bleibt die
  Spalte `''`.
- Bestellungen von vor der Aktualisierung behalten `''` und werden als unbekannt gezeigt, bis das Nachtragen unten
  ihren Siteaccess ableitet, wo es möglich ist.

## Den Siteaccess älterer Bestellungen nachtragen

`bin/php/expbackfillordersiteaccess.php` leitet den Siteaccess von Bestellungen ohne einen aus dem Inhaltsbaum ab, den
jeder Siteaccess ausliefert: den Knoten seines `site.ini [SiteAccessSettings] PathPrefix`, sonst seinen
`content.ini [NodeSettings] RootNode`. Eine Bestellung, deren Produkte (der Hauptort des Objekts jeder Position) alle
im Baum genau eines Siteaccess liegen, erhält diesen Siteaccess. Eine Bestellung, die keinem oder mehreren entspricht
oder ein Produkt enthält, das nicht mehr im Baum ist, bleibt unbekannt. Eine Bestellung mit Siteaccess wird nie
geändert.

Administrationen (admin, editor und ähnliche) nehmen nie Bestellungen an und kommen daher nie in Frage. Sie werden so
bestimmt wie im Siteaccess-Menü der Admin-Kopfzeile: die Siteaccesses aus `site.ini [AdminSiteAccessMenu]
AdminSiteAccessList[]` oder, wenn diese Liste leer ist, die mit einem Administrations-Design (`SiteDesign` oder
`AdditionalSiteDesignList[]` beginnt mit `admin`, so dass auch ein Editor zählt, der auf die Admin-Designs
zurückfällt) oder mit einem `[SiteSettings] FrontSiteAccess`.

```bash
php bin/php/expbackfillordersiteaccess.php                        # Probelauf: listet jede Bestellung und die Summen
php bin/php/expbackfillordersiteaccess.php --prefer-deepest-root  # siehe unten
php bin/php/expbackfillordersiteaccess.php --exclude=site_mobile,site_preview  # siehe unten
php bin/php/expbackfillordersiteaccess.php --apply                # schreibt
```

Ohne `--apply` wird nichts geschrieben: Aufgeführt werden die berücksichtigten Siteaccesses (mit der Wurzel, die jeder
ausliefert), die mit `--exclude` ausgeschlossenen und die als Administration ausgelassenen, dann jede Bestellung mit
dem Siteaccess, den sie erhielte, oder mit dem Grund, warum sie unbekannt bleibt, danach die Summen und die
ausgeschlossenen Siteaccesses. Lesen Sie zuerst den Probelauf.

`--exclude=<siteaccess>[,<siteaccess>...]` nennt Siteaccesses, die nie gewählt werden, etwa einen mobilen Zwilling
oder einen Vorschau-Siteaccess, der denselben Baum wie der Shop ausliefert, aber nie eine Bestellung angenommen hat.
Geben Sie eine durch Kommas getrennte Liste an, wiederholen Sie die Option (`--exclude=site_mobile
--exclude=site_preview`) oder beides. Ein ausgeschlossener Siteaccess fällt aus den Kandidaten einer Bestellung
heraus, so dass der nächste Kandidat gewinnen kann: Liegt ein Shop im Baum einer Site, die den ganzen Baum ausliefert,
erhält die Site seine Bestellungen, wenn der Shop ausgeschlossen ist. Eine Bestellung, die nur ausgeschlossene
Siteaccesses ausliefern, bleibt unbekannt, wird als
`stays unknown, only excluded siteaccesses serve its products: ...` gemeldet und in den Summen gezählt. Ein Name, der
nicht in `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` steht (ein Tippfehler, ein inzwischen entfernter
Siteaccess), ergibt eine Warnzeile, keinen Fehler; der Lauf geht weiter.

`--prefer-deepest-root` ist standardmäßig aus und nur für öffentliche Sites mit verschachtelten Wurzeln gedacht, etwa
einen Shop im Baum einer anderen Site: Von mehreren Sites, die jedes Produkt ausliefern, gewinnt die mit der tiefsten
Wurzel, wenn sie als einzige so tief liegt; zwei Sites mit derselben Wurzel (ein Shop und sein mobiler Zwilling)
lassen die Bestellung weiter unbekannt.

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
  Aktualisierung), `store_name`, `siteaccess_url` (die Adresse der Startseite, `''` wenn es keine gibt) und
  `siteaccess_product_urls` (Content-Objekt-ID => die Adresse des Produkts auf dem Siteaccess).
- `expOrderSiteaccess::frontURL()`, `nodeURL()` und `servedURL()` bauen diese Adressen;
  `expSubitemsURLColumn::servedPath()` nimmt das PathPrefix ab (null für einen Pfad außerhalb davon).
- `eZOrder::active()`, `activeSorted()` und `activeCount()` nehmen ein optionales letztes Argument: `null` für jede
  Bestellung, `''` für die Bestellungen ohne Siteaccess oder einen Namen. `sortColumnsForList()` enthält `siteaccess`.
- `expOrderSiteaccess` (`kernel/classes/expordersiteaccess.php`) speichert den Namen und baut die Filter.
- Die Commerce-Dienste von `expservices` (jede Bestellung, die sie liefern, etwa bei `expOrderServices` `view`,
  `list` und `mine`) enthalten `siteaccess`: den Namen oder `null`, wenn er nicht bekannt ist.
- Tests: `tests/tests/kernel/classes/shop/expOrderSiteaccessTest.php` und `expOrderSiteaccessURLTest.php` (ohne
  Datenbank).

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
