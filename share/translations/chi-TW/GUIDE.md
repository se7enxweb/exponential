# Traditional Chinese (chi-TW): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated and cross-checked in October 2026; review by native speakers is welcome. The Simplified Chinese
guide (`chi-CN`) has the same structure and the same glossary rows; where the two locales use different words, both
guides say so. **Never convert a chi-CN translation to Traditional characters**: vocabulary differs (物件/对象,
檔案/文件, 欄位/字段, 預設/默认, and a 列 is a row in Taiwan but a column on the mainland), and a converted text
reads as mainland Chinese in traditional letters.

## 1. Language and register

Standard Mandarin (國語) in Traditional characters (教育部《常用國字標準字體表》), as used in Taiwan. `chi-HK`
(Hong Kong) is to fall back to this catalogue (`FallbackLanguages[chi-HK]=chi-TW`); Hong Kong
readers then get Taiwanese vocabulary, which they read without trouble in software (檔案, 伺服器, 網路, 預設 are the
same there). The tone is neutral, concise and professional, as in the Traditional Chinese versions of Windows,
Microsoft 365 and common web services in Taiwan. The terminology follows the Microsoft Traditional Chinese terminology
and style guide where it has a term, Mozilla's zh-TW usage where it has none; no mainland words (section 4 names the
pairs), no colloquialisms, no literary Chinese.

**The old catalogue** (4,592 of 7,416 messages finished, hand-made; `chi-HK` is a byte-identical copy) set conventions
that remain valid: the buttons 儲存, 編輯, 取消, 預覽, 瀏覽, 複製, 套用, the terms 物件, 檔案, 節點, 區段 (section),
類別 (class), 範本, 草稿, 版本, 角色, 擴充功能, 套件, 垃圾桶, 登入/登出, 搜尋, and full-width punctuation with
「」 quotes. Other forms are no longer used, and its translations must be corrected
where they show them:

| Old | New | Why |
|---|---|---|
| 請使用者 (User, Username, User accounts …) | 使用者, 使用者名稱, 使用者帳號 | a systematic error, 42 times |
| 你 (217 times) | 您, sparingly, or no pronoun | section 2 |
| 發表 (Publish) | 發佈 | Microsoft and Apple form for publishing content |
| 規則 / 策略 (Policy) | 原則 | Microsoft: 群組原則 |
| 確認 (OK) | 確定 | Microsoft form of the OK button |
| 存取網址 (siteaccess) | 網站存取 | "access URL" was a misreading |
| 求助 (Help) | 說明 | Microsoft form |
| 不明 (Unknown) | 未知 | |
| 擱置 (Pending) | 待處理 | |
| 歸檔 (Archive) | 封存 | Microsoft form |
| 回存 (Restore) | 還原 | |
| 同意 (Approve) | 核准 | |
| 訂貨 (Order) | 訂單 | |
| 媒體功能庫, 設定值, 優先權, 可見的 | 媒體庫, 設定, 優先順序, 可見 | |
| one simplified character (帳户) | 帳號 | the only Simplified character in the file |

## 2. Form of address

The user is addressed with **您**, and sparingly: Chinese UI text is impersonal wherever the sentence allows it. The
old catalogue used 你 (217 times, 您 never), as Apple and many consumer apps in Taiwan do; Microsoft, Google and
government and banking sites in Taiwan use 您, and an administration interface is business software. So: 您, never 你.

- **Buttons, menu items, links and action labels** are verbs or verb–object phrases, usually two or four characters,
  without 請 and without a particle: 儲存, 刪除, 移除, 編輯, 新增, 建立, 取消, 套用, 確定, 返回, 上一步, 下一步, 發佈,
  儲存草稿, 捨棄草稿, 送出發佈, 全選, 刪除選取的項目, 清空垃圾桶, 上傳檔案. Not 點擊儲存, not 請儲存.
- **Titles, column headings and tabs** are nouns or noun phrases: 內容結構, 使用者帳號, 版面配置類型, 通知設定,
  物件狀態, 建立時間, 修改時間.
- **Sentences, hints and messages** are complete sentences with a full stop. Instructions begin with 請:
  - 請為將從此處建立的「%class」物件選擇位置。
  - 請輸入有效的電子郵件地址。
  - 請檢查網路連線，然後再試一次。
  - 可以在 content.ini 中變更版本歷程記錄的設定。
- **Questions and confirmations** use 確定要……嗎？ or 是否要……？:
  - 確定要刪除此類別嗎？
  - 是否要傳送訊息給核准者？
- **Error messages** state the fact, without blaming the user: 無法建立草稿。, 找不到檔案。, 您沒有刪除此類別的權限。
  (您 only where the subject cannot be left out; 沒有刪除此類別的權限。 is just as good).
- **Status messages** are short and neutral, completed actions with 已: 已刪除 1 個草稿。, 通知設定已儲存。,
  找不到 cron 工作的執行記錄。 No 被 passive where 已 does the work, no 了 after 已.
- **Tooltips that describe a click**: 按一下圖示即可顯示快顯選單。 or 按一下這裡即可……。
- "your account", "your profile" are 帳號, 個人資料 without a pronoun; where a possessive is needed: 您的帳號.
- Not allowed: 你, 妳, 咱們; 請 on a button; the exclamation mark where the source has none; English left in a
  sentence (except what TRANSLATING.md rule 5 keeps); mainland words (section 4); 的 chains of more than two.

## 3. Script and typography

- **Script**: Traditional characters only (教育部標準字體: 臺/台 both accepted, use 台 in running text as Microsoft
  does; 裡 not 裏; 著 not 着; 為 not 爲; 啟 not 啓); no simplified character.
  Product names, codes, file names, formats and abbreviations stay in Latin letters (TRANSLATING.md, rule 5):
  Exponential, Solr, PHP, URL, ID, JSON, cron, INI.
- **Punctuation**: full-width in Chinese text, as in 教育部《重訂標點符號手冊》: ，。、；：？！（）「」『』. A source full
  stop "." becomes 。, "?" becomes ？, "!" becomes ！, "," becomes ， (and 、 between items of a list: 編輯、刪除和移動).
  A colon after a label becomes ： (名稱：); half-width ":" stays inside code, times (12:30) and after a Latin-only
  label. Parentheses around Chinese or mixed text are full-width （）, half-width only inside code.
- **Quotation marks**: 「…」 for quoted text, button and field names and values in running text: 按一下「儲存」。
  Nested: 『…』. Straight quotes of the source stay inside HTML attributes, JavaScript and where the source quotes code
  or a value to be typed exactly. Never “” in chi-TW.
- **Ellipsis**: the source's form stays at the end of buttons, menu items and progress messages: "..." stays "..."
  (正在載入...), "…" stays "…" (另存新檔…). The Chinese ellipsis …… (two U+2026) only for an omission inside running
  text, and in the confirmation pattern 確定要……嗎？ of the glossary only as a description, not in a message.
- **Spacing** (the practice of the old catalogue — 304 spaced against 5 glued — and of current Microsoft, Apple and
  Google UIs in Taiwan): **one half-width space between Chinese characters and Latin letters, digits or a
  placeholder**, on both sides; **no space next to full-width punctuation**, brackets or quotation marks:
  - 使用 Solr 搜尋 · 已刪除 3 個物件。 · 共 %count 個項目 · %name 已發佈。 · 第 2 頁，共 %pages 頁
  - 「%name」已刪除。 · （PHP 8.1） · 名稱：%name
  - No space between a number and %, ° or a symbol unit (50%, 10°), one space before a Latin unit (10 MB, 5 GB).
  - A tag counts as nothing: `<b>%count</b> 個物件`, `已儲存 <em>%name</em>。`
- **Placeholders** stand free (TRANSLATING.md, rule 1). A Chinese character directly after a placeholder is harmless
  for the test, but the spacing rule above applies: `%name 的屬性`. A Latin letter directly after a placeholder makes
  it a different placeholder.
- **Plural and counts**: Chinese has no plural; "item(s)", "object(s)" are translated once. Counted nouns take a
  measure word after the number: 個 (objects, items, files: 3 個物件), 位 (people: 5 位使用者), 筆 (records and data
  rows: 12 筆記錄), 則 (messages, comments: 3 則訊息), 項 (settings: 3 項設定), 次 (runs, times), 頁 (pages),
  張 (images). Where the noun is unclear, use the label form 物件數：%count.
- **Numbers**: half-width digits 0-9; no Chinese numerals in UI text (第 1 步, not 第一步). Thousands separators,
  decimals, dates and times come from the locale, not from the text.
- **Word order** follows Chinese grammar; placeholders may move (TRANSLATING.md, rule 1). Modifiers precede the noun:
  "objects in %section" → %section 中的物件.

## 4. Glossary

Binding for the kernel and every extension. Section 5 adds the terms settled later. The
column "chi-CN" is not to be used here; it shows where the two locales part, so that nobody copies the
other one.

| English | Traditional Chinese (chi-TW) | Note | chi-CN (not here) |
|---|---|---|---|
| content | 內容 |  | 内容 |
| content object / object | 內容物件 / 物件 | 對象 is mainland usage (in Taiwan: a partner, a target group) | 内容对象 / 对象 |
| item (content in the tree, entry in a list) | 項目 | sub items: 子項目; selected items: 選取的項目; counted: %count 個項目 | 项目 |
| node / main node | 節點 / 主要節點 |  | 节点 / 主节点 |
| parent node / child node | 父節點 / 子節點 | parent level: 上一層 | 父节点 / 子节点 |
| location / main location | 位置 / 主要位置 |  | 位置 / 主位置 |
| content class / class | 內容類別 / 類別 | a PHP class: PHP 類別 | 内容类 / 类 |
| class group | 類別群組 |  | 类组 |
| attribute | 屬性 |  | 属性 |
| datatype | 資料類型 |  | 数据类型 |
| version / version history | 版本 / 版本歷程記錄 |  | 版本 / 版本历史记录 |
| draft | 草稿 | Store draft: 儲存草稿; Discard draft: 捨棄草稿 | same |
| publish / published | 發佈 / 已發佈 | Send for publishing: 送出發佈; not 發表 (old catalogue) | 发布 / 已发布 |
| translation | 翻譯 |  | 翻译 |
| language / main language / content language | 語言 / 主要語言 / 內容語言 |  | 语言 / 主语言 / 内容语言 |
| interface language | 介面語言 |  | 界面语言 |
| siteaccess | 網站存取 | not 存取網址 (old catalogue); the INI word SiteAccess stays in code | 站点访问 |
| site / website | 網站 / 網站 | both are 網站 | 站点 / 网站 |
| design (Exponential design) | 設計 |  | 设计 |
| template | 範本 | template file: 範本檔案; 模板 is mainland usage | 模板 |
| override / override template | 覆寫 / 覆寫範本 | overridden: 已覆寫 | 覆盖 / 覆盖模板 |
| fallback (language, design) | 後援 | fallback language: 後援語言 | 回退 |
| URL alias / URL wildcard | URL 別名 / URL 萬用字元 |  | URL 别名 / URL 通配符 |
| redirect | 重新導向 |  | 重定向 |
| section (Exponential) | 區段 | a part of a page or of a form: 部分 | 分区 |
| object state / state group | 物件狀態 / 狀態群組 |  | 对象状态 / 状态组 |
| status | 狀態 |  | 状态 |
| trash / empty trash | 垃圾桶 / 清空垃圾桶 | 垃圾桶 as in Taiwanese web applications; the Windows 資源回收筒 is not used | 回收站 / 清空回收站 |
| restore | 還原 | not 回存 (old catalogue) | 还原 |
| bookmark | 書籤 |  | 书签 |
| notification | 通知 |  | same |
| dashboard | 儀表板 |  | 仪表板 |
| content structure / media library | 內容結構 / 媒體庫 | not 媒體功能庫 (old catalogue) | 内容结构 / 媒体库 |
| collected information / information collection | 已收集的資訊 / 資訊收集 |  | 已收集的信息 / 信息收集 |
| form | 表單 |  | 表单 |
| poll / vote | 投票 / 投票 | Back to poll: 返回投票 | same |
| comment (by a reader) / comment (in code, translator comment) | 留言 / 註解 |  | 评论 / 注释 |
| newsletter / subscription | 電子報 / 訂閱 |  | 新闻通讯 / 订阅 |
| feed / RSS feed | 摘要 / RSS 摘要 |  | 源 / RSS 源 |
| user / user group | 使用者 / 使用者群組 | never 請使用者 (a systematic error of the old catalogue, 42 times) | 用户 / 用户组 |
| user account / account | 使用者帳號 / 帳號 | 帳戶 only for billing or bank accounts | 用户账户 / 账户 |
| username / login (the field) | 使用者名稱 |  | 用户名 |
| password | 密碼 |  | 密码 |
| anonymous user | 匿名使用者 |  | 匿名用户 |
| administrator / admin interface | 系統管理員 / 管理介面 |  | 管理员 / 管理界面 |
| log in / log out | 登入 / 登出 | logged in: 已登入 | 登录 / 退出登录 |
| sign up, register | 註冊 |  | 注册 |
| role / policy | 角色 / 原則 | 原則 as in Microsoft 群組原則; not 規則 or 策略 (old catalogue) | 角色 / 策略 |
| permission / access | 權限 / 存取 |  | 权限 / 访问 |
| access denied | 存取遭拒 |  | 拒绝访问 |
| limitation (policy) | 限制 |  | same |
| assign / unassign | 指派 / 取消指派 |  | 分配 / 取消分配 |
| approve / reject / approval | 核准 / 拒絕 / 核准 | not 同意 (old catalogue) | 批准 / 拒绝 / 审批 |
| pending | 待處理 | not 擱置 (old catalogue) | 待处理 |
| owner / author / creator | 擁有者 / 作者 / 建立者 |  | 所有者 / 作者 / 创建者 |
| editor (person) / editor (software) | 編輯者 / 編輯器 |  | 编辑人员 / 编辑器 |
| rich text editor / online editor | 富文字編輯器 / 線上編輯器 |  | 富文本编辑器 / 在线编辑器 |
| save / store (button) | 儲存 |  | 保存 |
| edit | 編輯 |  | 编辑 |
| delete / remove | 刪除 / 移除 | remove = take out of a list or place; delete = destroy; not 刪除 for Remove (old catalogue) | 删除 / 移除 |
| cancel / OK / apply | 取消 / 確定 / 套用 | OK is 確定, not 確認 (old catalogue) | 取消 / 确定 / 应用 |
| discard | 捨棄 | not 放棄 (old catalogue) | 放弃 |
| create / new / add | 建立 / 新增 / 新增 | new and add are both 新增; create is 建立 (not 創建) | 创建 / 新建 / 添加 |
| copy / move / swap | 複製 / 移動 / 交換 |  | 复制 / 移动 / 交换 |
| back / next / previous | 返回 / 下一步 / 上一步 | in a pager: 上一頁 / 下一頁 | same |
| open / close | 開啟 / 關閉 |  | 打开 / 关闭 |
| select (mark items) / choose (an option) | 選取 / 選擇 | select all: 全選 | 选择 / 选择 |
| browse | 瀏覽 |  | 浏览 |
| preview | 預覽 |  | 预览 |
| view (button: to look at) / view (noun: module view) | 檢視 / 檢視 |  | 查看 / 视图 |
| upload / download | 上傳 / 下載 |  | 上传 / 下载 |
| import / export | 匯入 / 匯出 | 導入/導出 are mainland usage | 导入 / 导出 |
| search / advanced search | 搜尋 / 進階搜尋 |  | 搜索 / 高级搜索 |
| filter (verb / noun) | 篩選 / 篩選器 |  | 筛选 / 筛选器 |
| sort / sort order | 排序 / 順序 |  | 排序 / 顺序 |
| refresh / reload | 重新整理 / 重新載入 |  | 刷新 / 重新加载 |
| enable / disable | 啟用 / 停用 |  | 启用 / 禁用 |
| activate / deactivate (account, user) | 啟用 / 停用 |  | 激活 / 停用 |
| install / uninstall | 安裝 / 解除安裝 |  | 安装 / 卸载 |
| update / upgrade | 更新 / 升級 |  | 更新 / 升级 |
| click / double-click | 按一下 / 按兩下 |  | 单击 / 双击 |
| drag / drop | 拖曳 / 放下 |  | 拖动 / 放置 |
| print | 列印 |  | 打印 |
| retry / try again | 重試 / 再試一次 |  | 重试 / 重试 |
| file | 檔案 | 文件 means a document in Taiwan | 文件 |
| folder / directory | 資料夾 / 目錄 |  | 文件夹 / 目录 |
| path / file system | 路徑 / 檔案系統 |  | 路径 / 文件系统 |
| binary file | 二進位檔案 |  | 二进制文件 |
| image / image alias | 圖片 / 圖片別名 | not 圖像 (old catalogue) | 图片 / 图片别名 |
| video / audio | 影片 / 音訊 |  | 视频 / 音频 |
| font | 字型 |  | 字体 |
| database / table (database) | 資料庫 / 資料表 |  | 数据库 / 数据表 |
| table (in a page or text) | 表格 |  | same |
| field (form, database) | 欄位 |  | 字段 |
| column / row | 欄 / 列 | the other way round than on the mainland: 列 is a row in Taiwan | 列 / 行 |
| data / information | 資料 / 資訊 |  | 数据 / 信息 |
| message | 訊息 |  | 消息 |
| cache / clear cache | 快取 / 清除快取 |  | 缓存 / 清除缓存 |
| index / reindex | 索引 / 重建索引 |  | same |
| search engine | 搜尋引擎 |  | 搜索引擎 |
| keyword | 關鍵字 |  | 关键词 |
| tag (keyword, eztags) / tag (HTML, XML) | 標籤 / HTML 標記 | 標記 keeps the markup tag apart from the keyword 標籤 | 标签 / HTML 标签 |
| format (file format) / formatting | 格式 / 格式設定 |  | 格式 / 格式设置 |
| settings / setup (admin tab) | 設定 / 設定 | not 設定值 (old catalogue) | 设置 / 设置 |
| configuration / configuration file | 組態 / 組態檔 |  | 配置 / 配置文件 |
| INI file / INI setting | INI 檔案 / INI 設定 |  | INI 文件 / INI 设置 |
| default | 預設 |  | 默认 |
| cron job / cronjob part | cron 工作 / cron 工作部分 |  | cron 任务 / cron 任务部分 |
| package | 套件 |  | 软件包 |
| extension | 擴充功能 |  | 扩展 |
| plugin / provider | 外掛程式 / 提供者 |  | 插件 / 提供程序 |
| server / web server | 伺服器 / Web 伺服器 |  | 服务器 / Web 服务器 |
| network | 網路 |  | 网络 |
| software / hardware | 軟體 / 硬體 |  | 软件 / 硬件 |
| program / code / source code | 程式 / 程式碼 / 原始碼 | 程序 means a procedure in Taiwan | 程序 / 代码 / 源代码 |
| function (PHP) / method | 函式 / 方法 |  | 函数 / 方法 |
| variable / parameter / argument | 變數 / 參數 / 引數 |  | 变量 / 参数 / 参数 |
| string / character / character set | 字串 / 字元 / 字元集 |  | 字符串 / 字符 / 字符集 |
| encoding | 編碼 |  | 编码 |
| interface (UI) / interface (PHP) | 介面 / 介面 |  | 界面 / 接口 |
| module / view (module view) | 模組 / 檢視 |  | 模块 / 视图 |
| link / broken link | 連結 / 失效連結 |  | 链接 / 失效链接 |
| menu / context menu | 選單 / 快顯選單 |  | 菜单 / 快捷菜单 |
| toolbar / sidebar | 工具列 / 側邊欄 |  | 工具栏 / 侧边栏 |
| tab | 索引標籤 |  | 选项卡 |
| page / layout | 頁面 / 版面配置 | Exponential Layouts: 版面配置 | 页面 / 布局 |
| block / zone | 區塊 / 區域 |  | 区块 / 区域 |
| navigation / breadcrumb | 導覽 / 導覽路徑 |  | 导航 / 面包屑导航 |
| icon / button | 圖示 / 按鈕 |  | 图标 / 按钮 |
| checkbox / drop-down list / list | 核取方塊 / 下拉式清單 / 清單 |  | 复选框 / 下拉列表 / 列表 |
| dialog / wizard | 對話方塊 / 精靈 |  | 对话框 / 向导 |
| window / browser | 視窗 / 瀏覽器 |  | 窗口 / 浏览器 |
| help / about | 說明 / 關於 | not 求助 (old catalogue) | 帮助 / 关于 |
| log (file) / log viewer | 日誌 / 日誌檢視器 | never the verb log in | 日志 / 日志查看器 |
| debug / debug output | 偵錯 / 偵錯輸出 |  | 调试 / 调试输出 |
| error / warning / notice | 錯誤 / 警告 / 提示 |  | 错误 / 警告 / 提示 |
| report / statistics | 報告 / 統計 |  | 报告 / 统计 |
| modified / created (columns) | 修改時間 / 建立時間 |  | 修改时间 / 创建时间 |
| name / description / title | 名稱 / 描述 / 標題 | a person's name: 姓名 | 名称 / 描述 / 标题 |
| identifier / ID / remote ID | 識別碼 / ID / 遠端 ID |  | 标识符 / ID / 远程 ID |
| remote / local | 遠端 / 本機 |  | 远程 / 本地 |
| hidden / visible / visibility | 隱藏 / 可見 / 可見性 | not 可見的 (old catalogue) | 隐藏 / 可见 / 可见性 |
| priority | 優先順序 | not 優先權 (old catalogue) | 优先级 |
| unknown / none / all / any | 未知 / 無 / 全部 / 任何 | unknown is 未知, not 不明 (old catalogue) | 未知 / 无 / 全部 / 任意 |
| archive (versions, orders) / archive file (.zip, .tar) | 封存 / 壓縮檔 | not 歸檔 (old catalogue) | 归档 / 压缩包 |
| backup | 備份 |  | 备份 |
| storage / disk space | 儲存空間 / 磁碟空間 |  | 存储 / 磁盘空间 |
| memory / performance | 記憶體 / 效能 |  | 内存 / 性能 |
| process / thread (operating system) | 處理程序 / 執行緒 |  | 进程 / 线程 |
| queue / job / task | 佇列 / 工作 / 任務 |  | 队列 / 作业 / 任务 |
| schedule / scheduled | 排程 / 已排程 |  | 计划 / 已计划 |
| timeout | 逾時 |  | 超时 |
| request / response | 要求 / 回應 |  | 请求 / 响应 |
| session / cookie | 工作階段 / Cookie |  | 会话 / Cookie |
| token / API key | 權杖 / API 金鑰 |  | 令牌 / API 密钥 |
| credentials / certificate | 認證 / 憑證 |  | 凭据 / 证书 |
| authentication / two-factor authentication | 驗證 / 雙重要素驗證 |  | 身份验证 / 双重身份验证 |
| encryption / encrypted | 加密 / 已加密 |  | same |
| locale / time zone | 地區設定 / 時區 |  | 区域设置 / 时区 |
| project | 專案 | 項目 means an item in Taiwan | 项目 |
| shop / webshop | 商店 / 網路商店 |  | 商店 / 网店 |
| order (shop) / order (sort order) | 訂單 / 順序 | not 訂貨 (old catalogue) | 订单 / 顺序 |
| basket / checkout / wish list | 購物車 / 結帳 / 願望清單 |  | 购物车 / 结账 / 愿望清单 |
| product / price / discount / currency | 商品 / 價格 / 折扣 / 貨幣 |  | 商品 / 价格 / 折扣 / 货币 |
| VAT / VAT type | 營業稅 / 營業稅類型 | the value-added tax of Taiwan is the 營業稅 | 增值税 / 增值税类型 |
| environment: development / test / staging / production | 開發 / 測試 / 預備 / 正式 | short forms DEV, TEST, STAGE, PROD stay | 开发 / 测试 / 预发布 / 生产 |
| AI / model / prompt | AI / 模型 / 提示詞 |  | AI / 模型 / 提示词 |
| embedding / vector / chunk | 嵌入 / 向量 / 分塊 |  | 嵌入 / 向量 / 分块 |
| health / health check | 健康情況 / 健康情況檢查 |  | 运行状况 / 运行状况检查 |
| confirmation question ("Delete …?") | 確定要……嗎？ | 確定要刪除此類別嗎？ | 确定要……吗？ |

## 5. Decisions after the translation

Settled from the terms that needed a decision in the 18 languages of 23 October 2026 and
from the context overrides they needed. They take precedence over the glossary.
Terms settled while comparing chi-CN and chi-TW are added here, with the distinction.

| English | Translation | Distinction |
|---|---|---|
| Store (button: save) | 儲存 | the shop tab of the admin page layout (kernel\|design/admin/pagelayout): 商店; secrets ("file away", design/admin/secrets): 存入 — context overrides |
| Order (shop) / Order (sort order, priority) | 訂單 / 順序 | the word decides nothing, the context does (shop, orderview, basket vs. class/edit, bookmark sorting); context overrides |
| State (object state) / State (address) | 狀態 / 州/省 | the address field of the shop and of user data: 州/省 |
| Login (button, verb) / Login (field: the user name) | 登入 / 使用者名稱 |  |
| View (button) / View (noun: module view, debug panel) | 檢視 / 檢視 | 檢視 for both |
| Up / Down (move in a list) | 上移 / 下移 | keys in the rich text editor: 向上鍵 / 向下鍵; expqueue "Up" (how long a worker runs): 運作時間 |
| Top / Bottom (position in an order) | 頂端 / 底端 | a position on the page: 上 / 下 |
| Delete (the key) | Delete 鍵 | key names stay in Latin letters: Enter 鍵, Esc 鍵 |
| destination (URL alias, wildcard) | 目標 | a place in the tree or an import target: 位置 |
| visibility (node) / invisible / hidden | 可見性 / 不可見 / 隱藏 |  |
| always available | 一律可用 | "available" in sentences: 可用 / 可以使用 |
| two-step sign-in / two-factor authentication | 兩步驟驗證 / 雙重要素驗證 |  |
| secret (settings, secret store) | 祕密 | secret value: 祕密值 |
| masked / unmasked; redact / redacted | 已遮罩 / 未遮罩; 遮罩 / 已遮罩 |  |
| activate / deactivate / reactivate; active | 啟用 / 停用 / 重新啟用; 作用中 | activate and enable are both 啟用 |
| warm (caches) / preload | 預熱 / 預先載入 |  |
| template operator / operator (a person) | 範本運算子 / 操作員 |  |
| finding / findings | 檢查結果 | results of a search or a run: 結果 |
| purge / purged | 永久刪除 / 已永久刪除 |  |
| True / False (setting values) | 是 / 否 | code literals true / false stay in Latin letters |
| Any (policy limitation value) | 任何 |  |
| Restart (wizard) / restart (server, process) | 重新開始 / 重新啟動 |  |
| live (run) / probe | 即時 / 探測 |  |
| staged (git) / staged review | 已暫存 / 暫存審閱 |  |
| dead letter / requeue / lease / retention | 無效信件 / 重新排入佇列 / 租用 / 保留期間 |  |
| extension point | 擴充點 |  |
| fetch / fetch function (template language) | 擷取 / 擷取函式 | the template function name fetch stays in code |
| persistent object | 持續性物件 |  |
| hits / misses / hit rate / evictions | 命中 / 未命中 / 命中率 / 收回 |  |
| maintenance mode / maintenance window | 維護模式 / 維護時段 |  |
| Switch on / Switch off | 開啟 / 關閉 |  |
| Pause / Resume / Skipped | 暫停 / 繼續 / 已略過 |  |
| Dismiss | 關閉 |  |
| contributors / third-party software | 貢獻者 / 協力廠商軟體 |  |
| navigation part | 導覽部分 |  |
| orphan files / asset / node picker | 孤立檔案 / 資產 / 節點選擇器 |  |
| origin (of a setting value) | 來源 |  |
| hosting provider | 主機代管業者 |  |

### Harmonisation of the catalogues (24 October 2026)

The reviewers diverged on the terms below. One term was picked per concept
(glossary first, then Microsoft Traditional Chinese practice, then the majority) and applied throughout
(only to items whose English source contains the concept).

| English | Translation | Replaced |
|---|---|---|
| worker (Velocity, PHP, queue) | 工作處理程序 | 工作者, 背景處理程式, 工作程序, Latin "worker"; worker mode: 工作處理程序模式 |
| process (OS, workflow, background) | 處理程序 | 處理序, 背景程序 (程序 alone stays "procedure": 設定程序, 發佈程序) |
| client (AI/MCP/OAuth/FTP) | 用戶端 | 客戶端 (the Microsoft term; `locale_check` flags 用戶 as vocabulary, deliberately kept) |
| preset | 預設組合 | 預設集, 預設組 |
| listener / listen | 接聽程式 / 接聽 | 偵聽, 監聽 |
| subtree | 子樹 | 子樹狀結構, 子樹狀目錄 |
| repository | 儲存庫 | 存放庫 |
| trigger (workflow) | 觸發程序 | 觸發器, 觸發條件 |
| instance | 執行個體 | 實例 |
| content tree | 內容樹 | 內容樹狀結構; "Content tree menu": 內容樹選單 |
| audit sink / destination of a sink | 稽核目的地 | 稽核接收端 |
| country / region | 國家/地區 | 國家／地區, 國家或地區 |
| accordion | 摺疊式面板 | 摺疊項目 |
| thread (AI chat) / thread (OS) | 對話串 / 執行緒 | 討論串 |
| retention period | 保留期間 | 保留期限 |
| custom tag (XML) | 自訂標記 | 自訂標籤 |
| newsletter skin | 版面樣式 | 外觀 |
| mail / SMS / MCP transport | 傳輸方式 | bare 傳輸 (transport encryption: 傳輸加密) |
| log file | 日誌檔案 | 記錄檔 |
| executable (file) | 執行檔 | 可執行檔 (the adjective 可執行 stays) |
| rollback | 回復 | 復原 (undo stays 復原) |
| bearer token | Bearer 權杖 | 持有者權杖 |
| adapter | 轉接器 | 配接器 |
| handler | 處理常式 | 處理程式 |
| proxy | 代理伺服器 | Latin Proxy |
| cron (running text) | cron | Cron (Crontab and code identifiers unchanged) |

Kept as the reviewers found them, because the words name different things: 封鎖清單 (suppression list) vs 黑名單
(newsletter blacklist); 位址 (URL, IP, network) vs 地址 (e-mail address, 電子郵件地址); 作業 (workflow or module
operation) vs 操作 (generic action); 相依項目 (item) vs 相依性 (concept); 寫入閘門 (write gate) vs 閘道 (gateway, mail
gate); 目標 / 目的地 / 目的 for "destination" depending on the context (URL alias target, file destination, import
node); 核對碼 (checksum) vs 檢查碼 (check digit); 期次 (newsletter edition) vs 版本 (product edition).
