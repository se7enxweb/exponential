---
title: Was Ihnen gehört
audience: customer, businesses
category: customer-success
order: 5
summary: Was ein Kunde von Exponential besitzt und kontrolliert: die Lizenz der Software, Ihre Inhalte und Daten, der für Sie geschriebene Code, die Konten, die Domain und die Backups, und die Fragen, die Sie mit Ihrem Partner und Hoster klären sollten, damit Sie immer wechseln oder weitermachen können.
keywords: Eigentum, Lizenz, GPL, freie Software, Inhalte, Daten, Code, Erweiterungen, Domain, Konten, Backups, Übergabe, Lock-in, Ausstieg, ownership, license, free software, content, data, code, extensions, domain, accounts, backups, handover, lock-in, exit
type: help
---
# Was Ihnen gehört

Eine Website zu besitzen bedeutet mehr, als für sie zu bezahlen. Wenn ein Partner geht, ein Hoster schließt oder ein
Vertrag endet, möchten Sie weitermachen können: mit denselben Inhalten, denselben Adressen und am besten demselben Code.
Diese Seite listet auf, was Ihnen gehört oder was Sie kontrollieren, und die Fragen, die Sie klären sollten, damit es so
bleibt.

> **Hinweis**
> Diese Seite beschreibt die Lizenz der Software und gute Praxis. Sie ist keine Rechtsberatung. Ihre Verträge legen
> fest, wem der für Sie geschriebene Code gehört und was ein Anbieter übergeben muss.

## Die Software: frei, unter der GNU GPL

Exponential ist freie Software. Die `README.md` des Repositorys („License“) sagt, dass sie unter der GNU General Public
License Version 2 lizenziert ist, dass die Nutzung unter der GPL kostenlos ist und dass die alte professionelle Lizenz
nicht mehr erhältlich ist. Der Lizenztext steht in der Datei `LICENSE`.

Was das für Sie als Kunde bedeutet:

- **Keine Lizenzgebühr** für die Software selbst.
- **Das Recht, sie zu nutzen, zu studieren, zu ändern und weiterzugeben**, unter den Bedingungen der GPL.
- **Keine Abhängigkeit von einem einzigen Anbieter** für das Recht, sie zu betreiben: Jeder Partner oder Hoster kann an
  Ihrer Installation arbeiten.

Die Hilfe, die Sie gerade lesen, ist freie Dokumentation unter der GNU Free Documentation License 1.3 oder später
([Freie Dokumentation](../freie-dokumentation.md)).

## Ihre Inhalte und Ihre Daten

Ihre Artikel, Produkte, Seiten, Bilder und Dateien sowie die Daten, die Menschen auf Ihrer Website hinterlassen (Konten,
Bestellungen, Formulareinsendungen), werden in der Datenbank und im Speicherverzeichnis Ihrer Installation gespeichert.
Stellen Sie sicher, dass:

- Sie wissen, **wo** sie gespeichert sind (welcher Hoster, welches Land) ([Was Sie Ihren Hoster fragen sollten](gesund-halten/was-sie-ihren-hoster-fragen-sollten.md));
- es **Backups** gibt, die Sie einem anderen Anbieter übergeben könnten ([Backups](gesund-halten/backups.md));
- Sie auf Anfrage eine **vollständige Kopie** erhalten können, in einer Form, die ein anderer Anbieter wiederherstellen
  kann, und Ihr Vertrag das festhält ([Verträge und SLAs](partner-und-hoster/vertraege-und-slas.md)).

Personenbezogene Daten in Ihrer Website liegen in Ihrer Verantwortung als die Organisation, die sie betreibt. Siehe
[DSGVO-Anfragen](handel/dsgvo-anfragen.md), was die Software bietet, wenn jemand nach seinen Daten fragt.

## Der für Sie geschriebene Code

Die meisten Websites haben eigenen Code: ein Design, Templates, vielleicht eine Erweiterung mit besonderen Funktionen.
Exponential hält solchen Code in Erweiterungen und Designs, getrennt vom Kernel (`README.md`: „Separation of content and
presentation layer“, „plugin API“). Fragen, die Sie mit Ihrem Partner klären sollten:

| Frage | Warum sie wichtig ist |
|---|---|
| Wem gehört das Urheberrecht an dem für Sie geschriebenen Code, und unter welcher Lizenz erhalten Sie ihn? | Entscheidet, ob ein anderer Partner ihn weiterführen darf |
| Wo wird der Code aufbewahrt (ein Repository, auf das Sie zugreifen können)? | Ohne den Code kann niemand die Website ändern |
| Ist jede Änderung in diesem Repository, oder gibt es Änderungen, die direkt auf dem Server vorgenommen wurden? | Änderungen nur auf dem Server gehen beim nächsten Upgrade oder Umzug verloren |
| Welche Erweiterungen von Drittanbietern verwendet die Website, und unter welchen Lizenzen? | Jede hat ihre eigenen Bedingungen und ihre eigene Pflege |

Die [Checkliste für die Übergabe](partner-und-hoster/checkliste-uebergabe.md) macht daraus eine Liste, die Sie abarbeiten,
bevor ein Anbieter wechselt.

## Konten, Domain und Zertifikate

- **Administratorkonten**: Mindestens eines gehört jemandem in Ihrer Organisation, nicht nur dem Partner
  ([Administratorkonten und Zugriff](gesund-halten/administratorkonten-und-zugriff.md)).
- **Der Domainname** ist auf den Namen Ihrer Organisation registriert, und Sie können auf das Konto beim Registrar zugreifen.
- **Konten bei Drittanbietern** (Zahlungsanbieter, E-Mail-Zustellung, Analyse, ein Schlüssel für einen KI-Anbieter)
  laufen nach Möglichkeit auf den Namen Ihrer Organisation, damit ein Partnerwechsel sie nicht abschneidet.

## Veröffentlichte Versionen ändern sich nicht

Die Releases von Exponential sind veröffentlichte Versionen, die sich nie ändern: „Ein veröffentlichtes Tag wird nie
geändert: Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag erreicht Sie als nächstes Release, nicht
als korrigierte Kopie des alten“ (`SECURITY.md`). Für einen Betreiber bedeutet das, dass die Versionsnummer, die Ihr
Anbieter nennt, genau ist: Zwei Installationen derselben Version führen denselben Code aus.

## Verwandte Seiten

- [Checkliste für die Übergabe](partner-und-hoster/checkliste-uebergabe.md)
- [Eine Website übernehmen](partner-und-hoster/eine-website-uebernehmen.md)
- [Lizenzierung für Käufer](../geschaeft/lizenzen-fuer-kaeufer.md)
- [Wer ist 7x?](../wer-ist-7x.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
