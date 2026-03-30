<?php

use yii\helpers\Url;
?>

<div class="topbar d-flex justify-content-between align-items-center px-3 bg-white border-bottom"
    style="height:60px;">

    <!-- Sidebar Toggle -->
    <div class="sidebar-toggle" style="cursor:pointer;">
        <i class="bi bi-list fs-3"></i>
    </div>

    <div class="d-flex align-items-center gap-3">

        <!-- Notification -->
        <div class="position-relative">
            <div id="bellBtn"
                class="d-flex align-items-center justify-content-center border rounded-circle"
                style="width:36px;height:36px;cursor:pointer;">
                <i class="bi bi-bell fs-6"></i>
            </div>

            <!-- Notification Popup -->
            <div id="notificationBox" class="shadow border rounded bg-white p-2"
                style="width:220px; position:absolute; top:45px; right:0; display:none; z-index:1000;">
                <small class="text-muted">No notifications</small>
            </div>
        </div>


        <!-- Avatar Dropdown -->
        <div class="dropdown">
            <a
                class="d-flex align-items-center"
                data-bs-toggle="dropdown"
                href="javascript:void(0)">

                <img
                    src="<?= Yii::$app->avatar->get(Yii::$app->user->identity) ?>"
                    alt="Profile"
                    width="36"
                    height="36"
                    class="rounded-circle border"
                    style="object-fit:cover;">
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item" href="<?= Url::to(['/user/profile']) ?>">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= Url::to(['/site/logout']) ?>" data-method="post">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('.sidebar-toggle');

        if (toggle) {
            toggle.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }
    });
</script>

<script>
    const bell = document.getElementById('bellBtn');
    const box = document.getElementById('notificationBox');

    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        box.style.display = box.style.display === 'block' ? 'none' : 'block';
    });

    // click outside to close
    document.addEventListener('click', function() {
        box.style.display = 'none';
    });
</script>