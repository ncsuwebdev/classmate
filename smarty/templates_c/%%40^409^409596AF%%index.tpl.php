<?php /* Smarty version 2.6.18, created on 2007-11-13 14:21:17
         compiled from ./application/modules/default/views/scripts/index/index.tpl */ ?>
<p>
Aerial is a collection of utilities designed to manage the classroom technology on campus at NC State University.
</p>

<?php if ($this->_tpl_vars['flickrImage']): ?>
<div id="flickr">
    <a href="<?php echo $this->_tpl_vars['flickrImage']['clickUri']; ?>
" target="_blank">
        <img src="<?php echo $this->_tpl_vars['flickrImage']['uri']; ?>
" width="<?php echo $this->_tpl_vars['flickrImage']['width']; ?>
" height="<?php echo $this->_tpl_vars['flickrImage']['height']; ?>
" alt="Flickr Image" />
    </a>
    <br /><br />
    Random image provided by <a target="_blank" href="http://flickr.com/search/?q=nc+state"><img src="<?php echo $this->_tpl_vars['sitePrefix']; ?>
/public/images/flickr.png" width="50" height="50" alt="flickr" /></a>
</div>
<?php endif; ?>