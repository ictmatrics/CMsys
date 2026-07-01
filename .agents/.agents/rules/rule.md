---
trigger: always_on
---

---
trigger: always_on
---

# CMSYS Workspace Rules & Coding References

* * *

## 🤖 Antigravity's Senior PHP Developer Mandate

> \[!CAUTION\] **As Antigravity, acting as a Senior PHP Developer, you are bound by an absolute directive to strictly follow every rule, coding standard, and security guideline in this document without exception.**
> 
> 1.  **Zero Deviation:** You must perfectly adhere to the syntax constraints (e.g., exclusively brace-based control structures `if () { ... }`, never alternative syntax).
> 2.  **Enforce Initialization:** You must ensure `__construct()` with `parent::__construct()` is present in every Model.
> 3.  **Impeccable Security:** Never embed variables directly in SQL strings. Always use framework-provided parameter binding, and always escape output using framework helpers.
> 4.  **No Inventions:** Use only the established ICTM Framework 4.0.1 patterns, directory structures, and templating (`{{ $var }}`). Do not introduce generic PHP solutions if a framework helper or structure exists.
> 
> **Your generated code must always be production-ready, PSR-12 compliant, typed accurately (PHP 8.2+), and seamlessly integrated with this specific MVC architecture.**

* * *
## 🚫Project Directory
> \[!IMPORTANT\]  **Project Directory:** e:\Webserver\cmsys.wis
## 🚫 File Modification Restrictions

> \[!IMPORTANT\] Do **NOT** update or edit original files of the ICTM Framework under this project **EXCEPT** for the following files:
> 
> *   `APP/Config/Route.php` — \[Route.php\]
> *   `public_html/css/style.css` — \[style.css\]
> *   `public_html/js/script.js` — \[script.js\]
> *   `APP/Views/` — \[Views Directory\]
> *   `APP/Models/` — \[Model Directory\]
> *   `APP/Controllers/` — \[Controller Directory\]
> *   `APP/Libraries/` — \[Libraries Directory\] - for new Library create new one
> *   `APP/Helpers/` — \[Helpers Directory\] - for new helper create new one

> All other files (core framework code, models, system directories, default helpers, etc.) are read-only and must remain unmodified.

* * *
## 🔄 Coding Patterns & Conventions (ICTM Framework 4.0.\*)

Follow these specific replacement rules when writing template/view logic and scripts:

|Pattern / Context|Replace (Do NOT Use)|For (USE instead)|
|:---|:---|:---|
|**Inside View files**|`<?= $var ?>` or `<?php echo $var; ?>`|`{{ $var }}`|
|**Inside View files**|`<?php include APPPATH . 'Views/layouts/footer.php'; ?>`|`{{ $this->view('layouts/footer') }}`|
|**Control Structures (All)**|`if ($cond): ... endif;`|`if ($cond) { ... }`
|**Loops (All)**|`foreach ($items as $item): ... endforeach;`|`foreach ($items as $item) { ... }`|
|**HTML Anchors (All)**|`<a href="url" class="cls">Name</a>`|`<?php redirectto('url', 'Name', 'cls'); ?>`<br> (uses helper function)|

## Strict PHP Syntax:

All generated PHP code must exclusively use standard brace-based control structures |\* (e.g., if (...) { ... }, foreach (...) { ... }). | | The use of alternative template syntax (e.g., if(): ... endif;) is strictly forbidden.|

## Model Initialization:

 Add 
 `public function __construct() { parent::__construct(); }` at the top of every model class to ensure the $db property is properly initialized. 

* * *

## 📂 1. Directory Structure & Namespace Rules

The application follows a strict directory structure. Classes must be autoloaded using the custom PSR-4 autoloader.

Directory Purpose Namespace Prefix / Path Mapping

`APP/Config/`

Configuration files (Routes, Database, Autoload)

`Config\`

`APP/Controllers/`

Controller classes handling request logic

`App\Controllers\`

`APP/Models/`

Model classes handling DB interactions

`App\Models\`

`APP/Views/`

View templates

Managed via `Controller::view()`

`APP/Helpers/`

Procedural helper scripts

Auto-loaded / included via `Autoload`

`APP/Libraries/`

Third-party or custom classes

`App\Libraries\` or auto-loaded

`APP/System/`

Core framework engines

`System\`

`public_html/`

Public web root (Assets, CSS, JS, index.php)

Accessible via web browser

### Namespace Formatting

*   Files under `APP/Controllers/` must start with `namespace App\Controllers;`
*   Files under `APP/Models/` must start with `namespace App\Models;`
*   Core system files must start with `namespace System\Config;` (or sub-namespace).

* * *

##  2. Coding Standards & Conventions

1.  **Strict Types**: Always include `declare(strict_types=1);` at the top of every PHP file.
2.  **PHP Version**: Optimized for PHP 8.2+. Use modern features (typed properties, match expressions, readonly properties where applicable).
3.  **Coding Style**: Follow PSR-12 coding standard.
    *   Use 4 spaces for indentation.
    *   Class names must be `PascalCase`.
    *   Method names must be `camelCase`.
    *   Table names and column names must be `snake_case`.

* * *

##  3. Routing & Request Handling

All routes are registered in `APP/Config/Route.php` using `$router`.

### Route Definition Syntax

```php
$router->get('/path', 'ControllerName@actionName');
$router->post('/path', 'ControllerName@actionName');
$router->any('/path', 'ControllerName@actionName');
```

### Dynamic Routes

Wildcard variables can be captured in curly braces. They are passed as arguments to the controller action in order:

```php
// Route
$router->get('/product/{slug}', 'ProductController@detail');

// Controller Method
public function detail(string $slug) { ... }
```

### Route Resolution Mapping

*   `ControllerName` is mapped to namespace `App\Controllers\<ControllerName>`.
*   Underscores (`_`) in the controller string are translated to subdirectories/namespaces. For example: `about_AboutController` maps to `App\Controllers\about\AboutController`.

* * *

##  4. Controller Rules

All controllers must extend `System\Config\Controller` and reside in `App\Controllers`.

### Typical Controller Template

```php
<?php
declare(strict_types=1);

namespace App\Controllers;

use System\Config\Controller;
use App\Models\ProductModel;

class ProductController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index()
    {   $Allproduct = $this->productModel->getAllProducts()
        $data=['title' => 'Products',
        'products' => $Allproduct  ];

        echo $this->view('products/list', $data);
    }
}
```

### Key Controller Operations

*   **Load View**: `echo $this->view('view_name', $data);` (see Templating below).
*   **Redirects**: Use the `redirect('path')` helper instead of raw headers where possible.
*   **Form Data**: Retrieve inputs via `$_POST['name']` and sanitize using `validate_data()`.

* * *

##  5. Model & Database Rules

All models must extend `System\Config\Model` and reside in `App\Models`.

### Model Template

```php
<?php
declare(strict_types=1);

namespace App\Models;

use System\Config\Model;

class ProductModel extends Model
{
  public function __construct(){
      parent::__construct();
  }
    private string $table = 'products';

    public function getFeaturedProducts(int $limit = 4): array
    {
        return $this->find_all($this->table, '', [
            ['is_featured', '=', 1]
        ]);
    }
}
```

### Core Database Methods (Inherited from `System\Config\Model`)

*   **`find_all(string $table, string $query = '', array $params = [])`**: Fetches all records matching conditions.
    *   Parameter array conditions must follow the format: `[ ['column', 'operator', 'value'], ... ]`.
    *   Alternatively, pass a raw SQL query with parameters.
*   **`find_single(string $table, int $id = null, string $query = '', array $params = [])`**: Fetches a single record as an object.
*   **`num_rows(string $table, string $query = '', array $params = [])`**: Counts matching rows.
*   **`insert(string $table, array $data)`**: Inserts array data into the table. Returns insert ID (int) or `false`.
*   **`update(string $table, array $data, int $id)`**: Updates the record with matching ID. Returns `bool`.
*   **`delete(string $table, int $id)`**: Deletes the record with matching ID. Returns `bool`.
*   **`newid()`**: Returns the last inserted ID.

* * *

##  6. Views & Templating Rules

Views are PHP files located in `APP/Views/` and loaded using `$this->view('view_name', $data)`.

### Templating Syntax

The template parser replaces `{{ $expression }}` with standard PHP echo statements.

*   **Syntax**: `{{ $variable }}` or `{{ pathto('home') }}`
*   **Compiled to**: `<?php echo $variable; ?>` or `<?php echo pathto('home'); ?>`

### View Example

```html
<h1>{{ $title }}</h1>
<div class="row">
    <?php foreach ($products as $product){ ?>
        <div class="col-md-3">
            <h3>{{ htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') }} </h3>
            <p>{{ format_price($product->price) }}</p>
        </div>
    <?php }?>
</div>
```

* * *

##  7. Core Helpers Reference

The framework autoloads utility functions from `APP/Helpers/`. Check these files for global functions:

###  URL & Navigation ([url\_helper.php])

*   **`redirect(string $page): void`**: Redirects to `BASE_URL/page` and exits.
*   **`redirectto(string $page, string $pagename = '', string $class = ''): void`**: Outputs a formatted link anchor tag `<a href="..." class="...">Name</a>`. **Always use this instead of raw `<a>` tags for internal pages.**
*   **`pathto(string $page): string`**: Returns the full URL path to the given page.
*   **`linkto(string $page): void`**: Echoes the full URL path to the given page.
*   **`image(string $src, string $alt = '', $width = '', $height = '', string $class = '', string $style = ''): void`**: Safely outputs a responsive `<img>` element.
*   **`alert(string $message): void`**: Renders a client-side JavaScript alert.
*   **`alertto(string $message, string $path): void`**: Shows an alert, then redirects using JS.
*   **`alerttoback(string $message): void`**: Shows an alert, then goes back in history.
*   **`confirmto(string $message, string $path, string $returnpath): void`**: Confirmation prompt redirecting based on decision.
*   **`getUserIP(): string`**: Retrieves the client's IP safely, honoring forwarders.

###  Flash Messages (`flash_helper.php`)

*   **`flash(string $name, string $message = '', string $class = 'alert alert-success', string $position = 'top-right'): void`**: Stores or renders dynamic session-based alert alerts.
    *   To set: `flash('success_msg', 'Profile updated successfully!');`
    *   To render (usually in layout/view): `flash('success_msg');`

###  Formatting & Validation ([format\_helper.php])

*   **`validate_data(string $value): string`**: Sanitizes string inputs to prevent XSS (applies `trim`, `stripslashes`, `strip_tags`, `htmlspecialchars`).
*   **`format_price(float|int|string $price): string`**: Dynamically formats pricing using currency preferences retrieved from settings.
*   **`now(): string`**: Returns current timestamp `Y-m-d H:i:s`.
*   **`today(): string`**: Returns current date `Y-m-d`.
*   **`header_nocache(): void`**: Disables browser caching headers.

* * *

##  8. Security & Integrity Guidelines

1.  **SQL Injection Prevention**:
    *   Never embed variables directly in raw query strings. Use placeholders and route parameters through `find_all`, `find_single`, etc.
2.  **XSS Protection**:
    *   Escape dynamic output using `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`.
    *   Sanitize all raw post/get inputs using `validate_data()`.
3.  **Session Management**:
    *   Sessions are automatically initialized by the flash helper. Ensure they remain active and secured.