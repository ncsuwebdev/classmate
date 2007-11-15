<?php
/**
 * Cyclone
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * This license is also available via the world-wide-web at
 * http://itdapps.ncsu.edu/bsd.txt
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to itappdev@ncsu.edu so we can send you a copy immediately.
 *
 * @package    Cyclone
 * @subpackage Internal_View_Smarty
 * @category   View Handler
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 * @license    http://itdapps.ncsu.edu/bsd.txt  BSD License
 * @author     Jason Austin <jason_austin@ncsu.edu>
 * @author     Garrison Locke <garrison_locke@ncsu.edu>
 * @see        http://itdapps.ncsu.edu
 * @version    SVN: $Id: Smarty.php 155 2007-07-19 19:44:26Z jfaustin@EOS.NCSU.EDU $
 */

/**
 * This view adapter allows pages to be rendered through the smarty templating engine
 *
 * @package    Cyclone
 * @subpackage Internal_View_Smarty
 * @category   View Handler
 * @see        http://smarty.php.net
 * @copyright  Copyright (c) 2007 NC State University Information Technology Division
 */
class Internal_View_Smarty extends Zend_View_Abstract
{
    /**
     * Smarty object
     *
     * @var Smarty
     */
    protected $_smarty = null;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);

        require_once 'Smarty/Smarty.class.php';
        $this->_smarty = new Smarty();
        $this->_smarty->caching = false;
        $this->_smarty->compile_dir  = './smarty/templates_c';
        $this->_smarty->cache_dir    = './smarty/cache';
        $this->_smarty->config_dir   = './smarty/configs';
        $this->_smarty->template_dir = './';
    }

    /**
     * Renders the page through smarty
     *
     */
    protected function _run()
    {
        $args = func_get_args();

        $template = $args[0];

        $this->_smarty->display($template);
    }

    /**
     * Overrides the __set method of Zend_View to assign the var through smarty as
     * well.
     *
     * @param string $var
     * @param mixed $val
     */
    public function __set($var, $val)
    {
        $this->_smarty->assign($var, $val);
        parent::__set($var, $val);
    }

    /**
     * Escapes the passed variable
     *
     * @param mixed $var
     * @return mixed
     */
    public function escape($var)
    {
        if (is_string($var)) {
            return parent::escape($var);
        }
        elseif (is_array($var)) {
            foreach ($var as $key => $val) {
                $var[$key] = $this->escape($val);
            }
            return $var;
        }
        else {
            return $var;
        }
    }
}