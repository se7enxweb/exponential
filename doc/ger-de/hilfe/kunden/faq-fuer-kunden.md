---
title: FAQ für Kunden
audience: customer, businesses
category: customer-success
order: 3
summary: Antworten auf die Fragen, die Website-Betreiber mit Exponential am häufigsten stellen: Kosten und Lizenzen, Upgrades, Backups, wen man anruft, Fehler und Sicherheitsprobleme melden, der Shop, Sprachen, Assistenten und der Wechsel zu einem anderen Anbieter.
keywords: FAQ, Fragen, Antworten, Website-Betreiber, Lizenz, Kosten, Upgrade, Backup, Support, Fehler, Sicherheitsmeldung, Shop, Mehrwertsteuer, DSGVO, Sprachen, Exponential Intelligence, Partner, Hoster, Umzug, questions, answers, site owner, license, cost, upgrade, backup, support, bug, security report, store, VAT, GDPR, languages, partner, host, move
type: help
---
# FAQ für Kunden

Kurze Antworten auf die Fragen, die Website-Betreiber am häufigsten stellen, jeweils mit der Seite, die mehr erklärt.

## Die Software

**Zahlen wir eine Lizenzgebühr für Exponential?**
Nein. Exponential ist freie Software unter der GNU General Public License, und die Nutzung unter der GPL ist kostenlos
(`README.md`, „License“). Sie bezahlen für die Arbeit drumherum: Bau, Hosting, Support und Pflege.
Siehe [Was Ihnen gehört](was-ihnen-gehoert.md).

**Wer macht Exponential?**
Es wird vom Unternehmen 7x geleitet und von der gemeinnützigen Exponential Foundation betrieben; siehe
[Wer ist 7x?](../wer-ist-7x.md) und [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md).

**Welche Version sollten wir betreiben?**
Die gepflegte Linie ist Exponential 6.0; Sicherheitskorrekturen werden mit dem nächsten Release 6.0.x veröffentlicht,
und ältere Hauptlinien werden nicht unterstützt (`SECURITY.md`). Siehe [Release-Linie und Support](gesund-halten/release-linie-und-support.md).

**Unsere Versionsnummer hat sich lange nicht geändert. Ist das ein Problem?**
Das kann es sein. Korrekturen erreichen Sie nur durch Upgrades, weil eine veröffentlichte Version nie geändert wird
(`SECURITY.md`). Fragen Sie Ihren Anbieter nach dem Upgrade-Kalender ([Wie Updates funktionieren](gesund-halten/wie-updates-funktionieren.md)).

## Pflege

**Wer führt die Upgrades durch?**
Meist Ihr Hoster oder Partner; Ihr Teil ist, den Zeitpunkt zu vereinbaren, zu testen und abzunehmen
([Upgrades mit Ihrem Anbieter](gesund-halten/upgrades-mit-ihrem-anbieter.md)).

**Woher wissen wir, dass unsere Backups funktionieren?**
Nur indem Sie eines wiederherstellen. Verlangen Sie mindestens einmal im Jahr und nach jedem Wechsel des Hosters einen
Wiederherstellungstest ([Wiederherstellungstest](gesund-halten/wiederherstellungstest.md)).

**Die Website ist nicht erreichbar. Was tun wir zuerst?**
Folgen Sie [Wenn die Website nicht erreichbar ist](gesund-halten/wenn-die-website-nicht-erreichbar-ist.md): von einem anderen Netz aus prüfen,
den Kontakt auf Ihrer Eskalationsliste anrufen und die Uhrzeit und das Gesehene notieren.

**Wen rufen wir wofür an?**
Fragen zu Inhalten: Ihr eigenes Team oder Ihr Partner. Code, Templates und Funktionen: Ihr Partner. Server, E-Mail,
Zertifikate und Backups: Ihr Hoster. Ein Fehler in Exponential selbst: der Issue-Tracker. Die vollständige Tabelle steht
unter [Wann Sie wen anrufen](partner-und-hoster/wann-sie-wen-anrufen.md).

## Probleme und Meldungen

**Wir haben einen Fehler gefunden. Wo melden wir ihn?**
Zuerst an Ihren Partner, der sagen kann, ob er im Code Ihrer Website oder in Exponential liegt. Fehler in Exponential
gehen an <https://github.com/se7enxweb/exponential/issues>; die Meldeseite der Hilfe (`/exphelp/report`) bereitet die
Meldung vor ([GitHub-Issues](gesund-halten/github-issues.md), [Die Meldeseite der Hilfe](gesund-halten/die-meldeseite-der-hilfe.md)).

**Wir glauben, ein Sicherheitsproblem gefunden zu haben.**
Melden Sie es nicht öffentlich. Schicken Sie es per E-Mail an security@se7enx.com, wie es `SECURITY.md` verlangt, mit
der Version und den Schritten und ohne Passwörter oder personenbezogene Daten ([Vertrauliche Sicherheitsmeldungen](gesund-halten/vertrauliche-sicherheitsmeldungen.md)).

**Die Website zeigt einen Fehler mit einer Referenz. Was ist das?**
Eine Referenz, die Ihr Administrator in den Logs nachschlagen kann. Kopieren Sie sie in Ihre Meldung
([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Shop

**Wo sehen wir, wie es dem Shop geht?**
Im Shop-Dashboard, dem ersten Eintrag des Reiters **Shop** (`doc/eng-us/features/6.0/store-dashboard.md`;
[Shop-Berichte](handel/shop-berichte.md)).

**Erledigt Exponential die Mehrwertsteuer für uns?**
Es wendet die USt-Typen und USt-Regeln an, die Sie einrichten. Welche Sätze und Regeln für Sie richtig sind, ist eine
Frage für Ihre Buchhaltung; die Seiten beschreiben die Software, nicht das Recht ([USt-Typen und USt-Regeln](handel/ust-typen-und-ust-regeln.md)).

**Ein Kunde fragt nach allen Daten, die wir über ihn haben.**
Siehe [DSGVO-Anfragen](handel/dsgvo-anfragen.md) und [Datenexport und Löschung](handel/datenexport-und-loeschung.md).
Diese Seiten sind keine Rechtsberatung.

## Inhalte und Wachstum

**Können Redakteure Seiten ohne Entwickler gestalten?**
Ja, mit Exponential Layouts, wo Ihre Website es verwendet ([Landingpages mit Layouts](wachstum/landingpages-mit-layouts.md)).

**Kann die Website für uns schreiben oder übersetzen?**
Exponential Intelligence schreibt Entwürfe, veröffentlicht nie von sich aus und sendet nichts irgendwohin, bis ein
Schlüssel eines Anbieters hinzugefügt wird (`README.md`, „Exponential Intelligence“). Siehe
[Exponential Intelligence für Betreiber](wachstum/exponential-intelligence.md).

**Können wir später eine Sprache oder eine zweite Website hinzufügen?**
Ja; beides sind Projekte mit Ihrem Partner ([Eine Sprache hinzufügen](wachstum/eine-sprache-hinzufuegen.md),
[Eine Website hinzufügen](wachstum/eine-website-hinzufuegen.md)).

**Kann eine App unsere Inhalte lesen?**
Ja, über die REST-API ([Die REST-API für Betreiber](wachstum/rest-api-fuer-betreiber.md)).

## Anbieter

**Können wir unseren Partner oder Hoster wechseln?**
Ja. Die Software ist frei, und jeder Anbieter kann daran arbeiten. Bereiten Sie sich mit der [Checkliste für die Übergabe](partner-und-hoster/checkliste-uebergabe.md) vor.

**Was sollte unsere Support-Vereinbarung enthalten?**
Siehe [Verträge und SLAs](partner-und-hoster/vertraege-und-slas.md) und die [SLA-Vorlage](partner-und-hoster/sla-vorlage.md).
Es sind Vorlagen, keine Rechtsberatung.

## Verwandte Seiten

- [Glossar für Kunden](glossar-fuer-kunden.md)
- [Kunden: hier beginnen](kunden-hier-beginnen.md)
- [Shop-FAQ](handel/shop-faq.md)
- [FAQ für Unternehmen](../unternehmen/faq-fuer-unternehmen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
