# Simplified Chinese (chi-CN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated and cross-checked in October 2026; review by native speakers is welcome. The Traditional Chinese
guide (`chi-TW`) has the same structure and the same glossary rows; where the two locales use different words, both
guides say so. **Never convert a chi-TW translation to Simplified characters**: vocabulary differs (对象/物件,
文件/檔案, 字段/欄位, 列/欄), and a converted text reads as Taiwanese Chinese in simplified letters.

## 1. Language and register

Standard Mandarin (普通话) in Simplified characters (《通用规范汉字表》), as used in mainland China. `chi-SG`
(Singapore) falls back to this catalogue (`FallbackLanguages[chi-SG]=chi-CN`). The tone is neutral, concise and
professional, as in the Simplified Chinese versions of Windows, Microsoft 365 and common web CMS. The terminology
follows the Microsoft Simplified Chinese terminology and style guide where it has a term, Mozilla's zh-CN usage where
it has none; no invented words, no internet slang (亲, 小伙伴), no colloquialisms (啥, 咋), no literary Chinese.

**The old catalogue** (6,211 of 7,758 messages finished, hand-made, 2000s) set conventions that remain valid: the
buttons 保存, 编辑, 取消, 确定, 预览, 浏览, 复制, the terms 节点, 分区 (section), 类 (class), 草稿, 版本, 角色, 策略,
工作流, 回收站, 属性, 模板, 用户名, 密码, and 您 as the form of address. Other forms of that catalogue are no longer
used, and its translations must be corrected where they show them:

| Old | New | Why |
|---|---|---|
| 缺省 | 默认 | 默认 is the current mainland word (Microsoft, Apple, every current UI) |
| 存取网址 (siteaccess) | 站点访问 | "access URL" was a misreading; a siteaccess is a site configuration |
| 退出 (Logout) | 退出登录 | 退出 alone means "quit" |
| 删除 for "Remove" | 移除 | remove and delete are kept apart (TRANSLATING.md) |
| 面板 (Dashboard) | 仪表板 | |
| 不允许进入 (Access denied) | 拒绝访问 | Microsoft form |
| 图像 | 图片 | everyday UI word (Office: 插入 > 图片); 图像 only in technical compounds (图像处理) |
| 州 for "State" | 状态 | 州/省 only in the address field (section 5) |
| text glued to Latin words (`使用Solr搜索`) | `使用 Solr 搜索` | section 3, spacing |
| half-width `, . ? : !` and straight quotes after Chinese | `，。？：！` and “” | section 3 |

## 2. Form of address

The user is addressed with **您**, and sparingly: Chinese UI text is impersonal wherever the sentence allows it. Of the
old catalogue's 6,211 finished messages, 458 use 您 and 23 use 你; Microsoft, Alibaba Cloud and Tencent Cloud consoles
use 您, consumer apps often 你. An administration interface is business software: 您, never 你.

- **Buttons, menu items, links and action labels** are verbs or verb–object phrases, usually two or four characters,
  without 请 and without a particle: 保存, 删除, 移除, 编辑, 添加, 新建, 取消, 应用, 确定, 返回, 上一步, 下一步, 发布,
  保存草稿, 放弃草稿, 提交发布, 全选, 删除所选项目, 清空回收站, 上传文件. Not 点击保存, not 保存吧, not 请保存.
- **Titles, column headings and tabs** are nouns or noun phrases: 内容结构, 用户帐户, 布局类型, 通知设置, 对象状态,
  创建时间, 修改时间.
- **Sentences, hints and messages** are complete sentences with a full stop. Instructions begin with 请:
  - 请为将从此处创建的“%class”对象选择位置。
  - 请输入有效的电子邮件地址。
  - 请检查网络连接，然后重试。
  - 可以在 content.ini 中更改版本历史记录设置。
- **Questions and confirmations** use 确定要……吗？ or 是否……？:
  - 确定要删除此类吗？
  - 是否向审批人发送消息？
- **Error messages** state the fact, without blaming the user: 无法创建草稿。, 找不到文件。, 您无权删除此类。
  (您 only where the subject cannot be left out; 没有删除此类的权限。 is just as good).
- **Status messages** are short and neutral, completed actions with 已: 已删除 1 个草稿。, 通知设置已保存。,
  未找到 cron 任务的运行记录。 No 被 passive where 已 does the work (not 草稿被删除了), no 了 after 已.
- **Tooltips that describe a click**: 单击图标可显示快捷菜单。 or 单击此处可……。
- "your account", "your profile" are 帐户, 个人资料 without a pronoun; where a possessive is needed: 您的帐户.
- Not allowed: 你, 亲, 咱们; 请 on a button; the exclamation mark where the source has none; English left in a
  sentence (except what TRANSLATING.md rule 5 keeps); 的 chains of more than two (所选对象的属性的名称 → 所选对象属性名称).

## 3. Script and typography

- **Script**: Simplified characters only (《通用规范汉字表》); no traditional character, not even in a name
  label. Product names, codes, file names, formats and abbreviations stay in
  Latin letters (TRANSLATING.md, rule 5): Exponential, Solr, PHP, URL, ID, JSON, cron, INI.
- **Punctuation**: full-width in Chinese text, as in GB/T 15834: ，。、；：？！（）“”‘’. A source full stop "."
  becomes 。, "?" becomes ？, "!" becomes ！, "," becomes ， (and 、 between items of a list: 编辑、删除和移动).
  A colon after a label becomes ： (名称：); half-width ":" stays inside code, times (12:30) and after a Latin-only
  label. Parentheses around Chinese or mixed text are full-width （）, half-width only inside code.
- **Quotation marks**: “…” for quoted text, button and field names and values in running text:
  单击“保存”。 Nested: ‘…’. Straight quotes of the source stay inside HTML attributes, JavaScript and where the source
  quotes code or a value to be typed exactly. Never 「」 in chi-CN.
- **Ellipsis**: the source's form stays at the end of buttons, menu items and progress messages: "..." stays "..."
  (正在加载...), "…" stays "…" (另存为…). The Chinese ellipsis …… (two U+2026) only for an omission inside running text,
  and in the confirmation pattern 确定要……吗？ of the glossary only as a description, not in a message.
- **Spacing** (the practice of current Microsoft, Apple and Google mainland UIs; the old catalogue glued and is
  corrected): **one half-width space between Chinese characters and Latin letters, digits or a placeholder**, on
  both sides; **no space next to full-width punctuation**, brackets or quotation marks:
  - 使用 Solr 搜索 · 已删除 3 个对象。 · 共 %count 个项目 · %name 已发布。 · 第 2 页，共 %pages 页
  - “%name”已删除。 · （PHP 8.1） · 名称：%name
  - No space between a number and %, ° or a symbol unit (50%, 10°), one space before a Latin unit (10 MB, 5 GB).
  - A tag counts as nothing: `<b>%count</b> 个对象`, `已保存 <em>%name</em>。`
- **Placeholders** stand free (TRANSLATING.md, rule 1). A Chinese character directly after a placeholder is harmless
  for the test (`%name的` would still be `%name`), but the spacing rule above applies: `%name 的属性`. A Latin letter
  directly after a placeholder makes it a different placeholder.
- **Plural and counts**: Chinese has no plural; "item(s)", "object(s)" are translated once. Counted nouns take a
  measure word after the number: 个 (objects, items, users, files: 3 个对象, 5 个用户), 项 (settings, entries of a list
  when 个 sounds wrong: 3 项设置), 条 (messages, records, log lines, comments: 12 条记录, 3 条消息), 次 (runs, times),
  页 (pages), 张 (images). Where the noun is unclear, use the label form 对象数：%count.
- **Numbers**: half-width digits 0-9; no Chinese numerals in UI text (第 1 步, not 第一步). Thousands separators,
  decimals, dates and times come from the locale, not from the text.
- **Word order** follows Chinese grammar; placeholders may move (TRANSLATING.md, rule 1). Modifiers precede the noun:
  "objects in %section" → %section 中的对象.

## 4. Glossary

Binding for the kernel and every extension. Section 5 adds the terms settled later. The
column "chi-TW" is not to be used here; it shows where the two locales part, so that nobody copies the
other one.

| English | Simplified Chinese (chi-CN) | Note | chi-TW (not here) |
|---|---|---|---|
| content | 内容 |  | 內容 |
| content object / object | 内容对象 / 对象 |  | 內容物件 / 物件 |
| item (content in the tree, entry in a list) | 项目 | sub items: 子项目; selected items: 所选项目; counted: %count 个项目 | 項目 |
| node / main node | 节点 / 主节点 |  | 節點 / 主要節點 |
| parent node / child node | 父节点 / 子节点 | parent level: 上一级 | 父節點 / 子節點 |
| location / main location | 位置 / 主位置 |  | 位置 / 主要位置 |
| content class / class | 内容类 / 类 | a PHP class: PHP 类 | 內容類別 / 類別 |
| class group | 类组 |  | 類別群組 |
| attribute | 属性 |  | 屬性 |
| datatype | 数据类型 |  | 資料類型 |
| version / version history | 版本 / 版本历史记录 |  | 版本 / 版本歷程記錄 |
| draft | 草稿 | Store draft: 保存草稿; Discard draft: 放弃草稿 | same |
| publish / published | 发布 / 已发布 | Send for publishing: 提交发布 | 發佈 / 已發佈 |
| translation | 翻译 |  | 翻譯 |
| language / main language / content language | 语言 / 主语言 / 内容语言 |  | 語言 / 主要語言 / 內容語言 |
| interface language | 界面语言 |  | 介面語言 |
| siteaccess | 站点访问 | not 存取网址 (old catalogue); the INI word SiteAccess stays in code | 網站存取 |
| site / website | 站点 / 网站 |  | 網站 / 網站 |
| design (Exponential design) | 设计 |  | 設計 |
| template | 模板 | template file: 模板文件 | 範本 |
| override / override template | 覆盖 / 覆盖模板 | overridden: 已覆盖 | 覆寫 / 覆寫範本 |
| fallback (language, design) | 回退 | fallback language: 回退语言 | 後援 |
| URL alias / URL wildcard | URL 别名 / URL 通配符 |  | URL 別名 / URL 萬用字元 |
| redirect | 重定向 |  | 重新導向 |
| section (Exponential) | 分区 | a part of a page or of a form: 部分 | 區段 |
| object state / state group | 对象状态 / 状态组 |  | 物件狀態 / 狀態群組 |
| status | 状态 |  | 狀態 |
| trash / empty trash | 回收站 / 清空回收站 |  | 垃圾桶 / 清空垃圾桶 |
| restore | 还原 |  | 還原 |
| bookmark | 书签 |  | 書籤 |
| notification | 通知 |  | same |
| dashboard | 仪表板 | not 面板 (old catalogue) | 儀表板 |
| content structure / media library | 内容结构 / 媒体库 |  | 內容結構 / 媒體庫 |
| collected information / information collection | 已收集的信息 / 信息收集 |  | 已收集的資訊 / 資訊收集 |
| form | 表单 |  | 表單 |
| poll / vote | 投票 / 投票 | Back to poll: 返回投票 | same |
| comment (by a reader) / comment (in code, translator comment) | 评论 / 注释 |  | 留言 / 註解 |
| newsletter / subscription | 新闻通讯 / 订阅 |  | 電子報 / 訂閱 |
| feed / RSS feed | 源 / RSS 源 |  | 摘要 / RSS 摘要 |
| user / user group | 用户 / 用户组 |  | 使用者 / 使用者群組 |
| user account / account | 用户帐户 / 帐户 | 帐, as Microsoft writes it: 帐户, 帐号 (decided 24.10.2026) | 使用者帳號 / 帳號 |
| username / login (the field) | 用户名 |  | 使用者名稱 |
| password | 密码 |  | 密碼 |
| anonymous user | 匿名用户 |  | 匿名使用者 |
| administrator / admin interface | 管理员 / 管理界面 |  | 系統管理員 / 管理介面 |
| log in / log out | 登录 / 退出登录 | logged in: 已登录; not 退出 alone (old catalogue) | 登入 / 登出 |
| sign up, register | 注册 |  | 註冊 |
| role / policy | 角色 / 策略 |  | 角色 / 原則 |
| permission / access | 权限 / 访问 |  | 權限 / 存取 |
| access denied | 拒绝访问 | not 不允许进入 (old catalogue) | 存取遭拒 |
| limitation (policy) | 限制 |  | same |
| assign / unassign | 分配 / 取消分配 |  | 指派 / 取消指派 |
| approve / reject / approval | 批准 / 拒绝 / 审批 |  | 核准 / 拒絕 / 核准 |
| pending | 待处理 |  | 待處理 |
| owner / author / creator | 所有者 / 作者 / 创建者 |  | 擁有者 / 作者 / 建立者 |
| editor (person) / editor (software) | 编辑人员 / 编辑器 |  | 編輯者 / 編輯器 |
| rich text editor / online editor | 富文本编辑器 / 在线编辑器 |  | 富文字編輯器 / 線上編輯器 |
| save / store (button) | 保存 |  | 儲存 |
| edit | 编辑 |  | 編輯 |
| delete / remove | 删除 / 移除 | remove = take out of a list or place; delete = destroy; not 删除 for Remove (old catalogue) | 刪除 / 移除 |
| cancel / OK / apply | 取消 / 确定 / 应用 |  | 取消 / 確定 / 套用 |
| discard | 放弃 |  | 捨棄 |
| create / new / add | 创建 / 新建 / 添加 |  | 建立 / 新增 / 新增 |
| copy / move / swap | 复制 / 移动 / 交换 |  | 複製 / 移動 / 交換 |
| back / next / previous | 返回 / 下一步 / 上一步 | in a pager: 上一页 / 下一页 | same |
| open / close | 打开 / 关闭 |  | 開啟 / 關閉 |
| select (mark items) / choose (an option) | 选择 / 选择 | select all: 全选 | 選取 / 選擇 |
| browse | 浏览 |  | 瀏覽 |
| preview | 预览 |  | 預覽 |
| view (button: to look at) / view (noun: module view) | 查看 / 视图 |  | 檢視 / 檢視 |
| upload / download | 上传 / 下载 |  | 上傳 / 下載 |
| import / export | 导入 / 导出 |  | 匯入 / 匯出 |
| search / advanced search | 搜索 / 高级搜索 |  | 搜尋 / 進階搜尋 |
| filter (verb / noun) | 筛选 / 筛选器 |  | 篩選 / 篩選器 |
| sort / sort order | 排序 / 顺序 |  | 排序 / 順序 |
| refresh / reload | 刷新 / 重新加载 |  | 重新整理 / 重新載入 |
| enable / disable | 启用 / 禁用 |  | 啟用 / 停用 |
| activate / deactivate (account, user) | 激活 / 停用 |  | 啟用 / 停用 |
| install / uninstall | 安装 / 卸载 |  | 安裝 / 解除安裝 |
| update / upgrade | 更新 / 升级 |  | 更新 / 升級 |
| click / double-click | 单击 / 双击 |  | 按一下 / 按兩下 |
| drag / drop | 拖动 / 放置 |  | 拖曳 / 放下 |
| print | 打印 |  | 列印 |
| retry / try again | 重试 / 重试 |  | 重試 / 再試一次 |
| file | 文件 |  | 檔案 |
| folder / directory | 文件夹 / 目录 |  | 資料夾 / 目錄 |
| path / file system | 路径 / 文件系统 |  | 路徑 / 檔案系統 |
| binary file | 二进制文件 |  | 二進位檔案 |
| image / image alias | 图片 / 图片别名 | not 图像 (old catalogue) except in technical compounds (图像处理) | 圖片 / 圖片別名 |
| video / audio | 视频 / 音频 |  | 影片 / 音訊 |
| font | 字体 |  | 字型 |
| database / table (database) | 数据库 / 数据表 |  | 資料庫 / 資料表 |
| table (in a page or text) | 表格 |  | same |
| field (form, database) | 字段 |  | 欄位 |
| column / row | 列 / 行 |  | 欄 / 列 |
| data / information | 数据 / 信息 |  | 資料 / 資訊 |
| message | 消息 |  | 訊息 |
| cache / clear cache | 缓存 / 清除缓存 |  | 快取 / 清除快取 |
| index / reindex | 索引 / 重建索引 |  | same |
| search engine | 搜索引擎 |  | 搜尋引擎 |
| keyword | 关键词 |  | 關鍵字 |
| tag (keyword, eztags) / tag (HTML, XML) | 标签 / HTML 标签 | an HTML or XML tag always with HTML/XML in front | 標籤 / HTML 標記 |
| format (file format) / formatting | 格式 / 格式设置 |  | 格式 / 格式設定 |
| settings / setup (admin tab) | 设置 / 设置 |  | 設定 / 設定 |
| configuration / configuration file | 配置 / 配置文件 |  | 組態 / 組態檔 |
| INI file / INI setting | INI 文件 / INI 设置 |  | INI 檔案 / INI 設定 |
| default | 默认 | not 缺省 (old catalogue) | 預設 |
| cron job / cronjob part | cron 任务 / cron 任务部分 |  | cron 工作 / cron 工作部分 |
| package | 软件包 |  | 套件 |
| extension | 扩展 |  | 擴充功能 |
| plugin / provider | 插件 / 提供程序 |  | 外掛程式 / 提供者 |
| server / web server | 服务器 / Web 服务器 |  | 伺服器 / Web 伺服器 |
| network | 网络 |  | 網路 |
| software / hardware | 软件 / 硬件 |  | 軟體 / 硬體 |
| program / code / source code | 程序 / 代码 / 源代码 |  | 程式 / 程式碼 / 原始碼 |
| function (PHP) / method | 函数 / 方法 |  | 函式 / 方法 |
| variable / parameter / argument | 变量 / 参数 / 参数 |  | 變數 / 參數 / 引數 |
| string / character / character set | 字符串 / 字符 / 字符集 |  | 字串 / 字元 / 字元集 |
| encoding | 编码 |  | 編碼 |
| interface (UI) / interface (PHP) | 界面 / 接口 |  | 介面 / 介面 |
| module / view (module view) | 模块 / 视图 |  | 模組 / 檢視 |
| link / broken link | 链接 / 失效链接 |  | 連結 / 失效連結 |
| menu / context menu | 菜单 / 快捷菜单 |  | 選單 / 快顯選單 |
| toolbar / sidebar | 工具栏 / 侧边栏 |  | 工具列 / 側邊欄 |
| tab | 选项卡 |  | 索引標籤 |
| page / layout | 页面 / 布局 | Exponential Layouts: 布局 | 頁面 / 版面配置 |
| block / zone | 区块 / 区域 |  | 區塊 / 區域 |
| navigation / breadcrumb | 导航 / 面包屑导航 |  | 導覽 / 導覽路徑 |
| icon / button | 图标 / 按钮 |  | 圖示 / 按鈕 |
| checkbox / drop-down list / list | 复选框 / 下拉列表 / 列表 |  | 核取方塊 / 下拉式清單 / 清單 |
| dialog / wizard | 对话框 / 向导 |  | 對話方塊 / 精靈 |
| window / browser | 窗口 / 浏览器 |  | 視窗 / 瀏覽器 |
| help / about | 帮助 / 关于 |  | 說明 / 關於 |
| log (file) / log viewer | 日志 / 日志查看器 | never the verb log in | 日誌 / 日誌檢視器 |
| debug / debug output | 调试 / 调试输出 |  | 偵錯 / 偵錯輸出 |
| error / warning / notice | 错误 / 警告 / 提示 |  | 錯誤 / 警告 / 提示 |
| report / statistics | 报告 / 统计 |  | 報告 / 統計 |
| modified / created (columns) | 修改时间 / 创建时间 |  | 修改時間 / 建立時間 |
| name / description / title | 名称 / 描述 / 标题 | a person's name: 姓名 | 名稱 / 描述 / 標題 |
| identifier / ID / remote ID | 标识符 / ID / 远程 ID |  | 識別碼 / ID / 遠端 ID |
| remote / local | 远程 / 本地 |  | 遠端 / 本機 |
| hidden / visible / visibility | 隐藏 / 可见 / 可见性 |  | 隱藏 / 可見 / 可見性 |
| priority | 优先级 |  | 優先順序 |
| unknown / none / all / any | 未知 / 无 / 全部 / 任意 |  | 未知 / 無 / 全部 / 任何 |
| archive (versions, orders) / archive file (.zip, .tar) | 归档 / 压缩包 |  | 封存 / 壓縮檔 |
| backup | 备份 |  | 備份 |
| storage / disk space | 存储 / 磁盘空间 |  | 儲存空間 / 磁碟空間 |
| memory / performance | 内存 / 性能 |  | 記憶體 / 效能 |
| process / thread (operating system) | 进程 / 线程 |  | 處理程序 / 執行緒 |
| queue / job / task | 队列 / 作业 / 任务 |  | 佇列 / 工作 / 任務 |
| schedule / scheduled | 计划 / 已计划 |  | 排程 / 已排程 |
| timeout | 超时 |  | 逾時 |
| request / response | 请求 / 响应 |  | 要求 / 回應 |
| session / cookie | 会话 / Cookie |  | 工作階段 / Cookie |
| token / API key | 令牌 / API 密钥 |  | 權杖 / API 金鑰 |
| credentials / certificate | 凭据 / 证书 |  | 認證 / 憑證 |
| authentication / two-factor authentication | 身份验证 / 双重身份验证 |  | 驗證 / 雙重要素驗證 |
| encryption / encrypted | 加密 / 已加密 |  | same |
| locale / time zone | 区域设置 / 时区 |  | 地區設定 / 時區 |
| project | 项目 |  | 專案 |
| shop / webshop | 商店 / 网店 |  | 商店 / 網路商店 |
| order (shop) / order (sort order) | 订单 / 顺序 |  | 訂單 / 順序 |
| basket / checkout / wish list | 购物车 / 结账 / 愿望清单 |  | 購物車 / 結帳 / 願望清單 |
| product / price / discount / currency | 商品 / 价格 / 折扣 / 货币 |  | 商品 / 價格 / 折扣 / 貨幣 |
| VAT / VAT type | 增值税 / 增值税类型 |  | 營業稅 / 營業稅類型 |
| environment: development / test / staging / production | 开发 / 测试 / 预发布 / 生产 | short forms DEV, TEST, STAGE, PROD stay | 開發 / 測試 / 預備 / 正式 |
| AI / model / prompt | AI / 模型 / 提示词 |  | AI / 模型 / 提示詞 |
| embedding / vector / chunk | 嵌入 / 向量 / 分块 |  | 嵌入 / 向量 / 分塊 |
| health / health check | 运行状况 / 运行状况检查 |  | 健康情況 / 健康情況檢查 |
| confirmation question ("Delete …?") | 确定要……吗？ | 确定要删除此类吗？ | 確定要……嗎？ |

## 5. Decisions after the translation

Settled from the terms that needed a decision in the 18 languages of 23 October 2026 and
from the context overrides they needed. They take precedence over the glossary.
Terms settled while comparing chi-CN and chi-TW are added here, with the distinction.

| English | Translation | Distinction |
|---|---|---|
| Store (button: save) | 保存 | the shop tab of the admin page layout (kernel\|design/admin/pagelayout): 商店; secrets ("file away", design/admin/secrets): 存入 — context overrides |
| Order (shop) / Order (sort order, priority) | 订单 / 顺序 | the word decides nothing, the context does (shop, orderview, basket vs. class/edit, bookmark sorting); context overrides |
| State (object state) / State (address) | 状态 / 州/省 | the address field of the shop and of user data: 州/省 |
| Login (button, verb) / Login (field: the user name) | 登录 / 用户名 |  |
| View (button) / View (noun: module view, debug panel) | 查看 / 视图 | chi-TW uses 檢視 for both; chi-CN keeps 查看 and 视图 apart |
| Up / Down (move in a list) | 上移 / 下移 | keys in the rich text editor: 向上键 / 向下键; expqueue "Up" (how long a worker runs): 运行时长 |
| Top / Bottom (position in an order) | 顶部 / 底部 | a position on the page: 上 / 下 |
| Delete (the key) | Delete 键 | key names stay in Latin letters: Enter 键, Esc 键 |
| destination (URL alias, wildcard) | 目标 | a place in the tree or an import target: 位置 |
| visibility (node) / invisible / hidden | 可见性 / 不可见 / 隐藏 |  |
| always available | 始终可用 | "available" in sentences: 可用 / 可以使用 |
| two-step sign-in / two-factor authentication | 两步验证 / 双重身份验证 |  |
| secret (settings, secret store) | 机密 | secret value: 机密值 |
| masked / unmasked; redact / redacted | 已遮盖 / 未遮盖; 遮盖 / 已遮盖 |  |
| activate / deactivate / reactivate; active | 激活 / 停用 / 重新激活; 活动 | enable is 启用 |
| warm (caches) / preload | 预热 / 预加载 |  |
| template operator / operator (a person) | 模板运算符 / 操作员 |  |
| finding / findings | 检查结果 | results of a search or a run: 结果 |
| purge / purged | 彻底删除 / 已彻底删除 |  |
| True / False (setting values) | 是 / 否 | code literals true / false stay in Latin letters |
| Any (policy limitation value) | 任意 |  |
| Restart (wizard) / restart (server, process) | 重新开始 / 重新启动 |  |
| live (run) / probe | 实时 / 探测 |  |
| staged (git) / staged review | 已暂存 / 暂存审阅 |  |
| dead letter / requeue / lease / retention | 死信 / 重新入队 / 租约 / 保留期 |  |
| extension point | 扩展点 |  |
| fetch / fetch function (template language) | 获取 / 获取函数 | the template function name fetch stays in code |
| persistent object | 持久化对象 |  |
| hits / misses / hit rate / evictions | 命中 / 未命中 / 命中率 / 逐出 |  |
| maintenance mode / maintenance window | 维护模式 / 维护时段 |  |
| Switch on / Switch off | 开启 / 关闭 |  |
| Pause / Resume / Skipped | 暂停 / 继续 / 已跳过 |  |
| Dismiss | 关闭 |  |
| contributors / third-party software | 贡献者 / 第三方软件 |  |
| navigation part | 导航部分 |  |
| orphan files / asset / node picker | 孤立文件 / 资源 / 节点选择器 |  |
| origin (of a setting value) | 来源 |  |
| hosting provider | 托管服务商 |  |

Harmonised after the review of the catalogues (24.10.2026). Order of choice: glossary,
then Microsoft zh-CN practice, then majority.

| English | Translation | Distinction |
|---|---|---|
| listener / event listener | 侦听器 / 事件侦听器 | Microsoft form (not 监听器); a server that listens on a port keeps 监听 |
| repository (package, git, SVN) / remote | 存储库 / 远程存储库 | not 仓库; one word for package repository and git repository |
| filter (noun) / filter event | 筛选器 / 筛选事件 | glossary; not 过滤器 |
| finetune / finetuning (installer) | 微调 | not 优化 |
| salutation | 称呼 | not 称谓 |
| do-not-contact list | 拒绝联系名单 | not 拒收名单; suppression list stays 抑制名单 |
| mail gate / gate log | 邮件网关 / 网关日志 | not 邮件关卡; the expmcp write gate stays 写入关卡, quality gates 质量门禁 |
| maintainer | 维护者 | not 维护人员 |
| cron job / cronjob part | cron 任务 / cron 任务部分 | always lower case, also in headings and after quotes; the daemon alone is Cron at the start of a sentence |
| preset | 方案 | column, debug bar, editor, AI and export presets alike; not 预置 / 预置项 |
| provider (AI, embedding, REST, scope) | 提供程序 | glossary; not 提供商; hosting or payment provider: 服务商 |
| thread (expai conversation) / thread (operating system, database) | 会话线程 / 线程 |  |
| held (job held by a budget) | 暂挂 | not 挂起 |
| contract (interface a class implements) | 契约 | not 约定 / 合约 |
