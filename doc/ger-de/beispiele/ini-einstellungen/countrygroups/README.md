---
title: countrygroups.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von countrygroups.ini.
keywords: Beispiele, Referenz, countrygroups.ini, examples, reference
type: generated
generated: exp:help:examples
---
# countrygroups.ini

Die ausgelieferten Einstellungen von `settings/countrygroups.ini`: 1 Blöcke, 4 Einstellungen.

> Exponential-Konfigurationsdatei für die gruppierte Länderliste  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/countrygroups.ini.append oder  
>       settings/override/countrygroups.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host.  
>
> Jede Länderauswahl (das Länderattribut, das Kontoformular des Shops, die  
> Mehrwertsteuerregeln, die Länder-Werkzeugleiste des Benutzers) zeigt zuerst diese Gruppen, jede in der  
> hier angegebenen Reihenfolge, dann „Alle Länder“ von A bis Z nach dem übersetzten Namen.  
> Die Länder selbst kommen aus country.ini; ein Code, der dort nicht steht, wird  
> übersprungen. Diese Einstellungen stehen nicht in country.ini, weil jede Gruppe dieser  
> Datei als Land gelesen wird.  
>
> Die Rangfolge der Standardliste ist erklärt in  
> doc/eng-us/features/6.0/country-groups.md  

| Block | Einstellungen |
|---|---|
| [CountrySettings](CountrySettings.md) | 4 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/countrygroups.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
