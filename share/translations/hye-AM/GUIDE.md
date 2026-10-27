# Armenian (hye-AM): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Eastern Armenian (արևելահայերեն), the language of the Republic of Armenia, in the reformed orthography
used there (ուղղագրություն of 1922/1940: յուրաքանչյուր, ոչ թե իւրաքանչիւր). The register is the neutral, technical
one of current Armenian software (the Armenian localisations of Windows, Microsoft Office, Google and Mozilla) and
of the Armenian Wikipedia. Western Armenian and the classical orthography are not used. No other locale falls back
to `hye-AM`.

- Armenian words are used where the software already has them: պահպանել, ջնջել, կարգավորումներ, պանակ,
  ներբեռնել, վերբեռնել, հղում, ներդիր, գաղտնաբառ. Internationalisms of computing keep their usual Armenian form:
  սերվեր, սկրիպտ, մոդուլ, քեշ, ֆայլ, դիտարկիչ (browser).
- No Russian words where Armenian has its own (not "նաստրոյկաներ", "պապկա").

## 2. Form of address

The interface addresses the user with the formal **Դուք** (you), **Ձեր** (your), written with a capital letter as
Armenian software does, and the plural verb forms that go with it.

- **Buttons, menu items and links**: the infinitive, as Armenian software does: Պահպանել, Չեղարկել, Ջնջել,
  Խմբագրել, Հրապարակել, Ուղարկել հրապարակման, Հեռացնել ընտրվածները, Տեղափոխել աղբարկղ. Not the imperative
  (Պահպանեք) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: Կարգավորումներ, Թարգմանություններ, Հիմնական տեղ.
- **Instructions**: the formal imperative: "Ընտրեք տարրը և սեղմեք «Լավ»։", "Խնդրում ենք սպասել ...։". A
  reference ("see ...") is "Տե՛ս ...".
- **Confirmations and questions**: "Համոզվա՞ծ եք, որ ցանկանում եք ...", "Ցանկանո՞ւմ եք ջնջել դասը".
- **What the user can or may do**: "Դուք կարող եք ...", "Դուք չեք կարող ...", or impersonal: "Հիմնական լեզուն
  հնարավոր չէ փոխել ...։". "You do not have permission" is "Դուք ... թույլտվություն չունեք".
- Not allowed: the familiar դու / քո, slash forms.

## 3. Script and typography

- **Armenian script** (Unicode U+0531 to U+0587), lower case with an initial capital where the source has one.
  The ligature և (U+0587) is written as Armenian writes it (և, ոչ թե եվ, inside words too: արևելք).
- **Punctuation, the Armenian marks**:
  - the full stop is the **verjaket ։** (U+0589), never the Latin full stop, at the end of a sentence;
  - the question mark **՞** (U+055E) stands on the stressed vowel of the questioning word, not at the end:
    "Ցանկանո՞ւմ եք ...";
  - the emphasis/exclamation mark **՛** (U+055B) on the stressed vowel where the source has an exclamation or a
    stressed imperative ("Տե՛ս"); a neutral UI rarely needs it;
  - comma `,` and the ellipsis "..." as in Latin script;
  - a **label keeps the Latin colon** ":" (Armenian software does so, and the verjaket would read as a sentence
    end): "Անուն:".
  - Inside code, URLs, placeholders, file names and anything that is not Armenian prose the Latin marks stay.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Latin text inside Armenian**: product names, codes and acronyms stay in Latin letters. An Armenian ending
  after an acronym is joined with a hyphen: PHP-ի, URL-ի կեղծանուն. Often a wording without an ending reads
  better: PHP ֆայլ.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never an Armenian letter or ending glued to a
  placeholder: "%count օբյեկտ", not "%count-ը" or "%countօբյեկտ". Put the placeholder where it needs no ending:
  "Օբյեկտ: %name", "%count տարր".
- **Numbers**: a noun after a number usually stays in the singular: "%count օբյեկտ". Digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Armenian | Note |
|---|---|---|
| content | բովանդակություն | |
| content object | բովանդակության օբյեկտ | |
| object | օբյեկտ | |
| node | հանգույց | main node: հիմնական հանգույց; parent node: ծնող հանգույց; child: զավակ |
| location | տեղ | main location: հիմնական տեղ |
| item (list entry) | տարր | |
| class | դաս | the content class; class group: դասերի խումբ; a PHP class: PHP դաս |
| content class | բովանդակության դաս | |
| attribute | ատրիբուտ | |
| datatype | տվյալների տիպ | |
| version | տարբերակ | version history: տարբերակների պատմություն |
| draft | սևագիր | store draft: պահպանել սևագիրը |
| publish | հրապարակել | published: հրապարակված; publishing: հրապարակում |
| send for publishing | ուղարկել հրապարակման | |
| discard | մերժել | discard draft: մերժել սևագիրը |
| archived | արխիվացված | |
| pending | սպասման մեջ | |
| rejected | մերժված | |
| siteaccess | կայքի մուտքի կետ | the configuration unit |
| site, website | կայք, վեբկայք | |
| section | բաժին | the Exponential section |
| role | դեր | |
| policy | քաղաքականություն | |
| limitation | սահմանափակում | |
| permission | թույլտվություն | access rights: մուտքի իրավունքներ |
| module | մոդուլ | |
| function | ֆունկցիա | |
| user | օգտատեր | |
| user group | օգտատերերի խումբ | |
| group | խումբ | |
| anonymous | անանուն | anonymous user: անանուն օգտատեր |
| administrator | ադմինիստրատոր | |
| administration (interface) | կառավարման միջերես | |
| username | օգտանուն | the column "Login" too |
| password | գաղտնաբառ | |
| log in / sign in | մուտք գործել | the noun: մուտք |
| log out / sign out | դուրս գալ | the noun: ելք |
| e-mail | էլփոստ | e-mail address: էլփոստի հասցե |
| cache | քեշ | clear cache: մաքրել քեշը |
| template | ձևանմուշ | |
| override | վերասահմանում | override template: վերասահմանող ձևանմուշ |
| fallback | պահուստային | fallback language: պահուստային լեզու |
| design | դիզայն | |
| workflow | աշխատահոսք | |
| trigger | տրիգեր | |
| event | իրադարձություն | |
| trash | աղբարկղ | empty trash: դատարկել աղբարկղը |
| bookmark | էջանիշ | |
| settings | կարգավորումներ | |
| setup | կարգաբերում | the Setup tab; installation: տեղադրում |
| layout | դասավորություն | Exponential Layouts |
| block | բլոկ | |
| zone | գոտի | |
| extension | ընդլայնում | |
| package | փաթեթ | |
| search | որոնում | the button: Որոնել |
| log | մատյան | log file: մատյանի ֆայլ |
| queue | հերթ | |
| job / task | առաջադրանք | |
| cron | cron | cron job: cron առաջադրանք |
| script | սկրիպտ | |
| worker | մշակող պրոցես | |
| kernel, core | միջուկ | |
| server | սերվեր | |
| database | տվյալների բազա | |
| folder | պանակ | |
| file | ֆայլ | |
| image | պատկեր | |
| preview | նախադիտում | |
| upload | վերբեռնել | |
| download | ներբեռնել | |
| export / import | արտահանել / ներմուծել | |
| remove | հեռացնել | take out of a list or a place |
| delete | ջնջել | destroy |
| edit | խմբագրել | |
| editor | խմբագրիչ | the person: խմբագիր |
| save / store | պահպանել | |
| Store (shop tab) | Խանութ | the word the shop menu uses for "Shop" |
| cancel | չեղարկել | |
| OK | Լավ | |
| apply | կիրառել | |
| back | հետ | |
| next / previous | հաջորդ / նախորդ | |
| enable / disable | միացնել / անջատել | |
| enabled / disabled | միացված / անջատված | |
| active / inactive | ակտիվ / ոչ ակտիվ | |
| run | գործարկել | |
| start / stop | սկսել / դադարեցնել | |
| session | աշխատաշրջան | |
| hidden / visible | թաքնված / տեսանելի | hide / reveal: թաքցնել / ցուցադրել |
| archive | արխիվ | |
| order (sort) | կարգ | sort order: դասավորման կարգ |
| order (shop) | պատվեր | |
| shop | խանութ | |
| basket | զամբյուղ | |
| product | ապրանք | |
| customer | հաճախորդ | |
| discount | զեղչ | |
| VAT | ԱԱՀ | VAT type: ԱԱՀ-ի տեսակ |
| price | գին | |
| currency | արժույթ | |
| tag | պիտակ | keyword tag; an HTML tag: HTML թեգ |
| translation | թարգմանություն | |
| language | լեզու | main language: հիմնական լեզու |
| locale | տեղայնացում | locale code: տեղայնացման կոդ |
| state | վիճակ | the object state; state group: վիճակների խումբ |
| status | կարգավիճակ | |
| view (button) | դիտել | a module view: դիտում |
| format | ձևաչափ | |
| sort | դասավորել | |
| filter | զտիչ | to filter: զտել |
| select | ընտրել | selected: ընտրված |
| browse | զննել | |
| copy | պատճենել | |
| move | տեղափոխել | |
| add | ավելացնել | |
| create | ստեղծել | |
| new | նոր | |
| restore | վերականգնել | |
| install / uninstall | տեղադրել / ապատեղադրել | |
| sub items | ենթատարրեր | |
| subtree | ենթածառ | |
| related objects | կապակցված օբյեկտներ | relation: կապ |
| URL alias | URL կեղծանուն | |
| redirect | վերահղում | |
| dashboard | վահանակ | |
| content structure | բովանդակության կառուցվածք | |
| media library | մեդիագրադարան | |
| identifier / ID | նույնացուցիչ / ID | |
| priority | առաջնահերթություն | |
| owner | սեփականատեր | |
| modified / created | փոփոխված / ստեղծված | |
| date / time | ամսաթիվ / ժամ | |
| error / warning | սխալ / զգուշացում | |
| help | օգնություն | |
| notification | ծանուցում | |
| wizard | վարպետ | |
| feed (RSS) | լրահոս | RSS feed: RSS լրահոս |
| tab | ներդիր | |
| audit, audit trail | աուդիտ, աուդիտի մատյան | |
| repository | պահոց | |
| checksum | ստուգիչ գումար | |
| account | հաշիվ | |
| default | լռելյայն | default value: լռելյայն արժեք |
| toolbar | գործիքագոտի | |
| container | կոնտեյներ | |
| AI | արհեստական բանականություն | |
| pattern (name pattern) | նմուշ | kept apart from ձևանմուշ (template) |
| wildcard | փոխարինող նշան | |
| remote ID | հեռավար ID | |
| placeholder | տեղապահ | |
| collection | հավաքածու | information collector: տեղեկությունների հավաքիչ |
| approve, approval | հաստատել, հաստատում | |
| software | ծրագրակազմ | |
| creator / modifier | ստեղծող / փոփոխող | |
| request (HTTP) | հարցում | |
| query | հարցում | search query: որոնման հարցում |
| handler / provider / listener | մշակիչ / մատակարար / ունկնդիր | |
| engine | շարժիչ | |
| release / upgrade | թողարկում / արդիացում | update: թարմացում |
| backup | պահուստային պատճեն | |
| maintenance | սպասարկում | |
| directory | գրացուցակ | |
| header (HTTP) | գլխագիր | |
| page layout (pagelayout template) | էջի ձևանմուշ | kept apart from դասավորություն (Exponential Layouts) |
| passkey | մուտքի բանալի | |
| two-step sign-in | երկքայլ մուտք | |
| device / browser | սարք / դիտարկիչ | |
| link | հղում | |
| registration, register | գրանցում, գրանցել | |
| payment gateway | վճարային դարպաս | |
| form | ձև | |
| comment | մեկնաբանություն | |
| newsletter / subscription | տեղեկագիր / բաժանորդագրություն | |
| field | դաշտ | |
| value | արժեք | |
| list | ցուցակ | |
| page | էջ | |
| title / name | վերնագիր / անուն | the column "Name": Անուն |
| description | նկարագրություն | |
| type | տեսակ | |
| path | ուղի | |
| width / height | լայնություն / բարձրություն | |
| size | չափ | |
| none | ոչ մեկը | |
| yes / no | այո / ոչ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Խանութ, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is պահպանել.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
