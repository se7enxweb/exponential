# Romansh (roh-CH): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Romansh (rumantsch) in the common written standard **Rumantsch Grischun**, the form of the Swiss Confederation, the
Canton of Grisons and the Romansh software localisations (Lia Rumantscha, Pledari Grond for the terms), in a neutral,
polite register. No idiom (Sursilvan, Vallader ...) is mixed in. No other locale falls back to `roh-CH`.

- Write real Romansh, never leave the German, Italian or English word where Rumantsch Grischun has a term:
  "datoteca", "pled-clav", "ordinatur", "colliaziun", "utilisader", "stizzar", "memorisar".
- Spelling of Rumantsch Grischun: -ziun (configuraziun, translaziun), tg/tsch/gl/gn (modifitgar, tschertgar, glista,
  agiuntar), the participle in -à/-ì/-ida (memorisà, stizzà, creà, publitgà).
- Short, plain sentences.

## 2. Form of address

- The user is addressed with the polite **Vus** (capital V, verb in the 2nd person plural), as official Romansh
  texts do: "Vulais Vus propi stizzar quest object?", "Vus n'avais betg la permissiun da ...".
- **Buttons, menu items and links**: the infinitive: Memorisar, Interrumper, Stizzar, Allontanar, Modifitgar,
  Agiuntar, Crear, Tschertgar, Tscherner, Trametter, Mussar, Publitgar, Chargiar si, Telechargiar, Applitgar,
  Restaurar.
- **Titles, column headings and tabs**: nouns: Parameters, Translaziuns, Lieu principal, Num.
- **Instructions**: the imperative of Vus: "Tschernai in object e cliccai sin \"OK\".".
- **Confirmations and questions**: "Vulais Vus propi ...?".
- **What the user can or may do**: "Vus pudais ...", "Vus na pudais betg ..."; the negation is na ... betg.
- **Messages about a result**: the passive: "Il sboz è vegnì memorisà.", "La datoteca è vegnida chargiada si.".

## 3. Script and typography

- **Latin** alphabet with à è é ì ò ù ö ü, as composed characters.
- **Capitals**: only the first word of a button, title or sentence, proper names and the polite Vus/Voss.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. The elided article (l', d') is never glued to a placeholder: write
  "la datoteca %name", not "l'%name".
- **Apostrophe**: the straight ' of the elisions (l'utilisader, d'objects, s'annunziar).
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Romansh | Note |
|---|---|---|
| content | cuntegn | content object: object da cuntegn |
| object | object | plural: objects |
| node | nod | plural: nods; main node: nod principal; parent node: nod superiur |
| location | lieu | main location: lieu principal |
| item (list entry) | element | |
| tree | arbra | content structure: structura dal cuntegn |
| class | classa | content class: classa da cuntegn; class group: gruppa da classas |
| attribute | attribut | |
| datatype | tip da datas | |
| version | versiun | version history: istorgia da las versiuns |
| draft | sboz | store draft: Memorisar il sboz |
| publish | publitgar | published: publitgà |
| send for publishing | trametter per publitgar | |
| discard | refusar | |
| siteaccess | access a la website | siteaccess as a setting name stays in code |
| site, website | website | plural: websites |
| section | secziun | an INI section: secziun INI |
| role | rolla | |
| policy | directiva | |
| limitation | restricziun | |
| permission | permissiun | access denied: access refusà |
| module | modul | |
| function | funcziun | |
| user / user group | utilisader / gruppa d'utilisaders | |
| anonymous | anonim | |
| administrator | administratur | administration interface: interfatscha d'administraziun |
| username | num d'utilisader | |
| password | pled-clav | |
| log in / log out | s'annunziar / s'deconnectar | |
| e-mail | e-mail | e-mail address: adressa d'e-mail |
| cache | memoria intermediara | clear cache: Svidar la memoria intermediara |
| template | model | override template: model da surscriver |
| override | surscriver | |
| fallback | da reserva | fallback language: lingua da reserva |
| design | concept grafic | |
| workflow | decurs da lavur | |
| trigger | activader | |
| event | eveniment | |
| trash | chanaster da ruments | empty trash: Svidar il chanaster da ruments |
| bookmark | segnapagina | |
| settings | parameters | configuration: configuraziun |
| setup | configuraziun | the Setup tab: Configuraziun |
| layout | disposiziun | Exponential Layouts stays |
| block / zone | bloc / zona | |
| extension | extensiun | |
| package | pachet | |
| search | tschertgar | search index: index da tschertga |
| log | protocol | log file: datoteca da protocol |
| queue | fila da spetga | |
| job / task | incumbensa | cronjob: incumbensa cron |
| handler | tractader | never the English word |
| gateway (payment) | portal da pajament | |
| feed (RSS) | fluss | |
| repository | deposit | |
| kernel, core | nucleus | |
| server | server | |
| database | banca da datas | |
| folder / directory | ordinatur | |
| file | datoteca | |
| image | maletg | thumbnail: maletg en miniatura |
| preview | prevista | |
| upload / download | chargiar si / telechargiar | |
| export / import | exportar / importar | |
| remove | allontanar | take out of a list or place |
| delete | stizzar | destroy |
| edit | modifitgar | |
| editor | editur | person or software |
| save / store | memorisar | the verb "Store" of the forms |
| Store (shop tab) | Butia | the shop, as "Shop" |
| cancel | interrumper | |
| OK | OK | |
| apply | applitgar | |
| back | enavos | |
| next / previous | proxim / precedent | |
| enable / disable | activar / deactivar | |
| active / inactive | activ / inactiv | |
| run | exequir | |
| start / stop | cumenzar / fermar | |
| session | sesida | |
| hidden / visible | zuppà / visibel | hide / reveal: zuppentar / mussar |
| archive | archiv | |
| order (sort) | urden | sort order: urden da zavrar |
| order (shop) | empustaziun | |
| shop | butia | |
| basket | chanaster | |
| product | product | |
| customer | cliant | |
| discount | rabat | |
| VAT | TPV | taglia sin la plivalur |
| price / currency | pretsch / valuta | |
| tag | etichetta | |
| translation | translaziun | |
| language | lingua | main language: lingua principala |
| locale | locale | as a setting; in prose: parameters regiunals |
| state | stadi | the object state |
| status | status | |
| view (button) | mussar | a module view (noun): vista |
| sort | zavrar | |
| filter | filter | to filter: filtrar |
| select | tscherner | selected: tschernì |
| copy / move | copiar / spustar | |
| add / create / new | agiuntar / crear / nov | |
| restore | restaurar | |
| install / uninstall | installar / deinstallar | |
| related objects | objects colliads | relation: relaziun |
| dashboard | survista | |
| identifier / ID | identificatur / ID | |
| priority | prioritad | |
| owner | possessur | |
| modified / created | modifitgà / creà | |
| date / time | data / ura | |
| error / warning | errur / avertiment | |
| help | agid | |
| notification | avis | |
| account | conto | |
| default | standard | |
| link | colliaziun | |
| none / all | nagin / tut | |
| yes / no | gea / na | |
| name / title / description | num / titel / descripziun | |
| value / type / list | valur / tip / glista | |
| page / path | pagina / percurs | |
| required / optional | obligatoric / facultativ | |
| options | opziuns | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Butia, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Memorisar.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (S'annunziar).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, OK ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
