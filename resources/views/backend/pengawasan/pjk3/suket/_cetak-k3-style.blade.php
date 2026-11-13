<style>
    @page {
        size: A4 portrait;
        margin: 14mm 16mm 32mm 16mm;
    }
    * { box-sizing: border-box; }
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 12pt;
        color: #000;
        margin: 0;
        padding: 0 0 8mm;
    }
    .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
    .kop h3, .kop h4, .kop p { margin: 0; }
    h2 { text-align: center; text-decoration: underline; font-size: 14pt; margin: 18px 0 4px; }
    h4 { margin: 16px 0 8px; }
    .nomor { text-align: center; margin-bottom: 18px; }
    p { text-align: justify; line-height: 1.5; }
    table.data, table.grid { width: 100%; border-collapse: collapse; }
    table.data td { vertical-align: top; padding: 3px 0; }
    table.grid th, table.grid td { border: 1px solid #000; padding: 4px 6px; font-size: 11pt; }
    table.grid th { background: #eee; }
    .hasil { font-size: 16pt; font-weight: bold; text-align: center; margin: 18px 0; letter-spacing: 1px; }
    .ttd { width: 48%; float: right; text-align: center; margin-top: 22px; page-break-inside: avoid; }
    .ttd .space { min-height: 22mm; }
    .ttd-bsre-wrap { width: 100%; border-collapse: collapse; margin: 2mm 0 1mm; }
    .ttd-bsre-wrap td { text-align: center; vertical-align: middle; }
    .ttd-bsre { width: 32mm; height: auto; }
    .watermark {
        position: fixed;
        top: 38%;
        left: 12%;
        font-size: 72pt;
        color: rgba(200, 0, 0, .18);
        transform: rotate(-24deg);
        z-index: 0;
        pointer-events: none;
        font-weight: bold;
    }
    .bsre-footer-wrap {
        position: fixed;
        left: 0;
        right: 0;
        bottom: -24mm;
        width: 100%;
    }
    .bsre-footer {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8pt;
        line-height: 1.35;
        color: #222;
    }
    .bsre-footer .bsre-logo { width: 38mm; vertical-align: middle; padding-right: 6px; }
    .bsre-footer .bsre-logo img { width: 36mm; height: auto; }
    .bsre-footer .bsre-text { vertical-align: middle; text-align: left; }
    @media print {
        .no-print { display: none; }
        .watermark { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bsre-footer { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
