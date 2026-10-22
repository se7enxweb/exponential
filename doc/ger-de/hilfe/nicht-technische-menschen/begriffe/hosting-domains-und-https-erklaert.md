---
title: Hosting, Domains und HTTPS erklärt
audience: non-technical, customer
category: plain-language
order: 17
summary: Die drei Dinge, die jede Website neben Exponential braucht - einen Server, auf dem sie läuft, einen Domainnamen, unter dem sie zu finden ist, und HTTPS für das Schloss - erklärt mit dem Vergleich eines Hauses, wer jeweils was bereitstellt, wie alles zusammenhängt und was Sie in Ihrer eigenen Hand behalten sollten.
keywords: Hosting, Hoster, Server, Domainname, Registrierungsstelle, DNS, HTTPS, Zertifikat, Schloss, E-Mail, Datenbank, SQLite, erklärt, Eigentum, hosting, host, server, domain name, registrar, certificate, padlock, e-mail, database, explained, ownership
type: help
---
# Hosting, Domains und HTTPS erklärt

## Inhalt

1. [Der Vergleich: ein Haus](#der-vergleich-ein-haus)
2. [Hosting: wo die Website wohnt](#hosting-wo-die-website-wohnt)
3. [Der Domainname: die Adresse](#der-domainname-die-adresse)
4. [HTTPS: das Schloss](#https-das-schloss)
5. [Wie die drei zusammenhängen](#wie-die-drei-zusammenhängen)
6. [Behalten Sie das in Ihrer eigenen Hand](#behalten-sie-das-in-ihrer-eigenen-hand)

## Der Vergleich: ein Haus

Eine Website ist wie ein Haus. **Hosting** ist das Grundstück und das Gebäude. Der **Domainname** ist die Anschrift,
unter der die Menschen es finden. **HTTPS** ist das Schloss an der Haustür, das die Gespräche drinnen privat hält.
Exponential ist die Einrichtung und der Haushalt, die das Haus nützlich machen. Exponential braucht alle drei, stellt
aber selbst keines davon bereit.

## Hosting: wo die Website wohnt

Eine Website läuft auf einem Computer, der immer eingeschaltet und mit dem Internet verbunden ist: einem **Server**.
Ein **Hosting-Unternehmen** vermietet Ihnen einen oder einen Teil davon und hält ihn am Laufen.

Für Exponential braucht der Server PHP (die Sprache, in der Exponential geschrieben ist), einen Ort, um die Inhalte zu
speichern (eine **Datenbank**), und ein Webserver-Programm ([Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
Sie müssen das nicht verstehen, aber es erklärt zwei Dinge, die ein Hoster vielleicht sagt:

- **"SQLite reicht für Sie."** SQLite hält die ganze Datenbank in einer Datei, ohne dass ein Datenbankserver
  eingerichtet werden muss ([Unterstützung für SQLite-Datenbanken](../../../funktionen/6.0/sqlite-datenbank.md)). Für eine
  kleine Website hält das die Dinge einfach und günstig.
- **"Wir betreiben es mit Velocity."** Exponential Velocity ist das eigene, schnelle Webserver-Programm von
  Exponential; Apache oder nginx sind übliche Alternativen ([Velocity für Unternehmen](../../unternehmen/zuverlaessigkeit/velocity-fuer-unternehmen.md)).
  Für Sie ist beides in Ordnung.

Wie Sie einen Hoster wählen, steht unter [Hosting-Auswahl](../hosting-auswahl/README.md).

## Der Domainname: die Adresse

Ihren **Domainnamen** (`example.org`) kaufen oder besser mieten Sie jährlich bei einer **Registrierungsstelle** für
Domains. Exponential verkauft keine Domains. Eine Einstellung bei der Registrierungsstelle, **DNS** genannt, lässt den
Namen auf den Server Ihres Hosters zeigen. Wenn Sie zu einem neuen Hoster wechseln, wird diese Einstellung geändert,
und die Adresse bleibt dieselbe.

Dieselben DNS-Einstellungen entscheiden auch, wohin **E-Mails** an `@example.org` gehen. Ihre E-Mail betreibt meist Ihr
Hoster oder ein eigener Mailanbieter, nicht Exponential ([Domainname und E-Mail](../hosting-auswahl/domainname-und-e-mail.md)).

## HTTPS: das Schloss

**HTTPS** bedeutet, dass die Verbindung zwischen einem Besucher und Ihrer Website verschlüsselt ist: Niemand dazwischen
kann Passwörter, Formulareingaben oder Bestellungen lesen. Browser zeigen ein Schloss und warnen Besucher vor Websites
ohne. HTTPS braucht ein **Zertifikat**, das der Hoster auf dem Server einrichtet; viele Hoster erneuern es
automatisch. Die Anmeldung in der Administration und das Annehmen von Zahlungen sollten nie ohne HTTPS geschehen.

## Wie die drei zusammenhängen

```
Visitor types example.org
        │
        ▼
  DNS (at the registrar) says: that is server 203.0.113.10
        │
        ▼
  The host's server answers, with HTTPS (padlock)
        │
        ▼
  Exponential builds the page from your content
```

Also: Der Besucher tippt `example.org`, das DNS bei der Registrierungsstelle nennt den Server, der Server des Hosters
antwortet mit HTTPS, und Exponential baut die Seite aus Ihren Inhalten. Reißt ein Glied dieser Kette, ist die Website
nicht erreichbar, obwohl Exponential in Ordnung ist. Deshalb ist "die Website ist weg" oft zuerst eine Frage an den
Hoster oder die Registrierungsstelle ([Wen Sie anrufen](../wenn-es-hakt/wen-sie-anrufen.md)).

## Behalten Sie das in Ihrer eigenen Hand

Wer auch immer Ihre Website baut, achten Sie darauf, dass:

- der **Domainname** auf den Namen **Ihrer** Organisation registriert ist, mit Ihrer E-Mail-Adresse als Kontakt;
- Sie die **Zugangsdaten zur Registrierungsstelle** haben (oder zumindest wissen, wer sie hat);
- Sie wissen, **wo die Sicherungen liegen**, und eine Kopie bekommen können;
- die Vereinbarung mit Ihrem Hoster sagt, dass Sie Ihre **Dateien und Datenbank** erhalten, wenn Sie gehen.

[Was Ihnen gehört](../../kunden/was-ihnen-gehoert.md) erklärt, warum, und
[Was Sie vor der Unterschrift fragen sollten](../hosting-auswahl/was-sie-vor-der-unterschrift-fragen-sollten.md) macht daraus Fragen.

## Verwandte Seiten

- [Begriffe erklärt](README.md)
- [Drei Wege zu einer Website](../hosting-auswahl/drei-wege-zu-einer-website.md)
- [Einen Hoster wählen](../../kunden/partner-und-hoster/einen-hoster-auswaehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
