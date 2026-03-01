
  <!-- Header area: START -->
  <header id="headerMain" class="header-main">
    {* include uri='design:menu/topbar.tpl' *}
    <nav class="navbar-main">
        <div class="container">
            <a href={"/"|ezurl} title="{ezini('SiteSettings','SiteName')}" class="navbar-brand">
                <img src={'logo.png'|ezimage()} alt="{$pagedesign.data_map.image.content[logo].text}" width="{$pagedesign.data_map.image.content[original].width}" height="{$pagedesign.data_map.image.content[original].height}" />
                {*
                {if $pagedesign.data_map.image.content.is_valid|not()}
                    {ezini('SiteSettings','SiteName')}
                {else}
                    <img src={$pagedesign.data_map.image.content[original].full_path|ezroot} alt="{$pagedesign.data_map.image.content[logo].text}" width="{$pagedesign.data_map.image.content[original].width}" height="{$pagedesign.data_map.image.content[original].height}" />
                {/if}
                *}
            </a>
            <button type="button" class="navbar-toggle" data-collapse-menu="#navbarCollapse" aria-label="Menu Icon">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div id="navbarCollapse" class="navbar-collapse">
                {* include uri='design:menu/multi-dropdown.tpl' *}
                {include uri='design:menu/dropdown.tpl'}
            </div>
            <a href="/shop/cart"><i class="fa-solid fa-cart-shopping fa-2xl"></i></a>
    </nav>
  </header>
  <!-- Header area: END -->