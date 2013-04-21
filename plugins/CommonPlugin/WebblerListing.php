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
 * This class overrides some methods of the WebblerListing class
 * 
 */
class CommonPlugin_WebblerListing extends WebblerListing
{
    public function __construct($title = '', $help = '')
    {
        parent::__construct($title, $help);
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /*
     *    Override parent methods to convert value and url to html entities
     */
    public function addElement($element, $url = '', $colsize = '')
    {
        parent::addElement($element, htmlspecialchars($url), $colsize);
        parent::setClass($element, 'row1');
    }

    public function addColumn($name, $column_name, $value, $url = '', $align = '')
    {
        parent::addColumn($name, $column_name, htmlspecialchars($value, ENT_QUOTES), htmlspecialchars($url), $align);
    }

    public function addRow($name, $row_name, $value, $url = '', $align = '')
    {
        parent::addRow($name, $row_name, nl2br(htmlspecialchars($value, ENT_QUOTES)), htmlspecialchars($url), $align);
    }

    /*
     *    Additional convenience methods
     */
    public function addColumnEmail($name, $column_name, $value, $url = '', $align = '')
    {
        parent::addColumn($name, $column_name, str_replace('@', '@&#8203;', htmlspecialchars($value, ENT_QUOTES)), htmlspecialchars($url), $align);
    }

    public function addColumnHtml($name, $column_name, $value, $url = '', $align = '')
    {
        parent::addColumn($name, $column_name, $value, htmlspecialchars($url), $align);
    }

    public function addRowHtml($name, $row_name, $value, $url = '', $align = '')
    {
        parent::addRow($name, $row_name, $value, htmlspecialchars($url), $align);
    }
    /*
     *    Override parent method to provide case-insensitive sorting
     */
    public function cmp($a, $b)
    {
        $sortcol = urldecode($_GET['sortby']);

        if (!is_array($a) || !is_array($b)) return 0;
        $val1 = strtolower(strip_tags($a['columns'][$sortcol]['value']));
        $val2 = strtolower(strip_tags($b['columns'][$sortcol]['value']));

        if ($val1 == $val2) return 0;
        return $val1 < $val2 ? -1 : 1;
    }
    /*
     *    Override parent method to fix php error messages on usort()
     */
     public function display($add_index = 0)
    {
        // Turn-off error reporting within core phplist
        $level = error_reporting(0);
        $html = parent::display($add_index);
        error_reporting($level);
        return $html;
    }

    /*
     *    Override parent methods to allow all columns to be sorted
     */
    public function listingHeader() 
    {
        $tophelp = '';
        if (!sizeof($this->columns)) {
            $tophelp = $this->help;
        }
        $html = '<tr valign="top">';
        $html .= sprintf(
            '<td><a name="%s"></a><div class="listinghdname">%s%s</div></td>',
            str_replace(" ","_",htmlspecialchars(strtolower($this->title))),
            $tophelp,$this->title
        );
        
        foreach ($this->columns as $column => $columnname) {
            if ($this->sortby[$columnname] && $this->sort) {
                $display = CommonPlugin_PageLink::create(
                    null, $columnname, 
                    array_merge($_GET, array('sortby' => $columnname))
                );
            } else {
                $display = $columnname;
            }
            $html .= sprintf('<td><div class="listinghdelement">%s</div></td>',$display);
        }

        $html .= '</tr>';
        return $html;
    }
}
