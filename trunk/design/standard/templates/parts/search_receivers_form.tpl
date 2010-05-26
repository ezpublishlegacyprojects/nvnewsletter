<form name="search_receivers" method="get" action={concat('/nvnewsletter/search_receivers/' )|ezurl}>

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">
    {'Search receivers'|i18n('design/nvnewsletter')|wash}
</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

    <div class="block float-break">

        <div class="block float-break">
            <label for="SearchText">{'Search'|i18n('design/nvnewsletter')}:</label>
            <input type="text" name="SearchText" id="SearchText" class="halfbox" value="{$searchText|wash}" />
        </div>
        
        {def $groups = fetch( 'nvnewsletter', 'groups' )}
        
        {if $groups}
        <div class="block float-break">
            <label for="SearchGroup">{'Group'|i18n('design/nvnewsletter')}:</label>
            <select name="SearchGroup" id="SearchGroup" class="halfbox">
                <option value="">{'All groups'|i18n('design/nvnewsletter')}</option>
            {foreach $groups as $group}
                <option value="{$group.id}"{if $search_group|eq($group.id)} selected="selected"{/if}>{$group.group_name|wash}</option>
            {/foreach}
            </select>
        </div>
        {/if}
        
        <div class="block float-break">
            <label>{'Search from'|i18n('design/nvnewsletter')}:</label>
            <label class="checkbox"><input type="radio" name="SearchFrom" value="email"{if or( $search_from|eq( 'email' ), is_set( $search_from )|not )} checked="checked"{/if} /> Email</label>
            <label class="checkbox"><input type="radio" name="SearchFrom" value="fields"{if $search_from|eq( 'fields' )} checked="checked"{/if} /> User fields</label>
        </div>
        
        <div class="block float-break">
            {def $wildcard    = ezini( 'SearchSettings', 'AllowWildcard', 'nvnewsletter.ini' )
                 $wildcardPre = ezini( 'SearchSettings', 'AllowWildcardPre', 'nvnewsletter.ini' )}
            {if $wildcardPre|eq('enabled')}
                <strong>Note: </strong>{'Wildcard search with *keyword* is enabled.'}
            {elseif $wildcard|eq('enabled')}
                <strong>Note: </strong>{'Wildcard search with keyword* is enabled.'}
            {/if}
        </div>
        
    </div>

    <div class="break"></div>

</div>

{* DESIGN: Content END *}</div></div></div>
    {* Buttons. *}
    <div class="controlbar" >
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
        <div class="block">
            <input class="button" type="submit" value="{'Search'|i18n('design/nvnewsletter')}" title="{'Search'|i18n('design/nvnewsletter')}" />
        </div>
    </div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</form>