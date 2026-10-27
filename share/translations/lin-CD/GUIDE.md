# Lingala (lin-CD): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Lingala (lingála) of Kinshasa, as used by Radio Okapi, Lingala Wikipedia (ln) and church and school
texts, in a plain, polite register. No other locale falls back to `lin-CD`.

- **Lingala grammar throughout**: ezali / ezalí te (is / is not), -aki (past), esili ko- (has already), ekozala /
  eko- (will), ya (of), na (with, and, in, at), mpo na (for), to (or), kasi (but), soki (if), oyo (this, which),
  wana (that), nyɔ́nsɔ (all), mosusu (other), te at the end of a negative clause.
- **Words**: a Lingala word where one is in common use (lokasa = page, nkómbó = name, lokóta = language, libunga =
  error, lisálisi = help, mokɔ́lɔ = date, ntángo = time). A technical term that Kinshasa speakers say in French
  stays **the French word in French spelling** (fichier, dossier, serveur, base de données, cache, mot de passe is
  translated: liloba ya sekele). Never leave the English word unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **yo** (singular), with the verb forms in o- (okoki, olingi). Never bino for one user.
- **Buttons, menu items and links**: the imperative stem: Bómbá (save), Tiká (cancel), Pangusá (delete), Longolá
  (remove), Bongisá (edit), Bakisá (add), Salá (create), Lukà (search), Ponà (choose), Tindá (send), Talá (view),
  Bimisá (publish).
- **Titles, column headings and tabs**: nouns or noun phrases: Bibongiseli, Mabongoli, Esika ya liboso, Nkómbó.
- **Instructions**: the imperative, with "Svp" or "Palado" where English has "please": "Ponà eloko mpe finá
  \"OK\".", "Palado, zelá tii ntína ya liboso ebombami.". A reference ("see ...") is "talá ...".
- **Confirmations and questions**: "Ozali na ntembe te ete olingi ...?" is too long; use "Olingi solo ...?":
  "Olingi solo kopangusa kalasi oyo?".
- **What the user can or may do**: "Okoki ...", "Okoki ... te". "You do not have permission" is "Ozali na
  ndingisa te ya ...".
- **Messages about a result**: "... esili ..." / passive in -am-: "Brouillon ebombami.", "Fichier etindami malamu.".

## 3. Script and typography

- **Latin script** with the open vowels **ɛ** and **ɔ** (Ɛ, Ɔ as capitals). Tone marks (á, ɛ́, ɔ́) only on the
  buttons and words given here with them and where a word would otherwise be ambiguous; never on French loan words.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after: "biloko %count". The same for code, file names, INI settings and
  product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Lingala | Note |
|---|---|---|
| content | makambo | content object: eloko ya makambo |
| object | eloko | plural: biloko |
| node | nœud | main node: nœud ya liboso; parent node: nœud ya tata |
| location | esika | main location: esika ya liboso |
| item (list entry) | eloko | |
| class | kalasi | the content class; class group: lisanga ya bakalasi |
| attribute | ezaleli | plural: bizaleli |
| datatype | lolenge ya ba-données | |
| version | version | version history: lisolo ya ba-version |
| draft | brouillon | store draft: Bómbá brouillon |
| publish | bimisá | published: ebimisami; publishing: kobimisa |
| send for publishing | tindá mpo ebimisama | |
| discard | bwaká | discard draft: bwaká brouillon |
| archived / pending / rejected | ebombami na archive / ezali kozela / eboyami | |
| siteaccess | siteaccess | kept as the setting name, allowlisted |
| site, website | site | |
| section | eteni | the Exponential section; an INI section: section |
| role | mokumba | |
| policy | mobeko | one rule of a role |
| limitation | ndelo | |
| permission | ndingisa | access denied: ndingisa ya kokɔta epesami te |
| module | module | |
| function | mosala | |
| user / user group | mosaleli / lisanga ya basaleli | |
| group | lisanga | |
| anonymous | ayebani te | anonymous user: mosaleli ayebani te |
| administrator | mokambi | administration (interface): bokambi |
| username | nkómbó ya mosaleli | login (the name): nkómbó ya kokɔta |
| password | liloba ya sekele | |
| log in / log out | kɔtá / bimá | the act: kokɔta / kobima |
| e-mail | e-mail | e-mail address: adresi ya e-mail |
| cache | cache | clear cache: pɛtɔlá cache |
| template | modèle | override template: modèle ya kozwa esika |
| override | kozwa esika | |
| fallback | ya kosunga | fallback language: lokóta ya kosunga |
| design | design | |
| workflow | nzela ya mosala | |
| trigger | ebandisi | |
| event | likambo | |
| trash | kitunga ya bosoto | empty trash: pɛtɔlá kitunga ya bosoto; move to trash: tyá na kitunga ya bosoto |
| bookmark | elembo | |
| settings | bibongiseli | configuration: configuration |
| setup | bobongisi | the Setup tab |
| layout | ndenge ya kotya | Exponential Layouts |
| block / zone | bloc / zone | |
| extension | extension | |
| package | paquet | |
| search | boluki | to search: lukà; search index: index ya boluki |
| log | journal | a log file; never "log in" |
| queue | molɔngɔ | job queue: molɔngɔ ya misala |
| job / task | mosala | |
| script | script | |
| kernel, core | motema | |
| server | serveur | |
| database | base de données | |
| folder / directory | dossier | |
| file | fichier | |
| image | elilingi | thumbnail: elilingi moke |
| preview | kotala liboso | |
| upload / download | tindá / kitisá | upload: tinda fichier; download: kitisa fichier |
| export / import | bimisá / kɔtisá | |
| remove | longolá | take out of a list or place |
| delete | pangusá | destroy; deleted: epangusami |
| edit | bongisá | |
| editor | mobongisi | |
| save / store | bómbá | the verb "Store" of the forms |
| Store (shop tab) | magazini | the shop, as "Shop" |
| cancel | tiká | |
| OK | Malamu | |
| apply | salelá | |
| back | zongá | |
| next / previous | oyo elandi / oyo eleki | |
| enable / disable | pelisá / bomá | enabled / disabled: epeli / ebomami |
| active / inactive | ezali kosala / ezali kosala te | |
| run | salisá | |
| start / stop | bandá / tɛlɛmisá | |
| session | session | |
| hidden / visible | ebombami / emonanaka | hide / reveal: bombá / lakisá |
| archive | archive | |
| order (sort) | molongo | sort order: molongo ya kotya |
| order (shop) | commande | |
| shop | magazini | |
| basket | kitunga | |
| product | ekoli | plural: biloko ya kotɛkɛ |
| customer | mosombi | |
| discount | bokitisi ntalo | |
| VAT | TVA | |
| price / currency | ntalo / mbongo | |
| tag | elembo | an HTML tag: tag HTML |
| translation | libongoli | plural: mabongoli |
| language | lokóta | plural: nkóta; main language: lokóta ya liboso |
| locale | locale | |
| state | ezalela | the object state |
| status | statut | kept apart from ezalela |
| view (button) | talá | a module view (noun): botali |
| format | format | |
| sort | tyá na molongo | |
| filter | filtre | |
| select | ponà | select all: ponà nyɔ́nsɔ; selected: oyo eponami |
| copy / move | kopí / bongola esika | |
| add / create / new | bakisá / salá / ya sika | |
| restore | zongisá | |
| install / uninstall | tyá / longolá | |
| related objects | biloko oyo etali yango | relation: boyokani |
| dashboard | tableau de bord | |
| identifier / ID | elembo ya koyeba / ID | |
| priority | ntína ya liboso | |
| owner | nkolo | |
| modified / created | ebongolami / esalemi | |
| date / time | mokɔ́lɔ / ntángo | |
| error / warning | libunga / likebisi | |
| help | lisálisi | |
| notification | sango | |
| account | compte | |
| default | ya libela | |
| link | lien | |
| none / all | eloko te / nyɔ́nsɔ | |
| yes / no | ɛɛ / te | |
| name / title / description | nkómbó / motó ya likambo / ndimbola | |
| value / type / list | motuya / lolenge / liste | |
| page / path | lokasa / nzela | |
| required / optional | esengeli / ezali mpo na kopona | |
| options | maponi | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is magazini, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is bómbá.
- "Login" in the user, session and audit lists is the user's login name (nkómbó ya kokɔta); the Login button of the
  error page is the act (Kɔtá).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
