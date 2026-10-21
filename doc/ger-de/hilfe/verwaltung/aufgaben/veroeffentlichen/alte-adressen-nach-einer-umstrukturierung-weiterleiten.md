---
title: Alte Adressen nach einer Umstrukturierung weiterleiten
audience: government, editor, admin
category: public-sector
order: 370
summary: Für Redakteure und Web-Verantwortliche - alte Adressen funktionsfähig halten, nachdem Seiten umbenannt, verschoben, zusammengelegt oder entfernt wurden - was Exponential von selbst weiterleitet, Aliase für einzelne Seiten, URL-Platzhalter für Gruppen mit Eine Adresse ausprobieren, und eine Testliste.
keywords: Weiterleitung, alte Adressen, Umstrukturierung, Relaunch, URL-Alias, URL-Platzhalter, URL umleiten, Eine Adresse ausprobieren, 301, verschobene Inhalte, entfernte Seiten, defekte Links, redirect, old addresses, reorganization, relaunch, URL alias, URL wildcards, Redirecting URL, Try an address, moved content, removed pages, broken links
type: help
---
# Alte Adressen nach einer Umstrukturierung weiterleiten

Diese Seite ist für Redakteure und Web-Verantwortliche nach einer Umstrukturierung: Abteilungen zusammengelegt, eine
Sektion umbenannt, alte Seiten außer Dienst gestellt. Lesezeichen, gedruckte Faltblätter und andere Sites verweisen auf
die alten Adressen. Exponential behält Weiterleitungen für umbenannte und verschobene Seiten von selbst; entfernte
Seiten und ganze Muster brauchen Sie
([Weiterleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md),
[URLs und Aliase, 4](../../../../anleitungen/urls-und-aliase.md#4-url-platzhalter)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

| Änderung | Brauchen Sie eine Weiterleitung? |
|---|---|
| Eine Seite umbenannt | nein: Der alte Alias bleibt und leitet weiter |
| Eine Seite verschoben | nein: Der alte Pfad leitet weiter |
| Eine Seite entfernt | ja: Die Adresse antwortet „nicht gefunden“ |
| Eine Gruppe von Seiten auf eine andere Site verlagert | ja: ein Platzhalter zur anderen Site |

Erstellen Sie eine Liste der alten Adressen, auf die es ankommt (aus der alten Sitemap, den Suchstatistiken oder den
Logs des Servers), mit ihrem neuen Ziel.

## Schritte

**Eine einzelne entfernte Seite:**

1. Öffnen Sie die Ersatzseite, ihre Seite **URL-Aliase**, **Neuer Alias** mit dem alten Pfad als **URL alias name**,
   **Alias im Hauptverzeichnis erstellen**, wenn der alte Pfad an der Wurzel begann, **Der Alias soll zum Ziel
   weiterleiten** markiert, **Erstellen** ([Einer Seite eine kurze Adresse geben](einer-seite-eine-kurze-adresse-geben.md)).

**Eine Gruppe von Adressen:**

1. **Setup > URL Platzhalter**.
2. **Neuer URL Platzhalter**: das Muster, zum Beispiel `old-department/*`; **Ziel**: `new-department/{1}`; markieren
   Sie **URL umleiten** (eine Weiterleitung mit 301). Legen Sie ihn an.
3. **Eine Adresse ausprobieren**: Tippen Sie eine alte Adresse ein, zum Beispiel `old-department/contact`; die Seite
   sagt, auf welchen Platzhalter sie passt und was daraus wird, ohne etwas zu ändern.

**Eine Adresse, die zu einer Modulseite führen soll** (Suche, eine Formularansicht): **Setup > URL-Übersetzer**, ein
globaler Alias mit Weiterleitung.

## Das Ergebnis prüfen

- [ ] Für jede Adresse auf Ihrer Liste: Öffnen Sie sie abgemeldet (oder `curl -sI <address>`): eine 301 an die
  richtige Stelle.
- [ ] Beobachten Sie in den Wochen danach das Fehler-Log und die Crawling-Berichte der Suchmaschinen auf Fehlgriffe.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Ein Platzhalter, der mehr erfasst als beabsichtigt | Platzhalter werden nur versucht, wenn kein Alias passt, in der Reihenfolge ihrer Anlage; testen Sie mit **Eine Adresse ausprobieren** |
| Weiterleitungsketten (alt zu älter zu neu) | verweisen Sie direkt auf die endgültige Adresse |
| Alles auf die Startseite weiterleiten | Besucher verlieren die Orientierung; schicken Sie sie zur nächstgelegenen passenden Seite |
| Seiten ohne Plan entfernen | entscheiden Sie das Ziel vor dem Entfernen |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Seite verschieben](eine-seite-verschieben.md)
- [URL-Platzhalter](../../../../../eng-us/help/creators/website-creators/url-wildcards.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
