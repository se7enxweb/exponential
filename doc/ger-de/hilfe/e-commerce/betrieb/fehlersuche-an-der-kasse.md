---
title: Fehlersuche an der Kasse
audience: ecommerce, website-creator, admin
category: e-commerce
order: 82
summary: Symptome, Ursachen und Lösungen für eine Exponential-Kasse, bei der etwas schiefgeht - bezahlt, aber keine Bestellung, NO_CALLBACK, leerer Warenkorb nach dem Abbrechen, an der Kasse entfernte Artikel, fehlender Versand, unbekannte oder null USt, Gateway fehlt im Ereignis, keine Bestellmail, Status lässt sich nicht setzen - mit den Logs und Bildschirmen, die zu prüfen sind.
keywords: Fehlersuche, Problem an der Kasse, Zahlungs-Callback, Zahlung nicht bestätigt, Bestellung fehlt, USt unbekannt, USt null, Versand fehlt, Gateway nicht aufgeführt, Bestellmail fehlt, Workflow-Prozess, troubleshooting, checkout problem, payment callback, NO_CALLBACK, checkoutagain, cancelcheckout, payment not confirmed, order missing, VAT unknown, VAT zero, shipping missing, gateway not listed, order mail missing, eZPaymentGatewayType.log, workflow process
type: help
---
# Fehlersuche an der Kasse

Diese Seite richtet sich an Administratoren und Entwickler, die herausfinden müssen, warum ein Bestellabschluss
schiefgegangen ist. Jeder Abschnitt beginnt mit dem, was jemand meldet, listet die wahrscheinlichen Ursachen in der
Reihenfolge auf, in der sie zu prüfen sind, und nennt das Log oder den Bildschirm, der es belegt.

## Inhalt

1. [Wo Sie zuerst nachsehen](#wo-sie-zuerst-nachsehen)
2. [Kunde hat bezahlt, aber keine Bestellung](#kunde-hat-bezahlt-aber-keine-bestellung)
3. [Artikel verschwinden an der Kasse](#artikel-verschwinden-an-der-kasse)
4. [Warenkorb leer nach dem Abbrechen](#warenkorb-leer-nach-dem-abbrechen)
5. [Versand fehlt oder ist doppelt](#versand-fehlt-oder-ist-doppelt)
6. [USt unbekannt oder null](#ust-unbekannt-oder-null)
7. [Gateway wird nicht angeboten](#gateway-wird-nicht-angeboten)
8. [Keine Bestellmail](#keine-bestellmail)
9. [Ein Status lässt sich nicht setzen](#ein-status-lässt-sich-nicht-setzen)

## Wo Sie zuerst nachsehen

| Quelle | Zeigt |
|---|---|
| Shop-Dashboard, „So funktioniert Ihr Shop“ und „Nächste Schritte“ | Die geltende Konfiguration und bekannte Probleme |
| **Setup > Workflow-Prozesse** | Workflow-Prozesse, die noch warten, zum Beispiel eine Zahlung |
| `var/log/eZPaymentGatewayType.log`, `var/log/eZRedirectGateway.log` | Jeden Schritt des Zahlungsereignisses und der Weiterleitungs-Gateways |
| Das Debug-Log | Nicht gefundene Handler-Dateien, Fehler bei USt und Kategorien |
| Reiter **Audit**, Kanal `commerce` | Begonnene Bestellabschlüsse, angelegte Bestellungen, genehmigte Zahlungen ([Audit-Trail für den Handel](audit-trail-fuer-den-handel.md)) |

## Kunde hat bezahlt, aber keine Bestellung

Der Anbieter hat das Geld genommen, aber die Bestellliste enthält keine Bestellung. Die Bestellung ist noch temporär,
weil der Callback des Anbieters die Zahlung nie genehmigt hat
([Temporäre Bestellungen und Aktivierung](../kasse/temporaere-bestellungen-und-aktivierung.md)). Der Kunde hat
möglicherweise Seiten „erneut zur Kasse“ und dann einen Fehler mit dem Code `NO_CALLBACK` gesehen
([Die Kassenoperationen Schritt für Schritt](../kasse/die-kassenoperationen-schritt-fuer-schritt.md#schritt-6-zurück-vom-anbieter)).

1. Ist die Callback-Adresse aus dem Internet über HTTPS erreichbar, ohne Anmeldung, Wartungsseite oder IP-Filter?
2. Ist die Callback-View von Richtlinienprüfungen ausgenommen (bei PayPal `PolicyOmitList[]=paypal/notify_url`)?
3. Zeigen die Einstellungen des Anbieters auf die Live-Adresse, nicht auf eine Test- oder alte Domain?
4. Zeigt das Gateway-Log den Callback und warum er abgelehnt wurde (Betrag, Währung, Validierung)?
5. Ist der Anbieter im selben Modus (Test oder Live) wie die Einstellungen der Site?

Beheben Sie zuerst die Ursache; erledigen Sie dann die Bestellungen der betroffenen Kunden von Hand anhand der
Unterlagen des Anbieters.

## Artikel verschwinden an der Kasse

Wenn **Zur Kasse** gedrückt wird, wird der Warenkorb geprüft: Artikel, deren Produkt nicht mehr veröffentlicht ist oder
deren Preis oder USt sich seit dem Hinzufügen geändert hat, werden entfernt und auf der Warenkorbseite aufgeführt.
Meistens wurde im Laufe des Tages ein Preis geändert. Sagen Sie dem Kunden, er soll den Artikel erneut hinzufügen.

## Warenkorb leer nach dem Abbrechen

| Ursache | Lösung |
|---|---|
| `ClearBasketOnCheckout=enabled` | Erwartetes Verhalten mit dieser Einstellung; schalten Sie sie ab, sofern das Gateway sie nicht verlangt |
| `ClearBasketOnLogout=enabled` und der Kunde hat sich abgemeldet | Erwartet |
| Eine Installation älter als 6.0.15 | Abbrechen auf der Bestätigungsseite hat früher den Warenkorb verloren; aktualisieren Sie ([Dauerhafte Bestellbelege](../../../funktionen/6.0/bestellbelege.md#eine-korrektur-am-bestellvorgang-nebenbei)) |

## Versand fehlt oder ist doppelt

| Symptom | Ursache |
|---|---|
| Keine Versandzeile | Der Versand-Workflow liegt nicht auf `shop` `confirmorder` before oder hat kein Ereignis einfacher Versand |
| Falscher Betrag | `workflow.ini [SimpleShippingWorkflow] ShippingCost` in einer anderen Override-Datei gewinnt ([Durchgänge durch die Shop-Einstellungen](../settings/README.md#den-wirksamen-wert-prüfen)) |
| Zwei Versandkosten | Sowohl ein Versand-Handler als auch der Workflow für einfachen Versand sind aktiv |
| Handler ignoriert | Debug-Log: „Shipping handler ... not found“; prüfen Sie `ExtensionDirectories[]` und den Dateinamen |

## USt unbekannt oder null

| Symptom | Ursache |
|---|---|
| „Determined by VAT charging rules“ wird nicht angeboten | `[VATSettings] Handler` nicht gesetzt |
| USt unbekannt | Dynamischer USt-Typ ohne Handler, oder der Handler hat nichts zurückgegeben |
| 0 Prozent für manche Kunden | Keine Regel hat gepasst; fügen Sie eine Auffangregel hinzu (`*`, keine Kategorien) |
| Fehler zum Attribut der Produktkategorie | `ProductCategoryAttribute` leer oder nicht an der Klasse |
| Hinweis auf ein fehlendes Benutzerland | Das Formular des Konto-Handlers hat kein Land, oder `UserCountryAttribute` ist falsch |

Einzelheiten: [Produktkategorien und USt-Regeln](../katalog/produktkategorien-und-ust-regeln.md).

## Gateway wird nicht angeboten

Das Ereignis Zahlungs-Gateway listet nur registrierte Gateways auf. Prüfen Sie, ob die Erweiterung aktiv ist, die Datei
`classes/<extension>gateway.php` heißt, sie `eZPaymentGatewayType::registerGateway()` aufruft und der INI-Cache geleert
wurde ([Durchgang durch paymentgateways.ini](../settings/paymentgateways-ini-durchgegangen.md#prüfen-ob-ein-gateway-verfügbar-ist)).

## Keine Bestellmail

Siehe [Bestellmails und Belege für Betreiber](../bestellungen/bestellmails-und-belege-fuer-betreiber.md#wenn-eine-mail-nicht-ankommt).
Die Mail wird nur gesendet, wenn die Bestellung aktiviert wird, sodass eine fehlende Bestellung eine fehlende Mail
erklärt.

## Ein Status lässt sich nicht setzen

Die angebotenen Auswahlmöglichkeiten sind die, die die Richtlinien `shop/setstatus` des Benutzers für den aktuellen
Status erlauben ([Statusübergänge und Berechtigungen](../bestellungen/statusuebergaenge-und-berechtigungen.md#wie-die-prüfung-funktioniert)).

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Workflows: Fehlersuche](../../../anleitungen/workflows.md#9-fehlerbehebung)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
