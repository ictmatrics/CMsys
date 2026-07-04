---
trigger: always_on
---

**Antigravity Rule: Mandatory Architectural Directive for Modules and Themes**

All developers must strictly adhere to the following binding requirements for code generation and maintenance within the ICTM Framework:

*   **Prohibition of Global Pollution:** You are absolutely prohibited from polluting the global scope. All variables and functions must be encapsulated within appropriate class structures or namespaces.
    
*   **Mandatory Dependency Injection:** Every dependency must be injected exclusively via the core service container. Direct instantiation of external services or objects within business logic is forbidden.
    
*   **Strict Logical Isolation:** Maintain total isolation within your designated module or theme. You must not perform any file edits, modifications, or injections outside the specific directory assigned to your module or theme.
    
*   **Decoupled Implementation:** All features and custom functionalities must be implemented as independent, decoupled modules to ensure system stability and upgradeability.