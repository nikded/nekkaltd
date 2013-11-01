<?php
/**
 * CommonPlugin for phplist
 * 
 * This file is a part of CommonPlugin.
 *
 * @category  phplist
 * @package   CommonPlugin
 * @author    Duncan Cameron
 * @copyright 2011-2012 Duncan Cameron
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License, Version 3
 */


/**
 * Template for the attribute search and select widget form
 */

/**
 *
 * Available fields
 * - $showSearch: whether to show the search fields
 * - $model: the model
 *        searchTerm: term to search on
 *        searchBy: attribute to search on 
 *        attributes: associative array of user attributes
 *        listID: selected list to filter
 *        lists: associative array of lists
 *        selectedAttrs: array of selected attributes
 *        unconfirmed: show only unconfirmed users
 *        blacklisted: show only blacklisted users
 */
?>
<style type="text/css">
div.inline {
    display: inline;
    white-space: nowrap
}
.inline label{
    display: inline;
}
input[type="text"], select {
    width: auto !important;
    display: inline !important;
}
</style>
<form method='post'>
    <fieldset>
        <?php if ($showSearch): ?>
        <div style='padding-bottom: 10px;'>
        <?php echo $this->i18n->get('Search for'); ?>:&nbsp;
            <input size="20" type="text" value='<?php echo $model->searchTerm; ?>' name="SearchForm[searchTerm]" id="SearchForm_searchTerm" />
            <?php echo CHtml::dropDownList(
            'SearchForm[searchBy]', $model->searchBy, CHtml::listData($model->attributes, 'id', 'name')
            ); ?>&nbsp;
            <?php echo $this->i18n->get('List'); ?>:&nbsp;
            <?php echo CHtml::dropDownList(
                'SearchForm[listID]', $model->listID,
                CHtml::listData($model->lists, 'id', 'name'),
                array('prompt' => 'All')
            ); ?>
            <div class='inline'>
            <?php echo CHtml::checkBox(
                'SearchForm[unconfirmed]',
                $model->unconfirmed,
                array('uncheckValue' => 0)
            ); ?>
            <?php echo CHtml::label($this->i18n->get('unconfirmed_caption'), 'SearchForm_unconfirmed'); ?>
            <?php echo CHtml::checkBox(
                'SearchForm[blacklisted]',
                $model->blacklisted,
                array('uncheckValue' => 0)
            ); ?>
            <?php echo CHtml::label($this->i18n->get('blacklisted_caption'), 'SearchForm_blacklisted'); ?>
            </div>
        </div>
        <?php endif; ?>
        <div class='clear'></div>
        <div>
        <?php echo CHtml::checkBoxList(
            'SearchForm[selectedAttrs]',
            $model->selectedAttrs,
             CHtml::listData($model->attributes, 'id', 'name'),
            array(
                'separator' => ' ', 'uncheckValue' => 0,
                'template' => '<div class="inline">{input} {label}</div>'
            )
        ); ?>
            <input type='submit' name='SearchForm[submit]' value='<?php echo $this->i18n->get('Show'); ?>' />
        </div>
    </fieldset>
</form>
