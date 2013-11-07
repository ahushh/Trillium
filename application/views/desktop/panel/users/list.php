<div class="title"><a href="<?= $this->url('panel') ?>"><?= $this->__('Control panel') ?></a> &raquo; <?= $this->__('List of the users') ?></div>
<a class="button" href="<?= $this->url('panel.users.manage') ?>"><?= $this->__('Create user') ?></a>
<?= !empty($this->list) ? '<div class="list">' . $this->list . '</div>' : '<div class="listEmpty">' . $this->__('List is empty') . '</div>' ?>