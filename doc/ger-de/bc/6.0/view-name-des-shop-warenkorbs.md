# View-Name des Shop-Warenkorbs

Lesen Sie diese Seite, wenn Sie einen Shop mit Exponential betreiben: Sie können der Warenkorbseite jetzt einen eigenen
Namen geben (zum Beispiel `/shop/cart/`), und unterwegs wurden vier Fehler im Shop behoben, von denen einer ändert,
welche Adresszeile ein Kunde ausfüllen muss. Bisher war der Warenkorb nur unter `/shop/basket/` erreichbar, mit diesem
Namen fest eingetragen an zwölf Stellen in sechs Kernel-Skripten und in der Formularaktion jedes ausgelieferten
Templates; eine Website, die ihn „cart“ nennen wollte, musste `kernel/shop` forken.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Einstellung `shop.ini [BasketSettings] BasketViewName` (Standard `basket`); Routing, Weiterleitungen, Formularaktionen, Brotkrumenpfad und Template-Suche folgen ihr. Korrekturen: In den Warenkorb legen, ein fataler Fehler unter PHP 8, abgeschlossene Warenkörbe, Adresszeile 1 als Pflichtfeld. |
| Wer betroffen ist | Beim Standardwert alle nur durch die Korrekturen. Eigene Templates `shop/userregister.tpl`, die Adresszeile 2 als Pflichtfeld kennzeichnen. Designs, die `shop/basket.tpl` überschreiben, funktionieren weiter. |
| Wie Sie es prüfen | `grep -n -A8 BasketSettings settings/shop.ini` |
| Wie Sie es beheben | Verschieben Sie das Pflichtfeld-Sternchen in einem eigenen `shop/userregister.tpl` von Zeile 2 auf Zeile 1. Sonst nichts. |

## Den Warenkorb in einer Minute umbenennen

```ini
# settings/override/shop.ini.append.php
[BasketSettings]
BasketViewName=cart
```

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

`/shop/cart/` zeigt jetzt den Warenkorb, und jede Weiterleitung und jedes Formular des Bestellvorgangs verwendet ihn.
`/shop/basket/` funktioniert für alte Links weiter.

## Hinzugefügt: ein konfigurierbarer Name für die Warenkorbseite des Shops

Der Name ist eine Einstellung. Jedes einfache Token funktioniert, und der ganze Bestellvorgang folgt ihm: Routing,
Weiterleitungen, Formularaktionen, der Brotkrumenpfad und die Template-Suche.

### Einstellung

`settings/shop.ini`:

```ini
[BasketSettings]
# The view name the shop uses for the basket page, and for every redirect,
# form action and breadcrumb that targets it.
#
#   BasketViewName=basket   -> /shop/basket/   (default)
#   BasketViewName=cart     -> /shop/cart/
#   BasketViewName=buyer    -> /shop/buyer/
BasketViewName=basket
```

Erlaubt sind Buchstaben, Ziffern, Bindestrich und Unterstrich. Alles andere fällt auf `basket` zurück, statt eine URL
zu erzeugen, die nirgendwohin führt.

### PHP-API

`kernel/classes/ezbasket.php` erhält zwei statische Methoden:

- `eZBasket::viewName()`  
  Der konfigurierte View-Name, validiert. Gibt `basket` zurück, wenn die Einstellung fehlt, leer oder kein brauchbares
  Token ist.

- `eZBasket::viewTemplate()`  
  Das Template, das `basket.php` rendern soll, *ohne* die Endung `.tpl`. Fragt das Design-System über
  `eZTemplateDesignResource::fileMatch()` in allen `allDesignBases()`, ob `shop/<name>.tpl` existiert, und gibt
  `basket` zurück, wenn nicht.

Wie ein Wert aufgelöst wird:

| `BasketViewName` | `viewName()` | gerendertes Template |
|------------------|--------------|--------------------|
| `basket`         | `basket`     | `shop/basket.tpl`  |
| `cart`           | `cart`       | `shop/cart.tpl`    |
| `buyer`          | `buyer`      | `shop/basket.tpl`  |
| `Trolley-2`      | `Trolley-2`  | `shop/basket.tpl`  |
| `my cart!`       | `basket`     | `shop/basket.tpl`  |
| *(leer)*         | `basket`     | `shop/basket.tpl`  |

`viewTemplate()` sorgt dafür, dass ein Name ohne eigenes Theme ohne zusätzliche Dateien funktioniert: Der Name wird
geroutet, und das gewöhnliche Warenkorb-Template rendert ihn.

### Routing

`kernel/shop/module.php` registriert den konfigurierten Namen zur Laufzeit als View, neben `basket`:

```php
$basketViewName = eZBasket::viewName();
if ( $basketViewName !== 'basket' )
{
    $ViewList[$basketViewName] = array(
        "functions" => array( 'buy' ),
        "script" => "basket.php",
        ... );
}
```

`basket` bleibt unabhängig von der Einstellung registriert, sodass vorhandene Links und Lesezeichen weiter aufgelöst
werden. Gleichzeitig sind nur der konfigurierte Name und `basket` aktiv:

```
BasketViewName=basket   /shop/basket/ 200   /shop/cart/ 404   /shop/buyer/ 404
BasketViewName=buyer    /shop/basket/ 200   /shop/cart/ 404   /shop/buyer/ 200
```

Beide Namen führen dasselbe `basket.php` aus, es gibt also einen Codepfad, und kein Verhalten kann zwischen ihnen
auseinanderlaufen.

### Was der Einstellung folgt

Zwölf Weiterleitungsstellen in sechs Skripten, bisher fest eingetragen. Jedes Skript unter `kernel/shop/` ist jetzt ein
Einstiegspunkt; der Code und damit die Aufrufe von `eZBasket::viewName()` liegen unter
`kernel/private/classes/views/shop/<script>.php` (prüfen Sie es mit
`grep -n viewName kernel/private/classes/views/shop/*.php`):

| Datei                         | Stellen |
|-------------------------------|-------|
| `kernel/shop/basket.php`      | 6 Aufrufe von `functionURI()`, dazu die Beschriftung im Brotkrumenpfad |
| `kernel/shop/add.php`         | 2, einschließlich des Literals `"/shop/basket/"` |
| `kernel/shop/updatebasket.php`| 1 |
| `kernel/shop/confirmorder.php`| 1 |
| `kernel/shop/register.php`    | 1 |
| `kernel/shop/userregister.php`| 1 |

Bewusst **nicht** umbenannt: `$FunctionList['basket']` in `kernel/shop/function_definition.php` ist der Name der
**Richtlinienfunktion** `shop/basket`. Ihn umzubenennen würde jede Rollenrichtlinie brechen, die Zugriff auf den
Warenkorb gewährt.

### Vertrag für Templates

`basket.php` setzt `basket_view_name`. Templates bilden die Aktion daraus und lesen die INI nie selbst:

```tpl
<form method="post" action={concat('/shop/',cond(is_set($basket_view_name),$basket_view_name,'basket'),'/')|ezurl}>
```

Der Rückfall mit `cond()` deckt ein Design ab, das das Template außerhalb des Moduls rendert.

In diesem Repository angepasst: `design/standard/templates/shop/basket.tpl` und
`design/base/templates/shop/basket.tpl`.

### Einen Namen gestalten

Ein Design kann einen Namen gestalten, indem es `shop/<name>.tpl` hinzufügt.
`design/standard/templates/shop/cart.tpl` wird als ausgearbeitetes Beispiel mitgeliefert und bindet das Markup des
Warenkorbs ein, statt es zu duplizieren:

```tpl
{include uri='design:shop/basket.tpl' basket_view_name=$basket_view_name}
```

`basket_view_name` muss ausdrücklich übergeben werden — ein eingebundenes Template erhält einen eigenen
Geltungsbereich und erbt keine Variablen, die das Modul gesetzt hat.

Jedes Design, das `shop/basket.tpl` überschreibt, funktioniert daher unter beiden Namen weiter, ohne dass eine Datei
pro Name nötig ist.

## Behoben: Kernel-Fehler, die beim Verdrahten gefunden wurden

Diese sind unabhängig von der Einstellung und gelten auch beim Standardwert.

### In den Warenkorb legen tat nichts

`kernel/shop/operation_definition.php` deklariert `option_list` als **Pflichtparameter vom Typ `array`**, aber
`add.php` reichte die Sitzungsvariable direkt durch — und die ist bei einem Produkt ohne Optionen einfach nicht
gesetzt. Die Parameterprüfung schlug fehl, und `eZOperationHandler::execute()` gab `STATUS_CONTINUE` zurück, *ohne den
Rumpf der Operation auszuführen*; die Anfrage leitete also auf den Warenkorb weiter, ohne etwas hinzugefügt zu haben,
und protokollierte keinen Fehler.

`add.php` übergibt jetzt immer ein Array.

### `array_keys( false )` — fatal unter PHP 8

Sobald der Rumpf lief, rief `eZShopOperationCollection::addToBasket()` `array_keys()` auf genau diesem Nicht-Array
auf. `$optionList` wird jetzt am Anfang der Methode normalisiert, was alle neun Verwendungen darin abdeckt.

### Ein abgeschlossener Warenkorb wurde als der aktuelle ausgeliefert

`eZBasket::currentBasket()` verglich nur mit `session_id`. Ein Warenkorb, dessen Sitzung geleert, der aber schon einer
Bestellung zugeordnet war, wurde daher jeder Anfrage ohne Sitzungs-ID zurückgegeben, sodass Artikel in einen toten
Warenkorb gelegt wurden. Die Suche wurde zunächst auf `order_id = 0` verschärft; dadurch ging wiederum der Warenkorb
während des Bestellvorgangs verloren, weil `createOrder()` die Bestell-ID zu Beginn setzt. Beim aktuellen Stand gibt
die Suche einen Warenkorb zurück, der keine Bestellung hat oder dessen Bestellung noch vorläufig ist (oder gelöscht
wurde, in diesem Fall wird der Warenkorb wieder von ihr gelöst), nie einen, dessen Bestellung abgeschlossen ist, und
eine Anfrage ohne Sitzungs-ID passt zu keinem Warenkorb.

### Adresszeile 2 war Pflicht, Zeile 1 nicht

`kernel/shop/userregister.php` (Code in `kernel/private/classes/views/shop/userregister.php`) validierte `Street2` und
ignorierte `Street1` — die verirrte Einrückung bei diesem `if` lässt vermuten, dass das nie beabsichtigt war. Ein auf
die naheliegende Weise ausgefülltes Formular ließ sich nicht absenden und gab keinen Hinweis darauf, warum. Zeile 1
ist jetzt das Pflichtfeld.

Dasselbe Skript akzeptiert außerdem `City` mit Rückfall auf `Place`, speichert die Adresszeile als `<city>` und
erfasst ein optionales `<phone>` — nur Pflicht, wenn das Formular das Feld tatsächlich anbietet, sodass ausgelieferte
Templates ohne das Feld nicht betroffen sind.

## Templates des Media-Designs

Das Media-Design fiel bisher auf die Shop-Templates von ezwebin zurück, die nicht dazu passen und `/shop/basket/` fest
eintragen. Ein vollständiger Satz von Overrides wird jetzt in der Erweiterung `sevenx_themes_media` mitgeliefert:

| Template | Zweck |
|---|---|
| `shop/basket.tpl` | Warenkorbseite, die Aktion folgt dem View-Namen |
| `shop/userregister.tpl` | Kontoinformationen, `City` + `Phone` |
| `shop/confirmorder.tpl` | Bestellung bestätigen |
| `shop/orderview.tpl` | aufgegebene Bestellung |
| `shop/country/edit.tpl` | Länderauswahl, gestaltet und nicht abgeschnitten |
| `shop/accounthandlers/html/ez.tpl` | Block mit Kunde und Adresse |

Commit:
<https://github.com/se7enxweb/sevenx_themes_media/commit/6f5c454>

Zwei davon beheben Probleme in den Templates, die sie ersetzen:

- `shop/country/edit.tpl` — das Standard-Template kürzt jeden Ländernamen auf zwanzig Zeichen; das war für eine schmale
  Auswahl mit fester Breite geschrieben und schneidet echte Einträge ab (`Saint Vincent and...`,
  `United States of...`). Das Override kürzt standardmäßig nicht mehr und gibt der Auswahl eine Klasse für
  Formularsteuerelemente. Übergeben Sie `max_len`, um wieder zu kürzen.

- `shop/accounthandlers/html/ez.tpl` — das Standard-Template beschriftet `street1` als **Company** und `street2` als
  **Street**, sodass eine gewöhnliche Adresse als Firmenname über einer leeren Straße erschien. Die Beschriftungen
  passen jetzt zu den Feldern, die Adresszeile liest `city` mit Rückfall auf `place`, und die Telefonnummer wird
  angezeigt.

### Eine Falle, die man kennen sollte

Absende-Schaltflächen werden einen Takt nach dem Absenden deaktiviert, nie innerhalb des Handlers:

```js
form.addEventListener( 'submit', function()
{
    window.setTimeout( disableButtons, 0 );
} );
```

Eine Absende-Schaltfläche, die deaktiviert wird, während das Submit-Ereignis noch läuft, wird **in den gesendeten
Daten weggelassen**. Damit geht der eigene Name der Schaltfläche verloren, das Modul sieht keine Aktion und rendert
stillschweigend ein leeres Formular erneut — ohne Validierungsmeldung, weil keine Validierung lief. Es lässt sich nur
in einem Browser nachstellen; `curl` hat kein JavaScript und war immer erfolgreich.

## Kompatibilität

Der Standard ist `basket`, und jede URL, jede Weiterleitung und jedes Template verhält sich ohne die Einstellung genau
wie bisher. `/shop/basket/` bleibt unter jeder Konfiguration registriert.

Die eine Verhaltensänderung beim Standardwert ist die obige Korrektur von `Street1`/`Street2`: Ein Formular, das bisher
Adresszeile 2 verlangte, verlangt jetzt Zeile 1. Ein eigenes `shop/userregister.tpl`, das Zeile 2 mit einem Sternchen
kennzeichnet, sollte es verschieben.

## Wo Sie es prüfen

| Aussage | Befehl |
|---|---|
| Einstellung und Standard | `grep -n -A8 BasketSettings settings/shop.ini` |
| Zugriffsmethoden | `grep -n "function viewName\|function viewTemplate" kernel/classes/ezbasket.php` |
| Registrierung der View zur Laufzeit | `sed -n 50,70p kernel/shop/module.php` |
| US-Account-Handler | `kernel/classes/shopaccounthandlers/bcusausershopaccounthandler.php` (Klasse `bcusauserShopAccountHandler`) |
| Ausgearbeitetes Template-Beispiel | `design/standard/templates/shop/cart.tpl` |

## Verwandte Seiten

- [Der Shop-Warenkorb in der Chronik vom September 2026](../../../eng-us/history/2026/2026-09a.md#11-september-the-basket-gets-a-name-you-choose)
- [Bestellbelege](../../../eng-us/features/6.0/order-receipts.md) und [Shop-Dashboard](../../../eng-us/features/6.0/store-dashboard.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
