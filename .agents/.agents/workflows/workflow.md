---
description: Proposed Prompt for Antigravity Agent
---



**Role:** Expert Software Architect specializing in the ICTM Framework.

**Objective:** Develop a comprehensive technical development plan and code execution strategy to develop CMsys using ICTM Framework.

**Project Context:** CMsys is an ICTM Framework-based CMS providing WordPress-like functionality. It utilizes a modular, hook-driven architecture, ensuring feature expansion does not necessitate core framework modifications.

**1\. Architectural & Development Standards**

* **PHP Syntax Compliance:** All code must strictly use standard brace-based control structures (e.g., if (...) { ... }, foreach (...) { ... }). The use of alternative template syntax (e.g., if(): ... endif;) is strictly forbidden.  
* **Event-Driven Core:** Implement a robust HookManager. All modules and themes must interface with core logic exclusively via do\_action() and apply\_filters() to maintain core file integrity.  
* **Security Architecture:**  
  * **Password Hashing:** Use password\_hash() with PASSWORD\_DEFAULT algorithm.  
  * **Authentication:** Must utilize existing ICTM\_Auth session management.

**2\. Module & Theme Lifecycle Management**

* **Manifest Specification:** Every entity must contain a manifest.json defining: name, version, author, dependencies, and required SQL scripts.  
* **Installation/Uninstallation Engine:**  
  * Validate ZIP structure upon upload.  
  * Extract to /Themes/ or /Modules/ directories.  
  * Execution of schema.sql and global registry updates.  
  * Uninstallation must include a complete cleanup of module-specific DB tables and file directories.  
* **Atomic Constraints:**  
  * Support dual theme storage (frontend\_theme/backend\_theme) in site\_options.  
  * Atomic activation: One theme active per scope; activating a new theme must trigger immediate deactivation of the predecessor.

**3\. Content & Media Management**

* **Editor Interface:** Integrate Summernote WYSIWYG editor with media insertion capabilities.  
* **Post Lifecycle:** Support Draft, Published, Expired, and Scheduled statuses.  
* **Slug Generation:** Implement automated sanitization (lowercase, special character removal) with collision checks and manual override fields.  
* **Media Library System:**  
  * **Storage:** Files must reside at Writables/images/YYYY/MM/DD/.  
  * **DB Integrity:** Metadata (original name, path, mime-type, dimensions, user\_id) must be logged in a media\_library table.  
  * **Prohibition:** No binary/blob storage or base64-encoded strings allowed for media.  
  * **Cleanup:** Deletion of any post/page must trigger an automatic recursive cleanup of associated physical media files and DB records.

**4\. RBAC & User Management**

* **Admin:** Full read/write/configuration access.  
* **Editor:** Access to Content, Media, and Comments. Restricted from System Settings or Theme/Module installation.  
* **Subscriber:** Frontend profile access only. No access to /admin/\*.

**5\. System Dashboard & Settings**

* **Settings Engine:** CRUD operations via a centralized site\_options (Key-Value) table.  
* **SEO & Analytics:** Provide fields for \<meta\> tag injection and footer/body script insertion.  
* **Dashboard Aggregation:** Single controller for aggregated statistics (posts, pages, users, comments), utilizing caching to optimize high-traffic performance.

**6\. Frontend & Admin UI/UX**

* **Framework:** Strict adherence to Bootstrap 5.3 grid and component systems.  
* **Data Handling:** Admin index views must implement DataTables with server-side processing for pagination and search.  
* **Interactivity:**  
  * Status toggles (e.g., Publish/Draft) must utilize asynchronous Ajax calls to /admin/api/update-status for real-time UI updates without reloads.  
  * Integrate Tagify for taxonomy/keyword inputs.  
  * Use Wow-animate on DOM load for aesthetic effects.  
* **Dashboard:** Aggregated statistics controller with caching.  
* **Frontend Framework:** Strict adherence to Bootstrap 5.3.  
* **Interactivity:** Admin index views must use DataTables with server-side processing; status toggles must use Ajax calls; include Tagify for taxonomy and Wow-animate for aesthetic effects.

