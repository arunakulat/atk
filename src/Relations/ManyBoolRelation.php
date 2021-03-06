<?php

namespace Sintattica\Atk\Relations;

use Sintattica\Atk\Ui\Page;
use Sintattica\Atk\Core\Config;
use Sintattica\Atk\Core\Tools;
use Sintattica\Atk\Session\SessionManager;
use Sintattica\Atk\Core\Node;

/**
 * Many-to-many relation.
 *
 * The relation shows a list of available records, and a set of checkboxes
 * to link the records with the current record on the source side.
 *
 * @author Ivo Jansch <ivo@achievo.org>
 */
class ManyBoolRelation extends ManyToManyRelation
{
    /**
     * Attribute flag. When used the atkManyBoolRelation shows add links to add records for the related table.
     */
    const AF_MANYBOOL_AUTOLINK = 33554432;

    /**
     * Hides the select all, select none and inverse links.
     */
    const AF_MANYBOOL_NO_TOOLBAR = 67108864;

    public $m_cols = 3;

    /**
     * The flag indicating wether or not we should show the 'details' link.
     *
     * @var bool
     */
    private $m_showDetailsLink = true;

    /**
     * Return a piece of html code to edit the attribute.
     *
     * @param array $record Current record
     * @param string $fieldprefix The fieldprefix to put in front of the name
     *                            of any html form element for this attribute.
     * @param string $mode The mode we're in ('add' or 'edit')
     *
     * @return string piece of html code
     */
    public function edit($record, $fieldprefix, $mode)
    {
        $cols = $this->m_cols;
        $modcols = $cols - 1;
        $this->createDestination();
        $this->createLink();
        $result = '';

        $selectedPk = $this->getSelectedRecords($record);

        $recordset = $this->_getSelectableRecords($record, $mode);
        $total_records = count($recordset);
        if ($total_records > 0) {
            $page = Page::getInstance();
            $page->register_script(Config::getGlobal('assets_url').'javascript/class.atkprofileattribute.js');

            if (!$this->hasFlag(self::AF_MANYBOOL_NO_TOOLBAR)) {
                $result .= '<div align="left">
                      [<a href="javascript:void(0)" onclick="profile_checkAll(\''.$this->getHtmlId($fieldprefix).'\'); return false;">'.Tools::atktext('check_all',
                        'atk').'</a> <a href="javascript:void(0)" onclick="profile_checkNone(\''.$this->getHtmlId($fieldprefix).'\'); return false;">'.Tools::atktext('check_none',
                        'atk').'</a> <a href="javascript:void(0)" onclick="profile_checkInvert(\''.$this->getHtmlId($fieldprefix).'\'); return false;">'.Tools::atktext('invert_selection',
                        'atk').'</a>]</div>';
            }

            $result .= '<table border="0"><tr>';
            for ($i = 0; $i < $total_records; ++$i) {
                $detaillink = '&nbsp;';
                $selector = '';
                if (in_array($this->m_destInstance->primaryKey($recordset[$i]), $selectedPk)) {
                    $sel = 'checked';
                    if ($this->getShowDetailsLink() && !$this->m_linkInstance->hasFlag(Node::NF_NO_EDIT) && $this->m_linkInstance->allowed('edit')) {
                        $localPkAttr = $this->getOwnerInstance()->getAttribute($this->getOwnerInstance()->primaryKeyField());
                        $localValue = $localPkAttr->value2db($record);

                        $remotePkAttr = $this->getDestination()->getAttribute($this->getDestination()->primaryKeyField());
                        $remoteValue = $remotePkAttr->value2db($recordset[$i]);

                        $selector = $this->m_linkInstance->m_table.'.'.$this->getLocalKey().'='.$localValue.''.' AND '.$this->m_linkInstance->m_table.'.'.$this->getRemoteKey()."='".$remoteValue."'";
                        // Create link to details.
                        $detaillink = Tools::href(Tools::dispatch_url($this->m_link, 'edit', array('atkselector' => $selector)),
                            '['.Tools::atktext('details', 'atk').']', SessionManager::SESSION_NESTED, true);
                    }
                } else {
                    $sel = '';
                }

                $inputId = $this->getHtmlId($fieldprefix).'_'.$i;

                if (count($this->m_onchangecode)) {
                    $onchange = ' onChange="'.$inputId.'_onChange(this);"';
                    $this->_renderChangeHandler($fieldprefix, '_'.$i);
                } else {
                    $onchange = '';
                }

                $result .= '<td class="table"><input type="checkbox" id="'.$inputId.'" name="'.$this->getHtmlName($fieldprefix).'[]['.$this->getRemoteKey().']" value="'.$recordset[$i][$this->m_destInstance->primaryKeyField()].'" '.$this->getCSSClassAttribute('atkcheckbox').' '.$sel.$onchange.'></td><td class="table">'.'<label for="'.$inputId.'">'.$this->m_destInstance->descriptor($recordset[$i]).'</label>'.'</td><td class="table">'.$detaillink.'</td>';
                if ($i % $cols == $modcols) {
                    $result .= "</tr><tr>\n";
                }
            }
            $result .= "</tr></table>\n";
        } else {
            $nodename = $this->m_destInstance->m_type;
            $modulename = $this->m_destInstance->m_module;
            $result .= Tools::atktext('select_none', $modulename, $nodename).' ';
        }
        // Add the add link if self::AF_MANYBOOL_AUTOLINK used
        if (($this->hasFlag(self::AF_MANYBOOL_AUTOLINK)) && ($this->m_destInstance->allowed('add'))) {
            $result .= Tools::href(Tools::dispatch_url($this->m_destination, 'add'), $this->getAddLabel(), SessionManager::SESSION_NESTED)."\n";
        }

        return $result;
    }

    /**
     * Set the number of columns.
     *
     * @param int $cols
     */
    public function setCols($cols)
    {
        $this->m_cols = $cols;
    }

    /**
     * Returns true if the details link should be rendered.
     *
     * @return bool
     */
    public function getShowDetailsLink()
    {
        return $this->m_showDetailsLink;
    }

    /**
     * Set wether or not we should show the details link.
     *
     * @param bool $status
     *
     * @return ManyToManyRelation
     */
    public function setShowDetailsLink($status)
    {
        $this->m_showDetailsLink = ($status == true);

        return $this;
    }
}
