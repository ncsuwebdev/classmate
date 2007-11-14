<?php /* Smarty version 2.6.18, created on 2007-11-13 14:19:58
         compiled from ./application/views/scripts/site.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'empty_alt', './application/views/scripts/site.tpl', 44, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title><?php echo $this->_tpl_vars['appTitle']; ?>
 - <?php echo $this->_tpl_vars['title']; ?>
</title>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/css/site.css" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/favicon.ico" />
<script language="javascript" type="text/javascript" src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/scripts/mootools.v1.11.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/packages/calendar/calendar_stripped.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/packages/calendar/calendar-setup_stripped.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/packages/calendar/calendar-en.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/packages/calendar/calendar-red.css" />
<?php $_from = $this->_tpl_vars['javascript']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['script']):
?>
<script language="javascript" type="text/javascript" src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/scripts/<?php echo $this->_tpl_vars['script']; ?>
"></script>
<?php endforeach; endif; unset($_from); ?>
</head>
<body>

<div class="top"></div>
<div class="header">
    <div class="menu">
    <?php $_from = $this->_tpl_vars['tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['tabs'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['tabs']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['t']):
        $this->_foreach['tabs']['iteration']++;
?>
    <?php if ($this->_tpl_vars['branch'] == $this->_tpl_vars['t']['module']): ?>
    <?php $this->assign('sectionTitle', $this->_tpl_vars['t']['display']); ?>
    <?php endif; ?>
    <div<?php if ($this->_tpl_vars['branch'] == $this->_tpl_vars['t']['module']): ?> id="menuItemActive"<?php endif; ?>><a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/<?php if ($this->_tpl_vars['t']['module'] == 'default'): ?>index<?php else: ?><?php echo $this->_tpl_vars['t']['module']; ?>
<?php endif; ?>/"><?php echo $this->_tpl_vars['t']['display']; ?>
</a></div>
    <?php endforeach; endif; unset($_from); ?>
    <?php unset($this->_sections['loop']);
$this->_sections['loop']['name'] = 'loop';
$this->_sections['loop']['start'] = (int)$this->_foreach['tabs']['total'];
$this->_sections['loop']['loop'] = is_array($_loop=12) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loop']['show'] = true;
$this->_sections['loop']['max'] = $this->_sections['loop']['loop'];
$this->_sections['loop']['step'] = 1;
if ($this->_sections['loop']['start'] < 0)
    $this->_sections['loop']['start'] = max($this->_sections['loop']['step'] > 0 ? 0 : -1, $this->_sections['loop']['loop'] + $this->_sections['loop']['start']);
else
    $this->_sections['loop']['start'] = min($this->_sections['loop']['start'], $this->_sections['loop']['step'] > 0 ? $this->_sections['loop']['loop'] : $this->_sections['loop']['loop']-1);
if ($this->_sections['loop']['show']) {
    $this->_sections['loop']['total'] = min(ceil(($this->_sections['loop']['step'] > 0 ? $this->_sections['loop']['loop'] - $this->_sections['loop']['start'] : $this->_sections['loop']['start']+1)/abs($this->_sections['loop']['step'])), $this->_sections['loop']['max']);
    if ($this->_sections['loop']['total'] == 0)
        $this->_sections['loop']['show'] = false;
} else
    $this->_sections['loop']['total'] = 0;
if ($this->_sections['loop']['show']):

            for ($this->_sections['loop']['index'] = $this->_sections['loop']['start'], $this->_sections['loop']['iteration'] = 1;
                 $this->_sections['loop']['iteration'] <= $this->_sections['loop']['total'];
                 $this->_sections['loop']['index'] += $this->_sections['loop']['step'], $this->_sections['loop']['iteration']++):
$this->_sections['loop']['rownum'] = $this->_sections['loop']['iteration'];
$this->_sections['loop']['index_prev'] = $this->_sections['loop']['index'] - $this->_sections['loop']['step'];
$this->_sections['loop']['index_next'] = $this->_sections['loop']['index'] + $this->_sections['loop']['step'];
$this->_sections['loop']['first']      = ($this->_sections['loop']['iteration'] == 1);
$this->_sections['loop']['last']       = ($this->_sections['loop']['iteration'] == $this->_sections['loop']['total']);
?>
    <div><a>&nbsp;</a></div>
    <?php endfor; endif; ?>
	</div>
</div>
<div class="content_left">
	<div class="date"><?php echo $this->_tpl_vars['title']; ?>
</div>
	<div class="newsitem">
	<?php echo $this->_tpl_vars['actionTemplate']; ?>

	</div>

</div>
<div class="content_right">
	<div class="links">
		<div class="title"><?php echo $this->_tpl_vars['sectionTitle']; ?>
 Navigation</div>
        <?php $_from = $this->_tpl_vars['subnav']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['subnav'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['subnav']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['s']):
        $this->_foreach['subnav']['iteration']++;
?>
            <a<?php if ($this->_tpl_vars['subNavSelect'] == $this->_tpl_vars['s']['select']): ?> class="active"<?php endif; ?> href="<?php echo $this->_tpl_vars['s']['link']; ?>
" target="<?php echo ((is_array($_tmp=$this->_tpl_vars['s']['target'])) ? $this->_run_mod_handler('empty_alt', true, $_tmp, '_self') : smarty_modifier_empty_alt($_tmp, '_self')); ?>
"><?php echo $this->_tpl_vars['s']['display']; ?>
</a>
            <div class="line"><span></span></div>
        <?php endforeach; endif; unset($_from); ?>
        <div class="title">Aerial</div>
        <a href="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/bug/add">Report a Bug</a>
        <div class="line"><span></span></div>

        <div class="line"><span></span></div>
	</div>
</div>
<div id="oitocfooter">
    <div class="left">
        <a href="http://framework.zend.com" target="_blank"><img align="left" src="http://framework.zend.com/images/PoweredBy_ZF_4DarkBG.png" width="127" height="25" alt="Powered By Zend Framework" /></a>

    </div>
    <div class="right">
        <a href="http://itdapps.ncsu.edu/"><img src="http://itdapps5.ncsu.edu/htdocs/public/images/minilogo.png" width="128" height="21" alt="OIT - Outreach Technologies" /></a>
    </div>
    &copy; <?php echo $this->_tpl_vars['copyrightDate']; ?>
 North Carolina State University
</div>
</body>
</html>