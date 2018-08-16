<div class="wrap">
<h1>
<?php
    echo __('Profile Options','jms-patient-profile');
?> <a href="
<?php
global $wp;
echo $wp->request;
?>admin.php?page=jms-patient-profile-sub1&action=new" class="page-title-action">
<?php
    echo __('New Option','jms-patient-profile');
?>
</a></h1>


<form id="posts-filter" method="get">

<!--
<p class="search-box">
	<label class="screen-reader-text" for="post-search-input">搜索文章:</label>
	<input type="search" id="post-search-input" name="s" value="">
	<input type="submit" id="search-submit" class="button" value="搜索文章">
</p>
-->

<input type="hidden" name="post_status" class="post_status_page" value="all">
<input type="hidden" name="post_type" class="post_type_page" value="post">
<?php wp_nonce_field( 'new_profile_field' ); ?>

<!--
<div class="tablenav top">
    <div class="alignleft actions bulkactions">
        <label for="bulk-action-selector-top" class="screen-reader-text">选择批量操作</label>
        <select name="action" id="bulk-action-selector-top">
        <option value="-1">批量操作</option>
            <option value="edit" class="hide-if-no-js">编辑</option>
            <option value="trash">移至回收站</option>
        </select>
        <input type="submit" id="doaction" class="button action" value="应用">
    </div>

    <div class="alignleft actions">
        <label for="filter-by-date" class="screen-reader-text">按日期筛选</label>
        <select name="m" id="filter-by-date">
            <option selected="selected" value="0">全部日期</option>
            <option value="201611">2016年十一月</option>
        </select>
        <label class="screen-reader-text" for="cat">按分类过滤</label>
        <select name="cat" id="cat" class="postform">
            <option value="0">所有分类目录</option>
            <option class="level-0" value="1">未分类</option>
        </select>
        <input type="submit" name="filter_action" id="post-query-submit" class="button" value="筛选">
    </div>

    <div class="tablenav-pages one-page">
        <span class="displaying-num">3项目</span>
        <span class="pagination-links">
            <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
            <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
            <span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text">页，共<span class="total-pages">1</span>页</span></span>
            <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
            <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
        </span>
    </div>
    <br class="clear">
</div>
-->

<h2 class="screen-reader-text">文章列表</h2>

<table class="wp-list-table widefat fixed striped posts">
	<thead>
	<tr>
		<td id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
            <!--<input id="cb-select-all-1" type="checkbox">-->
        </td>
        <th scope="col" id="title" class="manage-column column-title column-primary">
            <?php echo __('Item Name','jms-patient-profile');?>
        </th>
        <th scope="col" id="author" class="manage-column column-author">
            <?php echo __('Item Type','jms-patient-profile');?>
        </th>
        <th scope="col" id="categories" class="manage-column column-categories">
            <?php echo __('Item Order','jms-patient-profile');?>
        </th>
        <th scope="col" id="categories" class="manage-column column-categories">
            <?php echo __('Create Date','jms-patient-profile');?>
        </th>
    </tr>
	</thead>

	<tbody id="the-list">
    <?php
        foreach($result as $data) {
    ?>
		<tr id="post-20" class="iedit author-self level-0 post-20 type-post status-publish format-standard hentry category-uncategorized">
			<th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-20">选择文章2</label>
                <!--<input id="cb-select-20" type="checkbox" name="post[]" value="20">-->
                <div class="locked-indicator"></div>
            </th>
            <td class="title column-title has-row-actions column-primary page-title">
                <strong><a class="row-title" href="<?php echo $wp->request; ?>admin.php?page=jms-patient-profile-sub1&id=<?php echo $data["id"];?>&action=edit"><?php echo $data["name"]; ?></a></strong>
                <div class="row-actions">
                    <span class="edit"><a href="<?php echo $wp->request; ?>admin.php?page=jms-patient-profile-sub1&id=<?php echo $data["id"];?>&action=edit">
                    <?php echo __('Edit','jms-patient-profile'); ?>
                    </a> | </span>
                    <span class="trash"><a href="<?php echo $wp->request; ?>admin.php?page=jms-patient-profile-sub1&id=<?php echo $data["id"];?>&action=delete&_wpnonce=<?php echo wp_create_nonce( 'delete-action_'.$data["id"] );?>" class="submitdelete">移至回收站</a> | </span>
                </div>
            </td>
    
            <td class="author column-author">
                <?php
                    switch($data["type"]) {
                        case 1: echo __('Text','jms-patient-profile'); break;
                        case 2: echo __('Number','jms-patient-profile'); break;
                        case 3: echo __('Image','jms-patient-profile'); break;
                        case 4: echo __('File','jms-patient-profile'); break;
                        case 5: echo __('Description','jms-patient-profile'); break;
                        default: echo __('Data Error','jms-patient-profile'); break;
                    }
                ?>
            </td>
            
            <td class="categories column-categories">
                <?php echo $data["weight"]; ?>
            </td>
            
            <td class="date column-date" data-colname="日期"><abbr title="2016/11/07 13:30:52"><?php echo $data["create_date"]; ?></abbr></td>
        </tr>
    <?php
        }
    ?>
	</tbody>

	<tfoot>
   	</tfoot>
</table>

<!--
	<div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-bottom" class="screen-reader-text">选择批量操作</label>
            <select name="action2" id="bulk-action-selector-bottom">
                <option value="-1">批量操作</option>
                <option value="edit" class="hide-if-no-js">编辑</option>
                <option value="trash">移至回收站</option>
            </select>
            <input type="submit" id="doaction2" class="button action" value="应用">
		</div>

        <div class="tablenav-pages one-page">
            <span class="displaying-num">3项目</span>
            <span class="pagination-links">
                <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <span class="screen-reader-text">当前页</span>
                <span id="table-paging" class="paging-input">
                    <span class="tablenav-paging-text">第1页，共<span class="total-pages">1</span>页</span>
                </span>
                <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
            </span>
        </div>
        <br class="clear">
	</div>
-->
</form>
<br class="clear">
</div>