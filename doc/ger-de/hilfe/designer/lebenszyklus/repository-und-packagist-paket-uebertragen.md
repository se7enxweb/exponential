---
title: "Das Repository und das Packagist-Paket übertragen"
audience: designer, developer
category: theme-lifecycle
order: 290
summary: Das GitHub-Repository und das Packagist-Paket eines Themes an einen neuen Eigentümer übertragen, ohne Installationen zu zerstören - Übertragung und Weiterleitung bei GitHub, die Frage des Paketnamens, Betreuer bei Packagist und der Update-Hook.
keywords: Repository übertragen, gh repo transfer, Packagist-Betreuer, Paketname, Weiterleitung, Webhook, Eigentum, transfer repository, Packagist maintainer, package name, redirect, webhook, ownership
type: help
---
# Das Repository und das Packagist-Paket übertragen

Diese Seite ist für Designer, die ein Theme übergeben ([Ein Theme übergeben](uebergabe-an-einen-neuen-betreuer.md)).

## Das Repository

GitHub kann ein Repository an einen anderen Nutzer oder eine Organisation übertragen (**Settings > Transfer ownership**).
GitHub leitet dann die alte URL auf die neue weiter, aber sagen Sie Nutzern trotzdem, dass sie ihre Links aktualisieren
sollen; eine Weiterleitung kann abbrechen, wenn jemand später ein Repository mit dem alten Namen anlegt.

Nach der Übertragung:

1. Prüfen Sie, dass der Update-Hook zu Packagist noch funktioniert ([Packagist automatisch aktuell halten](../veroeffentlichung/packagist-automatisch-aktualisieren.md)).
2. Aktualisieren Sie `homepage`, `support` und `Info_url` in den Dateien des Themes im nächsten Release.

## Das Composer-Paket

Der Paketname (`yourvendor/mytheme`) steht in der `composer.json` jedes Nutzers. Zwei Möglichkeiten:

| Möglichkeit | Nutzer | Hinweis |
|---|---|---|
| Den Namen behalten | Ändern nichts | Fügen Sie den neuen Betreuer bei Packagist als Betreuer hinzu; aktualisieren Sie die Repository-URL auf der Paketseite |
| Ein neuer Name unter dem neuen Vendor | Ändern ihr `require` | Kennzeichnen Sie das alte Paket als aufgegeben, mit dem neuen als Nachfolger ([Ein Repository archivieren](ein-repository-archivieren.md)) |

Den Namen zu behalten, ist für Nutzer am einfachsten. Ein neuer Name ist sauberer, wenn der Vendor des neuen Eigentümers
erscheinen muss.

## Schritt für Schritt (Name behalten)

1. Fügen Sie auf Packagist den neuen Eigentümer als Betreuer des Pakets hinzu.
2. Übertragen Sie das GitHub-Repository.
3. Aktualisieren Sie auf der Paketseite die Repository-URL, falls sie nicht automatisch folgt.
4. Machen Sie ein Release vom neuen Ort aus und prüfen Sie, dass Packagist es zeigt.

## Verwandte Seiten

- [Ein Theme bei Packagist einreichen](../veroeffentlichung/bei-packagist-einreichen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
