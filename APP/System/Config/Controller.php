<?php
namespace System\config;

use Exception;
//require_once APPPATH. '/Helpers/format_helper.php';
/**
 * Base Controller
 * Loads the models and views
*/
use Config\tables;
class Controller
{ 
      /**
     * Load model
     *
     * @param string $model
     * @return object
     * @throws Exception
     */

    public function model(string $model)
    {
        // Replace underscores with directory separators to form the correct path
        $classPath = str_replace('_', DIRECTORY_SEPARATOR, $model);
        // Construct the full path to the model file
        $modelFile = APPPATH . 'Models' . DIRECTORY_SEPARATOR . $classPath . '.php';
   // echo $modelFile; exit;
        // Check if the model file exists
        if (file_exists($modelFile)) {
            // Require the model file
            require_once $modelFile;
    
            // Check if the class exists after requiring the file
            if (!class_exists($model)) {
                throw new Exception("Class '$model' does not exist in the file '$modelFile'");
            }
        } /* else {
            throw new Exception("Model file '$modelFile' does not exist");
        } */
        $model = strtolower($model);    
        // Instatiate model
        return new $model();
    }
    
    /**
     * Load view
     *
     * @param string $view
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function view(string $view, array $data = []): string
    {
        $viewFile = APPPATH . 'Views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception('View does not exist');
        }

        $content = file_get_contents($viewFile);
        $content = $this->parseTemplate($content);

        ob_start();
        extract($data);
        eval('?>' . $content);
        $view=ob_get_clean();
        return $view;
        extract($data);
    }

    /**
     * Custom parsing function
     *
     * @param string $content
     * @return string
     */
    private function parseTemplate(string $content): string
    {
        return preg_replace('/\{\{([^}]+)\}\}/', '<?php echo $1; ?>', $content);
    }
}
