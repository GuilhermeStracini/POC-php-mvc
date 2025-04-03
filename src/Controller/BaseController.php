<?php

namespace GuiBranco\PocMvc\Src\Controller;

use GuiBranco\PocMvc\Src\Core\BundleManager;
use Exception;

class BaseController
{
    protected array $viewsPath;

    protected string $layoutPath;

    protected array $data = [];

    protected $sections = [];
    protected $content = '';

    public function __construct(string $viewsPath, string|null $layoutPath = null)
    {
        $viewsPath = rtrim($viewsPath, '/');

        $controllerName = str_replace("Controller", "", $this->getControllerName());
        $this->viewsPath = [
            "{$viewsPath}/{$controllerName}/",
            "{$viewsPath}/Shared/",
            "{$viewsPath}/"
        ];
        $this->layoutPath = "{$viewsPath}/Shared/";
        if ($layoutPath) {
            $this->layoutPath = $layoutPath;
        }
    }

    /**
     * Get the controller name.
     *
     * @return string
     */
    private function getControllerName(): string
    {
        return basename(str_replace('\\', '/', get_called_class()));
    }

    /**
     * Render a view with optional data.
     *
     * @param string $template The view template file name (without extension).
     * @param array $data Data to be passed to the view.
     * @return string
     * @throws Exception
     */
    protected function view(string $viewName, array $data = [], ?string $layout = 'layout'): void
    {
        $searchedPaths = [];
        $layout = $layout ? "{$this->layoutPath}{$layout}.php" : null;
        foreach ($this->viewsPath as $path) {
            $fullPath = $path . $viewName . '.php';
            if (file_exists(filename: $fullPath)) {
                $this->render($fullPath, $data, $layout);
                return;
            }

            $searchedPaths[] = $fullPath;
        }

        throw new Exception("View not found: {$viewName} (searched in: " . implode(', ', $searchedPaths) . ")");
    }

    /**
     * Render a view template.
     *
     * @param string $template
     * @param array $data
     * @param string|null $layout
     * @return void
     */
    protected function render(string $template, array $data = [], ?string $layout = null): void
    {
        $data = array_merge($this->data, $data);

        extract($data);

        ob_start();
        include $template;
        $content = ob_get_clean();

        if ($layout) {
            $this->data['content'] = $content;
            include $layout;
        } else {
            echo $content;
        }
    }

    /**
     * Add a variable to be globally available in all templates.
     *
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Render a partial view with optional data.
     *
     * @param string $viewName The view template file name (without extension).
     * @param array $data Data to be passed to the view.
     * @return void
     */
    protected function partialView(string $viewName, array $data = []): void
    {
        $this->view($viewName, $data, null);
    }

    /**
     * Redirect to a given URL.
     *
     * @param string $url
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Return a JSON response.
     *
     * @param mixed $data
     * @return void
     */
    protected function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function startSection($name)
    {
        ob_start();
        $this->sections[$name] = '';
    }

    protected function endSection()
    {
        $lastSectionName = array_key_last($this->sections);
        if ($lastSectionName !== null) {
            $this->sections[$lastSectionName] = ob_get_clean();
        }
    }

    protected function renderSection($name)
    {
        return $this->sections[$name] ?? '';
    }

    protected function renderBundles($bundleName)
    {
        $assets = BundleManager::getBundle($bundleName);

        foreach ($assets as $asset) {
            if (preg_match('/\.css$/', $asset)) {
                echo "<link rel='stylesheet' href='$asset' />\n";
            } elseif (preg_match('/\.js$/', $asset)) {
                echo "<script src='$asset'></script>\n";
            } elseif (preg_match('/\.(woff|woff2|ttf|eot)$/', $asset)) {
                echo "<link rel='preload' href='$asset' as='font' type='font/woff2' crossorigin='anonymous' />\n";
            }
        }
    }
}
