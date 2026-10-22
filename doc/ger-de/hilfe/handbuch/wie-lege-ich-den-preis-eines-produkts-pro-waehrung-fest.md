---
title: Wie lege ich den Preis eines Produkts in jeder Währung fest?
audience: editor
category: manual
order: 175
summary: In einem Preisfeld mit mehreren Währungen zeigt die Preisliste eine Zeile pro aktiver Währung; automatische Preise folgen dem Wechselkurs, Individuellen Preis einstellen legt einen Preis in einer Währung fest, und Ausgewählte entfernen macht einen individuellen Preis wieder zu einem automatischen.
keywords: Preis in mehreren Währungen, Mehrwährungspreis, individueller Preis, automatischer Preis, Individuellen Preis einstellen, Währung, Wechselkurs, Basiswährung, USt-Typ, Preis inkl. USt, Preis exkl. USt, multi-currency price, multiprice, custom price, auto price, set custom price, currency, exchange rate, base currency, VAT type, price inc. VAT, price ex. VAT
type: help
---
# Wie lege ich den Preis eines Produkts in jeder Währung fest?

Ein Produkt, dessen Klasse ein Feld **Preis in mehreren Währungen** verwendet, hat einen Preis pro Währung. Die meisten
davon werden automatisch aus einem Preis und den Wechselkursen berechnet; Sie legen einen Preis von Hand fest, wo ein
runder Betrag oder ein Marktpreis wichtig ist. Ein einfaches Preisfeld hat nur einen Preis und nimmt an Währungen
nicht teil.

## Wer das tun kann

Redakteure, die das Produkt bearbeiten dürfen. Währungen und Kurse werden im Tab **Shop** mit `shop/administrate`
verwaltet.

## Schritte

1. Bearbeiten Sie das Produkt und gehen Sie zu seinem Preisfeld.
   Die Preisliste zeigt jede aktive Währung mit ihrem Wert; automatische Preise sind mit **Automatisch**
   gekennzeichnet.
2. Wählen Sie den **USt-Typ** und ob die eingegebenen Preise die USt enthalten (**Preis inkl. USt**) oder nicht
   (**Preis exkl. USt**).
3. Um einen Preis in einer Währung festzulegen, wählen Sie sie unter **Währung auswählen**, klicken Sie auf
   **Individuellen Preis einstellen** und geben Sie den Betrag ein.
4. Um einen festgelegten Preis zu ändern, überschreiben Sie seinen Wert.
5. Damit eine Währung wieder den Kursen folgt, haken Sie ihren individuellen Preis an und klicken Sie auf
   **Ausgewählte entfernen**.
6. Klicken Sie auf **Zur Veröffentlichung schicken**.

## Was schiefgehen kann

- **Eine Währung zeigt 0.00.** Die Währung ist neu, und die automatischen Preise wurden noch nicht berechnet. Klicken
  Sie unter **Shop** > **Währungen** auf **Auto-Preis aktualisieren** (nach **Auto-Umrechnungsfaktoren
  aktualisieren**, wenn die Kurse von einem Anbieter kommen).
- **Eine Währung fehlt in der Liste.** Sie ist inaktiv. Aktivieren Sie sie unter **Shop** > **Währungen**.
- **Automatische Preise haben sich von selbst geändert.** Sie folgen dem Wechselkurs, sobald die Kurse und die
  automatischen Preise aktualisiert werden. Setzen Sie einen individuellen Preis, wo der Betrag bleiben muss.
- **Alle automatischen Preise haben sich nach einem Entfernen geändert.** Sie werden aus einem individuellen Preis
  berechnet; wenn Sie diesen Preis entfernen, ändert sich, woran sie sich orientieren. Behalten Sie mindestens einen
  individuellen Preis, meist in der Hauptwährung des Shops.
- **Ein Warenkorb mischt Produkte mit einfachen Preisen und Preisen in mehreren Währungen.** Verwenden Sie für alle
  Produkte eines Shops dieselbe Art von Preisfeld.

## Verwandte Seiten

- [Wie füge ich dem Shop eine Währung hinzu?](wie-fuege-ich-eine-waehrung-hinzu.md)
- [Preise und Währungen](../kunden/handel/preise-und-waehrungen.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
