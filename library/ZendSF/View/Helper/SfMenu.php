<?php
/**
 * SfMenu.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of ZendSF.
 *
 * ZendSF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZendSF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * This class can add a 'span' tag to the 'a' tag when specified in the config file
 * using a 'span' element and setting it to 1. If no 'span' element is set or it is set
 * to 0 then the 'span' element is not generated unless a href is not specified.
 * example:
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <nav>
 *  <topMenu>
 *   <home>
 *    <label>Home</label>
 *    <span>1</span>
 *    <module>core</module>
 *    <controller>index</controller>
 *    <action>index</action>
 *   </home>
 *  </topMenu>
 * </nav>
 * </code>
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage View_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_View_Helper_SfMenu extends Zend_View_Helper_Navigation_Menu
{
    /**
     * @var string subIndicator
     */
    protected $_subIndicator = '';

    /**
     *
     * @param sring $container
     * @return ZendSF_View_Helper_SfMenu
     */
    public function sfMenu($container = null)
    {
        $this->log = Zend_Registry::get('log');

        if (is_string($container) &&
                Zend_Registry::isRegistered('siteMenu' . ucfirst($container))) {
            /* @var $container Zend_Navigation */
            $container = Zend_Registry::get('siteMenu' . ucfirst($container));
        }

        if ($container instanceof Zend_Navigation_Container) {
            $this->setContainer($container);
        }
        return $this;
    }

    /**
     * Gets the sub indicator.
     *
     * @return string
     */
    public function getSubIndicator()
    {
        return $this->_subIndicator;
    }

    /**
     * Sets the sub indicator.
     *
     * @param string $subIndicator
     * @return ZendSF_View_Helper_SfMenu
     */
    public function setSubIndicator($subIndicator)
    {
        $this->_subIndicator = (string) $subIndicator;
        return $this;
    }

    /**
     *
     * @param Zend_Navigation_Page $page
     * @return string
     */
    public function htmlify(Zend_Navigation_Page $page )
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // is page active?
        $activeClass = ($page->isActive()) ? 'active ' : '';

        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $activeClass . $page->getClass()
        );

        $properties = $page->getCustomProperties();

        // does page have a href?
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        // does it have a submenu?
        if (isset($properties['submenu'])) {
            $attribs['rel'] = $properties['submenu']['id'];
        }

        // add a span link?
        if (isset($properties['span']) && $properties['span'] == 1) {
            $spanStart = '<span>';
            $spanEnd = '</span>';
        } else {
            $spanStart = '';
            $spanEnd = '';
        }

        // does page have subpages?
        $sub_indicator = ($page->count()) ? $this->getSubIndicator() : '';

        return '<' . $element . $this->_htmlAttribs($attribs) . '>'
             . $spanStart
             . $this->view->escape($label)
             . $sub_indicator
             . $spanEnd
             . '</' . $element . '>';
    }

    protected function _renderMenu(Zend_Navigation_Container $container,
                                   $ulClass,
                                   $indent,
                                   $minDepth,
                                   $maxDepth,
                                   $onlyActive)
    {
        $html = '';

        // find deepest active
        if ($found = $this->findActive($container, $minDepth, $maxDepth)) {
            $foundPage = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
                            RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibilty
                continue;
            } else if ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } else if ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages() ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth ==  0) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else {
                    if ($ulSubClass && $ulSubId) {
                        $ulClass = ' id="' . $ulSubId . '" class="' . $ulSubClass . '"';
                    } else {
                        $ulClass = '';
                    }
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . self::EOL;
            } else if ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . self::EOL;
                    $html .= $ind . '</ul>' . self::EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            }

            $properties = $page->getCustomProperties();

            if (isset($properties['submenu'])) {
                $ulSubClass = $properties['submenu']['class'];
                $ulSubId = $properties['submenu']['id'];
            } else {
                $ulSubClass = null;
                $ulSubId = null;
            }

            // render li tag and page
            $liClass = $isActive ? ' class="active"' : '';
            $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
                   . $myIndent . '        ' . $this->htmlify($page) . self::EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . self::EOL
                       . $myIndent . '</ul>' . self::EOL;
            }
            $html = rtrim($html, self::EOL);
        }

        return $html;
    }
}
