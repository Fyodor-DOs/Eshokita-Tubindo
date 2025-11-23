<?php
$isLoggedIn  = is_logged_in();
$displayName = current_user_name();
$profileId   = current_user_id();
?>

<div class="offcanvas offcanvas-start bg-white" tabindex="-1" id="offcanvasNavbar"
    aria-labelledby="offcanvasNavbarLabel">
    <div class="offcanvas-header">
        <div class="dropdown">
            <a class="btn btn-outline-primary dropdown-toggle text-truncate" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <?= esc($displayName) ?>
            </a>

            <ul class="dropdown-menu p-2">
                <?php if ($isLoggedIn): ?>
                    <li>
                        <a class="dropdown-item rounded" href="<?= $profileId ? site_url('user/detail/' . $profileId) : '#' ?>">
                            Edit Profile
                        </a>
                    </li>
                    <li><a class="dropdown-item rounded" href="<?= site_url('logout') ?>">Logout</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item rounded" href="<?= site_url('login') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <button type="button" class="btn-close btn-close-danger" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?= $this->include('components/partials/Menubar') ?>
    </div>
</div>