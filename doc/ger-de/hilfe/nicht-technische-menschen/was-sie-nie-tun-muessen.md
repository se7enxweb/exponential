---
title: Was Sie nie tun müssen
audience: non-technical, customer, editor
category: plain-language
order: 6
summary: Die technischen Aufgaben rund um eine Exponential-Website - Installation, Aktualisierungen, Sicherungen, Domain, HTTPS, Templates, Erweiterungen und Kommandozeile -, wer sie jeweils für Sie erledigt und worum Sie bitten, damit Sie wissen, dass sie erledigt wurden.
keywords: keine Kommandozeile, kein Code, wer macht was, Installation, Aktualisierungen, Updates, Sicherungen, Domain, HTTPS, Templates, Erweiterungen, Composer, Hoster, Entwickler, Partner, Arbeitsteilung, no command line, no code, who does what, installation, updates, backups, templates, extensions, host, developer, partner, division of work
type: help
---
# Was Sie nie tun müssen

Exponential gut zu nutzen verlangt nicht, dass Sie technisch werden. Es verlangt aber, dass jemand Technisches eine
Handvoll Aufgaben erledigt. Diese Seite listet diese Aufgaben auf, sagt offen, dass sie eine Kommandozeile oder Code
erfordern, und sagt Ihnen, worum Sie bitten, damit Sie prüfen können, dass sie erledigt wurden, ohne zu verstehen, wie.

## Inhalt

1. [Die Faustregel](#die-faustregel)
2. [Die Aufgaben, eine nach der anderen](#die-aufgaben-eine-nach-der-anderen)
3. [Worum Sie schriftlich bitten](#worum-sie-schriftlich-bitten)
4. [Dinge, die technisch aussehen, aber Ihre Sache sind](#dinge-die-technisch-aussehen-aber-ihre-sache-sind)
5. [Wenn Sie eines Tages mehr lernen möchten](#wenn-sie-eines-tages-mehr-lernen-möchten)

## Die Faustregel

Wenn etwas **im Browser, in der Administration** geschieht, können Sie es lernen. Wenn es **auf dem Server**
geschieht, über eine Kommandozeile, eine Dateiübertragung oder einen Code-Editor, ist es die Aufgabe von jemand
anderem. Diese Hilfe kennzeichnet solche Aufgaben deutlich, damit Sie nie raten müssen.

## Die Aufgaben, eine nach der anderen

| Aufgabe | Warum sie eine technische Person braucht | Wer sie meist erledigt | Was sie ist |
|---|---|---|---|
| Exponential installieren | Braucht einen Server mit PHP und einer Datenbank; die Dateien werden normalerweise mit dem Werkzeug Composer auf der Kommandozeile installiert, und ein Einrichtungsassistent im Browser schließt die Arbeit ab | Hoster, Entwickler oder Partner | [Anforderungen auf einen Blick](../hosting/grundlagen/anforderungen-auf-einen-blick.md) |
| Aktualisierungen und Sicherheitskorrekturen einspielen | Dieselben Werkzeuge; die Datenbank braucht vielleicht Änderungen; danach werden Caches geleert | Dieselbe Person | [Wie Updates funktionieren](../kunden/gesund-halten/wie-updates-funktionieren.md) |
| Sicherungen und Tests der Wiederherstellung | Kopien der Dateien und der Datenbank, an einem anderen Ort aufbewahrt | Hoster oder Entwickler | [Sicherungen](../kunden/gesund-halten/backups.md), [Test der Wiederherstellung](../kunden/gesund-halten/wiederherstellungstest.md) |
| Domainname und DNS | Bei der Registrierungsstelle der Domain eingestellt | Sie oder Ihr Hoster | [Domainname und E-Mail](hosting-auswahl/domainname-und-e-mail.md) |
| HTTPS-Zertifikat | Auf dem Server eingerichtet | Hoster | [Hosting, Domains und HTTPS erklärt](begriffe/hosting-domains-und-https-erklaert.md) |
| Das Design ändern (Templates, Farben, Schriften) | Template-Dateien und Stylesheets | Designer oder Entwickler | [Erweiterungen erklärt](begriffe/erweiterungen-erklaert.md) |
| Eine Erweiterung hinzufügen (Karten, Newsletter, 2FA ...) | Mit Composer installiert und in den Einstellungen eingeschaltet | Entwickler oder Partner | dieselbe |
| Neue Inhaltstypen oder Felder | Unter **Setup** von einem Administrator geändert, der das Inhaltsmodell kennt | Website-Ersteller | [Ihr Inhaltsmodell entwerfen](../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md) |
| Zu einem anderen Hoster umziehen | Dateien, Datenbank und Einstellungen werden umgezogen | Alter und neuer Hoster | [Zu einem neuen Hoster wechseln](hosting-auswahl/zu-einem-neuen-hoster-wechseln.md) |
| Die Server-Logs lesen | Dateien auf dem Server | Administrator oder Entwickler | [Logs für Betreiber](../kunden/gesund-halten/logs-fuer-betreiber.md) |

Einige dieser Aufgaben lassen sich aus der Administration erledigen, sobald die Website die richtigen Erweiterungen
hat: Eine Website aus Git-Klonen mit aktiven Erweiterungen `git_manager` und `ezupdate` lässt sich zum Beispiel aus der
Administration aktualisieren ([Wie Updates funktionieren](../kunden/gesund-halten/wie-updates-funktionieren.md#die-vier-teile-eines-updates)).
Auch dann sollte die Person, die auf den Knopf drückt, jemand sein, der die Dinge reparieren kann, wenn das Update
schiefgeht. Das sind selten Sie, und das ist in Ordnung.

## Worum Sie schriftlich bitten

Sie müssen die Arbeit nicht verstehen, um zu prüfen, ob sie erledigt wurde. Bitten Sie schriftlich um diese
Ergebnisse:

| Nach | Bitten Sie um |
|---|---|
| Einer Installation | Die Adressen der Website und der Administration, Ihr eigenes Konto und die Bestätigung, dass HTTPS funktioniert |
| Jedem Update | Die neue Versionsnummer (angezeigt unter **Setup > Systeminformationen**), die Bestätigung, dass die Startseite lädt, die Administration sich öffnet und ein Testobjekt veröffentlicht werden konnte ([Wie Updates funktionieren](../kunden/gesund-halten/wie-updates-funktionieren.md#woran-sie-erkennen-dass-ein-update-funktioniert-hat)) |
| Jedem Quartal | Das Datum des letzten Tests der Wiederherstellung und was wiederhergestellt wurde |
| Jeder Änderung an Design oder Erweiterungen | Was sich für Redakteure und was sich für Besucher ändert |

[Was Sie vor der Unterschrift fragen sollten](hosting-auswahl/was-sie-vor-der-unterschrift-fragen-sollten.md) nimmt diese Punkte von
Anfang an in die Vereinbarung auf.

## Dinge, die technisch aussehen, aber Ihre Sache sind

Einige Dinge klingen technisch, sind aber Entscheidungen, die nur Sie treffen können:

- **Wer ein Konto bekommt und welche Rolle.** Ihr Administrator richtet es ein, Sie entscheiden
  ([Rollen und Rechte erklärt](begriffe/rollen-und-rechte-erklaert.md)).
- **Was die Website sagt.** Inhalte, Tonfall, Bilder, die Reihenfolge des Menüs.
- **Wann ein Update stattfinden darf.** Besucher sehen vielleicht kurz eine Wartungsseite
  ([Wartungsmodus und geplante Arbeiten](../kunden/gesund-halten/wartungsmodus-und-geplante-arbeiten.md)).
- **Was zu tun ist, wenn etwas fehlschlägt.** Zur Sicherung zurückkehren oder vorwärts reparieren: Entscheiden Sie
  vorher, nicht währenddessen.
- **Wen Sie anrufen.** Schreiben Sie es jetzt auf ([Wen Sie anrufen](wenn-es-hakt/wen-sie-anrufen.md)).

## Wenn Sie eines Tages mehr lernen möchten

Nichts auf dieser Seite ist geheim. Jede Aufgabe oben ist in dieser Hilfe dokumentiert, und Exponential ist freie
Software, die Sie studieren dürfen. Wenn die Neugier siegt, ist [Mit Git entwickeln](../mit-git-entwickeln.md) der
empfohlene erste Schritt vom Benutzer zum Entwickler, und der Kurskatalog im Bereich [Trainer](../trainer/trainer-hier-beginnen.md)
zeigt, wie die Themen Schritt für Schritt vermittelt werden. Aber Sie *müssen* es nie.

## Verwandte Seiten

- [Menschen ohne technische Vorkenntnisse: hier beginnen](nicht-technische-menschen-hier-beginnen.md)
- [Drei Wege zu einer Website](hosting-auswahl/drei-wege-zu-einer-website.md)
- [Wer macht was](../kunden/partner-und-hoster/wer-was-macht.md)
- [Was Ihnen gehört](../kunden/was-ihnen-gehoert.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
