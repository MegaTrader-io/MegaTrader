<div class="mt-navbar__user">
    <?php if (is_user_logged_in()): ?>
        <?php get_template_part('template-parts/my-profile', null, [
                'avatar_size' => 48,
                'go_to_dashboard' => true,
                'hide_user_information' => true,
        ]); ?>
    <?php else: ?>
        <div class="mt-navbar__auth">
            <a href="/auth/login" class="mt-navbar__auth-link mega-btn-md mega-btn-outline-md text-white w-100">
                <span class="mt-navbar__auth-link-text">SIGN IN</span>
            </a>

            <a href="/auth/register" class="mega-btn-md mega-btn-default-md">
                REGISTER
            </a>
        </div>
    <?php endif ?>
</div>