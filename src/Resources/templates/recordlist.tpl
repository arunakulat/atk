{atkconfig var="recordlist_onclick" smartyvar="recordlist_onclick"}
{if isset($formstart)}{$formstart}{/if}

<div class="recordListContainer">
    <table id="{$listid}" class="table table-bordered table-condensed recordList">
        <!-- header -->
        <tr>
            {section name=headerloop loop=$header}
                <th {if isset($header[headerloop].htmlattributes)}{$header[headerloop].htmlattributes}{/if}
                        {if $smarty.section.headerloop.index===0}class="recordListThFirst"{else}class="recordListTh"{/if}>
                    {if $header[headerloop].content != ""}{$header[headerloop].content}{else}&nbsp;{/if}
                </th>
            {/section}
        </tr>

        {if count($sort)}
            <!-- sort row -->
            <tr>
                {$sortstart}
                {foreach from=$sort item=col}
                    <th {if isset($col.htmlattributes)}{$col.htmlattributes}{/if}>
                        {if $col.content != ""}{$col.content}{else}&nbsp;{/if}
                    </th>
                {/foreach}
                {$sortend}
            </tr>
        {/if}

        {if count($search)}
            <!-- search row -->
            <tr>
                {$searchstart}
                {foreach from=$search item=col}
                    <th class="recordListSearch" {if isset($col.htmlattributes)}{$col.htmlattributes}{/if}>
                        {if $col.content != ""}{$col.content}{else}&nbsp;{/if}
                    </th>
                {/foreach}
                {$searchend}
            </tr>
        {/if}

        <!-- records -->
        {$liststart}
        {foreach from=$rows item=row}
            <tr id="{$row.id}" class="{$row.class}"
                {if $row.background!=""}style="background-color:{$row.background}" {/if}
                {if $recordlist_onclick}
                    onmouseover="highlightrow(this, '{$row.highlight}')"
                    onmouseout="resetrow(this)"
                    onclick="selectrow(this, '{$listid}', {$row.rownum})"
                {/if}
                    >
                {section name=colloop loop=$row.cols}
                    <{if $row.type == "subtotal"}th{else}td{/if}
                            class="{if $smarty.section.colloop.index===0}recordListTdFirst{else}recordListTd{/if}{if $row.cols[colloop].type == "data" && $recordlist_onclick} clickable{/if} row-type-{$row.cols[colloop].type}"
                            {if isset($row.cols[colloop].htmlattributes)}{$row.cols[colloop].htmlattributes}{/if}
                            {if $row.cols[colloop].type == "data" && $recordlist_onclick} onclick="rl_try('{$listid}', event, {$row.rownum}, ['select', 'edit', 'view'
                    ], false);"{/if}>
                        {if $row.cols[colloop].content != ""}{$row.cols[colloop].content}{else}&nbsp;{/if}
                    </{if $row.type == "subtotal"}th{else}td{/if}>
                {/section}
            </tr>
        {/foreach}
        {$listend}

        {if count($total)}
            <!-- totals row -->
            <tr>
                {foreach from=$total item=col}
                    <th {if isset($col.htmlattributes)}{$col.htmlattributes}{/if}>
                        {if $col.content != ""}{$col.content}{else}&nbsp;{/if}
                    </th>
                {/foreach}
            </tr>
        {/if}
    </table>

    {if $mra!=""}
        <!-- multirecord actions -->
        {if $editing}
            {$mra}
        {else}
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td valign="top">&gt;</td>
                    <td style="padding-top: 6px; padding-left: 5px">
                        {$mra}
                    </td>
                </tr>
            </table>
        {/if}
    {/if}
</div>

{if isset($formend)}{$formend}{/if}
