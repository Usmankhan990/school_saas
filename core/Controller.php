<?php
/**
 * Base Controller – View rendering, auth helpers, audit logging
 */
class Controller
{
    /**
     * Render a view with layout.
     */
    protected function view(string $template, array $data = [], string $layout = 'app'): void
    {
        // Make data available as variables in the view
        extract($data);

        // Clear old input after rendering
        Session::clearOldInput();

        // Capture the content view
        ob_start();
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$template}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Render inside layout
        $layoutFile = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    /**
     * Render a view without layout (e.g., login/register).
     */
    protected function viewOnly(string $template, array $data = []): void
    {
        extract($data);
        Session::clearOldInput();
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $template) . '.php';
        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$template}");
        }
        require $viewFile;
    }

    /**
     * Return JSON response.
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Enforce role requirement.
     */
    protected function authorize(array $roles): void
    {
        Middleware::roleCheck($roles);
    }

    /**
     * Get POST data with sanitization.
     */
    protected function input(string $key, string $default = ''): string
    {
        return sanitize($_POST[$key] ?? $default);
    }

    /**
     * Get all POST data.
     */
    protected function allInput(): array
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            if ($key === CSRF_TOKEN_NAME) continue;
            $data[$key] = is_string($value) ? sanitize($value) : $value;
        }
        return $data;
    }

    /**
     * Save old input for form repopulation after validation error.
     */
    protected function saveInput(): void
    {
        Session::setOldInput($_POST);
    }

    /**
     * Log an audit event.
     */
    protected function audit(string $action, string $entity, ?int $entityId = null, array $meta = []): void
    {
        try {
            Database::insert('audit_logs', [
                'school_id'  => current_school_id(),
                'user_id'    => current_user_id(),
                'action'     => $action,
                'entity'     => $entity,
                'entity_id'  => $entityId,
                'meta_json'  => json_encode($meta),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            // Silently fail for audit logs
        }
    }

    /**
     * Handle file upload.
     * Returns the relative path or null on failure.
     */
    protected function uploadFile(string $inputName, string $destDir, array $allowedTypes = null): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$inputName];
        $allowedTypes = $allowedTypes ?? ALLOWED_IMAGE_TYPES;

        // Validate type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            flash('error', 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes));
            return null;
        }

        // Validate size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            flash('error', 'File too large. Max size: ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB');
            return null;
        }

        // Create directory if needed
        $fullDir = UPLOAD_PATH . '/' . $destDir;
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0775, true);
        }

        // Generate filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $fullDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return 'uploads/' . $destDir . '/' . $filename;
        }

        return null;
    }
}
