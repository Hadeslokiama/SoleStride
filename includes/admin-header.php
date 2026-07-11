<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth-check.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en" data-theme="unbound-dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unbound Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= app_url('assets/favicon.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root,
        [data-theme="unbound-dark"] {
            color-scheme: dark;
            --bg: #09090B;
            --fg: #FAFAFA;
            --muted: #27272A;
            --muted-fg: #A1A1AA;
            --accent: #DFE104;
            --accent-fg: #000000;
            --border: #3F3F46;
        }

        [data-theme="unbound-light"] {
            color-scheme: light;
            --bg: #FAFAFA;
            --fg: #09090B;
            --muted: #E4E4E7;
            --muted-fg: #52525B;
            --accent: #F5FF00;
            --accent-fg: #000000;
            --border: #A1A1AA;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: 'var(--bg)',
                        fg: 'var(--fg)',
                        muted: 'var(--muted)',
                        'muted-fg': 'var(--muted-fg)',
                        accent: 'var(--accent)',
                        'accent-fg': 'var(--accent-fg)',
                        border: 'var(--border)'
                    },
                    fontFamily: {
                        display: ['Space Grotesk', 'sans-serif'],
                        body: ['Inter', 'sans-serif']
                    }
                }
            },
            daisyui: {
                themes: [
                    {
                        'unbound-dark': {
                            'base-100': '#09090B',
                            'base-200': '#27272A',
                            'base-300': '#3F3F46',
                            'base-content': '#FAFAFA',
                            'primary': '#DFE104',
                            'primary-content': '#000000',
                            'secondary': '#FAFAFA',
                            'secondary-content': '#09090B',
                            'accent': '#DFE104',
                            'accent-content': '#000000',
                            'neutral': '#27272A',
                            'neutral-content': '#FAFAFA',
                            '--rounded-box': '0',
                            '--rounded-btn': '0',
                            '--rounded-badge': '0'
                        }
                    },
                    {
                        'unbound-light': {
                            'base-100': '#FAFAFA',
                            'base-200': '#E4E4E7',
                            'base-300': '#A1A1AA',
                            'base-content': '#09090B',
                            'primary': '#F5FF00',
                            'primary-content': '#000000',
                            'secondary': '#09090B',
                            'secondary-content': '#FAFAFA',
                            'accent': '#F5FF00',
                            'accent-content': '#000000',
                            'neutral': '#E4E4E7',
                            'neutral-content': '#09090B',
                            '--rounded-box': '0',
                            '--rounded-btn': '0',
                            '--rounded-badge': '0'
                        }
                    }
                ]
            }
        };
    </script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= app_url('assets/css/style.css?v=' . filemtime(__DIR__ . '/../assets/css/style.css')) ?>">
    <script>
        (function () {
            const savedTheme = localStorage.getItem('unbound-theme');
            if (savedTheme === 'unbound-light' || savedTheme === 'unbound-dark') {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('[data-theme-toggle]');
            const root = document.documentElement;

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const nextTheme = root.getAttribute('data-theme') === 'unbound-light' ? 'unbound-dark' : 'unbound-light';
                    root.setAttribute('data-theme', nextTheme);
                    localStorage.setItem('unbound-theme', nextTheme);
                });
            });
        });
    </script>
</head>
<body class="admin-body">
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-brand-row">
            <h2 class="admin-logo">Unbound Admin</h2>
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Toggle light and dark theme">
                <span class="theme-icon theme-icon-sun" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                    </svg>
                </span>
                <span class="theme-icon theme-icon-moon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" focusable="false">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </span>
            </button>
        </div>
        <ul class="admin-nav">
            <li><a href="<?= app_url('admin/dashboard.php') ?>">Dashboard</a></li>
            <li><a href="<?= app_url('admin/inventory.php') ?>">Inventory</a></li>
            <li><a href="<?= app_url('admin/manage-users.php') ?>">Manage Users</a></li>
            <li><a href="<?= app_url('admin/audit-log.php') ?>">Audit Log</a></li>
            <li><a href="<?= app_url('auth/logout.php') ?>">Logout</a></li>
        </ul>
    </aside>
    <main id="main-content" class="admin-main">
