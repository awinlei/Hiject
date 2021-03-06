<?php if ($this->user->hasAccess('ConfigController', 'index')): ?>
<li class="dropdown">
    <a href="#" class="dropdown-menu"><i class="fa fa-wrench"></i> <i class="fa fa-caret-down"></i></a>
    <ul>
            <li>
                <i class="fa fa-user"></i>
                <?= $this->url->link(t('Users management'), 'UserController', 'index') ?>
            </li>
            <li>
                <i class="fa fa-group"></i>
                <?= $this->url->link(t('Groups management'), 'GroupController', 'index') ?>
            </li>
            <li>
                <i class="fa fa-plug"></i>
                <?= $this->url->link(t('Plugins management'), 'PluginController', 'show') ?>
            </li>
            <li>
                <i class="fa fa-gears"></i>
                <?= $this->url->link(t('Application settings'), 'ConfigController', 'index') ?>
            </li>
    </ul>
</li>
<?php endif ?>
