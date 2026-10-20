---
title: Sicherheitsmeldungen bleiben vertraulich
audience: advocate
category: advocacy
order: 104
summary: Was ein Fürsprecher tut, wenn jemand eine Sicherheitslücke in Exponential erwähnt - die öffentliche Diskussion stoppen, sie vertraulich an die Adresse in SECURITY.md oder die vertrauliche Meldung bei GitHub leiten, sie nie auf den Websites anderer nachstellen, nie Termine versprechen - mit Nachrichtenvorlagen.
keywords: Sicherheit, Sicherheitslücke, verantwortungsvolle Offenlegung, vertrauliche Meldung, SECURITY.md, security@se7enx.com, vertrauliche Meldung von Sicherheitslücken bei GitHub, Fürsprecher, Vorlagen, security, vulnerability, responsible disclosure, private report, GitHub private vulnerability reporting, advocate, templates
type: help
---
# Sicherheitsmeldungen bleiben vertraulich

Diese Seite ist für jeden Fürsprecher. Früher oder später erzählt Ihnen jemand von einer möglichen Sicherheitslücke, in
einem Forum, einem Chat oder nach einem Vortrag. Was Sie in den nächsten Minuten tun, zählt: Eine öffentliche
Sicherheitslücke kann gegen jede Website missbraucht werden, bevor es eine Korrektur gibt.

## Inhalt

1. [Die Regel](#die-regel)
2. [Der vertrauliche Weg](#der-vertrauliche-weg)
3. [Schritt für Schritt](#schritt-für-schritt)
4. [Nachrichtenvorlagen](#nachrichtenvorlagen)
5. [Was Sie nie tun](#was-sie-nie-tun)

## Die Regel

> Sicherheitsprobleme werden vertraulich gemeldet, nie in einem öffentlichen Tracker, Forum, Chat oder Beitrag.

„Bitte melden Sie Sicherheitsprobleme vertraulich per E-Mail an security@se7enx.com, nicht im öffentlichen
Issue-Tracker oder in den Diskussionen. Sie können eine erste Antwort innerhalb eines Werktags erwarten“
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Die vertrauliche Meldung von
Sicherheitslücken des Repositorys bei GitHub wird ebenfalls angenommen ([Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md#security-problems)).

## Der vertrauliche Weg

Die Meldung sollte laut SECURITY.md enthalten:

- die Version (`composer show se7enxweb/exponential` oder das Tag oder den Commit) und die PHP-Version;
- die Engine (Exponential Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM) und die Datenbank;
- was ein Angreifer tun kann, und die Schritte zum Nachstellen.

Und sie sollte Passwörter, Geheimnisse und personenbezogene Daten **weglassen**.

Was danach geschieht, laut SECURITY.md: Die meldende Person erfährt, ob die Meldung angenommen oder abgelehnt wird, und
wird auf dem Laufenden gehalten; ein angenommenes Problem wird auf `main` behoben und mit der nächsten Version 6.0.x
veröffentlicht.

## Schritt für Schritt

1. **Stoppen Sie die Verbreitung.** Bitten Sie die Person freundlich, keine weiteren Details öffentlich zu
   veröffentlichen; wenn Details schon öffentlich sind, bitten Sie, sie zu entfernen.
2. **Leiten Sie es weiter.** Geben Sie ihr die vertrauliche Adresse oder die vertrauliche Meldung bei GitHub. Lassen Sie
   **sie** melden; sie hat es gefunden.
3. **Wenn sie nicht kann oder nicht will** und Sie die Details haben, melden Sie es selbst vertraulich, nennen Sie sie
   als Finderin oder Finder und sagen Sie ihr, dass Sie es getan haben.
4. **Vergessen Sie die Details in der Öffentlichkeit.** Besprechen Sie sie in keinem Kanal, auch nicht nach einer
   Korrektur, über das hinaus, was die Release Notes sagen.
5. **Testen Sie es nicht auf Websites, die Ihnen nicht gehören.** Die Website eines anderen zu testen kann illegal und
   schädlich sein.

## Nachrichtenvorlagen

```text
IN A PUBLIC THREAD
Thanks for spotting this! Could you remove the details here and send them privately as SECURITY.md
describes (security@se7enx.com, or GitHub's private vulnerability reporting)? That way it can be fixed
before anyone misuses it. You'll get a first answer within one business day.

IN PRIVATE
Thanks for telling me. The right place is security@se7enx.com (or the private vulnerability reporting
on the GitHub repository). Please include the version, PHP, engine, database, what an attacker could do
and the steps - and no passwords or personal data. I won't share what you told me.
```

## Was Sie nie tun

- Details einer Sicherheitslücke veröffentlichen, teilen oder weiterleiten.
- Ein öffentliches Issue dazu eröffnen.
- Einen Termin oder ein Ergebnis für eine Korrektur versprechen.
- Den Schweregrad öffentlich beurteilen („das ist nichts“, „das ist kritisch“).
- Eine Belohnung verlangen oder anbieten; die Quellen beschreiben kein Bounty-Programm.

## Verwandte Seiten

- [Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)
- [Für Sicherheit eintreten](fuer-sicherheit-eintreten.md)
- [Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
