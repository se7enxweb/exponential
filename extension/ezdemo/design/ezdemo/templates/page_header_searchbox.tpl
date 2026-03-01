<form class="span4 form-search" method="get" action="{'/content/search'|ezurl( 'no' )}" id="site-wide-search">
    <label class="pull-right">
        <span class="hidden">{'Search'|i18n('design/ezdemo/pagelayout')}</span>
        {if $pagedata.is_edit}
            <input class="search-query span3" type="search" name="SearchText" id="site-wide-search-field" placeholder="{'Search text'|i18n('design/ezdemo/pagelayout')}" disabled="disabled" />
        {else}
            <input class="search-query span3" type="search" name="SearchText" id="site-wide-search-field" placeholder="{'Search text'|i18n('design/ezdemo/pagelayout')}" />
            {if eq( $ui_context, 'browse' )}
             <input name="Mode" type="hidden" value="browse" />
            {/if}
        {/if}
    </label>
</form>
