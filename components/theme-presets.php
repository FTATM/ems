<?php
/**
 * Shared list of accent-color presets for the whole app.
 * Used by components/header.php (to inject the CSS override) and
 * pages/mndidb.php (to render the picker).
 */
function emsThemePresets()
{
    return [
        'green'  => ['label_th' => 'เขียว (ค่าเริ่มต้น)', 'label_en' => 'Green (Default)', 'accent' => '#8BAE66', 'accent_dark' => '#6e9248'],
        'blue'   => ['label_th' => 'ฟ้า',                 'label_en' => 'Blue',            'accent' => '#4A90D9', 'accent_dark' => '#3A73AD'],
        'teal'   => ['label_th' => 'ฟ้าอมเขียว',          'label_en' => 'Teal',            'accent' => '#3E97AE', 'accent_dark' => '#2C7385'],
        'purple' => ['label_th' => 'ม่วง',                 'label_en' => 'Purple',          'accent' => '#9B6FD4', 'accent_dark' => '#7A54AD'],
        'orange' => ['label_th' => 'ส้ม',                  'label_en' => 'Orange',          'accent' => '#E08A3C', 'accent_dark' => '#B96E28'],
        'rose'   => ['label_th' => 'ชมพูกุหลาบ',           'label_en' => 'Rose',            'accent' => '#D9527A', 'accent_dark' => '#B03D60'],
    ];
}

function emsHexToRgb($hex)
{
    $hex = ltrim($hex, '#');
    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
    ];
}

/** ผสมสี accent เข้ากับสีพื้น (ขาว/ดำ) ในสัดส่วนน้อยๆ เพื่อได้สีพื้นหลัง/เส้นขอบโทนอ่อนที่เปลี่ยนตาม preset */
function emsBlend($r, $g, $b, $withR, $withG, $withB, $ratio)
{
    $nr = round($withR * (1 - $ratio) + $r * $ratio);
    $ng = round($withG * (1 - $ratio) + $g * $ratio);
    $nb = round($withB * (1 - $ratio) + $b * $ratio);
    return sprintf('#%02x%02x%02x', $nr, $ng, $nb);
}

/** ดึง preset ที่เลือกไว้ปัจจุบันจาก DB (คืนค่า 'green' ถ้าเชื่อมต่อไม่ได้ หรือยังไม่เคยตั้งค่า) */
function emsGetCurrentPreset($conn)
{
    $key = 'green';
    try {
        $res = $conn->query("SELECT setting_value FROM app_settings WHERE setting_key = 'theme_preset' LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $presets = emsThemePresets();
            if (isset($presets[$row['setting_value']])) {
                $key = $row['setting_value'];
            }
        }
    } catch (\Throwable $e) {
        // เชื่อมต่อไม่ได้ -> ใช้ค่าเริ่มต้น
    }
    return $key;
}

/** สร้าง <style> override ตัวแปรสี CSS ให้ตรงกับ preset ที่เลือก (ใช้ !important เพื่อชนะไฟล์ CSS เดิมของแต่ละหน้า) */
function emsRenderThemePresetStyle($presetKey)
{
    $presets = emsThemePresets();
    $p = $presets[$presetKey] ?? $presets['green'];
    [$r, $g, $b] = emsHexToRgb($p['accent']);
    $accent = $p['accent'];
    $accentDark = $p['accent_dark'];

    // พื้นหลัง/เส้นขอบโทนอ่อน (light mode: ผสมกับขาว, dark mode: ผสมกับดำ) ให้เปลี่ยนตาม accent ด้วย
    // ไม่แตะ --bg-card (การ์ดต้องขาว/ดำสนิทเพื่อให้อ่านง่าย) และไม่แตะสีสถานะ (--ok/--warn/--busy)
    $bgPageLight = emsBlend($r, $g, $b, 255, 255, 255, 0.045);
    $bgSelectLight = emsBlend($r, $g, $b, 255, 255, 255, 0.07);
    $borderLight = emsBlend($r, $g, $b, 255, 255, 255, 0.30);

    $bgPageDark = emsBlend($r, $g, $b, 9, 9, 11, 0.10);
    $bgSelectDark = emsBlend($r, $g, $b, 9, 9, 11, 0.16);
    $borderDark = emsBlend($r, $g, $b, 9, 9, 11, 0.35);

    // ── ตัวหนังสือ (--text-primary/--text-heading/--text-secondary/--text-body/--text-muted/--text-label)
    //    ในหลายไฟล์ผสมสีเขียวไว้ตั้งแต่แรก (ไม่ใช่สีเทากลางๆ) เลยต้องปรับตาม accent ด้วย
    //    ไม่แตะ --text-card/--text-th (เป็นสีเทากลางๆ อยู่แล้ว ไว้ให้อ่านง่ายเสมอ)
    $textDarkest = emsBlend($r, $g, $b, 0, 0, 0, 0.18);
    $textMedium = emsBlend($r, $g, $b, 0, 0, 0, 0.42);
    $textMuted = emsBlend($r, $g, $b, 0, 0, 0, 0.58);

    $textLightest = emsBlend($r, $g, $b, 255, 255, 255, 0.88);
    $textMediumDark = emsBlend($r, $g, $b, 255, 255, 255, 0.58);
    $textMutedDark = emsBlend($r, $g, $b, 255, 255, 255, 0.75);

    // ── sidemenu.css ใช้ตัวแปรชุดแยกของตัวเอง (--sm-*) ไม่ผูกกับ --primary/--green เลย ต้อง override เพิ่ม ──
    $smTextLight = emsBlend($r, $g, $b, 0, 0, 0, 0.30);
    $smMutedLight = emsBlend($r, $g, $b, 0, 0, 0, 0.60);
    $smLabelLight = emsBlend($r, $g, $b, 255, 255, 255, 0.35);
    $smBgHoverLight = emsBlend($r, $g, $b, 255, 255, 255, 0.09);
    $smBorderLight = emsBlend($r, $g, $b, 255, 255, 255, 0.22);
    $smFooterLight = emsBlend($r, $g, $b, 255, 255, 255, 0.05);
    $smLangBgLight = emsBlend($r, $g, $b, 255, 255, 255, 0.10);
    $smLangTextLight = emsBlend($r, $g, $b, 0, 0, 0, 0.45);

    $smTextDark = emsBlend($r, $g, $b, 255, 255, 255, 0.85);
    $smMutedDark = emsBlend($r, $g, $b, 255, 255, 255, 0.55);
    $smLabelDark = emsBlend($r, $g, $b, 0, 0, 0, 0.55);
    $smAccentDkDark = emsBlend($r, $g, $b, 255, 255, 255, 0.35);
    $smBgDark = emsBlend($r, $g, $b, 9, 9, 11, 0.07);
    $smBgHoverDark = emsBlend($r, $g, $b, 9, 9, 11, 0.14);
    $smFooterDark = emsBlend($r, $g, $b, 9, 9, 11, 0.04);
    $smLangTextDark = emsBlend($r, $g, $b, 255, 255, 255, 0.65);
    ?>
    <style id="ems-theme-preset-override">
    :root {
        --primary: <?= $accent ?> !important;
        --primary-dark: <?= $accentDark ?> !important;
        --primary-light: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.12) !important;
        --primary-border: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.40) !important;
        --border-focus: <?= $accent ?> !important;
        --green: <?= $accent ?> !important;
        --green-dark: <?= $accentDark ?> !important;
        --green-icon: <?= $accent ?> !important;
        --green-light: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.10) !important;
        --green-glow: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.18) !important;
        --green-soft: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.09) !important;

        --bg-page: <?= $bgPageLight ?> !important;
        --bg-select: <?= $bgSelectLight ?> !important;
        --bg-filter: #ffffff !important;
        --bg-info: #ffffff !important;
        --border: <?= $borderLight ?> !important;

        --text-primary: <?= $textDarkest ?> !important;
        --text-heading: <?= $textDarkest ?> !important;
        --text-secondary: <?= $textMedium ?> !important;
        --text-body: <?= $textMedium ?> !important;
        --text-muted: <?= $textMuted ?> !important;
        --text-label: <?= $textMuted ?> !important;
        --border-card: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.20) !important;
        --footer-border: <?= $borderLight ?> !important;

        --sm-accent: <?= $accent ?> !important;
        --sm-accent-dk: <?= $accentDark ?> !important;
        --sm-accent-glow: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.22) !important;
        --sm-accent-soft: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.13) !important;
        --sm-bg-hover: <?= $smBgHoverLight ?> !important;
        --sm-border: <?= $smBorderLight ?> !important;
        --sm-text: <?= $smTextLight ?> !important;
        --sm-muted: <?= $smMutedLight ?> !important;
        --sm-label: <?= $smLabelLight ?> !important;
        --sm-footer: <?= $smFooterLight ?> !important;
        --sm-lang-bg: <?= $smLangBgLight ?> !important;
        --sm-lang-text: <?= $smLangTextLight ?> !important;
        --sm-shadow: 4px 0 40px rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.15), 2px 0 16px rgba(0,0,0,0.08) !important;
    }
    html.dark {
        --primary: <?= $accent ?> !important;
        --primary-dark: <?= $accentDark ?> !important;
        --primary-light: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.16) !important;
        --primary-border: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.35) !important;
        --border-focus: <?= $accent ?> !important;
        --green: <?= $accent ?> !important;
        --green-dark: <?= $accentDark ?> !important;
        --green-icon: <?= $accent ?> !important;
        --green-light: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.14) !important;
        --green-glow: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.22) !important;
        --green-soft: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.09) !important;

        --bg-page: <?= $bgPageDark ?> !important;
        --bg-select: <?= $bgSelectDark ?> !important;
        --bg-filter: #09090b !important;
        --bg-info: #09090b !important;
        --border: <?= $borderDark ?> !important;

        --text-primary: <?= $textLightest ?> !important;
        --text-heading: <?= $textLightest ?> !important;
        --text-secondary: <?= $textMediumDark ?> !important;
        --text-body: <?= $textMediumDark ?> !important;
        --text-muted: <?= $textMutedDark ?> !important;
        --text-label: <?= $textMutedDark ?> !important;
        --border-card: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.15) !important;
        --footer-border: <?= $borderDark ?> !important;

        --sm-accent: <?= $accent ?> !important;
        --sm-accent-dk: <?= $smAccentDkDark ?> !important;
        --sm-accent-glow: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.28) !important;
        --sm-accent-soft: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.16) !important;
        --sm-bg: <?= $smBgDark ?> !important;
        --sm-bg-hover: <?= $smBgHoverDark ?> !important;
        --sm-border: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.14) !important;
        --sm-text: <?= $smTextDark ?> !important;
        --sm-muted: <?= $smMutedDark ?> !important;
        --sm-label: <?= $smLabelDark ?> !important;
        --sm-footer: <?= $smFooterDark ?> !important;
        --sm-lang-bg: rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.09) !important;
        --sm-lang-text: <?= $smLangTextDark ?> !important;
        --sm-shadow: 6px 0 48px rgba(0,0,0,0.6), 0 0 0 1px rgba(<?= $r ?>, <?= $g ?>, <?= $b ?>, 0.08) !important;
    }
    .bg-green, .bg-green-reverse { background: linear-gradient(0deg, <?= $accentDark ?>, <?= $accent ?>) !important; }
    </style>
    <?php
}
