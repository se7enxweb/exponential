---
title: "Kundenwebsites als statische Websites ausliefern"
audience: website-creator, editor
category: website-creation
order: 751
summary: Die Website eines Kunden in Exponential bauen, die Redaktionsinstallation privat halten und eine schnelle statische öffentliche Website auf GitHub Pages oder einem anderen Git-Host übergeben - mit Einrichtung, Übergabe und dem, was Sie dem Kunden sagen.
keywords: Website-Ersteller, Agentur, Freiberufler, Kundenwebsite, Übergabe, Wartung, kostenloses Hosting, statische Website, Statisches Veröffentlichen, website creator, agency, client site, handover, free hosting, static site, GitHub Pages, gh-pages, Jekyll alternative, Netlify, CNAME, custom domain, deploy, static publishing
type: help
---
# Kundenwebsites als statische Websites ausliefern

Viele Kundenwebsites sind Visitenkarten, Portfolios oder Veranstaltungsseiten, die sich ab und zu ändern. Für diese
können Sie wie gewohnt in Exponential bauen und die öffentliche Website als statische Kopie ausliefern: keine
Hosting-Rechnung für den Kunden, fast keine Sicherheitswartung, und die Redaktionsinstallation kann auf einem Server
bleiben, den nur die Redakteure des Kunden erreichen.

## Was Sie am Ende haben

- Die öffentliche Website des Kunden auf GitHub Pages (oder einem anderen Git-Host) unter der Domain des Kunden.
- Eine Redaktionsinstallation, die der Kunde wie bisher nutzt, mit einer Schaltfläche **Veröffentlichen** oder
  automatischem Veröffentlichen.
- Ein Repository, das dem Kunden gehört, mit einem Tag für jede Veröffentlichung.

## Passt das Projekt?

Klären Sie es mit dem Kunden, bevor Sie ein Angebot machen: [Passt statisch zu meiner Website?](../../statisches-veroeffentlichen/passt-statisch-zu-meiner-website.md)
Ein Shop, Buchungen, Mitgliederbereiche oder viele Formulare brauchen einen Server; eine statische Kopie ist dann das falsche Werkzeug.

## Die Einrichtung

1. Bauen Sie die Website wie gewohnt. Verwenden Sie `ezurl` und `ezdesign` für Links, damit der Build sie umschreiben kann.
2. Lassen Sie den Kunden das GitHub-Konto oder die Organisation und das Repository anlegen und Ihnen Zugriff geben. Die
   Website darf nie an Ihrem persönlichen Konto hängen.
3. Legen Sie das Ziel mit dem Assistenten an, mit einem Deploy Key oder einem Token, der nur dieses Repository erreicht.
   Siehe [Mit GitHub verbinden](../../statisches-veroeffentlichen/mit-github-verbinden-token-key-oder-app.md).
4. Setzen Sie die Domain des Kunden: [Eigene Domain verwenden](../../statisches-veroeffentlichen/eigene-domain-verwenden.md).
5. Entscheiden Sie mit dem Kunden, wer veröffentlicht: die Redakteure selbst oder eine prüfende Person. Vergeben Sie
   die Richtlinien entsprechend: [Wer veröffentlichen darf](../../statisches-veroeffentlichen/wer-veroeffentlichen-darf-rollen-und-richtlinien.md).

## Die Übergabe

Sagen Sie dem Kunden schriftlich:

- wo er bearbeitet, und dass Änderungen erst mit der Veröffentlichung (oder nach dem eingestellten Zeitplan) öffentlich werden;
- was die statische Website nicht kann (Formulare gehen an die Live-Site oder sind ein Link, keine Anmeldung);
- wie man zurückgeht: Setup > Statisches Veröffentlichen > Verlauf > zurücknehmen;
- wem das GitHub-Konto, die Domain und der Token gehören.

Schicken Sie ihm [Was sich für Redakteure ändert, wenn die Website statisch ist](../../was-sich-fuer-redakteure-aendert-wenn-die-website-statisch-ist.md).

## Wartung, die Sie anbieten können

Updates der Redaktionsinstallation, die Benachrichtigungen der Veröffentlichungen im Blick behalten, die jährliche
Verlängerung der Domain. Siehe [Benachrichtigungen und Webhooks](../../statisches-veroeffentlichen/benachrichtigungen-und-webhooks.md).

## Jetzt Ihre eigene erstellen

Probieren Sie es zuerst mit einem Demo-Kunden: [In 15 Minuten eine kostenlose Website](../../statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
