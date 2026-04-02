<?php
/**
 * PdfHelper – DomPDF v2.0 wrapper with school branding
 */
class PdfHelper
{
    /**
     * Render a view file and return HTML string.
     */
    public static function renderView(string $template, array $data = []): string
    {
        extract($data);
        ob_start();
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $template) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        }
        return ob_get_clean();
    }

    /**
     * Generate PDF from HTML and stream to browser.
     */
    public static function generate(string $html, string $filename = 'document.pdf'): void
    {
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ['Attachment' => true]);
        exit;
    }

    /**
     * Get school header HTML for PDFs.
     */
    public static function schoolHeader(array $school): string
    {
        $logo = '';
        if (!empty($school['logo_url'])) {
            $logo = '<img src="' . htmlspecialchars($school['logo_url']) . '" style="height:60px;margin-right:15px;">';
        }
        return '
        <div style="text-align:center;border-bottom:2px solid #333;padding-bottom:15px;margin-bottom:20px;">
            ' . $logo . '
            <h1 style="margin:0;font-size:24px;color:#1a1a2e;">' . htmlspecialchars($school['name'] ?? '') . '</h1>
            <p style="margin:4px 0 0;font-size:12px;color:#555;">' . htmlspecialchars($school['address'] ?? '') . '</p>
            <p style="margin:2px 0 0;font-size:11px;color:#777;">' . htmlspecialchars($school['phone'] ?? '') . '</p>
        </div>';
    }

    /**
     * Get footer HTML.
     */
    public static function footer(): string
    {
        return '
        <div style="border-top:1px solid #ccc;padding-top:10px;margin-top:30px;text-align:center;font-size:10px;color:#888;">
            <p>Generated on ' . date('d M Y, h:i A') . '</p>
            <p>This is a computer generated document.</p>
        </div>';
    }
}
