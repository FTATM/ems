<footer style="
    width: 100%;
    padding: 0.4rem 0;
    text-align: center;
    font-size: 0.75rem;
    color: var(--footer-text, #888);
    background: var(--footer-bg, #fff);
    border-top: 1px solid var(--footer-border, #e5e7eb);
    transition: background 0.3s, color 0.3s, border-color 0.3s;
">
    © 2024 Energy Management Solutions
</footer>

<style>
body {
    font-family: var(--ems-font, 'Inter', 'Noto Sans Thai', 'Sarabun', sans-serif);
}

:root {
    --footer-bg: var(--ems-surface, #ffffff);
    --footer-text: var(--ems-muted, #586472);
    --footer-border: var(--ems-border, #d9e0e7);
}

html.dark {
    --footer-bg: var(--ems-surface, #141a21);
    --footer-text: var(--ems-muted, #8b98a5);
    --footer-border: var(--ems-border, #2a3542);
}
</style>