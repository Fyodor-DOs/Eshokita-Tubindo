<?php
// Set active class for the current page (segment1), assuming that segment2 and segment3 indicate the next levels
$active = isset($segment2) ? '' : 'active';
$segment2 = isset($segment2) ? $segment2 : ''; // Ensure segment2 is an empty string if not set
$segment3 = isset($segment3) ? $segment3 : ''; // Ensure segment3 is an empty string if not set

// Convert segment1 to URL format (replace spaces with hyphens)
$segment1Url = str_replace(' ', '-', strtolower($segment1));
?>

<h3 class="my-0"><?= ucwords($segment1) ?></h3>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?= base_url('/dashboard') ?>" class="text-black">
                <i class="bi bi-house-door"></i>
            </a>
        </li>
        <!-- Check if segment2 exists, and adjust link display accordingly -->
        <li class="breadcrumb-item <?= $active ?>" aria-current="page">
            <?php if ($segment2): ?>
                <a href="<?= base_url($segment1Url) ?>" class="text-black text-decoration-none"><?= ucwords($segment1) ?></a>
            <?php else: ?>
                <?= ucwords($segment1) ?>
            <?php endif; ?>
        </li>

        <!-- If segment2 exists, add it as the last breadcrumb item -->
        <?php if ($segment2): ?>
            <li class="breadcrumb-item active" aria-current="page"><?= ucwords($segment2) ?></li>
        <?php endif; ?>

        <!-- If segment3 exists, add it as the last breadcrumb item -->
        <?php if ($segment3): ?>
            <li class="breadcrumb-item active" aria-current="page"><?= $segment3 ?></li>
        <?php endif; ?>
    </ol>
</nav>